<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        try { 
            Mail::to('support@resume-chameleon.com')->send(new \App\Mail\ContactFormSubmission($validated));

            return response()->json([
                'message' => 'Your message has been sent successfully. We will get back to you soon.'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to process contact form: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send your message. Please try again later.'
            ], 500);
        }
    }
}
