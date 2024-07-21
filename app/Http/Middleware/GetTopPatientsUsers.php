<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
// import the Patient model
use App\Models\Patient;
use Inertia\Inertia;


class GetTopPatientsUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // get the user starred patients array
        $starredPatients = $request->user()->starred_patients;
        if (!$starredPatients) {
            $topPatients = [];
        } else {
            // get those patients from the database
        $topPatients = Patient::whereIn('_id', $starredPatients)
            ->orderBy('updated_at', 'desc')
            ->with('user')
            ->get();
        }
        // get the user starred users array
        $starredUsers = $request->user()->starred_users;
        if (!$starredUsers) {
            $topUsers = [];
        } else {
            // get those users from the database
            $topUsers = User::whereIn('_id', $starredUsers)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        // share the patients with the view in Inertia
        Inertia::share([
            'topPatients' => $topPatients,
            'topUsers' => $topUsers,
        ]);
        return $next($request);
    }
}
