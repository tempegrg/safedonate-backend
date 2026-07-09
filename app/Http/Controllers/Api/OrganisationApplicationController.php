<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\OrganisationApplication;
use App\Models\Organisation;

class OrganisationApplicationController extends Controller
{
    // =========================================
    // SUBMIT APPLICATION
    // =========================================
    public function submit(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'organisation_name' => 'required|string|max:255',
            'organisation_type' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'website' => 'required|string',
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $logoPath = $request->file('logo')->store('logos', 'public');
        $certificatePath = $request->file('certificate')->store('certificates', 'public');

        $supportingDocumentPath = null;
        if ($request->hasFile('supporting_document')) {
            $supportingDocumentPath = $request->file('supporting_document')
                ->store('supporting_documents', 'public');
        }

        $application = OrganisationApplication::create([
            'user_id' => $request->user_id,
            'organisation_name' => $request->organisation_name,
            'organisation_type' => $request->organisation_type,
            'registration_number' => $request->registration_number,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
            'logo_path' => $logoPath,
            'certificate_path' => $certificatePath,
            'supporting_document_path' => $supportingDocumentPath,
            'status' => 'pending',
            'admin_remark' => null,
        ]);

        $application->load('user');

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    // =========================================
    // GET ALL APPLICATIONS
    // =========================================
    public function index()
    {
        $applications = OrganisationApplication::with('user')
            ->latest()
            ->get()
            ->map(function ($application) {
                $application->logo_url = $application->logo_path
                    ? asset('storage/' . $application->logo_path)
                    : null;

                $application->certificate_url = $application->certificate_path
                    ? asset('storage/' . $application->certificate_path)
                    : null;

                $application->supporting_document_url = $application->supporting_document_path
                    ? asset('storage/' . $application->supporting_document_path)
                    : null;

                $application->submitted_by_name = $application->user?->name;
                $application->submitted_by_email = $application->user?->email;

                return $application;
            });

        return response()->json([
            'applications' => $applications
        ]);
    }

    // =========================================
    // GET SINGLE APPLICATION
    // =========================================
    public function show($id)
    {
        $application = OrganisationApplication::with('user')->findOrFail($id);

        $application->logo_url = $application->logo_path
            ? asset('storage/' . $application->logo_path)
            : null;

        $application->certificate_url = $application->certificate_path
            ? asset('storage/' . $application->certificate_path)
            : null;

        $application->supporting_document_url = $application->supporting_document_path
            ? asset('storage/' . $application->supporting_document_path)
            : null;

        $application->submitted_by_name = $application->user?->name;
        $application->submitted_by_email = $application->user?->email;

        return response()->json($application);
    }

    // =========================================
    // GET LATEST APPLICATION BY USER
    // =========================================
    public function getUserApplications($userId)
    {
        try {
            $application = OrganisationApplication::where('user_id', $userId)
                ->latest('created_at')
                ->first();

            if (!$application) {
                return response()->json([
                    'application' => null
                ], 200);
            }

            $application->logo_url = $application->logo_path
                ? asset('storage/' . $application->logo_path)
                : null;

            $application->certificate_url = $application->certificate_path
                ? asset('storage/' . $application->certificate_path)
                : null;

            $application->supporting_document_url = $application->supporting_document_path
                ? asset('storage/' . $application->supporting_document_path)
                : null;

            return response()->json([
                'application' => $application
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch user application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================
    // APPROVE APPLICATION
    // =========================================
    public function approve($id)
    {
        $application = OrganisationApplication::findOrFail($id);

        if ($application->status === 'approved') {
            return response()->json([
                'message' => 'Application already approved'
            ]);
        }

        $application->status = 'approved';
        $application->admin_remark = null;
        $application->save();

        $existingOrganisation = Organisation::where(
            'registration_no',
            $application->registration_number
        )->first();

        if (!$existingOrganisation) {
            Organisation::create([
                'name' => $application->organisation_name,
                'registration_no' => $application->registration_number,
                'website' => $application->website,
                'category' => $application->organisation_type,
                'status' => 'verified',
                'logo' => $application->logo_path,
                'description' => $application->description,
                'email' => $application->email,
                'phone' => $application->phone,
                'address' => $application->address,
            ]);
        }

        return response()->json([
            'message' => 'Application approved successfully'
        ]);
    }

    // =========================================
    // REJECT APPLICATION
    // =========================================
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_remark' => 'nullable|string|max:1000',
        ]);

        $application = OrganisationApplication::findOrFail($id);

        $application->status = 'rejected';
        $application->admin_remark = $request->admin_remark;
        $application->save();

        return response()->json([
            'message' => 'Application rejected successfully',
            'application' => $application
        ]);
    }

    // =========================================
    // UPDATE APPLICATION
    // =========================================
    public function update(Request $request, $id)
    {
        $application = OrganisationApplication::findOrFail($id);

        $request->validate([
            'organisation_name' => 'required|string|max:255',
            'organisation_type' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'website' => 'required|string',
        ]);

        $application->update([
            'organisation_name' => $request->organisation_name,
            'organisation_type' => $request->organisation_type,
            'registration_number' => $request->registration_number,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
        ]);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application
        ]);
    }

    // =========================================
    // DELETE APPLICATION
    // =========================================
    public function destroy($id)
    {
        $application = OrganisationApplication::findOrFail($id);

        if ($application->logo_path) {
            Storage::disk('public')->delete($application->logo_path);
        }

        if ($application->certificate_path) {
            Storage::disk('public')->delete($application->certificate_path);
        }

        if ($application->supporting_document_path) {
            Storage::disk('public')->delete($application->supporting_document_path);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }
}