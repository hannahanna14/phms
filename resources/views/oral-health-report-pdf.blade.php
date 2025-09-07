<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Oral Health Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .school-info {
            flex: 1;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .region {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-details {
            font-size: 12px;
            color: #666;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 3px;
            text-align: left;
            font-size: 9px;
            word-wrap: break-word;
            max-width: 60px;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <svg width="70" height="70" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" fill="#1e3a8a" stroke="#000" stroke-width="2"/>
                <circle cx="50" cy="50" r="35" fill="#ffffff" stroke="#1e3a8a" stroke-width="1"/>
                <text x="50" y="40" text-anchor="middle" font-family="Arial" font-size="8" font-weight="bold" fill="#1e3a8a">NAAWAN</text>
                <text x="50" y="50" text-anchor="middle" font-family="Arial" font-size="6" fill="#1e3a8a">CENTRAL</text>
                <text x="50" y="60" text-anchor="middle" font-family="Arial" font-size="6" fill="#1e3a8a">SCHOOL</text>
            </svg>
        </div>
        <div class="school-info">
            <div class="school-name">NAAWAN CENTRAL SCHOOL</div>
            <div class="region">Region X - Northern Mindanao</div>
            <div class="report-title">Oral Health Report</div>
            <div class="report-details">
                Grade {{ $grade_level }} 
                @if($section) - Section {{ $section }} @endif
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                @if(in_array('name', $fields))
                    <th>Name</th>
                @endif
                @if(in_array('lrn', $fields))
                    <th>LRN</th>
                @endif
                @if(in_array('grade_level', $fields))
                    <th>Grade</th>
                @endif
                @if(in_array('section', $fields))
                    <th>Section</th>
                @endif
                @if(in_array('gender', $fields))
                    <th>Gender</th>
                @endif
                @if(in_array('age', $fields))
                    <th>Age</th>
                @endif
                @if(in_array('birthdate', $fields))
                    <th>Birthdate</th>
                @endif
                @if(in_array('decayed_teeth', $oral_exam_fields))
                    <th>Decayed Teeth</th>
                @endif
                @if(in_array('missing_teeth', $oral_exam_fields))
                    <th>Missing Teeth</th>
                @endif
                @if(in_array('filled_teeth', $oral_exam_fields))
                    <th>Filled Teeth</th>
                @endif
                @if(in_array('total_dmft', $oral_exam_fields))
                    <th>Total DMFT</th>
                @endif
                @if(in_array('sealant', $oral_exam_fields))
                    <th>Sealant</th>
                @endif
                @if(in_array('fluoride_application', $oral_exam_fields))
                    <th>Fluoride Application</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $student)
                <tr>
                    @if(in_array('name', $fields))
                        <td>{{ $student['name'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('lrn', $fields))
                        <td>{{ $student['lrn'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('grade_level', $fields))
                        <td>{{ $student['grade_level'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('section', $fields))
                        <td>{{ $student['section'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('gender', $fields))
                        <td>{{ $student['gender'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('age', $fields))
                        <td>{{ $student['age'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('birthdate', $fields))
                        <td>{{ $student['birthdate'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('decayed_teeth', $oral_exam_fields))
                        <td>{{ $student['oral_health']->decayed_teeth ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('missing_teeth', $oral_exam_fields))
                        <td>{{ $student['oral_health']->missing_teeth ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('filled_teeth', $oral_exam_fields))
                        <td>{{ $student['oral_health']->filled_teeth ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('total_dmft', $oral_exam_fields))
                        <td>{{ $student['oral_health']->total_dmft ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('sealant', $oral_exam_fields))
                        <td>{{ $student['oral_health']->sealant ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('fluoride_application', $oral_exam_fields))
                        <td>{{ $student['oral_health']->fluoride_application ?? 'N/A' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Printed by: Test User</div>
        <div>Date: {{ date('F d, Y') }}</div>
    </div>
</body>
</html>
