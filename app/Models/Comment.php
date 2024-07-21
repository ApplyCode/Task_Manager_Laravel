<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];

    // every comment belongs to one user
    function user()
    {
        return $this->belongsTo(User::class);
    }
    // every comment belongs to one task
    function task()
    {
        return $this->belongsTo(Task::class);
    }

    // global scope to query each comment with its squad
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('squad', function (Builder $builder) {
            if (auth()->user())
                $builder->where('squad_id', auth()->user()->squad_id);
        });
    }

    // local scope to query each comment with its user
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithUser(Builder $query)
    {
        return $query->with('user');
    }

    // local scope to query each comment with its task
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithTask(Builder $query)
    {
        return $query->with('task');
    }

    // local scope to query each comment from the authenticated user
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

    // local scope to query each comment from the authenticated user
    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeForTask(Builder $query, string $type)
    {
        if ($type == 'and')
            return $query->where('task_id', auth()->id());
        return $query->orWhere('task_id', auth()->id());
    }
}
