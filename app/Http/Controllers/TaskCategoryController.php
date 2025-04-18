<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCategoryRequest;
use App\Http\Requests\UpdateTaskCategoryRequest;
use App\Models\TaskCategory;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TaskCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('TaskCategories/Index', [
            'taskCategories' => TaskCategory::query()
                ->withCount('tasks')
                ->paginate(50),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('TaskCategories/Create');
    }

    public function store(StoreTaskCategoryRequest $request): RedirectResponse
    {
        TaskCategory::create($request->validated());

        return redirect()->route('task-categories.index')->with('success', 'Task category created successfully.');
    }

    public function edit(TaskCategory $taskCategory): Response
    {
        return Inertia::render('TaskCategories/Edit', [
            'taskCategory' => $taskCategory,
        ]);
    }

    public function update(UpdateTaskCategoryRequest $request, TaskCategory $taskCategory): RedirectResponse
    {
        $taskCategory->update($request->validated());

        return redirect()->route('task-categories.index')->with('success', 'Task category updated successfully.');
    }

    public function destroy(TaskCategory $taskCategory): RedirectResponse
    {
        if ($taskCategory->tasks()->count() > 0) {
            $taskCategory->tasks()->detach();
        }

        $taskCategory->delete();

        return redirect()->route('task-categories.index')->with('success', 'Task category deleted successfully.');
    }
}
