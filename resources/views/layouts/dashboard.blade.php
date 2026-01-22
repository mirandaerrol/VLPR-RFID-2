<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VLPR System - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    @include('style')
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-shield-alt"></i>
            <h2>VLPR System</h2>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-title">HOME</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                <i class="fas fa-home"></i> HOME
            </a>
            <div class="nav-section-title">Vehicle Entry</div>
            
            <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Logs
            </a>
            
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Reports
            </a>
            <div class="nav-section-title">Management</div>

            <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                <i class="fas fa-car"></i> Vehicle
            </a>

            <a href="{{ route('admin.vehicle_owners.index') }}" class="nav-link {{ request()->routeIs('admin.vehicle_owners.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i> Owner
            </a>

            <a href="{{ route('admin.guards.index') }}" class="nav-link {{ request()->routeIs('admin.guards.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Users
            </a>
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </nav>
    <main class="main-wrapper">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>