<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class VehicleController extends Controller
{
    public function index()
    {
        $plates = session('plates');
        return view('vehicle_detection', compact('plates',));
    }

    public function detectPlate(Request $request)
    {
        
    }

    public function show()
    {
        $plates = session('plates');

        return view('vehicle_detection', compact('plates'));
    }
    
    public function liveDetection()
    {
        try {
            $response = Http::get('http://127.0.0.1:5000/latest_detection');

            if ($response->failed()) {
                Log::error('Failed to fetch latest detection', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Unable to fetch live detection data'], 502);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception in liveDetection', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Server error while fetching detection'], 500);
        }
    }

}
