<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Oral Health Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5in;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .school-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .region {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-details {
            font-size: 11px;
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
            text-align: center;
            font-size: 9px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
            line-height: 1.1;
        }
        
        /* Column widths */
        .col-name {
            width: 15%;
            text-align: left !important;
            padding-left: 6px;
        }
        
        .col-lrn {
            width: 12%;
        }
        
        .col-basic {
            width: 6%;
        }
        
        .col-oral-data {
            width: 6%;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            padding-top: 10px;
        }
    </style>
<body>
    <div class="header" style="text-align: center; margin-bottom: 20px;">
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
        <div class="report-title" style="font-size: 14px; font-weight: bold; margin-bottom: 2px;">Oral Health Report</div>
        <div class="report-details" style="font-size: 11px; color: #666;">
            {{ $grade_level }} 
            @if($section) - Section {{ $section }} @endif
        </div>
    </div>
    <table class="data-table">
        <thead>
                @if(in_array('name', $fields))
                    <th class="col-name">Name</th>
                @endif
                @if(in_array('lrn', $fields))
                    <th class="col-lrn">LRN</th>
                @endif
                @if(in_array('grade_level', $fields))
                    <th class="col-basic">Grade</th>
                @endif
                @if(in_array('section', $fields))
                    <th class="col-basic">Section</th>
                @endif
                @if(in_array('gender', $fields))
                    <th class="col-basic">Gender</th>
                @endif
                @if(in_array('age', $fields))
                    <th class="col-basic">Age</th>
                @endif
                @if(isset($oral_exam_fields) && is_array($oral_exam_fields))
                    @foreach($oral_exam_fields as $field)
                        <th class="col-oral-data">
                            @switch($field)
                                @case('permanent_teeth_decayed')
                                    Perm. Teeth Decayed
                                    @break
                                @case('permanent_teeth_filled')
                                    Perm. Teeth Filled
                                    @break
                                @case('permanent_for_extraction')
                                    Perm. For Extraction
                                    @break
                                @case('permanent_for_filling')
                                    Perm. For Filling
                                    @break
                                @case('temporary_teeth_decayed')
                                    Temp. Teeth Decayed
                                    @break
                                @case('temporary_teeth_filled')
                                    Temp. Teeth Filled
                                    @break
                                @case('temporary_for_extraction')
                                    Temp. For Extraction
                                    @break
                                @case('temporary_for_filling')
                                    Temp. For Filling
                                    @break
                                @default
                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                            @endswitch
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $student)
                <tr>
                    @if(in_array('name', $fields))
                        <td class="col-name">{{ $student['name'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('lrn', $fields))
                        <td class="col-lrn">{{ $student['lrn'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('grade_level', $fields))
                        <td class="col-basic">{{ $student['grade_level'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('section', $fields))
                        <td class="col-basic">{{ $student['section'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('gender', $fields))
                        <td class="col-basic">{{ $student['gender'] ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('age', $fields))
                        <td class="col-basic">{{ $student['age'] ?? 'N/A' }}</td>
                    @endif
                    
                    @php
                        $oralExam = $student['oral_health_examination'] ?? null;
                    @endphp
                    
                    @if(isset($oral_exam_fields) && is_array($oral_exam_fields))
                        @foreach($oral_exam_fields as $field)
                            <td class="col-oral-data">{{ $oralExam[$field] ?? 0 }}</td>
                        @endforeach
                    @endif
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
