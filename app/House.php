<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'phone', 'display_picture',
    ];

    protected $hidden = [
        'pivot', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];


    /**
     * Relationships
     */

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function groups()
    {
        return $this->hasMany('App\Group');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }
}
