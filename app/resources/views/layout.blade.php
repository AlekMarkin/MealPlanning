<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meal Planning</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* center main content */
        .center-wrap {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        /* top-right  area */
        .nav {
            position: fixed;
            top: 10px;
            right: 10px;
            text-align: right;
        }
        .nav a, .nav button {
            margin-left: 8px;
        }
        .box {
            max-width: 700px;
            margin: 0 auto;
        }
        .big {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .small {
            font-size: 14px;
            margin-bottom: 20px;
            color: #333;
        }
        form {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .msg { margin: 8px 0; color: #b00; }
        .ok { margin: 8px 0; color: #080; }
        input[type=text], input[type=email], input[type=password] {
            width: 95%;
            padding: 6px;
            margin: 4px 0 8px 0;
        }
    </style>
</head>
<body>

<div class="nav">
    @if(!empty($userId))
        <div>Logged in as <strong>{{ $userName }}</strong></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Log out</button>
        </form>
    @else
        <div>Not logged in</div>
        <div>
            <a href="#login">Login</a>
            <a href="#register">Sign up</a>
        </div>
    @endif
</div>

@yield('content')

</body>
</html>
