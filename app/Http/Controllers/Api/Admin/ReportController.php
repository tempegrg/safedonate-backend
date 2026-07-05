<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

use App\Models\Organisation;
use App\Models\VerificationLog;

class ReportController extends Controller
{
    // =========================================
    // GET DASHBOARD REPORTS
    // =========================================

    public function index()
    {
        return response()->json([

            // Total organisations
            'total_organisations' =>
                Organisation::count(),

            // Total verification logs
            'total_logs' =>
                VerificationLog::count(),

            // Verified websites
            'verified_logs' =>
                VerificationLog::where(
                    'result',
                    'verified'
                )->count(),

            // Warning websites
            'warning_logs' =>
                VerificationLog::where(
                    'result',
                    'warning'
                )->count(),
        ]);
    }
}