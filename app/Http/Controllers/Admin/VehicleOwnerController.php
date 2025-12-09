<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;

class VehicleOwnerController extends Controller
{
    public function index()
    {
        $vehicleOwners = VehicleOwner::orderBy('created_at', 'desc')->get();
        
        return view('admin.vehicle_owner.index', compact('vehicleOwners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'school_year' => 'nullable|string',
            'type_of_owner' => 'required|in:student,employee',
            'valid_id' => 'required|string',
            'rfid_code' => 'nullable|string|unique:vehicle_owner,rfid_code',
        ]);

        VehicleOwner::create($validated);

        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $owner = VehicleOwner::findOrFail($id);

        $validated = $request->validate([
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'school_year' => 'nullable|string',
            'type_of_owner' => 'required|in:student,employee',
            'valid_id' => 'required|string',
            'rfid_code' => 'nullable|string|unique:vehicle_owner,rfid_code,' . $id . ',owner_id',
        ]);

        $owner->update($validated);

        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner updated successfully.');
    }

    public function destroy($id)
    {
        $owner = VehicleOwner::findOrFail($id);
        $owner->delete();
        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner deleted successfully.');
    }
}