<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rfid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RfidController extends Controller
{
    /**
     * Display a listing of RFID tags.
     */
    public function index()
    {
        $rfids = Rfid::all();
        return view('admin.rfids.index', compact('rfids'));
    }

    /**
     * Show the form for creating a new RFID tag.
     */
    public function create()
    {
        return view('admin.rfids.create');
    }

    /**
     * Store a newly created RFID tag.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rfid_code' => 'required|string|max:50|unique:rfids,rfid_code|regex:/^[A-Za-z0-9\-]+$/',
        ], [
            'rfid_code.unique' => 'This RFID Code is already registered in the system.',
            'rfid_code.regex' => 'RFID Code can only contain letters, numbers, and hyphens.',
        ]);

        Rfid::create([
            'rfid_code' => $request->rfid_code
        ]);

        Log::info('RFID tag created', ['rfid_code' => $request->rfid_code]);

        return redirect()->route('admin.rfids.index')->with('success', 'RFID created successfully.');
    }

    /**
     * Show the form for editing the specified RFID tag.
     */
    public function edit(Rfid $rfid)
    {
        return view('admin.rfids.edit', compact('rfid'));
    }

    /**
     * Update the specified RFID tag.
     */
    public function update(Request $request, Rfid $rfid)
    {
        $request->validate([
            'rfid_code' => 'required|string|max:50|unique:rfids,rfid_code,' . $rfid->rfid_id . ',rfid_id|regex:/^[A-Za-z0-9\-]+$/',
        ], [
            'rfid_code.regex' => 'RFID Code can only contain letters, numbers, and hyphens.',
        ]);

        $rfid->update(['rfid_code' => $request->rfid_code]);

        Log::info('RFID tag updated', ['rfid_id' => $rfid->rfid_id]);

        return redirect()->route('admin.rfids.index')->with('success', 'RFID updated successfully!');
    }

    /**
     * Remove the specified RFID tag.
     */
    public function destroy(Rfid $rfid)
    {
        Log::info('RFID tag deleted', ['rfid_id' => $rfid->rfid_id, 'rfid_code' => $rfid->rfid_code]);

        $rfid->delete();
        return redirect()->route('admin.rfids.index')->with('success', 'RFID deleted successfully!');
    }
}
