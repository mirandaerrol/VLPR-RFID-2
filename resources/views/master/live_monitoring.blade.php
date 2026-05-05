@extends('layouts.master')

@section('title', 'Live Duty Monitoring')

@section('content')
    <h2 class="section-title"><i class="fas fa-satellite-dish"></i> Live Duty Monitoring</h2>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 1rem;">
        
        <!-- A. LIVE STATUS WIDGET -->
        <div class="chart-container" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.5rem; color: #334155;"><i class="fas fa-satellite-dish text-red"></i> Live Guard Status</h3>
            
            @if($currentDuty)
                <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
                    <div style="flex: 1; min-width: 250px; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h4 style="margin:0; font-size: 1.2rem;">{{ $currentDuty->user->name }}</h4>
                                <span class="badge-auth" style="background: #dcfce7; color: #16a34a; font-size: 0.7rem;">ON DUTY</span>
                            </div>
                        </div>
                        
                        <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem;">
                            <div><i class="fas fa-clock"></i> Shift: {{ \Carbon\Carbon::parse($currentDuty->shift_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($currentDuty->shift_end)->format('h:i A') }}</div>
                            <div><i class="fas fa-sign-in-alt"></i> Last Auth: {{ $currentDuty->user->last_login_at ? \Carbon\Carbon::parse($currentDuty->user->last_login_at)->format('h:i A') : 'Never' }}</div>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <form action="{{ route('master.force_logout', $currentDuty->user_id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: #ef4444; font-size: 0.8rem; padding: 8px 12px; border: none; color: white; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-power-off"></i> Force Logout
                                </button>
                            </form>
                            <button onclick="openMasterModal('reassignModal')" style="background: #3b82f6; font-size: 0.8rem; padding: 8px 12px; border: none; color: white; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-exchange-alt"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 200px; text-align: center;">
                        <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 10px;">Time Remaining</div>
                        @php
                            $end = \Carbon\Carbon::parse($currentDuty->duty_date . ' ' . $currentDuty->shift_end);
                            $diff = now()->diff($end);
                        @endphp
                        <div style="font-size: 2.5rem; font-weight: 800; color: #334155;">
                            {{ $diff->h }}h {{ $diff->i }}m
                        </div>
                        <div style="margin-top: 10px;">
                            <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                @php
                                    $start = \Carbon\Carbon::parse($currentDuty->duty_date . ' ' . $currentDuty->shift_start);
                                    $total = $start->diffInMinutes($end);
                                    $elapsed = $start->diffInMinutes(now());
                                    $percent = min(100, max(0, ($elapsed / $total) * 100));
                                @endphp
                                <div style="width: {{ $percent }}%; height: 100%; background: #3b82f6;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; background: #fef2f2; border: 1px dashed #ef4444; border-radius: 12px;">
                    <i class="fas fa-user-slash" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                    <h4 style="color: #991b1b;">No Guard Assigned</h4>
                    <p style="color: #b91c1c; font-size: 0.9rem;">There is currently no guard scheduled for this time slot.</p>
                    <a href="{{ route('master.schedule_management') }}" style="display:inline-block; background: #ef4444; color: white; text-decoration:none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        <i class="fas fa-plus"></i> Assign Now
                    </a>
                </div>
            @endif
        </div>

        <!-- B. QUICK ALERTS -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="chart-container" style="padding: 1rem; border-left: 4px solid #f59e0b; margin-bottom: 0;">
                <h4 style="margin:0 0 10px 0; font-size: 0.9rem;"><i class="fas fa-user-clock text-orange"></i> No-Show Alerts</h4>
                @forelse($noShowAlerts as $alert)
                    <div style="background: #fffbeb; padding: 8px; border-radius: 6px; margin-bottom: 5px; font-size: 0.8rem; display: flex; justify-content: space-between;">
                        <span><strong>{{ $alert->user->name }}</strong></span>
                        <span style="color: #92400e;">(Shift: {{ \Carbon\Carbon::parse($alert->shift_start)->format('h:i A') }})</span>
                    </div>
                @empty
                    <p style="font-size: 0.8rem; color: #64748b;">All guards checked in on time.</p>
                @endforelse
            </div>

            <div class="chart-container" style="padding: 1rem; border-left: 4px solid #ef4444; margin-bottom: 0;">
                <h4 style="margin:0 0 10px 0; font-size: 0.9rem;"><i class="fas fa-user-lock text-red"></i> Unauthorized Access</h4>
                @if($offDutyAttempts->count() > 0)
                    <p style="font-size: 0.8rem; color: #ef4444;"><strong>{{ $offDutyAttempts->count() }}</strong> attempts detected.</p>
                    <button onclick="openMasterModal('offDutyModal')" style="width:100%; font-size: 0.75rem; background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; padding: 5px; border-radius: 4px; cursor:pointer;">View Logs</button>
                @else
                    <p style="font-size: 0.8rem; color: #64748b;">No unauthorized attempts.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Reassign Modal -->
    <div id="reassignModal" class="custom-modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-exchange-alt text-blue"></i> Reassign Current Duty</h3>
                <span class="close-btn" onclick="closeMasterModal('reassignModal')">&times;</span>
            </div>
            <form action="{{ route('master.reassign_duty') }}" method="POST">
                @csrf
                <input type="hidden" name="assignment_id" value="{{ $currentDuty->id ?? '' }}">
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:10px;">Select New Guard to take over shift:</label>
                    <select name="new_user_id" class="filter-select" style="width:100%;" required>
                        @foreach($guardsList as $guard)
                            <option value="{{ $guard->id }}" {{ ($currentDuty && $currentDuty->user_id == $guard->id) ? 'disabled' : '' }}>
                                {{ $guard->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="width:100%; background:#3b82f6; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer;">
                    Confirm Reassignment
                </button>
            </form>
        </div>
    </div>

    <!-- Off-Duty Access Logs Modal -->
    <div id="offDutyModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-user-lock text-red"></i> Unauthorized Access Attempts</h3>
                <span class="close-btn" onclick="closeMasterModal('offDutyModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Guard Name</th>
                            <th>Attempted Time</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offDutyAttempts as $attempt)
                        <tr>
                            <td style="font-weight:700;">{{ $attempt->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($attempt->attempted_at)->format('M d, Y h:i A') }}</td>
                            <td>{{ $attempt->ip_address }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;">No unauthorized attempts logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection