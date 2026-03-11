<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Analytics - VLPR</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Include Chart.js for Statistics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .analytics-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 1rem;
            margin-top: 2rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            margin-top: 2rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .section-title-wrapper .section-title {
            border-bottom: none;
            margin: 0;
            padding: 0;
        }

        /* Grids */
        .stats-grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .stats-grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }

        /* Clickable Stat Cards */
        .stat-card-link {
            text-decoration: none;
            display: block;
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 1rem;
            cursor: pointer;
        }
        .stat-card-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.15);
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            border: 1px solid #f3f4f6;
            height: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .stat-card.active-chart {
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(88, 188, 130, 0.2);
        }
        
        .stat-icon {
            width: 4rem; height: 4rem; border-radius: 1rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.75rem;
            margin-right: 1.5rem; transition: transform 0.3s;
        }

        .stat-card-link:hover .stat-icon, .stat-card:hover .stat-icon { transform: scale(1.1); }

        .stat-info h3 { font-size: 0.9rem; text-transform: uppercase; color: var(--text-gray); font-weight: 700; margin-bottom: 0.25rem; }
        .stat-info .value { font-size: 2.25rem; font-weight: 800; color: var(--text-dark); line-height: 1; }

        /* Colors */
        .bg-purple { background-color: #f3e8ff; color: #7e22ce; }
        .bg-blue { background-color: #e0f2fe; color: #2563eb; }
        .bg-orange { background-color: #ffedd5; color: #d97706; }
        .bg-red { background-color: #fee2e2; color: #dc2626; }
        .bg-green { background-color: #dcfce7; color: #16a34a; }
        .bg-teal { background-color: #ccfbf1; color: #0d9488; }
        .text-blue { color: #2563eb; }
        .text-purple { color: #7e22ce; }
        .text-orange { color: #d97706; }
        .text-red { color: #dc2626; }

        /* Chart Container */
        .chart-container {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #f3f4f6;
            margin-top: 2rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        /* Activity Table Styling */
        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th { background: #f8fafc; padding: 12px 15px; text-align: left; font-size: 0.85rem; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
        .activity-table td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }
        .activity-table tr:hover td { background-color: #f8fafc; }
        
        .badge-auth { background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge-unauth { background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        
        /* Modal Styles */
        .custom-modal {
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0; top: 0; 
            width: 100%; height: 100%; 
            overflow: auto; 
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background-color: #ffffff; 
            margin: 5% auto; 
            padding: 25px;
            width: 90%; 
            max-width: 900px;
            border-radius: 16px; 
            position: relative; 
            animation: slideDown 0.3s ease-out;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;
        }
        .close-btn { color: #94a3b8; font-size: 28px; font-weight: bold; cursor: pointer; transition: 0.2s; line-height: 1; }
        .close-btn:hover { color: #0f172a; }
        .modal-table-wrapper {
            overflow-x: auto;
            max-height: 60vh;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .filter-select {
            padding: 0.4rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            color: #475569;
            background-color: #fff;
            outline: none;
            cursor: pointer;
            height: auto;
        }

        @keyframes slideDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
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

    <div class="analytics-container">
        
        <!-- ROW 1: ENTITY STATISTICS -->
        <h2 class="section-title"><i class="fas fa-database"></i> Database Overview</h2>
        <div class="stats-grid-4">
            <a onclick="openMasterModal('ownersModal')" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Total Owners</h3>
                        <div class="value">{{ number_format($totalOwners) }}</div>
                    </div>
                </div>
            </a>
            
            <a onclick="openMasterModal('vehiclesModal')" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-purple"><i class="fas fa-car"></i></div>
                    <div class="stat-info">
                        <h3>Reg. Vehicles</h3>
                        <div class="value">{{ number_format($totalVehicles) }}</div>
                    </div>
                </div>
            </a>

            <a onclick="openMasterModal('guardsModal')" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-orange"><i class="fas fa-shield-alt"></i></div>
                    <div class="stat-info">
                        <h3>Active Guards</h3>
                        <div class="value">{{ number_format($totalGuards) }}</div>
                    </div>
                </div>
            </a>

            <a onclick="openMasterModal('unregisteredModal')" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <h3>Unregistered Veh.</h3>
                        <div class="value">{{ number_format($totalUnregistered) }}</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- ROW 2: TIME-BASED LOGS (WITH FILTER) -->
        <div class="section-title-wrapper">
            <h2 class="section-title"><i class="fas fa-clock"></i> Detection Activity</h2>
            
            <!-- Dynamic Filters -->
            <form method="GET" action="{{ route('master.dashboard') }}" id="filterForm" style="display: flex; gap: 10px;">
                <select name="month" id="monthFilter" class="filter-select" onchange="this.form.submit()">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" id="yearFilter" class="filter-select" onchange="this.form.submit()">
                    @for($y = \Carbon\Carbon::now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <div class="stats-grid-3">
            <div class="stat-card" id="card-today" onclick="changeChart('today')" title="Click to view today's chart">
                <div class="stat-icon bg-green"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-info">
                    <h3>Logs Today</h3>
                    <div class="value">{{ number_format($logsToday) }}</div>
                </div>
            </div>

            <div class="stat-card active-chart" id="card-month" onclick="changeChart('month')" title="Click to view chart for selected month">
                <div class="stat-icon bg-teal"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-info">
                    <h3>Logs in {{ date('M', mktime(0, 0, 0, $selectedMonth, 1)) }}</h3>
                    <div class="value">{{ number_format($logsThisMonth) }}</div>
                </div>
            </div>

            <div class="stat-card" id="card-year" onclick="changeChart('year')" title="Click to view chart for selected year">
                <div class="stat-icon" style="background: #e2e8f0; color: #475569;"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3>Logs in {{ $selectedYear }}</h3>
                    <div class="value">{{ number_format($logsThisYear) }}</div>
                </div>
            </div>
        </div>

        <!-- ROW 3: FULL WIDTH CHART -->
        <div class="chart-container" id="mainChartContainer">
            <h3 id="chartTitle" style="margin-bottom: 20px; color: #334155; font-size: 1.1rem;">
                <i class="fas fa-chart-bar"></i> Daily Detections ({{ date('F', mktime(0, 0, 0, $selectedMonth, 1)) }} {{ $selectedYear }})
                <span style="font-size: 0.8rem; font-weight: normal; color: #64748b; margin-left: 10px;">(Click a dot to view logs)</span>
            </h3>
            <div style="position: relative; height: 350px; width: 100%; cursor: pointer;">
                <canvas id="logsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!--            MODALS SECTION                  -->
    <!-- ========================================== -->

    <!-- 1. Owners Modal -->
    <div id="ownersModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-users text-blue"></i> Registered Owners Directory</h3>
                <span class="close-btn" onclick="closeMasterModal('ownersModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>RFID Code</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ownersList as $owner)
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;">{{ $owner->f_name }} {{ $owner->l_name }}</td>
                            <td><span class="badge-auth" style="background:#e0f2fe; color:#2563eb;">{{ ucfirst($owner->type_of_owner) }}</span></td>
                            <td>{{ $owner->contact_number }}</td>
                            <td style="font-family: monospace; color: #64748b;">{{ $owner->rfid_code ?? 'Not Set' }}</td>
                            <td>{{ $owner->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding:15px;">No owners found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Vehicles Modal -->
    <div id="vehiclesModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-car text-purple"></i> Registered Vehicles</h3>
                <span class="close-btn" onclick="closeMasterModal('vehiclesModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Plate Number</th>
                            <th>Vehicle Type</th>
                            <th>Owner Name</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiclesList as $vehicle)
                        <tr>
                            <td style="font-weight: 800; font-size: 1.1rem; color: #1e293b;">{{ $vehicle->plate_number }}</td>
                            <td>{{ $vehicle->vehicle_type ?? 'N/A' }}</td>
                            <td style="font-weight: 600;">{{ $vehicle->owner ? $vehicle->owner->f_name.' '.$vehicle->owner->l_name : 'No Owner' }}</td>
                            <td>{{ $vehicle->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:15px;">No vehicles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Guards Modal -->
    <div id="guardsModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-shield-alt text-orange"></i> Active Guard Accounts</h3>
                <span class="close-btn" onclick="closeMasterModal('guardsModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Guard Name</th>
                            <th>Email Address</th>
                            <th>Account Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guardsList as $guard)
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;"><i class="fas fa-user-shield" style="color:#cbd5e1; margin-right:5px;"></i> {{ $guard->name }}</td>
                            <td>{{ $guard->email }}</td>
                            <td>{{ $guard->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:15px;">No guards found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Unregistered Vehicles Modal -->
    <div id="unregisteredModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-exclamation-triangle text-red"></i> Unregistered Vehicles Log</h3>
                <span class="close-btn" onclick="closeMasterModal('unregisteredModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Detected Plate</th>
                            <th>Total Detections</th>
                            <th>Last Seen Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unregisteredList as $unreg)
                        <tr>
                            <td style="font-weight: 800; font-size: 1.1rem; color: #dc2626;">{{ $unreg->detected_plate_number }}</td>
                            <td><span class="badge-unauth" style="background:#f1f5f9; color:#475569;">{{ $unreg->total_detections }} times</span></td>
                            <td>{{ Carbon\Carbon::parse($unreg->last_seen)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:15px;">No unregistered vehicles logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. NEW: Chart Details Modal -->
    <div id="chartDetailsModal" class="custom-modal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-list text-blue"></i> Detections for <span id="chartDetailsDate" style="color: var(--primary-color);"></span></h3>
                <span class="close-btn" onclick="closeMasterModal('chartDetailsModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Plate Number</th>
                            <th>Method</th>
                            <th>Vehicle Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="chartDetailsBody">
                        <tr><td colspan="5" style="text-align:center; padding:15px;"><i class="fas fa-spinner fa-spin"></i> Loading data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript for Chart and Modals -->
    <script>
        // Modal Control Functions
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

        // --- Chart Configuration & Dynamic Updates ---
        
        let myChart; 
        let currentPeriod = 'month'; // Track the active chart view

        // Datasets injected from controller, title injected by PHP
        const selectedMonthText = "{{ date('F', mktime(0, 0, 0, $selectedMonth, 1)) }}";
        const selectedYearText = "{{ $selectedYear }}";

        const chartDataSets = {
            today: {
                labels: {!! json_encode($labelsToday ?? []) !!},
                data: {!! json_encode($dataToday ?? []) !!},
                title: '<i class="fas fa-chart-bar"></i> Hourly Detections (Today)'
            },
            month: {
                labels: {!! json_encode($labelsMonth ?? []) !!},
                data: {!! json_encode($dataMonth ?? []) !!},
                title: '<i class="fas fa-chart-bar"></i> Daily Detections (' + selectedMonthText + ' ' + selectedYearText + ')'
            },
            year: {
                labels: {!! json_encode($labelsYear ?? []) !!},
                data: {!! json_encode($dataYear ?? []) !!},
                title: '<i class="fas fa-chart-bar"></i> Monthly Detections (' + selectedYearText + ')'
            }
        };

        function changeChart(period) {
            currentPeriod = period;

            document.getElementById('card-today').classList.remove('active-chart');
            document.getElementById('card-month').classList.remove('active-chart');
            document.getElementById('card-year').classList.remove('active-chart');
            document.getElementById('card-' + period).classList.add('active-chart');

            // Add the instruction text to the title
            document.getElementById('chartTitle').innerHTML = chartDataSets[period].title + '<span style="font-size: 0.8rem; font-weight: normal; color: #64748b; margin-left: 10px;">(Click a dot to view logs)</span>';

            const ctx = document.getElementById('logsChart').getContext('2d');
            const dataToRender = chartDataSets[period];

            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dataToRender.labels,
                    datasets: [{
                        label: 'Vehicle Logs',
                        data: dataToRender.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6, // Made dots slightly larger to make clicking easier
                        pointHoverRadius: 8,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    },
                    // --- NEW: Add onClick event to handle dot clicks ---
                    onClick: function(evt, activeElements) {
                        if (activeElements.length > 0) {
                            const dataIndex = activeElements[0].index;
                            const label = this.data.labels[dataIndex];
                            openChartDetails(currentPeriod, label);
                        }
                    }
                }
            });
        }

        // --- NEW: Fetch and display chart details ---
        function openChartDetails(period, label) {
            document.getElementById('chartDetailsDate').innerText = label;
            document.getElementById('chartDetailsBody').innerHTML = '<tr><td colspan="5" style="text-align:center; padding:20px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6;"></i><br><span style="color:#64748b; margin-top:10px; display:block;">Loading records...</span></td></tr>';
            
            openMasterModal('chartDetailsModal');

            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;

            // Fetch data from the new endpoint
            fetch(`/master/chart-details?period=${period}&label=${label}&month=${month}&year=${year}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const tbody = document.getElementById('chartDetailsBody');
                    tbody.innerHTML = '';
                    
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:15px; color:#64748b;">No logs found for this specific period.</td></tr>';
                        return;
                    }
                    
                    data.forEach(log => {
                        let statusText = 'Authorized (In)';
                        let badgeClass = 'badge-auth';
                        
                        if (log.time_out) {
                            statusText = 'Logged Out';
                            badgeClass = 'badge-auth'; // Or a different class if you want gray/neutral for logout
                        } else if (!log.owner_id) {
                            statusText = 'Unregistered';
                            badgeClass = 'badge-unauth';
                        }

                        tbody.innerHTML += `
                            <tr>
                                <td style="color: #64748b; font-size: 0.85rem;">${log.formatted_date}</td>
                                <td style="font-weight: 800; color: #1e293b;">${log.plate_number}</td>
                                <td><span style="background:#e2e8f0; color:#475569; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.75rem;">${log.detection_method}</span></td>
                                <td>${log.vehicle_type}</td>
                                <td><span class="${badgeClass}">${statusText}</span></td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error fetching details:', error);
                    document.getElementById('chartDetailsBody').innerHTML = '<tr><td colspan="5" style="text-align:center; padding:15px; color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Error loading data. Please check your connection.</td></tr>';
                });
        }

        document.addEventListener("DOMContentLoaded", function() {
            changeChart('month');
        });
    </script>

</body>
</html>