<?php

namespace App\Services;

use App\Events\ProjectEvent;
use App\Models\Project;
use Illuminate\Broadcasting\PendingBroadcast;

class ProjectService
{
    public function store(array $validated): PendingBroadcast
    {
        // create a new project with the validated data
        $project = Project::create($validated);
        $project->load('user');
        // fire the ProjectEvent event with the squad_id and the validated data
        return broadcast(new ProjectEvent($project->squad_id, $project, 'create'));
    }

    public function update(Project $project, array $validated): PendingBroadcast
    {
        // update the project with the validated data
        $project->update($validated);
        $project->load('user');
        // fire the ProjectEvent event with the squad_id and the validated data
        return broadcast(new ProjectEvent($project->squad_id, $project, 'update'));
    }

    public function destroy(Project $project): PendingBroadcast
    {
        $thisProject = $project->load('user');
        $squad_id = $project->squad_id;
        // delete the project
        $project->delete();
        // fire the ProjectEvent event with the squad_id and the project
        return broadcast(new ProjectEvent($squad_id, $thisProject, 'delete'));
    }
}
