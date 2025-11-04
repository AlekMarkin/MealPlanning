@extends('layouts.app')

@section('content')
    @if(($mode ?? 'guest') === 'guest')
        {{-- ---------- GUEST (hero + forms) ---------- --}}
        <div style="text-align:center; max-width:860px; margin:0 auto;">
            <h1 style="font-size:42px; margin-bottom:10px;">A small step begins a long journey</h1>
            <p style="font-size:16px; color:#555; max-width:640px; margin:0 auto 20px;">
                If you're ready to reduce your carbon footprint by eating right, sign up and let's get started.
            </p>

            <div style="display:flex; gap:16px; justify-content:center; align-items:flex-start; flex-wrap:wrap;">
                {{-- Login --}}
                <div style="border:1px solid #ddd; padding:16px; width:320px; text-align:left;">
                    <h3 style="margin-top:0;">Login</h3>
                    <form method="post" action="{{ route('login') }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label>Email</label><br>
                            <input type="email" name="email" required value="{{ old('email') }}" style="width:100%;">
                        </div>
                        <div style="margin-bottom:8px;">
                            <label>Password</label><br>
                            <input type="password" name="password" required style="width:100%;">
                        </div>
                        <button type="submit">Sign in</button>
                    </form>
                </div>

                {{-- Register --}}
                <div style="border:1px solid #ddd; padding:16px; width:320px; text-align:left;">
                    <h3 style="margin-top:0;">Register</h3>
                    <form method="post" action="{{ route('register') }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label>Name</label><br>
                            <input type="text" name="name" required value="{{ old('name') }}" style="width:100%;">
                        </div>
                        <div style="margin-bottom:8px;">
                            <label>Email</label><br>
                            <input type="email" name="email" required value="{{ old('email') }}" style="width:100%;">
                        </div>
                        <div style="margin-bottom:8px;">
                            <label>Password</label><br>
                            <input type="password" name="password" required style="width:100%;">
                        </div>
                        <button type="submit">Create account</button>
                    </form>
                </div>
            </div>

            @if($errors->any())
                <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin:16px auto; max-width:680px; text-align:left;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
        </div>

    @else
        {{-- ---------- AUTH (welcome + snapshot) ---------- --}}
        <div style="max-width:900px; margin:0 auto; text-align:left;">
            <h2 style="margin:0 0 6px 0;">Welcome back, {{ $userName ?? 'friend' }}!</h2>
            <p style="margin-top:0; color:#555;">Use the top navigation to manage your Foods, Goals, and more.</p>

            {{-- Goals + Today’s intake --}}
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div style="border:1px solid #ddd; padding:12px;">
                    <h3 style="margin-top:0;">Your Goals</h3>
                    @php
                        $hasGoals = isset($goals) && is_array($goals) && count($goals);
                    @endphp
                    @if($hasGoals)
                        <table border="1" cellpadding="6" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($goals as $key => $g)
                                    <tr>
                                        <td>{{ $g['label'] ?? ucfirst($key) }}</td>
                                        <td>
                                            {{ rtrim(rtrim(number_format($g['target'] ?? 0, 2, '.', ''), '0'), '.') }}
                                            {{ !empty($g['unit']) ? $g['unit'] : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No goals yet. <a href="{{ route('goals.index') }}">Set your first goal</a>.</p>
                    @endif
                </div>

                <div style="border:1px solid #ddd; padding:12px;">
                    <h3 style="margin-top:0;">Today’s Intake (total)</h3>
                    @php
                        $t = $totals ?? [];
                        $fmt = fn($v) => rtrim(rtrim(number_format((float)($v ?? 0), 2, '.', ''), '0'), '.');
                    @endphp
                    <table border="1" cellpadding="6" cellspacing="0" width="100%">
                        <tbody>
                            <tr><td>Calories</td><td>{{ $fmt($t['calories'] ?? 0) }}</td></tr>
                            <tr><td>Protein</td><td>{{ $fmt($t['protein'] ?? 0) }}</td></tr>
                            <tr><td>Carbs</td><td>{{ $fmt($t['carbs'] ?? 0) }}</td></tr>
                            <tr><td>Fat</td><td>{{ $fmt($t['fat'] ?? 0) }}</td></tr>
                            <tr><td>Fiber</td><td>{{ $fmt($t['fiber'] ?? 0) }}</td></tr>
                            <tr><td>Sugar</td><td>{{ $fmt($t['sugar'] ?? 0) }}</td></tr>
                            <tr><td>Sodium (mg)</td><td>{{ $fmt($t['sodium'] ?? 0) }}</td></tr>
                            <tr><td>Carbon footprint (gCO₂e)</td><td>{{ $fmt($t['carbon_footprint'] ?? 0) }}</td></tr>
                        </tbody>
                    </table>
                    <div style="margin-top:8px;">
                        <a href="/intakes">View or add today’s intakes</a>
                    </div>
                </div>
            </div>

            {{-- Simple advice per goal --}}
            @php $hasAdvice = isset($advice) && is_array($advice) && count($advice); @endphp
            @if($hasAdvice)
                <div style="margin-top:16px; border:1px solid #ddd; padding:12px;">
                    <h3 style="margin-top:0;">Tips</h3>
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($advice as $metric => $text)
                            <li><strong>{{ $goals[$metric]['label'] ?? ucfirst($metric) }}:</strong> {{ $text }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
@endsection
