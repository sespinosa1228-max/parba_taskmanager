@extends('layouts.app')

@section('title', 'Mission Control | Orbital')
@section('breadcrumb', 'MISSION CONTROL')

@section('content')
    <section class="hero" aria-labelledby="dashboard-title">
        <img class="hero-image" src="{{ asset('images/earth-orbit.jpg') }}" alt="Earth viewed from orbit">
        <div class="hero-shade" aria-hidden="true"></div>
        <div class="hero-copy">
            <span class="eyebrow">Field log / personal flight plan</span>
            <h1 id="dashboard-title">Your day,<br><span>in orbit.</span></h1>
            <p>One small step at a time. Keep your next mission in sight and your flight plan moving forward.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="{{ route('tasks.create') }}"><span aria-hidden="true">+</span> New mission</a>
                <span class="hero-coordinate">SECTOR 07 / EARTH ORBIT</span>
            </div>
        </div>
    </section>

    <section class="metric-strip" aria-label="Mission totals">
        <div class="metric">
            <div class="metric-copy"><span class="stat-label">Active missions</span><strong class="stat-value">{{ $pendingCount }}</strong></div>
            <span class="metric-mark" aria-hidden="true">P</span>
        </div>
        <div class="metric">
            <div class="metric-copy"><span class="stat-label">Missions landed</span><strong class="stat-value">{{ $completedCount }}</strong></div>
            <span class="metric-mark mint" aria-hidden="true">C</span>
        </div>
        <div class="metric">
            <div class="metric-copy"><span class="stat-label">Flight plan progress</span><strong class="stat-value">{{ $completionRate }}%</strong></div>
            <span class="metric-mark" aria-hidden="true">%</span>
        </div>
    </section>

    <div class="main-grid">
        <section class="missions" aria-labelledby="missions-heading">
            <div class="section-heading">
                <div>
                    <h2 id="missions-heading">Mission queue</h2>
                    <p>{{ $taskCount }} {{ \Illuminate\Support\Str::plural('mission', $taskCount) }} on your flight plan</p>
                </div>
                <nav class="filter-tabs" aria-label="Filter missions">
                    <a class="filter-tab {{ $filter === 'all' ? 'is-active' : '' }}" href="{{ route('tasks.index') }}">All</a>
                    <a class="filter-tab {{ $filter === 'pending' ? 'is-active' : '' }}" href="{{ route('tasks.index', ['status' => 'pending']) }}">In orbit</a>
                    <a class="filter-tab {{ $filter === 'completed' ? 'is-active' : '' }}" href="{{ route('tasks.index', ['status' => 'completed']) }}">Landed</a>
                </nav>
            </div>

            <div class="task-list">
                @forelse ($tasks as $task)
                    <article class="task-row {{ $task->status === 'completed' ? 'is-completed' : '' }}">
                        <form class="inline-form" action="{{ route('tasks.status', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                            <button class="completion-button" type="submit" aria-label="{{ $task->status === 'completed' ? 'Return mission to active' : 'Mark mission complete' }}">
                                <span class="completion-glyph" aria-hidden="true">&#10003;</span>
                            </button>
                        </form>

                        <div class="task-copy">
                            <div class="task-title-line">
                                <h3 class="task-title">{{ $task->title }}</h3>
                                <span class="status-tag">{{ $task->status === 'completed' ? 'Landed' : 'In orbit' }}</span>
                            </div>
                            @if ($task->description)
                                <p class="task-description">{{ $task->description }}</p>
                            @endif
                            @if ($task->due_date)
                                <span class="task-due">Due {{ $task->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>

                        <div class="task-actions">
                            <a class="text-action" href="{{ route('tasks.edit', $task) }}">Edit</a>
                            <form class="inline-form" action="{{ route('tasks.destroy', $task) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-action danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-orbit" aria-hidden="true">O</span>
                        @if ($taskCount === 0)
                            <h3>Your flight plan is clear.</h3>
                            <p>Every great expedition starts with one mission. Add the first task to your queue.</p>
                            <a class="button button-primary" href="{{ route('tasks.create') }}"><span aria-hidden="true">+</span> Add a mission</a>
                        @else
                            <h3>No missions in this sector.</h3>
                            <p>Try another filter or add a new mission to your flight plan.</p>
                            <a class="button button-secondary" href="{{ route('tasks.index') }}">View all missions</a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($tasks->hasPages())
                <nav class="pagination" aria-label="Mission pages">
                    <span>Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() }}</span>
                    <span>
                        @if ($tasks->previousPageUrl())
                            <a href="{{ $tasks->previousPageUrl() }}">Previous</a>
                        @endif
                        @if ($tasks->nextPageUrl())
                            <a href="{{ $tasks->nextPageUrl() }}">Next</a>
                        @endif
                    </span>
                </nav>
            @endif
        </section>

        <aside aria-label="Flight plan status">
            <section class="flight-rail">
                <h2 class="rail-heading">Flight stats</h2>
                <p class="rail-subtitle">Telemetry / current orbit.</p>
                <div class="progress-wrap">
                    <div class="progress-ring" style="--progress: {{ $completionRate }}%">
                        <div class="progress-inner">
                            <strong class="progress-value">{{ $completionRate }}%</strong>
                            <span class="progress-caption">complete</span>
                        </div>
                    </div>
                </div>
                <div class="rail-data">
                    <div class="rail-data-row"><span>In the queue</span><strong>{{ $pendingCount }}</strong></div>
                    <div class="rail-data-row"><span>Landed safely</span><strong>{{ $completedCount }}</strong></div>
                    <div class="rail-data-row"><span>Total missions</span><strong>{{ $taskCount }}</strong></div>
                </div>
            </section>
            <div class="callout"><p><strong>Mission note</strong><br>Small steps still move you across the stars. Pick one task and bring it home.</p></div>
        </aside>
    </div>
@endsection