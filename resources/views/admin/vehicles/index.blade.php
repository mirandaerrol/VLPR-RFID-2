@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container">
    <div class="mb-8 flex-between">
        <h1 class="text-3xl font-800"><i class="fas fa-car-side"></i> Vehicle Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleModal">
             <i class="fas fa-plus"></i> Register New Vehicle
        </button>
    </div>

    @if(session('success'))
        <div id="success-popup" class="card card-padding mb-6 status-authorized" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th> 
                        <th>Owner Name</th>
                        <th>Vehicle Type</th>
                        <th>Plate Number</th>
                        <th>RFID Code</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td><span class="text-slate-400 font-600">{{ $loop->iteration }}</span></td>
                        <td><strong class="text-dark">{{ $vehicle->owner->f_name }} {{ $vehicle->owner->l_name }}</strong></td>
                        <td>
                            <span class="status-badge" style="background-color: var(--slate-100); color: var(--slate-600); font-size: 0.75rem; text-transform: uppercase;">
                                {{ $vehicle->vehicle_type ?? 'Unknown' }}
                            </span>
                        </td> 
                        <td><code class="font-bold text-dark" style="font-size: 1rem;">{{ $vehicle->plate_number }}</code></td>
                        <td>
                            @if($vehicle->owner && $vehicle->owner->rfid_code)
                                <code class="font-bold" style="color: var(--green-500); background-color: #f0fdf4; padding: 0.2rem 0.5rem; border-radius: 0.4rem; font-family: monospace;">{{ $vehicle->owner->rfid_code }}</code>
                            @else
                                <span class="text-slate-300 fs-xs italic">Unassigned</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="flex gap-2" style="justify-content: flex-end;">
                                <button class="btn btn-primary" style="padding: 0.5rem;"
                                        data-id="{{ $vehicle->vehicle_id }}"
                                        data-owner="{{ $vehicle->owner_id }}"
                                        data-type="{{ $vehicle->vehicle_type }}" 
                                        data-plate="{{ $vehicle->plate_number }}"
                                        onclick="openEditVehicleModal(this)" title="Edit Vehicle">
                                        <i class="fas fa-edit"></i>
                                </button>
                                <form id="delete-form-{{ $vehicle->vehicle_id }}" 
                                      action="{{ route('admin.vehicles.destroy', $vehicle->vehicle_id) }}" 
                                      method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-red" style="padding: 0.5rem;"
                                            onclick="openDeleteModal('delete-form-{{ $vehicle->vehicle_id }}')" title="Delete Vehicle">
                                            <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-400 py-12">No registered vehicles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-car-side text-primary"></i> Register Vehicle</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.vehicles.create')
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-edit text-primary"></i> Edit Vehicle Details</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.vehicles.edit')
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
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--red-500);"></i>
                    </div>
                </div>
                <h2 class="mb-2 font-800 text-dark">Delete Vehicle?</h2>
                <p class="text-slate-500 mb-6">Are you sure you want to delete this vehicle record? This process cannot be undone.</p>
                <div class="flex-center gap-4">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="flex: 1;">Cancel</button>
                    <button onclick="confirmDeletionSubmit()" class="btn btn-red" style="flex: 1;">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const hasErrors = @json($errors->any());
</script>
@endsection