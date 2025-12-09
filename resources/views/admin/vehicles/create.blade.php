<!-- VALIDATION ERRORS -->
@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('admin.vehicles.store') }}" method="POST" class="detect-form" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    
    <div class="input-span">
        <label for="owner_id" class="label">Vehicle Owner:</label>
        <select name="owner_id" id="owner_id" required class="input">
            <option value="">Select Owner</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->owner_id }}" {{ old('owner_id') == $owner->owner_id ? 'selected' : '' }}>
                    {{ $owner->f_name }} {{ $owner->l_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="input-span">
        <label for="vehicle_type" class="label">Vehicle Type:</label>
        <select name="vehicle_type" id="vehicle_type" class="input" required>
            <option value="">Select Type</option>
            <option value="Car" {{ old('vehicle_type') == 'Car' ? 'selected' : '' }}>Car</option>
            <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
            <option value="SUV" {{ old('vehicle_type') == 'SUV' ? 'selected' : '' }}>SUV</option>
            <option value="Truck" {{ old('vehicle_type') == 'Truck' ? 'selected' : '' }}>Truck</option>
            <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van</option>
        </select>
    </div>

    <div class="input-span">
        <label for="plate_number" class="label">Plate Number:</label>
        <input type="text" name="plate_number" id="plate_number" class="input" value="{{ old('plate_number', request('plate')) }}" required>
    </div>
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <button type="button" data-bs-dismiss="modal" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button type="submit" class="submit" style="width: auto;">Register Vehicle</button>
    </div>
</form>