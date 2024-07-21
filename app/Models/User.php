<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // every user has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // every user has many projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    // every user may have many projects
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    // every user may belongs to many roles
    public function roles() {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // global scope to query each user with its squad, unless is for authentication routes
    protected static function boot()
    {
        parent::boot();
        // static::addGlobalScope('squad', function ($builder) {
        //     if (request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.request') || request()->routeIs('password.reset')) {
        //         $builder;
        //     } else {
        //         $builder->where('squad_id', auth()->user()->squad_id);
        //     }
        // });
        self::deleting(function (User $user) {
            foreach ($user->appointments as $appointment) {
                $appointment->delete();
            }
        });



    }

    // local scope to exclude the authenticated user from the query
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeExcludeAuthUser(Builder $query)
    {
        return $query->where('_id', '!=', auth()->id());
    }

    // local scope to query for user by email or name
    /**
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $searchTerm)
    {
        return $query->where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
        });
    }


    /**
     * Get the user appointments.
     */
    public function appointments()
    {
        if ($this->type === 'patient') {
            return $this->hasMany(Appointment::class, 'patient_id');
        }
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Get the user patient info.
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Get the user doctor info.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }



}
