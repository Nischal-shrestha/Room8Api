<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{

    protected $fillable = [
        'payer_id', 'receiver_id', 'group_id', 'amount_due', 'is_paid'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'amount_due' => 'float'
    ];

    /**
     * Query Scopes
     */

    public function scopePaid($query)
    {
        return $query->where('is_paid', '1');
    }

    public function scopeNotPaid($query)
    {
        return $query->where('is_paid', '0');
    }


    /**
     * Relationships
     */

    public function payer()
    {
        return $this->belongsTo('App\User', 'payer_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
