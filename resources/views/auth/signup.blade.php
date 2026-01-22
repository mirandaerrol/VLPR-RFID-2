<div class="login-container">
    @if($errors->any())
        @foreach ($errors->all() as $error)
            <div style="color: red; text-align: center;">{{ $error }}</div>
        @endforeach
    @endif

    <form class="form" action="{{ route('admin.signup.submit') }}" method="post">
        @csrf

        <span class="input-span">
            <label for="name" class="label">Username</label>
            <input type="text" name="name" id="name" placeholder="Enter username" value="{{ old('name') }}" required />
        </span>

        <span class="input-span">
            <label for="email" class="label">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter email address" value="{{ old('email') }}" required />
        </span>

        <span class="input-span">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required />
        </span>

        <span class="input-span">
            <label for="password_confirmation" class="label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required />
        </span>

        <input class="submit" type="submit" value="Sign Up" />
        
        <span class="span" style="text-align: center; margin-top: 10px; display: block;">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </span>
    </form>
</div>


@include('auth.login-style')