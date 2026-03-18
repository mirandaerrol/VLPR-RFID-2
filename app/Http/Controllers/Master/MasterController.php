<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get selected month and year from request, default to current if none selected
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // 1. Core Entity Statistics (Counts) - Using DB::raw for efficiency
        $totalOwners = VehicleOwner::count();
        $totalVehicles = Vehicle::count();
        $totalGuards = User::where('role', 'guard')->count();
        
        // Optimized distinct count using DB
        $totalUnregistered = Log::whereNull('owner_id')
                                ->whereNotNull('detected_plate_number')
                                ->distinct()
                                ->count('detected_plate_number');

        // 2. Fetch Lists for the Interactive Modals
        $ownersList = VehicleOwner::orderBy('created_at', 'desc')->get();
        $vehiclesList = Vehicle::with('owner')->orderBy('created_at', 'desc')->get();
        $guardsList = User::where('role', 'guard')->orderBy('created_at', 'desc')->get();
        
        // Optimized unregistered list query
        $unregisteredList = Log::whereNull('owner_id')
                                ->whereNotNull('detected_plate_number')
                                ->select('detected_plate_number', DB::raw('MAX(created_at) as last_seen'), DB::raw('COUNT(*) as total_detections'))
                                ->groupBy('detected_plate_number')
                                ->orderBy('last_seen', 'desc')
                                ->get();

        // 3. Time-Segregated Log Statistics (Card Totals)
        $logsToday = Log::whereDate('created_at', Carbon::today())->count();
        
        // Filtered by selected Month and Year
        $logsThisMonth = Log::whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->count();
                            
        // Filtered by selected Year
        $logsThisYear = Log::whereYear('created_at', $selectedYear)->count();

        // 4. Data for the Charts (Dynamic Segregation)
        
        // A. TODAY (Grouped by Hour) - Always shows actual today
        $todayLogs = Log::selectRaw('HOUR(created_at) as hour, count(*) as count')
                        ->whereDate('created_at', Carbon::today())
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->get();
        $labelsToday = $todayLogs->map(function($log) {
            // Safer time formatting
            return Carbon::createFromTime($log->hour, 0, 0)->format('g A');
        });
        $dataToday = $todayLogs->pluck('count');

        // B. SELECTED MONTH (Grouped by Day)
        $monthLogs = Log::selectRaw('DATE(created_at) as date, count(*) as count')
                        ->whereMonth('created_at', $selectedMonth)
                        ->whereYear('created_at', $selectedYear)
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();
        $labelsMonth = $monthLogs->map(function($log) {
            return Carbon::parse($log->date)->format('M d');
        });
        $dataMonth = $monthLogs->pluck('count');

        // C. SELECTED YEAR (Grouped by Month)
        $yearLogs = Log::selectRaw('MONTH(created_at) as month, count(*) as count')
                        ->whereYear('created_at', $selectedYear)
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();
        $labelsYear = $yearLogs->map(function($log) use ($selectedYear) {
            // FIX: Explicitly create date with the selected year to prevent the '0000' bug
            return Carbon::createFromDate($selectedYear, $log->month, 1)->format('M Y');
        });
        $dataYear = $yearLogs->pluck('count');

        return view('master.dashboard', compact(
            'totalOwners', 
            'totalVehicles', 
            'totalGuards', 
            'totalUnregistered',
            'ownersList',
            'vehiclesList',
            'guardsList',
            'unregisteredList',
            'logsToday', 
            'logsThisMonth', 
            'logsThisYear',
            'labelsToday', 'dataToday',
            'labelsMonth', 'dataMonth',
            'labelsYear', 'dataYear',
            'selectedMonth', 'selectedYear' // Pass these to the view for the filter dropdowns
        ));
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