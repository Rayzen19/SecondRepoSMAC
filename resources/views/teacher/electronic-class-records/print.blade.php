<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Record - {{ $assignment->subject->name }} Q{{ $quarter }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 0.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 12pt;
        }

        .info-section {
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-box {
            border: 1px solid #000;
            padding: 2px 5px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .student-name {
            text-align: left;
            padding-left: 5px;
            min-width: 150px;
        }

        .category-header {
            font-weight: bold;
            font-size: 8pt;
        }

        .written-work {
            background-color: #e3f2fd;
        }

        .performance-task {
            background-color: #fff3e0;
        }

        .quarterly-assessment {
            background-color: #e8f5e9;
        }

        .final-grade {
            background-color: #f3e5f5;
            font-weight: bold;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 60px;
        }

        .deped-logo {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 60px;
        }

        .small-text {
            font-size: 6pt;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>Senior High School Class Record</h2>
        <p style="margin: 2px 0;">School Year: {{ $assignment->academicYear->year }}</p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div>
                <span class="info-box">REGION: ___________</span>
                <span class="info-box">DIVISION: ___________</span>
            </div>
            <div>
                <span class="info-box">SCHOOL ID: ___________</span>
                <span class="info-box">SCHOOL YEAR: {{ $assignment->academicYear->year }}</span>
            </div>
        </div>
        <div class="info-row">
            <div>
                <span class="info-box">SCHOOL NAME: ___________________________</span>
            </div>
        </div>
        <div class="info-row">
            <div>
                <span class="info-box">FIRST QUARTER</span>
                <span class="info-box">GRADE & SECTION: {{ $assignment->strand->name }}</span>
            </div>
            <div>
                <span class="info-box">TEACHER: {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}</span>
            </div>
        </div>
        <div class="info-row">
            <div>
                <span class="info-box">SEMESTER: 1ST</span>
            </div>
            <div>
                <span class="info-box">SUBJECT: {{ $assignment->subject->name }}</span>
                <span class="info-box">TRACK: Core Subject (All Tracks)</span>
            </div>
        </div>
    </div>

    <!-- Class Record Table -->
    <table>
        <thead>
            <tr>
                <th rowspan="3" style="width: 150px;">LEARNERS' NAMES</th>
                <th colspan="{{ $writtenWorks->count() + 3 }}" class="category-header written-work">
                    WRITTEN WORK ({{ $assignment->written_works_percentage }}%)
                </th>
                <th colspan="{{ $performanceTasks->count() + 3 }}" class="category-header performance-task">
                    PERFORMANCE TASKS ({{ $assignment->performance_tasks_percentage }}%)
                </th>
                <th colspan="{{ $quarterlyAssessments->count() + 3 }}" class="category-header quarterly-assessment">
                    QUARTERLY ASSESSMENT ({{ $assignment->quarterly_assessment_percentage }}%)
                </th>
                <th rowspan="3" class="final-grade">Initial<br>Grade</th>
                <th rowspan="3" class="final-grade">Quarterly<br>Grade</th>
            </tr>
            <tr>
                <!-- Written Work columns -->
                @for($i = 1; $i <= $writtenWorks->count(); $i++)
                    <th class="written-work small-text">{{ $i }}</th>
                @endfor
                <th class="written-work small-text">Total</th>
                <th class="written-work small-text">PS</th>
                <th class="written-work small-text">WS</th>

                <!-- Performance Task columns -->
                @for($i = 1; $i <= $performanceTasks->count(); $i++)
                    <th class="performance-task small-text">{{ $i }}</th>
                @endfor
                <th class="performance-task small-text">Total</th>
                <th class="performance-task small-text">PS</th>
                <th class="performance-task small-text">WS</th>

                <!-- Quarterly Assessment columns -->
                @for($i = 1; $i <= $quarterlyAssessments->count(); $i++)
                    <th class="quarterly-assessment small-text">{{ $i }}</th>
                @endfor
                <th class="quarterly-assessment small-text">Total</th>
                <th class="quarterly-assessment small-text">PS</th>
                <th class="quarterly-assessment small-text">WS</th>
            </tr>
            <tr>
                <!-- Written Work max scores -->
                @foreach($writtenWorks as $ww)
                    <th class="written-work small-text">{{ $ww->max_score }}</th>
                @endforeach
                <th class="written-work small-text">{{ $writtenWorks->sum('max_score') }}</th>
                <th class="written-work small-text">100</th>
                <th class="written-work small-text">{{ $assignment->written_works_percentage }}</th>

                <!-- Performance Task max scores -->
                @foreach($performanceTasks as $pt)
                    <th class="performance-task small-text">{{ $pt->max_score }}</th>
                @endforeach
                <th class="performance-task small-text">{{ $performanceTasks->sum('max_score') }}</th>
                <th class="performance-task small-text">100</th>
                <th class="performance-task small-text">{{ $assignment->performance_tasks_percentage }}</th>

                <!-- Quarterly Assessment max scores -->
                @foreach($quarterlyAssessments as $qa)
                    <th class="quarterly-assessment small-text">{{ $qa->max_score }}</th>
                @endforeach
                <th class="quarterly-assessment small-text">{{ $quarterlyAssessments->sum('max_score') }}</th>
                <th class="quarterly-assessment small-text">100</th>
                <th class="quarterly-assessment small-text">{{ $assignment->quarterly_assessment_percentage }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentGrades as $gradeData)
            <tr>
                <td class="student-name">{{ $gradeData['student']->last_name }}, {{ $gradeData['student']->first_name }}</td>

                <!-- Written Work Scores -->
                @foreach($gradeData['ww_scores'] as $score)
                    <td>{{ $score ?? '' }}</td>
                @endforeach
                <td><strong>{{ $gradeData['ww_total'] }}</strong></td>
                <td>{{ $gradeData['ww_ps'] }}</td>
                <td><strong>{{ $gradeData['ww_ws'] }}</strong></td>

                <!-- Performance Task Scores -->
                @foreach($gradeData['pt_scores'] as $score)
                    <td>{{ $score ?? '' }}</td>
                @endforeach
                <td><strong>{{ $gradeData['pt_total'] }}</strong></td>
                <td>{{ $gradeData['pt_ps'] }}</td>
                <td><strong>{{ $gradeData['pt_ws'] }}</strong></td>

                <!-- Quarterly Assessment Scores -->
                @foreach($gradeData['qa_scores'] as $score)
                    <td>{{ $score ?? '' }}</td>
                @endforeach
                <td><strong>{{ $gradeData['qa_total'] }}</strong></td>
                <td>{{ $gradeData['qa_ps'] }}</td>
                <td><strong>{{ $gradeData['qa_ws'] }}</strong></td>

                <!-- Final Grades -->
                <td class="final-grade">{{ $gradeData['initial_grade'] }}</td>
                <td class="final-grade">{{ $gradeData['quarterly_grade'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Legend -->
    <div style="margin-top: 10px; font-size: 7pt;">
        <p style="margin: 2px 0;"><strong>Legend:</strong> PS = Percentage Score | WS = Weighted Score</p>
        <p style="margin: 2px 0;">
            <strong>Assessment Details:</strong><br>
            Written Works: @foreach($writtenWorks as $i => $ww) {{ $i+1 }}. {{ $ww->name }} ({{ $ww->max_score }}) @if(!$loop->last), @endif @endforeach<br>
            Performance Tasks: @foreach($performanceTasks as $i => $pt) {{ $i+1 }}. {{ $pt->name }} ({{ $pt->max_score }}) @if(!$loop->last), @endif @endforeach<br>
            Quarterly Assessments: @foreach($quarterlyAssessments as $i => $qa) {{ $i+1 }}. {{ $qa->name }} ({{ $qa->max_score }}) @if(!$loop->last), @endif @endforeach
        </p>
    </div>

    <!-- Signatures -->
    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
        <div style="text-align: center;">
            <p style="margin: 0;">___________________________</p>
            <p style="margin: 2px 0; font-size: 7pt;">Teacher's Signature</p>
        </div>
        <div style="text-align: center;">
            <p style="margin: 0;">___________________________</p>
            <p style="margin: 2px 0; font-size: 7pt;">Principal's Signature</p>
        </div>
    </div>
</body>
</html>
