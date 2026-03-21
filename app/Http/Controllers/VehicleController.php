<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    /**
     * Display the vehicle detection page.
     */
    public function index()
    {
        $plates = session('plates');
        return view('vehicle_detection', compact('plates'));
    }

    /**
     * Show vehicle detection results.
     */
    public function show()
    {
        $plates = session('plates');
        return view('vehicle_detection', compact('plates'));
    }

    /**
     * Get the latest detection directly from the shared database.
     * 
     * NOTE: Since the Flask backend runs locally and Laravel runs on Railway,
     * Laravel cannot proxy to Flask. Instead, both systems share the same
     * MySQL database, so we query it directly here.
     */
    public function liveDetection()
    {
        try {
            $result = DB::table('time_log as t')
                ->join('logs as l', 't.logs_id', '=', 'l.logs_id')
                ->leftJoin('vehicles as v', 'l.vehicle_id', '=', 'v.vehicle_id')
                ->leftJoin('vehicle_owner as o', 'l.owner_id', '=', 'o.owner_id')
                ->select(
                    'l.logs_id', 'l.created_at', 'l.detected_plate_number',
                    'l.detection_method', 'l.vehicle_type as log_vehicle_type',
                    't.time_in', 't.time_out', 't.updated_at',
                    'v.plate_number', 'v.vehicle_type as db_vehicle_type',
                    'o.f_name', 'o.l_name', 'o.contact_number'
                )
                ->orderBy('t.updated_at', 'desc')
                ->first();

            if ($result) {
                $status = '';
                $vehicleType = $result->log_vehicle_type ?: ($result->db_vehicle_type ?: 'Unknown');

                if ($result->plate_number) {
                    $plate = $result->plate_number;
                    if ($result->time_out) {
                        $status = 'Logged Out';
                    } elseif ($result->f_name) {
                        $status = 'Authorized (Logged In)';
                    } else {
                        $status = 'Unauthorized (Logged In)';
                    }
                } else {
                    $status = 'Unknown Vehicle';
                    $plate = $result->detected_plate_number;
                }

                return response()->json([
                    'plate' => $plate,
                    'status' => $status,
                    'method' => $result->detection_method,
                    'vehicle_type' => $vehicleType,
                    'owner' => $result->f_name ? [
                        'f_name' => $result->f_name,
                        'l_name' => $result->l_name,
                        'contact_number' => $result->contact_number,
                    ] : null,
                    'time_in' => $result->time_in,
                    'time_out' => $result->time_out,
                    'detected_at' => $result->updated_at,
                ]);
            }

            return response()->json(['message' => 'No detections yet'], 404);

        } catch (\Exception $e) {
            Log::error('Exception in liveDetection', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Server error while fetching detection'], 500);
        }
    }
}
