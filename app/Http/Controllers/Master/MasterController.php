<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Log;
use App\Models\DutyAssignment;
use App\Models\OffDutyAccessLog;
use App\Models\GuardLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->databaseOverview($request);
    }

    public function databaseOverview(Request $request)
    {
        // 1. Core Entity Statistics (Counts)
        $totalOwners = VehicleOwner::count();
        $totalVehicles = Vehicle::count();
        $totalGuards = User::where('role', 'guard')->count();
        
        $totalUnregistered = Log::whereNull('owner_id')
                                ->whereNotNull('detected_plate_number')
                                ->distinct()
                                ->count('detected_plate_number');

        $dutyAssignmentsCount = DutyAssignment::count();

        // 2. Fetch Lists for the Interactive Modals
        $ownersList = VehicleOwner::orderBy('created_at', 'desc')->get();
        $vehiclesList = Vehicle::with('owner')->orderBy('created_at', 'desc')->get();
        $guardsList = User::where('role', 'guard')->orderBy('created_at', 'desc')->get();
        
        $unregisteredList = Log::whereNull('owner_id')
                                ->whereNotNull('detected_plate_number')
                                ->select('detected_plate_number', DB::raw('MAX(created_at) as last_seen'), DB::raw('COUNT(*) as total_detections'))
                                ->groupBy('detected_plate_number')
                                ->orderBy('last_seen', 'desc')
                                ->get();

        return view('master.database_overview', compact(
            'totalOwners', 'totalVehicles', 'totalGuards', 'totalUnregistered',
            'ownersList', 'vehiclesList', 'guardsList', 'unregisteredList', 'dutyAssignmentsCount'
        ));
    }

    public function liveMonitoring()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // A. Current On-Duty Guard
        $currentDuty = DutyAssignment::with('user')
            ->where('duty_date', $today)
            ->where('shift_start', '<=', $currentTime)
            ->where('shift_end', '>=', $currentTime)
            ->first();

        // B. Off-Duty Access Attempts
        $offDutyAttempts = OffDutyAccessLog::with('user')
            ->orderBy('attempted_at', 'desc')
            ->limit(10)
            ->get();

        // D. No-Show Alerts (Shift started > 30 mins ago, no login log for today)
        $thirtyMinsAgo = $now->copy()->subMinutes(30)->toTimeString();
        $noShowAssignments = DutyAssignment::with('user')
            ->where('duty_date', $today)
            ->where('shift_start', '<=', $thirtyMinsAgo)
            ->get();
        
        $noShowAlerts = [];
        foreach($noShowAssignments as $assignment) {
            $hasLoggedIn = GuardLoginLog::where('user_id', $assignment->user_id)
                ->whereDate('login_at', $today)
                ->whereTime('login_at', '>=', $assignment->shift_start)
                ->exists();
            
            if (!$hasLoggedIn) {
                $noShowAlerts[] = $assignment;
            }
        }

        $guardsList = User::where('role', 'guard')->orderBy('created_at', 'desc')->get();

        return view('master.live_monitoring', compact('currentDuty', 'offDutyAttempts', 'noShowAlerts', 'guardsList'));
    }

    public function scheduleManagement()
    {
        $dutyAssignments = DutyAssignment::with('user')
            ->orderBy('duty_date', 'asc')
            ->orderBy('shift_start', 'asc')
            ->get();

        $guardsList = User::where('role', 'guard')->orderBy('created_at', 'desc')->get();

        return view('master.schedule_management', compact('dutyAssignments', 'guardsList'));
    }

    public function detectionActivity(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $logsToday = Log::whereDate('created_at', Carbon::today())->count();
        $logsThisMonth = Log::whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->count();
        $logsThisYear = Log::whereYear('created_at', $selectedYear)->count();

        // Data for the Charts
        $todayLogs = Log::selectRaw('HOUR(created_at) as hour, count(*) as count')
                        ->whereDate('created_at', Carbon::today())
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->get();
        $labelsToday = $todayLogs->map(fn($log) => Carbon::createFromTime($log->hour, 0, 0)->format('g A'));
        $dataToday = $todayLogs->pluck('count');

        $monthLogs = Log::selectRaw('DATE(created_at) as date, count(*) as count')
                        ->whereMonth('created_at', $selectedMonth)
                        ->whereYear('created_at', $selectedYear)
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();
        $labelsMonth = $monthLogs->map(fn($log) => Carbon::parse($log->date)->format('M d'));
        $dataMonth = $monthLogs->pluck('count');

        $yearLogs = Log::selectRaw('MONTH(created_at) as month, count(*) as count')
                        ->whereYear('created_at', $selectedYear)
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();
        $labelsYear = $yearLogs->map(fn($log) => Carbon::createFromDate($selectedYear, $log->month, 1)->format('M Y'));
        $dataYear = $yearLogs->pluck('count');

        return view('master.detection_activity', compact(
            'logsToday', 'logsThisMonth', 'logsThisYear',
            'labelsToday', 'dataToday', 'labelsMonth', 'dataMonth', 'labelsYear', 'dataYear',
            'selectedMonth', 'selectedYear'
        ));
    }

    public function forceLogout($userId)
    {
        DB::table('sessions')->where('user_id', $userId)->delete();
        return back()->with('success', 'Guard session terminated successfully.');
    }

    public function reassignDuty(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:duty_assignments,id',
            'new_user_id' => 'required|exists:users,id',
        ]);

        $assignment = DutyAssignment::findOrFail($request->assignment_id);
        $assignment->user_id = $request->new_user_id;
        $assignment->save();

        return back()->with('success', 'Duty reassigned successfully.');
    }

    public function duplicateSchedule(Request $request)
    {
        $request->validate([
            'source_date' => 'required|date',
            'target_date' => 'required|date|after:source_date',
        ]);

        $assignments = DutyAssignment::whereDate('duty_date', $request->source_date)->get();
        
        if ($assignments->isEmpty()) {
            return back()->with('error', 'No assignments found on the source date.');
        }

        foreach ($assignments as $assignment) {
            DutyAssignment::create([
                'user_id' => $assignment->user_id,
                'duty_date' => $request->target_date,
                'shift_start' => $assignment->shift_start,
                'shift_end' => $assignment->shift_end,
            ]);
        }

        return back()->with('success', 'Schedule duplicated successfully.');
    }

    public function getHistoricalDuty(Request $request)
    {
        $request->validate([
            'search_date' => 'required|date',
            'search_time' => 'required',
        ]);

        $dateTime = Carbon::parse($request->search_date . ' ' . $request->search_time);
        
        $assignment = DutyAssignment::with('user')
            ->whereDate('duty_date', $request->search_date)
            ->where('shift_start', '<=', $request->search_time)
            ->where('shift_end', '>=', $request->search_time)
            ->first();
        
        $logins = GuardLoginLog::with('user')
            ->whereDate('login_at', $request->search_date)
            ->whereTime('login_at', '<=', $request->search_time)
            ->orderBy('login_at', 'desc')
            ->get();

        $vehicleLogs = Log::with(['vehicle', 'owner', 'timeLog'])
            ->whereDate('created_at', $request->search_date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('master.historical_audit', compact('assignment', 'logins', 'dateTime', 'vehicleLogs'));
    }

    public function assignDuty(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'duty_date' => 'required|date|after_or_equal:today',
            'shift_start' => 'required',
            'shift_end' => 'required',
        ]);

        DutyAssignment::create($request->all());

        return back()->with('success', 'Guard assigned to duty successfully.');
    }

    public function deleteDuty($id)
    {
        $assignment = DutyAssignment::findOrFail($id);
        $assignment->delete();
        return back()->with('success', 'Duty assignment deleted.');
    }

    // API Route for fetching log details when a chart dot is clicked
    public function getChartDetails(Request $request)
    {
        $request->validate([
            'period' => 'required|in:today,month,year',
            'label' => 'required|string',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2100',
        ]);
        
        $period = $request->query('period');
        $label = $request->query('label'); 
        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        $query = Log::with(['vehicle', 'timeLog'])->orderBy('created_at', 'desc');

        if ($period === 'today') {
            // label format: 'g A' (e.g., '2 PM')
            $time = Carbon::createFromFormat('g A', $label);
            $query->whereDate('created_at', Carbon::today())
                  ->whereTime('created_at', '>=', $time->format('H:00:00'))
                  ->whereTime('created_at', '<=', $time->format('H:59:59'));
                  
        } elseif ($period === 'month') {
            // label format: 'M d' (e.g., 'Dec 18')
            $date = Carbon::parse($label . ' ' . $year);
            $query->whereDate('created_at', $date->toDateString());
            
        } elseif ($period === 'year') {
            // label format: 'M Y' (e.g., 'Dec 2025')
            $date = Carbon::createFromFormat('M Y', $label);
            $query->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
        }

        $logs = $query->get()->map(function($log) {
            return [
                'formatted_date' => $log->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                'plate_number' => $log->vehicle->plate_number ?? ($log->detected_plate_number ?? 'Unknown'),
                'detection_method' => $log->detection_method,
                'vehicle_type' => $log->vehicle_type ?? ($log->vehicle->vehicle_type ?? 'N/A'),
                'time_out' => $log->timeLog ? $log->timeLog->time_out : null,
                'owner_id' => $log->owner_id,
            ];
        });

        return response()->json($logs);
    }
}