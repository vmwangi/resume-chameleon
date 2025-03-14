<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show()
    {
        return auth()->user();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->update($request->only('name'));
        return $user;
    }

    public function upgradePlan(Request $request)
    {
        $user = auth()->user();
        $user->update(['plan' => 'pro']);
        return response()->json(['message' => 'Plan upgraded successfully']);
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_updates' => 'required|boolean',
            'job_alerts_tips' => 'required|boolean',
        ]);

        $user = auth()->user();
        $user->notification_preferences = $request->only([
            'email_updates',
            'job_alerts_tips'
        ]);
        $user->save();

        return response()->json([
            'message' => 'Notification preferences updated successfully',
            'notification_preferences' => $user->notification_preferences
        ]);
    }

    public function destroy()
    {
        $user = auth()->user();
        
        // Delete related data if needed
        $user->resumes()->delete();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Delete the user
        $user->delete();
        
        return response()->json(['message' => 'Account deleted successfully']);
    }
}