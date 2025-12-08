<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner; // Changed from Rfid to VehicleOwner
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

        // 1. Find Owner by RFID Code directly
        // (Since RFID is now a column on the owner table)
        $owner = VehicleOwner::where('rfid_code', $code)->first();

        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'RFID Tag not registered.'], 404);
        }

        // 2. Find Associated Vehicle
        // Since an owner can have multiple vehicles, we grab the first one found 
        // to associate with the log for display purposes.
        $vehicle = $owner->vehicles()->first();
        
        // 3. CHECK FOR OPEN SESSION (Time In without Time Out)
        // We look for a log with this RFID Code string that has a TimeLog where time_out is NULL
        $existingLog = Log::where('rfid_code', $code)
            ->whereHas('timeLog', function($q) {
                $q->whereNull('time_out');
            })
            ->with('timeLog')
            ->latest()
            ->first();

        if ($existingLog) {
            // --- SCENARIO A: VEHICLE IS EXITING (LOG OUT) ---
            
            // Update the existing TimeLog with the current time
            $existingLog->timeLog->time_out = Carbon::now();
            $existingLog->timeLog->save();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle Logged OUT',
                'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                'status' => 'Authorized (Exited)',
                'owner' => $owner,
                'timestamp' => Carbon::now()->format('H:i:s')
            ]);

        } else {
            // --- SCENARIO B: VEHICLE IS ENTERING (LOG IN) ---

            // Create New Main Log
            $log = new Log();
            $log->rfid_code = $code; // Store the code string directly
            $log->owner_id = $owner->owner_id;
            
            // If the owner has a vehicle, link the first one found.
            if ($vehicle) {
                $log->vehicle_id = $vehicle->vehicle_id;
            }
            
            $log->created_at = Carbon::now();
            $log->updated_at = Carbon::now();
            $log->save();

            // Create New Time Log (Time In)
            $timeLog = new TimeLog();
            $timeLog->logs_id = $log->logs_id;
            $timeLog->time_in = Carbon::now();
            $timeLog->save();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle Logged IN',
                'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                'status' => 'Authorized (Entered)',
                'owner' => $owner,
                'timestamp' => $timeLog->time_in->format('H:i:s')
            ]);
        }
    }
}