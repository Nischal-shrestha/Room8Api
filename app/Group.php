<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'house_id'
    ];

    protected $hidden = [
        'pivot', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Store Validation Rules
     */

    public static $storeRules = array(
        'name' => 'required|max:255',
        'house_id' => 'required|numeric',
        'description' => 'required|max:1000'
    );

    /**
     * Relationships
     */

    public function house()
    {
        return $this->belongsTo('App\House');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function payables()
    {
        return $this->hasMany('App\Payable');
    }


}
