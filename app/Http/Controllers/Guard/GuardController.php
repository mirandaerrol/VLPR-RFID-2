<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use App\Models\Vehicle;
use App\Models\Log;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GuardController extends Controller
{
    public function dashboard()
    {
        return view('guard.dashboard');
    }

    public function scanRfid(Request $request)
    {
        // Wrap entire logic in try-catch to prevent generic 500 errors
        try {
            $request->validate(['rfid_code' => 'required|string']);
            $code = $request->rfid_code;

            // 1. Find Owner by RFID Code directly
            $owner = VehicleOwner::where('rfid_code', $code)->first();

            if (!$owner) {
                return response()->json(['success' => false, 'message' => 'RFID Tag not registered.'], 404);
            }

            // 2. Find Associated Vehicle
            // Ensure the relationship exists in VehicleOwner model
            $vehicle = $owner->vehicles()->first();
            
            // 3. CHECK FOR OPEN SESSION (Time In without Time Out)
            $existingLog = Log::where('rfid_code', $code)
                ->whereHas('timeLog', function($q) {
                    $q->whereNull('time_out');
                })
                ->with('timeLog')
                ->latest()
                ->first();

            if ($existingLog) {
                // --- LOG OUT ---
                
                // Safety check for relationship
                if (!$existingLog->timeLog) {
                    // FIX: Instead of throwing an error, create the missing TimeLog so they can log out
                    $timeLog = new TimeLog();
                    $timeLog->logs_id = $existingLog->logs_id;
                    $timeLog->time_in = $existingLog->created_at; // Use log creation time as fallback
                    $timeLog->time_out = Carbon::now();
                    $timeLog->save();
                    
                    // Reload relation
                    $existingLog->load('timeLog');
                } else {
                    $existingLog->timeLog->time_out = Carbon::now();
                    $existingLog->timeLog->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle Logged OUT',
                    'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                    'status' => 'Authorized (Exited)',
                    'method' => 'RFID',
                    // Use null coalescing to safely access vehicle_type (defaults to N/A if vehicle or column missing)
                    'vehicle_type' => ($vehicle && isset($vehicle->vehicle_type)) ? $vehicle->vehicle_type : 'N/A',
                    'owner' => $owner,
                    'timestamp' => Carbon::now()->format('H:i:s')
                ]);

            } else {
                // --- LOG IN ---
                DB::beginTransaction();
                try {
                    $log = new Log();
                    $log->rfid_code = $code;
                    $log->owner_id = $owner->owner_id;
                    $log->detection_method = 'RFID';

                    if ($vehicle) {
                        $log->vehicle_id = $vehicle->vehicle_id;
                        // Only try to set vehicle_type if the column is expected to exist. 
                        // If you haven't run the migration yet, this line might cause an issue, 
                        // but normally Laravel ignores non-fillable attributes unless they cause SQL error.
                        if (isset($vehicle->vehicle_type)) {
                            $log->vehicle_type = $vehicle->vehicle_type;
                        }
                    }
                    
                    $log->created_at = Carbon::now();
                    $log->updated_at = Carbon::now();
                    $log->save();

                    // Create New Time Log
                    $timeLog = new TimeLog();
                    $timeLog->logs_id = $log->logs_id;
                    $timeLog->time_in = Carbon::now();
                    $timeLog->save();
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e; // Re-throw to be caught by outer catch
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle Logged IN',
                    'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                    'status' => 'Authorized (Entered)',
                    'method' => 'RFID',
                    'vehicle_type' => ($vehicle && isset($vehicle->vehicle_type)) ? $vehicle->vehicle_type : 'N/A',
                    'owner' => $owner,
                    'timestamp' => $timeLog->time_in->format('H:i:s')
                ]);
            }
        } catch (\Exception $e) {
            // Log the error for server-side debugging
            \Illuminate\Support\Facades\Log::error('RFID Scan Error: ' . $e->getMessage());
            
            // Return the actual error message to the frontend for easier debugging
            return response()->json([
                'success' => false, 
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
}