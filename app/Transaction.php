<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'group_id', 'house_id', 'description', 'amount', 'location', 'is_calculated'
    ];

    protected $hidden = [
        'pivot', 'created_at', 'updated_at',
        'user_id', 'group_id', 'house_id'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'is_calculated' => 'boolean',
        'amount' => 'float'
    ];


    public static $storeRules = array(
        'user_id' => 'required|numeric|exists:users,id',
        'group_id' => 'required|numeric|exists:groups,id',
        'house_id' => 'required|numeric|exists:houses,id',
        'description' => 'required|max:1000',
        'amount' => 'required|numeric',
        'location' => 'required',
    );

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function group()
    {
        return $this->belongsTo('App\User');
    }

    public function house()
    {
        return $this->belongsTo('App\House');
    }
}
