<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Master Analytics') - VLPR</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Include Chart.js for Statistics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    @include('style')
    <style>
        .master-body {
            background-color: var(--bg-body); 
            color: var(--text-dark);
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
        }
        .navbar-master {
            display: flex; justify-content: space-between; align-items: center;
            background-color: #0f172a; padding: 1rem 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; width: 100%;
        }
        .navbar-master .logo { color: #fff; font-weight: 700; font-size: 1.4rem; margin: 0; }
        
        /* Sidebar & Layout Styles */
        html { scroll-behavior: smooth; }
        .dashboard-wrapper {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        .sidebar {
            width: 280px;
            background-color: #0f172a;
            color: #f8fafc;
            padding: 2rem 1.5rem;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            flex-shrink: 0;
            border-right: 1px solid #1e293b;
            overflow-y: auto;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-item {
            margin-bottom: 0.75rem;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .sidebar-link:hover {
            background-color: #1e293b;
            color: #38bdf8;
        }
        .sidebar-link i {
            margin-right: 1rem;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        .sidebar-link.active {
            background-color: #3b82f6;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .main-content {
            flex-grow: 1;
            background-color: #f8fafc;
            width: 100%;
        }
        
        .analytics-container {
            padding: 2rem 3rem;
            max-width: 1400px;
            margin: 0;
            width: 100%;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Common Components */
        .chart-container {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #f3f4f6;
            margin-bottom: 2rem;
            width: 100%;
        }

        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th { background: #f8fafc; padding: 12px 15px; text-align: left; font-size: 0.85rem; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
        .activity-table td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }
        
        .badge-auth { background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge-unauth { background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }

        .filter-select {
            padding: 0.4rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            color: #475569;
            background-color: #fff;
            outline: none;
        }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 2rem 0.5rem; }
            .sidebar-link span { display: none; }
            .sidebar-link i { margin-right: 0; width: 100%; }
            .analytics-container { padding: 2rem 1.5rem; }
        }

        /* Modal Styles (needed globally for common actions) */
        .custom-modal {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0; 
            width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background-color: #ffffff; margin: 5% auto; padding: 25px;
            width: 90%; max-width: 900px; border-radius: 16px; position: relative; 
            animation: slideDown 0.3s ease-out;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; }
        .close-btn { color: #94a3b8; font-size: 28px; font-weight: bold; cursor: pointer; }
        @keyframes slideDown { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        @yield('extra-styles')
    </style>
</head>
<body class="master-body">

    <nav class="navbar-master">
        <h1 class="logo"><i class="fas fa-chart-line" style="color: #38bdf8;"></i> Master Analytics</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn" style="width:auto; background:rgba(255,255,255,0.1); color:white; border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="{{ route('master.dashboard') }}" class="sidebar-link {{ request()->routeIs('master.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-database"></i>
                        <span>Database Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('master.live_monitoring') }}" class="sidebar-link {{ request()->routeIs('master.live_monitoring') ? 'active' : '' }}">
                        <i class="fas fa-satellite-dish"></i>
                        <span>Live Duty Monitoring</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('master.schedule_management') }}" class="sidebar-link {{ request()->routeIs('master.schedule_management') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedule Management</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('master.detection_activity') }}" class="sidebar-link {{ request()->routeIs('master.detection_activity') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Detection Activity</span>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="analytics-container">
                @if(session('success'))
                    <div style="background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bcf0da;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @yield('modals')

    <script>
        function openMasterModal(id) {
            document.getElementById(id).style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        }

        function closeMasterModal(id) {
            document.getElementById(id).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = 'auto';
            }
        }
    </script>
    @yield('extra-scripts')
</body>
</html>