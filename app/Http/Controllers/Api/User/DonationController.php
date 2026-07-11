<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Organisation;
use App\Models\VerificationLog;

use Illuminate\Support\Facades\Http;

class DonationController extends Controller
{
    // =========================================
    // VERIFY DONATION LINK
    // =========================================
    public function verifyLink(Request $request)
    {
        // =========================================
        // VALIDATE INPUT
        // =========================================
        $request->validate([
            'website' => 'required|string'
        ]);

        // =========================================
        // CLEAN WEBSITE INPUT
        // =========================================
        $website = strtolower(trim($request->website));

        $website = str_replace(
            ['https://', 'http://', 'www.'],
            '',
            $website
        );

        $website = rtrim($website, '/');

        $fullUrl = 'https://' . $website;

        // =========================================
        // CHECK TRUSTED ORGANISATION DATABASE
        // =========================================
        $organisation = Organisation::all()->first(function ($org) use ($website) {
            $orgWebsite = strtolower(trim($org->website));

            $orgWebsite = str_replace(
                ['https://', 'http://', 'www.'],
                '',
                $orgWebsite
            );

            $orgWebsite = rtrim($orgWebsite, '/');

            return $orgWebsite === $website;
        });

        // =========================================
        // DEFAULT SECURITY STATUS
        // =========================================
        $securityStatus = 'unknown';

        // =========================================
        // VIRUSTOTAL URL SCAN
        // =========================================
        $apiKey = env('VIRUSTOTAL_API_KEY');

        try {
            // Submit URL first so VirusTotal can analyze/update it
            Http::withoutVerifying()->withHeaders([
                'x-apikey' => $apiKey,
            ])->asForm()->post(
                'https://www.virustotal.com/api/v3/urls',
                [
                    'url' => $fullUrl
                ]
            );

            // Build VirusTotal URL ID
            $urlId = rtrim(strtr(base64_encode($fullUrl), '+/', '-_'), '=');

            // Get URL report directly from VirusTotal
            $urlResponse = Http::withoutVerifying()->withHeaders([
                'x-apikey' => $apiKey,
            ])->get("https://www.virustotal.com/api/v3/urls/{$urlId}");

            if ($urlResponse->successful()) {
                $urlData = $urlResponse->json();

                $stats = $urlData['data']['attributes']['last_analysis_stats'] ?? null;

                if ($stats) {
                    $malicious = $stats['malicious'] ?? 0;
                    $suspicious = $stats['suspicious'] ?? 0;

                    // =========================================
                    // DETECT THREATS USING VIRUSTOTAL RESULTS
                    // =========================================

                    // Dangerous:
                    // Three or more security vendors classify the website as malicious.
                    if ($malicious >= 3) {

                        $securityStatus = 'malicious';

                    // Warning:
                    // One or two malicious detections OR
                    // at least one suspicious detection.
                    } elseif (
                        ($malicious >= 1 && $malicious <= 2) ||
                        ($suspicious >= 1)
                    ) {

                        $securityStatus = 'warning';

                    // Safe:
                    // No malicious or suspicious detections.
                    } else {

                        $securityStatus = 'trusted';
                    }
                } else {
                    $securityStatus = 'unknown';
                }
            } else {
                $securityStatus = 'unknown';
            }

        } catch (\Exception $e) {
            $securityStatus = 'unknown';
        }

        // =========================================
        // DETERMINE RESULT FOR REPORT
        // =========================================
        $logResult = 'unknown';

        if ($organisation) {

            // Website belongs to a trusted organisation
            $logResult = 'verified';

        } else {

            switch ($securityStatus) {

                case 'trusted':
                    // Safe according to VirusTotal, but not a trusted organisation
                    $logResult = 'unknown';
                    break;

                case 'warning':
                    $logResult = 'warning';
                    break;

                case 'malicious':
                    $logResult = 'danger';
                    break;

                default:
                    $logResult = 'unknown';
                    break;
            }
        }

        // =========================================
        // SAVE VERIFICATION LOG
        // =========================================
        VerificationLog::create([
            'website' => $website,
            'result' => $logResult,
        ]);

        // =========================================
        // FINAL RESPONSE
        // =========================================

        // =========================================
        // TRUSTED ORGANISATION FOUND
        // =========================================
        if ($organisation) {

            if ($securityStatus == 'trusted') {
                return response()->json([
                    'status' => 'verified',
                    'security_status' => 'safe',
                    'message' => 'Trusted organisation and website is safe.',
                    'organisation' => $organisation->name
                ]);
            }

            if ($securityStatus == 'warning') {
                return response()->json([
                    'status' => 'verified',
                    'security_status' => 'warning',
                    'message' => 'Trusted organisation found, but some security warnings were detected.',
                    'organisation' => $organisation->name
                ]);
            }

            if ($securityStatus == 'malicious') {
                return response()->json([
                    'status' => 'verified',
                    'security_status' => 'danger',
                    'message' => 'Trusted organisation found, but security risks were detected on the website.',
                    'organisation' => $organisation->name
                ]);
            }

            // VirusTotal unavailable / unknown
            return response()->json([
                'status' => 'verified',
                'security_status' => 'unknown',
                'message' => 'Trusted organisation found. However, VirusTotal does not currently have sufficient reputation information for this website.',
                'organisation' => $organisation->name
            ]);
        }

        // =========================================
        // WEBSITE NOT FOUND IN SAFEDONATE DATABASE
        // =========================================

        if ($securityStatus == 'trusted') {
            return response()->json([
                'status' => 'unknown',
                'security_status' => 'safe',
                'message' => 'Website is not registered in trusted database, but no major security threats were detected',
            ]);
        }

        if ($securityStatus == 'warning') {
            return response()->json([
                'status' => 'unknown',
                'security_status' => 'warning',
                'message' => 'Website is not registered in trusted database and some security warnings were detected',
            ]);
        }

        if ($securityStatus == 'malicious') {
            return response()->json([
                'status' => 'danger',
                'security_status' => 'danger',
                'message' => 'Potentially dangerous website detected',
            ]);
        }

        // VirusTotal unavailable / unknown
        return response()->json([
            'status' => 'unknown',
            'security_status' => 'unknown',
            'message' => 'Website is not registered as a trusted organisation. VirusTotal does not currently have sufficient reputation information for this website. Proceed with caution before making any donation.',
        ]);
    }

    // =========================================
    // GET VERIFICATION LOGS
    // =========================================
    public function logs()
    {
        $logs = VerificationLog::latest()->get();

        return response()->json([
            'logs' => $logs
        ]);
    }

    // =========================================
    // DELETE VERIFICATION LOG
    // =========================================
    public function deleteLog($id)
    {
        $log = VerificationLog::find($id);

        if (!$log) {
            return response()->json([
                'message' => 'Log not found'
            ], 404);
        }

        $log->delete();

        return response()->json([
            'message' => 'Log deleted successfully'
        ]);
    }
}