@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container">
    <div class="mb-8 flex-between">
        <h1 class="text-3xl font-800"><i class="fas fa-user-tie"></i> Vehicle Owners</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOwnerModal">
            <i class="fas fa-plus"></i> Register New Owner
        </button>
    </div>

    @if(session('success'))
        <div id="success-popup" class="card card-padding mb-6" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
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
                        <th>Type</th>
                        <th>Contact Number</th>
                        <th>RFID Code</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicleOwners as $owner)
                        <tr>
                            <td><span class="text-slate-400 font-600">{{ $loop->iteration }}</span></td>
                            <td><strong class="text-dark">{{ $owner->f_name }} {{ $owner->l_name }}</strong></td>
                            <td>
                                <span class="status-badge {{ $owner->type_of_owner == 'student' ? 'status-authorized' : 'status-pending' }}" style="text-transform: uppercase; font-size: 0.7rem;">
                                    {{ $owner->type_of_owner }}
                                </span>
                            </td>
                            <td class="text-slate-600">{{ $owner->contact_number }}</td>
                            <td>
                                @if($owner->rfid_code)
                                    <code class="font-bold" style="color: var(--green-500); background-color: #f0fdf4; padding: 0.2rem 0.5rem; border-radius: 0.4rem; font-family: monospace;">{{ $owner->rfid_code }}</code>
                                @else
                                    <span class="text-slate-300">--</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="flex gap-2" style="justify-content: flex-end;">
                                    <button type="button" class="btn btn-blue" style="padding: 0.5rem;"
                                            data-fname="{{ $owner->f_name }}"
                                            data-lname="{{ $owner->l_name }}"
                                            data-address="{{ $owner->address }}"
                                            data-contact="{{ $owner->contact_number }}"
                                            data-type="{{ $owner->type_of_owner }}"
                                            data-valid="{{ $owner->valid_id }}"
                                            data-rfid-code="{{ $owner->rfid_code }}" 
                                            onclick="openShowOwnerModal(this)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary" style="padding: 0.5rem;"
                                            data-id="{{ $owner->owner_id }}"
                                            data-fname="{{ $owner->f_name }}"
                                            data-lname="{{ $owner->l_name }}"
                                            data-address="{{ $owner->address }}"
                                            data-contact="{{ $owner->contact_number }}"
                                            data-type="{{ $owner->type_of_owner }}"
                                            data-valid="{{ $owner->valid_id }}"
                                            data-rfid-code="{{ $owner->rfid_code }}" 
                                            onclick="openEditOwnerModal(this)" title="Edit Owner">
                                            <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="delete-form-{{ $owner->owner_id }}" 
                                          action="{{ route('admin.vehicle_owners.destroy', $owner->owner_id) }}" 
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-red" style="padding: 0.5rem;"
                                                onclick="openDeleteModal('delete-form-{{ $owner->owner_id }}')" title="Delete Owner">
                                                <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-400 py-12">No vehicle owners registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="createOwnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-user-plus text-primary"></i> Register Owner</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.vehicle_owner.create')
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editOwnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-user-edit text-primary"></i> Edit Owner Info</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.vehicle_owner.edit')
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="showOwnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-id-card text-primary"></i> Owner Details</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.vehicle_owner.show')
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
                <h2 class="mb-2 font-800 text-dark">Remove Owner?</h2>
                <p class="text-slate-500 mb-6">Are you sure you want to remove this owner? All associated vehicle data will be disconnected.</p>
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