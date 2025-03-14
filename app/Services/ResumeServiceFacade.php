<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ResumeServiceFacade
{
    protected $deepseekService;
    protected $openaiService;

    public function __construct(DeepseekService $deepseekService, OpenAIService $openaiService)
    {
        $this->deepseekService = $deepseekService;
        $this->openaiService = $openaiService;
    }

    /**
     * Extract all resume details from PDF content with fallback
     * 
     * @param string $pdfContent
     * @return array
     */
    public function extractAllDetailsFromPdf($pdfContent)
    {
        try {
            // Try OpenAI first
            $result = $this->openaiService->extractAllDetailsFromPdf($pdfContent);
            
            // Check if the result is valid
            if ($this->isValidResumeData($result)) {
                return $result;
            }
            
            return $this->deepseekService->extractAllDetailsFromPdf($pdfContent);
        } catch (\Exception $e) {
            Log::error('Error in OpenAI extractAllDetailsFromPdf: ' . $e->getMessage());
            
            // Try Deepseek as fallback
            try {
                return $this->deepseekService->extractAllDetailsFromPdf($pdfContent);
            } catch (\Exception $e2) {
                Log::error('Both OpenAI and Deepseek failed to extract resume details: ' . $e2->getMessage());
                return [
                    'general_details' => [],
                    'work_experience' => [],
                    'education' => [],
                    'skills' => ['items' => []],
                    'professional_qualifications' => ['items' => []],
                    'referees' => ['text' => '']
                ];
            }
        }
    }
    
    /**
     * Regenerate a specific section of the resume with fallback
     */
    public function regenerateSection($pdfContent, $section, $currentData = [], $jobDescription = null)
    {
        try {
            // Try OpenAI first
            $result = $this->openaiService->regenerateSection($pdfContent, $section, $currentData, $jobDescription);
            
            // Check if the result is valid
            if ($result && !empty($result)) {
                return $result;
            }
            
            return $this->deepseekService->regenerateSection($pdfContent, $section, $currentData, $jobDescription);
        } catch (\Exception $e) {
            Log::error('Error in OpenAI regenerateSection: ' . $e->getMessage());
            
            // Try Deepseek as fallback
            try {
                return $this->deepseekService->regenerateSection($pdfContent, $section, $currentData, $jobDescription);
            } catch (\Exception $e2) {
                Log::error('Both OpenAI and Deepseek failed to regenerate section: ' . $e2->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Tailor resume to job description with fallback
     */
    public function tailorResumeToJob($resumeData, $jobDescription)
    {
        try {

            $result = $this->openaiService->tailorResumeToJob($resumeData, $jobDescription);
        
            if ($result && $this->isValidTailoredData($result)) {
                return $result;
            }
        
            return $this->deepseekService->tailorResumeToJob($resumeData, $jobDescription);
            
            return $this->deepseekService->tailorResumeToJob($resumeData, $jobDescription);
        } catch (\Exception $e) {
            Log::error('Error in OpenAI tailorResumeToJob: ' . $e->getMessage());
            
            // Try Deepseek as fallback
            try {
                return $this->deepseekService->tailorResumeToJob($resumeData, $jobDescription);
            } catch (\Exception $e2) {
                Log::error('Both OpenAI and Deepseek failed to tailor resume: ' . $e2->getMessage());
                return null;
            }
        }
    }

    private function isValidTailoredData($data)
    {
        $requiredFields = [
            'general_details' => ['fullName', 'email', 'phone', 'location', 'summary'],
            'work_experience' => [],
            'education' => [],
            'skills' => ['items']
        ];

        foreach ($requiredFields as $section => $fields) {
            if (!isset($data[$section])) {
                return false;
            }
            foreach ($fields as $field) {
                if (!array_key_exists($field, $data[$section])) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Generate a suitable resume title with fallback
     */
    public function generateResumeTitle($resumeData, $jobDescription)
    {
        try {
            // Try OpenAI first
            $result = $this->openaiService->generateResumeTitle($resumeData, $jobDescription);
            
            // Check if the result is valid
            if ($result && !empty($result)) {
                return $result;
            }
            
            // If OpenAI failed, try Deepseek
            return $this->deepseekService->generateResumeTitle($resumeData, $jobDescription);
        } catch (\Exception $e) {
            Log::error('Error in OpenAI generateResumeTitle: ' . $e->getMessage());
            
            // Try Deepseek as fallback
            try {
                return $this->deepseekService->generateResumeTitle($resumeData, $jobDescription);
            } catch (\Exception $e2) {
                Log::error('Both OpenAI and Deepseek failed to generate resume title: ' . $e2->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Generate a cover letter with fallback
     */
    public function generateCoverLetter($resumeData, $jobDescription, $structured = false)
    {
        try {
            if (empty($resumeData['general_details']['fullName'])) {
                throw new \Exception('Invalid resume data - missing general details');
            }

            $coverLetter = $this->openaiService->generateCoverLetter($resumeData, $jobDescription, $structured);
            
            if (!$coverLetter && $structured) {
                // If structured generation failed, try unstructured
                $coverLetter = $this->openaiService->generateCoverLetter($resumeData, $jobDescription, false);
            }

            return $coverLetter;
        } catch (\Exception $e) {
            Log::error('Error in OpenAI generateCoverLetter: ' . $e->getMessage());
            
            // Try Deepseek as fallback
            try {
                return $this->deepseekService->generateCoverLetter($resumeData, $jobDescription, $structured);
            } catch (\Exception $e2) {
                Log::error('Both OpenAI and Deepseek failed to generate cover letter: ' . $e2->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Check if resume data is valid
     */
    private function isValidResumeData($data)
    {
        if (!is_array($data)) {
            return false;
        }
        
        $requiredSections = ['general_details', 'work_experience', 'education', 'skills'];
        foreach ($requiredSections as $section) {
            if (!isset($data[$section])) {
                return false;
            }
        }
        
        return true;
    }
}