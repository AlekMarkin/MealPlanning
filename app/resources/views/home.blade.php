@extends('layouts.app')

@php
    // Fallbacks if the controller didn't pass data
    $mode = $mode ?? (session('user_id') ? 'auth' : 'guest');
    $goals = $goals ?? [];
@endphp


@section('content')
    @if($mode === 'guest')
        <div style="text-align:center;">
            <h1 style="font-size:42px; margin-bottom:10px;">A small step begins a long journey</h1>
            <p style="font-size:16px; color:#555; max-width:640px; margin:0 auto 20px;">
                If you're ready to reduce your carbon footprint by eating right,
                sign up and let's get started.
            </p>

            <div style="margin-top:20px;">
                <a class="btn" href="/login">Login</a>
                <a class="btn" href="/register">Register</a>
            </div>
        </div>
    @else
        <div style="text-align:center;">
            <h2 style="margin-bottom:6px;">Welcome back!</h2>
            <p style="color:#555; margin-top:0;">Use the quick links or create a goal right here.</p>

            {{-- Quick Links (everything reachable from Home) --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; margin:18px 0;">
                <a class="btn" href="/goals">Manage Goals</a>
                <a class="btn" href="/recipes">Recipes</a>
                <a class="btn" href="/nutrition-facts">Nutrition Facts</a>
                <a class="btn" href="/user-metrics">User Metrics</a>
                <a class="btn" href="/meal-plans">Meal Plans</a>
            </div>

            {{-- Quick Add Goal (posts to existing Goals controller) --}}
            <div style="max-width:520px; margin:0 auto; text-align:left; border:1px solid #e5e5e5; padding:14px; border-radius:8px;">
                <h3 style="margin-top:0;">Quick add goal</h3>
                <form method="post" action="/goals/store">
                    @csrf
                    <label>Name</label>
                    <input type="text" name="name" style="width:100%; padding:6px;" placeholder="e.g., Eat more veggies">

                    <label style="display:block; margin-top:10px;">Direction</label>
                    <select name="direction" style="width:100%; padding:6px;">
                        <option value="up">up</option>
                        <option value="down">down</option>
                    </select>

                    <label style="display:block; margin-top:10px;">Target Value (number)</label>
                    <input type="number" name="target_value" style="width:100%; padding:6px;" placeholder="e.g., 5">

                    <div style="margin-top:12px;">
                        <button type="submit" class="btn">Save</button>
                    </div>
                </form>
            </div>

            {{-- Latest 5 Goals for this user --}}
            <div style="max-width:720px; margin:20px auto; text-align:left;">
                <h3 style="margin-bottom:6px;">Your latest goals</h3>
                @if(count($goals) === 0)
                    <p style="color:#777;">No goals yet. Add one above!</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Direction</th>
                                <th>Target</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($goals as $g)
                            <tr>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->direction }}</td>
                                <td>{{ $g->target_value }}</td>
                                <td class="actions">
                                    <a class="btn" href="/goals/{{ $g->id }}/edit">Edit</a>
                                    <form method="post" action="/goals/{{ $g->id }}/delete" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn" onclick="return confirm('Delete this goal?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top:8px;">
                        <a href="/goals">View all goals →</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
