<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\VehicleOwner;


class LogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $logsQuery = Log::with('vehicle', 'owner', 'timeLog')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('detected_plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($subQuery) use ($search) {
                        $subQuery->where('plate_number', 'LIKE', "%{$search}%");
                    });
            });
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
            'owners' => $owners, 
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::table('time_log')->where('logs_id', $id)->delete();
            $log = Log::findOrFail($id);
            $log->delete();

            return back()->with('success', 'Log deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete log.');
        }
    }
}