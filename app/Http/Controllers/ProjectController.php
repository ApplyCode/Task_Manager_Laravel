<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Services\ProjectService;
use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        // get all projects for the current user and eager load the tasks and user
        $myProjects = Project::forUser('and')->withTasks()->withUser()->get();
        // get all projects and eager load the tasks and user
        $projects = Project::withTasks()->withUser()->get();
        // return the projects view with the projects
        return Inertia::render('Projects/Index', [
            'myProjects' => $myProjects,
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        // return the projects create view
        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        //validate the incoming request using our StoreProjectRequest
        $validated = $request->validated();
        /** @var ProjectService $projectService */
        $projectService = resolve(ProjectService::class);
        $projectService->store($validated);
        // return a redirect to the projects index
        return redirect()->route('projects')->with('success', 'Project created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Inertia\Response
     */
    public function show(Project $project): Response
    {
        // load the tasks and user for the project, with this logic: if the task is public, load for everyone, if not, load only for the current user if he is either the owner of the project or the task is assigned to him, or he is one of the team members
        // uses relationship methods from the Project model, and not the Builder class, so we can't use the `where` method, but we can use the `public` method from the Task model, and the `forUser` method from the Task model, and the `or` method from the Builder class, to achieve
        $project->load(['tasks' => function ($query) {
            $query->forUser()->public('or');
        }, 'tasks.user', 'tasks.project', 'tasks.comments']);

        // return the projects show view with the project
        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Inertia\Response
     */
    public function edit(Project $project): Response
    {
        // return the projects edit view with the project
        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        // validate the incoming request using our UpdateProjectRequest
        $validated = $request->validated();
        /** @var ProjectService $projectService */
        $projectService = resolve(ProjectService::class);
        $projectService->update($project, $validated);
        // return a redirect to the projects index
        return redirect()->route('projects')->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project): RedirectResponse
    {
        /** @var ProjectService $projectService */
        $projectService = resolve(ProjectService::class);
        $projectService->destroy($project);
        return redirect()->route('projects')->with('success', 'Project deleted.');
    }
}
