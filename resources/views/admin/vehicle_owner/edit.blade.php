<form id="editOwnerForm" action="#" method="POST">
    @csrf
    @method('PUT')
    
    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0;">
        <div class="form-group">
            <label for="edit_f_name" class="form-label">First Name</label>
            <input type="text" name="f_name" id="edit_f_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="edit_l_name" class="form-label">Last Name</label>
            <input type="text" name="l_name" id="edit_l_name" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label for="edit_address" class="form-label">Complete Address</label>
        <input type="text" name="address" id="edit_address" class="form-control" required>
    </div>

    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0;">
        <div class="form-group">
            <label for="edit_contact_number" class="form-label">Contact Number</label>
            <input type="text" name="contact_number" id="edit_contact_number" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="edit_type_of_owner" class="form-label">Owner Type</label>
            <select name="type_of_owner" id="edit_type_of_owner" class="form-control" required>
                <option value="student">Student</option>
                <option value="employee">Employee</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="edit_valid_id" class="form-label">Valid ID Number</label>
        <input type="text" name="valid_id" id="edit_valid_id" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="edit_rfid_code" class="form-label" style="color: var(--green-500);"><i class="fas fa-wifi"></i> Assigned RFID Tag</label>
        <input type="text" name="rfid_code" id="edit_rfid_code" class="form-control" placeholder="Scan or type new RFID code..." style="border-color: var(--green-500); background-color: #f0fdf4;">
        <p class="fs-sm text-slate-400 mt-2">Leave blank if no RFID tag is assigned.</p>
    </div>

    <div class="flex-center gap-4 mt-8" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Information</button>
    </div>
</form>