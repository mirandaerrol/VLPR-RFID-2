@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container">
    <div class="mb-8">
        <h1 class="text-3xl font-800"><i class="fas fa-exclamation-circle text-red-500"></i> Unregistered Vehicle Reports</h1>
        <p class="text-slate-500">These vehicles were flagged by guards during real-time monitoring.</p>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Reported Plate</th>
                        <th>Reported By</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong class="status-badge status-unauthorized" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ $report->plate_number }}
                                </strong>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-shield text-slate-400"></i>
                                    <span class="font-600">{{ $report->user->name ?? 'Unknown Guard' }}</span>
                                </div>
                            </td>
                            <td class="text-slate-500">{{ $report->created_at->format('M d, Y • g:i A') }}</td>
                            <td>
                                <span class="status-badge status-pending">Pending Review</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-400 py-12">
                                <i class="fas fa-clipboard-check mb-4" style="font-size: 3rem; display: block; opacity: 0.5;"></i>
                                No unregistered vehicles have been reported yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</div>
@endsection