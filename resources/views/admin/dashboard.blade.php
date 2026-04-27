@extends('layouts.dashboard') 

@section('content')
<div class="dashboard-container">
    
    <div class="mb-8">
        <h1 class="text-3xl font-800"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
        <p class="text-slate-500">Welcome back, Admin. Real-time monitoring active.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h4>Registered Vehicles</h4>
                <h2>{{ $totalRegistered ?? 0 }}</h2>
            </div>
            <div class="stat-icon" style="color: var(--blue-500);">
                <i class="fas fa-car"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h4>Total Detections</h4>
                <h2>{{ $totalDetections ?? 0 }}</h2>
            </div>
            <div class="stat-icon" style="color: var(--orange-500);">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h4>RFID Scans</h4>
                <h2>{{ $totalRfidScanned ?? 0 }}</h2> 
            </div>
            <div class="stat-icon" style="color: var(--purple-500);">
                <i class="fas fa-wifi"></i>
            </div>
        </div>
    </div>

    <h3 class="mb-4 font-bold text-dark">Live Monitoring</h3>
    <div class="admin-monitor-grid">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-video" style="color: var(--blue-500);"></i> Live Camera Feeds</h3>
                <span class="status-badge status-unauthorized" style="font-size: 0.75rem;">
                    <i class="fas fa-circle" style="font-size: 8px;"></i> LIVE
                </span>
            </div>
            <div class="live-stream-grid">
                <div>
                    <div class="gate-label entry-label">
                        <i class="fas fa-sign-in-alt"></i> ENTRY GATE
                    </div>
                    <div class="live-stream-container m-0">
                        <img src="{{ $detectionBackendUrl }}/video_feed/entry?api_key={{ $detectionApiKey }}"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x225?text=Entry+Offline'; this.style.opacity='0.5';"
                             alt="Entry Gate" class="stream-img">
                    </div>
                </div>
                <div>
                    <div class="gate-label exit-label">
                        <i class="fas fa-sign-out-alt"></i> EXIT GATE
                    </div>
                    <div class="live-stream-container m-0">
                        <img src="{{ $detectionBackendUrl }}/video_feed/exit?api_key={{ $detectionApiKey }}"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x225?text=Exit+Offline'; this.style.opacity='0.5';"
                             alt="Exit Gate" class="stream-img">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bell" style="color: var(--orange-500);"></i> Latest Activity</h3>
                <p id="last-updated" class="text-slate-400 fs-sm">Waiting...</p>
            </div>
            
            <div id="detection-details" class="detection-details-container">
                <div class="flex-center flex-col" style="padding: 3rem 1rem; color: var(--slate-300);">
                    <i class="fas fa-car-side" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Waiting for vehicle or RFID...</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mb-4 font-bold text-dark">Quick Actions</h3>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bolt" style="color: var(--primary-color);"></i> Access Tools</h3>
        </div>
        
        <div class="quick-actions-grid">
            <a href="{{ route('admin.logs.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-list" style="color: var(--primary-color);"></i>
                    <h4>View All Logs</h4>
                </div>
            </a>

            <a href="{{ route('admin.vehicles.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-plus-circle" style="color: var(--blue-500);"></i>
                    <h4>Register Vehicle</h4>
                </div>
            </a>

            <a href="{{ route('admin.guards.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-user-plus" style="color: var(--orange-500);"></i>
                    <h4>Add New Guard</h4>
                </div>
            </a>
        </div>
    </div>

</div>

<script>
    const liveUrl = "{{ route('vehicle_detect_live') }}";
</script>
@endsection