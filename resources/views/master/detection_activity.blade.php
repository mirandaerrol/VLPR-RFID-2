@extends('layouts.master')

@section('title', 'Detection Activity')

@section('extra-styles')
    <style>
        .stat-card.active-chart {
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(88, 188, 130, 0.2);
        }
        .bg-green { background-color: #dcfce7; color: #16a34a; }
        .bg-teal { background-color: #ccfbf1; color: #0d9488; }
    </style>
@endsection

@section('content')
    <div class="section-title-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
        <h2 class="section-title" style="border-bottom: none; margin: 0; padding: 0;"><i class="fas fa-clock"></i> Detection Activity</h2>
        
        <!-- Dynamic Filters -->
        <form method="GET" action="{{ route('master.detection_activity') }}" id="filterForm" style="display: flex; gap: 10px;">
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

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div class="stat-card" id="card-today" onclick="changeChart('today')" title="Click to view today's chart" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%; cursor: pointer; transition: all 0.2s;">
            <div class="stat-icon bg-green" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem;"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Logs Today</h3>
                <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($logsToday) }}</div>
            </div>
        </div>

        <div class="stat-card active-chart" id="card-month" onclick="changeChart('month')" title="Click to view chart for selected month" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%; cursor: pointer; transition: all 0.2s;">
            <div class="stat-icon bg-teal" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem;"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Logs in {{ date('M', mktime(0, 0, 0, $selectedMonth, 1)) }}</h3>
                <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($logsThisMonth) }}</div>
            </div>
        </div>

        <div class="stat-card" id="card-year" onclick="changeChart('year')" title="Click to view chart for selected year" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%; cursor: pointer; transition: all 0.2s;">
            <div class="stat-icon" style="background: #e2e8f0; color: #475569; width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem;"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Logs in {{ $selectedYear }}</h3>
                <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($logsThisYear) }}</div>
            </div>
        </div>
    </div>

    <!-- ROW 3: FULL WIDTH CHART -->
    <div class="chart-container" id="mainChartContainer" style="margin-top: 2rem;">
        <h3 id="chartTitle" style="margin-bottom: 20px; color: #334155; font-size: 1.1rem;">
            <i class="fas fa-chart-bar"></i> Daily Detections ({{ date('F', mktime(0, 0, 0, $selectedMonth, 1)) }} {{ $selectedYear }})
            <span style="font-size: 0.8rem; font-weight: normal; color: #64748b; margin-left: 10px;">(Click a dot to view logs)</span>
        </h3>
        <div style="position: relative; height: 350px; width: 100%; cursor: pointer;">
            <canvas id="logsChart"></canvas>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Chart Details Modal -->
    <div id="chartDetailsModal" class="custom-modal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-list text-blue"></i> Detections for <span id="chartDetailsDate" style="color: var(--primary-color);"></span></h3>
                <span class="close-btn" onclick="closeMasterModal('chartDetailsModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper" style="overflow-x: auto; max-height: 60vh; overflow-y: auto;">
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
@endsection

@section('extra-scripts')
    <script>
        let myChart; 
        let currentPeriod = 'month';

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

            document.getElementById('chartTitle').innerHTML = chartDataSets[period].title + '<span style="font-size: 0.8rem; font-weight: normal; color: #64748b; margin-left: 10px;">(Click a dot to view logs)</span>';

            const ctx = document.getElementById('logsChart').getContext('2d');
            const dataToRender = chartDataSets[period];

            if (myChart) { myChart.destroy(); }

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
                        pointRadius: 6,
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

        function openChartDetails(period, label) {
            document.getElementById('chartDetailsDate').innerText = label;
            document.getElementById('chartDetailsBody').innerHTML = '<tr><td colspan="5" style="text-align:center; padding:20px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6;"></i><br><span style="color:#64748b; margin-top:10px; display:block;">Loading records...</span></td></tr>';
            
            openMasterModal('chartDetailsModal');

            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;

            fetch(`/master/chart-details?period=${period}&label=${label}&month=${month}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('chartDetailsBody');
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:15px; color:#64748b;">No logs found for this specific period.</td></tr>';
                        return;
                    }
                    data.forEach(log => {
                        let statusText = log.time_out ? 'Logged Out' : (log.owner_id ? 'Authorized (In)' : 'Unregistered');
                        let badgeClass = log.owner_id ? 'badge-auth' : 'badge-unauth';
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
                    document.getElementById('chartDetailsBody').innerHTML = '<tr><td colspan="5" style="text-align:center; padding:15px; color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Error loading data.</td></tr>';
                });
        }

        document.addEventListener("DOMContentLoaded", function() {
            changeChart('month');
        });
    </script>
@endsection