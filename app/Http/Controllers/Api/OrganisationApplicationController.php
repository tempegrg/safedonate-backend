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
        // Validate Input
        $request->validate([

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

        // =========================================
        // Upload Logo
        // =========================================

        $logoPath = $request
            ->file('logo')
            ->store('logos', 'public');

        // =========================================
        // Upload Registration Certificate
        // =========================================

        $certificatePath = $request
            ->file('certificate')
            ->store('certificates', 'public');

        // =========================================
        // Upload Supporting document (Optional)
        // =========================================

        $supportingDocumentPath = null;

        if ($request->hasFile('supporting_document')) {

            $supportingDocumentPath = $request
                ->file('supporting_document')
                ->store('supporting_documents', 'public');
        }

        // =========================================
        // Save Application
        // =========================================

       $application = OrganisationApplication::create([

        'user_id' => null,

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
    ]);

        return response()->json([

            'message' =>
                'Application submitted successfully',

            'application' =>
                $application,
        ]);
    }

    // =========================================
    // GET ALL APPLICATIONS
    // =========================================
    public function index()
    {
        return response()->json([

            'applications' =>
                OrganisationApplication::latest()->get(),
        ]);
    }

    // =========================================
    // GET SINGLE APPLICATION
    // =========================================
    public function show($id)
    {
        $application = OrganisationApplication::findOrFail($id);

        $application->logo_url =
            asset('storage/' . $application->logo_path);

        $application->certificate_url =
            asset('storage/' . $application->certificate_path);

        if ($application->supporting_document_path) {

            $application->supporting_document_url =
                asset(
                    'storage/' .
                    $application->supporting_document_path
                );
        }

        return response()->json($application);
    }
    // =========================================
    // APPROVE APPLICATION
    // =========================================
    public function approve($id)
    {
        $application = OrganisationApplication::findOrFail($id);

        // Already approved?
        if ($application->status == 'verified') {

            return response()->json([
                'message' => 'Application already approved'
            ]);

        }

        // Update application status
        $application->status = 'verified';
        $application->save();

        // Create verified organisation
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

        return response()->json([

            'message' => 'Application approved successfully'

        ]);
    }

    // =========================================
    // REJECT APPLICATION
    // =========================================
    public function reject($id)
    {
        $application = OrganisationApplication::findOrFail($id);

        $application->status = 'rejected';

        $application->save();

        return response()->json([
            'message' => 'Application rejected successfully'
        ]);
    }
}