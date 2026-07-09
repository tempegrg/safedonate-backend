<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Admin\OrganisationController;
use App\Http\Controllers\Api\User\DonationController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\OrganisationApplicationController;

// =========================================
// AUTH
// =========================================
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);

// =========================================
// VERIFIED ORGANISATIONS
// =========================================
Route::get('/organisations', [OrganisationController::class, 'index']);
Route::post('/organisations', [OrganisationController::class, 'store']);
Route::delete('/organisations/{id}', [OrganisationController::class, 'destroy']);

// =========================================
// VERIFY DONATION LINK
// =========================================
Route::post('/verify-link', [DonationController::class, 'verifyLink']);

// =========================================
// VERIFICATION LOGS
// =========================================
Route::get('/logs', [DonationController::class, 'logs']);
Route::delete('/logs/{id}', [DonationController::class, 'deleteLog']);

// =========================================
// REPORTS
// =========================================
Route::get('/reports', [ReportController::class, 'index']);

// =========================================
// ORGANISATION APPLICATIONS
// =========================================

// Submit new application
Route::post(
    '/organisation-applications',
    [OrganisationApplicationController::class, 'submit']
);

// Get all applications (admin)
Route::get(
    '/organisation-applications',
    [OrganisationApplicationController::class, 'index']
);

// Get latest application by current user
Route::get(
    '/organisation-applications/user/{userId}',
    [OrganisationApplicationController::class, 'getUserApplications']
);

// Get single application by ID
Route::get(
    '/organisation-applications/{id}',
    [OrganisationApplicationController::class, 'show']
);

// Approve application
Route::put(
    '/organisation-applications/{id}/approve',
    [OrganisationApplicationController::class, 'approve']
);

// Reject application
Route::put(
    '/organisation-applications/{id}/reject',
    [OrganisationApplicationController::class, 'reject']
);

// Update application
Route::put(
    '/organisation-applications/{id}',
    [OrganisationApplicationController::class, 'update']
);

// Delete application
Route::delete(
    '/organisation-applications/{id}',
    [OrganisationApplicationController::class, 'destroy']
);

// =========================================
// ORGANISATION APPLICATION FILE VIEW ROUTES
// =========================================
Route::get(
    '/organisation-applications/logo/{id}',
    [OrganisationApplicationController::class, 'viewLogo']
);

Route::get(
    '/organisation-applications/certificate/{id}',
    [OrganisationApplicationController::class, 'viewCertificate']
);

Route::get(
    '/organisation-applications/supporting-document/{id}',
    [OrganisationApplicationController::class, 'viewSupportingDocument']
);

// =========================================
// DEBUG DB
// =========================================
Route::get('/debug-db', function () {
    return response()->json([
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
    ]);
});

Route::get('/debug-storage-files', function () {
    return response()->json([
        'logos' => Storage::disk('public')->files('logos'),
        'certificates' => Storage::disk('public')->files('certificates'),
        'supporting_documents' => Storage::disk('public')->files('supporting_documents'),
    ]);
});