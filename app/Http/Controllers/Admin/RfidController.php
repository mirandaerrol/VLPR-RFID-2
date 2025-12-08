<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Rfid;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function index()
    {
        $rfids = Rfid::all();
        return view('admin.rfids.index', compact('rfids'));
    }

    public function create()
    {
        return view('admin.rfids.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rfid_code' => 'required|string|unique:rfids,rfid_code|max:255',
        ], [
            'rfid_code.unique' => 'This RFID Code is already registered in the system.',
        ]);
        Rfid::create([
            'rfid_code' => $request->rfid_code
        ]);

        return redirect()->route('admin.rfids.index')->with('success', 'RFID created successfully.');
    }

    public function edit(Rfid $rfid)
    {
        return view('admin.rfids.edit', compact('rfid'));
    }

    public function update(Request $request, Rfid $rfid)
    {
        $request->validate([
            'rfid_code' => 'required|unique:rfids,rfid_code,' . $rfid->rfid_id . ',rfid_id',
        ]);

        $rfid->update($request->all());
        return redirect()->route('admin.rfids.index')->with('success', 'RFID updated successfully!');
    }

    public function destroy(Rfid $rfid)
    {
        $rfid->delete();
        return redirect()->route('admin.rfids.index')->with('success', 'RFID deleted successfully!');
    }
}