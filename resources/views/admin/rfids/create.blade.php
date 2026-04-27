@extends('layouts.dashboard') 

@section('content')
<div class="dashboard-container">   
    <div class="mb-8">
        <h1 class="text-3xl font-800"><i class="fas fa-plus-circle text-primary"></i> Register New RFID</h1>
        <p class="text-slate-500">Add a new RFID tag to the system database for vehicle assignment.</p>
    </div>

    <div class="card" style="max-width: 600px;">   
        <div class="card-padding">
            @if($errors->any())
                <div class="card mb-4" style="background-color: #fef2f2; border-color: #fecaca; padding: 1rem;">
                    <ul class="m-0" style="color: #991b1b; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li class="fs-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.rfids.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rfid_code" class="form-label">RFID Code:</label>
                    <input type="text" name="rfid_code" id="rfid_code" class="form-control" required placeholder="Enter unique RFID code..." autofocus>
                    <p class="text-slate-400 fs-sm mt-2">Example: 0012345678 (10-digit code usually found on the tag)</p>
                </div>

                <div class="flex gap-4 mt-8" style="justify-content: flex-end;">
                    <a href="{{ route('admin.rfids.index') }}" class="btn btn-outline">Cancel</a>                   
                    <button type="submit" class="btn btn-primary">Create RFID Tag</button>
                </div>                               
            </form>
        </div>
    </div>
</div>
@endsection
