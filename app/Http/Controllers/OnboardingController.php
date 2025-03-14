<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnboardingResponse;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Store a new onboarding response.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'objective' => 'required|string',
            'challenge' => 'required|string',
            'referral_source' => 'required|string',
            'custom_objective' => 'nullable|string',
            'custom_challenge' => 'nullable|string',
            'custom_referral_source' => 'nullable|string',
        ]);

        // Check if user already has an onboarding response
        $existingResponse = OnboardingResponse::where('user_id', Auth::id())->first();
        
        if ($existingResponse) {
            return response()->json(['message' => 'Onboarding already completed'], 200);
        }

        $response = OnboardingResponse::create([
            'user_id' => Auth::id(),
            'objective' => $validated['objective'],
            'challenge' => $validated['challenge'],
            'referral_source' => $validated['referral_source'],
            'custom_objective' => $validated['custom_objective'],
            'custom_challenge' => $validated['custom_challenge'],
            'custom_referral_source' => $validated['custom_referral_source'],
        ]);

        return response()->json(['message' => 'Onboarding responses saved successfully'], 200);
    }

    /**
     * Check if the user has completed onboarding.
     */
    public function check()
    {
        $completed = OnboardingResponse::where('user_id', Auth::id())->exists();
        return response()->json(['completed' => $completed]);
    }
}