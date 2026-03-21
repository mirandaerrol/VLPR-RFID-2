<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as AppLog;

class VehicleTableController extends Controller
{
    /**
     * Display a listing of vehicles.
     */
    public function index()
    {
        $vehicles = Vehicle::with('owner')->orderBy('created_at', 'desc')->get();
        $owners = VehicleOwner::all();
        return view('admin.vehicles.index', compact('vehicles', 'owners'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $owners = VehicleOwner::all();
        return view('admin.vehicles.create', compact('owners'));
    }

    /**
     * Store a newly created vehicle.
     * Also links any existing unknown logs to this new vehicle.
     */
    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:vehicle_owner,owner_id',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number|regex:/^[A-Z0-9]+$/',
            'vehicle_type' => 'required|string|max:50|in:Car,Motorcycle,Bus,Truck',
        ]);

        DB::beginTransaction();
        try {
            // Create the new Vehicle
            $vehicle = Vehicle::create($request->only(['owner_id', 'plate_number', 'vehicle_type']));

            // Link existing unknown logs to this new vehicle
            $updatedCount = Log::where('detected_plate_number', $request->plate_number)
                ->where(function($query) {
                    $query->whereNull('vehicle_id')
                          ->orWhereNull('owner_id');
                })
                ->update([
                    'vehicle_id' => $vehicle->vehicle_id,
                    'owner_id' => $request->owner_id,
                    'vehicle_type' => $request->vehicle_type
                ]);

            DB::commit();

            AppLog::info('Vehicle registered', [
                'vehicle_id' => $vehicle->vehicle_id,
                'plate' => $request->plate_number,
                'linked_logs' => $updatedCount,
            ]);

            return redirect()->route('admin.vehicles.index')
                ->with('success', "Vehicle registered successfully. {$updatedCount} previous log(s) linked.");
        } catch (\Exception $e) {
            DB::rollBack();
            AppLog::error('Vehicle registration failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to register vehicle. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $owners = VehicleOwner::all();
        return view('admin.vehicles.edit', compact('vehicle', 'owners'));
    }

    /**
     * Update the specified vehicle.
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'owner_id' => 'required|exists:vehicle_owner,owner_id',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $id . ',vehicle_id|regex:/^[A-Z0-9]+$/',
            'vehicle_type' => 'required|string|max:50|in:Car,Motorcycle,Bus,Truck',
        ]);

        DB::beginTransaction();
        try {
            $vehicle->update($request->only(['owner_id', 'plate_number', 'vehicle_type']));

            // Update associated logs if ownership changed
            if ($vehicle->wasChanged('owner_id')) {
                Log::where('vehicle_id', $vehicle->vehicle_id)
                    ->update(['owner_id' => $vehicle->owner_id]);
            }

            DB::commit();

            AppLog::info('Vehicle updated', ['vehicle_id' => $id, 'plate' => $request->plate_number]);

            return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            AppLog::error('Vehicle update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update vehicle. Please try again.');
        }
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        AppLog::info('Vehicle deleted', ['vehicle_id' => $id, 'plate' => $vehicle->plate_number]);
        
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')
                         ->with('success', 'Vehicle deleted successfully.');
    }
}
