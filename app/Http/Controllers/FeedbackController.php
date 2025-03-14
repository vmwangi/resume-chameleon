<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'score' => 'required|integer|min:0|max:10',
        ]);

        $feedback = new Feedback();
        $feedback->text = $validated['text'];
        $feedback->score = $validated['score'];
        $feedback->user_id = Auth::user()->id;
        $feedback->save();

        // Send email notification
        Mail::raw('New feedback received: ' . $validated['text'] . "\nRecommendation Score: " . $validated['score'], function ($message) {
            $message->to('valentine@afterwork.ai')
                    ->subject('Resume Chameleon - New Feedback');
        });

        return response()->json(['message' => 'Feedback received successfully.'], 201);
    }
}