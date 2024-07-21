<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Services\PatientService;
use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
  // protected $userModel;
  // protected $userService;
  // protected $patientService;

    // public function index()
    // {
    //     $user = auth()->user();
    //     $patients = User::where('type', 'patient')->orderBy('name')->get();
    //
    //     return view('admin.patients', compact('user', 'patients'));
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        // return the patients view with the patients
        $myPatients = Patient::forUser('and')->withAppointments()->withUser()->get();
        // $myPatients = Patient::forUser('and')->get();

        $patients = Patient::withAppointments()->withUser()->get();
        return Inertia::render('Patients/Index', [
            'myPatients' => $myPatients,
            'patients' => $patients,

        ]);
    }

    public function getAvailableByDate(Request $request)
    {
        $serviceResponse = $this->patientService->getAvailableByDate(
            $request->date
        );

        if (!$serviceResponse->success) {
            return response()->json($serviceResponse->errors);
        }

        return response()->json($serviceResponse->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        // return the patients create view
        return Inertia::render('Patients/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePatientRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        //validate the incoming request using our StorePatientRequest
        $validated = $request->validated();
        /** @var PatientService $patientService */
        $patientService = resolve(PatientService::class);
        $patientService->store($validated);
        // return a redirect to the patients index
        return redirect()->route('patients')->with('success', 'Patient created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Inertia\Response
     */
    public function show(Patient $patient): Response
    {
        // load the appointment and user for the patient, with this logic: if the detail is public, load for everyone, if not, load only for the current user if he is either the owner of the patient or the detail is assigned to him, or he is one of the team members
        // uses relationship methods from the Patient model, and not the Builder class, so we can't use the `where` method, but we can use the `public` method from the Appointment model, and the `forUser` method from the Appointment model, and the `or` method from the Builder class, to achieve
        $patient->load(['appointments' => function ($query) {
            $query->forUser()->public('or');
        }, 'appointments.user', 'appointments.patient'
        // , 'appointments.comments'
        ]);

        // return the patients show view with the patient
        return Inertia::render('Patients/Show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Inertia\Response
     */
    public function edit(Patient $patient): Response
    {
        // return the patients edit view with the patient
        return Inertia::render('Patients/Edit', [
            'patient' => $patient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePatientRequest  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        // validate the incoming request using our UpdatePatientRequest
        $validated = $request->validated();
        /** @var PatientService $patientService */
        $patientService = resolve(PatientService::class);
        $patientService->update($patient, $validated);
        // return a redirect to the patients index
        return redirect()->route('patients')->with('success', 'Patient updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        /** @var PatientService $patientService */
        $patientService = resolve(PatientService::class);
        $patientService->destroy($patient);
        return redirect()->route('patients')->with('success', 'Patient deleted.');
    }
}
