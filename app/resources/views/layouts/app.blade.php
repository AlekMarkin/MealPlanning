<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meal Planning</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; margin:0; }
        .topbar { padding:10px 15px; background:#f0f0f0; display:flex; justify-content:space-between; align-items:center; }
        .nav a { margin-right:10px; text-decoration:none; }
        .container { max-width: 720px; margin: 30px auto; padding: 10px; text-align:center; }
        form { text-align:left; margin: 0 auto; max-width: 520px; }
        .msg { margin: 10px auto; color: #333; }
        table { width:100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; }
        .actions form { display:inline; }
        .btn { padding:6px 10px; }
        .logout-btn { background:none;border:none;padding:0;margin:0;color:#00f;cursor:pointer;text-decoration:underline; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('goals.index') }}">Goals</a>
            <a href="/recipes">Recipes</a>
            <a href="/user-metrics">User Metrics</a>
            <a href="/meal-plans">Meal Plans</a>
            @if(session('user_id'))
                <a href="{{ route('foods.index') }}">Foods</a>
            @endif
        </div>

        <div>
            @if(session('user_id'))
                <span>Signed in as <strong>{{ session('user_name') }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}" class="btn">Login</a>
                <a href="{{ route('register.form') }}" class="btn">Register</a>
            @endif
        </div>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="msg" style="color:#b00020;">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="msg" style="color:#006400;">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
