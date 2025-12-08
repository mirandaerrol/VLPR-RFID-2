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

    
    <form class="form" action="{{ route('forget_password_submit') }}" method="post">
        @csrf
        <span class="input-span">
            <label for="email" class="label">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required />
        </span>
        <input class="submit" type="submit" value="Send Reset Link" />

        <span class="span">
            <a href="{{ route('login') }}">Back to Login</a>
        </span>
    </form>
</div>


@include('auth.login-style')

