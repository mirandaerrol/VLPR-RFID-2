<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;

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
            'vehicle_type' => 'required|string',
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