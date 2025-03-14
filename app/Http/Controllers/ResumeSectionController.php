<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\RegenerationLimit;
use App\Services\ResumeServiceFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ResumeSectionController extends Controller
{
    protected $resumeService;

    public function __construct(ResumeServiceFacade $resumeService)
    {
        $this->resumeService = $resumeService;
    }

    /**
     * Regenerate a specific section of the resume
     */
    public function regenerateSection(Request $request, $id, $section)
    {
        $request->validate([
            'section' => 'required|string|in:general_details,work_experience,education,skills,professional_qualifications,referees',
        ]);
        
        try {
            $resume = Resume::where('user_id', Auth::id())->findOrFail($id);
            
            // Check if user has reached regeneration limit
            if (!$this->checkRegenerationLimit($resume->id, $section)) {
                return response()->json(['error' => 'You have reached the maximum regeneration limit for this section (3 per hour)'], 429);
            }
            
            // Get current section data
            $currentData = $this->parseSectionData($resume->$section);
            $sourceContent = null;

            // Priority 1: Use stored source text
            if (!empty($resume->source_text)) {
                $sourceContent = $resume->source_text; 
            }
            // Priority 2: Use existing structured data
            else {
                $resumeData = [
                    'general_details' => $resume->general_details,
                    'work_experience' => $resume->work_experience,
                    'education' => $resume->education,
                    'skills' => $resume->skills,
                    'professional_qualifications' => $resume->professional_qualifications,
                    'referees' => $resume->referees
                ];
                $sourceContent = json_encode($resumeData); 
            }

            // Regenerate the section
            $regeneratedData = $this->resumeService->regenerateSection(
                $sourceContent, 
                $section, 
                $currentData,
                $resume->job_description
            );
            
            if (!$regeneratedData) {
                return response()->json(['error' => 'Failed to regenerate section'], 500);
            }
            
            // Format the regenerated data to match tailored format
            $formattedData = $this->formatRegeneratedData($regeneratedData, $section);
            
            // Update the resume with proper JSON encoding
            if ($section === 'work_experience') {
                // Ensure work_experience is stored as a properly formatted JSON array
                if (is_array($formattedData)) {
                    $resume->work_experience = json_encode($formattedData);
                } else {
                    // If not an array, encode an empty array
                    $resume->work_experience = json_encode([]);
                }
            } else {
                $resume->$section = json_encode($formattedData);
            }
            
            $resume->save();
            
            // Record the regeneration
            $this->recordRegeneration($resume->id, $section);

            $resume->refresh();
            
            return response()->json([
                'message' => 'Section regenerated successfully',
                'data' => $this->formatResumeResponse($resume)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Section regeneration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to regenerate section: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Format regenerated data to match tailored format
     */
    private function formatRegeneratedData($data, $section)
    {
        if (empty($data)) {
            return [];
        }
        
        try {
            switch ($section) {
                case 'skills':
                    // Handle both direct array and nested structure
                    if (isset($data['skills']) && isset($data['skills']['items']) && is_array($data['skills']['items'])) {
                        // If we got a full resume structure with skills inside
                        $items = $data['skills']['items'];
                        $data = ['items' => []];
                        foreach ($items as $skill) {
                            $data['items'][] = $this->toSentenceCase($skill);
                        }
                    } elseif (isset($data['items']) && is_array($data['items'])) {
                        // Format skills to sentence case
                        foreach ($data['items'] as $key => $skill) {
                            $data['items'][$key] = $this->toSentenceCase($skill);
                        }
                    } elseif (is_array($data)) {
                        // If we just got a plain array of skills
                        $items = $data;
                        $data = ['items' => []];
                        foreach ($items as $skill) {
                            $data['items'][] = $this->toSentenceCase($skill);
                        }
                    }
                    break;
                    
                case 'professional_qualifications':
                    // Handle both direct array and nested structure
                    if (isset($data['professional_qualifications']) && isset($data['professional_qualifications']['items']) && is_array($data['professional_qualifications']['items'])) {
                        // If we got a full resume structure with qualifications inside
                        $data = $data['professional_qualifications'];
                    }
                    
                    if (isset($data['items']) && is_array($data['items'])) {
                        // Format qualification names to title case
                        foreach ($data['items'] as $key => $qual) {
                            if (isset($qual['name'])) {
                                $data['items'][$key]['name'] = $this->toTitleCase($qual['name']);
                            }
                            if (isset($qual['issuer'])) {
                                $data['items'][$key]['issuer'] = $this->toTitleCase($qual['issuer']);
                            }
                            
                            // Ensure all required fields exist
                            $requiredFields = ['name', 'issuer', 'date', 'expirationDate', 'current'];
                            foreach ($requiredFields as $field) {
                                if (!isset($qual[$field])) {
                                    $data['items'][$key][$field] = $field === 'current' ? false : '';
                                }
                            }
                        }
                    } else {
                        // If we don't have the expected structure, create it
                        $data = ['items' => []];
                    }
                    break;
                    
                case 'work_experience':
                    // Handle both direct array and nested structure
                    if (isset($data['work_experience']) && is_array($data['work_experience'])) {
                        // If we got a full resume structure with work experience inside
                        $data = $data['work_experience'];
                    }
                    
                    // Ensure we're working with an array
                    if (!is_array($data)) {
                        $data = [];
                    }
                    
                    // Ensure each work experience entry has all required fields
                    foreach ($data as $key => $job) {
                        // Ensure all required fields exist
                        $requiredFields = ['title', 'company', 'location', 'startDate', 'endDate', 'current', 'description'];
                        foreach ($requiredFields as $field) {
                            if (!isset($job[$field])) {
                                $data[$key][$field] = $field === 'current' ? false : '';
                            }
                        }
                        
                        // Format title and company to title case if they exist
                        if (isset($job['title']) && is_string($job['title'])) {
                            $data[$key]['title'] = $this->toTitleCase($job['title']);
                        }
                        
                        if (isset($job['company']) && is_string($job['company'])) {
                            $data[$key]['company'] = $this->toTitleCase($job['company']);
                        }
                        
                        // Ensure current is boolean
                        $data[$key]['current'] = isset($job['current']) ? (bool)$job['current'] : false;
                    }
                    break;
                    
                case 'education':
                    // Handle education data which might be a JSON string
                    if (isset($data['education']) && is_array($data['education'])) {
                        // If we got a full resume structure with education inside
                        $data = $data['education'];
                    }
                    
                    if (is_string($data)) {
                        $educationData = json_decode($data, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (isset($educationData['education']) && is_array($educationData['education'])) {
                                // Format education entries
                                foreach ($educationData['education'] as $key => $edu) {
                                    $this->formatEducationEntry($educationData['education'][$key]);
                                }
                                return json_encode($educationData);
                            } else {
                                // If we just got a plain array of education entries
                                foreach ($educationData as $key => $edu) {
                                    $this->formatEducationEntry($educationData[$key]);
                                }
                                return json_encode(['education' => $educationData]);
                            }
                        }
                    } elseif (is_array($data)) {
                        // Format education entries
                        foreach ($data as $key => $edu) {
                            $this->formatEducationEntry($data[$key]);
                        }
                        
                        // If this is just an array of education entries, wrap it
                        if (!isset($data['education'])) {
                            $data = ['education' => $data];
                        }
                        
                        return json_encode($data);
                    }
                    break;
                    
                case 'general_details':
                    // Handle nested structure
                    if (isset($data['general_details']) && is_array($data['general_details'])) {
                        // If we got a full resume structure with general details inside
                        $data = $data['general_details'];
                    }
                    
                    // Ensure all required fields exist
                    $requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
                    foreach ($requiredFields as $field) {
                        if (!isset($data[$field])) {
                            $data[$field] = '';
                        }
                    }
                    
                    // Format fullName to title case
                    if (isset($data['fullName']) && is_string($data['fullName'])) {
                        $data['fullName'] = $this->toTitleCase($data['fullName']);
                    }
                    break;
                    
                case 'referees':
                    // Handle nested structure
                    if (isset($data['referees']) && is_array($data['referees'])) {
                        // If we got a full resume structure with referees inside
                        $data = $data['referees'];
                    }
                    
                    // Ensure the text field exists
                    if (!isset($data['text'])) {
                        $data = ['text' => ''];
                    }
                    break;
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error formatting regenerated data: ' . $e->getMessage());
            Log::error('Section: ' . $section);
            Log::error('Data: ' . json_encode($data));
            
            // Return a safe default structure based on the section
            return $this->getDefaultStructureForSection($section);
        }
    }
    
    /**
     * Format an education entry
     */
    private function formatEducationEntry(&$entry)
    {
        // Ensure all required fields exist
        $requiredFields = ['institution', 'degree', 'startDate', 'endDate', 'current', 'description'];
        foreach ($requiredFields as $field) {
            if (!isset($entry[$field])) {
                $entry[$field] = $field === 'current' ? false : '';
            }
        }
        
        // Format institution and degree to title case if they exist
        if (isset($entry['institution']) && is_string($entry['institution'])) {
            $entry['institution'] = $this->toTitleCase($entry['institution']);
        }
        
        if (isset($entry['degree']) && is_string($entry['degree'])) {
            $entry['degree'] = $this->toTitleCase($entry['degree']);
        }
        
        // Ensure current is boolean
        $entry['current'] = isset($entry['current']) ? (bool)$entry['current'] : false;
    }
    
    /**
     * Get default structure for a section
     */
    private function getDefaultStructureForSection($section)
    {
        switch ($section) {
            case 'skills':
                return ['items' => []];
                
            case 'professional_qualifications':
                return ['items' => []];
                
            case 'work_experience':
                return [];
                
            case 'education':
                return json_encode(['education' => []]);
                
            case 'general_details':
                return [
                    'fullName' => '',
                    'email' => '',
                    'phone' => '',
                    'location' => '',
                    'summary' => ''
                ];
                
            case 'referees':
                return ['text' => ''];
                
            default:
                return [];
        }
    }

    /**
     * Get regeneration limits for a resume
     */
    public function getRegenerationLimits($id)
    {
        try {
            $resume = Resume::where('user_id', Auth::id())->findOrFail($id);
            
            $limits = [];
            
            $sections = [
                'general_details',
                'work_experience',
                'education',
                'skills',
                'professional_qualifications',
                'referees'
            ];
            
            foreach ($sections as $section) {
                $count = RegenerationLimit::where('resume_id', $resume->id)
                    ->where('section', $section)
                    ->count();
                    
                $limits[$section] = $count;
            }
            
            return response()->json([
                'limits' => $limits,
                'max_per_section' => 3
            ]);
            
        } catch (\Exception $e) { 
            return response()->json(['error' => 'Failed to get regeneration limits'], 500);
        }
    }

    private function parseSectionData($data)
    {
        if (is_array($data)) {
            return $data;
        }
        
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            return $decoded ?? [];
        }
        
        return [];
    }

    private function formatResumeResponse($resume)
    {
        return [
            'id' => $resume->id,
            'user_id' => $resume->user_id,
            'title' => $resume->title,
            'general_details' => $resume->general_details,
            'work_experience' => $resume->work_experience,
            'education' => $resume->education,
            'skills' => $resume->skills,
            'professional_qualifications' => $resume->professional_qualifications,
            'referees' => $resume->referees,
            'job_description' => $resume->job_description,
            'cover_letter' => $resume->cover_letter,
            'status' => $resume->status,
            'created_at' => $resume->created_at,
            'updated_at' => $resume->updated_at
        ];
    }

    /**
     * Check if user has reached regeneration limit (3 per hour per section)
     */
    private function checkRegenerationLimit($resumeId, $section)
    {
        $oneHourAgo = Carbon::now()->subHour();
        
        $count = RegenerationLimit::where('resume_id', $resumeId)
            ->where('section', $section)
            ->where('created_at', '>=', $oneHourAgo)
            ->count();
            
        return $count < 3;
    }
    
    /**
     * Record a regeneration attempt
     */
    private function recordRegeneration($resumeId, $section)
    {
        RegenerationLimit::create([
            'resume_id' => $resumeId,
            'user_id' => Auth::id(),
            'section' => $section
        ]);
    }
    
    /**
     * Convert a string to title case
     * 
     * @param string $string
     * @return string
     */
    private function toTitleCase($string)
    {
        if (!is_string($string)) {
            return '';
        }
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
        if (!is_string($string)) {
            return '';
        }
        return ucfirst(strtolower($string));
    }
}