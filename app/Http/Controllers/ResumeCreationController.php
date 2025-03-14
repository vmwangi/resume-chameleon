<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Services\ResumeServiceFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class ResumeCreationController extends Controller
{
    protected $resumeService;

    public function __construct(ResumeServiceFacade $resumeService)
    {
        $this->resumeService = $resumeService;
    }

    /**
     * Upload and parse a resume
     */
    public function uploadResume(Request $request)
    {
        $validated = $request->validate([
            'resume_file' => 'required|file|mimes:pdf|max:10240',
            'job_description' => 'nullable|string'
        ]);
        
        try {
            $file = $request->file('resume_file');
            
            // Extract text directly from uploaded file without storing
            $pdfContent = $this->extractTextFromPdf($file->getRealPath());
            
            if (empty($pdfContent)) {
                return response()->json(['error' => 'Could not extract text from PDF'], 400);
            }
            
            // Extract details from PDF text
            $allDetails = $this->resumeService->extractAllDetailsFromPdf($pdfContent);
            
            // Generate a default title based on the extracted content
            $defaultTitle = '';
            if (!empty($allDetails['work_experience']) && is_array($allDetails['work_experience'])) {
                // Sort work experience by start date (most recent first)
                usort($allDetails['work_experience'], function($a, $b) {
                    return strtotime($b['startDate'] ?? '0') - strtotime($a['startDate'] ?? '0');
                });
                
                // Use the most recent job title for the resume title
                if (!empty($allDetails['work_experience'][0]['title'])) {
                    $defaultTitle = $allDetails['work_experience'][0]['title'] . ' Resume';
                }
            }
            
            // If we couldn't get a title from work experience, use the person's name
            if (empty($defaultTitle) && !empty($allDetails['general_details']['fullName'])) {
                $defaultTitle = $allDetails['general_details']['fullName'] . "'s Resume";
            }
            
            // If we still don't have a title, use a generic one
            if (empty($defaultTitle)) {
                $defaultTitle = 'Professional Resume';
            }
            
            // Create resume record as draft (not completed)
            $resume = Auth::user()->resumes()->create([
                'title' => $defaultTitle,
                'job_description' => $request->job_description,
                'general_details' => $allDetails['general_details'] ?? [],
                'work_experience' => $allDetails['work_experience'] ?? [],
                'education' => $allDetails['education'] ?? [],
                'skills' => $allDetails['skills'] ?? ['items' => []],
                'professional_qualifications' => $allDetails['professional_qualifications'] ?? ['items' => []],
                'referees' => $allDetails['referees'] ?? ['text' => ''],
                'source_text' => $pdfContent, // Store extracted text directly
                'status' => 'draft' // Create as draft initially
            ]);
            
            // If we have a job description, try to generate a more specific title
            if ($request->job_description) {
                $suggestedTitle = $this->resumeService->generateResumeTitle($allDetails, $request->job_description);
                if ($suggestedTitle) {
                    $resume->update(['title' => $suggestedTitle]);
                    $resume->refresh();
                }
            }
            
            return response()->json([
                'message' => 'Resume uploaded and parsed successfully',
                'data' => $resume,
                'suggested_title' => $defaultTitle
            ]);
            
        } catch (\Exception $e) {
            Log::error('Resume upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload resume: ' . $e->getMessage()], 500);
        }
    }

    public function tailorResume(Request $request)
    {
        try {
            $request->validate([
                'resume_id' => 'required|exists:resumes,id',
                'job_description' => 'required|string',
                'title' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            $resume = Resume::findOrFail($request->resume_id);

            // Verify ownership
            if ($resume->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Update the existing resume with new title and job description
            $resume->update([
                'title' => $request->title,
                'job_description' => $request->job_description
            ]);

            // Perform AI tailoring
            $tailoredData = $this->resumeService->tailorResumeToJob(
                $this->parseResumeData($resume),
                $request->job_description
            );

            if (!$tailoredData || !isset($tailoredData['general_details']['fullName'])) {
                Log::error('Invalid tailored data structure');
                throw new \Exception('AI service returned invalid resume structure');
            }

            // Update with tailored data
            $resume->update([
                'general_details' => $tailoredData['general_details'] ?? $resume->general_details,
                'work_experience' => $tailoredData['work_experience'] ?? $resume->work_experience,
                'education' => $tailoredData['education'] ?? $resume->education,
                'skills' => $tailoredData['skills'] ?? $resume->skills,
                'professional_qualifications' => $tailoredData['professional_qualifications'] ?? $resume->professional_qualifications,
                'status' => 'completed' // Mark as completed after tailoring
            ]);

            return response()->json([
                'message' => 'Resume tailored successfully',
                'resume_id' => $resume->id
            ]);

        } catch (\Exception $e) {
            Log::error('Tailor error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to tailor resume: ' . $e->getMessage()], 500);
        }
    }

    public function createFromExisting(Request $request)
    {
        try {
            $request->validate([
                'source_resume_id' => 'required|integer|exists:resumes,id',
                'job_description' => 'required|string',
                'title' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get the source resume
            $sourceResume = $user->resumes()->findOrFail($request->input('source_resume_id'));

            // Create a new resume with data from the source resume
            $newResume = $user->resumes()->create([
                'title' => $request->title,
                'job_description' => $request->job_description,
                'general_details' => $sourceResume->general_details,
                'work_experience' => $sourceResume->work_experience,
                'education' => $sourceResume->education,
                'skills' => $sourceResume->skills,
                'professional_qualifications' => $sourceResume->professional_qualifications,
                'interests' => $sourceResume->interests,
                'referees' => $sourceResume->referees,
                'source_text' => $sourceResume->source_text,
                'status' => 'draft' // Start as draft
            ]);

            // Prepare resume data for tailoring
            $resumeData = $this->parseResumeData($sourceResume);

            // Perform AI tailoring
            $tailoredData = $this->resumeService->tailorResumeToJob(
                $resumeData,
                $request->job_description
            );

            if (!$tailoredData || !isset($tailoredData['general_details']['fullName'])) {
                Log::error('Invalid tailored data structure in createFromExisting');
                throw new \Exception('AI service returned invalid resume structure');
            }

            // Update the new resume with tailored data
            $newResume->update([
                'general_details' => $tailoredData['general_details'] ?? $sourceResume->general_details,
                'work_experience' => $tailoredData['work_experience'] ?? $sourceResume->work_experience,
                'education' => $tailoredData['education'] ?? $sourceResume->education,
                'skills' => $tailoredData['skills'] ?? $sourceResume->skills,
                'professional_qualifications' => $tailoredData['professional_qualifications'] ?? $sourceResume->professional_qualifications,
                'status' => 'completed' // Mark as completed after tailoring
            ]);

            return response()->json([
                'message' => 'Resume created and tailored successfully',
                'resume_id' => $newResume->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create resume from existing: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create resume: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Extract text from PDF file
     */
    private function extractTextFromPdf($filePath)
    {
        // Use in-memory parsing instead of file storage
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    private function parseResumeData($resume)
    {
        return [
            'general_details' => is_string($resume->general_details) 
                ? json_decode($resume->general_details, true) 
                : ($resume->general_details ?? []),
            
            'work_experience' => is_string($resume->work_experience) 
                ? json_decode($resume->work_experience, true) 
                : ($resume->work_experience ?? []),
            
            'education' => is_string($resume->education) 
                ? json_decode($resume->education, true) 
                : ($resume->education ?? []),
            
            'skills' => is_string($resume->skills) 
                ? json_decode($resume->skills, true) 
                : ($resume->skills ?? []),
            
            'professional_qualifications' => is_string($resume->professional_qualifications) 
                ? json_decode($resume->professional_qualifications, true) 
                : ($resume->professional_qualifications ?? []),
            
            'referees' => is_string($resume->referees) 
                ? json_decode($resume->referees, true) 
                : ($resume->referees ?? [])
        ];
    }

    /**
     * Convert a string to title case
     * 
     * @param string $string
     * @return string
     */
    private function toTitleCase($string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Convert a string to sentence case
     * 
     * @param string $string
     * @return string
     */
    private function toSentenceCase($string)
    {
        return ucfirst(strtolower($string));
    }
}