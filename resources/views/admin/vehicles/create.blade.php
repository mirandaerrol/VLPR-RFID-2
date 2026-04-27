@if ($errors->any())
    <div class="card mb-4" style="background-color: #fef2f2; border-color: #fecaca; padding: 1rem;">
        <ul class="m-0" style="color: #991b1b; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li class="fs-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vehicles.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label for="owner_id" class="form-label">Vehicle Owner</label>
        <select name="owner_id" id="owner_id" required class="form-control">
            <option value="">Select Owner</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->owner_id }}" {{ old('owner_id') == $owner->owner_id ? 'selected' : '' }}>
                    {{ $owner->f_name }} {{ $owner->l_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="vehicle_type" class="form-label">Vehicle Type</label>
        <select name="vehicle_type" id="vehicle_type" class="form-control" required>
            <option value="">Select Type</option>
            <option value="Car" {{ old('vehicle_type') == 'Car' ? 'selected' : '' }}>Car</option>
            <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
            <option value="SUV" {{ old('vehicle_type') == 'SUV' ? 'selected' : '' }}>SUV</option>
            <option value="Truck" {{ old('vehicle_type') == 'Truck' ? 'selected' : '' }}>Truck</option>
            <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van</option>
        </select>
    </div>

    <div class="form-group">
        <label for="plate_number" class="form-label">Plate Number</label>
        <input type="text" name="plate_number" id="plate_number" class="form-control" value="{{ old('plate_number', request('plate')) }}" required placeholder="e.g. ABC 1234">
    </div>

    <div class="flex-center gap-4 mt-8" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Register Vehicle</button>
    </div>
</form>