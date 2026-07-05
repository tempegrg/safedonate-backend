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

Route::delete(
    '/organisations/{id}',
    [OrganisationController::class, 'destroy']
);

// =========================================
// VERIFY DONATION LINK
// =========================================

Route::post('/verify-link', [DonationController::class, 'verifyLink']);

// =========================================
// VERIFICATION LOGS
// =========================================

Route::get('/logs', [DonationController::class, 'logs']);

Route::delete(
    '/logs/{id}',
    [DonationController::class, 'deleteLog']
);

// =========================================
// REPORTS
// =========================================

Route::get(
    '/reports',
    [ReportController::class, 'index']
);

// =========================================
// ORGANISATION APPLICATIONS
// =========================================

Route::post(
    '/organisation-applications',
    [OrganisationApplicationController::class, 'submit']
);

Route::get(
    '/organisation-applications',
    [OrganisationApplicationController::class, 'index']
);

Route::get(
    '/organisation-applications/{id}',
    [OrganisationApplicationController::class, 'show']
);

Route::put(
    '/organisation-applications/{id}/approve',
    [OrganisationApplicationController::class, 'approve']
);

Route::put(
    '/organisation-applications/{id}/reject',
    [OrganisationApplicationController::class, 'reject']
);

Route::get('/debug-db', function () {
    return response()->json([
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
    ]);
});