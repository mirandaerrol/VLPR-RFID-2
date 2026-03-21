<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use Illuminate\Support\Facades\Log;

class VehicleOwnerController extends Controller
{
    /**
     * Display a listing of vehicle owners.
     */
    public function index()
    {
        $vehicleOwners = VehicleOwner::orderBy('created_at', 'desc')->get();
        
        return view('admin.vehicle_owner.index', compact('vehicleOwners'));
    }

    /**
     * Store a newly created vehicle owner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'school_year' => 'nullable|string|max:50',
            'type_of_owner' => 'required|in:student,employee',
            'valid_id' => 'required|string|max:255',
            'rfid_code' => 'nullable|string|max:50|unique:vehicle_owner,rfid_code',
        ]);

        VehicleOwner::create($validated);

        Log::info('Vehicle owner created', ['name' => "{$validated['f_name']} {$validated['l_name']}"]);

        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner registered successfully.');
    }

    /**
     * Update the specified vehicle owner.
     */
    public function update(Request $request, $id)
    {
        $owner = VehicleOwner::findOrFail($id);

        $validated = $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'school_year' => 'nullable|string|max:50',
            'type_of_owner' => 'required|in:student,employee',
            'valid_id' => 'required|string|max:255',
            'rfid_code' => 'nullable|string|max:50|unique:vehicle_owner,rfid_code,' . $id . ',owner_id',
        ]);

        $owner->update($validated);

        Log::info('Vehicle owner updated', ['owner_id' => $id]);

        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner updated successfully.');
    }

    /**
     * Remove the specified vehicle owner.
     */
    public function destroy($id)
    {
        $owner = VehicleOwner::findOrFail($id);
        
        Log::info('Vehicle owner deleted', ['owner_id' => $id, 'name' => "{$owner->f_name} {$owner->l_name}"]);
        
        $owner->delete();
        return redirect()->route('admin.vehicle_owners.index')->with('success', 'Owner deleted successfully.');
    }
}
