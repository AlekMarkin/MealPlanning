@extends('layout')

@section('content')
<div class="center-wrap">
    <div class="box">
        @if (session('error'))
            <div class="msg">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="ok">{{ session('success') }}</div>
        @endif

        @if (empty($userId))
            <div class="big">A small step begins a long journey</div>
            <div class="small">
                If you're ready to reduce your carbon footprint by eating right, sign up and let's get started.
            </div>

            {{-- Login Form --}}
            <form id="login" method="POST" action="{{ route('login') }}">
                @csrf
                <h3>Login</h3>
                <label>Email</label><br>
                <input type="email" name="email" value="{{ $old_email ?? '' }}" required><br>
                <label>Password</label><br>
                <input type="password" name="password" required><br>
                <button type="submit">Log in</button>
            </form>

            {{-- Registration Form --}}
            <form id="register" method="POST" action="{{ route('register') }}">
                @csrf
                <h3>Sign up</h3>
                <label>Name</label><br>
                <input type="text" name="name" value="{{ $old_name ?? '' }}" required><br>

                <label>Email</label><br>
                <input type="email" name="email" value="{{ $old_email ?? '' }}" required><br>

                <label>Password</label><br>
                <input type="password" name="password" required><br>

                <button type="submit">Create account</button>
            </form>
        @else
            {{-- After login auth block moved to top-right nav --}}
            <div class="big">Welcome, {{ $userName }}!</div>
            <div class="small">
                Here we will later show a simple form for meals, recipes, and calendar planning.<br>
                (Not implemented yet.)
            </div>
        @endif
    </div>
</div>
@endsection
