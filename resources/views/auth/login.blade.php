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

    <form class="form" action="{{ route('login.submit') }}" method="post">
        @csrf

        <span class="input-span">
            <label for="name" class="label">Username</label>
            <input type="text" name="name" id="name" placeholder="Enter username" value="{{ old('name') }}" required />
        </span>

        <span class="input-span">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required />
        </span>

        <span class="span">
            <a href="{{ route('forget_password') }}">Forgot password?</a>
        </span>

        <input class="submit" type="submit" value="Log in" />

        <span class="span" style="text-align: center; margin-top: 10px; display: block; width: 100%;">
            Don't have an account? <a href="{{ route('admin.signup') }}">Sign Up</a>
        </span>
    </form>

</div>

@include('auth.login-style')