@extends('layouts.app')

@section('content')
<h1>Edit Recipe</h1>

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
    <em>Hint:</em> Ingredients are added on the recipe page after you save.
</p>

<form method="post" action="{{ route('recipes.update', $recipe) }}" style="text-align:left;max-width:600px;margin:0 auto;">
    @csrf @method('PUT')

    <div style="margin-bottom:8px;">
        <label>Name</label><br>
        <input type="text" name="name" value="{{ old('name', $recipe->name) }}" required>
    </div>

    <div style="margin-bottom:8px;">
        <label>Instructions</label><br>
        <textarea name="instructions" rows="8" placeholder="How to make it…">{{ old('instructions', $recipe->instructions) }}</textarea>
    </div>

    <button type="submit">Save</button>
    <a href="{{ route('recipes.show', $recipe) }}" style="margin-left:10px;">Back</a>
</form>
@endsection
