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
        try {
            $request->validate(['rfid_code' => 'required|string']);
            $code = $request->rfid_code;

            // 1. Find Owner
            $owner = VehicleOwner::where('rfid_code', $code)->first();

            if (!$owner) {
                return response()->json(['success' => false, 'message' => 'RFID Tag not registered.'], 404);
            }

            // 2. CHECK FOR OPEN SESSION (Time In without Time Out)
            // If they are already logged in, we log them out regardless of vehicle selection
            $existingLog = Log::where('rfid_code', $code)
                ->whereHas('timeLog', function($q) {
                    $q->whereNull('time_out');
                })
                ->with(['timeLog', 'vehicle'])
                ->latest()
                ->first();

            if ($existingLog) {
                // --- LOG OUT ---
                
                if (!$existingLog->timeLog) {
                    $timeLog = new TimeLog();
                    $timeLog->logs_id = $existingLog->logs_id;
                    $timeLog->time_in = $existingLog->created_at;
                    $timeLog->time_out = Carbon::now();
                    $timeLog->save();
                    $existingLog->load('timeLog');
                } else {
                    $existingLog->timeLog->time_out = Carbon::now();
                    $existingLog->timeLog->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle Logged OUT',
                    'plate' => $existingLog->vehicle ? $existingLog->vehicle->plate_number : 'Owner ID Only',
                    'status' => 'Logged Out',
                    'method' => 'RFID',
                    'vehicle_type' => $existingLog->vehicle_type ?? 'N/A',
                    'owner' => $owner,
                    'timestamp' => Carbon::now()->format('H:i:s')
                ]);

            } else {
                // --- LOG IN ---

                // 3. Check for Multiple Vehicles
                $vehicles = $owner->vehicles; // Assuming 'vehicles' relationship exists in VehicleOwner model

                if ($vehicles->count() > 1) {
                    // Return special response to trigger Modal on Frontend
                    return response()->json([
                        'success' => true,
                        'multiple_vehicles' => true,
                        'vehicles' => $vehicles, // Send list of vehicles to frontend
                        'owner' => $owner
                    ]);
                }

                // 4. Single Vehicle Auto-Login
                $vehicle = $vehicles->first(); 
                
                return $this->createLog($owner, $vehicle, $code);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('RFID Scan Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()], 500);
        }
    }

    // NEW: Method to handle vehicle selection from the modal
    public function selectVehicle(Request $request)
    {
        try {
            $request->validate([
                'rfid_code' => 'required|string',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id'
            ]);

            $owner = VehicleOwner::where('rfid_code', $request->rfid_code)->first();
            if (!$owner) {
                return response()->json(['success' => false, 'message' => 'Invalid Owner'], 404);
            }

            $vehicle = Vehicle::find($request->vehicle_id);
            
            // Verify this vehicle actually belongs to the owner
            if ($vehicle->owner_id !== $owner->owner_id) {
                 return response()->json(['success' => false, 'message' => 'Vehicle does not belong to owner'], 403);
            }

            return $this->createLog($owner, $vehicle, $request->rfid_code);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('RFID Select Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Selection Error: ' . $e->getMessage()], 500);
        }
    }

    // Helper function to create the log entry
    private function createLog($owner, $vehicle, $code) {
        DB::beginTransaction();
        try {
            $log = new Log();
            $log->rfid_code = $code;
            $log->owner_id = $owner->owner_id;
            $log->detection_method = 'RFID';

            if ($vehicle) {
                $log->vehicle_id = $vehicle->vehicle_id;
                // Capture current vehicle type in log for historical accuracy
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
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}