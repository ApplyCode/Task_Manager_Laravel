<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['_id'];

    /**
     * The attributes that can be fillable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'specialty', 'birth_date'
    ];

    protected $casts = [
        'birth_date' => 'datetime'
    ];

}
