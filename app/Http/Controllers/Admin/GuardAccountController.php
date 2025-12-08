<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuardAccountController extends Controller
{
    public function index()
    {
        $guards = User::where('role', 'guard')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.guards.index', compact('guards'));
    }

    public function create()
    {
        return view('admin.guards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guard', 
        ]);
        return redirect()->route('admin.guards.index')->with('success', 'Guard account created successfully!');
    }
    public function destroy($id)
    {
        $guard = User::findOrFail($id);

        if ($guard->role !== 'guard') {
            return back()->with('error', 'Cannot delete this user.');
        }
        $guard->delete();
        return back()->with('success', 'Guard account deleted successfully.');
    }
}