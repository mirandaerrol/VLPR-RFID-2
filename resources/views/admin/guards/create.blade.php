@if ($errors->any())
    <div class="card mb-4" style="background-color: #fef2f2; border-color: #fecaca; padding: 1rem;">
        <ul class="m-0" style="color: #991b1b; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li class="fs-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.guards.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required placeholder="e.g. Juan Dela Cruz" value="{{ old('name') }}">
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required placeholder="guard@school.edu" value="{{ old('email') }}">
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
        <p class="text-slate-400 fs-sm mt-1">Minimum 8 characters.</p>
    </div>

    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <div class="flex-center gap-4 mt-6" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </div>
</form>