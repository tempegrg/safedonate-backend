<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable = [

    'name',

    'registration_no',

    'website',

    'category',

    'status',

    'logo',

    'description',

    'email',

    'phone',

    'address',

    ];
}