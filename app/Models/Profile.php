<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Profile extends Model
{
    // model structure
    protected $fillable = [
        'username',
        'address',
        'identity',
        'password',
    ];

    public $timestamps = false;

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        return Crypt::decryptString($value);
    }

}
