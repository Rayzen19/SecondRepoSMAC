# Teacher Subject Assignment Limit Feature

## Overview
Implemented a **maximum limit of 3 subject assignments** per teacher per academic year to prevent overloading teachers with too many subjects.

## Implementation Date
December 11, 2025

## Changes Made

### 1. Backend Validation (TeacherController.php)

#### Modified Method: `storeAssignment()`
**File:** `app/Http/Controllers/Admin/TeacherController.php`

**Added Validation:**
```php
// Check if teacher already has 3 subject assignments for this academic year
$assignmentCount = AcademicYearStrandSubject::where('teacher_id', $teacher->id)
    ->where('academic_year_id', $data['academic_year_id'])
    ->count();

if ($assignmentCount >= 3) {
    return redirect()->back()->with('error', 'This teacher has already reached the maximum limit of 3 subject assignments for this academic year.');
}
```

**Logic:**
- Counts existing assignments for the teacher in the selected academic year
- If count is 3 or more, prevents new assignment and shows error message
- Validation happens **before** duplicate check and creation

#### Modified Method: `assignments()`
**File:** `app/Http/Controllers/Admin/TeacherController.php`

**Added Assignment Counting:**
```php
// Count assignments per academic year for validation
$assignmentCounts = $existingAssignments->groupBy('academic_year_id')->map(function($group) {
    return $group->count();
});
```

**Purpose:**
- Groups assignments by academic year
- Counts how many subjects assigned per year
- Passes counts to frontend for dynamic validation

---

### 2. Frontend Enhancement (assignments.blade.php)

#### Added Information Alert
**File:** `resources/views/admin/teachers/assignments.blade.php`

```html
<div class="alert alert-info mb-3">
    <i class="ti ti-info-circle me-2"></i>
    <strong>Limit:</strong> Each teacher can be assigned to a maximum of <strong>3 subjects</strong> per academic year.
</div>
```

**Visual Indicator:**
- Blue info alert shown above the form
- Clearly states the 3-subject limit
- Visible to admin before making assignments

#### JavaScript Dynamic Validation
**File:** `resources/views/admin/teachers/assignments.blade.php`

**Added Script:**
```javascript
const form = document.getElementById('assignmentForm');
const academicYearSelect = document.querySelector('select[name="academic_year_id"]');
const submitBtn = form?.querySelector('button[type="submit"]');
const assignmentCounts = @json($assignmentCounts ?? []);

// Check assignment limit on academic year change
function checkAssignmentLimit() {
    if (!academicYearSelect || !submitBtn) return;
    const selectedYearId = academicYearSelect.value;
    const count = assignmentCounts[selectedYearId] || 0;
    
    if (count >= 3) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-lock me-1"></i>Limit Reached (3/3)';
        submitBtn.classList.remove('bg-info');
        submitBtn.classList.add('btn-secondary');
    } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="ti ti-plus me-1"></i>Submit (' + count + '/3)';
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('bg-info');
    }
}

if (academicYearSelect) {
    academicYearSelect.addEventListener('change', checkAssignmentLimit);
    checkAssignmentLimit(); // Check on page load
}
```

**Features:**
- Real-time counter showing `(X/3)` on submit button
- Automatically disables submit button when limit reached
- Button text changes to "Limit Reached (3/3)"
- Button color changes from blue to gray when disabled
- Updates dynamically when academic year selection changes

---

## How It Works

### Admin Workflow:

1. **Navigate to Teacher Assignment Page**
   - Go to Admin → Teachers → Teacher List
   - Click "Assignment" button for any teacher

2. **See Limit Information**
   - Blue alert at top shows: "Each teacher can be assigned to a maximum of 3 subjects per academic year"

3. **Select Academic Year**
   - Choose an academic year from dropdown
   - Submit button shows current count: "Submit (0/3)", "Submit (1/3)", "Submit (2/3)"

