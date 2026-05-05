@extends('layouts.master')

@section('title', 'Historical Audit')

@section('extra-styles')
    <style>
        .audit-card {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #f3f4f6;
            margin-bottom: 2rem;
        }
        .audit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
        }
        .audit-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-item h4 {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        .info-item p {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }
    </style>
@endsection

@section('content')
    <div class="audit-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0; color: #1e293b;"><i class="fas fa-history text-purple"></i> Historical Incident Audit</h2>
        <a href="{{ route('master.schedule_management') }}" class="filter-select" style="text-decoration: none; background: #f1f5f9; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to Schedule
        </a>
    </div>

    <div class="audit-card">
        <div style="margin-bottom: 2rem;">
            <h3 style="color: #334155; margin-bottom: 0.5rem;">Audit for {{ $dateTime->format('M d, Y') }} at {{ $dateTime->format('h:i A') }}</h3>
            <p style="color: #64748b;">System snapshot of duty assignments and active logins at this timestamp.</p>
        </div>

        <div class="audit-info-grid">
            <div class="info-item">
                <h4>Assigned Guard</h4>
                <p>{{ $assignment ? $assignment->user->name : 'No guard assigned' }}</p>
            </div>
            <div class="info-item">
                <h4>Shift Period</h4>
                <p>{{ $assignment ? \Carbon\Carbon::parse($assignment->shift_start)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($assignment->shift_end)->format('h:i A') : 'N/A' }}</p>
            </div>
            <div class="info-item">
                <h4>System Status</h4>
                <p>
                    @if($assignment)
                        <span class="badge-auth">Covered</span>
                    @else
                        <span class="badge-unauth">Uncovered</span>
                    @endif
                </p>
            </div>
        </div>

        <h3 style="margin-bottom: 1rem; color: #334155;"><i class="fas fa-sign-in-alt text-blue"></i> Guard Login History (That day)</h3>
        <div class="modal-table-wrapper">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Guard Name</th>
                        <th>Login Timestamp</th>
                        <th>Comparison</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logins as $login)
                    <tr>
                        <td style="font-weight: 700;">{{ $login->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($login->login_at)->format('h:i A') }}</td>
                        <td>
                            @php
                                $loginTime = \Carbon\Carbon::parse($login->login_at);
                                $isAfter = $loginTime->gt($dateTime);
                            @endphp
                            @if($loginTime->isSameMinute($dateTime))
                                <span style="color: #16a34a; font-weight: bold;">Exact Match</span>
                            @else
                                <span style="color: #64748b;">{{ $loginTime->diffForHumans($dateTime) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; padding:15px;">No login records found for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection