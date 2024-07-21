<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];

    // every project has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // every project has one user that created it
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
                ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
        });
    }
}
