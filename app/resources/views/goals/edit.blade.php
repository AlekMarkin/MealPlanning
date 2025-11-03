@extends('layouts.app')

@section('content')
    <h2>Edit Goal</h2>

    <form method="post" action="/goals/{{ $goal->id }}/update">
        @csrf

        <label>Name</label>
        <input type="text" name="name" value="{{ $goal->name }}" style="width:100%; padding:6px;">

        <label style="display:block; margin-top:10px;">Direction</label>
        <select name="direction" style="width:100%; padding:6px;">
            <option value="up" {{ $goal->direction==='up' ? 'selected' : '' }}>up</option>
            <option value="down" {{ $goal->direction==='down' ? 'selected' : '' }}>down</option>
        </select>

        <label style="display:block; margin-top:10px;">Target Value (number)</label>
        <input type="number" name="target_value" value="{{ $goal->target_value }}" style="width:100%; padding:6px;">

        <div style="margin-top:12px;">
            <button type="submit" class="btn">Update</button>
            <a href="/goals" class="btn">Cancel</a>
        </div>
    </form>
@endsection
