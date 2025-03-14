<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        $query = Resume::where('user_id', Auth::id());
        
        // Always return completed resumes only
        $query->where('status', 'completed'); 
        
        return response()->json($query->orderBy('updated_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Create directly as completed
        $resume = Auth::user()->resumes()->create([
            'title' => $validated['title'],
            'status' => 'completed', 
        ]);

        return response()->json($resume, 201);
    }

    public function show($id)
    {
        $resume = Auth::user()->resumes()->find($id);

        if (!$resume) {
            return response()->json(['error' => 'Resume not found or does not belong to the user'], 404);
        }

        return response()->json($resume);
    }

    public function update(Request $request, $id)
    {
        $resume = Auth::user()->resumes()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'general_details' => 'sometimes|json',
            'work_experience' => 'sometimes|json',
            'education' => 'sometimes|json',
            'skills' => 'sometimes|json',
            'professional_qualifications' => 'sometimes|json',
            'referees' => 'sometimes|json',
            'cover_letter' => 'sometimes|json',
            'job_description' => 'sometimes|string'
        ]);

        foreach ($validated as $key => $value) {
            if (in_array($key, ['general_details', 'work_experience', 'education', 'skills', 
               'professional_qualifications', 'referees', 'cover_letter'])) {
                $resume->$key = json_decode($value, true);
            } else {
                $resume->$key = $value;
            }
        }

        $resume->save();

        return response()->json($resume);
    }

    public function destroy($id)
    {
        $resume = Auth::user()->resumes()->findOrFail($id);
        $resume->delete();
        return response()->json(null, 204);
    }

    /**
     * Convert a string to title case
     * 
     * @param string $string
     * @return string
     */
    protected function toTitleCase($string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Convert a string to sentence case
     * 
     * @param string $string
     * @return string
     */
    protected function toSentenceCase($string)
    {
        return ucfirst(strtolower($string));
    }
}