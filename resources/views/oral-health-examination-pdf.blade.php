<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oral Health Examination Report Card</title>
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
        
        /* Dental Chart Styles */
        .dental-chart {
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 7px;
        }
        
        .dental-chart td {
            border: 1px solid #000;
            width: 15px;
            height: 12px;
            text-align: center;
            vertical-align: middle;
            padding: 1px;
        }
        
        /* Empty cells with no borders */
        .empty {
            border: none;
            background: transparent;
        }
        
        /* No border class for specific cells */
        .no-border {
            border: none !important;
            background: transparent !important;
        }
        
        /* Label cells */
        .label {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
            padding: 1px;
        }
        
        /* Tooth number cells */
        .tooth-number {
            background-color: #f9f9f9;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Symbol cells */
        .symbol {
            background-color: white;
            color: #0066cc;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Working area cells */
        .work-area {
            background-color: white;
        }
        
        /* Side labels */
        .side-label {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
        }
        
        /* Container for table and side label */
        .chart-container {
            position: relative;
            margin: 10px 0;
        }
        
        /* Side permanent teeth label */
        .permanent-teeth-label {
            position: absolute;
            left: -80px;
            top: 60px;
            font-weight: bold;
            font-size: 7px;
            padding: 1px;
            width: 140px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        
        .dental-charts-container {
            width: 100%;
            overflow: hidden;
        }
        
        .charts-row {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .grade-section {
            float: left;
            width: 48%;
            margin-left: 25px;
            margin-right: 2%;
        }
        
        .grade-section:last-child {
            margin-right: 0;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .grade-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin: 10px 0 5px 0;
        }
        
        .grade-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        
        .school-year {
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            width: 150px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="form-number">2018 SHD FORM 3</div>
            <div class="republic-info">
                REPUBLIC OF THE PHILIPPINES<br>
                DEPARTMENT OF EDUCATION<br>
                <strong>BUREAU OF LEARNERS SUPPORT SERVICES-SCHOOL HEALTH DIVISION</strong>
            </div>
            <div class="tagig-city">Tagig City</div>
        </div>
    </div>
    
    <div class="main-title">
        ORAL HEALTH EXAMINATION REPORT CARD
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

    <!-- Dental Chart Section -->
    <div style="margin-top: 30px;">
        <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 20px; border: 2px solid #000; padding: 8px;">
            SCHOOL ORAL HEALTH EXAMINATION CARD
        </div>

        <div class="dental-charts-container">
            <div class="charts-row clearfix">
            <!-- Kinder -->
            <div class="grade-section">
                <div class="grade-header">
                    <div class="grade-title">KINDER</div>
                    <div class="school-year">S.Y. ___________________</div>
                </div>
            <div class="chart-container">
                <div class="permanent-teeth-label">PERMANENT TEETH</div>
                <table class="dental-chart">
                <!-- Row 1: Empty spacing -->
                <tr>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                
                <!-- Row 2: Temporary teeth numbers (55-65) with RIGHT spanning columns 2-3 -->
                <tr>
                    <td class="empty no-border"></td>
                    <td class="side-label no-border" colspan="2">RIGHT</td>
                    <td class="tooth-number">55</td>
                    <td class="tooth-number">54</td>
                    <td class="tooth-number">53</td>
                    <td class="tooth-number">52</td>
                    <td class="tooth-number">51</td>
                    <td class="tooth-number">61</td>
                    <td class="tooth-number">62</td>
                    <td class="tooth-number">63</td>
                    <td class="tooth-number">64</td>
                    <td class="tooth-number">65</td>
                    <td class="side-label no-border" colspan="2">LEFT</td>
                    <td class="empty no-border"></td>
                </tr>
                
                <!-- Row 3: Temporary teeth symbols with horizontal TEMPORARY TEETH label -->
                <tr>
                    <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                
                <!-- Row 4: Empty spacing -->
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                
                <!-- Row 5: Empty spacing -->
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                
                <!-- Row 6: Upper permanent teeth numbers (18-28) -->
                <tr>
                    <td class="tooth-number">18</td>
                    <td class="tooth-number">17</td>
                    <td class="tooth-number">16</td>
                    <td class="tooth-number">15</td>
                    <td class="tooth-number">14</td>
                    <td class="tooth-number">13</td>
                    <td class="tooth-number">12</td>
                    <td class="tooth-number">11</td>
                    <td class="tooth-number">21</td>
                    <td class="tooth-number">22</td>
                    <td class="tooth-number">23</td>
                    <td class="tooth-number">24</td>
                    <td class="tooth-number">25</td>
                    <td class="tooth-number">26</td>
                    <td class="tooth-number">27</td>
                    <td class="tooth-number">28</td>
                </tr>
                
                <!-- Row 7: Upper permanent teeth symbols -->
                <tr>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                </tr>
                
                <!-- Row 8: Lower permanent teeth symbols -->
                <tr>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                </tr>
                
                <!-- Row 9: Lower permanent teeth numbers -->
                <tr>
                    <td class="tooth-number">48</td>
                    <td class="tooth-number">47</td>
                    <td class="tooth-number">46</td>
                    <td class="tooth-number">45</td>
                    <td class="tooth-number">44</td>
                    <td class="tooth-number">43</td>
                    <td class="tooth-number">42</td>
                    <td class="tooth-number">41</td>
                    <td class="tooth-number">31</td>
                    <td class="tooth-number">32</td>
                    <td class="tooth-number">33</td>
                    <td class="tooth-number">34</td>
                    <td class="tooth-number">35</td>
                    <td class="tooth-number">36</td>
                    <td class="tooth-number">37</td>
                    <td class="tooth-number">38</td>
                </tr>
                
                <!-- Row 10: Empty spacing -->
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                
                <!-- Row 11: Empty spacing -->
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                
                <!-- Row 12: TEMPORARY TEETH label and symbols -->
                <tr>
                    <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                
                <!-- Row 13: Bottom temporary teeth numbers (85-75) -->
                <tr>
                    <td class="empty no-border"></td>
                    <td class="side-label no-border" colspan="2">RIGHT</td>
                    <td class="tooth-number">85</td>
                    <td class="tooth-number">84</td>
                    <td class="tooth-number">83</td>
                    <td class="tooth-number">82</td>
                    <td class="tooth-number">81</td>
                    <td class="tooth-number">71</td>
                    <td class="tooth-number">72</td>
                    <td class="tooth-number">73</td>
                    <td class="tooth-number">74</td>
                    <td class="tooth-number">75</td>
                    <td class="side-label no-border" colspan="2">LEFT</td>
                    <td class="empty no-border"></td>
                </tr>
                
                <!-- Row 14: Bottom spacing -->
                <tr>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
            </table>
        </div>
        </div>

            <!-- Grade 1 -->
            <div class="grade-section">
                <div class="grade-header">
                    <div class="grade-title">GRADE 1</div>
                    <div class="school-year">S.Y. ___________________</div>
                </div>
            <div class="chart-container">
                <div class="permanent-teeth-label">PERMANENT TEETH</div>
                <table class="dental-chart">
                <!-- Same dental chart structure as Kinder -->
                <tr>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                <tr>
                    <td class="empty no-border"></td>
                    <td class="side-label no-border" colspan="2">RIGHT</td>
                    <td class="tooth-number">55</td>
                    <td class="tooth-number">54</td>
                    <td class="tooth-number">53</td>
                    <td class="tooth-number">52</td>
                    <td class="tooth-number">51</td>
                    <td class="tooth-number">61</td>
                    <td class="tooth-number">62</td>
                    <td class="tooth-number">63</td>
                    <td class="tooth-number">64</td>
                    <td class="tooth-number">65</td>
                    <td class="side-label no-border" colspan="2">LEFT</td>
                    <td class="empty no-border"></td>
                </tr>
                <tr>
                    <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr>
                    <td class="tooth-number">18</td>
                    <td class="tooth-number">17</td>
                    <td class="tooth-number">16</td>
                    <td class="tooth-number">15</td>
                    <td class="tooth-number">14</td>
                    <td class="tooth-number">13</td>
                    <td class="tooth-number">12</td>
                    <td class="tooth-number">11</td>
                    <td class="tooth-number">21</td>
                    <td class="tooth-number">22</td>
                    <td class="tooth-number">23</td>
                    <td class="tooth-number">24</td>
                    <td class="tooth-number">25</td>
                    <td class="tooth-number">26</td>
                    <td class="tooth-number">27</td>
                    <td class="tooth-number">28</td>
                </tr>
                <tr>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                </tr>
                <tr>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                </tr>
                <tr>
                    <td class="tooth-number">48</td>
                    <td class="tooth-number">47</td>
                    <td class="tooth-number">46</td>
                    <td class="tooth-number">45</td>
                    <td class="tooth-number">44</td>
                    <td class="tooth-number">43</td>
                    <td class="tooth-number">42</td>
                    <td class="tooth-number">41</td>
                    <td class="tooth-number">31</td>
                    <td class="tooth-number">32</td>
                    <td class="tooth-number">33</td>
                    <td class="tooth-number">34</td>
                    <td class="tooth-number">35</td>
                    <td class="tooth-number">36</td>
                    <td class="tooth-number">37</td>
                    <td class="tooth-number">38</td>
                </tr>
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="work-area"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr>
                    <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="symbol">×</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">—</td>
                    <td class="symbol">—</td>
                    <td class="symbol">◇</td>
                    <td class="symbol">×</td>
                    <td class="symbol">○</td>
                    <td class="symbol">○</td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
                <tr>
                    <td class="empty no-border"></td>
                    <td class="side-label no-border" colspan="2">RIGHT</td>
                    <td class="tooth-number">85</td>
                    <td class="tooth-number">84</td>
                    <td class="tooth-number">83</td>
                    <td class="tooth-number">82</td>
                    <td class="tooth-number">81</td>
                    <td class="tooth-number">71</td>
                    <td class="tooth-number">72</td>
                    <td class="tooth-number">73</td>
                    <td class="tooth-number">74</td>
                    <td class="tooth-number">75</td>
                    <td class="side-label no-border" colspan="2">LEFT</td>
                    <td class="empty no-border"></td>
                </tr>
                <tr>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                    <td class="empty no-border"></td>
                </tr>
            </table>
        </div>
        </div>
            </div>
        </div>

        <div class="dental-charts-container">
            <div class="charts-row clearfix">
                <!-- Grade 2 -->
                <div class="grade-section">
                    <div class="grade-header">
                        <div class="grade-title">GRADE 2</div>
                        <div class="school-year">S.Y. ___________________</div>
                    </div>
                    <div class="chart-container">
                        <div class="permanent-teeth-label">PERMANENT TEETH</div>
                        <table class="dental-chart">
                        <!-- Row 1: Empty spacing -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="side-label no-border" colspan="2">RIGHT</td>
                            <td class="tooth-number">55</td>
                            <td class="tooth-number">54</td>
                            <td class="tooth-number">53</td>
                            <td class="tooth-number">52</td>
                            <td class="tooth-number">51</td>
                            <td class="tooth-number">61</td>
                            <td class="tooth-number">62</td>
                            <td class="tooth-number">63</td>
                            <td class="tooth-number">64</td>
                            <td class="tooth-number">65</td>
                            <td class="side-label no-border" colspan="2">LEFT</td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="tooth-number">18</td>
                            <td class="tooth-number">17</td>
                            <td class="tooth-number">16</td>
                            <td class="tooth-number">15</td>
                            <td class="tooth-number">14</td>
                            <td class="tooth-number">13</td>
                            <td class="tooth-number">12</td>
                            <td class="tooth-number">11</td>
                            <td class="tooth-number">21</td>
                            <td class="tooth-number">22</td>
                            <td class="tooth-number">23</td>
                            <td class="tooth-number">24</td>
                            <td class="tooth-number">25</td>
                            <td class="tooth-number">26</td>
                            <td class="tooth-number">27</td>
                            <td class="tooth-number">28</td>
                        </tr>
                        <tr>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                        </tr>
                        <tr>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                        </tr>
                        <tr>
                            <td class="tooth-number">48</td>
                            <td class="tooth-number">47</td>
                            <td class="tooth-number">46</td>
                            <td class="tooth-number">45</td>
                            <td class="tooth-number">44</td>
                            <td class="tooth-number">43</td>
                            <td class="tooth-number">42</td>
                            <td class="tooth-number">41</td>
                            <td class="tooth-number">31</td>
                            <td class="tooth-number">32</td>
                            <td class="tooth-number">33</td>
                            <td class="tooth-number">34</td>
                            <td class="tooth-number">35</td>
                            <td class="tooth-number">36</td>
                            <td class="tooth-number">37</td>
                            <td class="tooth-number">38</td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="side-label no-border" colspan="2">RIGHT</td>
                            <td class="tooth-number">85</td>
                            <td class="tooth-number">84</td>
                            <td class="tooth-number">83</td>
                            <td class="tooth-number">82</td>
                            <td class="tooth-number">81</td>
                            <td class="tooth-number">71</td>
                            <td class="tooth-number">72</td>
                            <td class="tooth-number">73</td>
                            <td class="tooth-number">74</td>
                            <td class="tooth-number">75</td>
                            <td class="side-label no-border" colspan="2">LEFT</td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        </table>
                    </div>
                </div>

                <!-- Grade 3 -->
                <div class="grade-section">
                    <div class="grade-header">
                        <div class="grade-title">GRADE 3</div>
                        <div class="school-year">S.Y. ___________________</div>
                    </div>
                    <div class="chart-container">
                        <div class="permanent-teeth-label">PERMANENT TEETH</div>
                        <table class="dental-chart">
                        <!-- Same dental chart structure for Grade 3 -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="side-label no-border" colspan="2">RIGHT</td>
                            <td class="tooth-number">55</td>
                            <td class="tooth-number">54</td>
                            <td class="tooth-number">53</td>
                            <td class="tooth-number">52</td>
                            <td class="tooth-number">51</td>
                            <td class="tooth-number">61</td>
                            <td class="tooth-number">62</td>
                            <td class="tooth-number">63</td>
                            <td class="tooth-number">64</td>
                            <td class="tooth-number">65</td>
                            <td class="side-label no-border" colspan="2">LEFT</td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="tooth-number">18</td>
                            <td class="tooth-number">17</td>
                            <td class="tooth-number">16</td>
                            <td class="tooth-number">15</td>
                            <td class="tooth-number">14</td>
                            <td class="tooth-number">13</td>
                            <td class="tooth-number">12</td>
                            <td class="tooth-number">11</td>
                            <td class="tooth-number">21</td>
                            <td class="tooth-number">22</td>
                            <td class="tooth-number">23</td>
                            <td class="tooth-number">24</td>
                            <td class="tooth-number">25</td>
                            <td class="tooth-number">26</td>
                            <td class="tooth-number">27</td>
                            <td class="tooth-number">28</td>
                        </tr>
                        <tr>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                        </tr>
                        <tr>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                        </tr>
                        <tr>
                            <td class="tooth-number">48</td>
                            <td class="tooth-number">47</td>
                            <td class="tooth-number">46</td>
                            <td class="tooth-number">45</td>
                            <td class="tooth-number">44</td>
                            <td class="tooth-number">43</td>
                            <td class="tooth-number">42</td>
                            <td class="tooth-number">41</td>
                            <td class="tooth-number">31</td>
                            <td class="tooth-number">32</td>
                            <td class="tooth-number">33</td>
                            <td class="tooth-number">34</td>
                            <td class="tooth-number">35</td>
                            <td class="tooth-number">36</td>
                            <td class="tooth-number">37</td>
                            <td class="tooth-number">38</td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="work-area"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                        </tr>
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="symbol">×</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">—</td>
                            <td class="symbol">—</td>
                            <td class="symbol">◇</td>
                            <td class="symbol">×</td>
                            <td class="symbol">○</td>
                            <td class="symbol">○</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="side-label no-border" colspan="2">RIGHT</td>
                            <td class="tooth-number">85</td>
                            <td class="tooth-number">84</td>
                            <td class="tooth-number">83</td>
                            <td class="tooth-number">82</td>
                            <td class="tooth-number">81</td>
                            <td class="tooth-number">71</td>
                            <td class="tooth-number">72</td>
                            <td class="tooth-number">73</td>
                            <td class="tooth-number">74</td>
                            <td class="tooth-number">75</td>
                            <td class="side-label no-border" colspan="2">LEFT</td>
                            <td class="empty no-border"></td>
                        </tr>
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade 4 and 5 --><br>
        <div class="dental-charts-container">
            <div class="charts-row clearfix">
                <!-- Grade 4 -->
                <div class="grade-section">
                    <div class="grade-header">
                        <div class="grade-title">GRADE 4</div>
                        <div class="school-year">S.Y. ___________________</div>
                    </div>
                    <div class="chart-container">
                        <div class="permanent-teeth-label">PERMANENT TEETH</div>
                        <table class="dental-chart">
                        <!-- Complete dental chart structure for Grade 4 -->
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">55</td><td class="tooth-number">54</td><td class="tooth-number">53</td><td class="tooth-number">52</td><td class="tooth-number">51</td><td class="tooth-number">61</td><td class="tooth-number">62</td><td class="tooth-number">63</td><td class="tooth-number">64</td><td class="tooth-number">65</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="tooth-number">18</td><td class="tooth-number">17</td><td class="tooth-number">16</td><td class="tooth-number">15</td><td class="tooth-number">14</td><td class="tooth-number">13</td><td class="tooth-number">12</td><td class="tooth-number">11</td><td class="tooth-number">21</td><td class="tooth-number">22</td><td class="tooth-number">23</td><td class="tooth-number">24</td><td class="tooth-number">25</td><td class="tooth-number">26</td><td class="tooth-number">27</td><td class="tooth-number">28</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="tooth-number">48</td><td class="tooth-number">47</td><td class="tooth-number">46</td><td class="tooth-number">45</td><td class="tooth-number">44</td><td class="tooth-number">43</td><td class="tooth-number">42</td><td class="tooth-number">41</td><td class="tooth-number">31</td><td class="tooth-number">32</td><td class="tooth-number">33</td><td class="tooth-number">34</td><td class="tooth-number">35</td><td class="tooth-number">36</td><td class="tooth-number">37</td><td class="tooth-number">38</td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">85</td><td class="tooth-number">84</td><td class="tooth-number">83</td><td class="tooth-number">82</td><td class="tooth-number">81</td><td class="tooth-number">71</td><td class="tooth-number">72</td><td class="tooth-number">73</td><td class="tooth-number">74</td><td class="tooth-number">75</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Grade 5 -->
                <div class="grade-section">
                    <div class="grade-header">
                        <div class="grade-title">GRADE 5</div>
                        <div class="school-year">S.Y. ___________________</div>
                    </div>
                    <div class="chart-container">
                        <div class="permanent-teeth-label">PERMANENT TEETH</div>
                        <table class="dental-chart">
                        <!-- Complete dental chart structure for Grade 5 -->
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">55</td><td class="tooth-number">54</td><td class="tooth-number">53</td><td class="tooth-number">52</td><td class="tooth-number">51</td><td class="tooth-number">61</td><td class="tooth-number">62</td><td class="tooth-number">63</td><td class="tooth-number">64</td><td class="tooth-number">65</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="tooth-number">18</td><td class="tooth-number">17</td><td class="tooth-number">16</td><td class="tooth-number">15</td><td class="tooth-number">14</td><td class="tooth-number">13</td><td class="tooth-number">12</td><td class="tooth-number">11</td><td class="tooth-number">21</td><td class="tooth-number">22</td><td class="tooth-number">23</td><td class="tooth-number">24</td><td class="tooth-number">25</td><td class="tooth-number">26</td><td class="tooth-number">27</td><td class="tooth-number">28</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="tooth-number">48</td><td class="tooth-number">47</td><td class="tooth-number">46</td><td class="tooth-number">45</td><td class="tooth-number">44</td><td class="tooth-number">43</td><td class="tooth-number">42</td><td class="tooth-number">41</td><td class="tooth-number">31</td><td class="tooth-number">32</td><td class="tooth-number">33</td><td class="tooth-number">34</td><td class="tooth-number">35</td><td class="tooth-number">36</td><td class="tooth-number">37</td><td class="tooth-number">38</td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">85</td><td class="tooth-number">84</td><td class="tooth-number">83</td><td class="tooth-number">82</td><td class="tooth-number">81</td><td class="tooth-number">71</td><td class="tooth-number">72</td><td class="tooth-number">73</td><td class="tooth-number">74</td><td class="tooth-number">75</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade 6 -->
        <div class="dental-charts-container">
            <div class="charts-row clearfix">
                <div class="grade-section">
                    <div class="grade-header">
                        <div class="grade-title">GRADE 6</div>
                        <div class="school-year">S.Y. ___________________</div>
                    </div>
                    <div class="chart-container">
                        <div class="permanent-teeth-label">PERMANENT TEETH</div>
                        <table class="dental-chart">
                        <!-- Complete dental chart structure for Grade 6 -->
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">55</td><td class="tooth-number">54</td><td class="tooth-number">53</td><td class="tooth-number">52</td><td class="tooth-number">51</td><td class="tooth-number">61</td><td class="tooth-number">62</td><td class="tooth-number">63</td><td class="tooth-number">64</td><td class="tooth-number">65</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="tooth-number">18</td><td class="tooth-number">17</td><td class="tooth-number">16</td><td class="tooth-number">15</td><td class="tooth-number">14</td><td class="tooth-number">13</td><td class="tooth-number">12</td><td class="tooth-number">11</td><td class="tooth-number">21</td><td class="tooth-number">22</td><td class="tooth-number">23</td><td class="tooth-number">24</td><td class="tooth-number">25</td><td class="tooth-number">26</td><td class="tooth-number">27</td><td class="tooth-number">28</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">○</td></tr>
                        <tr><td class="tooth-number">48</td><td class="tooth-number">47</td><td class="tooth-number">46</td><td class="tooth-number">45</td><td class="tooth-number">44</td><td class="tooth-number">43</td><td class="tooth-number">42</td><td class="tooth-number">41</td><td class="tooth-number">31</td><td class="tooth-number">32</td><td class="tooth-number">33</td><td class="tooth-number">34</td><td class="tooth-number">35</td><td class="tooth-number">36</td><td class="tooth-number">37</td><td class="tooth-number">38</td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="work-area"></td><td class="empty"></td><td class="empty"></td></tr>
                        <tr><td class="label no-border" colspan="3">TEMPORARY TEETH</td><td class="symbol">○</td><td class="symbol">○</td><td class="symbol">×</td><td class="symbol">◇</td><td class="symbol">—</td><td class="symbol">—</td><td class="symbol">◇</td><td class="symbol">×</td><td class="symbol">○</td><td class="symbol">○</td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="side-label no-border" colspan="2">RIGHT</td><td class="tooth-number">85</td><td class="tooth-number">84</td><td class="tooth-number">83</td><td class="tooth-number">82</td><td class="tooth-number">81</td><td class="tooth-number">71</td><td class="tooth-number">72</td><td class="tooth-number">73</td><td class="tooth-number">74</td><td class="tooth-number">75</td><td class="side-label no-border" colspan="2">LEFT</td><td class="empty no-border"></td></tr>
                        <tr><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty no-border"></td><td class="empty no-border"></td><td class="empty no-border"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oral Health Condition Table -->
        <div style="margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 8px; line-height: 1.4;">
                <tr>
                    <td colspan="8" style="text-align: center; font-weight: bold; font-size: 9px; border: 2px solid #000; padding: 3px; background-color: #f0f0f0;">
                        ORAL HEALTH CONDITION
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px; font-weight: bold; width: 20%;"></td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">Kinder</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">1/7</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">2/8</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">3/9</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">4/10</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">5/11</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">6/12</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Gingivitis</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Periodontal Disease</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Malocclusion</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Supernumerary teeth</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Retained deciduous teeth</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Decubital ulcer</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Calculus</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Cleft lip / palate</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Root fragment</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Fluorosis</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Others, specify</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
            </table>
        </div>
<br>
        <!-- Temporary and Permanent Teeth Summary Tables -->
        <div style="margin-top: 10px; page-break-before: always;">
            <div>
                <!-- Temporary Teeth Table -->
                <div style="float: left; width: 48%; margin-right: 2%;">
                    <div style="text-align: center; font-weight: bold; font-size: 8px; padding: 3px; margin-bottom: 2px;">
                        TEMPORARY TEETH
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 6px; line-height: 1.3;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; font-weight: bold; width: 25%;"></td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold; font-size: 7px;">dft Index</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">Kinder</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">1</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">2</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">3</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">4</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">5</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; font-weight: bold;">Index d.f.t.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./decayed</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./filled</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total d.f.t.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Extraction</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Filling</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total Sound Teeth</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                </table>
                </div>

                <!-- Permanent Teeth Table -->
                <div style="float: left; width: 48%;">
                    <div style="text-align: center; font-weight: bold; font-size: 8px; padding: 3px; margin-bottom: 2px;">
                        PERMANENT TEETH
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 6px; line-height: 1.3;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; font-weight: bold; width: 25%;"></td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold; font-size: 7px;">dft Index</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">Kinder</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">1/7</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">2/8</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">3/9</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">4/10</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">5/11</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">6/12</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; font-weight: bold;">Index D.M.F.T.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./decayed</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./Missing</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No. T./Filled</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total D.M.F.T.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Extraction</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Filling</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total Sound Teeth</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                </table>
                </div>
            </div>
        </div>

        <!-- Symbol Legend Table -->
        <div style="margin-top: 30px; clear: both;">
            <h3 style="font-size: 10px; font-weight: bold; margin-bottom: 15px; text-align: left;">SYMBOL FOR MOUTH EXAMINATION</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                <tr>
                    <td style="border: none; padding: 3px; width: 5%; font-weight: bold;">X</td>
                    <td style="border: none; padding: 3px; width: 28%;">- Carious tooth indicated for extraction</td>
                    <td style="border: none; padding: 3px; width: 5%; font-weight: bold;">{ }</td>
                    <td style="border: none; padding: 3px; width: 28%;">- Sound/erupted Permanent tooth</td>
                    <td style="border: none; padding: 3px; width: 5%; font-weight: bold;">FB</td>
                    <td style="border: none; padding: 3px; width: 29%;">- Fixed Bridge</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">D</td>
                    <td style="border: none; padding: 3px;">- Carious tooth indicated for filling</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">PFS</td>
                    <td style="border: none; padding: 3px;">- Pit and Fissure Sealant</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">CD</td>
                    <td style="border: none; padding: 3px;">- Complete Denture</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">GI</td>
                    <td style="border: none; padding: 3px;">- Root fragment</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">JC</td>
                    <td style="border: none; padding: 3px;">- Jacket Crown</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">GI</td>
                    <td style="border: none; padding: 3px;">- Glass Ionomer</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">M</td>
                    <td style="border: none; padding: 3px;">- Missing tooth</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">P</td>
                    <td style="border: none; padding: 3px;">- Pontic</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">CO</td>
                    <td style="border: none; padding: 3px;">- Composite</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">F2</td>
                    <td style="border: none; padding: 3px;">- Permanent filled tooth with recurrence of decay</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">RPD</td>
                    <td style="border: none; padding: 3px;">- Removable Partial Denture</td>
                    <td style="border: none; padding: 3px; font-weight: bold;">AM</td>
                    <td style="border: none; padding: 3px;">- Amalgam</td>
                </tr>
            </table>
        </div>

        <!-- Intervention/Treatment Record Table -->
        <div style="margin-top: 30px; clear: both;">
            <h3 style="font-size: 10px; font-weight: bold; margin-bottom: 15px; text-align: center;">INTERVENTION/TREATMENT RECORD</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; width: 10%;">Date</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; width: 25%;">Chief Complaint</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; width: 25%;">Intervention/Treatment Done</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; width: 20%;">Remarks</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; width: 20%;">Attended by(Name/Position)</th>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
