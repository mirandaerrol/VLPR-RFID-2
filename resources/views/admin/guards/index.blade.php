@extends('layouts.dashboard')

@section('content')
<div class="dashboard-container">
    <div class="mb-8 flex-between">
        <h1 class="text-3xl font-800"><i class="fas fa-users-shield"></i> Guard Accounts</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#guardModal">
             <i class="fas fa-plus"></i> Register New Guard
        </button>
    </div>

    @if(session('success'))
        <div id="success-popup" class="card card-padding mb-6" style="background-color: #f0fdf4; border-color: #bbf7d0;">
            <div class="flex-between">
                <span class="font-600" style="color: #166534;"><i class="fas fa-check-circle"></i> {{ session('success') }}</span>
                <i class="fas fa-times close-btn" onclick="this.parentElement.parentElement.style.display='none'"></i>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guards as $guard)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong class="text-dark">{{ $guard->name }}</strong></td>
                            <td class="text-slate-500">{{ $guard->email }}</td>
                            <td class="text-slate-400 fs-sm">{{ $guard->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <button type="button" class="btn btn-red" style="padding: 0.5rem;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        onclick="setDeleteAction('{{ route('admin.guards.destroy', $guard->id) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-400 py-8">No guard accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $guards->links() }}
    </div>
</div>

<div class="modal fade" id="guardModal" tabindex="-1" aria-labelledby="guardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="font-bold"><i class="fas fa-user-shield text-primary"></i> Register Guard</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                @include('admin.guards.create')
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body" style="padding: 2.5rem 1.5rem;">
                <div class="mb-4">
                    <div class="flex-center" style="width: 4rem; height: 4rem; background-color: #fef2f2; border-radius: 50%; margin: 0 auto;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--red-500);"></i>
                    </div>
                </div>               
                <h2 class="mb-2 font-800 text-dark">Are you sure?</h2>
                <p class="text-slate-500 mb-6">Do you really want to delete this guard account? This process cannot be undone.</p>             
                <div class="flex-center gap-4">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="flex: 1;">Cancel</button>
                    <form id="deleteGuardForm" action="" method="POST" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red w-full flex-center">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const hasErrors = @json($errors->any());
</script>

@endsection