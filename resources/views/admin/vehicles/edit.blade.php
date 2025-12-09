<!-- EDIT FORM (Included in Modal) -->
<form id="editVehicleForm" action="#" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    @method('PUT')
    
    <div class="input-span">
        <label for="edit_owner_id" class="label">Vehicle Owner:</label>
        <select name="owner_id" id="edit_owner_id" required class="input">
            <option value="">Select Owner</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->owner_id }}">
                    {{ $owner->f_name }} {{ $owner->l_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="input-span">
        <label for="edit_vehicle_type" class="label">Vehicle Type:</label>
        <select name="vehicle_type" id="edit_vehicle_type" class="input" required>
            <option value="Car">Car</option>
            <option value="Motorcycle">Motorcycle</option>
            <option value="SUV">SUV</option>
            <option value="Truck">Truck</option>
            <option value="Van">Van</option>
        </select>
    </div>

    <div class="input-span">
        <label for="edit_plate_number" class="label">Plate Number:</label>
        <input type="text" name="plate_number" id="edit_plate_number" class="input" required>
    </div>
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <button type="button" data-bs-dismiss="modal" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button type="submit" class="submit" style="width: auto;">Update Vehicle</button>
    </div>
</form>