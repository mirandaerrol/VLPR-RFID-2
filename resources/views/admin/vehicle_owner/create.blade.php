@if ($errors->any())
    <div class="card mb-4" style="background-color: #fef2f2; border-color: #fecaca; padding: 1rem;">
        <ul class="m-0" style="color: #991b1b; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li class="fs-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vehicle_owners.store') }}" method="POST">
    @csrf
    
    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0;">
        <div class="form-group">
            <label for="f_name" class="form-label">First Name</label>
            <input type="text" name="f_name" id="f_name" class="form-control" required value="{{ old('f_name') }}" placeholder="Enter first name...">
        </div>

        <div class="form-group">
            <label for="l_name" class="form-label">Last Name</label>
            <input type="text" name="l_name" id="l_name" class="form-control" required value="{{ old('l_name') }}" placeholder="Enter last name...">
        </div>
    </div>

    <div class="form-group">
        <label for="address" class="form-label">Complete Address</label>
        <input type="text" name="address" id="address" class="form-control" required value="{{ old('address') }}" placeholder="Street, Barangay, City...">
    </div>

    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0;">
        <div class="form-group">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" class="form-control" required value="{{ old('contact_number') }}" placeholder="09xxxxxxxxx">
        </div>

        <div class="form-group">
            <label for="type_of_owner" class="form-label">Owner Type</label>
            <select name="type_of_owner" id="type_of_owner" class="form-control" required>
                <option value="student" {{ old('type_of_owner') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="employee" {{ old('type_of_owner') == 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="valid_id" class="form-label">Valid ID Number</label>
        <input type="text" name="valid_id" id="valid_id" class="form-control" required value="{{ old('valid_id') }}" placeholder="Enter ID number...">
    </div>

    <div class="form-group">
        <label for="rfid_code" class="form-label" style="color: var(--green-500);"><i class="fas fa-wifi"></i> RFID Tag (Optional)</label>
        <input type="text" name="rfid_code" id="rfid_code" class="form-control" placeholder="Scan or type RFID code..." value="{{ old('rfid_code') }}" style="border-color: var(--green-500); background-color: #f0fdf4;">
        <p class="fs-sm text-slate-400 mt-2">Recommended for automatic gate access.</p>
    </div>

    <div class="flex-center gap-4 mt-8" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Register Owner</button>
    </div>
</form>