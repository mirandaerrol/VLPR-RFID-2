@extends('layouts.master')

@section('title', 'Schedule Management')

@section('content')
    <h2 class="section-title"><i class="fas fa-calendar-alt"></i> Schedule Management</h2>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
        <!-- C. SHIFT SCHEDULING TOOL -->
        <div class="chart-container" style="margin-top: 0;">
            <h3 style="margin-bottom: 1rem; color: #334155;"><i class="fas fa-calendar-alt text-teal"></i> Roster Management</h3>
            <div style="display: flex; gap: 10px;">
                <button onclick="openMasterModal('duplicateModal')" style="flex:1; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; cursor: pointer; text-align: left;">
                    <i class="fas fa-copy text-teal" style="font-size: 1.5rem; margin-bottom: 10px; display: block;"></i>
                    <span style="font-weight: bold; display: block;">Duplicate Schedule</span>
                    <span style="font-size: 0.8rem; color: #64748b;">Copy one day's roster to another.</span>
                </button>
                <button onclick="openMasterModal('dutyModal')" style="flex:1; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; cursor: pointer; text-align: left;">
                    <i class="fas fa-calendar-plus text-purple" style="font-size: 1.5rem; margin-bottom: 10px; display: block;"></i>
                    <span style="font-weight: bold; display: block;">Calendar View</span>
                    <span style="font-size: 0.8rem; color: #64748b;">Manage full-week schedules.</span>
                </button>
            </div>
        </div>

        <!-- D. HISTORICAL INCIDENT AUDIT -->
        <div class="chart-container" style="margin-top: 0;">
            <h3 style="margin-bottom: 1rem; color: #334155;"><i class="fas fa-search-location text-purple"></i> Incident Duty Audit</h3>
            <form action="{{ route('master.historical_audit') }}" method="GET" style="background: #f8fafc; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">Select a date and time to see exactly who was on duty and logged in.</p>
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 2;">
                        <label style="font-size: 0.7rem; color: #64748b;">Incident Date</label>
                        <input type="date" name="search_date" class="filter-select" style="width: 100%;" required>
                    </div>
                    <div style="flex: 2;">
                        <label style="font-size: 0.7rem; color: #64748b;">Incident Time</label>
                        <input type="time" name="search_time" class="filter-select" style="width: 100%;" required>
                    </div>
                    <button type="submit" style="flex: 1; height: 38px; background: #8b5cf6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="chart-container">
        <h3 style="margin-bottom: 1rem; color: #334155;"><i class="fas fa-list text-blue"></i> Recent Duty Assignments</h3>
        <div class="modal-table-wrapper" style="max-height: 400px; overflow-y: auto;">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Guard Name</th>
                        <th>Duty Date</th>
                        <th>Shift Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dutyAssignments->take(20) as $assignment)
                    <tr>
                        <td style="font-weight: 700; color: #1e293b;">{{ $assignment->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($assignment->duty_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($assignment->shift_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($assignment->shift_end)->format('h:i A') }}</td>
                        <td>
                            <form action="{{ route('master.delete_duty', $assignment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; padding:15px;">No active duty assignments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Duty Assignment Modal -->
    <div id="dutyModal" class="custom-modal">
        <div class="modal-content" style="max-width: 1000px;">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-calendar-check text-green"></i> Guard Duty Roster</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button onclick="toggleDutyView()" id="viewToggleBtn" class="filter-select" style="background: #f1f5f9; border: 1px solid #cbd5e1; font-weight: bold;">
                        <i class="fas fa-list"></i> Switch to List View
                    </button>
                    <span class="close-btn" onclick="closeMasterModal('dutyModal')">&times;</span>
                </div>
            </div>
            
            <div id="calendarView">
                <div id="dutyCalendar" style="min-height: 500px; margin-bottom: 20px;"></div>
            </div>

            <div id="listView" style="display: none;">
                <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin-top:0; margin-bottom:10px;">Assign New Duty</h4>
                    <form action="{{ route('master.assign_duty') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; align-items: end;">
                        @csrf
                        <div>
                            <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Select Guard</label>
                            <select name="user_id" id="form_user_id" class="filter-select" style="width:100%;" required>
                                @foreach($guardsList as $guard)
                                    <option value="{{ $guard->id }}">{{ $guard->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Duty Date</label>
                            <input type="date" name="duty_date" id="form_duty_date" class="filter-select" style="width:100%;" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Shift Start</label>
                            <input type="time" name="shift_start" class="filter-select" style="width:100%;" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Shift End</label>
                            <input type="time" name="shift_end" class="filter-select" style="width:100%;" required>
                        </div>
                        <button type="submit" style="background:var(--primary-color); color:white; border:none; padding:10px; border-radius:8px; cursor:pointer; font-weight:bold;">
                            <i class="fas fa-plus"></i> Assign
                        </button>
                    </form>
                </div>

                <div class="modal-table-wrapper">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Guard Name</th>
                                <th>Duty Date</th>
                                <th>Shift Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dutyAssignments as $assignment)
                            <tr>
                                <td style="font-weight: 700; color: #1e293b;">{{ $assignment->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->duty_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->shift_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($assignment->shift_end)->format('h:i A') }}</td>
                                <td>
                                    <form action="{{ route('master.delete_duty', $assignment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center; padding:15px;">No active duty assignments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Duplicate Schedule Modal -->
    <div id="duplicateModal" class="custom-modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-copy text-teal"></i> Duplicate Roster</h3>
                <span class="close-btn" onclick="closeMasterModal('duplicateModal')">&times;</span>
            </div>
            <form action="{{ route('master.duplicate_schedule') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Source Date (Copy from)</label>
                    <input type="date" name="source_date" class="filter-select" style="width:100%;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:0.8rem; color:#64748b; margin-bottom:5px;">Target Date (Paste to)</label>
                    <input type="date" name="target_date" class="filter-select" style="width:100%;" required>
                </div>
                <button type="submit" style="width:100%; background:#14b8a6; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer;">
                    Duplicate Roster
                </button>
            </form>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        // --- CALENDAR LOGIC ---
        let calendar;
        let isListView = false;

        function toggleDutyView() {
            const calView = document.getElementById('calendarView');
            const listView = document.getElementById('listView');
            const toggleBtn = document.getElementById('viewToggleBtn');

            if (isListView) {
                calView.style.display = 'block';
                listView.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-list"></i> Switch to List View';
                if (calendar) calendar.render();
            } else {
                calView.style.display = 'none';
                listView.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-calendar-alt"></i> Switch to Calendar';
            }
            isListView = !isListView;
        }

        const dutyEvents = [
            @foreach($dutyAssignments as $assignment)
            {
                title: '{{ $assignment->user->name }}',
                start: '{{ $assignment->duty_date }}T{{ $assignment->shift_start }}',
                end: '{{ $assignment->duty_date }}T{{ $assignment->shift_end }}',
                backgroundColor: '#3b82f6',
                borderColor: '#2563eb',
                extendedProps: {
                    guardName: '{{ $assignment->user->name }}',
                    shift: '{{ \Carbon\Carbon::parse($assignment->shift_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($assignment->shift_end)->format('h:i A') }}'
                }
            },
            @endforeach
        ];

        function initCalendar() {
            const calendarEl = document.getElementById('dutyCalendar');
            if (!calendarEl) return;

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: dutyEvents,
                eventClick: function(info) {
                    alert('Guard: ' + info.event.extendedProps.guardName + '\n' + info.event.extendedProps.shift);
                },
                dateClick: function(info) {
                    // When a date is clicked, switch to list view and set the date
                    if (!isListView) toggleDutyView();
                    document.getElementById('form_duty_date').value = info.dateStr;
                    document.getElementById('form_user_id').focus();
                },
                height: 'auto',
                themeSystem: 'standard'
            });
            calendar.render();
        }

        // Override original openMasterModal to handle calendar refresh
        const originalOpenModal = openMasterModal;
        window.openMasterModal = function(id) {
            originalOpenModal(id);
            if (id === 'dutyModal') {
                if (!calendar) {
                    initCalendar();
                } else {
                    setTimeout(() => calendar.render(), 100);
                }
            }
        }
    </script>
@endsection