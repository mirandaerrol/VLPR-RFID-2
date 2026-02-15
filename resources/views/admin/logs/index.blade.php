@extends('layouts.dashboard')
@include('style')

@section('content')
<div class="dashboard-container">
    <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="margin:0;">Vehicle Logs</h1>
        
        <button type="button" class="submit" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fas fa-search"></i> Search Logs
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!--Registered vehicle-->
    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: #27ae60; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            <i class="fas fa-check-circle"></i> Registered Vehicles
        </h2>

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
                    
                    <tr class="log-group-header">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $plateNumber }}</strong></td>
                        <td>{{ $ownerName }}</td>
                        <td>{{ $logs->count() }}</td>
                        <td>{{ $mostRecentLog->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <button type="button" class="submit" style="background-color: #3498db;"
                                    onclick="openLogDetails('{{ $uniqueId }}', '{{ $plateNumber }}')">
                                View Details
                            </button>

                            <!-- HIDDEN CONTENT -->
                            <div id="content-{{ $uniqueId }}" style="display: none;">
                                <table class="table table-bordered table-striped" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th> <!-- Changed from ID to # -->
                                            <th style="width: 10%;">METHOD</th> 
                                            <th style="width: 15%;">TYPE</th>
                                            <th style="width: 15%;">DATE</th>
                                            <th style="width: 15%;">TIME IN</th>
                                            <th style="width: 15%;">TIME OUT</th>
                                            <th style="width: 10%;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td> <!-- Using loop iteration for sequential numbering -->
                                                <td>
                                                    @if($log->detection_method == 'RFID')
                                                        <span style="background:#e8f5e9; color:#2e7d32; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.8rem;">RFID</span>
                                                    @elseif($log->detection_method == 'PLATE' || $log->detection_method == 'CAMERA')
                                                        <span style="background:#e3f2fd; color:#1565c0; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.8rem;">PLATE</span>
                                                    @else
                                                        <span style="color:#666;">--</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->vehicle_type ?? ($log->vehicle->vehicle_type ?? 'N/A') }}</td>
                                                <td>{{ $log->created_at->format('d/m/Y') }}</td>
                                                <td style="color: #27ae60; font-weight: bold;">
                                                    {{ $log->timeLog->time_in ? \Carbon\Carbon::parse($log->timeLog->time_in)->format('H:i:s') : '--' }}
                                                </td>
                                                <td style="color: #c0392b; font-weight: bold;">
                                                    {{ $log->timeLog->time_out ? \Carbon\Carbon::parse($log->timeLog->time_out)->format('H:i:s') : '--' }}
                                                </td>
                                                <td style="text-align: center;">
                                                    <form id="delete-reg-{{ $log->logs_id }}" action="{{ route('admin.logs.destroy', $log->logs_id) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="openDeleteModal('delete-reg-{{ $log->logs_id }}')">
                                                            Delete
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
                    <tr><td colspan="6" style="text-align: center; padding: 1rem;">No registered vehicle logs found.</td></tr> <!-- Updated colspan -->
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 15px; display: flex; justify-content: center;">
            {{ $registeredLogs->appends(['search' => $search, 'unreg_page' => request('unreg_page')])->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>


    <!--unregistered and unknown vehicle-->
    <div class="card">
        <h2 style="color: #c0392b; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            <i class="fas fa-exclamation-triangle"></i> Unregistered / Unknown
        </h2>

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
                    
                    <tr class="log-group-header">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong style="color: #c0392b;">{{ $plateNumber }}</strong></td>
                        <td><span style="background:#fadbd8; color:#c0392b; padding:2px 8px; border-radius:4px;">Unregistered</span></td>
                        <td>{{ $logs->count() }}</td>
                        <td>{{ $mostRecentLog->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <button type="button" class="submit" style="background-color: #7f8c8d; margin-right: 5px;"
                                    onclick="openLogDetails('{{ $uniqueId }}', '{{ $plateNumber }}')">
                                View Details
                            </button>

                            <button type="button" class="submit" style="background-color: #f39c12;"
                                    onclick="openRegisterModal('{{ $plateNumber }}')">
                                Register
                            </button>

                            <!-- HIDDEN CONTENT -->
                            <div id="content-{{ $uniqueId }}" style="display: none;">
                                 <div class="mb-3 text-end">
                                    <button type="button" class="btn btn-warning btn-sm" onclick="openRegisterModal('{{ $plateNumber }}')">
                                        Register This Vehicle
                                    </button>
                                </div>
                                <table class="table table-bordered table-striped" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th> <!-- Changed from ID to # -->
                                            <th style="width: 10%;">METHOD</th> 
                                            <th style="width: 15%;">TYPE</th> 
                                            <th style="width: 15%;">DATE</th>
                                            <th style="width: 15%;">TIME IN</th>
                                            <th style="width: 15%;">TIME OUT</th>
                                            <th style="width: 10%;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td> <!-- Using loop iteration for sequential numbering -->
                                                <td>
                                                    @if($log->detection_method == 'RFID')
                                                        <span style="background:#e8f5e9; color:#2e7d32; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.8rem;">RFID</span>
                                                    @else
                                                        <span style="background:#e3f2fd; color:#1565c0; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.8rem;">PLATE</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->vehicle_type ?? 'N/A' }}</td>
                                                <td>{{ $log->created_at->format('d/m/Y') }}</td>
                                                <td style="font-weight: bold;">
                                                    {{ $log->timeLog->time_in ? \Carbon\Carbon::parse($log->timeLog->time_in)->format('H:i:s') : '--' }}
                                                </td>
                                                <td style="font-weight: bold;">
                                                    {{ $log->timeLog->time_out ? \Carbon\Carbon::parse($log->timeLog->time_out)->format('H:i:s') : '--' }}
                                                </td>
                                                <td style="text-align: center;">
                                                    <form id="delete-unreg-{{ $log->logs_id }}" action="{{ route('admin.logs.destroy', $log->logs_id) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="openDeleteModal('delete-unreg-{{ $log->logs_id }}')">
                                                            Delete
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
                    <tr><td colspan="6" style="text-align: center; padding: 1rem;">No unregistered logs found.</td></tr> <!-- Updated colspan -->
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 15px; display: flex; justify-content: center;">
            {{ $unregisteredLogs->appends(['search' => $search, 'reg_page' => request('reg_page')])->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ========================================== -->
<!--            MODALS SECTION                  -->
<!-- ========================================== -->

<!-- 1. SEARCH MODAL -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Vehicle Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.logs.index') }}">
                    <div class="mb-3">
                        <label for="logSearchModal" class="form-label">Plate Number</label>
                        <input type="text" name="search" id="logSearchModal" class="form-control" placeholder="Type plate number..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 2. DETAILS MODAL -->
<!-- UPDATED: Added inline style to FORCE 95% WIDTH to fix the narrow modal issue -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true" style="padding-right: 0;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 95vw; margin-left: auto; margin-right: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalTitle">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsModalBody" style="padding: 20px; overflow-x: auto;">
                <!-- Content injected by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. REGISTER VEHICLE MODAL -->
<div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Register Vehicle</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.vehicles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="owner_id" class="form-label" style="font-weight: bold;">Vehicle Owner:</label>
                        <select name="owner_id" id="owner_id" class="form-control" required>
                            <option value="">-- Select Owner --</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->owner_id }}">
                                    {{ $owner->f_name }} {{ $owner->l_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Added Vehicle Type Select -->
                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label" style="font-weight: bold;">Vehicle Type:</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Car">Car</option>
                            <option value="Motorcycle">Motorcycle</option>
                            <option value="SUV">SUV</option>
                            <option value="Truck">Truck</option>
                            <option value="Van">Van</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="plate_number" class="form-label" style="font-weight: bold;">Plate Number:</label>
                        <input type="text" name="plate_number" id="plate_number" class="form-control" required readonly style="background-color: #e9ecef;">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="submit" style="border:none;">Register Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 4. DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="text-align: center;">
            <div class="modal-body p-4">
                <div style="margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c;"></i>
                </div>
                <h2 style="margin-bottom: 10px; color: #333;">Are you sure?</h2>
                <p style="color: #666; margin-bottom: 25px;">Do you really want to delete this log? This process cannot be undone.</p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                    <button onclick="confirmDeletionSubmit()" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. DETAILS MODAL LOGIC ---
    function openLogDetails(uniqueId, plateNumber) {
        var content = document.getElementById('content-' + uniqueId).innerHTML;
        document.getElementById('detailsModalBody').innerHTML = content;
        document.getElementById('detailsModalTitle').innerText = 'Detailed Logs for ' + plateNumber;
        var myModal = new bootstrap.Modal(document.getElementById('detailsModal'));
        myModal.show();
    }

    // --- 2. REGISTER MODAL LOGIC ---
    function openRegisterModal(plateNumber) {
        var plateInput = document.getElementById('plate_number');
        if(plateInput) {
            plateInput.value = plateNumber;
        }
        var myModal = new bootstrap.Modal(document.getElementById('vehicleModal'));
        myModal.show();
    }

    // --- 3. DELETE MODAL LOGIC ---
    let currentDeleteFormId = null;
    function openDeleteModal(formId) {
        currentDeleteFormId = formId; 
        var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    }
    function confirmDeletionSubmit() {
        if(currentDeleteFormId) {
            document.getElementById(currentDeleteFormId).submit();
        }
    }

    // --- AUTO REFRESH LOGIC ---
    setInterval(function(){
        // Only reload if no modal is open (to prevent interrupting user interaction)
        if (!document.querySelector('.modal.show')) {
            window.location.reload();
        }
    }, 5000); // 5000 milliseconds = 5 seconds
</script>
@endsection