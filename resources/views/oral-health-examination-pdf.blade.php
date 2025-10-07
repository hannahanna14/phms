@php
    // Helper function to get condition data for display
    function getConditionDisplay($oralHealthDataByGrade, $conditionKey, $gradeKey, $gradeDataKey) {
        $gradeData = $oralHealthDataByGrade[$gradeKey] ?? null;
        if (!$gradeData || !$gradeData->conditions) {
            return '';
        }

        $condition = $gradeData->conditions[$conditionKey][$gradeDataKey] ?? null;
        if (!$condition || !$condition['present']) {
            return '';
        }

        $display = '&#10003;'; // HTML entity for check mark
        if (!empty($condition['date'])) {
            $display .= ' (' . date('m/d/y', strtotime($condition['date'])) . ')';
        }
        if ($conditionKey === 'others_specify' && !empty($condition['specification'])) {
            $display .= ' ' . $condition['specification'];
        }

        return $display;
    }

    // Helper function to get index dft data
    function getIndexDftData($oralHealthDataByGrade, $gradeKey, $field) {
        $gradeData = $oralHealthDataByGrade[$gradeKey] ?? null;
        if (!$gradeData) {
            return '';
        }

        $value = $gradeData->$field ?? null;
        return $value !== null ? $value : '';
    }
