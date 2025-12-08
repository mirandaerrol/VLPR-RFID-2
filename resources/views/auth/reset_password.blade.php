<div class="login-container">
    @if($errors->any())
        @foreach ($errors->all() as $error)
            <div style="color: red;">{{ $error }}</div>
        @endforeach
    @endif

    @if (session('success'))
        <div id="success-popup" style="color: green;">{{ session('success') }}</div>
            <script>
                setTimeout(function() {
                    const popup = document.getElementById('success-popup');
                    if(popup) popup.style.display = 'none';
                }, 3000);
            </script>
    @endif

    @if (session('error'))
        <div id="error-popup" style="color: red;">{{ session('error') }}</div>
            <script>
                setTimeout(function() {
                    const popup = document.getElementById('success-popup');
                    if(popup) popup.style.display = 'none';
                }, 3000);
            </script>
    @endif

    
    <form class="form" action="{{ route('reset_password_submit',[$token,$email]) }}" method="post">
        @csrf
        <span class="input-span">
            <label for="password" class="label">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required />
        </span>

        <span class="input-span">
            <label for="confirm_password" class="label">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required />
        </span>

        <input class="submit" type="submit" value="Reset Password" />
    </form>
</div>

@include('auth.login-style')