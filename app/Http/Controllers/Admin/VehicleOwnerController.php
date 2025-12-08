<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
// REMOVED: use App\Models\Rfid; because the table no longer exists

class VehicleOwnerController extends Controller
{
    public function index()
    {
        // 1. Get owners
        // We removed 'with("rfid")' because the relationship no longer exists.
        // The rfid_code is now a direct column on the vehicle_owner table.
        $vehicleOwners = VehicleOwner::orderBy('created_at', 'desc')->get();
        
        // 2. We no longer need $rfids list for dropdowns
        
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
            
            // CHANGED: Validate 'rfid_code' as a string, unique in vehicle_owner table
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
            
            // CHANGED: Ignore current owner ID for unique check
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