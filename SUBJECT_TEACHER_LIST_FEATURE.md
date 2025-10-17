# Subject Teacher List Feature

## Overview
Added a "Teacher List" button to the subject list page that allows viewing all teachers assigned to a specific subject.

## Changes Made

### 1. Updated Subject Index View
**File:** `resources/views/admin/subjects/index.blade.php`

Added a new action button with a users icon (`ti ti-users`) to each subject row in all three sections (Core, Applied, and Specialized subjects). This button links to the teacher list page for that specific subject.

```php
<a href="{{ route('admin.subjects.teachers', $subject) }}" class="me-2" title="View Teachers">
    <i class="ti ti-users"></i>
</a>
```

### 2. Added Route
**File:** `routes/web.php`

Added a new route to handle the teacher list request:

```php
Route::get('/subjects/{subject}/teachers', [App\Http\Controllers\Admin\SubjectController::class, 'teachers'])
    ->name('admin.subjects.teachers');
```

### 3. Updated SubjectController
**File:** `app/Http/Controllers/Admin/SubjectController.php`

#### Added Import:
```php
use App\Models\AcademicYearStrandSubject;
```

#### Added `teachers()` Method:
This method retrieves all teachers assigned to teach the subject by:
- Querying the `AcademicYearStrandSubject` table with relationships loaded (teacher, academicYear, strand)
- Filtering by the specific subject ID
- Grouping by teacher ID to consolidate multiple assignments
- Mapping the data to include teacher information and their assignment details (academic year and strand)

```php
public function teachers(Subject $subject)
{
    $teachers = AcademicYearStrandSubject::with([
        'teacher',
        'academicYear',
        'strand'
    ])
    ->where('subject_id', $subject->id)
    ->whereHas('teacher')
    ->get()
    ->groupBy('teacher_id')
    ->map(function ($assignments) {
        $teacher = $assignments->first()->teacher;
        $assignmentDetails = $assignments->map(function ($assignment) {
            return [
                'academic_year' => $assignment->academicYear->name ?? 'N/A',
                'strand' => $assignment->strand->code ?? 'N/A',
            ];
        });
        
        return [
            'teacher' => $teacher,
            'assignments' => $assignmentDetails,
        ];
    })
    ->values();

    return view('admin.subjects.teachers', compact('subject', 'teachers'));
}
```

### 4. Created Teachers View
**File:** `resources/views/admin/subjects/teachers.blade.php`

A new Blade template that displays:

#### Header Section:
- Breadcrumb navigation
- Subject information (name, code, semester)
- "Back to Subjects" button

#### Table Display:
- Employee Number
- Teacher Name (with avatar initials)
- Email
- Department
- Specialization
- Academic Year(s) - displayed as badges
- Strand(s) - displayed as badges

#### Empty State:
- Shows a friendly message when no teachers are assigned to the subject
- Uses the `ti ti-users` icon

#### Footer:
- Displays total count of teachers assigned to the subject

## Features

### Teacher Information Display
- Shows all relevant teacher information in a clean table format
- Groups assignments by teacher (same teacher may teach the subject in different years/strands)
- Displays academic years and strands as badges for easy visualization
- Includes avatar with teacher initials for better visual identification

### User Experience
- Consistent design with other admin pages
- Responsive layout
- Clear navigation back to subject list
- Empty state handling when no teachers are assigned

## Usage

1. Navigate to the subject list page at `/admin/subjects`
2. Click the users icon (👥) next to any subject
3. View all teachers who have been assigned to teach that subject
4. See which academic years and strands each teacher has taught the subject in

## Dependencies

This feature relies on:
- `AcademicYearStrandSubject` model - stores teacher assignments
- `Teacher` model - teacher information
- `AcademicYear` model - academic year data
- `Strand` model - strand information

## Database Relationships

The feature uses the following relationships:
- `AcademicYearStrandSubject` belongs to `Teacher`
- `AcademicYearStrandSubject` belongs to `Subject`
- `AcademicYearStrandSubject` belongs to `AcademicYear`
- `AcademicYearStrandSubject` belongs to `Strand`
