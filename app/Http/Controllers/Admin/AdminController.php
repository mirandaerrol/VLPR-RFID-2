<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Log;
use App\Models\Rfid;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRegistered = Vehicle::count();

        $totalDetections = Log::count();

        $totalRfidScanned = Log::whereNotNull('rfid_code')->count();

        return view('admin.dashboard', compact('totalRegistered', 'totalDetections', 'totalRfidScanned'));
    }
}