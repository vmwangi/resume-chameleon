<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $resume->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
            line-height: 1.3;
            font-size: 10pt;
        }
        h1 {
            font-size: 16pt;
            margin-bottom: 5px;
            text-align: center;
        }
        h2 {
            font-size: 12pt;
            margin-top: 10px;
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
        }
        .contact-info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .section {
            margin-bottom: 10px;
        }
        .experience-item, .education-item, .qualification-item {
            margin-bottom: 8px;
        }
        .job-title, .degree, .qual-name {
            font-weight: bold;
        }
        .company, .institution, .qual-issuer {
            font-style: italic;
        }
        .date {
            color: #666;
            font-size: 9pt;
        }
        .skills-list {
            margin-top: 5px;
        }
        .qualification-item {
            margin-bottom: 5px;
            line-height: 1.4;
        }
        .qualifications-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 5px;
        }
        .qual-item {
            background-color: #f5f5f5;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <h1>{{ $resume->general_details['fullName'] ?? 'Resume' }}</h1>

    <div class="contact-info">
        @if(!empty($resume->general_details['email']) || !empty($resume->general_details['phone']) || !empty($resume->general_details['location']))
            @if(!empty($resume->general_details['email']))
                {{ $resume->general_details['email'] }} |
            @endif
            @if(!empty($resume->general_details['phone']))
                {{ $resume->general_details['phone'] }} |
            @endif
            @if(!empty($resume->general_details['location']))
                {{ $resume->general_details['location'] }}
            @endif
        @endif
    </div>

    @if(!empty($resume->general_details['summary']))
        <div class="section">
            <h2>Professional Summary</h2>
            <p>{{ $resume->general_details['summary'] }}</p>
        </div>
    @endif

    @if(!empty($resume->work_experience))
        <div class="section">
            <h2>Work Experience</h2>
            @foreach($resume->work_experience as $job)
                <div class="experience-item">
                    <div class="job-title">{{ $job['title'] ?? 'Untitled Position' }}</div>
                    <div class="company">{{ $job['company'] ?? 'Unknown Company' }}</div>
                    <div class="date">
                        @if(!empty($job['startDate']))
                            {{ date('M Y', strtotime($job['startDate'])) }} -
                            @if(!empty($job['current']) && $job['current'])
                                Present
                            @elseif(!empty($job['endDate']))
                                {{ date('M Y', strtotime($job['endDate'])) }}
                            @else
                                Present
                            @endif
                        @endif
                    </div>
                    <p>{{ $job['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    @endif

    @if(is_array($resume->education) && count($resume->education) > 0)
        <div class="section">
            <h2>Education</h2>
            @foreach($resume->education as $education)
                <div class="education-item">
                    <div class="degree">{{ $education['degree'] ?? 'Unknown Degree' }}</div>
                    <div class="institution">{{ $education['institution'] ?? 'Unknown Institution' }}</div>
                    @if(isset($education['startDate']) || isset($education['endDate']))
                        <div class="date">
                            @if(isset($education['startDate']))
                                {{ date('M Y', strtotime($education['startDate'])) }}
                            @endif
                            -
                            @if(isset($education['endDate']))
                                {{ date('M Y', strtotime($education['endDate'])) }}
                            @else
                                Present
                            @endif
                        </div>
                    @endif
                    @if(!empty($education['description']))
                        <p>{{ $education['description'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @php
    // Process professional qualifications to ensure we have the correct structure
    $qualifications = [];
    if (!empty($resume->professional_qualifications)) {
        if (is_array($resume->professional_qualifications)) {
            // Handle nested structure
            if (isset($resume->professional_qualifications['items'])) {
                $qualifications = $resume->professional_qualifications['items'];
            } elseif (isset($resume->professional_qualifications['professional_qualifications']['items'])) {
                $qualifications = $resume->professional_qualifications['professional_qualifications']['items'];
            } elseif (is_array($resume->professional_qualifications)) {
                $qualifications = $resume->professional_qualifications;
            }
        }
    }
    @endphp

    @if(!empty($qualifications))
        <div class="section">
            <h2>Professional Qualifications</h2>
            <div class="qualifications-list">
                @foreach($qualifications as $qual)
                    @if(is_array($qual) && !empty($qual['name']))
                        <span class="qual-item">{{ $qual['name'] }}</span>
                    @elseif(is_string($qual))
                        <span class="qual-item">{{ $qual }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($resume->skills['items']))
        <div class="section">
            <h2>Skills</h2>
            <div class="skills-list">
                {{ implode(', ', $resume->skills['items']) }}
            </div>
        </div>
    @endif

    @if(!empty($resume->referees['text']))
        <div class="section">
            <h2>References</h2>
            <div class="references">
                {{ $resume->referees['text'] }}
            </div>
        </div>
    @endif
</body>
</html>