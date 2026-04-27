@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container">
    <div class="mb-8 flex-between">
        <h1 class="text-3xl font-800"><i class="fas fa-list-ul"></i> Vehicle Logs</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fas fa-search"></i> Search Logs
        </button>
    </div>

    @if(session('success'))
        <div id="success-popup" class="card card-padding mb-6 status-authorized" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Registered Vehicles Section -->
    <div class="card">
        <div class="card-header" style="background-color: #f0fdf4; border-bottom-color: #dcfce7;">
            <h3 style="color: #166534;"><i class="fas fa-check-circle"></i> Registered Vehicles</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Plate Number</th>
                        <th>Owner Name</th>
                        <th>Total Logs</th>
                        <th>Last Seen</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="registeredTableBody">
                    @forelse ($registeredLogs as $plateNumber => $logs)
                        @php
                            $mostRecentLog = $logs->first();
                            $ownerName = $mostRecentLog->owner ? $mostRecentLog->owner->f_name . ' ' . $mostRecentLog->owner->l_name : 'No Owner Assigned';
                            $uniqueId = 'reg_' . Str::slug($plateNumber);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong class="text-dark">{{ $plateNumber }}</strong></td>
                            <td class="text-slate-600">{{ $ownerName }}</td>
                            <td><span class="status-badge status-pending" style="padding: 0.2rem 0.6rem;">{{ $logs->count() }}</span></td>
                            <td class="text-slate-500 fs-sm">{{ $mostRecentLog->created_at->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <button type="button" class="btn btn-blue" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"
                                        onclick="openLogDetails('{{ $uniqueId }}', '{{ $plateNumber }}')">
                                    View Details
                                </button>

                                <div id="content-{{ $uniqueId }}" style="display: none;">
                                    <table class="table" style="min-width: 600px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Method</th> 
                                                <th>Type</th>
                                                <th>Time In</th>
                                                <th>Time Out</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($logs as $log)
                                                <tr data-timestamp="{{ $log->created_at->timestamp }}">
                                                    <td class="row-index">{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if($log->detection_method == 'RFID')
                                                            <span class="status-badge status-authorized" style="font-size: 0.7rem; padding: 0.1rem 0.4rem;">RFID</span>
                                                        @else
                                                            <span class="status-badge status-pending" style="font-size: 0.7rem; padding: 0.1rem 0.4rem; background-color: #e0f2fe; color: #0369a1;">PLATE</span>
                                                        @endif
                                                    </td>
                                                    <td class="fs-sm">{{ $log->vehicle_type ?? ($log->vehicle->vehicle_type ?? 'N/A') }}</td>
                                                    <td class="font-bold text-green-500">
                                                        {{ $log->timeLog->time_in ? \Carbon\Carbon::parse($log->timeLog->time_in)->setTimezone('Asia/Manila')->format('H:i:s') : '--' }}
                                                    </td>
                                                    <td class="font-bold text-red-500">
                                                        {{ $log->timeLog->time_out ? \Carbon\Carbon::parse($log->timeLog->time_out)->setTimezone('Asia/Manila')->format('H:i:s') : '--' }}
                                                    </td>
                                                    <td>
                                                        <form id="delete-reg-{{ $log->logs_id }}" action="{{ route('admin.logs.destroy', $log->logs_id) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="btn btn-red" style="padding: 0.3rem 0.6rem;"
                                                                    onclick="openDeleteModal('delete-reg-{{ $log->logs_id }}')">
                                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-400 py-8">No registered vehicle logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-padding" style="border-top: 1px solid var(--slate-100);">
            {{ $registeredLogs->appends(['search' => $search, 'unreg_page' => request('unreg_page')])->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>

    <!-- Unregistered Vehicles Section -->
    <div class="card">
        <div class="card-header" style="background-color: #fef2f2; border-bottom-color: #fee2e2;">
            <h3 style="color: #991b1b;"><i class="fas fa-exclamation-triangle"></i> Unregistered / Unknown</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Detected Plate</th>
                        <th>Status</th>
                        <th>Total Logs</th>
                        <th>Last Seen</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="unregisteredTableBody">
                    @forelse ($unregisteredLogs as $plateNumber => $logs)
                        @php 
                            $mostRecentLog = $logs->first(); 
                            $uniqueId = 'unreg_' . Str::slug($plateNumber);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong style="color: #ef4444;">{{ $plateNumber }}</strong></td>
                            <td><span class="status-badge status-unauthorized" style="font-size: 0.75rem;">Unregistered</span></td>
                            <td><span class="status-badge status-pending" style="padding: 0.2rem 0.6rem;">{{ $logs->count() }}</span></td>
                            <td class="text-slate-500 fs-sm">{{ $mostRecentLog->created_at->setTimezone('Asia/Manila')->format('Y-m-d H:i:s') }}</td>
                            <td class="flex gap-2">
                                <button type="button" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"
                                        onclick="openLogDetails('{{ $uniqueId }}', '{{ $plateNumber }}')">
                                    Details
                                </button>
                                <button type="button" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"
                                        onclick="openRegisterModal('{{ $plateNumber }}')">
                                    Register
                                </button>

                                <div id="content-{{ $uniqueId }}" style="display: none;">
                                    <div class="mb-4" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="openRegisterModal('{{ $plateNumber }}')">
                                            <i class="fas fa-plus-circle"></i> Register This Vehicle
                                        </button>
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Method</th> 
                                                <th>Time In</th>
                                                <th>Time Out</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($logs as $log)
                                                <tr data-timestamp="{{ $log->created_at->timestamp }}">
                                                    <td class="row-index">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="status-badge" style="font-size: 0.7rem; background-color: #e0f2fe; color: #0369a1;">PLATE</span>
                                                    </td>
                                                    <td class="font-bold">{{ $log->timeLog->time_in ? \Carbon\Carbon::parse($log->timeLog->time_in)->setTimezone('Asia/Manila')->format('H:i:s') : '--' }}</td>
                                                    <td class="font-bold">{{ $log->timeLog->time_out ? \Carbon\Carbon::parse($log->timeLog->time_out)->setTimezone('Asia/Manila')->format('H:i:s') : '--' }}</td>
                                                    <td>
                                                        <form id="delete-unreg-{{ $log->logs_id }}" action="{{ route('admin.logs.destroy', $log->logs_id) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="btn btn-red" style="padding: 0.3rem 0.6rem;"
                                                                    onclick="openDeleteModal('delete-unreg-{{ $log->logs_id }}')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-400 py-8">No unregistered logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-padding" style="border-top: 1px solid var(--slate-100);">
            {{ $unregisteredLogs->appends(['search' => $search, 'reg_page' => request('reg_page')])->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-search text-primary"></i> Search Logs</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.logs.index') }}">
                    <div class="form-group">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="search" class="form-control" placeholder="Type plate number..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="flex-center mt-6" style="justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary">Search Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detailsModalTitle" class="font-bold">Log Details</h3>
                <div class="flex gap-4">
                    <select id="logSortOrder" class="form-control" style="width: auto; height: 35px; padding: 0 1rem;" onchange="sortModalLogs()">
                        <option value="desc">Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                    <span class="close-btn" data-bs-dismiss="modal">&times;</span>
                </div>
            </div>
            <div class="modal-body" id="detailsModalBody" style="max-height: 70vh; overflow-y: auto;">
                <!-- Injected via JS -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-car-side text-primary"></i> Register Vehicle</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.vehicles.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Vehicle Owner</label>
                        <select name="owner_id" class="form-control" required>
                            <option value="">-- Select Owner --</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->owner_id }}">{{ $owner->f_name }} {{ $owner->l_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Car">Car</option>
                            <option value="Motorcycle">Motorcycle</option>
                            <option value="SUV">SUV</option>
                            <option value="Truck">Truck</option>
                            <option value="Van">Van</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="plate_number" id="plate_number" class="form-control" required readonly>
                    </div>
                    <div class="flex-center gap-4 mt-6" style="justify-content: flex-end;">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body" style="padding: 2.5rem 1.5rem;">
                <div class="mb-4">
                    <div class="flex-center" style="width: 4rem; height: 4rem; background-color: #fef2f2; border-radius: 50%; margin: 0 auto;">
                        <i class="fas fa-trash-alt" style="font-size: 2rem; color: var(--red-500);"></i>
                    </div>
                </div>               
                <h2 class="mb-2 font-800 text-dark">Delete Log?</h2>
                <p class="text-slate-500 mb-6">Are you sure you want to delete this specific log record? This action cannot be undone.</p>             
                <div class="flex-center gap-4">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="flex: 1;">Cancel</button>
                    <button onclick="confirmDeletionSubmit()" class="btn btn-red" style="flex: 1;">Delete Log</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Periodic refresh
    setInterval(function(){
        if (!document.querySelector('.modal.show')) {
            window.location.reload();
        }
    }, 10000); 
</script>
@endsection