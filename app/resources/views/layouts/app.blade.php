<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meal Planning</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- global styles --}}
    <style>
        body { font-family: Arial, sans-serif; margin:0; }
        .topbar { padding:10px 15px; background:#f0f0f0; display:flex; justify-content:space-between; align-items:center; }
        .nav a { margin-right:12px; text-decoration:none; color:#222; }
        .nav a:last-child { margin-right:0; }
        .container { max-width: 960px; margin: 30px auto; padding: 10px; text-align:center; }
        form { text-align:left; margin: 0 auto; max-width: 520px; }
        table { width:100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; }
        .actions form { display:inline; }
        .btn { padding:6px 10px; }
    </style>
</head>
<body>
    {{-- top navigation bar --}}
    <div class="topbar">
        {{-- main navigation links --}}
        <div class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('foods.index') }}">Foods</a>
            <a href="{{ route('recipes.index') }}">Recipes</a>

            {{-- authenticated user navigation --}}
            @if(session('user_id'))
                <a href="{{ route('intakes.index') }}">Daily Intake</a>
                <a href="{{ route('goals.index') }}">Goals</a>
                <a href="{{ route('user-metrics.index') }}">User Metrics</a>
                <a href="{{ route('meal-plans.index') }}">Meal Plans</a>
            @endif
        </div>

        {{-- user authentication status and actions --}}
        <div class="nav">
            @if(session('user_id'))
                <span>Signed in as <strong>{{ session('user_name') }}</strong></span>
                <form method="post" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}">Login</a>
                <a href="{{ route('register.form') }}">Register</a>
            @endif
        </div>
    </div>

    {{-- main content container --}}
    <div class="container">
        @yield('content')
    </div>
</body>
</html>