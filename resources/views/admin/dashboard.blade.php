@extends('layouts.dashboard') 
@include('style')

@section('content')
<div class="dashboard-container">
    
    <div style="margin-bottom: 2rem;">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
        <p style="color: #64748b;">Welcome back, Admin. Real-time monitoring active.</p>
    </div>

    <!-- 1. STATS ROW -->
    <div class="stats-grid">
        <!-- Vehicles -->
        <div class="stat-card">
            <div class="stat-info">
                <h4>Registered Vehicles</h4>
                <h2>{{ $totalRegistered ?? 0 }}</h2>
            </div>
            <div class="stat-icon" style="color: #3b82f6;">
                <i class="fas fa-car"></i>
            </div>
        </div>

        <!-- Detections -->
        <div class="stat-card">
            <div class="stat-info">
                <h4>Total Detections</h4>
                <h2>{{ $totalDetections ?? 0 }}</h2>
            </div>
            <div class="stat-icon" style="color: #f59e0b;">
                <i class="fas fa-camera"></i>
            </div>
        </div>

        <!-- RFIDs -->
        <div class="stat-card">
            <div class="stat-info">
                <h4>RFID Scans</h4>
                <h2>{{ $totalRfidScanned ?? 0 }}</h2> 
            </div>
            <div class="stat-icon" style="color: #8b5cf6;">
                <i class="fas fa-wifi"></i>
            </div>
        </div>
    </div>

    <!-- 2. LIVE MONITOR SECTION (NEW) -->
    <h3 style="margin-bottom: 1rem; color: #333;">Live Monitoring</h3>
    <div class="admin-monitor-grid">
        
        <!-- Video Feed -->
        <div class="card video-card">
            <div class="card-header">
                <h3><i class="fas fa-video" style="color: #ef4444;"></i> Live Camera Feed</h3>
                <span class="status-badge status-authorized" style="background:#fee2e2; color:#ef4444; font-size: 0.75rem;">
                    <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i> LIVE
                </span>
            </div>
            <div class="live-stream-container">
                <!-- Connects to your Python Flask App -->
                <img src="http://127.0.0.1:5000/video_feed" 
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/800x450?text=Camera+Offline'; this.style.opacity='0.5';"
                     alt="Live Stream">
            </div>
        </div>

        <!-- Real-Time Detection Info -->
        <div class="card info-card">
            <div class="card-header">
                <h3><i class="fas fa-bell" style="color: #f59e0b;"></i> Latest Activity</h3>
                <p id="last-updated" style="font-size: 0.8rem; color: #999;">Waiting...</p>
            </div>
            
            <div id="detection-details" class="detection-details-container">
                <div style="text-align: center; padding: 3rem 1rem; color: #cbd5e1;">
                    <i class="fas fa-car-side" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Waiting for vehicle or RFID...</p>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. QUICK ACTIONS -->
    <h3 style="margin-bottom: 1rem; color: #333;">Quick Actions</h3>
    <div class="card">
        <div class="section-header">
            <h3><i class="fas fa-history" style="color: #58bc82; margin-right: 10px;"></i> Quick Actions</h3>
        </div>
        
        <div class="quick-actions-grid">
            <a href="{{ route('admin.logs.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-list" style="color: #58bc82;"></i>
                    <h4>View All Logs</h4>
                </div>
            </a>

            <a href="{{ route('admin.vehicles.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-plus-circle" style="color: #3b82f6;"></i>
                    <h4>Register Vehicle</h4>
                </div>
            </a>

            <a href="{{ route('admin.guards.index') }}" style="text-decoration: none;">
                <div class="action-card">
                    <i class="fas fa-user-plus" style="color: #d97706;"></i>
                    <h4>Add New Guard</h4>
                </div>
            </a>
        </div>
    </div>

</div>

<!-- REAL-TIME JAVASCRIPT -->
<script>
    const liveUrl = "{{ route('vehicle_detect_live') }}";

    async function fetchLatestDetection() {
        try {
            const response = await fetch(liveUrl);
            if (!response.ok) return;
            const data = await response.json();
            
            // Only update if there is data
            if (data.plate) {
                updateDetectionPanel(data);
            }

        } catch (error) { console.error(error); }
    }

    function updateDetectionPanel(data) {
        const container = document.getElementById("detection-details");
        const isAuth = data.status.toLowerCase().includes('authorized') && !data.status.toLowerCase().includes('un');
        const statusClass = isAuth ? 'status-authorized' : 'status-unauthorized';
        const icon = isAuth ? 'fa-check-circle' : 'fa-times-circle';

        container.innerHTML = `
            <div class="detail-item">
                <label>License Plate</label>
                <span style="font-size: 1.8rem; color: #333;">${data.plate}</span>
            </div>
            <div class="detail-item">
                <label>Status</label>
                <span class="status-badge ${statusClass}"><i class="fas ${icon}" style="margin-right:5px;"></i> ${data.status}</span>
            </div>
            <div class="detail-item">
                <label>Owner / Driver</label>
                <span>${data.owner ? data.owner.f_name + ' ' + data.owner.l_name : 'No Owner Record'}</span>
            </div>
            <div class="detail-item" style="border:none;">
                <label>Time</label>
                <span>${data.timestamp || new Date().toLocaleTimeString()}</span>
            </div>
        `;
        document.getElementById("last-updated").innerText = "Updated: " + new Date().toLocaleTimeString();
    }

    // Poll every 2 seconds
    setInterval(fetchLatestDetection, 2000);
    fetchLatestDetection();
</script>
@endsection