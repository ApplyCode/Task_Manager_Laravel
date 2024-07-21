<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request) : Response
    {
        return Inertia::render('Tasks/Index', [
            'myTasks'               =>  Task::fromUser('and')
                                    ->whereNull('project_id')
                                    ->with('user', 'comments')
                                    ->get(),
            'myProjectTasks'        =>  Task::fromUser('and')
                                    ->whereNotNull('project_id')
                                    ->with('project', 'user', 'comments')
                                    ->get()
                                    ->groupBy('project_id'),
            'assignedTasks'         =>  Task::assignedToUser('and')
                                    ->whereNull('project_id')
                                    ->with('user', 'comments')
                                    ->get(),
            'assignedProjectTasks'  =>  Task::assignedToUser('and')
                                    ->whereNotNull('project_id')
                                    ->with('project', 'user', 'comments')
                                    ->get()
                                    ->groupBy('project_id'),
            'teamTasks'             =>  Task::forTeam('and')
                                    ->whereNull('project_id')
                                    ->with('user', 'comments')
                                    ->get(),
            'teamProjectTasks'      =>  Task::forTeam('and')
                                    ->whereNotNull('project_id')
                                    ->with('project', 'user', 'comments')
                                    ->get()
                                    ->groupBy('project_id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create() : Response
    {
        // return the tasks create view
        return Inertia::render('Tasks/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTaskRequest $request) : RedirectResponse
    {
        //validate the incoming request using our StoreTaskRequest
        $validated = $request->validated();
        /** @var TaskService $taskService */
        $taskService = resolve(TaskService::class);
        $taskService->store($validated);
        // return a redirect to the last page
        return redirect()->back(303)->with('success', 'Task created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Inertia\Response
     */
    public function show(Task $task) : Response
    {
        // return the tasks show view
        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Inertia\Response
     */
    public function edit(Task $task) : Response
    {
        // return the tasks edit view
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTaskRequest $request, Task $task) : RedirectResponse
    {
        // validate the incoming request using our UpdateTaskRequest
        $validated = $request->validated();
        // update the task with the validated data
        /** @var TaskService $taskService */
        $taskService = resolve(TaskService::class);
        $taskService->update($task, $validated);
        // return a redirect to the last page
        return redirect()->back(303)->with('success', 'Task updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task) : RedirectResponse
    {
        // get the task
        /** @var TaskService $taskService */
        $taskService = resolve(TaskService::class);
        $taskService->destroy($task);
        // return a redirect to the last page
        return redirect()->back(303)->with('success', 'Task deleted successfully.');
    }

    /**
     * Remove bulk resources from storage.
     *  
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyBulk(Request $request) : RedirectResponse
    {
        // get the ids from the request
        $ids = $request->input('ids');
        /** @var TaskService $taskService */
        $taskService = resolve(TaskService::class);
        $taskService->destroyBulk($ids);
        // return a redirect to the last page
        return redirect()->back(303)->with('success', 'Task deleted successfully.');
    }
    
}
