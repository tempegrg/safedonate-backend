<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationApplication extends Model
{
    protected $fillable = [

        'user_id',

        'organisation_name',
        'organisation_type',
        'registration_number',

        'description',

        'email',
        'phone',
        'address',
        'website',

        'certificate_path',
        'supporting_document_path',
        'logo_path',

        'status',
        'admin_remark',
    ];
}