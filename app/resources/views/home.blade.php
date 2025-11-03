@extends('layouts.app')

@section('content')
    @if(!session('user_id'))
        <div style="text-align:center;">
            <h1 style="font-size:42px; margin-bottom:10px;">A small step begins a long journey</h1>
            <p style="font-size:16px; color:#555; max-width:640px; margin:0 auto 20px;">
                If you're ready to reduce your carbon footprint by eating right,
                sign up and let's get started.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:1fr; gap:24px; max-width:720px; margin:20px auto;">
            {{-- Login --}}
            <div style="border:1px solid #ddd; padding:16px; border-radius:8px;">
                <h3 style="margin-top:0;">Login</h3>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label>Email</label><br>
                        <input type="email" name="email" value="{{ old('email') }}" required style="width:100%; padding:8px;">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label>Password</label><br>
                        <input type="password" name="password" required style="width:100%; padding:8px;">
                    </div>
                    <button type="submit" class="btn">Sign in</button>
                </form>
            </div>

            {{-- Register --}}
            <div style="border:1px solid #ddd; padding:16px; border-radius:8px;">
                <h3 style="margin-top:0;">Register</h3>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label>Name</label><br>
                        <input type="text" name="name" value="{{ old('name') }}" required style="width:100%; padding:8px;">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label>Email</label><br>
                        <input type="email" name="email" value="{{ old('email') }}" required style="width:100%; padding:8px;">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label>Password</label><br>
                        <input type="password" name="password" required style="width:100%; padding:8px;">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label>Confirm Password</label><br>
                        <input type="password" name="password_confirmation" required style="width:100%; padding:8px;">
                    </div>
                    <button type="submit" class="btn">Create account</button>
                </form>
            </div>
        </div>
    @else
        <h2>Welcome back, {{ session('user_name') }}!</h2>
        <p>Use the top navigation to manage your Foods, Goals, and more.</p>
    @endif
@endsection
