@extends('layouts.dashboard') 

@section('content')
<div class="dashboard-container">
    <div class="mb-8 flex-between">
        <h1 class="text-3xl font-800"><i class="fas fa-id-card"></i> RFID Management</h1>
        <a href="{{ route('admin.rfids.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New RFID
        </a>
    </div>

    @if (session('success'))
        <div id="success-popup" class="card card-padding mb-6 status-authorized" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>RFID Code</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rfids as $rfid)
                    <tr>
                        <td><span class="text-slate-400 font-600">#{{ $rfid->rfid_id }}</span></td>
                        <td><code class="text-dark font-bold" style="background-color: var(--slate-100); padding: 0.3rem 0.6rem; border-radius: 0.5rem; font-family: monospace;">{{ $rfid->rfid_code }}</code></td>
                        <td>
                            <span class="status-badge status-authorized" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">Active</span>
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.rfids.destroy', $rfid->rfid_id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-red" style="padding: 0.5rem;" onclick="return confirm('Are you sure you want to delete this RFID tag? This might affect registered vehicles.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
