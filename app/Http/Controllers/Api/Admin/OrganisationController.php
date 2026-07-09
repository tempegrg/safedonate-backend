<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisation;

class OrganisationController extends Controller
{
    // =========================================
    // GET ALL VERIFIED ORGANISATIONS
    // =========================================
    public function index()
    {
        $organisations = Organisation::where('status', 'verified')
            ->latest()
            ->get()
            ->map(function ($organisation) {
                $organisation->logo_url = $organisation->logo
                    ? asset('storage/' . $organisation->logo)
                    : null;

                return $organisation;
            });

        return response()->json([
            'organisations' => $organisations
        ]);
    }

    // =========================================
    // ADD NEW ORGANISATION
    // =========================================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_no' => 'required|string|max:255',
            'website' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
        ]);

        $organisation = Organisation::create([
            'name' => $request->name,
            'registration_no' => $request->registration_no,
            'website' => $request->website,
            'category' => $request->category,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $request->logo,
            'status' => $request->status ?? 'verified',
        ]);

        $organisation->logo_url = $organisation->logo
            ? asset('storage/' . $organisation->logo)
            : null;

        return response()->json([
            'message' => 'Organisation added successfully',
            'organisation' => $organisation
        ], 201);
    }

    // =========================================
    // DELETE ORGANISATION
    // =========================================
    public function destroy($id)
    {
        $organisation = Organisation::find($id);

        if (!$organisation) {
            return response()->json([
                'message' => 'Organisation not found'
            ], 404);
        }

        $organisation->delete();

        return response()->json([
            'message' => 'Organisation deleted successfully'
        ]);
    }
}