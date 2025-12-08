<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string'
        ]);
        $exists = Report::where('plate_number', $request->plate_number)
                        ->where('created_at', '>', now()->subMinutes(5))
                        ->exists();

        if ($exists) {
            return response()->json(['message' => 'Vehicle already reported recently.'], 429);
        }

        Report::create([
            'plate_number' => $request->plate_number,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Vehicle reported successfully to Admin.'], 200);
    }
    public function index()
    {
        $reports = Report::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }
}