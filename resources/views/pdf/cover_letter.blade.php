<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $resume->title }} - Cover Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            line-height: 1.4;
            font-size: 10.5pt;
        }
        .header {
            text-align: right;
            margin-bottom: 15px;
        }
        .date {
            margin-bottom: 10px;
        }
        .company-info {
            margin-bottom: 10px;
        }
        .subject {
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .content p {
            margin: 8px 0;
        }
        .signature {
            margin-top: 15px;
        }
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($resume->cover_letter['applicant']['name']))
            <div>{{ $resume->cover_letter['applicant']['name'] }}</div>
        @endif
        @if(!empty($resume->cover_letter['applicant']['address']))
            <div>{{ $resume->cover_letter['applicant']['address'] }}</div>
        @endif
        @if(!empty($resume->cover_letter['applicant']['email']))
            <div>{{ $resume->cover_letter['applicant']['email'] }}</div>
        @endif
        @if(!empty($resume->cover_letter['applicant']['phone']))
            <div>{{ $resume->cover_letter['applicant']['phone'] }}</div>
        @endif
    </div>

    <div class="date">
        {{ $resume->cover_letter['date'] }}
    </div>

    <div class="company-info">
        @if(!empty($resume->cover_letter['company']['name']))
            <div>{{ $resume->cover_letter['company']['name'] }}</div>
        @endif
        @if(!empty($resume->cover_letter['company']['address']))
            <div>{{ $resume->cover_letter['company']['address'] }}</div>
        @endif
    </div>

    <div class="subject">
        {{ $resume->cover_letter['subject'] }}
    </div>

    <div class="content">
        @if(!empty($resume->cover_letter['greeting']))
            <p>{{ $resume->cover_letter['greeting'] }}</p>
        @endif
        @if(!empty($resume->cover_letter['introduction']))
            <p>{{ $resume->cover_letter['introduction'] }}</p>
        @endif
        @if(!empty($resume->cover_letter['background']))
            <p>{{ $resume->cover_letter['background'] }}</p>
        @endif
        @if(!empty($resume->cover_letter['skillsAlignment']))
            <p>{{ $resume->cover_letter['skillsAlignment'] }}</p>
        @endif
        @if(!empty($resume->cover_letter['closing']))
            <p>{{ $resume->cover_letter['closing'] }}</p>
        @endif
    </div>

    <div class="signature">
        <p>Best regards,<br>
        {{ $resume->cover_letter['applicant']['name'] ?? 'Applicant' }}</p>
    </div>
</body>
</html>

