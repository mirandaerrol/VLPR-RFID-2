<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use App\Models\Vehicle;
use App\Models\Log;
use App\Models\TimeLog;
use Carbon\Carbon;

class GuardController extends Controller
{
    public function dashboard()
    {
        return view('guard.dashboard');
    }

    public function scanRfid(Request $request)
    {
        $request->validate(['rfid_code' => 'required|string']);
        $code = $request->rfid_code;

        $owner = VehicleOwner::where('rfid_code', $code)->first();

        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'RFID Tag not registered.'], 404);
        }
        $vehicle = $owner->vehicles()->first();
        $existingLog = Log::where('rfid_code', $code)
            ->whereHas('timeLog', function($q) {
                $q->whereNull('time_out');
            })
            ->with('timeLog')
            ->latest()
            ->first();

        if ($existingLog) {
            $existingLog->timeLog->time_out = Carbon::now();
            $existingLog->timeLog->save();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle Logged OUT',
                'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                'status' => 'Authorized (Exited)',
                'method' => 'RFID',
                'vehicle_type' => $vehicle ? $vehicle->vehicle_type : 'N/A',
                'owner' => $owner,
                'timestamp' => Carbon::now()->format('H:i:s')
            ]);

        } else {
            $log = new Log();
            $log->rfid_code = $code;
            $log->owner_id = $owner->owner_id;
            $log->detection_method = 'RFID';

            if ($vehicle) {
                $log->vehicle_id = $vehicle->vehicle_id;
                $log->vehicle_type = $vehicle->vehicle_type;
            }
            
            $log->created_at = Carbon::now();
            $log->updated_at = Carbon::now();
            $log->save();

            // Create New Time Log
            $timeLog = new TimeLog();
            $timeLog->logs_id = $log->logs_id;
            $timeLog->time_in = Carbon::now();
            $timeLog->save();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle Logged IN',
                'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                'status' => 'Authorized (Entered)',
                'method' => 'RFID',
                'vehicle_type' => $vehicle ? $vehicle->vehicle_type : 'N/A',
                'owner' => $owner,
                'timestamp' => $timeLog->time_in->format('H:i:s')
            ]);
        }
    }
}