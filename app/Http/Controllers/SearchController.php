<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request): Response
    {
        // get the search term from the request
        $searchTerm = $request->input('search');
        // search for users that match the search term for name or email, and are not the current user
        $users = User::search($searchTerm)->excludeAuthUser()->get();
        // search for projects that match the search term
        $projects = Project::search($searchTerm)->get();
        // search for tasks that match the search term, if they are private they have to be owned by the current user, or the current user has to be assigned to them or a member of the team assigned to them, if they are public everyone can see them
        $tasks = Task::search($searchTerm)
            ->where(function ($query) {
                $query->forUser()->public('or');
            })
            ->with('user', 'project', 'comments')
            ->get()
            ->groupBy('project_id');
        $allUsers = User::get()->keyBy('_id');
        // return the users show view with the user, projects and tasks
        return Inertia::render('Search/Show', [
            'searchedUsers' => $users,
            'searchedProjects' => $projects,
            'searchedTasks' => $tasks,
            'users' => $allUsers,
        ]);
    }
}
