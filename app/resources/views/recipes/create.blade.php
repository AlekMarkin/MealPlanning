@extends('layouts.app')

@section('content')
<h1>Create Recipe</h1>

@if ($errors->any())
    <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin-bottom:10px;">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<p style="margin-bottom:10px;color:#555;">
    <em>Hint:</em> You will be able to add ingredients on the next step.
</p>

<form method="post" action="{{ route('recipes.store') }}" style="text-align:left;max-width:600px;margin:0 auto;">
    @csrf

    <div style="margin-bottom:8px;">
        <label>Name</label><br>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>

    <div style="margin-bottom:8px;">
        <label>Instructions</label><br>
        <textarea name="instructions" rows="8" placeholder="How to make it…">{{ old('instructions') }}</textarea>
    </div>

    <button type="submit">Create</button>
    <a href="{{ route('recipes.index') }}" style="margin-left:10px;">Cancel</a>
</form>
@endsection
