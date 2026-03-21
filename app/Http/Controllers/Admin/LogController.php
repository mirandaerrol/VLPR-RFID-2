<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\VehicleOwner;
use Carbon\Carbon;


class LogController extends Controller
{
    /**
     * Display logs with search, date filtering, and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $method = $request->input('method'); // PLATE, RFID, or all

        $logsQuery = Log::with(['vehicle', 'owner', 'timeLog'])
            ->orderBy('created_at', 'desc');

        // Search by plate number
        if ($search) {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('detected_plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($subQuery) use ($search) {
                        $subQuery->where('plate_number', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('owner', function ($subQuery) use ($search) {
                        $subQuery->where('f_name', 'LIKE', "%{$search}%")
                                 ->orWhere('l_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Date range filter
        if ($dateFrom) {
            $logsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $logsQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Detection method filter
        if ($method && $method !== 'all') {
            $logsQuery->where('detection_method', strtoupper($method));
        }

        $allLogs = $logsQuery->get();

        $groupedLogs = $allLogs->groupBy(function ($log) {
            return $log->vehicle->plate_number ?? $log->detected_plate_number ?? 'Unknown';
        });

        $registeredGroups = $groupedLogs->filter(function ($logs) {
            return $logs->first()->vehicle_id !== null;
        });

        $unregisteredGroups = $groupedLogs->filter(function ($logs) {
            return $logs->first()->vehicle_id === null;
        });

        $perPage = 10;
        
        $regPage = $request->input('reg_page', 1);
        $registeredPaginated = new LengthAwarePaginator(
            $registeredGroups->forPage($regPage, $perPage),
            $registeredGroups->count(),
            $perPage,
            $regPage,
            ['path' => $request->url(), 'pageName' => 'reg_page', 'query' => $request->query()]
        );

        $unregPage = $request->input('unreg_page', 1);
        $unregisteredPaginated = new LengthAwarePaginator(
            $unregisteredGroups->forPage($unregPage, $perPage),
            $unregisteredGroups->count(),
            $perPage,
            $unregPage,
            ['path' => $request->url(), 'pageName' => 'unreg_page', 'query' => $request->query()]
        );
        $owners = VehicleOwner::all();

        return view('admin.logs.index', [
            'registeredLogs' => $registeredPaginated,
            'unregisteredLogs' => $unregisteredPaginated,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'method' => $method,
            'owners' => $owners, 
        ]);
    }

    /**
     * Delete a single log entry and its associated time_log.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            DB::table('time_log')->where('logs_id', $id)->delete();
            $log = Log::findOrFail($id);
            $log->delete();
            
            DB::commit();

            return back()->with('success', 'Log deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Log deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete log.');
        }
    }

    /**
     * Bulk delete multiple log entries.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array|min:1',
            'log_ids.*' => 'integer|exists:logs,logs_id',
        ]);

        try {
            DB::beginTransaction();

            $logIds = $request->input('log_ids');
            
            // Delete associated time_logs first
            DB::table('time_log')->whereIn('logs_id', $logIds)->delete();
            
            // Delete the logs
            Log::whereIn('logs_id', $logIds)->delete();

            DB::commit();

            $count = count($logIds);
            return back()->with('success', "{$count} log(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Bulk log deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete logs.');
        }
    }

    /**
     * Export logs as CSV with optional filters.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $method = $request->input('method');

        $logsQuery = Log::with(['vehicle', 'owner', 'timeLog'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('detected_plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($subQuery) use ($search) {
                        $subQuery->where('plate_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $logsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $logsQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($method && $method !== 'all') {
            $logsQuery->where('detection_method', strtoupper($method));
        }

        $logs = $logsQuery->get();

        $filename = 'vehicle_logs_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Log ID',
                'Plate Number',
                'Detection Method',
                'Vehicle Type',
                'Owner Name',
                'Contact Number',
                'Time In',
                'Time Out',
                'Status',
                'Created At',
            ]);

            foreach ($logs as $log) {
                $plate = $log->vehicle->plate_number ?? $log->detected_plate_number ?? 'Unknown';
                $ownerName = $log->owner ? "{$log->owner->f_name} {$log->owner->l_name}" : 'Unregistered';
                $contact = $log->owner->contact_number ?? 'N/A';
                $timeIn = $log->timeLog->time_in ?? 'N/A';
                $timeOut = $log->timeLog->time_out ?? 'Still Inside';
                $status = $log->owner_id ? 'Registered' : 'Unregistered';

                fputcsv($file, [
                    $log->logs_id,
                    $plate,
                    $log->detection_method,
                    $log->vehicle_type ?? 'N/A',
                    $ownerName,
                    $contact,
                    $timeIn,
                    $timeOut,
                    $status,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
