<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
// import the Patient model
use App\Models\Patient;
use App\Models\Task;
use Inertia\Inertia;


class GetAllPatientsUsersCategoriesLabels
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
        Inertia::share([
            'allPatients'   =>  Patient::orderBy('name', 'asc')
                                ->with('user')
                                ->withTrashed()
                                ->get(),
            'allUsers'      =>  User::where('squad_id', auth()->user()->squad_id)
                                ->orderBy('name', 'asc')
                                ->withTrashed()
                                ->get(),
            'allCategories' =>  Task::get()
                                ->pluck('category')
                                ->flatten()
                                ->unique()
                                ->reject(function ($value, $key) {
                                    return $value === null;
                                })->values(),
            'allLabels'     =>  Task::get()
                                ->pluck('labels')
                                ->flatten()
                                ->unique()
                                ->reject(function ($value, $key) {
                                    return $value === null;
                                })->values(),
        ]);
        return $next($request);
    }
}
