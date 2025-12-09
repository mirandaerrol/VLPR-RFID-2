@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vehicle_owners.store') }}" method="POST" class="detect-form" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    <div class="input-span">
        <label for="f_name" class="label">First Name:</label>
        <input type="text" name="f_name" id="f_name" class="input" required value="{{ old('f_name') }}">
    </div>

    <div class="input-span">
        <label for="l_name" class="label">Last Name:</label>
        <input type="text" name="l_name" id="l_name" class="input" required value="{{ old('l_name') }}">
    </div>

    <div class="input-span">
        <label for="address" class="label">Address:</label>
        <input type="text" name="address" id="address" class="input" required value="{{ old('address') }}">
    </div>

    <div class="input-span">
        <label for="contact_number" class="label">Contact Number:</label>
        <input type="text" name="contact_number" id="contact_number" class="input" required value="{{ old('contact_number') }}">
    </div>

    <div class="input-span">
        <label for="school_year" class="label">School Year:</label>
        <input type="text" name="school_year" id="school_year" class="input" value="{{ old('school_year') }}">
    </div>

    <div class="input-span">
        <label for="type_of_owner" class="label">Type of Owner:</label>
        <select name="type_of_owner" id="type_of_owner" class="input" required>
            <option value="student" {{ old('type_of_owner') == 'student' ? 'selected' : '' }}>Student</option>
            <option value="employee" {{ old('type_of_owner') == 'employee' ? 'selected' : '' }}>Employee</option>
        </select>
    </div>

    <div class="input-span">
        <label for="valid_id" class="label">Valid ID:</label>
        <input type="text" name="valid_id" id="valid_id" class="input" required value="{{ old('valid_id') }}">
    </div>
    <div class="input-span">
        <label for="rfid_code" class="label" style="color: #27ae60;">RFID Tag (Optional):</label>
        <input type="text" name="rfid_code" id="rfid_code" class="input" placeholder="Scan or type RFID code..." value="{{ old('rfid_code') }}">
    </div>

    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <button type="button" data-bs-dismiss="modal" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button type="submit" class="submit" style="width: auto;">Create</button>
    </div>
</form>