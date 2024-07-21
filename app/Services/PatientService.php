<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Patient;
use App\Events\PatientEvent;
// use App\Services\Responses\ServiceResponse;
use Illuminate\Broadcasting\PendingBroadcast;

class PatientService
{
    // protected $userModel;

    // public function __construct(User $userModel)
    // {
    //     $this->userModel = $userModel;
    // }


    public function getAvailableByDate(string $date): ServiceResponse
    {
        try {
            $date = Carbon::parse($date)->toDateTimeString();

            $patients = $this->userModel
                ->where('users.type', 'patient')
                ->whereNotIn('users.id', function ($query) use ($date) {
                    $query->from('appointments')
                        ->select('appointments.patient_id')
                        ->where('appointments.start_date', $date)
                        ->where('status', '!=', 'cancelled');
                })
                ->leftJoin('patients', 'users.id', 'patients.user_id')
                ->get(['users.id', 'users.name', 'users.image', 'patients.blood_type']);
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Error al buscar pacientes.',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Busqueda exitosa.',
            $patients
        );
    }

    public function store(array $validated): PendingBroadcast
    {
        // create a new patient with the validated data
        $patient = Patient::create($validated);
        $patient->load('user');
        // fire the PatientEvent event with the squad_id and the validated data
        return broadcast(new PatientEvent($patient->squad_id, $patient, 'create'));
    }


    public function update(Patient $patient, array $validated): PendingBroadcast
    {
        // update the patient with the validated data
        $patient->update($validated);
        $patient->load('user');
        // fire the PatientEvent event with the squad_id and the validated data
        return broadcast(new PatientEvent($patient->squad_id, $patient, 'update'));
    }

    public function destroy(Patient $patient): PendingBroadcast
    {
        $thisPatient = $patient->load('user');
        $squad_id = $patient->squad_id;
        // delete the patient
        $patient->delete();
        // fire the PatientEvent event with the squad_id and the patient
        return broadcast(new PatientEvent($squad_id, $thisPatient, 'delete'));
    }
}