4. **Assign Subjects (Up to 3)**
   - Select specialization, subject, and submit
   - Can assign up to 3 different subjects

5. **Limit Reached**
   - After 3 assignments in same academic year:
     - Submit button becomes disabled
     - Button text: "Limit Reached (3/3)"
     - Button color changes to gray
     - Cannot add more subjects for that year

6. **Try to Exceed Limit**
   - If somehow form is submitted (e.g., via direct POST):
     - Backend validation catches it
     - Error message: "This teacher has already reached the maximum limit of 3 subject assignments for this academic year."

---

## Validation Layers

### Layer 1: Frontend (JavaScript)
- **Real-time feedback**
- Shows counter (X/3)
- Disables submit button
- Better UX - prevents unnecessary form submissions

### Layer 2: Backend (Controller)
- **Server-side security**
- Validates on form submission
- Cannot be bypassed
- Shows clear error message

---

## Error Messages

### When Limit Reached:
```
This teacher has already reached the maximum limit of 3 subject assignments for this academic year.
```

### When Duplicate Assignment:
```
This assignment already exists for this teacher.
```

---

## Technical Details

### Database Query:
```php
AcademicYearStrandSubject::where('teacher_id', $teacher->id)
    ->where('academic_year_id', $data['academic_year_id'])
    ->count()
```

### Validation Logic:
- Count assignments **per academic year**
- Each academic year has independent 3-subject limit
- Teacher can have 3 subjects in 2024-2025 AND 3 subjects in 2025-2026
- Limit applies to **active assignments** only (deleted assignments not counted)

---

## Benefits

1. **Prevents Teacher Overload**
   - Ensures fair workload distribution
   - Maximum 3 subjects per teacher per year

2. **Clear Communication**
   - Visual alert explains the limit
   - Real-time counter shows progress
   - Immediate feedback when limit reached

3. **User-Friendly**
   - Submit button shows count (X/3)
   - Automatic disable when limit reached
   - No confusing error messages on submit

4. **Secure**
   - Backend validation ensures limit cannot be bypassed
   - Frontend validation improves UX
   - Both layers work together

---

## Files Modified

### Backend:
- `app/Http/Controllers/Admin/TeacherController.php`
  - Line 45-51: Added assignment counting
  - Line 65-72: Added limit validation

### Frontend:
- `resources/views/admin/teachers/assignments.blade.php`
  - Line 54-57: Added info alert
  - Line 59: Added form ID
  - Line 137-165: Added JavaScript validation

---

## Testing Scenarios

### Scenario 1: New Teacher (0 Assignments)
- ✅ Button shows: "Submit (0/3)"
- ✅ Button enabled
- ✅ Can assign subjects

### Scenario 2: Teacher with 2 Assignments
- ✅ Button shows: "Submit (2/3)"
- ✅ Button enabled
- ✅ Can assign 1 more subject

### Scenario 3: Teacher with 3 Assignments
- ✅ Button shows: "Limit Reached (3/3)"
- ✅ Button disabled and grayed out
- ✅ Cannot assign more subjects

### Scenario 4: Different Academic Years
- ✅ Year 2024-2025: 3 subjects (full)
- ✅ Year 2025-2026: 0 subjects (can assign)
- ✅ Each year has independent limit

---

## Future Enhancements (Optional)

1. **Configurable Limit**
   - Make limit adjustable in settings
   - Different limits for different departments

2. **Warning at 2 Subjects**
   - Show yellow badge when approaching limit
   - "1 assignment remaining"

3. **Override Permission**
   - Allow super admin to bypass limit
   - With special confirmation

4. **Assignment History**
   - Show deleted assignments separately
   - Restore deleted assignments

---

## Feature Complete! ✅

The 3-subject limit is now enforced with:
- ✅ Backend validation (security)
- ✅ Frontend validation (UX)
- ✅ Clear visual indicators
- ✅ Real-time feedback
- ✅ Error messages
- ✅ Dynamic button states
