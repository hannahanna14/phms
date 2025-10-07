<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Examination Report Card</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.3in;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header-top {
            position: relative;
            margin-bottom: 10px;
        }
        
        .form-number {
            font-size: 8px;
            position: absolute;
            left: 0;
            top: 0;
        }
        
        .republic-info {
            font-size: 7px;
            text-align: center;
            line-height: 1.2;
            margin: 0 auto;
            width: 100%;
        }
        
        .tagig-city {
            font-size: 7px;
            text-align: center;
            margin-top: 2px;
        }
        
        .bureau-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .main-title {
            border: 2px solid #000;
            color: black;
            padding: 8px 10px;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            line-height: 1.3;
            word-wrap: break-word;
            overflow: visible;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            border: none;
            padding: 4px 0;
            vertical-align: middle;
        }
        
        .date-cell {
            display: flex;
            align-items: center;
        }
        
        .info-label {
            font-size: 9px;
            font-weight: bold;
            width: 80px;
            flex-shrink: 0;
        }
        
        .info-value {
            border-bottom: 1px solid #000;
            height: 18px;
            width: 200px;
            margin-right: 40px;
            padding-left: 5px;
            display: inline-block;
            text-align: left;
        }
        
        .info-label-right {
            font-size: 9px;
            font-weight: bold;
            width: 80px;
            flex-shrink: 0;
        }
        
        .info-value-right {
            border-bottom: 1px solid #000;
            height: 18px;
            width: 200px;
            padding-left: 5px;
            display: inline-block;
            text-align: left;
        }
        
        .three-column {
            display: flex;
            gap: 10px;
        }
        
        .column {
            flex: 1;
        }
        
        .date-section {
            display: inline-block;
            margin-right: 20px;
        }
        
        .date-box {
            border-bottom: 1px solid #000;
            padding: 2px 5px;
            width: 30px;
            text-align: center;
            display: inline-block;
            margin-right: 5px;
        }
        
        .date-label {
            font-size: 8px;
            margin-right: 10px;
        }
        
        .examination-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 7px;
            table-layout: fixed;
        }
        
        .examination-table th,
        .examination-table td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            font-size: 6px;
            line-height: 1.1;
        }
        
        .examination-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .grade-header {
            font-size: 4px;
            width: 6%;
            padding: 1px;
            line-height: 1.0;
        }
        
        .findings-header {
            font-size: 4px;
            width: 6%;
            padding: 1px;
            writing-mode: vertical-lr;
            text-orientation: mixed;
            height: 50px;
        }
        
        .examination-item {
            text-align: left;
            padding-left: 2px;
            width: 22%;
            font-size: 7px;
            font-weight: bold;
            line-height: 1.1;
        }
        
        .legends-table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .legends-table th,
        .legends-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            vertical-align: top;
            width: 12.5%;
        }
        
        .legends-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .legend-category {
            width: 120px;
            font-weight: bold;
        }
        
        .legend-item {
            padding-left: 3px;
            font-size: 10px;
            text-align: left;
            word-wrap: break-word;
            overflow: hidden;
        }
        
        .intervention-table {
            width: 90%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .intervention-table th,
        .intervention-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        .intervention-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .intervention-header {
            font-size: 9px;
            font-weight: bold;
        }
        
        /* Data cell specific styles for better text handling */
        .examination-table tbody td {
            font-size: 5px;
            padding: 0.5px;
            line-height: 1.0;
            max-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Handle long text in specific rows */
        .examination-table tbody td.long-text {
            white-space: normal;
            word-wrap: break-word;
            hyphens: auto;
            font-size: 4px;
        }
        
        /* Print optimizations */
        @media print {
            .examination-table {
                font-size: 6px;
            }
            
            .examination-table th,
            .examination-table td {
                font-size: 5px;
                padding: 0.5px;
            }
            
            .examination-item {
                font-size: 6px;
            }
            
            .grade-header,
            .findings-header {
                font-size: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="form-number">2018 SHD FORM 2</div>
            <div class="republic-info">
                REPUBLIC OF THE PHILIPPINES<br>
                DEPARTMENT OF EDUCATION<br>
                <strong>BUREAU OF LEARNERS SUPPORT SERVICES-SCHOOL HEALTH DIVISION</strong>
            </div>
            <div class="tagig-city">Tagig City</div>
        </div>
    </div>
    
    <div class="main-title">
        BUREAU OF LEARNERS SUPPORT SERVICES-SCHOOL HEALTH DIVISION
    </div>
    
    <table class="info-table">
        <tr>
            <td class="info-label">Name:</td>
            <td class="info-value">{{ $student->full_name ?? '' }}</td>
            <td class="info-label-right">School ID:</td>
            <td class="info-value-right">{{ $student->school_id ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">LRN:</td>
            <td class="info-value">{{ $student->lrn ?? '' }}</td>
            <td class="info-label-right"></td>
            <td class="info-value-right"></td>
        </tr>
        <tr>
            <td class="info-label">Date of Birth:</td>
            <td class="date-cell">
                <span class="date-label">Month:</span>
                <span class="date-box">{{ $student->birthdate ? \Carbon\Carbon::parse($student->birthdate)->format('M') : '' }}</span>
                <span class="date-label">Day:</span>
                <span class="date-box">{{ $student->birthdate ? \Carbon\Carbon::parse($student->birthdate)->format('d') : '' }}</span>
                <span class="date-label">Year:</span>
                <span class="date-box">{{ $student->birthdate ? \Carbon\Carbon::parse($student->birthdate)->format('Y') : '' }}</span>
            </td>
            <td class="info-label-right">Region:</td>
            <td class="info-value-right">{{ $student->region ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Birthplace:</td>
            <td class="info-value">{{ $student->birthplace ?? '' }}</td>
            <td class="info-label-right">Division:</td>
            <td class="info-value-right">{{ $student->division ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Parent/Guardian:</td>
            <td class="info-value">{{ $student->parent_guardian ?? '' }}</td>
            <td class="info-label-right">Telephone No:</td>
            <td class="info-value-right">{{ $student->telephone_no ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Address:</td>
            <td class="info-value">{{ $student->address ?? '' }}</td>
            <td class="info-label-right"></td>
            <td class="info-value-right"></td>
        </tr>
    </table>
    
    <table class="examination-table">
        <thead>
            <tr>
                <th rowspan="2" class="examination-item"></th>
                <th class="grade-header">SPED<br>Kinder/</th>
                <th class="grade-header">Grade1/<br>SPED</th>
                <th class="grade-header">Grade2/<br>SPED</th>
                <th class="grade-header">Grade3/<br>SPED</th>
                <th class="grade-header">Grade4/<br>SPED</th>
                <th class="grade-header">Grade5/<br>SPED</th>
                <th class="grade-header">Grade6/<br>SPED</th>
                <th class="grade-header">Grade7/<br>SPED</th>
                <th class="grade-header">Grade8/<br>SPED</th>
                <th class="grade-header">Grade9/<br>SPED</th>
                <th class="grade-header">Grade10/<br>SPED</th>
                <th class="grade-header">Grade11/<br>SPED</th>
                <th class="grade-header">Grade12/<br>SPED</th>
            </tr>
            <tr>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
                <th class="findings-header">Findings</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="examination-item">Date of Examination</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->examination_date ? \Carbon\Carbon::parse($exam->examination_date)->format('m/d/Y') : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Temperature/BP</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->temperature ? $exam->temperature : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Heart Rate/Pulse Rate/Respiratory Rate</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td class="long-text">{{ $exam && $exam->heart_rate ? $exam->heart_rate : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Height(in cm)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->height ? $exam->height : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Weight(in kg)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->weight ? $exam->weight : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Nutritional Status(NS)(BMI/Wt-for-Age)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td class="long-text">{{ $exam && $exam->nutritional_status_bmi ? $exam->nutritional_status_bmi : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Nutritional Status(NS)(Height-for-Age)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td class="long-text">{{ $exam && $exam->nutritional_status_height ? $exam->nutritional_status_height : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Vision Screening using appropriate chart</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->vision_screening ? $exam->vision_screening : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Auditory Screening (Tuning Fork)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->auditory_screening ? $exam->auditory_screening : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Skin/Scalp</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && ($exam->skin || $exam->scalp) ? ($exam->skin ?? $exam->scalp) : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Eyes/Ears/Nose</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && ($exam->eye || $exam->ear || $exam->nose) ? ($exam->eye ?? $exam->ear ?? $exam->nose) : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Mouth/Throat/Neck</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && ($exam->mouth || $exam->throat || $exam->neck) ? ($exam->mouth ?? $exam->throat ?? $exam->neck) : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Lungs/Heart</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->lungs_heart ? $exam->lungs_heart : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Abdomen</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->abdomen ? $exam->abdomen : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Deformities</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->deformities ? $exam->deformities : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Iron Supplementation (✓ or X)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->iron_supplementation ? $exam->iron_supplementation : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Deworming (✓ or X)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td class="long-text">{{ $exam && $exam->deworming_status ? $exam->deworming_status : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">Immunization (Specify what kind)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td class="long-text">{{ $exam && $exam->immunization ? $exam->immunization : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">SBFP Beneficiary (✓ or X)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->sbfp_beneficiary ? $exam->sbfp_beneficiary : '' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="examination-item">4Ps Beneficiary(✓ or X)</td>
                @foreach($orderedExaminations as $grade => $exam)
                    <td>{{ $exam && $exam->four_ps_beneficiary ? $exam->four_ps_beneficiary : '' }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    
    <table class="legends-table">
        <thead>
            <tr>
                <th colspan="8">LEGENDS:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="legend-category">NS</td>
                <td class="legend-category">Vision/Auditor Screening</td>
                <td class="legend-category">Skin/Scalp</td>
                <td class="legend-category">Eye/Ear/Nose</td>
                <td class="legend-category">Mouth/Neck/Throat</td>
                <td class="legend-category">Lungs/Heart</td>
                <td class="legend-category">Abdomen</td>
                <td class="legend-category">Deformities</td>
            </tr>
            <tr>
                <td class="legend-item">a. Normal Weight</td>
                <td class="legend-item">a. Passed</td>
                <td class="legend-item">a. Normal</td>
                <td class="legend-item">a. Normal</td>
                <td class="legend-item">a. Normal</td>
                <td class="legend-item">a. Normal</td>
                <td class="legend-item">a. Normal</td>
                <td class="legend-item">a. Acquired</td>
            </tr>
            <tr>
                <td class="legend-item">b. Wasted/ Underweight</td>
                <td class="legend-item">b. Failed</td>
                <td class="legend-item">b. Presence of Lice</td>
                <td class="legend-item">b. Stye</td>
                <td class="legend-item">b. Enlarged tonsils</td>
                <td class="legend-item">b. Rales</td>
                <td class="legend-item">b. Distended</td>
                <td class="legend-item">b. Congenital (Specify)</td>
            </tr>
            <tr>
                <td class="legend-item">c. Severly Wasted/ Underw't</td>
                <td class="legend-item"></td>
                <td class="legend-item">c.Redness of Skin</td>
                <td class="legend-item">c. Eye Redness</td>
                <td class="legend-item">c. Presence of lesions</td>
                <td class="legend-item">c. Wheeze</td>
                <td class="legend-item">c. Abdominal pain</td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">d.Overweight</td>
                <td class="legend-item"></td>
                <td class="legend-item">d. White Spots</td>
                <td class="legend-item">d. Ocular Movement</td>
                <td class="legend-item">d. Inflamed pharynx</td>
                <td class="legend-item">d. Murmur</td>
                <td class="legend-item">d. Tenderness</td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">e. Obese</td>
                <td class="legend-item"></td>
                <td class="legend-item">e. Flaky Spots</td>
                <td class="legend-item">e. Pale Conjunctiva</td>
                <td class="legend-item">e. Enlarged lymphnodes</td>
                <td class="legend-item">e. Irregular heart rate</td>
                <td class="legend-item">e. Dysmenorrhea</td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">f. Normal Height</td>
                <td class="legend-item"></td>
                <td class="legend-item">f. Impetigo/ boil</td>
                <td class="legend-item">f. Ear discharge</td>
                <td class="legend-item">f. Others, specify</td>
                <td class="legend-item">f. Other specify</td>
                <td class="legend-item">f. Others, specify</td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">g. Stunted</td>
                <td class="legend-item"></td>
                <td class="legend-item">g.Hematoma</td>
                <td class="legend-item">g. Impacted cerumen</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">h. Severly Stunted</td>
                <td class="legend-item"></td>
                <td class="legend-item">h.Bruises/Injuries</td>
                <td class="legend-item">h. Mucus discharge</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item">i. Tall</td>
                <td class="legend-item"></td>
                <td class="legend-item">i.Itchiness</td>
                <td class="legend-item">i. Nose Bleeding (Epistaxis</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item">j. Skin Lesions</td>
                <td class="legend-item">j. Eye discharge</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item">k. Acne/Pimple</td>
                <td class="legend-item">k. Matted Eyelashes</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
            <tr>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item">l. Other specify</td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
                <td class="legend-item"></td>
            </tr>
        </tbody>
    </table>
    
    <table class="intervention-table">
        <thead>
            <tr>
                <th colspan="5" class="intervention-header">INTERVENTION/TREATMENT RECORD</th>
            </tr>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 25%;">Chief Complaint</th>
                <th style="width: 30%;">Intervention/Treatment Done</th>
                <th style="width: 15%;">Remarks</th>
                <th style="width: 15%;">Attended by (Name/Position)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($healthTreatments) && $healthTreatments->count() > 0)
                @foreach($healthTreatments as $treatment)
                <tr>
                    <td style="padding: 3px; font-size: 6px;">{{ $treatment->date ? \Carbon\Carbon::parse($treatment->date)->format('m/d/Y') : '' }}</td>
                    <td style="padding: 3px; font-size: 6px;">{{ $treatment->chief_complaint ?? '' }}</td>
                    <td style="padding: 3px; font-size: 6px;">{{ $treatment->treatment ?? '' }}</td>
                    <td style="padding: 3px; font-size: 6px;">{{ $treatment->remarks ?? '' }}</td>
                    <td style="padding: 3px; font-size: 6px;">{{ $treatment->attended_by ?? 'School Nurse' }}</td>
                </tr>
                @endforeach
                
                @for($i = $healthTreatments->count(); $i < 5; $i++)
                <tr>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                </tr>
                @endfor
            @else
                @for($i = 0; $i < 5; $i++)
                <tr>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                    <td style="height: 25px;"></td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
</body>
</html>
