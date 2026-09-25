<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('status', 'all');

        if (! in_array($filter, ['all', 'pending', 'completed'], true)) {
            $filter = 'all';
        }

        $tasks = Task::query()
            ->when($filter !== 'all', fn (Builder $query): Builder => $query->where('status', $filter))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $taskCount = Task::count();
        $pendingCount = Task::where('status', 'pending')->count();
        $completedCount = Task::where('status', 'completed')->count();
        $completionRate = $taskCount === 0 ? 0 : (int) round($completedCount / $taskCount * 100);

        return view('tasks.index', compact(
            'tasks',
            'filter',
            'taskCount',
            'pendingCount',
            'completedCount',
            'completionRate',
        ));
    }

    public function create(): View
    {
        return view('tasks.form', ['task' => new Task]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ]);

        Task::create([...$validated, 'status' => 'pending']);

        return Redirect::route('tasks.index')->with('success', 'Mission added to your launch queue.');
    }

    public function edit(Task $task): View
    {
        return view('tasks.form', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'completed'])],
        ]);

        $task->update($validated);

        return Redirect::route('tasks.index')->with('success', 'Mission details updated.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'completed'])],
        ]);

        $task->update($validated);

        return Redirect::route('tasks.index')->with(
            'success',
            $task->status === 'completed' ? 'Mission marked complete.' : 'Mission returned to the launch queue.',
        );
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return Redirect::route('tasks.index')->with('success', 'Mission removed from your flight plan.');
    }
}
