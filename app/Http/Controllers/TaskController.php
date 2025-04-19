<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\TaskCategory;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return Inertia::render('Tasks/Index', [
    //         'tasks' => Task::with('media', 'taskCategories')
    //             ->orderBy('created_at', 'desc') // 👈 Sort newest first
    //             ->paginate(50),
    //     ]);
    // }

    public function index(Request $request)
    {
        $query = Task::query()
            ->with(['media', 'taskCategories'])
            ->orderBy('created_at', 'desc')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');
            })
            ->when($request->has('categories'), function ($query) use ($request) {
                $query->whereHas('taskCategories', function ($subQuery) use ($request) {
                    $subQuery->whereIn('id', $request->query('categories'));
                });
            });

        return Inertia::render('Tasks/Index', [
            'tasks' => $query->paginate(50)->withQueryString(),
            'filters' => $request->only(['search']),
            'categories' => \App\Models\TaskCategory::whereHas('tasks')
                ->withCount('tasks')
                ->orderBy('name')
                ->get(),
            'selectedCategories' => $request->query('categories', []),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'categories' => TaskCategory::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->safe(['name', 'due_date']) + ['is_completed' => false]);

        if ($request->hasFile('media')) {
            $task->addMedia($request->file('media'))->toMediaCollection();
        }

        if ($request->has('categories')) {
            $task->taskCategories()->sync($request->validated('categories'));
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $task->load(['media', 'taskCategories']);
        $task->append(['mediaFile']);

        return Inertia::render('Tasks/Edit', [
            'categories' => TaskCategory::all(),
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        if ($request->hasFile('media')) {
            $task->getFirstMedia()?->delete();
            $task->addMedia($request->file('media'))->toMediaCollection();
        }
        $task->taskCategories()->sync($request->validated('categories', []));
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
