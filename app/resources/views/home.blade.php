@extends('layouts.app')

@section('content')
    @if(($mode ?? 'guest') === 'guest')
        {{-- ---------- GUEST (hero + forms) ---------- --}}
        <div style="text-align: center; max-width: 1200px; margin: 0 auto; padding: 20px;">
            <h1 style="font-size: 42px; margin-bottom: 10px;">A small step begins a long journey</h1>
            <p style="font-size: 16px; color: #555; max-width: 640px; margin: 0 auto 40px;">
                If you're ready to reduce your carbon footprint by eating right, sign up and let's get started.
            </p>

            <div style="display: flex; gap: 30px; justify-content: center; align-items: flex-start; flex-wrap: wrap; margin-bottom: 40px;">
                {{-- Login --}}
                <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 350px;">
                    <h2 style="margin-top: 0; margin-bottom: 25px;">Login</h2>
                    <form method="post" action="{{ route('login') }}">
                        @csrf
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Email</label>
                            <input type="email" name="email" required value="{{ old('email') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Password</label>
                            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <button type="submit" style="width: 100%; background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Sign in</button>
                    </form>
                </div>

                {{-- Register --}}
                <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 350px;">
                    <h2 style="margin-top: 0; margin-bottom: 25px;">Register</h2>
                    <form method="post" action="{{ route('register') }}">
                        @csrf
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Name</label>
                            <input type="text" name="name" required value="{{ old('name') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Email</label>
                            <input type="email" name="email" required value="{{ old('email') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Password</label>
                            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Confirm Password</label>
                            <input type="password" name="password_confirmation" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                        </div>
                        <button type="submit" style="width: 100%; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Create account</button>
                    </form>
                </div>
            </div>

            @if($errors->any())
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px; max-width: 700px; margin-left: auto; margin-right: auto; text-align: left;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px; max-width: 700px; margin-left: auto; margin-right: auto;">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px; max-width: 700px; margin-left: auto; margin-right: auto;">
                    {{ session('success') }}
                </div>
            @endif
        </div>

    @else
        {{-- ---------- AUTH (welcome + snapshot) ---------- --}}
        <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            <h1 style="margin: 0 0 10px 0;">Welcome back, {{ $userName ?? 'friend' }}!</h1>
            <p style="margin: 0 0 30px 0; color: #666;">Use the top navigation to manage your Foods, Goals, and more.</p>

            {{-- Goals + Today's intake --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <!-- Your Goals -->
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0; margin-bottom: 20px;">Your Goals</h2>
                    @php
                        $hasGoals = isset($goals) && is_array($goals) && count($goals);
                    @endphp
                    @if($hasGoals)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                        <th style="padding: 12px; text-align: left;">Metric</th>
                                        <th style="padding: 12px; text-align: left;">Target</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($goals as $key => $g)
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 12px;">{{ $g['label'] ?? ucfirst($key) }}</td>
                                            <td style="padding: 12px;">
                                                {{ rtrim(rtrim(number_format($g['target'] ?? 0, 2, '.', ''), '0'), '.') }}
                                                {{ !empty($g['unit']) ? $g['unit'] : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: #999; text-align: center; padding: 20px 0;">No goals yet. <a href="{{ route('goals.index') }}">Set your first goal</a>.</p>
                    @endif
                </div>

                <!-- Today's Intake -->
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0; margin-bottom: 20px;">Today's Intake (total)</h2>
                    @php
                        $t = $totals ?? [];
                        $fmt = fn($v) => rtrim(rtrim(number_format((float)($v ?? 0), 2, '.', ''), '0'), '.');
                    @endphp
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Calories</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['calories'] ?? 0) }} kcal</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Protein</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['protein'] ?? 0) }} g</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Carbs</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['carbs'] ?? 0) }} g</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Fat</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['fat'] ?? 0) }} g</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Fiber</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['fiber'] ?? 0) }} g</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Sugar</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['sugar'] ?? 0) }} g</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Sodium</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['sodium'] ?? 0) }} mg</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; font-weight: 500;">Carbon footprint</td>
                                    <td style="padding: 12px; text-align: right;">{{ $fmt($t['carbon_footprint'] ?? 0) }} gCO₂e</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 15px;">
                        <a href="/intakes" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">View or add today's intakes</a>
                    </div>
                </div>
            </div>

            {{-- Simple advice per goal --}}
            @php $hasAdvice = isset($advice) && is_array($advice) && count($advice); @endphp
            @if($hasAdvice)
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <h2 style="margin-top: 0; margin-bottom: 20px;">Tips</h2>
                    <ul style="margin: 0; padding: 0; list-style: none; line-height: 1.8;">
                        @foreach($advice as $metric => $text)
                            <li style="margin-bottom: 15px;"><strong>{{ $goals[$metric]['label'] ?? ucfirst($metric) }}:</strong> {{ $text }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    @endif
@endsection