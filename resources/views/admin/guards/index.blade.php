@extends('layouts.dashboard')
@include('style')

@section('content')
<div class="dashboard-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Guard Accounts</h1>
            <button type="button" class="submit" data-bs-toggle="modal" data-bs-target="#guardModal">
                 Register New Guard
            </button>
        </div>
        @if(session('success'))
            <div id="success-popup" style="background:#d4edda; color:#155724; padding:1rem; margin-bottom:1rem; border-radius:5px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    const popup = document.getElementById('success-popup');
                    if(popup) popup.style.display = 'none';
                }, 3000);
            </script>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guards as $guard)
                    <tr>
                        <td>{{ $guard->id }}</td>
                        <td><strong>{{ $guard->name }}</strong></td>
                        <td>{{ $guard->email }}</td>
                        <td>{{ $guard->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <button type="button" class="delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"
                                    onclick="setDeleteAction('{{ route('admin.guards.destroy', $guard->id) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No guard accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $guards->links() }}
        </div>
    </div>
</div>
<div class="modal fade" id="guardModal" tabindex="-1" aria-labelledby="guardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="guardModalLabel" style="font-size: 1.5rem; margin: 0;">Register Guard Account</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.guards.create')
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="text-align: center;">
            <div class="modal-body p-4">
                <div style="margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c;"></i>
                </div>               
                <h2 style="margin-bottom: 10px; color: #333;">Are you sure?</h2>
                <p style="color: #666; margin-bottom: 25px;">Do you really want to delete this guard account? This process cannot be undone.</p>             
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" data-bs-dismiss="modal" style="padding: 10px 25px; background: #bdc3c7; color: #333; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Cancel</button>
                    <form id="deleteGuardForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button  type="submit" style="padding: 10px 25px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function setDeleteAction(actionUrl) {
        document.getElementById('deleteGuardForm').action = actionUrl;
    }
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('guardModal'));
            myModal.show();
        @endif
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create') {
            var myModal = new bootstrap.Modal(document.getElementById('guardModal'));
            myModal.show();
        }
    });
</script>

@endsection