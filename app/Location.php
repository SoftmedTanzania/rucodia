<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Location extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'latitude', 'longitude', 'name', 'created_by', 'updated_by', 'deleted_by',
    ];


    /**
     * Get the users for the level.
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps()->withPivot('uuid');
    }
}
