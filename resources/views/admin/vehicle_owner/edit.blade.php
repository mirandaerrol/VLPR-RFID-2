<form id="editOwnerForm" action="#" method="POST" class="detect-form" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    @method('PUT')
    
    <div class="input-span">
        <label for="edit_f_name" class="label">First Name:</label>
        <input type="text" name="f_name" id="edit_f_name" class="input" required>
    </div>

    <div class="input-span">
        <label for="edit_l_name" class="label">Last Name:</label>
        <input type="text" name="l_name" id="edit_l_name" class="input" required>
    </div>

    <div class="input-span">
        <label for="edit_address" class="label">Address:</label>
        <input type="text" name="address" id="edit_address" class="input" required>
    </div>

    <div class="input-span">
        <label for="edit_contact_number" class="label">Contact Number:</label>
        <input type="text" name="contact_number" id="edit_contact_number" class="input" required>
    </div>

    <div class="input-span">
        <label for="edit_school_year" class="label">School Year:</label>
        <input type="text" name="school_year" id="edit_school_year" class="input">
    </div>

    <div class="input-span">
        <label for="edit_type_of_owner" class="label">Type of Owner:</label>
        <select name="type_of_owner" id="edit_type_of_owner" class="input" required>
            <option value="student">Student</option>
            <option value="employee">Employee</option>
        </select>
    </div>

    <div class="input-span">
        <label for="edit_valid_id" class="label">Valid ID:</label>
        <input type="text" name="valid_id" id="edit_valid_id" class="input" required>
    </div>

    <div class="input-span">
        <label for="edit_rfid_code" class="label" style="color: #27ae60;">RFID Tag:</label>
        <input type="text" name="rfid_code" id="edit_rfid_code" class="input" placeholder="Scan or type RFID code...">
    </div>

    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <button type="button" data-bs-dismiss="modal" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button type="submit" class="submit" style="width: auto;">Update</button>
    </div>
</form>