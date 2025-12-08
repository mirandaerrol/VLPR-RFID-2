@extends('layouts.dashboard')
@include('style')

@section('content')
<div class="dashboard-container">
    <div class="card">
        <h1>Reported Unregistered Vehicles</h1>
        <p>These vehicles were flagged by guards at the gate.</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Reported Plate</th>
                    <th>Reported By (Guard)</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->report_id }}</td>
                        <td>
                            <strong class="reports">
                                {{ $report->plate_number }}
                            </strong>
                        </td>
                        <td>{{ $report->user->name ?? 'Unknown Guard' }}</td>
                        <td>{{ $report->created_at->format('F j, Y, g:i a') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection