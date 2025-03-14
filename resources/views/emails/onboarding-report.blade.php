<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Onboarding Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2f855a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        h2 {
            color: #2d3748;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .summary {
            background-color: #f0fff4;
            border-left: 4px solid #2f855a;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Daily Onboarding Report - {{ $date }}</h1>
    
    <div class="summary">
        <p><strong>Total new onboarding responses:</strong> {{ $totalResponses }}</p>
    </div>
    
    <h2>Objective Statistics</h2>
    <table>
        <thead>
            <tr>
                <th>Objective</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($objectiveCounts as $objective => $count)
            <tr>
                <td>{{ $objective }}</td>
                <td>{{ $count }}</td>
                <td>{{ round(($count / $totalResponses) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Challenge Statistics</h2>
    <table>
        <thead>
            <tr>
                <th>Challenge</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($challengeCounts as $challenge => $count)
            <tr>
                <td>{{ $challenge }}</td>
                <td>{{ $count }}</td>
                <td>{{ round(($count / $totalResponses) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Referral Source Statistics</h2>
    <table>
        <thead>
            <tr>
                <th>Referral Source</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($referralSourceCounts as $source => $count)
            <tr>
                <td>{{ $source }}</td>
                <td>{{ $count }}</td>
                <td>{{ round(($count / $totalResponses) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Detailed Responses</h2>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Objective</th>
                <th>Challenge</th>
                <th>Referral Source</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($responses as $response)
            <tr>
                <td>{{ $response->user->name }} ({{ $response->user->email }})</td>
                <td>{{ $response->objective }}</td>
                <td>{{ $response->challenge }}</td>
                <td>{{ $response->referral_source }}</td>
                <td>{{ $response->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>This report was automatically generated.</p>
</body>
</html>