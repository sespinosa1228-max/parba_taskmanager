@extends('layouts.app')

@php($isEditing = $task->exists)

@section('title', ($isEditing ? 'Edit Mission' : 'New Mission').' | Orbital')
@section('breadcrumb', $isEditing ? 'EDIT MISSION' : 'NEW MISSION')

@section('content')
    <div class="form-page">
        <a class="back-link" href="{{ route('tasks.index') }}"><span aria-hidden="true">&larr;</span> Back to mission control</a>

        <header class="form-intro">
            <span class="eyebrow">{{ $isEditing ? 'Flight plan / revise mission' : 'Flight plan / new objective' }}</span>
            <h1>{{ $isEditing ? 'Adjust your course.' : 'Plot a new course.' }}</h1>
            <p>{{ $isEditing ? 'Update the mission details or bring its status up to date.' : 'Give your next objective a name, a few coordinates, and a place in your flight plan.' }}</p>
        </header>

        <div class="form-layout">
            <form class="task-form" action="{{ $isEditing ? route('tasks.update', $task) : route('tasks.store') }}" method="POST">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="field">
                    <label class="form-label" for="title">Mission name <span>REQUIRED</span></label>
                    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $task->title) }}" maxlength="120" placeholder="e.g. Prepare the launch checklist" required autofocus>
                    @error('title') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label class="form-label" for="description">Mission notes <span>OPTIONAL</span></label>
                    <textarea class="form-control" id="description" name="description" maxlength="1000" placeholder="Add the details you'll want at mission control...">{{ old('description', $task->description) }}</textarea>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label class="form-label" for="due_date">Target date <span>OPTIONAL</span></label>
                    <input class="form-control" id="due_date" name="due_date" type="date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                    @error('due_date') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                @if ($isEditing)
                    <div class="field">
                        <label class="form-label" for="status">Mission status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending" @selected(old('status', $task->status) === 'pending')>In orbit / Pending</option>
                            <option value="completed" @selected(old('status', $task->status) === 'completed')>Landed / Completed</option>
                        </select>
                        @error('status') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="form-actions">
                    <button class="button button-primary" type="submit">{{ $isEditing ? 'Save changes' : 'Add to flight plan' }}</button>
                    <a class="button button-secondary" href="{{ route('tasks.index') }}">Cancel</a>
                </div>
            </form>

            <aside class="form-aside">
                <span class="side-label">Flight recorder</span>
                <h2>Keep it mission-ready.</h2>
                <p>Every expedition starts somewhere. This is the first mark on your map, a point to return to as the mission unfolds.</p>
            </aside>
        </div>
    </div>
@endsection