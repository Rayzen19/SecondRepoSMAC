# Student Grades and Decision Support System

## Overview
This document outlines the implementation of the Grades page and Decision Support System for the student portal.

## Features Implemented

### 1. Grades Page (`/student/grades`)

#### Features:
- **Academic Year/Semester Filter**: Students can filter grades by academic year and semester
- **Grades Table**: Displays subject code, subject name, and grade with color-coded badges
  - Green badge (≥90): Excellent
  - Blue badge (≥80): Good
  - Yellow badge (<80): Needs improvement
- **Average Calculation**: Shows the overall average for the selected semester
- **Performance Visualization**:
  - Performance bars for different assessment types:
    - Activities
    - Quizzes
    - Assignment
    - Major Quiz
    - Exam
    - Recitation
    - Project
  - Interactive donut chart showing performance distribution
- **Export/Print**: Report button to print or export grades

#### Files Created:
- `app/Http/Controllers/Student/GradeController.php`
- `resources/views/student/grades/index.blade.php`

### 2. Decision Support System (`/student/decision-support`)

#### Features:
- **Overall Performance Summary**:
  - Overall average grade
  - Number of strong subjects (≥90)
  - Number of subjects needing improvement (<80)

- **Academic Strengths**: Lists top 5 subjects where student excels
- **Areas for Improvement**: Lists subjects with grades below 80
- **Personalized Recommendations**: 
  - AI-driven suggestions based on performance
  - Tailored advice for different performance levels
  - Specific recommendations for weak subjects

- **Study Tips & Resources**:
  - Time management strategies
  - Effective study strategies
  - Best practices for academic success

- **Performance Analysis**:
  - Performance level categorization (Outstanding, Very Good, Good, Needs Improvement)
  - Quick stats dashboard
  - Overall grade letter

- **Suggested Action Items**:
  - Schedule review sessions
  - Join study groups
  - Seek teacher support

#### Files Created:
- `app/Http/Controllers/Student/DecisionSupportController.php`
- `resources/views/student/decision-support/index.blade.php`

### 3. Navigation Updates

#### Student Sidebar Menu:
Added two new menu items:
- **Grades** - Access to grades and performance visualization
- **Decision Support System** - Access to personalized academic recommendations

#### Updated Files:
- `resources/views/student/components/template.blade.php`
- `routes/web.php`

## Technical Details

### Routes Added:
```php
// Grades
Route::get('/student/grades', [GradeController::class, 'index'])->name('student.grades.index');

// Decision Support System
Route::get('/student/decision-support', [DecisionSupportController::class, 'index'])->name('student.decision-support.index');
```

### Database Models Used:
- `AcademicYear` - Academic year information
- `StudentEnrollment` - Student enrollment records
- `SubjectEnrollment` - Subject enrollments with grades (fq_grade, sq_grade, a_grade, f_grade)
- `AcademicYearStrandSubject` - Subject assignments with teacher info

### Frontend Technologies:
- **Bootstrap 5** - UI framework
- **Tabler Icons** - Icon library
- **Chart.js** - Donut chart visualization for performance data
- **Blade Templates** - Laravel templating engine

## How to Use

### For Students:

1. **View Grades**:
   - Navigate to "Grades" in the sidebar
   - Select academic year and semester from dropdown
   - View grades table with color-coded performance indicators
   - Check performance chart for visual analysis
   - Click "Report" button to print/export

2. **Access Decision Support System**:
   - Navigate to "Decision Support System" in the sidebar
   - View overall performance summary cards
   - Review academic strengths and weaknesses
   - Read personalized recommendations
   - Explore study tips and suggested action items

## Customization Options

### Grade Thresholds:
You can modify the grade thresholds in the controllers:
- **Excellent**: ≥90 (Green)
- **Good**: 80-89 (Blue)
- **Needs Improvement**: <80 (Yellow/Warning)

### Performance Data:
Currently using mock data (60% for all categories). To use real data:
1. Calculate from `SubjectRecord` and `SubjectRecordResult` models
2. Update the `performanceData` array in `GradeController`

### Recommendations:
The Decision Support System generates recommendations based on:
- Overall average performance
- Number of weak subjects
- Individual subject performance

You can customize the recommendation logic in `DecisionSupportController`.

## Future Enhancements

1. **Real-time Performance Calculation**: Calculate actual performance data from assessment records
2. **Grade Trends**: Show grade improvement/decline over time
3. **Comparison with Class Average**: Compare student performance with class average
4. **Goal Setting**: Allow students to set academic goals
5. **Progress Tracking**: Track progress toward goals
6. **Notifications**: Alert students when grades are updated
7. **Export Options**: Export to PDF/Excel
8. **Mobile Optimization**: Enhanced mobile-responsive design

## Testing Checklist

- [ ] Access grades page and verify data loads correctly
- [ ] Test year/semester filter functionality
- [ ] Verify average calculation is accurate
- [ ] Check chart.js visualization renders properly
- [ ] Test Decision Support System page loads
- [ ] Verify recommendations display based on performance
- [ ] Test sidebar navigation highlights correctly
- [ ] Verify responsive design on mobile devices
- [ ] Test print functionality
- [ ] Check for proper authentication/authorization

## Notes

- Grades are pulled from the `SubjectEnrollment` model
- Current implementation uses mock performance data
- Chart.js CDN is used for donut chart visualization
- All routes are protected by `auth:student` middleware
- Icons use Tabler Icons library (already included in template)
