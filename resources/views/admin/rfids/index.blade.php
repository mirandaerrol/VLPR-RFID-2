@extends('layouts.dashboard') 
@include('style')

@section('content')
    <div class="dashboard-container">    
        <div class="card">           
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1>RFID List</h1>
                @if (session('success'))
                    <div id="success-popup" style="color: green;">{{ session('success') }}</div>
                        <script>
                            setTimeout(function() {
                                const popup = document.getElementById('success-popup');
                                if(popup) popup.style.display = 'none';
                            }, 3000);
                        </script>
                @endif
                <a href="{{ route('admin.rfids.create') }}" class="submit">Add New RFID</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>RFID Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rfids as $rfid)
                    <tr>
                        <td>{{ $rfid->rfid_id }}</td>
                        <td>{{ $rfid->rfid_code }}</td>
                        <td>
                            <!--<a href="{{ route('admin.rfids.edit', $rfid->rfid_id) }}" class="submit">Edit</a>-->
                            <form action="{{ route('admin.rfids.destroy', $rfid->rfid_id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="delete" onclick="return confirm('Are you sure you want to delete this RFID?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
