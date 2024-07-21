<?php

namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        // return the users view with the users
        return Inertia::render('Users/Index', [
            'users' => User::excludeAuthUser()->get(),
        ]);
    }


  
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response
     */
    public function show(User $user): Response
    {
        // all the tasks below have to obey one the following rules in order to be displayed to the current user:
        // 1. the task has to be assigned to the current user
        // 2. the task has to be owned by the current user
        // 3. the task has to be assigned to a team that the current user is a member of
        // get tasks for the selected user that don't have a project_id
        $usersTasks = Task::where('user_id', $user->_id)
            ->whereNull('project_id')
            ->where(function ($query) {
                $query->where('public', true)
                    ->orWhere('assigned_to', auth()->id())
                    ->orWhere('team', 'LIKE', '%' . auth()->id() . '%');
            })
            ->with('user', 'comments')
            ->get();
        // get tasks for the selected user that have a project_id, eager load the project and group them by project
        $usersProjectTasks = Task::where('user_id', $user->_id)
            ->whereNotNull('project_id')
            ->where(function ($query) {
                $query->where('public', true)
                    ->orWhere('assigned_to', auth()->id())
                    ->orWhere('team', 'LIKE', '%' . auth()->id() . '%');
            })
            ->with('project','user', 'comments')
            ->get()
            ->groupBy('project_id');
        // get the assigned tasks for the selected user that don't have a project_id
        $assignedTasks = Task::where('assigned_to', $user->_id)
            ->whereNull('project_id')
            ->where(function ($query){
                $query->where('public', true)
                    ->orWhere('user_id', auth()->id())
                    ->orWhere('team', 'LIKE', '%' . auth()->id() . '%');
            })
            ->with('user', 'comments')
            ->get();
        // get the assigned tasks for the selected user that have a project_id, eager load the project and group them by project
        $assignedProjectTasks = Task::where('assigned_to', $user->_id)
            ->whereNotNull('project_id')
            ->where(function ($query){
                $query->where('public', true)
                    ->orWhere('user_id', auth()->id())
                    ->orWhere('team', 'LIKE', '%' . auth()->id() . '%');
            })
            ->with('project','user', 'comments')
            ->get()
            ->groupBy('project_id');
        // get team tasks for the selected user that don't have aproject_id, team is an array of user_ids, so this user_id must be inside this array
        $teamTasks = Task::where('team', 'LIKE', '%' . $user->_id . '%')
            ->whereNull('project_id')
            ->where(function ($query){
                $query->where('public', true)
                    ->orWhere('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            })
            ->with('user', 'comments')
            ->get();
        // get team tasks for the selected user that have a project_id, eager load the project and group them by project, team is an array of user_ids, so this user_id must be inside this array
        $teamProjectTasks = Task::where('team', 'LIKE', '%' . $user->_id . '%')
            ->whereNotNull('project_id')
            ->where(function ($query){
                $query->where('public', true)
                    ->orWhere('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            })
            ->with('project','user', 'comments')
            ->get()
            ->groupBy('project_id');

        // get user's projects
        $projects = $user->projects()->get();

        // return the users show view with the user
        return Inertia::render('Users/Show', [
            'user' => $user,
            'usersTasks' => $usersTasks,
            'usersProjectTasks' => $usersProjectTasks,
            'assignedTasks' => $assignedTasks,
            'assignedProjectTasks' => $assignedProjectTasks,
            'teamTasks' => $teamTasks,
            'teamProjectTasks' => $teamProjectTasks,
            'projects' => $projects,
        ]);
    }

    /**
     * Create the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response
     */
    public function create(User $user): Response
    {
        // create a new user
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // search if there is no user with the same email, stores a new user based on the request at the same squad_id as the current user
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
        ]);

        $newUser = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(Str::uuid()),
            'starred_projects' => [],
            'starred_tasks' => [],
            'squad_id' => auth()->user()->squad_id,
        ]);
        // fires the registered event
        event(new Registered($newUser));
        // fires the UserEvent
        event(new UserEvent(auth()->user()->squad_id, $newUser, 'create'));
        // return back to the previous page
        return redirect()->route('users')->with('success', 'User invited.');
    }
}
