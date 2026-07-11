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

            // =========================================
            // ORGANISATIONS
            // =========================================
            'total_organisations' => Organisation::count(),

            // =========================================
            // TOTAL VERIFICATION LOGS
            // =========================================
            'total_logs' => VerificationLog::count(),

            // =========================================
            // VERIFIED ORGANISATIONS
            // =========================================
            'verified_logs' => VerificationLog::where(
                'result',
                'verified'
            )->count(),

            // =========================================
            // WARNING WEBSITES
            // =========================================
            'warning_logs' => VerificationLog::where(
                'result',
                'warning'
            )->count(),

            // =========================================
            // DANGEROUS WEBSITES
            // =========================================
            'danger_logs' => VerificationLog::where(
                'result',
                'danger'
            )->count(),

            // =========================================
            // UNKNOWN WEBSITES
            // =========================================
            'unknown_logs' => VerificationLog::where(
                'result',
                'unknown'
            )->count(),
        ]);
    }
}