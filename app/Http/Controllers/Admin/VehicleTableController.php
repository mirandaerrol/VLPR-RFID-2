<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\Log; // Import Log model

class VehicleTableController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('owner')->orderBy('created_at', 'desc')->get();
        $owners = VehicleOwner::all();
        return view('admin.vehicles.index', compact('vehicles', 'owners'));
    }

    public function create()
    {
        $owners = VehicleOwner::all();
        return view('admin.vehicles.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:vehicle_owner,owner_id',
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'vehicle_type' => 'required|string',
        ]);

        // Create the new Vehicle
        $vehicle = Vehicle::create($request->all());

        // --- NEW LOGIC: Link existing unknown logs to this new vehicle ---
        // Find logs where detected_plate_number matches the new plate
        // AND (vehicle_id is null OR owner_id is null)
        Log::where('detected_plate_number', $request->plate_number)
            ->where(function($query) {
                $query->whereNull('vehicle_id')
                      ->orWhereNull('owner_id');
            })
            ->update([
                'vehicle_id' => $vehicle->vehicle_id,
                'owner_id' => $request->owner_id,
                'vehicle_type' => $request->vehicle_type // Optional: update type if you want history to reflect it
            ]);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle registered successfully and previous logs updated.');
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $owners = VehicleOwner::all();
        return view('admin.vehicles.edit', compact('vehicle', 'owners'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'owner_id' => 'required|exists:vehicle_owner,owner_id',
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $id . ',vehicle_id',
            'vehicle_type' => 'required|string',
        ]);

        $vehicle->update($request->all());

        // Optional: Update logs if plate number changed
        if ($vehicle->wasChanged('plate_number') || $vehicle->wasChanged('owner_id')) {
             Log::where('vehicle_id', $vehicle->vehicle_id)
                ->update([
                    'owner_id' => $vehicle->owner_id,
                    // If plate changed, we might want to update detected_plate_number too, or leave it as history
                ]);
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')
                         ->with('success', 'Vehicle deleted successfully.');
    }
}