<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GuardAccountController extends Controller
{
    /**
     * Display a listing of guard accounts.
     */
    public function index()
    {
        $guards = User::where('role', 'guard')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.guards.index', compact('guards'));
    }

    /**
     * Show the form for creating a new guard account.
     */
    public function create()
    {
        return view('admin.guards.create');
    }

    /**
     * Store a newly created guard account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $guard = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guard', 
        ]);

        Log::info('Guard account created', ['guard_id' => $guard->id, 'name' => $guard->name]);

        return redirect()->route('admin.guards.index')->with('success', 'Guard account created successfully!');
    }

    /**
     * Remove the specified guard account.
     */
    public function destroy($id)
    {
        $guard = User::findOrFail($id);

        if ($guard->role !== 'guard') {
            Log::warning('Attempted to delete non-guard user', ['user_id' => $id, 'role' => $guard->role]);
            return back()->with('error', 'Cannot delete this user.');
        }

        Log::info('Guard account deleted', ['guard_id' => $id, 'name' => $guard->name]);

        $guard->delete();
        return back()->with('success', 'Guard account deleted successfully.');
    }
}
