<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl;
    protected $maxRetries = 3;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    private function validateTailoredResumeStructure($data)
    {
        if (!isset($data['general_details']) || !is_array($data['general_details'])) {
            return false;
        }

        $requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data['general_details'])) {
                Log::error("Missing required field in general_details: $field");
                return false;
            }
        }
        
        return true;
    }

    /**
     * Extract all resume details from PDF content in a single call
     * 
     * @param string $pdfContent
     * @return array
     */
    public function extractAllDetailsFromPdf($pdfContent)
    {
        $prompt = $this->buildExtractAllDetailsPrompt($pdfContent);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert resume parser. Extract structured information from resume text and return it as valid JSON. Your response must be a complete, properly formatted JSON object without any additional text. Generate truthful content based ONLY on what is provided in the resume text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.1,
                'max_tokens' => 4000,
                'response_format' => ['type' => 'json_object']
            ]);
            
            $data = $response->json();
            
            if (isset($data['choices'][0]['message']['content'])) {
                $content = $data['choices'][0]['message']['content'];
                return $this->parseJsonResponse($content);
            }
            
            Log::error('Failed to extract resume details from OpenAI: Invalid response format', ['response' => $data]);
            return [
                'general_details' => [],
                'work_experience' => [],
                'education' => [],
                'skills' => ['items' => []],
                'professional_qualifications' => ['items' => []],
                'referees' => ['text' => '']
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to extract resume details from OpenAI: ' . $e->getMessage());
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
    
    /**
     * Regenerate a specific section of the resume
     * 
     * @param string $pdfContent
     * @param string $section
     * @param array $currentData
     * @return array
     */
    public function regenerateSection($pdfContent, $section, $currentData = [], $jobDescription = null)
    {
        $prompt = $this->buildRegenerateSectionPrompt($pdfContent, $section, $currentData, $jobDescription);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert resume enhancer. Your task is to regenerate and improve the specified section of a resume while strictly adhering to the provided resume text and job description.

                        **Guidelines:**
                        - **Use only verifiable information:** You must not create or infer details that are not explicitly mentioned in the resume.
                        - **Rephrase and enhance:** Improve clarity, conciseness, and impact without altering factual accuracy.
                        - **Strictly no assumptions or fabrication:** If a detail is missing, do not add or guess information. Instead, return an empty string "".
                        - **Return JSON format only:** Your response must be a complete, properly formatted JSON object, without any additional text.

                        If you cannot generate truthful content based on the given resume text and job description, return an empty string "".'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0,
                'max_tokens' => 2000,
                'response_format' => ['type' => 'json_object']
            ]);
            
            $data = $response->json();
            
            if (isset($data['choices'][0]['message']['content'])) {
                $content = $data['choices'][0]['message']['content'];
                return $this->parseJsonResponse($content);
            }
            
            Log::error('Failed to regenerate section with OpenAI: Invalid response format', ['response' => $data]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Failed to regenerate section with OpenAI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build prompt for extracting all resume details
     * 
     * @param string $pdfContent
     * @return string
     */
    private function buildExtractAllDetailsPrompt($pdfContent)
{
    return <<<EOT
Extract all details from the following resume text and return them in a structured JSON format.
Include the following sections:
1. general_details (fullName, email, phone, location, summary)
2. work_experience (array of jobs with title, company, startDate, endDate, current, description)
3. education (array of education with institution, degree, startDate, endDate, current, description). The entire education section should be JSON-encoded as a string.
4. skills (object with items array)
5. professional_qualifications (object with items array of qualifications with name, issuer, date, expirationDate, current)
6. referees (object with text property)

Format dates as YYYY-MM-DD where possible.
If a section is not found, return an empty array or appropriate default.
Return ONLY valid JSON without any explanations.

Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
Generate truthful content based ONLY on what's provided in the resume text.

Example of expected output format:
{
  "general_details": {
    "fullName": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1 (555) 123-4567",
    "location": "San Francisco, CA",
    "summary": "Experienced software engineer with 5+ years of experience in web development."
  },
  "work_experience": [
    {
      "title": "Senior Software Engineer",
      "company": "Tech Company",
      "location": "San Francisco, CA",
      "startDate": "2020-01-01",
      "endDate": "",
      "current": true,
      "description": "Led development of cloud-based applications."
    }
  ],
  "education": "{\\"education\\":[{\\"degree\\":\\"Bachelor of Science in Computer Science\\",\\"institution\\":\\"University of California\\",\\"startDate\\":\\"2012-09-01\\",\\"endDate\\":\\"2016-05-30\\",\\"description\\":\\"Graduated with honors.\\"},{\\"degree\\":\\"High School Diploma\\",\\"institution\\":\\"XYZ High School\\",\\"startDate\\":\\"2008-01-01\\",\\"endDate\\":\\"2012-01-01\\",\\"description\\":\\"Acquired comprehensive knowledge in computer science principles, algorithms, and software development, laying a strong foundation for a career in technology.\\"}]}"
}

Resume text:
$pdfContent
EOT;
}
    
    /**
     * Build prompt for regenerating a specific section
     * 
     * @param string $pdfContent
     * @param string $section
     * @param array $currentData
     * @param string $jobDescription
     * @return string
     */
    private function buildRegenerateSectionPrompt($pdfContent, $section, $currentData, $jobDescription = null)
    {
        $currentDataJson = json_encode($currentData);
        
        $additionalInstructions = '';
        $jobContext = '';
        $exampleOutput = '';
        
        // Add job description context if available
        if ($jobDescription) {
            $jobContext = <<<EOT
Consider the following job description when regenerating the section:
$jobDescription

EOT;
        }
        
        // Add section-specific instructions and examples
        if ($section === 'professional_qualifications') {
            $additionalInstructions = <<<EOT
For professional qualifications, focus on formal certifications, licenses, and credentials that are relevant to the candidate's field.
Each qualification should include:
- name: The official name of the certification or qualification (e.g., "AWS Certified Solutions Architect", "PMP Certification")
- issuer: The organization that issued the certification (e.g., "Amazon Web Services", "Project Management Institute")
- date: When the certification was obtained
- expirationDate: When the certification expires (if applicable)
- current: Whether the certification is currently valid

Do not include general skills, educational degrees, or work experiences as professional qualifications.
Focus only on formal, recognized certifications and credentials.
EOT;

            $exampleOutput = <<<EOT
Example of expected output format:
{
  "professional_qualifications": {
    "items": [
      {
        "name": "AWS Certified Solutions Architect",
        "issuer": "Amazon Web Services",
        "date": "2019-06-15",
        "expirationDate": "2022-06-15",
        "current": false
      },
      {
        "name": "Certified Scrum Master",
        "issuer": "Scrum Alliance",
        "date": "2020-03-10",
        "expirationDate": "2022-03-10",
        "current": true
      }
    ]
  }
}
EOT;
        } elseif ($section === 'skills') {
            $additionalInstructions = <<<EOT
For skills, focus on technical and professional abilities that are relevant to the candidate's field.
Format each skill in sentence case (first letter capitalized).
Include a mix of technical skills, soft skills, and domain-specific knowledge.
If a job description is provided, prioritize skills that match the job requirements.
EOT;

            $exampleOutput = <<<EOT
Example of expected output format:
{
  "skills": {
    "items": [
      "JavaScript",
      "Python",
      "React",
      "Node.js",
      "Cloud Architecture",
      "Team Leadership",
      "Agile Methodologies"
    ]
  }
}
EOT;
        } elseif ($section === 'work_experience') {
            $additionalInstructions = <<<EOT
For work experience, focus on achievements and responsibilities that demonstrate the candidate's value.
Each work experience entry should:
- Highlight measurable achievements with specific metrics where possible
- Use action verbs to begin sentences
- Emphasize skills and experiences relevant to the target job
- Be concise but comprehensive
- Include 3-5 bullet points or sentences per role
EOT;

            $exampleOutput = <<<EOT
Example of expected output format:
{
  "work_experience": [
    {
      "title": "Senior Software Engineer",
      "company": "Tech Company",
      "location": "San Francisco, CA",
      "startDate": "2020-01-01",
      "endDate": "",
      "current": true,
      "description": "Led development of cloud-based applications that served over 10,000 daily users. Implemented performance optimizations that reduced load times by 40%. Mentored junior developers and established coding standards that improved code quality and reduced bugs by 30%."
    },
    {
      "title": "Software Engineer",
      "company": "StartUp Inc.",
      "location": "San Francisco, CA",
      "startDate": "2018-03-15",
      "endDate": "2019-12-31",
      "current": false,
      "description": "Developed front-end components using React and Redux. Created RESTful APIs using Node.js and Express. Collaborated with UX designers to implement responsive designs."
    }
  ]
}
EOT;
        }
        
        return <<<EOT
Regenerate and enhance the "$section" section of this resume.
Make it more professional, impactful, and tailored to highlight the candidate's strengths.

$jobContext
Current $section data:
$currentDataJson

Resume text:
$pdfContent

$additionalInstructions

$exampleOutput

Return ONLY valid JSON for the regenerated $section section without any explanations.
Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
Generate truthful content based ONLY on what's provided in the resume text and current data.
EOT;
    }

    /**
     * Parse JSON response from LLM
     * 
     * @param string $content
     * @return array
     */
    private function parseJsonResponse($content)
    {
        try {
            $json = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try to extract JSON if there's additional text
                preg_match('/\{.*\}/s', $content, $matches);
                
                if (empty($matches)) {
                    Log::error('Failed to extract JSON from OpenAI response', ['content' => $content]);
                    return [];
                }
                
                $json = json_decode($matches[0], true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Failed to parse JSON from OpenAI: ' . json_last_error_msg(), ['content' => $content]);
                    return [];
                }
            }
            
            return $json;
        } catch (\Exception $e) {
            Log::error('Exception parsing JSON from OpenAI: ' . $e->getMessage(), ['content' => $content]);
            return [];
        }
    }

    /**
     * Generate a response from OpenAI API
     */
    public function generateResponse($systemPrompt, $userPrompt, $jsonOutput = false, $retryCount = 0)
    {
        try {
            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.1,
                'max_tokens' => 4000
            ];

            if ($jsonOutput) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->timeout(300)
              ->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                
                if ($jsonOutput && $content) {
                    $jsonData = json_decode($content, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('Invalid JSON in OpenAI response: ' . json_last_error_msg());
                        
                        if ($retryCount < $this->maxRetries) {
                            return $this->generateResponse($systemPrompt, $userPrompt, $jsonOutput, $retryCount + 1);
                        }
                        
                        return null;
                    }
                    
                    return $jsonData;
                }
                
                return $content;
            } else {
                $errorBody = $response->body();
                Log::error('OpenAI API error: ' . $errorBody);
                
                if ($retryCount < $this->maxRetries) { 
                    sleep(pow(2, $retryCount));
                    return $this->generateResponse($systemPrompt, $userPrompt, $jsonOutput, $retryCount + 1);
                }
                
                return null;
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API exception: ' . $e->getMessage());
            
            if ($retryCount < $this->maxRetries) {
                sleep(pow(2, $retryCount));
                return $this->generateResponse($systemPrompt, $userPrompt, $jsonOutput, $retryCount + 1);
            }
            
            return null;
        }
    }

    /**
     * Extract professional qualifications from PDF content
     */
    public function extractProfessionalQualificationsFromPdf($pdfContent)
    {
        $systemPrompt = "You are an expert resume parser specializing in professional qualifications. Extract only the professional qualifications from the provided resume text. You must return complete data in a valid JSON format with the following structure.

        The following fields MUST be included in the response:
        1. professional_qualifications: A list of professional qualifications, each with name, issuer, date, and expiration date.

        Here is the exact JSON structure you MUST follow:
        {
            \"professional_qualifications\": {
                \"items\": [
                    {
                        \"name\": \"\",
                        \"issuer\": \"\",
                        \"date\": \"\",
                        \"expirationDate\": \"\",
                        \"current\": true
                    }
                ]
            }
        }

        Important requirements:
        1. You MUST include ALL fields in your response, even if empty.
        2. If a field cannot be determined, use an empty string as appropriate.
        3. For dates, use YYYY-MM-DD format if possible, or as much of that format as can be determined.
        4. Set 'current' to true if the professional qualification is still valid or has no expiration date.
        5. If no professional qualifications are found, return an empty array for 'items'.
        6. Focus ONLY on formal certifications, licenses, and credentials (e.g., 'AWS Certified Solutions Architect', 'PMP Certification').
        7. Do NOT include general skills, educational degrees, or work experiences as professional qualifications.
        8. Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
        9. Generate truthful content based ONLY on what's provided in the resume text.

        Example of expected output:
        {
            \"professional_qualifications\": {
                \"items\": [
                    {
                        \"name\": \"AWS Certified Solutions Architect\",
                        \"issuer\": \"Amazon Web Services\",
                        \"date\": \"2019-06-15\",
                        \"expirationDate\": \"2022-06-15\",
                        \"current\": false
                    },
                    {
                        \"name\": \"Project Management Professional (PMP)\",
                        \"issuer\": \"Project Management Institute\",
                        \"date\": \"2018-03-10\",
                        \"expirationDate\": \"2021-03-10\",
                        \"current\": true
                    }
                ]
            }
        }";

        $userPrompt = "Here is the text extracted from a resume PDF. Please parse the professional qualifications into structured JSON: " . $pdfContent;

        $response = $this->generateResponse($systemPrompt, $userPrompt, true);

        if ($response && is_array($response)) {
            $isValid = $this->validateProfessionalQualificationsStructure($response);

            if (!$isValid) {
                Log::error('Invalid professional qualifications structure received from OpenAI');
                return $this->createEmptyProfessionalQualificationsStructure();
            }
        } else {
            Log::error('No valid response received from OpenAI for professional qualifications parsing');
            return $this->createEmptyProfessionalQualificationsStructure();
        }

        return $response;
    }

    /**
     * Validate professional qualifications structure
     */
    private function validateProfessionalQualificationsStructure($data)
    {
        if (!isset($data['professional_qualifications']) || !is_array($data['professional_qualifications'])) {
            return false;
        }

        if (!isset($data['professional_qualifications']['items']) || !is_array($data['professional_qualifications']['items'])) {
            return false;
        }

        foreach ($data['professional_qualifications']['items'] as $qual) {
            if (!is_array($qual)) {
                return false;
            }

            $requiredFields = ['name', 'issuer', 'date', 'expirationDate', 'current'];
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $qual)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create empty professional qualifications structure
     */
    private function createEmptyProfessionalQualificationsStructure()
    {
        return [
            'professional_qualifications' => [
                'items' => []
            ]
        ];
    }

    /**
     * Extract resume information from PDF content
     */
    public function extractResumeFromPdf($pdfContent)
    {
        // First call to extract general details
        $generalDetails = $this->extractGeneralDetailsFromPdf($pdfContent);

        // Second call to extract remaining details
        $remainingDetails = $this->extractRemainingDetailsFromPdf($pdfContent);
        
        // Third call to extract professional qualifications
        $professionalQualifications = $this->extractProfessionalQualificationsFromPdf($pdfContent);

        // Combine the results
        $resumeData = array_merge($generalDetails, $remainingDetails, $professionalQualifications);

        // Validate the combined structure
        if (!$this->validateResumeStructure($resumeData)) {
            Log::error('Invalid resume structure after combining all details from OpenAI');
            return $this->createEmptyResumeStructure();
        }

        return $resumeData;
    }

    /**
     * Validate resume data structure
     */
    private function validateResumeStructure($data)
    {
        $requiredSections = ['general_details', 'work_experience', 'education', 'skills', 'interests', 'referees', 'professional_qualifications'];
        foreach ($requiredSections as $section) {
            if (!isset($data[$section])) {
                Log::error('Missing required section in resume data: ' . $section);
                return false;
            }
        }

        $generalDetailsFields = ['fullName', 'email', 'phone', 'location', 'summary'];
        if (!is_array($data['general_details'])) {
            Log::error('general_details is not an array');
            return false;
        }

        foreach ($generalDetailsFields as $field) {
            if (!array_key_exists($field, $data['general_details'])) {
                Log::error('Missing required field in general_details: ' . $field);
                return false;
            }
        }

        if (!is_array($data['work_experience'])) {
            Log::error('work_experience is not an array');
            return false;
        }

        if (!is_array($data['education'])) {
            Log::error('education is not an array');
            return false;
        }

        if (!is_array($data['skills'])) {
            Log::error('skills is not an array');
            return false;
        }

        if (!is_array($data['interests'])) {
            Log::error('interests is not an array');
            return false;
        }

        if (!isset($data['skills']['items']) || !is_array($data['skills']['items'])) {
            Log::error('skills.items is missing or not an array');
            return false;
        }

        if (!isset($data['interests']['items']) || !is_array($data['interests']['items'])) {
            Log::error('interests.items is missing or not an array');
            return false;
        }

        if (!is_array($data['referees'])) {
            Log::error('referees is not an array');
            return false;
        }

        if (!isset($data['referees']['text'])) {
            Log::error('referees.text is missing');
            return false;
        }
        
        if (!is_array($data['professional_qualifications'])) {
            Log::error('professional_qualifications is not an array');
            return false;
        }
        
        if (!isset($data['professional_qualifications']['items']) || !is_array($data['professional_qualifications']['items'])) {
            Log::error('professional_qualifications.items is missing or not an array');
            return false;
        }

        return true;
    }

    /**
     * Create empty resume structure
     */
    private function createEmptyResumeStructure()
    {
        return [
            'general_details' => [
                'fullName' => '',
                'email' => '',
                'phone' => '',
                'location' => '',
                'summary' => ''
            ],
            'work_experience' => [],
            'education' => [],
            'skills' => [
                'items' => []
            ],
            'interests' => [
                'items' => []
            ],
            'professional_qualifications' => [
                'items' => []
            ],
            'referees' => [
                'text' => ''
            ]
        ];
    }

    /**
     * Tailor resume to job description
     */
    public function tailorResumeToJob($resumeData, $jobDescription, $retryCount = 0)
    {
        // Ensure resumeData is an array
        if (is_string($resumeData)) {
            $resumeData = json_decode($resumeData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('tailorResumeToJob - Invalid JSON in resumeData: ' . json_last_error_msg());
                return null;
            }
        }

        // Ensure general_details has all required fields
        if (!isset($resumeData['general_details']) || !is_array($resumeData['general_details'])) {
            $resumeData['general_details'] = [
                'fullName' => '',
                'email' => '',
                'phone' => '',
                'location' => '',
                'summary' => ''
            ];
        } else {
            $requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
            foreach ($requiredFields as $field) {
                if (!isset($resumeData['general_details'][$field])) {
                    $resumeData['general_details'][$field] = '';
                }
            }
        }

        // Ensure work_experience is an array
        if (!isset($resumeData['work_experience']) || !is_array($resumeData['work_experience'])) {
            $resumeData['work_experience'] = [];
        }

        // Ensure education is an array
        if (!isset($resumeData['education']) || !is_array($resumeData['education'])) {
            $resumeData['education'] = [];
        }

        // Add missing sections if needed
        $requiredSections = ['general_details', 'work_experience', 'education', 'skills', 'interests', 'referees', 'professional_qualifications'];
        $emptyStructure = $this->createEmptyResumeStructure();
        
        foreach ($requiredSections as $section) {
            if (!isset($resumeData[$section])) {
                Log::warning('tailorResumeToJob - Adding missing section: ' . $section);
                $resumeData[$section] = $emptyStructure[$section];
            }
        }

        // Ensure skills and interests have 'items' array
        if (!isset($resumeData['skills']['items'])) {
            $resumeData['skills']['items'] = [];
        }
        
        if (!isset($resumeData['interests']['items'])) {
            $resumeData['interests']['items'] = [];
        }
        
        // Ensure professional_qualifications has 'items' array
        if (!isset($resumeData['professional_qualifications']['items'])) {
            $resumeData['professional_qualifications']['items'] = [];
        }
        
        // Ensure referees has 'text' property
        if (!isset($resumeData['referees']['text'])) {
            $resumeData['referees']['text'] = '';
        }

        $systemPrompt = "You are an expert resume tailoring assistant. Your task is to tailor the provided resume to match the job description. Modify the resume data to highlight relevant skills, experiences, and professional qualifications that align with the job requirements. Return the tailored resume in the same JSON structure as the input.

        Important requirements:
        1. Your response MUST be a valid JSON object with the exact same structure as the input.
        2. Focus on tailoring the summary, work experience descriptions, skills, and professional qualifications.
        3. Highlight relevant achievements and experiences in three sentences. Mention the impact achieved in terms of metrics in the last sentence if possible.
        4. Add relevant skills and professional qualifications that the candidate likely has based on their experience.
        5. Do not invent qualifications or experiences that are not implied by the original resume.
        6. Keep the tailoring professional and truthful.
        7. Ensure professional qualifications are prominently featured if they are relevant to the job description.
        8. For professional qualifications, focus on formal certifications, licenses, and credentials that are relevant to the candidate's field.
        9. Generate truthful content based ONLY on what's provided in the resume data and job description.
        10. You MUST maintain ALL fields from the original general_details section including fullName, email, phone, location, and summary.
        11. Never remove or omit any existing fields from general_details.
        12. If enhancing the summary, keep existing contact information intact. 

        Example of expected output format (simplified):
        {
          \"general_details\": {
            \"fullName\": \"John Doe\",
            \"email\": \"john.doe@example.com\",
            \"phone\": \"+1 (555) 123-4567\",
            \"location\": \"San Francisco, CA\",
            \"summary\": \"Dedicated software engineer with 5+ years of experience in web development, specializing in cloud-based applications that align perfectly with the requirements of this Senior Developer role.\"
          },
          \"work_experience\": [...],
          \"education\": [...],
          \"skills\": {\"items\": [...]},
          \"professional_qualifications\": {\"items\": [...]},
          \"referees\": {\"text\": \"...\"}
        }";

        $userPrompt = "Resume Data:\n" . json_encode($resumeData, JSON_PRETTY_PRINT) . "\n\nJob Description:\n" . $jobDescription;

        $tailoredResume = $this->generateResponse($systemPrompt, $userPrompt, true);
        
        // Log the response structure
        if ($tailoredResume) { 
            
            // Validate the structure of the tailored resume
            if (!$this->validateResumeStructure($tailoredResume)) {
                Log::error('tailorResumeToJob - Invalid tailored resume structure');
                
                if ($retryCount < 1) { // Only retry once
                    return $this->tailorResumeToJob($resumeData, $jobDescription, $retryCount + 1);
                }
                
                Log::error('tailorResumeToJob - Tailored resume data: ' . json_encode($tailoredResume));
                return null;
            }

            if (!$this->validateTailoredResumeStructure($tailoredResume)) {
                Log::error('Invalid tailored resume structure');
                return null;
            } 
            
        } else {
            Log::error('tailorResumeToJob - Failed to get tailored resume from OpenAI');
            
            if ($retryCount < 1) { // Only retry once
                return $this->tailorResumeToJob($resumeData, $jobDescription, $retryCount + 1);
            }
            
            return null;
        }
        
        return $tailoredResume;
    }

    /**
     * Generate a suitable resume title from job description and resume data
     */
    public function generateResumeTitle($resumeData, $jobDescription, $retryCount = 0)
    {

        // Ensure resumeData is an array
        if (is_string($resumeData)) {
            $resumeData = json_decode($resumeData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('generateResumeTitle - Invalid JSON in resumeData: ' . json_last_error_msg());
                return null;
            }
        }

        $systemPrompt = "You are an expert resume title generator. Your task is to create a short professional, concise title for a resume based on the job description and the candidate's experience. The title should be specific to the job being applied for and highlight the candidate's qualifications.

        Important requirements:
        1. The title should be no more than 20 characters.
        2. Do not include the candidate's name in the title.
        3. Focus on the job position and relevant qualifications.
        4. Return ONLY the title text, with no additional explanation or formatting.
        5. Be specific about the role (e.g., 'Senior Java Developer' rather than just 'Developer').
        6. Generate truthful content based ONLY on what's provided in the resume data and job description.

        Examples of good titles:
        - 'Sr. Java Developer'
        - 'Marketing Director'
        - 'UX/UI Designer'
        - 'Data Scientist'
        - 'Project Manager'";

        $userPrompt = "Resume Data:\n" . json_encode($resumeData, JSON_PRETTY_PRINT) . "\n\nJob Description:\n" . $jobDescription;

        $title = $this->generateResponse($systemPrompt, $userPrompt);
        
        // Clean up the title if needed
        if ($title) {
            $title = trim($title);
            // Remove any quotes that might be in the response
            $title = str_replace(['"', "'"], '', $title);
            // Limit to 60 characters
            if (strlen($title) > 60) {
                $title = substr($title, 0, 57) . '...';
            }
            
        } else {
            Log::error('generateResumeTitle - Failed to generate title');
            
            if ($retryCount < 1) { // Only retry once

                return $this->generateResumeTitle($resumeData, $jobDescription, $retryCount + 1);
            }
            
            return "Professional Resume";
        }
        
        return $title;
    }

    /**
     * Extract general details from PDF content
     */
    public function extractGeneralDetailsFromPdf($pdfContent)
    {
        $systemPrompt = "You are an expert resume parser. Extract only the general details from the provided resume text. You must return complete data in a valid JSON format with the following structure.

        The following fields MUST be included in the response:
        1. general_details: Contains the candidate's full name, email, phone, location, and summary.

        Here is the exact JSON structure you MUST follow:
        {
            \"general_details\": {
                \"fullName\": \"\",
                \"email\": \"\",
                \"phone\": \"\",
                \"location\": \"\",
                \"summary\": \"\"
            }
        }

        Important requirements:
        1. You MUST include ALL fields in your response, even if empty.
        2. If a field cannot be determined, use an empty string as appropriate.
        3. Do not omit any fields from the JSON structure.
        4. Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
        5. Generate truthful content based ONLY on what's provided in the resume text.

        Example of expected output:
        {
            \"general_details\": {
                \"fullName\": \"John Doe\",
                \"email\": \"john.doe@example.com\",
                \"phone\": \"+1 (555) 123-4567\",
                \"location\": \"San Francisco, CA\",
                \"summary\": \"Experienced software engineer with 5+ years of experience in web development, specializing in JavaScript frameworks and cloud technologies.\"
            }
        }";

        $userPrompt = "Here is the text extracted from a resume PDF. Please parse the general details into structured JSON: " . $pdfContent;

        $response = $this->generateResponse($systemPrompt, $userPrompt, true);

        if ($response && is_array($response)) {
            $isValid = $this->validateGeneralDetailsStructure($response);

            if (!$isValid) {
                Log::error('Invalid general details structure received from OpenAI');
                return $this->createEmptyGeneralDetailsStructure();
            }
        } else {
            Log::error('No valid response received from OpenAI for general details parsing');
            return $this->createEmptyGeneralDetailsStructure();
        }

        return $response;
    }

    /**
     * Extract remaining details from PDF content
     */
    public function extractRemainingDetailsFromPdf($pdfContent)
    {
        $systemPrompt = "You are an expert resume parser. Extract structured information from the provided resume text, excluding general details. You must return complete data in a valid JSON format with the following structure.

        The following fields MUST be included in the response:
        1. work_experience: A list of work experiences, each with title, company, location, start date, end date, current status, and description.
        2. education: A list of educational qualifications, each with institution, degree, start date, end date, current status, and description.
        3. skills: A list of skills in sentence case.
        4. interests: A list of interests in sentence case.
        5. referees: A text field for referee information.

        Here is the exact JSON structure you MUST follow:
        {
            \"work_experience\": [
                {
                    \"title\": \"\",
                    \"company\": \"\",
                    \"location\": \"\",
                    \"startDate\": \"\",
                    \"endDate\": \"\",
                    \"current\": false,
                    \"description\": \"\"
                }
            ],
            \"education\": [
                {
                    \"institution\": \"\",
                    \"degree\": \"\",
                    \"startDate\": \"\",
                    \"endDate\": \"\",
                    \"current\": false,
                    \"description\": \"\"
                }
            ],
            \"skills\": {
                \"items\": []
            },
            \"interests\": {
                \"items\": []
            },
            \"referees\": {
                \"text\": \"\"
            }
        }

        Important requirements:
        1. You MUST include ALL fields in your response, even if empty.
        2. Format each skill in the skills.items array in sentence case.
        3. If a field cannot be determined, use an empty string or empty array as appropriate.
        4. For dates, use YYYY-MM-DD format if possible, or as much of that format as can be determined.
        5. Do not omit any sections or fields from the JSON structure.
        6. Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
        7. Generate truthful content based ONLY on what's provided in the resume text.

        Example of expected output:
        {
            \"work_experience\": [
                {
                    \"title\": \"Senior Software Engineer\",
                    \"company\": \"Tech Company\",
                    \"location\": \"San Francisco, CA\",
                    \"startDate\": \"2020-01-01\",
                    \"endDate\": \"\",
                    \"current\": true,
                    \"description\": \"Led development of cloud-based applications.\"
                }
            ],
            \"education\": [
                {
                    \"institution\": \"University of California\",
                    \"degree\": \"Bachelor of Science in Computer Science\",
                    \"startDate\": \"2012-09-01\",
                    \"endDate\": \"2016-05-30\",
                    \"current\": false,
                    \"description\": \"Graduated with honors.\"
                }
            ],
            \"skills\": {
                \"items\": [\"JavaScript\", \"Python\", \"React\", \"Node.js\"]
            },
            \"interests\": {
                \"items\": [\"Open Source Development\", \"Hiking\", \"Photography\"]
            },
            \"referees\": {
                \"text\": \"Available upon request.\"
            }
        }";

        $userPrompt = "Here is the text extracted from a resume PDF. Please parse the remaining details into structured JSON: " . $pdfContent;

        $response = $this->generateResponse($systemPrompt, $userPrompt, true);

        if ($response && is_array($response)) {
            $isValid = $this->validateRemainingDetailsStructure($response);

            if (!$isValid) {
                Log::error('Invalid remaining details structure received from OpenAI');
                return $this->createEmptyRemainingDetailsStructure();
            }
        } else {
            Log::error('No valid response received from OpenAI for remaining details parsing');
            return $this->createEmptyRemainingDetailsStructure();
        }

        return $response;
    }

    /**
     * Validate general details structure
     */
    private function validateGeneralDetailsStructure($data)
    {
        $requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
        if (!isset($data['general_details']) || !is_array($data['general_details'])) {
            return false;
        }

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data['general_details'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create empty general details structure
     */
    private function createEmptyGeneralDetailsStructure()
    {
        return [
            'general_details' => [
                'fullName' => '',
                'email' => '',
                'phone' => '',
                'location' => '',
                'summary' => ''
            ]
        ];
    }

    /**
     * Validate remaining details structure
     */
    private function validateRemainingDetailsStructure($data)
    {
        $requiredSections = ['work_experience', 'education', 'skills', 'interests', 'referees'];
        foreach ($requiredSections as $section) {
            if (!isset($data[$section])) {
                return false;
            }
        }

        if (!is_array($data['work_experience']) || !is_array($data['education'])) {
            return false;
        }

        if (!is_array($data['skills']) || !is_array($data['interests'])) {
            return false;
        }

        if (!isset($data['skills']['items']) || !is_array($data['skills']['items']) ||
            !isset($data['interests']['items']) || !is_array($data['interests']['items'])) {
            return false;
        }

        if (!is_array($data['referees'])) {
            return false;
        }

        if (!isset($data['referees']['text'])) {
            return false;
        }

        return true;
    }

    /**
     * Create empty remaining details structure
     */
    private function createEmptyRemainingDetailsStructure()
    {
        return [
            'work_experience' => [],
            'education' => [],
            'skills' => [
                'items' => []
            ],
            'interests' => [
                'items' => []
            ],
            'referees' => [
                'text' => ''
            ]
        ];
    }

    /**
     * Generate a cover letter based on resume and job description
     */
    public function generateCoverLetter($resumeData, $jobDescription, $structured = false, $retryCount = 0)
    {
        // Ensure resumeData is an array
        if (is_string($resumeData)) {

            $resumeData = json_decode($resumeData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('generateCoverLetter - Invalid JSON in resumeData: ' . json_last_error_msg());
                return null;
            }
        }

        // Get location from general details or latest work experience
        $location = '';
        if (isset($resumeData['general_details']['location']) && !empty($resumeData['general_details']['location'])) {
            $location = $resumeData['general_details']['location'];
        } else if (isset($resumeData['work_experience']) && is_array($resumeData['work_experience']) && !empty($resumeData['work_experience'])) {
            // Sort work experience by start date (most recent first)
            usort($resumeData['work_experience'], function($a, $b) {
                return strtotime($b['startDate'] ?? '0') - strtotime($a['startDate'] ?? '0');
            });
            
            // Get location from the most recent work experience
            if (isset($resumeData['work_experience'][0]['location']) && !empty($resumeData['work_experience'][0]['location'])) {
                $location = $resumeData['work_experience'][0]['location'];
                
                // Update general details with the location if it's missing
                if (empty($resumeData['general_details']['location'])) {
                    $resumeData['general_details']['location'] = $location;
                }
            }
        }

        if ($structured) {
            $systemPrompt = "You are an expert cover letter writer. Return a JSON formatted response with a professional cover letter using this exact structure:

{
  \"applicant\": {
    \"name\": \"Full Name\",
    \"location\": \"City, Country\",
    \"email\": \"email@domain.com\",
    \"phone\": \"+XXX XXX XXX XXX\"
  },
  \"date\": \"Month Day, Year\",
  \"company\": {
    \"name\": \"Company Name\",
    \"location\": \"City, Country\"
  },
  \"subject\": \"Application for Position Name\",
  \"greeting\": \"Dear Hiring Manager,\",
  \"introduction\": \"Opening paragraph stating interest and position...\",
  \"background\": \"Paragraph about relevant experience and achievements...\",
  \"skillsAlignment\": \"Paragraph connecting skills to company needs...\",
  \"closing\": \"Final paragraph expressing interest in discussion...\"
}

Rules:
1. You MUST include ALL fields in the JSON response, even if some information is not provided in the resume.
2. Right-align applicant contact information
3. Include specific metrics and achievements
4. Use formal business letter format
5. Omit missing information completely (use empty strings)
6. Keep paragraphs under 5 lines
7. Never use markdown or special characters
8. ALWAYS include the applicant's location from the resume data or most recent work experience
9. Highlight any relevant professional qualifications in the background or skillsAlignment sections
10. Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
11. Generate truthful content based ONLY on what's provided in the resume data and job description.

Example of expected output:
{
  \"applicant\": {
    \"name\": \"John Doe\",
    \"location\": \"San Francisco, CA\",
    \"email\": \"john.doe@example.com\",
    \"phone\": \"+1 (555) 123-4567\"
  },
  \"date\": \"March 12, 2025\",
  \"company\": {
    \"name\": \"Tech Innovations Inc.\",
    \"location\": \"San Francisco, CA\"
  },
  \"subject\": \"Application for Senior Software Engineer Position\",
  \"greeting\": \"Dear Hiring Manager,\",
  \"introduction\": \"I am writing to express my interest in the Senior Software Engineer position at Tech Innovations Inc. With over 5 years of experience in developing cloud-based applications and a strong background in JavaScript and Python, I am confident in my ability to contribute to your team's success.\",
  \"background\": \"Throughout my career at Tech Company, I have led the development of several successful cloud applications that served thousands of users daily. I implemented efficient coding practices that reduced load times by 40% and improved user retention by 25%. My AWS Certified Solutions Architect qualification has equipped me with the expertise to design scalable and secure cloud infrastructure.\",
  \"skillsAlignment\": \"Your job posting emphasizes the need for experience with React and Node.js, which align perfectly with my technical skill set. I have successfully delivered projects using these technologies, resulting in robust applications with excellent user experiences. Additionally, my experience with agile methodologies would enable me to seamlessly integrate with your development processes.\",
  \"closing\": \"I am excited about the opportunity to bring my technical expertise and passion for innovation to Tech Innovations Inc. I would welcome the chance to discuss how my background and skills would be an ideal fit for your team. Thank you for considering my application.\"
}";

            $userPrompt = "Resume Data:\n".json_encode($resumeData, JSON_PRETTY_PRINT)."\n\nJob Description:\n".$jobDescription;

            $response = $this->generateResponse($systemPrompt, $userPrompt, true);
            
            if ($response && is_array($response)) {
                if (!$this->validateCoverLetterStructure($response)) {
                    Log::error('generateCoverLetter - Invalid cover letter structure received from OpenAI');
                    
                    if ($retryCount < 1) { // Only retry once
                        return $this->generateCoverLetter($resumeData, $jobDescription, $structured, $retryCount + 1);
                    }
                    
                    return $this->createEmptyCoverLetterStructure($resumeData);
                }
                
                // Ensure location is set from resume data if missing
                if (empty($response['applicant']['location']) && !empty($location)) {
                    $response['applicant']['location'] = $location;
                }

            } else {
                Log::error('generateCoverLetter - No valid response received from OpenAI for cover letter generation');
                
                if ($retryCount < 1) { // Only retry once

                    return $this->generateCoverLetter($resumeData, $jobDescription, $structured, $retryCount + 1);
                }
                
                return $this->createEmptyCoverLetterStructure($resumeData);
            }
            
            return $response;
        } else {
            $systemPrompt = "You are an expert cover letter writer. Create a professional, compelling cover letter that highlights how the candidate's experience, skills, and professional qualifications make them an excellent fit for the job. The cover letter should be personalized, concise, and persuasive. Your response should be a complete cover letter, ready to be sent. Do not use asterisks (**) for emphasis.

            Important requirements:
            1. Generate truthful content based ONLY on what's provided in the resume data and job description.
            2. Include specific metrics and achievements from the resume.
            3. Highlight relevant professional qualifications.
            4. Keep paragraphs concise and focused.
            5. Use formal business letter format.
            6. Never use markdown or special characters.

            Example of a good cover letter structure:
            [Applicant's Contact Information]
            [Date]
            [Employer's Contact Information]
            
            Dear [Hiring Manager's Name or 'Hiring Manager'],
            
            [Introduction: Express interest in the position and briefly mention your background]
            
            [Body Paragraph 1: Highlight relevant experience and achievements]
            
            [Body Paragraph 2: Connect your skills to the job requirements]
            
            [Closing: Express interest in an interview and thank them]
            
            Sincerely,
            [Your Name]";

            $userPrompt = "Here is the resume data:\n" . json_encode($resumeData, JSON_PRETTY_PRINT) . 
                          "\n\nHere is the job description:\n" . $jobDescription . 
                          "\n\nPlease write a tailored cover letter that showcases how this candidate is the perfect fit for the role. Do not use asterisks or other markdown formatting.";

            $coverLetter = $this->generateResponse($systemPrompt, $userPrompt);
            
            if ($coverLetter) {

            } else {
                Log::error('generateCoverLetter - Failed to generate plain text cover letter');
                
                if ($retryCount < 1) { // Only retry once
                    return $this->generateCoverLetter($resumeData, $jobDescription, $structured, $retryCount + 1);
                }
            }
            
            return $coverLetter;
        }
    }

    /**
     * Parse a cover letter into structured format
     */
    public function parseCoverLetter($coverLetterText)
    {
        $systemPrompt = "You are an expert cover letter parser. Parse the provided cover letter text into a structured JSON format with the following fields:

{
  \"applicant\": {
    \"name\": \"Full Name\",
    \"location\": \"City, Country\",
    \"email\": \"email@domain.com\",
    \"phone\": \"+XXX XXX XXX XXX\"
  },
  \"date\": \"Month Day, Year\",
  \"company\": {
    \"name\": \"Company Name\",
    \"location\": \"City, Country\"
  },
  \"subject\": \"Application for Position Name\",
  \"greeting\": \"Dear Hiring Manager,\",
  \"introduction\": \"Opening paragraph stating interest and position...\",
  \"background\": \"Paragraph about relevant experience and achievements...\",
  \"skillsAlignment\": \"Paragraph connecting skills to company needs...\",
  \"closing\": \"Final paragraph expressing interest in discussion...\"
}

Rules:
1. You MUST include ALL fields in the JSON response, even if some information is not provided in the cover letter.
2. If information is not available, use empty strings.
3. Extract as much information as possible from the text.
4. Preserve the original wording for paragraphs.
5. Your response MUST be a valid JSON object. Do not include any text before or after the JSON.
6. Generate truthful content based ONLY on what's provided in the cover letter text.

Example of expected output:
{
  \"applicant\": {
    \"name\": \"John Doe\",
    \"location\": \"San Francisco, CA\",
    \"email\": \"john.doe@example.com\",
    \"phone\": \"+1 (555) 123-4567\"
  },
  \"date\": \"March 12, 2025\",
  \"company\": {
    \"name\": \"Tech Innovations Inc.\",
    \"location\": \"San Francisco, CA\"
  },
  \"subject\": \"Application for Senior Software Engineer Position\",
  \"greeting\": \"Dear Hiring Manager,\",
  \"introduction\": \"I am writing to express my interest in the Senior Software Engineer position at Tech Innovations Inc.\",
  \"background\": \"Throughout my career at Tech Company, I have led the development of several successful cloud applications.\",
  \"skillsAlignment\": \"Your job posting emphasizes the need for experience with React and Node.js, which align perfectly with my technical skill set.\",
  \"closing\": \"I am excited about the opportunity to bring my technical expertise and passion for innovation to Tech Innovations Inc.\"
}";

        $userPrompt = "Here is the cover letter text to parse:\n\n" . $coverLetterText;

        $response = $this->generateResponse($systemPrompt, $userPrompt, true);
        
        if ($response && is_array($response)) {
            if (!$this->validateCoverLetterStructure($response)) {
                Log::error('parseCoverLetter - Invalid cover letter structure received from OpenAI');
                return null;
            }
            
        } else {
            Log::error('parseCoverLetter - Failed to parse cover letter');
        }
        
        return $response;
    }

    /**
     * Validate cover letter structure
     */
    private function validateCoverLetterStructure($data)
    {
        $requiredSections = ['applicant', 'date', 'company', 'subject', 'greeting', 'introduction', 'background', 'skillsAlignment', 'closing'];
        foreach ($requiredSections as $section) {
            if (!isset($data[$section])) {
                return false;
            }
        }
        
        $applicantFields = ['name', 'location', 'email', 'phone'];
        foreach ($applicantFields as $field) {
            if (!array_key_exists($field, $data['applicant'])) {
                return false;
            }
        }
        
        $companyFields = ['name', 'location'];
        foreach ($companyFields as $field) {
            if (!array_key_exists($field, $data['company'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Create empty cover letter structure based on resume data
     */
    private function createEmptyCoverLetterStructure($resumeData)
    {
        $name = '';
        $location = '';
        $email = '';
        $phone = '';
        
        if (isset($resumeData['general_details'])) {
            $name = $resumeData['general_details']['fullName'] ?? '';
            $location = $resumeData['general_details']['location'] ?? '';
            $email = $resumeData['general_details']['email'] ?? '';
            $phone = $resumeData['general_details']['phone'] ?? '';
        }
        
        // If location is still empty, try to get it from the most recent work experience
        if (empty($location) && isset($resumeData['work_experience']) && is_array($resumeData['work_experience']) && !empty($resumeData['work_experience'])) { 
            
            usort($resumeData['work_experience'], function($a, $b) {
                return strtotime($b['startDate'] ?? '0') - strtotime($a['startDate'] ?? '0');
            });
             
            if (isset($resumeData['work_experience'][0]['location']) && !empty($resumeData['work_experience'][0]['location'])) {
                $location = $resumeData['work_experience'][0]['location'];
            }
        }
        
        return [
            'applicant' => [
                'name' => $name,
                'location' => $location,
                'email' => $email,
                'phone' => $phone
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
            'closing' => 'I look forward to discussing my application further. Thank you for your consideration.'
        ];
    }
}