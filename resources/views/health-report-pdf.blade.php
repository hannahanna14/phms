<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Health Report</title>
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
            <div class="report-title">Health Report</div>
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
                @if(in_array('height', $health_exam_fields))
                    <th>Height</th>
                @endif
                @if(in_array('weight', $health_exam_fields))
                    <th>Weight</th>
                @endif
                @if(in_array('nutritional_status_bmi', $health_exam_fields))
                    <th>BMI Status</th>
                @endif
                @if(in_array('nutritional_status_height', $health_exam_fields))
                    <th>Height Status</th>
                @endif
                @if(in_array('temperature', $health_exam_fields))
                    <th>Temperature</th>
                @endif
                @if(in_array('heart_rate', $health_exam_fields))
                    <th>Heart Rate</th>
                @endif
                @if(in_array('vision_screening', $health_exam_fields))
                    <th>Vision</th>
                @endif
                @if(in_array('auditory_screening', $health_exam_fields))
                    <th>Hearing</th>
                @endif
                @if(in_array('skin', $health_exam_fields))
                    <th>Skin</th>
                @endif
                @if(in_array('scalp', $health_exam_fields))
                    <th>Scalp</th>
                @endif
                @if(in_array('eye', $health_exam_fields))
                    <th>Eyes</th>
                @endif
                @if(in_array('ear', $health_exam_fields))
                    <th>Ears</th>
                @endif
                @if(in_array('nose', $health_exam_fields))
                    <th>Nose</th>
                @endif
                @if(in_array('mouth', $health_exam_fields))
                    <th>Mouth</th>
                @endif
                @if(in_array('throat', $health_exam_fields))
                    <th>Throat</th>
                @endif
                @if(in_array('neck', $health_exam_fields))
                    <th>Neck</th>
                @endif
                @if(in_array('lungs_heart', $health_exam_fields))
                    <th>Lungs/Heart</th>
                @endif
                @if(in_array('abdomen', $health_exam_fields))
                    <th>Abdomen</th>
                @endif
                @if(in_array('deworming_status', $health_exam_fields))
                    <th>Deworming</th>
                @endif
                @if(in_array('iron_supplementation', $health_exam_fields))
                    <th>Iron Supplement</th>
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
                    @if(in_array('height', $health_exam_fields))
                        <td>{{ $student['health_exam']->height ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('weight', $health_exam_fields))
                        <td>{{ $student['health_exam']->weight ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('nutritional_status_bmi', $health_exam_fields))
                        <td>{{ $student['health_exam']->nutritional_status_bmi ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('nutritional_status_height', $health_exam_fields))
                        <td>{{ $student['health_exam']->nutritional_status_height ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('temperature', $health_exam_fields))
                        <td>{{ $student['health_exam']->temperature ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('heart_rate', $health_exam_fields))
                        <td>{{ $student['health_exam']->heart_rate ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('vision_screening', $health_exam_fields))
                        <td>{{ $student['health_exam']->vision_screening ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('auditory_screening', $health_exam_fields))
                        <td>{{ $student['health_exam']->auditory_screening ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('skin', $health_exam_fields))
                        <td>{{ $student['health_exam']->skin ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('scalp', $health_exam_fields))
                        <td>{{ $student['health_exam']->scalp ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('eye', $health_exam_fields))
                        <td>{{ $student['health_exam']->eye ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('ear', $health_exam_fields))
                        <td>{{ $student['health_exam']->ear ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('nose', $health_exam_fields))
                        <td>{{ $student['health_exam']->nose ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('mouth', $health_exam_fields))
                        <td>{{ $student['health_exam']->mouth ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('throat', $health_exam_fields))
                        <td>{{ $student['health_exam']->throat ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('neck', $health_exam_fields))
                        <td>{{ $student['health_exam']->neck ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('lungs_heart', $health_exam_fields))
                        <td>{{ $student['health_exam']->lungs_heart ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('abdomen', $health_exam_fields))
                        <td>{{ $student['health_exam']->abdomen ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('deworming_status', $health_exam_fields))
                        <td>{{ $student['health_exam']->deworming_status ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('iron_supplementation', $health_exam_fields))
                        <td>{{ $student['health_exam']->iron_supplementation ?? 'N/A' }}</td>
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
