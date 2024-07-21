<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use App\Services\AppointmentService;


class DashboardController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $tasks = Task::forUser()
            ->withUser()
            ->withComments()
            ->get();
        return Inertia::render('Dashboard/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function config()
    {
        $user = auth()->user();

        return view('config', compact('user'));
    }

    public function indextwo()
    {
        $user = auth()->user();

        if ($user->type === 'admin') {
            $appointments = $this->appointmentService->getPendingAppointments();
            $confirmed = $this->appointmentService->getConfirmedAppointments(null, true);
            $ended = $this->appointmentService->getEndedAppointments(null, null, true);

            return view('admin.dashboard', compact('user', 'appointments', 'confirmed', 'ended'));
        }

        $nextDate = $this->appointmentService->getNextAppointmentDate();
        $confirmed = $this->appointmentService->getConfirmedAppointments($user->id, true);
        $ended = $this->appointmentService->getEndedAppointments($user->id, null, true);
        $appointments = $this->appointmentService->getNextAppointments(4);
        $endedAppointments = $this->appointmentService->getEndedAppointments($user->id, 4);

        return view('dashboard', compact(
            'user',
            'nextDate',
            'confirmed',
            'ended',
            'appointments',
            'endedAppointments'
        ));
    }


}