@endphp

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
                    <td class="temp-k55">{{ $oralHealthDataByGrade['K']->temp_55 ?? '' }}</td>
                    <td class="temp-k54">{{ $oralHealthDataByGrade['K']->temp_54 ?? '' }}</td>
                    <td class="temp-k53">{{ $oralHealthDataByGrade['K']->temp_53 ?? '' }}</td>
                    <td class="temp-k52">{{ $oralHealthDataByGrade['K']->temp_52 ?? '' }}</td>
                    <td class="temp-k51">{{ $oralHealthDataByGrade['K']->temp_51 ?? '' }}</td>
                    <td class="temp-k61">{{ $oralHealthDataByGrade['K']->temp_61 ?? '' }}</td>
                    <td class="temp-k62">{{ $oralHealthDataByGrade['K']->temp_62 ?? '' }}</td>
                    <td class="temp-k63">{{ $oralHealthDataByGrade['K']->temp_63 ?? '' }}</td>
                    <td class="temp-k64">{{ $oralHealthDataByGrade['K']->temp_64 ?? '' }}</td>
                    <td class="temp-k65">{{ $oralHealthDataByGrade['K']->temp_65 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="perm-k18">{{ $oralHealthDataByGrade['K']->perm_18 ?? '' }}</td>
                    <td class="perm-k17">{{ $oralHealthDataByGrade['K']->perm_17 ?? '' }}</td>
                    <td class="perm-k16">{{ $oralHealthDataByGrade['K']->perm_16 ?? '' }}</td>
                    <td class="perm-k15">{{ $oralHealthDataByGrade['K']->perm_15 ?? '' }}</td>
                    <td class="perm-k14">{{ $oralHealthDataByGrade['K']->perm_14 ?? '' }}</td>
                    <td class="perm-k13">{{ $oralHealthDataByGrade['K']->perm_13 ?? '' }}</td>
                    <td class="perm-k12">{{ $oralHealthDataByGrade['K']->perm_12 ?? '' }}</td>
                    <td class="perm-k11">{{ $oralHealthDataByGrade['K']->perm_11 ?? '' }}</td>
                    <td class="perm-k21">{{ $oralHealthDataByGrade['K']->perm_21 ?? '' }}</td>
                    <td class="perm-k22">{{ $oralHealthDataByGrade['K']->perm_22 ?? '' }}</td>
                    <td class="perm-k23">{{ $oralHealthDataByGrade['K']->perm_23 ?? '' }}</td>
                    <td class="perm-k24">{{ $oralHealthDataByGrade['K']->perm_24 ?? '' }}</td>
                    <td class="perm-k25">{{ $oralHealthDataByGrade['K']->perm_25 ?? '' }}</td>
                    <td class="perm-k26">{{ $oralHealthDataByGrade['K']->perm_26 ?? '' }}</td>
                    <td class="perm-k27">{{ $oralHealthDataByGrade['K']->perm_27 ?? '' }}</td>
                    <td class="perm-k28">{{ $oralHealthDataByGrade['K']->perm_28 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                </tr>

                <!-- Row 8: Lower permanent teeth symbols -->
                <tr>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="perm-k48">{{ $oralHealthDataByGrade['K']->perm_48 ?? '' }}</td>
                    <td class="perm-k47">{{ $oralHealthDataByGrade['K']->perm_47 ?? '' }}</td>
                    <td class="perm-k46">{{ $oralHealthDataByGrade['K']->perm_46 ?? '' }}</td>
                    <td class="perm-k45">{{ $oralHealthDataByGrade['K']->perm_45 ?? '' }}</td>
                    <td class="perm-k44">{{ $oralHealthDataByGrade['K']->perm_44 ?? '' }}</td>
                    <td class="perm-k43">{{ $oralHealthDataByGrade['K']->perm_43 ?? '' }}</td>
                    <td class="perm-k42">{{ $oralHealthDataByGrade['K']->perm_42 ?? '' }}</td>
                    <td class="perm-k41">{{ $oralHealthDataByGrade['K']->perm_41 ?? '' }}</td>
                    <td class="perm-k31">{{ $oralHealthDataByGrade['K']->perm_31 ?? '' }}</td>
                    <td class="perm-k32">{{ $oralHealthDataByGrade['K']->perm_32 ?? '' }}</td>
                    <td class="perm-k33">{{ $oralHealthDataByGrade['K']->perm_33 ?? '' }}</td>
                    <td class="perm-k34">{{ $oralHealthDataByGrade['K']->perm_34 ?? '' }}</td>
                    <td class="perm-k35">{{ $oralHealthDataByGrade['K']->perm_35 ?? '' }}</td>
                    <td class="perm-k36">{{ $oralHealthDataByGrade['K']->perm_36 ?? '' }}</td>
                    <td class="perm-k37">{{ $oralHealthDataByGrade['K']->perm_37 ?? '' }}</td>
                    <td class="perm-k38">{{ $oralHealthDataByGrade['K']->perm_38 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="temp-k85">{{ $oralHealthDataByGrade['K']->temp_85 ?? '' }}</td>
                    <td class="temp-k84">{{ $oralHealthDataByGrade['K']->temp_84 ?? '' }}</td>
                    <td class="temp-k83">{{ $oralHealthDataByGrade['K']->temp_83 ?? '' }}</td>
                    <td class="temp-k82">{{ $oralHealthDataByGrade['K']->temp_82 ?? '' }}</td>
                    <td class="temp-k81">{{ $oralHealthDataByGrade['K']->temp_81 ?? '' }}</td>
                    <td class="temp-k71">{{ $oralHealthDataByGrade['K']->temp_71 ?? '' }}</td>
                    <td class="temp-k72">{{ $oralHealthDataByGrade['K']->temp_72 ?? '' }}</td>
                    <td class="temp-k73">{{ $oralHealthDataByGrade['K']->temp_73 ?? '' }}</td>
                    <td class="temp-k74">{{ $oralHealthDataByGrade['K']->temp_74 ?? '' }}</td>
                    <td class="temp-k75">{{ $oralHealthDataByGrade['K']->temp_75 ?? '' }}</td>
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
                    <td class="temp-g1-55">{{ $oralHealthDataByGrade['1']->temp_55 ?? '' }}</td>
                    <td class="temp-g1-54">{{ $oralHealthDataByGrade['1']->temp_54 ?? '' }}</td>
                    <td class="temp-g1-53">{{ $oralHealthDataByGrade['1']->temp_53 ?? '' }}</td>
                    <td class="temp-g1-52">{{ $oralHealthDataByGrade['1']->temp_52 ?? '' }}</td>
                    <td class="temp-g1-51">{{ $oralHealthDataByGrade['1']->temp_51 ?? '' }}</td>
                    <td class="temp-g1-61">{{ $oralHealthDataByGrade['1']->temp_61 ?? '' }}</td>
                    <td class="temp-g1-62">{{ $oralHealthDataByGrade['1']->temp_62 ?? '' }}</td>
                    <td class="temp-g1-63">{{ $oralHealthDataByGrade['1']->temp_63 ?? '' }}</td>
                    <td class="temp-g1-64">{{ $oralHealthDataByGrade['1']->temp_64 ?? '' }}</td>
                    <td class="temp-g1-65">{{ $oralHealthDataByGrade['1']->temp_65 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="perm-g1-18">{{ $oralHealthDataByGrade['1']->perm_18 ?? '' }}</td>
                    <td class="perm-g1-17">{{ $oralHealthDataByGrade['1']->perm_17 ?? '' }}</td>
                    <td class="perm-g1-16">{{ $oralHealthDataByGrade['1']->perm_16 ?? '' }}</td>
                    <td class="perm-g1-15">{{ $oralHealthDataByGrade['1']->perm_15 ?? '' }}</td>
                    <td class="perm-g1-14">{{ $oralHealthDataByGrade['1']->perm_14 ?? '' }}</td>
                    <td class="perm-g1-13">{{ $oralHealthDataByGrade['1']->perm_13 ?? '' }}</td>
                    <td class="perm-g1-12">{{ $oralHealthDataByGrade['1']->perm_12 ?? '' }}</td>
                    <td class="perm-g1-11">{{ $oralHealthDataByGrade['1']->perm_11 ?? '' }}</td>
                    <td class="perm-g1-21">{{ $oralHealthDataByGrade['1']->perm_21 ?? '' }}</td>
                    <td class="perm-g1-22">{{ $oralHealthDataByGrade['1']->perm_22 ?? '' }}</td>
                    <td class="perm-g1-23">{{ $oralHealthDataByGrade['1']->perm_23 ?? '' }}</td>
                    <td class="perm-g1-24">{{ $oralHealthDataByGrade['1']->perm_24 ?? '' }}</td>
                    <td class="perm-g1-25">{{ $oralHealthDataByGrade['1']->perm_25 ?? '' }}</td>
                    <td class="perm-g1-26">{{ $oralHealthDataByGrade['1']->perm_26 ?? '' }}</td>
                    <td class="perm-g1-27">{{ $oralHealthDataByGrade['1']->perm_27 ?? '' }}</td>
                    <td class="perm-g1-28">{{ $oralHealthDataByGrade['1']->perm_28 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                </tr>
                <tr>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="perm-g1-48">{{ $oralHealthDataByGrade['1']->perm_48 ?? '' }}</td>
                    <td class="perm-g1-47">{{ $oralHealthDataByGrade['1']->perm_47 ?? '' }}</td>
                    <td class="perm-g1-46">{{ $oralHealthDataByGrade['1']->perm_46 ?? '' }}</td>
                    <td class="perm-g1-45">{{ $oralHealthDataByGrade['1']->perm_45 ?? '' }}</td>
                    <td class="perm-g1-44">{{ $oralHealthDataByGrade['1']->perm_44 ?? '' }}</td>
                    <td class="perm-g1-43">{{ $oralHealthDataByGrade['1']->perm_43 ?? '' }}</td>
                    <td class="perm-g1-42">{{ $oralHealthDataByGrade['1']->perm_42 ?? '' }}</td>
                    <td class="perm-g1-41">{{ $oralHealthDataByGrade['1']->perm_41 ?? '' }}</td>
                    <td class="perm-g1-31">{{ $oralHealthDataByGrade['1']->perm_31 ?? '' }}</td>
                    <td class="perm-g1-32">{{ $oralHealthDataByGrade['1']->perm_32 ?? '' }}</td>
                    <td class="perm-g1-33">{{ $oralHealthDataByGrade['1']->perm_33 ?? '' }}</td>
                    <td class="perm-g1-34">{{ $oralHealthDataByGrade['1']->perm_34 ?? '' }}</td>
                    <td class="perm-g1-35">{{ $oralHealthDataByGrade['1']->perm_35 ?? '' }}</td>
                    <td class="perm-g1-36">{{ $oralHealthDataByGrade['1']->perm_36 ?? '' }}</td>
                    <td class="perm-g1-37">{{ $oralHealthDataByGrade['1']->perm_37 ?? '' }}</td>
                    <td class="perm-g1-38">{{ $oralHealthDataByGrade['1']->perm_38 ?? '' }}</td>
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
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&#8212;</td>
                    <td class="symbol">&lt;&gt;</td>
                    <td class="symbol">&#215;</td>
                    <td class="symbol">O</td>
                    <td class="symbol">O</td>
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
                    <td class="temp-g1-85">{{ $oralHealthDataByGrade['4']->temp_85 ?? '' }}</td>
                    <td class="temp-g1-84">{{ $oralHealthDataByGrade['4']->temp_84 ?? '' }}</td>
                    <td class="temp-g1-83">{{ $oralHealthDataByGrade['4']->temp_83 ?? '' }}</td>
                    <td class="temp-g1-82">{{ $oralHealthDataByGrade['4']->temp_82 ?? '' }}</td>
                    <td class="temp-g1-81">{{ $oralHealthDataByGrade['4']->temp_81 ?? '' }}</td>
                    <td class="temp-g1-71">{{ $oralHealthDataByGrade['4']->temp_71 ?? '' }}</td>
                    <td class="temp-g1-72">{{ $oralHealthDataByGrade['4']->temp_72 ?? '' }}</td>
                    <td class="temp-g1-73">{{ $oralHealthDataByGrade['4']->temp_73 ?? '' }}</td>
                    <td class="temp-g1-74">{{ $oralHealthDataByGrade['4']->temp_74 ?? '' }}</td>
                    <td class="temp-g1-75">{{ $oralHealthDataByGrade['4']->temp_75 ?? '' }}</td>
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
                            <td class="temp-g2-55">{{ $oralHealthDataByGrade['2']->temp_55 ?? '' }}</td>
                            <td class="temp-g2-54">{{ $oralHealthDataByGrade['2']->temp_54 ?? '' }}</td>
                            <td class="temp-g2-53">{{ $oralHealthDataByGrade['2']->temp_53 ?? '' }}</td>
                            <td class="temp-g2-52">{{ $oralHealthDataByGrade['2']->temp_52 ?? '' }}</td>
                            <td class="temp-g2-51">{{ $oralHealthDataByGrade['2']->temp_51 ?? '' }}</td>
                            <td class="temp-g2-61">{{ $oralHealthDataByGrade['2']->temp_61 ?? '' }}</td>
                            <td class="temp-g2-62">{{ $oralHealthDataByGrade['2']->temp_62 ?? '' }}</td>
                            <td class="temp-g2-63">{{ $oralHealthDataByGrade['2']->temp_63 ?? '' }}</td>
                            <td class="temp-g2-64">{{ $oralHealthDataByGrade['2']->temp_64 ?? '' }}</td>
                            <td class="temp-g2-65">{{ $oralHealthDataByGrade['2']->temp_65 ?? '' }}</td>
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="perm-g2-18">{{ $oralHealthDataByGrade['2']->perm_18 ?? '' }}</td>
                            <td class="perm-g2-17">{{ $oralHealthDataByGrade['2']->perm_17 ?? '' }}</td>
                            <td class="perm-g2-16">{{ $oralHealthDataByGrade['2']->perm_16 ?? '' }}</td>
                            <td class="perm-g2-15">{{ $oralHealthDataByGrade['2']->perm_15 ?? '' }}</td>
                            <td class="perm-g2-14">{{ $oralHealthDataByGrade['2']->perm_14 ?? '' }}</td>
                            <td class="perm-g2-13">{{ $oralHealthDataByGrade['2']->perm_13 ?? '' }}</td>
                            <td class="perm-g2-12">{{ $oralHealthDataByGrade['2']->perm_12 ?? '' }}</td>
                            <td class="perm-g2-11">{{ $oralHealthDataByGrade['2']->perm_11 ?? '' }}</td>
                            <td class="perm-g2-21">{{ $oralHealthDataByGrade['2']->perm_21 ?? '' }}</td>
                            <td class="perm-g2-22">{{ $oralHealthDataByGrade['2']->perm_22 ?? '' }}</td>
                            <td class="perm-g2-23">{{ $oralHealthDataByGrade['2']->perm_23 ?? '' }}</td>
                            <td class="perm-g2-24">{{ $oralHealthDataByGrade['2']->perm_24 ?? '' }}</td>
                            <td class="perm-g2-25">{{ $oralHealthDataByGrade['2']->perm_25 ?? '' }}</td>
                            <td class="perm-g2-26">{{ $oralHealthDataByGrade['2']->perm_26 ?? '' }}</td>
                            <td class="perm-g2-27">{{ $oralHealthDataByGrade['2']->perm_27 ?? '' }}</td>
                            <td class="perm-g2-28">{{ $oralHealthDataByGrade['2']->perm_28 ?? '' }}</td>
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                        </tr>
                        <tr>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="perm-g2-48">{{ $oralHealthDataByGrade['2']->perm_48 ?? '' }}</td>
                            <td class="perm-g2-47">{{ $oralHealthDataByGrade['2']->perm_47 ?? '' }}</td>
                            <td class="perm-g2-46">{{ $oralHealthDataByGrade['2']->perm_46 ?? '' }}</td>
                            <td class="perm-g2-45">{{ $oralHealthDataByGrade['2']->perm_45 ?? '' }}</td>
                            <td class="perm-g2-44">{{ $oralHealthDataByGrade['2']->perm_44 ?? '' }}</td>
                            <td class="perm-g2-43">{{ $oralHealthDataByGrade['2']->perm_43 ?? '' }}</td>
                            <td class="perm-g2-42">{{ $oralHealthDataByGrade['2']->perm_42 ?? '' }}</td>
                            <td class="perm-g2-41">{{ $oralHealthDataByGrade['2']->perm_41 ?? '' }}</td>
                            <td class="perm-g2-31">{{ $oralHealthDataByGrade['2']->perm_31 ?? '' }}</td>
                            <td class="perm-g2-32">{{ $oralHealthDataByGrade['2']->perm_32 ?? '' }}</td>
                            <td class="perm-g2-33">{{ $oralHealthDataByGrade['2']->perm_33 ?? '' }}</td>
                            <td class="perm-g2-34">{{ $oralHealthDataByGrade['2']->perm_34 ?? '' }}</td>
                            <td class="perm-g2-35">{{ $oralHealthDataByGrade['2']->perm_35 ?? '' }}</td>
                            <td class="perm-g2-36">{{ $oralHealthDataByGrade['2']->perm_36 ?? '' }}</td>
                            <td class="perm-g2-37">{{ $oralHealthDataByGrade['2']->perm_37 ?? '' }}</td>
                            <td class="perm-g2-38">{{ $oralHealthDataByGrade['2']->perm_38 ?? '' }}</td>
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="temp-g2-85">{{ $oralHealthDataByGrade['2']->temp_85 ?? '' }}</td>
                            <td class="temp-g2-84">{{ $oralHealthDataByGrade['2']->temp_84 ?? '' }}</td>
                            <td class="temp-g2-83">{{ $oralHealthDataByGrade['2']->temp_83 ?? '' }}</td>
                            <td class="temp-g2-82">{{ $oralHealthDataByGrade['2']->temp_82 ?? '' }}</td>
                            <td class="temp-g2-81">{{ $oralHealthDataByGrade['2']->temp_81 ?? '' }}</td>
                            <td class="temp-g2-71">{{ $oralHealthDataByGrade['2']->temp_71 ?? '' }}</td>
                            <td class="temp-g2-72">{{ $oralHealthDataByGrade['2']->temp_72 ?? '' }}</td>
                            <td class="temp-g2-73">{{ $oralHealthDataByGrade['2']->temp_73 ?? '' }}</td>
                            <td class="temp-g2-74">{{ $oralHealthDataByGrade['2']->temp_74 ?? '' }}</td>
                            <td class="temp-g2-75">{{ $oralHealthDataByGrade['2']->temp_75 ?? '' }}</td>
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
                            <td class="temp-g3-55">{{ $oralHealthDataByGrade['3']->temp_55 ?? '' }}</td>
                            <td class="temp-g3-54">{{ $oralHealthDataByGrade['3']->temp_54 ?? '' }}</td>
                            <td class="temp-g3-53">{{ $oralHealthDataByGrade['3']->temp_53 ?? '' }}</td>
                            <td class="temp-g3-52">{{ $oralHealthDataByGrade['3']->temp_52 ?? '' }}</td>
                            <td class="temp-g3-51">{{ $oralHealthDataByGrade['3']->temp_51 ?? '' }}</td>
                            <td class="temp-g3-61">{{ $oralHealthDataByGrade['3']->temp_61 ?? '' }}</td>
                            <td class="temp-g3-62">{{ $oralHealthDataByGrade['3']->temp_62 ?? '' }}</td>
                            <td class="temp-g3-63">{{ $oralHealthDataByGrade['3']->temp_63 ?? '' }}</td>
                            <td class="temp-g3-64">{{ $oralHealthDataByGrade['3']->temp_64 ?? '' }}</td>
                            <td class="temp-g3-65">{{ $oralHealthDataByGrade['3']->temp_65 ?? '' }}</td>
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="perm-g3-18">{{ $oralHealthDataByGrade['3']->perm_18 ?? '' }}</td>
                            <td class="perm-g3-17">{{ $oralHealthDataByGrade['3']->perm_17 ?? '' }}</td>
                            <td class="perm-g3-16">{{ $oralHealthDataByGrade['3']->perm_16 ?? '' }}</td>
                            <td class="perm-g3-15">{{ $oralHealthDataByGrade['3']->perm_15 ?? '' }}</td>
                            <td class="perm-g3-14">{{ $oralHealthDataByGrade['3']->perm_14 ?? '' }}</td>
                            <td class="perm-g3-13">{{ $oralHealthDataByGrade['3']->perm_13 ?? '' }}</td>
                            <td class="perm-g3-12">{{ $oralHealthDataByGrade['3']->perm_12 ?? '' }}</td>
                            <td class="perm-g3-11">{{ $oralHealthDataByGrade['3']->perm_11 ?? '' }}</td>
                            <td class="perm-g3-21">{{ $oralHealthDataByGrade['3']->perm_21 ?? '' }}</td>
                            <td class="perm-g3-22">{{ $oralHealthDataByGrade['3']->perm_22 ?? '' }}</td>
                            <td class="perm-g3-23">{{ $oralHealthDataByGrade['3']->perm_23 ?? '' }}</td>
                            <td class="perm-g3-24">{{ $oralHealthDataByGrade['3']->perm_24 ?? '' }}</td>
                            <td class="perm-g3-25">{{ $oralHealthDataByGrade['3']->perm_25 ?? '' }}</td>
                            <td class="perm-g3-26">{{ $oralHealthDataByGrade['3']->perm_26 ?? '' }}</td>
                            <td class="perm-g3-27">{{ $oralHealthDataByGrade['3']->perm_27 ?? '' }}</td>
                            <td class="perm-g3-28">{{ $oralHealthDataByGrade['3']->perm_28 ?? '' }}</td>

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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                        </tr>
                        <tr>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="perm-g3-48">{{ $oralHealthDataByGrade['3']->perm_48 ?? '' }}</td>
                            <td class="perm-g3-47">{{ $oralHealthDataByGrade['3']->perm_47 ?? '' }}</td>
                            <td class="perm-g3-46">{{ $oralHealthDataByGrade['3']->perm_46 ?? '' }}</td>
                            <td class="perm-g3-45">{{ $oralHealthDataByGrade['3']->perm_45 ?? '' }}</td>
                            <td class="perm-g3-44">{{ $oralHealthDataByGrade['3']->perm_44 ?? '' }}</td>
                            <td class="perm-g3-43">{{ $oralHealthDataByGrade['3']->perm_43 ?? '' }}</td>
                            <td class="perm-g3-42">{{ $oralHealthDataByGrade['3']->perm_42 ?? '' }}</td>
                            <td class="perm-g3-41">{{ $oralHealthDataByGrade['3']->perm_41 ?? '' }}</td>
                            <td class="perm-g3-31">{{ $oralHealthDataByGrade['3']->perm_31 ?? '' }}</td>
                            <td class="perm-g3-32">{{ $oralHealthDataByGrade['3']->perm_32 ?? '' }}</td>
                            <td class="perm-g3-33">{{ $oralHealthDataByGrade['3']->perm_33 ?? '' }}</td>
                            <td class="perm-g3-34">{{ $oralHealthDataByGrade['3']->perm_34 ?? '' }}</td>
                            <td class="perm-g3-35">{{ $oralHealthDataByGrade['3']->perm_35 ?? '' }}</td>
                            <td class="perm-g3-36">{{ $oralHealthDataByGrade['3']->perm_36 ?? '' }}</td>
                            <td class="perm-g3-37">{{ $oralHealthDataByGrade['3']->perm_37 ?? '' }}</td>
                            <td class="perm-g3-38">{{ $oralHealthDataByGrade['3']->perm_38 ?? '' }}</td>
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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
                            <td class="temp-g3-85">{{ $oralHealthDataByGrade['3']->temp_85 ?? '' }}</td>
                            <td class="temp-g3-84">{{ $oralHealthDataByGrade['3']->temp_84 ?? '' }}</td>
                            <td class="temp-g3-83">{{ $oralHealthDataByGrade['3']->temp_83 ?? '' }}</td>
                            <td class="temp-g3-82">{{ $oralHealthDataByGrade['3']->temp_82 ?? '' }}</td>
                            <td class="temp-g3-81">{{ $oralHealthDataByGrade['3']->temp_81 ?? '' }}</td>
                            <td class="temp-g3-71">{{ $oralHealthDataByGrade['3']->temp_71 ?? '' }}</td>
                            <td class="temp-g3-72">{{ $oralHealthDataByGrade['3']->temp_72 ?? '' }}</td>
                            <td class="temp-g3-73">{{ $oralHealthDataByGrade['3']->temp_73 ?? '' }}</td>
                            <td class="temp-g3-74">{{ $oralHealthDataByGrade['3']->temp_74 ?? '' }}</td>
                            <td class="temp-g3-75">{{ $oralHealthDataByGrade['3']->temp_75 ?? '' }}</td>
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
                        <!-- Grade 4 Dental Chart -->
                        <!-- Row 1: Temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g4-55">{{ $oralHealthDataByGrade['4']->temp_55 ?? '' }}</td>
                            <td class="temp-g4-54">{{ $oralHealthDataByGrade['4']->temp_54 ?? '' }}</td>
                            <td class="temp-g4-53">{{ $oralHealthDataByGrade['4']->temp_53 ?? '' }}</td>
                            <td class="temp-g4-52">{{ $oralHealthDataByGrade['4']->temp_52 ?? '' }}</td>
                            <td class="temp-g4-51">{{ $oralHealthDataByGrade['4']->temp_51 ?? '' }}</td>
                            <td class="temp-g4-61">{{ $oralHealthDataByGrade['4']->temp_61 ?? '' }}</td>
                            <td class="temp-g4-62">{{ $oralHealthDataByGrade['4']->temp_62 ?? '' }}</td>
                            <td class="temp-g4-63">{{ $oralHealthDataByGrade['4']->temp_63 ?? '' }}</td>
                            <td class="temp-g4-64">{{ $oralHealthDataByGrade['4']->temp_64 ?? '' }}</td>
                            <td class="temp-g4-65">{{ $oralHealthDataByGrade['4']->temp_65 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 2: Temporary teeth numbers -->
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

                        <!-- Row 3: Temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 5: Upper permanent teeth data -->
                        <tr>
                            <td class="perm-g4-18">{{ $oralHealthDataByGrade['4']->perm_18 ?? '' }}</td>
                            <td class="perm-g4-17">{{ $oralHealthDataByGrade['4']->perm_17 ?? '' }}</td>
                            <td class="perm-g4-16">{{ $oralHealthDataByGrade['4']->perm_16 ?? '' }}</td>
                            <td class="perm-g4-15">{{ $oralHealthDataByGrade['4']->perm_15 ?? '' }}</td>
                            <td class="perm-g4-14">{{ $oralHealthDataByGrade['4']->perm_14 ?? '' }}</td>
                            <td class="perm-g4-13">{{ $oralHealthDataByGrade['4']->perm_13 ?? '' }}</td>
                            <td class="perm-g4-12">{{ $oralHealthDataByGrade['4']->perm_12 ?? '' }}</td>
                            <td class="perm-g4-11">{{ $oralHealthDataByGrade['4']->perm_11 ?? '' }}</td>
                            <td class="perm-g4-21">{{ $oralHealthDataByGrade['4']->perm_21 ?? '' }}</td>
                            <td class="perm-g4-22">{{ $oralHealthDataByGrade['4']->perm_22 ?? '' }}</td>
                            <td class="perm-g4-23">{{ $oralHealthDataByGrade['4']->perm_23 ?? '' }}</td>
                            <td class="perm-g4-24">{{ $oralHealthDataByGrade['4']->perm_24 ?? '' }}</td>
                            <td class="perm-g4-25">{{ $oralHealthDataByGrade['4']->perm_25 ?? '' }}</td>
                            <td class="perm-g4-26">{{ $oralHealthDataByGrade['4']->perm_26 ?? '' }}</td>
                            <td class="perm-g4-27">{{ $oralHealthDataByGrade['4']->perm_27 ?? '' }}</td>
                            <td class="perm-g4-28">{{ $oralHealthDataByGrade['4']->perm_28 ?? '' }}</td>
                        </tr>

                        <!-- Row 6: Upper permanent teeth numbers -->
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                        </tr>

                        <!-- Row 8: Lower permanent teeth symbols -->
                        <tr>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 10: Lower permanent teeth data -->
                        <tr>
                            <td class="perm-g4-48">{{ $oralHealthDataByGrade['4']->perm_48 ?? '' }}</td>
                            <td class="perm-g4-47">{{ $oralHealthDataByGrade['4']->perm_47 ?? '' }}</td>
                            <td class="perm-g4-46">{{ $oralHealthDataByGrade['4']->perm_46 ?? '' }}</td>
                            <td class="perm-g4-45">{{ $oralHealthDataByGrade['4']->perm_45 ?? '' }}</td>
                            <td class="perm-g4-44">{{ $oralHealthDataByGrade['4']->perm_44 ?? '' }}</td>
                            <td class="perm-g4-43">{{ $oralHealthDataByGrade['4']->perm_43 ?? '' }}</td>
                            <td class="perm-g4-42">{{ $oralHealthDataByGrade['4']->perm_42 ?? '' }}</td>
                            <td class="perm-g4-41">{{ $oralHealthDataByGrade['4']->perm_41 ?? '' }}</td>
                            <td class="perm-g4-31">{{ $oralHealthDataByGrade['4']->perm_31 ?? '' }}</td>
                            <td class="perm-g4-32">{{ $oralHealthDataByGrade['4']->perm_32 ?? '' }}</td>
                            <td class="perm-g4-33">{{ $oralHealthDataByGrade['4']->perm_33 ?? '' }}</td>
                            <td class="perm-g4-34">{{ $oralHealthDataByGrade['4']->perm_34 ?? '' }}</td>
                            <td class="perm-g4-35">{{ $oralHealthDataByGrade['4']->perm_35 ?? '' }}</td>
                            <td class="perm-g4-36">{{ $oralHealthDataByGrade['4']->perm_36 ?? '' }}</td>
                            <td class="perm-g4-37">{{ $oralHealthDataByGrade['4']->perm_37 ?? '' }}</td>
                            <td class="perm-g4-38">{{ $oralHealthDataByGrade['4']->perm_38 ?? '' }}</td>
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

                        <!-- Row 12: Bottom temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 13: Bottom temporary teeth numbers -->
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

                        <!-- Row 14: Bottom temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g4-85">{{ $oralHealthDataByGrade['4']->temp_85 ?? '' }}</td>
                            <td class="temp-g4-84">{{ $oralHealthDataByGrade['4']->temp_84 ?? '' }}</td>
                            <td class="temp-g4-83">{{ $oralHealthDataByGrade['4']->temp_83 ?? '' }}</td>
                            <td class="temp-g4-82">{{ $oralHealthDataByGrade['4']->temp_82 ?? '' }}</td>
                            <td class="temp-g4-81">{{ $oralHealthDataByGrade['4']->temp_81 ?? '' }}</td>
                            <td class="temp-g4-71">{{ $oralHealthDataByGrade['4']->temp_71 ?? '' }}</td>
                            <td class="temp-g4-72">{{ $oralHealthDataByGrade['4']->temp_72 ?? '' }}</td>
                            <td class="temp-g4-73">{{ $oralHealthDataByGrade['4']->temp_73 ?? '' }}</td>
                            <td class="temp-g4-74">{{ $oralHealthDataByGrade['4']->temp_74 ?? '' }}</td>
                            <td class="temp-g4-75">{{ $oralHealthDataByGrade['4']->temp_75 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
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
                        <!-- Grade 5 Dental Chart -->
                        <!-- Row 1: Temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g5-55">{{ $oralHealthDataByGrade['5']->temp_55 ?? '' }}</td>
                            <td class="temp-g5-54">{{ $oralHealthDataByGrade['5']->temp_54 ?? '' }}</td>
                            <td class="temp-g5-53">{{ $oralHealthDataByGrade['5']->temp_53 ?? '' }}</td>
                            <td class="temp-g5-52">{{ $oralHealthDataByGrade['5']->temp_52 ?? '' }}</td>
                            <td class="temp-g5-51">{{ $oralHealthDataByGrade['5']->temp_51 ?? '' }}</td>
                            <td class="temp-g5-61">{{ $oralHealthDataByGrade['5']->temp_61 ?? '' }}</td>
                            <td class="temp-g5-62">{{ $oralHealthDataByGrade['5']->temp_62 ?? '' }}</td>
                            <td class="temp-g5-63">{{ $oralHealthDataByGrade['5']->temp_63 ?? '' }}</td>
                            <td class="temp-g5-64">{{ $oralHealthDataByGrade['5']->temp_64 ?? '' }}</td>
                            <td class="temp-g5-65">{{ $oralHealthDataByGrade['5']->temp_65 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 2: Temporary teeth numbers -->
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

                        <!-- Row 3: Temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 5: Upper permanent teeth data -->
                        <tr>
                            <td class="perm-g5-18">{{ $oralHealthDataByGrade['5']->perm_18 ?? '' }}</td>
                            <td class="perm-g5-17">{{ $oralHealthDataByGrade['5']->perm_17 ?? '' }}</td>
                            <td class="perm-g5-16">{{ $oralHealthDataByGrade['5']->perm_16 ?? '' }}</td>
                            <td class="perm-g5-15">{{ $oralHealthDataByGrade['5']->perm_15 ?? '' }}</td>
                            <td class="perm-g5-14">{{ $oralHealthDataByGrade['5']->perm_14 ?? '' }}</td>
                            <td class="perm-g5-13">{{ $oralHealthDataByGrade['5']->perm_13 ?? '' }}</td>
                            <td class="perm-g5-12">{{ $oralHealthDataByGrade['5']->perm_12 ?? '' }}</td>
                            <td class="perm-g5-11">{{ $oralHealthDataByGrade['5']->perm_11 ?? '' }}</td>
                            <td class="perm-g5-21">{{ $oralHealthDataByGrade['5']->perm_21 ?? '' }}</td>
                            <td class="perm-g5-22">{{ $oralHealthDataByGrade['5']->perm_22 ?? '' }}</td>
                            <td class="perm-g5-23">{{ $oralHealthDataByGrade['5']->perm_23 ?? '' }}</td>
                            <td class="perm-g5-24">{{ $oralHealthDataByGrade['5']->perm_24 ?? '' }}</td>
                            <td class="perm-g5-25">{{ $oralHealthDataByGrade['5']->perm_25 ?? '' }}</td>
                            <td class="perm-g5-26">{{ $oralHealthDataByGrade['5']->perm_26 ?? '' }}</td>
                            <td class="perm-g5-27">{{ $oralHealthDataByGrade['5']->perm_27 ?? '' }}</td>
                            <td class="perm-g5-28">{{ $oralHealthDataByGrade['5']->perm_28 ?? '' }}</td>
                        </tr>

                        <!-- Row 6: Upper permanent teeth numbers -->
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                        </tr>

                        <!-- Row 8: Lower permanent teeth symbols -->
                        <tr>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 10: Lower permanent teeth data -->
                        <tr>
                            <td class="perm-g5-48">{{ $oralHealthDataByGrade['5']->perm_48 ?? '' }}</td>
                            <td class="perm-g5-47">{{ $oralHealthDataByGrade['5']->perm_47 ?? '' }}</td>
                            <td class="perm-g5-46">{{ $oralHealthDataByGrade['5']->perm_46 ?? '' }}</td>
                            <td class="perm-g5-45">{{ $oralHealthDataByGrade['5']->perm_45 ?? '' }}</td>
                            <td class="perm-g5-44">{{ $oralHealthDataByGrade['5']->perm_44 ?? '' }}</td>
                            <td class="perm-g5-43">{{ $oralHealthDataByGrade['5']->perm_43 ?? '' }}</td>
                            <td class="perm-g5-42">{{ $oralHealthDataByGrade['5']->perm_42 ?? '' }}</td>
                            <td class="perm-g5-41">{{ $oralHealthDataByGrade['5']->perm_41 ?? '' }}</td>
                            <td class="perm-g5-31">{{ $oralHealthDataByGrade['5']->perm_31 ?? '' }}</td>
                            <td class="perm-g5-32">{{ $oralHealthDataByGrade['5']->perm_32 ?? '' }}</td>
                            <td class="perm-g5-33">{{ $oralHealthDataByGrade['5']->perm_33 ?? '' }}</td>
                            <td class="perm-g5-34">{{ $oralHealthDataByGrade['5']->perm_34 ?? '' }}</td>
                            <td class="perm-g5-35">{{ $oralHealthDataByGrade['5']->perm_35 ?? '' }}</td>
                            <td class="perm-g5-36">{{ $oralHealthDataByGrade['5']->perm_36 ?? '' }}</td>
                            <td class="perm-g5-37">{{ $oralHealthDataByGrade['5']->perm_37 ?? '' }}</td>
                            <td class="perm-g5-38">{{ $oralHealthDataByGrade['5']->perm_38 ?? '' }}</td>
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

                        <!-- Row 12: Bottom temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 13: Bottom temporary teeth numbers -->
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

                        <!-- Row 14: Bottom temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g5-85">{{ $oralHealthDataByGrade['5']->temp_85 ?? '' }}</td>
                            <td class="temp-g5-84">{{ $oralHealthDataByGrade['5']->temp_84 ?? '' }}</td>
                            <td class="temp-g5-83">{{ $oralHealthDataByGrade['5']->temp_83 ?? '' }}</td>
                            <td class="temp-g5-82">{{ $oralHealthDataByGrade['5']->temp_82 ?? '' }}</td>
                            <td class="temp-g5-81">{{ $oralHealthDataByGrade['5']->temp_81 ?? '' }}</td>
                            <td class="temp-g5-71">{{ $oralHealthDataByGrade['5']->temp_71 ?? '' }}</td>
                            <td class="temp-g5-72">{{ $oralHealthDataByGrade['5']->temp_72 ?? '' }}</td>
                            <td class="temp-g5-73">{{ $oralHealthDataByGrade['5']->temp_73 ?? '' }}</td>
                            <td class="temp-g5-74">{{ $oralHealthDataByGrade['5']->temp_74 ?? '' }}</td>
                            <td class="temp-g5-75">{{ $oralHealthDataByGrade['5']->temp_75 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
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
                        <!-- Grade 6 Dental Chart -->
                        <!-- Row 1: Temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g6-55">{{ $oralHealthDataByGrade['6']->temp_55 ?? '' }}</td>
                            <td class="temp-g6-54">{{ $oralHealthDataByGrade['6']->temp_54 ?? '' }}</td>
                            <td class="temp-g6-53">{{ $oralHealthDataByGrade['6']->temp_53 ?? '' }}</td>
                            <td class="temp-g6-52">{{ $oralHealthDataByGrade['6']->temp_52 ?? '' }}</td>
                            <td class="temp-g6-51">{{ $oralHealthDataByGrade['6']->temp_51 ?? '' }}</td>
                            <td class="temp-g6-61">{{ $oralHealthDataByGrade['6']->temp_61 ?? '' }}</td>
                            <td class="temp-g6-62">{{ $oralHealthDataByGrade['6']->temp_62 ?? '' }}</td>
                            <td class="temp-g6-63">{{ $oralHealthDataByGrade['6']->temp_63 ?? '' }}</td>
                            <td class="temp-g6-64">{{ $oralHealthDataByGrade['6']->temp_64 ?? '' }}</td>
                            <td class="temp-g6-65">{{ $oralHealthDataByGrade['6']->temp_65 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 2: Temporary teeth numbers -->
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

                        <!-- Row 3: Temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 5: Upper permanent teeth data -->
                        <tr>
                            <td class="perm-g6-18">{{ $oralHealthDataByGrade['6']->perm_18 ?? '' }}</td>
                            <td class="perm-g6-17">{{ $oralHealthDataByGrade['6']->perm_17 ?? '' }}</td>
                            <td class="perm-g6-16">{{ $oralHealthDataByGrade['6']->perm_16 ?? '' }}</td>
                            <td class="perm-g6-15">{{ $oralHealthDataByGrade['6']->perm_15 ?? '' }}</td>
                            <td class="perm-g6-14">{{ $oralHealthDataByGrade['6']->perm_14 ?? '' }}</td>
                            <td class="perm-g6-13">{{ $oralHealthDataByGrade['6']->perm_13 ?? '' }}</td>
                            <td class="perm-g6-12">{{ $oralHealthDataByGrade['6']->perm_12 ?? '' }}</td>
                            <td class="perm-g6-11">{{ $oralHealthDataByGrade['6']->perm_11 ?? '' }}</td>
                            <td class="perm-g6-21">{{ $oralHealthDataByGrade['6']->perm_21 ?? '' }}</td>
                            <td class="perm-g6-22">{{ $oralHealthDataByGrade['6']->perm_22 ?? '' }}</td>
                            <td class="perm-g6-23">{{ $oralHealthDataByGrade['6']->perm_23 ?? '' }}</td>
                            <td class="perm-g6-24">{{ $oralHealthDataByGrade['6']->perm_24 ?? '' }}</td>
                            <td class="perm-g6-25">{{ $oralHealthDataByGrade['6']->perm_25 ?? '' }}</td>
                            <td class="perm-g6-26">{{ $oralHealthDataByGrade['6']->perm_26 ?? '' }}</td>
                            <td class="perm-g6-27">{{ $oralHealthDataByGrade['6']->perm_27 ?? '' }}</td>
                            <td class="perm-g6-28">{{ $oralHealthDataByGrade['6']->perm_28 ?? '' }}</td>
                        </tr>

                        <!-- Row 6: Upper permanent teeth numbers -->
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
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                        </tr>

                        <!-- Row 8: Lower permanent teeth symbols -->
                        <tr>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
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

                        <!-- Row 10: Lower permanent teeth data -->
                        <tr>
                            <td class="perm-g6-48">{{ $oralHealthDataByGrade['6']->perm_48 ?? '' }}</td>
                            <td class="perm-g6-47">{{ $oralHealthDataByGrade['6']->perm_47 ?? '' }}</td>
                            <td class="perm-g6-46">{{ $oralHealthDataByGrade['6']->perm_46 ?? '' }}</td>
                            <td class="perm-g6-45">{{ $oralHealthDataByGrade['6']->perm_45 ?? '' }}</td>
                            <td class="perm-g6-44">{{ $oralHealthDataByGrade['6']->perm_44 ?? '' }}</td>
                            <td class="perm-g6-43">{{ $oralHealthDataByGrade['6']->perm_43 ?? '' }}</td>
                            <td class="perm-g6-42">{{ $oralHealthDataByGrade['6']->perm_42 ?? '' }}</td>
                            <td class="perm-g6-41">{{ $oralHealthDataByGrade['6']->perm_41 ?? '' }}</td>
                            <td class="perm-g6-31">{{ $oralHealthDataByGrade['6']->perm_31 ?? '' }}</td>
                            <td class="perm-g6-32">{{ $oralHealthDataByGrade['6']->perm_32 ?? '' }}</td>
                            <td class="perm-g6-33">{{ $oralHealthDataByGrade['6']->perm_33 ?? '' }}</td>
                            <td class="perm-g6-34">{{ $oralHealthDataByGrade['6']->perm_34 ?? '' }}</td>
                            <td class="perm-g6-35">{{ $oralHealthDataByGrade['6']->perm_35 ?? '' }}</td>
                            <td class="perm-g6-36">{{ $oralHealthDataByGrade['6']->perm_36 ?? '' }}</td>
                            <td class="perm-g6-37">{{ $oralHealthDataByGrade['6']->perm_37 ?? '' }}</td>
                            <td class="perm-g6-38">{{ $oralHealthDataByGrade['6']->perm_38 ?? '' }}</td>
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

                        <!-- Row 12: Bottom temporary teeth symbols -->
                        <tr>
                            <td class="label no-border" colspan="3">TEMPORARY TEETH</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&#8212;</td>
                            <td class="symbol">&lt;&gt;</td>
                            <td class="symbol">&#215;</td>
                            <td class="symbol">O</td>
                            <td class="symbol">O</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>

                        <!-- Row 13: Bottom temporary teeth numbers -->
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

                        <!-- Row 14: Bottom temporary teeth data -->
                        <tr>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="temp-g6-85">{{ $oralHealthDataByGrade['6']->temp_85 ?? '' }}</td>
                            <td class="temp-g6-84">{{ $oralHealthDataByGrade['6']->temp_84 ?? '' }}</td>
                            <td class="temp-g6-83">{{ $oralHealthDataByGrade['6']->temp_83 ?? '' }}</td>
                            <td class="temp-g6-82">{{ $oralHealthDataByGrade['6']->temp_82 ?? '' }}</td>
                            <td class="temp-g6-81">{{ $oralHealthDataByGrade['6']->temp_81 ?? '' }}</td>
                            <td class="temp-g6-71">{{ $oralHealthDataByGrade['6']->temp_71 ?? '' }}</td>
                            <td class="temp-g6-72">{{ $oralHealthDataByGrade['6']->temp_72 ?? '' }}</td>
                            <td class="temp-g6-73">{{ $oralHealthDataByGrade['6']->temp_73 ?? '' }}</td>
                            <td class="temp-g6-74">{{ $oralHealthDataByGrade['6']->temp_74 ?? '' }}</td>
                            <td class="temp-g6-75">{{ $oralHealthDataByGrade['6']->temp_75 ?? '' }}</td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                            <td class="empty no-border"></td>
                        </tr>
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
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'gingivitis', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Periodontal Disease</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'periodontal_disease', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Malocclusion</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'malocclusion', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Supernumerary teeth</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'supernumerary_teeth', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Retained deciduous teeth</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'retained_deciduous_teeth', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Decubital ulcer</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'decubital_ulcer', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Calculus</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'calculus', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Cleft lip / palate</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'cleft_lip_palate', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Root fragment</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'root_fragment', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Fluorosis</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'fluorosis', '6', 'grade_6_12') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 3px;">Others, specify</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', 'K', 'kinder') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '1', 'grade_1_7') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '2', 'grade_2_8') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '3', 'grade_3_9') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '4', 'grade_4_10') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '5', 'grade_5_11') }}</td>
                    <td style="border: 1px solid #000; padding: 3px; font-size: 7px;">{{ getConditionDisplay($oralHealthDataByGrade, 'others_specify', '6', 'grade_6_12') }}</td>
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
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_index_dft') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./decayed</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_teeth_decayed') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./filled</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_teeth_filled') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total d.f.t.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_total_dft') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Extraction</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_for_extraction') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Filling</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'temporary_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'temporary_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'temporary_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'temporary_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'temporary_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'temporary_for_filling') }}</td>
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
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_index_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_index_dft') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./decayed</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_teeth_decayed') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_teeth_decayed') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No T./Missing</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_teeth_missing') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_teeth_missing') }}</td>
                        
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">No. T./Filled</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_teeth_filled') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_teeth_filled') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">Total D.M.F.T.</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_total_dft') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_total_dft') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Extraction</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_for_extraction') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_for_extraction') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;">For Filling</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, 'K', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '1', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '2', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '3', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '4', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '5', 'permanent_for_filling') }}</td>
                        <td style="border: 1px solid #000; padding: 3px;">{{ getIndexDftData($oralHealthDataByGrade, '6', 'permanent_for_filling') }}</td>
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
                @php
                    // Collect all treatments from all grades
                    $allTreatments = collect();
                    foreach ($oralHealthTreatmentsByGrade as $gradeKey => $treatments) {
                        if ($treatments && $treatments->count() > 0) {
                            $allTreatments = $allTreatments->merge($treatments);
                        }
                    }
                    // Sort by date
                    $allTreatments = $allTreatments->sortBy('date');
                    $maxRows = 10; // Show up to 10 rows
                @endphp

                @forelse($allTreatments->take($maxRows) as $treatment)
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; font-size: 7px;">{{ $treatment->date ? $treatment->date->format('m/d/Y') : '' }}</td>
                    <td style="border: 1px solid #000; padding: 5px; font-size: 7px;">{{ $treatment->chief_complaint ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 5px; font-size: 7px;">{{ $treatment->treatment ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 5px; font-size: 7px;">{{ $treatment->remarks ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 5px; font-size: 7px;">nurse</td>
                </tr>
                @empty
                @endforelse

                @for($i = $allTreatments->count(); $i < $maxRows; $i++)
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; height: 20px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                    <td style="border: 1px solid #000; padding: 5px;"></td>
                </tr>
                @endfor
            </table>
        </div>
    </div>
</body>
</html>
