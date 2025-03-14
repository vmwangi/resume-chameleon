<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeDownloadController extends Controller
{
    public function downloadResume(Request $request, $id)
    {
        try {
            // Check for token in the request
            $token = $request->query('token');
            if ($token) {
                // Find the token in the database
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($personalAccessToken) {
                    // Get the user associated with the token
                    $user = $personalAccessToken->tokenable;

                    // Log in the user
                    Auth::login($user);
                }
            }

            // Check if user is authenticated
            if (!auth()->check()) {
                // Instead of returning a JSON response which causes a redirect to login
                // Return a redirect to the login page
                return redirect()->route('login');
            }

            // Get the resume by ID
            $resume = Resume::findOrFail($id);

            // Check if the resume belongs to the authenticated user
            if ($resume->user_id !== auth()->id()) {
                return response()->json(['error' => 'You do not have permission to access this resume'], 403);
            }

            // Ensure all JSON fields are decoded properly into arrays
            $resumeData = [
                'title' => $resume->title,
                'general_details' => is_string($resume->general_details) ? json_decode($resume->general_details, true) : ($resume->general_details ?? []),
                'work_experience' => is_string($resume->work_experience) ? json_decode($resume->work_experience, true) : ($resume->work_experience ?? []),
                'education' => is_string($resume->education) ? json_decode($resume->education, true) : ($resume->education ?? []),
                'skills' => is_string($resume->skills) ? json_decode($resume->skills, true) : ($resume->skills ?? ['items' => []]),
                'professional_qualifications' => is_string($resume->professional_qualifications) ? json_decode($resume->professional_qualifications, true) : ($resume->professional_qualifications ?? ['items' => []]),
                'referees' => is_string($resume->referees) ? json_decode($resume->referees, true) : ($resume->referees ?? ['text' => ''])
            ];

            // Fallbacks for invalid JSON or unexpected data
            $resumeData['general_details'] = is_array($resumeData['general_details']) ? $resumeData['general_details'] : [];

            // Handle work experience after regeneration
            if (is_array($resumeData['work_experience'])) {
                // If work_experience is already a proper array, use it directly
                $resumeData['work_experience'] = $resumeData['work_experience'];
            } else {
                // If not an array, set to empty array
                $resumeData['work_experience'] = [];
            }

            // Handle nested education structure - if education is nested inside an "education" property, extract it
            if (is_array($resumeData['education']) && isset($resumeData['education']['education']) && is_array($resumeData['education']['education'])) {
                $resumeData['education'] = $resumeData['education']['education'];
            } elseif (!is_array($resumeData['education'])) {
                $resumeData['education'] = [];
            }

            // Handle professional qualifications with better structure detection
            if (is_array($resumeData['professional_qualifications'])) {
                // Case 1: Double nested structure
                if (isset($resumeData['professional_qualifications']['professional_qualifications']['items'])) {
                    $resumeData['professional_qualifications'] = ['items' => $resumeData['professional_qualifications']['professional_qualifications']['items']];
                }
                // Case 2: Already has items array
                elseif (isset($resumeData['professional_qualifications']['items'])) {
                    // Keep as is
                }
                // Case 3: Is a simple array of qualifications
                elseif (is_array($resumeData['professional_qualifications']) && !isset($resumeData['professional_qualifications']['items'])) {
                    // Check if it's a flat array of strings or objects
                    $isSimpleArray = false;
                    foreach ($resumeData['professional_qualifications'] as $key => $value) {
                        if (is_numeric($key)) {
                            $isSimpleArray = true;
                            break;
                        }
                    }

                    if ($isSimpleArray) {
                        $items = [];
                        foreach ($resumeData['professional_qualifications'] as $qual) {
                            if (is_string($qual)) {
                                $items[] = ['name' => $qual];
                            } elseif (is_array($qual)) {
                                $items[] = $qual;
                            }
                        }
                        $resumeData['professional_qualifications'] = ['items' => $items];
                    } else {
                        $resumeData['professional_qualifications'] = ['items' => []];
                    }
                }
            } else {
                $resumeData['professional_qualifications'] = ['items' => []];
            }

            $resumeData['skills'] = is_array($resumeData['skills']) ? $resumeData['skills'] : ['items' => []];
            $resumeData['referees'] = is_array($resumeData['referees']) ? $resumeData['referees'] : ['text' => ''];

            // Load the view with the resume data
            $pdf = Pdf::loadView('pdf.resume', ['resume' => (object) $resumeData]);

            // Generate filename: job title - applicant name
            $jobTitle = '';
            if (!empty($resumeData['work_experience']) && is_array($resumeData['work_experience'])) {
                usort($resumeData['work_experience'], function ($a, $b) {
                    return strtotime($b['startDate'] ?? '0') - strtotime($a['startDate'] ?? '0');
                });
                $jobTitle = $resumeData['work_experience'][0]['title'] ?? '';
            }

            $applicantName = $resumeData['general_details']['fullName'] ??
                       $resumeData['general_details']['email'] ??
                       'Resume';
            $applicantName = $this->toTitleCase($applicantName);

            $filename = trim($jobTitle) ? $jobTitle . ' - ' . $applicantName : $applicantName;
            $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);

            // Set the filename in the Content-Disposition header
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error downloading resume: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download resume: ' . $e->getMessage()], 500);
        }
    }

    public function downloadCoverLetter(Request $request, $id)
    {
        try {
            // Check for token in the request
            $token = $request->query('token');
            if ($token) {
                // Find the token in the database
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($personalAccessToken) {
                    // Get the user associated with the token
                    $user = $personalAccessToken->tokenable;

                    // Log in the user
                    Auth::login($user);
                }
            }

            // Check if user is authenticated
            if (!auth()->check()) {
                // Instead of returning a JSON response which causes a redirect to login
                // Return a redirect to the login page
                return redirect()->route('login');
            }

            // Get the resume by ID
            $resume = Resume::findOrFail($id);

            // Check if the resume belongs to the authenticated user
            if ($resume->user_id !== auth()->id()) {
                return response()->json(['error' => 'You do not have permission to access this cover letter'], 403);
            }

            // Parse cover_letter if it's a JSON string, or use default structure
            $coverLetterData = $resume->cover_letter;
            if (is_string($resume->cover_letter)) {
                $coverLetterData = json_decode($resume->cover_letter, true) ?? [];
            }

            // Get the work experience data
            $workExperience = is_string($resume->work_experience) ?
                json_decode($resume->work_experience, true) :
                ($resume->work_experience ?? []);

            // Ensure work_experience is an array
            if (!is_array($workExperience)) {
                $workExperience = [];
            }

            // Ensure all required fields exist with defaults
            $resumeData = [
                'title' => $resume->title,
                'general_details' => is_string($resume->general_details) ? json_decode($resume->general_details, true) : ($resume->general_details ?? []),
                'work_experience' => $workExperience,
                'cover_letter' => array_merge([
                    'applicant' => [
                        'name' => $resume->general_details['fullName'] ?? '',
                        'location' => $resume->general_details['location'] ?? '',
                        'email' => $resume->general_details['email'] ?? '',
                        'phone' => $resume->general_details['phone'] ?? ''
                    ],
                    'date' => date('F j, Y'),
                    'company' => [
                        'name' => '',
                        'location' => ''
                    ],
                    'subject' => 'Application for Position',
                    'greeting' => 'Dear Hiring Manager,',
                    'introduction' => '',
                    'background' => '',
                    'skillsAlignment' => '',
                    'closing' => ''
                ], $coverLetterData ?? [])
            ];

            $pdf = Pdf::loadView('pdf.cover_letter', ['resume' => (object) $resumeData]);

            // Generate filename: Cover Letter - applicant name - role
            $applicantName = $resumeData['general_details']['fullName'] ?? '';
            $applicantName = $this->toTitleCase($applicantName);

            $jobTitle = '';
            if (!empty($resumeData['work_experience']) && is_array($resumeData['work_experience'])) {
                $workExperience = $resumeData['work_experience'];
                usort($workExperience, function ($a, $b) {
                    return strtotime($b['startDate'] ?? '0') - strtotime($a['startDate'] ?? '0');
                });
                $jobTitle = $workExperience[0]['title'] ?? '';
            }

            $filename = 'Cover Letter';
            if ($applicantName) {
                $filename .= ' - ' . $applicantName;
            }
            if ($jobTitle) {
                $filename .= ' - ' . $jobTitle;
            }
            $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);

            // Set the filename in the Content-Disposition header
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error downloading cover letter: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download cover letter: ' . $e->getMessage()], 500);
        }
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
}