<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Services\ResumeServiceFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CoverLetterController extends Controller
{
    protected $resumeService;

    public function __construct(ResumeServiceFacade $resumeService)
    {
        $this->resumeService = $resumeService;
    }

    public function generateCoverLetter(Request $request)
    {
        try {
            $request->validate([
                'resume_id' => 'required|exists:resumes,id',
                'structured' => 'sometimes|boolean'
            ]);

            $resumeId = $request->input('resume_id');
            $jobDescription = $request->input('job_description');
            $structured = $request->input('structured', true);
            
            // Validate inputs
            if (!$resumeId) {
                return response()->json(['error' => 'Resume ID is required'], 400);
            }
            
            // Get the resume
            $resume = Resume::findOrFail($resumeId);
            
            // Check if user owns this resume
            if ($resume->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Use job description from request or from resume
            if (!$jobDescription) {
                $jobDescription = $resume->job_description;
                if (!$jobDescription) {
                    return response()->json(['error' => 'Job description is required'], 400);
                }
            }
            
            // Prepare resume data
            $resumeData = [
                'general_details' => $this->parseJsonField($resume->general_details),
                'work_experience' => $this->parseJsonField($resume->work_experience),
                'education' => $this->parseJsonField($resume->education),
                'skills' => $this->parseJsonField($resume->skills),
                'professional_qualifications' => $this->parseJsonField($resume->professional_qualifications),
                'referees' => $this->parseJsonField($resume->referees)
            ];
            
            $coverLetter = null;
            $retryCount = 0;
            $maxRetries = 1;  
            
            while ($retryCount <= $maxRetries && !$coverLetter) {
                $coverLetter = $this->resumeService->generateCoverLetter($resumeData, $jobDescription, $structured);
                
                if (!$coverLetter && $retryCount < $maxRetries) {
                    $retryCount++;
                    sleep(1); 
                }
            }
            
            if (!$coverLetter) {
                return response()->json(['error' => 'Failed to generate cover letter'], 500);
            }
            
            // Update the resume with the cover letter
            $resume->cover_letter = is_array($coverLetter) ? json_encode($coverLetter) : $coverLetter;
            $resume->save();
            
            return response()->json([
                'message' => 'Cover letter generated successfully',
                'cover_letter' => $coverLetter
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating cover letter: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate cover letter: ' . $e->getMessage()], 500);
        }
    }

    public function parseCoverLetter(Request $request)
    {
        $request->validate([
            'cover_letter' => 'required|string',
        ]);

        try {
            $parsedCoverLetter = $this->resumeService->parseCoverLetter($request->input('cover_letter'));
            
            if (!$parsedCoverLetter) {
                return response()->json(['error' => 'Failed to parse cover letter'], 500);
            }
            
            return response()->json($parsedCoverLetter);
        } catch (\Exception $e) {
            Log::error('Failed to parse cover letter: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to parse cover letter: ' . $e->getMessage()], 500);
        }
    }

    private function parseJsonField($field)
    {
        if (empty($field)) {
            return [];
        }
        
        if (is_array($field)) {
            return $field;
        }
        
        try {
            $parsed = json_decode($field, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $parsed;
            }
            
            Log::warning('parseJsonField - JSON decode error: ' . json_last_error_msg(), [
                'field_type' => gettype($field),
                'field_length' => is_string($field) ? strlen($field) : 'not a string',
                'field_preview' => is_string($field) ? substr($field, 0, 100) : 'not a string'
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error parsing JSON field: ' . $e->getMessage(), [
                'field_type' => gettype($field),
                'field_length' => is_string($field) ? strlen($field) : 'not a string',
                'field_preview' => is_string($field) ? substr($field, 0, 100) : 'not a string'
            ]);
            return [];
        }
    }
}