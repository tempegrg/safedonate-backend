<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}