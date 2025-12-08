@extends('layouts.dashboard') 
@include('style')

@section('content')
<div class="dashboard-container">   
    <div class="card" style="max-width: 600px;">   
        <h1>Create RFID</h1>
        <form action="{{ route('admin.rfids.store') }}" method="POST" >
            @csrf
            <div class="input-span">
                <label for="rfid_code" class="label">RFID Code:</label>
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div style="color: red;">{{ $error }}</div>
                    @endforeach
                @endif
                <input type="text" name="rfid_code" id="rfid_code" required><br>
                <div style="display: flex; flex-direction: column; gap: 15px; text-align: center; padding-top: 15px;">
                    <button type="submit" class="submit">Create</button>
                    <a href="{{ route('admin.rfids.index') }}" class="back-btn">Cancel</a>                   
                </div>                               
            </div>
            
        </form>
     </div>
</div>
@endsection

