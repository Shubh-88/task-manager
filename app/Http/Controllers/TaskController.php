<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->tasks();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query
            ->orderByRaw("status = 'completed'")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
          'title' => 'required|min:3',
    'description' => 'nullable|max:500',
    'due_date' => 'nullable|date',
    'status' => 'in:pending,completed'
        ]);

        // auth()->user()->tasks()->create($request->all());

        auth()->user()->tasks()->create([
    'title' => $request->title,
    'description' => $request->description,
    'due_date' => $request->due_date,
    'status' => 'pending'
]);

        return back();
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        // $task->update($request->all());
        $task->update([
    'title' => $request->title,
    'description' => $request->description,
    'due_date' => $request->due_date,
    'status' => $request->status,
]);

        return back();
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        $task->delete();

        return back();
    }
}