<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;

class VehicleTableController extends Controller
{
    // --- UPDATED INDEX METHOD ---
    public function index()
    {
        // FIX: Removed '.rfid' from eager loading.
        // We only load 'owner' because 'rfid_code' is now just a column inside the owner table.
        $vehicles = Vehicle::with('owner')->orderBy('created_at', 'desc')->get();

        // 2. Fetch Data for the "Create Modal" Dropdowns
        $owners = VehicleOwner::all();

        // 3. Pass variables to the view
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
        ]);

        Vehicle::create($request->all());

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle registered successfully.');
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
        ]);

        $vehicle->update($request->all());

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