@extends('layouts.dashboard')
@include('style')

@section('content')
<div class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Vehicle Owners</h1>
            <button type="button" class="submit" data-bs-toggle="modal" data-bs-target="#createOwnerModal">
                Register New Owner
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
        <table class="vehicle-owner-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Type of Owner</th>
                    <th>RFID Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicleOwners as $owner)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $owner->f_name }} {{ $owner->l_name }}</td>
                        <td>{{ $owner->address }}</td>
                        <td>{{ $owner->contact_number }}</td>
                        <td>{{ $owner->type_of_owner }}</td>

                        <td>
                            @if($owner->rfid_code)
                                <span style="background:#e8f5e9; color:#27ae60; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:0.85rem;">
                                    {{ $owner->rfid_code }}
                                </span>
                            @else
                                <span style="color:#bdc3c7;">--</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="submit"
                                    style="background-color: #3498db;"
                                    data-fname="{{ $owner->f_name }}"
                                    data-lname="{{ $owner->l_name }}"
                                    data-address="{{ $owner->address }}"
                                    data-contact="{{ $owner->contact_number }}"
                                    data-school="{{ $owner->school_year }}"
                                    data-type="{{ $owner->type_of_owner }}"
                                    data-valid="{{ $owner->valid_id }}"
                                    data-rfid-code="{{ $owner->rfid_code }}" 
                                    onclick="openShowOwnerModal(this)">
                                    <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="submit" 
                                    style="background-color: #58bc82;"
                                    data-id="{{ $owner->owner_id }}"
                                    data-fname="{{ $owner->f_name }}"
                                    data-lname="{{ $owner->l_name }}"
                                    data-address="{{ $owner->address }}"
                                    data-contact="{{ $owner->contact_number }}"
                                    data-school="{{ $owner->school_year }}"
                                    data-type="{{ $owner->type_of_owner }}"
                                    data-valid="{{ $owner->valid_id }}"
                                    data-rfid-code="{{ $owner->rfid_code }}" 
                                    onclick="openEditOwnerModal(this)">
                                    <i class="fas fa-edit"></i>
                            </button>
                            <form id="delete-form-{{ $owner->owner_id }}" 
                                  action="{{ route('admin.vehicle_owners.destroy', $owner->owner_id) }}" 
                                  method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete" 
                                        onclick="openDeleteModal('delete-form-{{ $owner->owner_id }}')">
                                        <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="createOwnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Create Vehicle Owner</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Edit Vehicle Owner</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <h2 class="modal-title" style="font-size: 1.5rem; margin:0;">Owner Details</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.vehicle_owner.show')
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
                <p style="color: #666; margin-bottom: 25px;">Do you really want to delete this Owner? This process cannot be undone.</p>
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
    .label { font-weight: bold; margin-bottom: 5px; display: block; color: #555; }
    .input-span { margin-bottom: 15px; }
</style>

<script>
    function openEditOwnerModal(button) {
        var id = button.getAttribute('data-id');
        var fname = button.getAttribute('data-fname');
        var lname = button.getAttribute('data-lname');
        var address = button.getAttribute('data-address');
        var contact = button.getAttribute('data-contact');
        var school = button.getAttribute('data-school');
        var type = button.getAttribute('data-type');
        var valid = button.getAttribute('data-valid');
        var rfidCode = button.getAttribute('data-rfid-code'); // UPDATED

        var form = document.getElementById('editOwnerForm');
        var actionUrl = "{{ route('admin.vehicle_owners.update', ':id') }}";
        form.action = actionUrl.replace(':id', id);

        document.getElementById('edit_f_name').value = fname;
        document.getElementById('edit_l_name').value = lname;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_contact_number').value = contact;
        document.getElementById('edit_school_year').value = school;
        document.getElementById('edit_valid_id').value = valid;
        
        var typeSelect = document.getElementById('edit_type_of_owner');
        if(typeSelect) typeSelect.value = type;

        document.getElementById('edit_rfid_code').value = rfidCode || "";

        var myModal = new bootstrap.Modal(document.getElementById('editOwnerModal'));
        myModal.show();
    }

    function openShowOwnerModal(button) {
        document.getElementById('show_name').innerText = button.getAttribute('data-fname') + ' ' + button.getAttribute('data-lname');
        document.getElementById('show_address').innerText = button.getAttribute('data-address');
        document.getElementById('show_contact').innerText = button.getAttribute('data-contact');
        document.getElementById('show_school').innerText = button.getAttribute('data-school');
        document.getElementById('show_type').innerText = button.getAttribute('data-type');
        document.getElementById('show_valid').innerText = button.getAttribute('data-valid');
        document.getElementById('show_rfid').innerText = button.getAttribute('data-rfid-code'); // UPDATED

        var myModal = new bootstrap.Modal(document.getElementById('showOwnerModal'));
        myModal.show();
    }

    let currentDeleteFormId = null;
    function openDeleteModal(formId) {
        currentDeleteFormId = formId; 
        var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    }
    function confirmDeletionSubmit() {
        if(currentDeleteFormId) document.getElementById(currentDeleteFormId).submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('createOwnerModal'));
            myModal.show();
        @endif
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create') {
            var myModal = new bootstrap.Modal(document.getElementById('createOwnerModal'));
            myModal.show();
        }
    });
</script>
@endsection