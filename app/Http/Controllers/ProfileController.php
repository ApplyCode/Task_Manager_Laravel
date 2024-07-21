<?php

namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // broadcast the user event with two arguments: the channel that is the squad_id and the user
        broadcast(new UserEvent($request->user()->squad_id, $request->user(), 'update'));

        return redirect()->back(303)->with('success', 'Profile updated.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();
        Task::fromUser('and')->delete();
        Project::fromUser('and')->delete();
        Comment::fromUser('and')->delete();

        Auth::logout();

        $user->delete();

        broadcast(new UserEvent($user->squad_id, $user, 'delete'));

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the starred projects array for the current user at the database.
     */
    public function updateStarredProjects(Request $request)
    {
        $request->validate([
            'starredProjects' => ['array'],
            'starredProjects.*' => ['exists:projects,_id'],
        ]);

        $request->user()->update([
            'starred_projects' => $request->starredProjects,
        ]);

        $user = $request->user();

        $user->starred_projects = $request->starredProjects;

        $user->save();
        

        return Redirect::route('projects', [], 303);
    }

    /**
     * Update the starred users array for the current user at the database.
     */

    public function updateStarredUsers(Request $request)
    {
        $request->validate([
            'starredUsers' => ['array'],
            'starredUsers.*' => ['exists:users,_id'],
        ]);

        $request->user()->update([
            'starred_users' => $request->starredUsers,
        ]);

        $user = $request->user();

        $user->starred_users = $request->starredUsers;

        $user->save();

        return Redirect::route('users', [], 303);
    }

}
