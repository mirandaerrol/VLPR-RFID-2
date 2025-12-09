@extends('layouts.dashboard')
@include('style')

@section('content')
<div class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Vehicle List</h1>
            <button type="button" class="submit" data-bs-toggle="modal" data-bs-target="#vehicleModal">
                 Register New Vehicle
            </button>
        </div>
        @if(session('success'))
            <div id="success-popup" style="background:#d4edda; color:#155724; padding:1rem; margin-bottom:1rem; border-radius:5px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    const popup = document.getElementById('success-popup');
                    if(popup) popup.style.display = 'none';
                }, 3000);
            </script>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>#</th> 
                    <th>Owner Name</th>
                    <th>Vehicle Type</th>
                    <th>Plate Number</th>
                    <th>RFID Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $vehicle)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $vehicle->owner->f_name }} {{ $vehicle->owner->l_name }}</td>
                    <td>{{ $vehicle->vehicle_type ?? 'N/A' }}</td> 
                    <td>{{ $vehicle->plate_number }}</td>
                    
                    <td>
                        @if($vehicle->owner && $vehicle->owner->rfid_code)
                            <span style="background:#e8f5e9; color:#27ae60; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.85rem;">
                                {{ $vehicle->owner->rfid_code }}
                            </span>
                        @else
                            <span style="color:#bdc3c7;">Unassigned</span>
                        @endif
                    </td>
                    
                    <td>
                        <button class="submit" 
                                style="background-color: #58bc82;"
                                data-id="{{ $vehicle->vehicle_id }}"
                                data-owner="{{ $vehicle->owner_id }}"
                                data-type="{{ $vehicle->vehicle_type }}" 
                                data-plate="{{ $vehicle->plate_number }}"
                                onclick="openEditVehicleModal(this)">
                                <i class="fas fa-edit"></i>
                        </button>
                        <form id="delete-form-{{ $vehicle->vehicle_id }}" 
                              action="{{ route('admin.vehicles.destroy', $vehicle->vehicle_id) }}" 
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            
                            <button type="button" class="delete" 
                                    onclick="openDeleteModal('delete-form-{{ $vehicle->vehicle_id }}')">
                                    <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                @if($vehicles->isEmpty())
                <tr>
                    <td colspan="6" style="text-align:center;">No vehicles found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Register Vehicle</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Edit Vehicle</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.vehicles.edit')
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="text-align: center;">
            <div class="modal-body p-4">
                <div style="margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c;"></i>
                </div>
                <h2 style="margin-bottom: 10px; color: #333;">Are you sure?</h2>
                <p style="color: #666; margin-bottom: 25px;">Do you really want to delete this vehicle? This process cannot be undone.</p>
                
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" data-bs-dismiss="modal" style="padding: 10px 25px; background: #bdc3c7; color: #333; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Cancel</button>
                    <button onclick="confirmDeletionSubmit()" style="padding: 10px 25px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
</style>

<script>
    function openEditVehicleModal(button) {
        var id = button.getAttribute('data-id');
        var owner = button.getAttribute('data-owner');
        var type = button.getAttribute('data-type');
        var plate = button.getAttribute('data-plate');

        var form = document.getElementById('editVehicleForm');
        var actionUrl = "{{ route('admin.vehicles.update', ':id') }}";
        form.action = actionUrl.replace(':id', id);

        if(document.getElementById('edit_plate_number')) document.getElementById('edit_plate_number').value = plate;
        if(document.getElementById('edit_owner_id')) document.getElementById('edit_owner_id').value = owner;
        if(document.getElementById('edit_vehicle_type')) document.getElementById('edit_vehicle_type').value = type; // Set Type

        var myModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
        myModal.show();
    }
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
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('vehicleModal'));
            myModal.show();
        @endif

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create') {
            var myModal = new bootstrap.Modal(document.getElementById('vehicleModal'));
            myModal.show();
        }
    });
</script>

@endsection