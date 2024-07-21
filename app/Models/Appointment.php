<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
// use App\Models\User;
use App\Presenters\AppointmentPresenter;
use Laracasts\Presenter\PresentableTrait;


class Appointment extends Model
{
    use HasFactory, SoftDeletes;
    use PresentableTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];

    protected $presenter = AppointmentPresenter::class;

    protected $fillable = [
        'patient_id', 'doctor_id', 'start_date', 'end_date', 'status', 'color'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // every appointment belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor.
     */
    // public function doctor()
    // {
    //     return $this->belongsTo(User::class, 'doctor_id');
    // }


    // public function tasks()
    // {
    //     return $this->hasMany(Task::class);
    // }

    // global scope to query each project with its squad
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('squad', function (Builder $builder) {
            if (auth()->user())
                $builder->where('squad_id', auth()->user()->squad_id);
        });
    }

    // local scope to query each project with its tasks
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithTasks(Builder $query)
    {
        return $query->with('tasks');
    }

    // local scope to query each project with its user
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithUser(Builder $query)
    {
        return $query->with('user');
    }

    // local scope to query each project from the authenticated user
    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeFromUser(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('user_id', auth()->id());
        return $query->orWhere('user_id', auth()->id());
    }

    // local scope to query each appointment from the authenticated user or assigned to the authenticated user or from the authenticated user's team
    public function scopeForUser(Builder $query)
    {
        return $query->where(function (Builder $query) {
            $query->fromUser('and')
                ->assignedToUser('or')
                ->forTeam('or');
        });
    }

    // local scope to query each appointment assigned to the authenticated user
    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeAssignedToUser(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('assigned_to', auth()->id());
        return $query->orWhere('assigned_to', auth()->id());
    }

    // local scope to query each task from the authenticated user's team
    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeForTeam(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('team', 'LIKE', '%' . auth()->id() . '%');
        return $query->orWhere('team', 'LIKE', '%' . auth()->id() . '%');
    }

    // local scope to query public appointments
    public function scopePublic(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('public', true);
        return $query->orWhere('public', true);
    }

    // local scope to query private appointments
    public function scopePrivate(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('public', false);
        return $query->orWhere('public', false);
    }


    // local scope to search for projects that match the search term
    /**
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $searchTerm)
    {
        return $query->where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
        });
    }




}
