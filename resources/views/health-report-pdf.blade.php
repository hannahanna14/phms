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
            table-layout: fixed;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: left;
            font-size: 8px;
            word-wrap: break-word;
            overflow: hidden;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
            line-height: 1.1;
        }
        
        /* Specific column widths based on content */
        .col-name { width: 14%; }  /* Increased from 12% */
        .col-lrn { width: 10%; }   /* Increased from 8% */
        .col-grade { width: 5%; }  /* Increased from 4% */
        .col-section { width: 6%; } /* Increased from 5% */
        .col-gender { width: 5%; }  /* Increased from 4% */
        .col-age { width: 4%; }     /* Increased from 3% */
        .col-health { width: 6%; }  /* Increased from 5% */
        .col-health-wide { width: 8%; } /* Increased from 7% */
        .col-health-narrow { width: 5%; } /* Increased from 4% */
        
        /* Responsive adjustments for many columns */
        @media print {
            .data-table {
                font-size: 7px;
            }
            .data-table th {
                font-size: 6px;
                padding: 2px 1px;
            }
            .data-table td {
                padding: 2px 1px;
            }
        }
        
        /* Text truncation for long content */
        .col-health-wide,
        .col-health {
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header" style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 20px;">
        @if($schoolSettings->school_logo)
            <img src="{{ public_path('storage/' . $schoolSettings->school_logo) }}" alt="School Logo" style="height: 60px; margin-bottom: 10px;">
        @else
            <img src="{{ public_path('images/logo.png') }}" alt="School Logo" style="height: 60px; margin-bottom: 10px;">
        @endif
        <div class="school-name" style="font-size: 16px; font-weight: bold; margin-bottom: 2px;">
            {{ $schoolSettings->school_name ?: 'NAAWAN CENTRAL SCHOOL' }}
        </div>
        @if($schoolSettings->school_address)
            <div class="region" style="font-size: 11px; color: #666; margin-bottom: 8px;">{{ $schoolSettings->school_address }}</div>
        @else
            <div class="region" style="font-size: 11px; color: #666; margin-bottom: 8px;">Region X - Northern Mindanao</div>
        @endif
        <div class="report-title" style="font-size: 14px; font-weight: bold; margin-bottom: 2px;">Health Report</div>
        <div class="report-details" style="font-size: 11px; color: #666;">
            {{ $grade_level }} 
            @if($section) - Section {{ $section }} @endif
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                @if(in_array('name', $fields)) <th class="col-name">Name</th> @endif
                @if(in_array('lrn', $fields)) <th class="col-lrn">LRN</th> @endif
                @if(in_array('grade_level', $fields)) <th class="col-grade">Grade</th> @endif
                @if(in_array('section', $fields)) <th class="col-section">Section</th> @endif
                @if(in_array('gender', $fields)) <th class="col-gender">Gender</th> @endif
                @if(in_array('age', $fields)) <th class="col-age">Age</th> @endif
                @foreach($health_exam_fields as $field)
                    @php
                        $columnClass = 'col-health';
                        if (in_array($field, ['nutritional_status_bmi', 'nutritional_status_height', 'vision_screening', 'auditory_screening'])) {
                            $columnClass = 'col-health-wide';
                        } elseif (in_array($field, ['age', 'weight', 'height', 'temperature', 'heart_rate'])) {
                            $columnClass = 'col-health-narrow';
                        }
                    @endphp
                    <th class="{{ $columnClass }}">{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $student)
                <tr>
                    @if(in_array('name', $fields)) <td class="col-name">{{ $student['name'] ?? 'N/A' }}</td> @endif
                    @if(in_array('lrn', $fields)) <td class="col-lrn">{{ $student['lrn'] ?? 'N/A' }}</td> @endif
                    @if(in_array('grade_level', $fields)) <td class="col-grade">{{ $student['grade_level'] ?? 'N/A' }}</td> @endif
                    @if(in_array('section', $fields)) <td class="col-section">{{ $student['section'] ?? 'N/A' }}</td> @endif
                    @if(in_array('gender', $fields)) <td class="col-gender">{{ $student['gender'] ?? 'N/A' }}</td> @endif
                    @if(in_array('age', $fields)) <td class="col-age">{{ $student['age'] ?? 'N/A' }}</td> @endif
                    @foreach($health_exam_fields as $field)
                        @php
                            $columnClass = 'col-health';
                            if (in_array($field, ['nutritional_status_bmi', 'nutritional_status_height', 'vision_screening', 'auditory_screening'])) {
                                $columnClass = 'col-health-wide';
                            } elseif (in_array($field, ['age', 'weight', 'height', 'temperature', 'heart_rate'])) {
                                $columnClass = 'col-health-narrow';
                            }
                        @endphp
                        <td class="{{ $columnClass }}">{{ $student['health_exam'][$field] ?? 'N/A' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Printed by: {{ $user_name ?? 'System' }}</div>
        <div>Date: {{ \Carbon\Carbon::now('Asia/Manila')->format('F d, Y') }} at {{ \Carbon\Carbon::now('Asia/Manila')->format('g:i A') }}</div>
    </div>
</body>
</html>
