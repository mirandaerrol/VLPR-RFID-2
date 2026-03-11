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
        // Fetch the last 50 logs to pre-populate the dashboard history
        $initialLogs = DB::table('time_log as t')
            ->join('logs as l', 't.logs_id', '=', 'l.logs_id')
            ->leftJoin('vehicles as v', 'l.vehicle_id', '=', 'v.vehicle_id')
            ->leftJoin('vehicle_owner as o', 'l.owner_id', '=', 'o.owner_id')
            ->select(
                'l.detected_plate_number', 'l.detection_method', 'l.vehicle_type as log_vehicle_type',
                't.time_in', 't.time_out', 't.updated_at',
                'v.plate_number', 'v.vehicle_type as db_vehicle_type',
                'o.f_name', 'o.l_name', 'o.contact_number'
            )
            ->orderBy('t.updated_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($row) {
                $plate = $row->plate_number ?: $row->detected_plate_number;
                $status = "";
                if ($row->time_out) {
                    $status = "Logged Out";
                } elseif ($row->f_name) {
                    $status = "Authorized (Logged In)";
                } else {
                    $status = "Unauthorized (Logged In)";
                }
                
                return [
                    'plate' => $plate,
                    'status' => $status,
                    'method' => $row->detection_method,
                    'time_in' => $row->time_in,
                    'time_out' => $row->time_out,
                    'updated_at' => $row->updated_at,
                    'owner' => $row->f_name ? [
                        'f_name' => $row->f_name,
                        'l_name' => $row->l_name,
                        'contact_number' => $row->contact_number,
                    ] : null,
                ];
            });

        return view('guard.dashboard', compact('initialLogs'));
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

                // FIX: Included time_in, time_out, and updated_at
                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle Logged OUT',
                    'plate' => $existingLog->vehicle ? $existingLog->vehicle->plate_number : 'Owner ID Only',
                    'status' => 'Logged Out',
                    'method' => 'RFID',
                    'vehicle_type' => $existingLog->vehicle_type ?? 'N/A',
                    'owner' => $owner,
                    'time_in' => $existingLog->timeLog->time_in,
                    'time_out' => $existingLog->timeLog->time_out,
                    'updated_at' => $existingLog->timeLog->updated_at,
                    'timestamp' => Carbon::now()->format('H:i:s')
                ]);

            } else {
                // --- LOG IN ---

                // 3. Check for Multiple Vehicles
                $vehicles = Vehicle::where('owner_id', $owner->owner_id)->get();

                if ($vehicles->count() > 1) {
                    return response()->json([
                        'success' => true,
                        'multiple_vehicles' => true,
                        'vehicles' => $vehicles,
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

    public function selectVehicleLog(Request $request)
    {
        try {
            $request->validate([
                'rfid_code' => 'required|string',
                'vehicle_id' => 'required' 
            ]);

            $owner = VehicleOwner::where('rfid_code', $request->rfid_code)->first();
            if (!$owner) {
                return response()->json(['success' => false, 'message' => 'Invalid Owner'], 404);
            }

            $vehicle = Vehicle::where('vehicle_id', $request->vehicle_id)->first();
            
            if (!$vehicle) {
                return response()->json(['success' => false, 'message' => 'Vehicle not found.'], 404);
            }

            if ($vehicle->owner_id !== $owner->owner_id) {
                 return response()->json(['success' => false, 'message' => 'Vehicle does not belong to owner'], 403);
            }

            return $this->createLog($owner, $vehicle, $request->rfid_code);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('RFID Select Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Selection Error: ' . $e->getMessage()], 500);
        }
    }

    private function createLog($owner, $vehicle, $code) {
        DB::beginTransaction();
        try {
            $log = new Log();
            $log->rfid_code = $code;
            $log->owner_id = $owner->owner_id;
            $log->detection_method = 'RFID';

            if ($vehicle) {
                $log->vehicle_id = $vehicle->vehicle_id;
                if (isset($vehicle->vehicle_type)) {
                    $log->vehicle_type = $vehicle->vehicle_type;
                }
            }
            
            $log->created_at = Carbon::now();
            $log->updated_at = Carbon::now();
            $log->save();

            $timeLog = new TimeLog();
            $timeLog->logs_id = $log->logs_id;
            $timeLog->time_in = Carbon::now();
            $timeLog->save();
            
            DB::commit();

            // FIX: Included time_in, time_out, and updated_at
            return response()->json([
                'success' => true,
                'message' => 'Vehicle Logged IN',
                'plate' => $vehicle ? $vehicle->plate_number : 'Owner ID Only',
                'status' => 'Authorized (Entered)',
                'method' => 'RFID',
                'vehicle_type' => ($vehicle && isset($vehicle->vehicle_type)) ? $vehicle->vehicle_type : 'N/A',
                'owner' => $owner,
                'time_in' => $timeLog->time_in,
                'time_out' => null,
                'updated_at' => $log->updated_at,
                'timestamp' => $timeLog->time_in->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}