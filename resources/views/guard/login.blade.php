<div class="login-container">
    @if($errors->any())
        @foreach ($errors->all() as $error)
            <div style="color: red;">{{ $error }}</div>
        @endforeach
    @endif

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form class="form" action="{{ route('guard_login_submit') }}" method="post">
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
            <a href="{{ route('guard_forget_password') }}">Forgot password//?</a>
        </span>
        <input class="submit" type="submit" value="Log in" />
    </form>
</div>

<style>
/*Center the login box*/
.login-container {
  min-height: 100vh; 
  display: flex;
  flex-direction: column;
  justify-content: center; 
  align-items: center; 
  background: #f5f5f5;
}

.form {
  --bg-light: #efefef;
  --bg-dark: #707070;
  --clr: #58bc82;
  --clr-alpha: #9c9c9c60;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  width: 100%;
  max-width: 300px;
  padding: 2rem;
  background: #fff; 
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form .input-span {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form input[type="text"],
.form input[type="password"] {
  border-radius: 0.5rem;
  padding: 1rem 0.75rem;
  width: 100%;
  border: none;
  background-color: var(--clr-alpha);
  outline: 2px solid var(--bg-dark);
}

.form input[type="text"]:focus,
.form input[type="password"]:focus {
  outline: 2px solid var(--clr);
}

.label {
  align-self: flex-start;
  color: var(--clr);
  font-weight: 600;
}

.form .submit {
  padding: 1rem 0.75rem;
  width: 100%;
  border-radius: 3rem;
  background-color: var(--bg-dark);
  color: var(--bg-light);
  border: none;
  cursor: pointer;
  transition: all 300ms;
  font-weight: 600;
  font-size: 0.9rem;
}

.form .submit:hover {
  background-color: var(--clr);
  color: var(--bg-dark);
}

.span {
  text-decoration: none;
  color: var(--bg-dark);
  align-self: flex-end;
}

.span a {
  color: var(--clr);
  font-size: 0.85rem;
}
</style>
