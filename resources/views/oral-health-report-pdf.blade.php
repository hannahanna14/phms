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
            @php
                // Try multiple logo locations
                $logoPaths = [
                    public_path('images/logo.png'),
                    resource_path('js/assets/logo.png'),
                    public_path('assets/logo.png'),
                ];
                
                $logoFound = false;
                foreach ($logoPaths as $path) {
                    if (file_exists($path)) {
                        try {
                            $imageData = file_get_contents($path);
                            if ($imageData !== false) {
                                $base64 = base64_encode($imageData);
                                $extension = pathinfo($path, PATHINFO_EXTENSION);
                                echo '<img src="data:image/' . $extension . ';base64,' . $base64 . '" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">';
                                $logoFound = true;
                                break;
                            }
                        } catch (Exception $e) {
                            // Continue to next path
                        }
                    }
                }
                
                if (!$logoFound) {
                    echo '<div style="width: 70px; height: 70px; border: 2px solid #1e3a8a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; color: #1e3a8a; background-color: #f8fafc;">LOGO</div>';
                }
            @endphp
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
                <!-- Dynamic Oral Health Columns -->
                @foreach($oral_exam_fields as $field)
                    <th>{{ ucwords(str_replace(['_', 'permanent', 'temporary'], [' ', 'Perm.', 'Temp.'], $field)) }}</th>
                @endforeach
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
                    <!-- Dynamic Oral Health Data -->
                    @foreach($oral_exam_fields as $field)
                        <td>{{ $student[$field] ?? 'N/A' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Printed by: {{ $user_name ?? 'System' }}</div>
        <div>Date: {{ date('F d, Y') }}</div>
    </div>
</body>
</html>
