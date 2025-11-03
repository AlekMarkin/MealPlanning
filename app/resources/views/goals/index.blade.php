@extends('layouts.app')

@section('content')
    <h2>Your Goals</h2>
    <p><a href="/goals/create" class="btn">+ Add Goal</a></p>

    @if(count($goals) === 0)
        <p>No goals yet.</p>
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
    @endif
@endsection
