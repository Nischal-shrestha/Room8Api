<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'phone', 'display_picture'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'phone', 'pivot', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Model Store rules
     */

    public static $storeRules = array(
        'name' => 'required|max:255',
        'username' => 'required|min:6|max:255|alpha_dash|unique:users,username',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|numeric',
        'display_picture' => 'nullable|url',
        'password' => 'required|min:6|max:255'
    );


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relationships
     */

    public function houses()
    {
        return $this->belongsToMany('App\House');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function toPay()
    {
        return $this->hasMany('App\Payable', 'payer_id', 'id');
    }

    public function toReceive()
    {
        return $this->hasMany('App\Payable', 'receiver_id', 'id');
    }
}
