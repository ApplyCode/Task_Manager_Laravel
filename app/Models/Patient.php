<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Builder;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];


    public function appointments()
    {
      return $this->hasMany(Appointment::class);
    }
    // every patient belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function getParental()
    // {
    //     return $this->hasMany(Parental::class);
    // }
    //
    //
    // public function getPersonalHistory()
    // {
    //     return $this->hasMany(PersonalHistory::class);
    // }
    //
    // public function getQuirurgicalIntervention()
    // {
    //     return $this->hasMany(QuirurgicalIntervention::class);
    // }
    //
    // public function getHospitalization()
    // {
    //     return $this->hasMany(Hospitalization::class);
    // }
    //
    // public function getGynecoObstetric()
    // {
    //     return $this->hasMany(GynecoObstetric::class);
    // }
    //
    // public function getDiet()
    // {
    //     return $this->hasMany(Diet::class);
    // }
    // public function getAddiction()
    // {
    //     return $this->hasMany(Addiction::class);
    // }
    // public function getSystem()
    // {
    //     return $this->hasMany(System::class);
    // }
    // public function getPhysical()
    // {
    //     return $this->hasMany(Physical::class);
    // }
    // public function getLabtest()
    // {
    //     return $this->hasMany(Labtest::class);
    // }
    // public function getAllergic()
    // {
    //     return $this->hasMany(Allergic::class);
    // }

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
    public function scopeWithAppointments(Builder $query)
    {
        return $query->with('appointments');
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
    public function scopeForUser(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('user_id', auth()->id());
        return $query->orWhere('user_id', auth()->id());
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
                ->orWhere('address', 'LIKE', '%' . $searchTerm . '%');
        });
    }

}
