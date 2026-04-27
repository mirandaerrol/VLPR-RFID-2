<form id="editVehicleForm" action="#" method="POST">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="edit_owner_id" class="form-label">Vehicle Owner</label>
        <select name="owner_id" id="edit_owner_id" required class="form-control">
            <option value="">Select Owner</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->owner_id }}">
                    {{ $owner->f_name }} {{ $owner->l_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="edit_vehicle_type" class="form-label">Vehicle Type</label>
        <select name="vehicle_type" id="edit_vehicle_type" class="form-control" required>
            <option value="Car">Car</option>
            <option value="Motorcycle">Motorcycle</option>
            <option value="SUV">SUV</option>
            <option value="Truck">Truck</option>
            <option value="Van">Van</option>
        </select>
    </div>

    <div class="form-group">
        <label for="edit_plate_number" class="form-label">Plate Number</label>
        <input type="text" name="plate_number" id="edit_plate_number" class="form-control" required placeholder="e.g. ABC 1234">
    </div>

    <div class="flex-center gap-4 mt-8" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Vehicle</button>
    </div>
</form>