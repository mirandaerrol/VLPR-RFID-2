@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('admin.guards.store') }}" method="POST" class="detect-form" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    
    <div class="input-span">
        <label class="label">Full Name</label>
        <input type="text" name="name" class="input" required placeholder="e.g. Juan Dela Cruz" value="{{ old('name') }}">
    </div>

    <div class="input-span">
        <label class="label">Email Address</label>
        <input type="email" name="email" class="input" required placeholder="guard@school.edu" value="{{ old('email') }}">
    </div>

    <div class="input-span">
        <label class="label">Password</label>
        <input type="password" name="password" class="input" required>
        <span style="font-size: 0.8rem; color: #666;">Minimum 8 characters.</span>
    </div>

    <div class="input-span">
        <label class="label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="input" required>
    </div>
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <button type="button" data-bs-dismiss="modal" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button type="submit" class="submit" style="width: auto;">Create Account</button>
    </div>
</form>