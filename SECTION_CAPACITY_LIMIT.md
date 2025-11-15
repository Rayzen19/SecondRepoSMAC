# Section Capacity Limit - Maximum 30 Students Per Section

## 🎯 Overview
Implemented a **maximum capacity of 30 students per section** in the student assignment system to prevent overcrowding and maintain manageable class sizes.

## ✨ Features Implemented

### 1. **Backend Validation**
- **File**: `app/Http/Controllers/Admin/AssigningListController.php`
- Validates section capacity before saving assignments
- Prevents exceeding the 30-student limit
- Provides clear error messages when a section is full
- Handles student transfers between sections correctly

### 2. **Frontend Validation**
- **File**: `resources/views/admin/assigning_list/index.blade.php`
- Real-time capacity checking before assignment
- Shows clear alert when trying to assign to a full section
- Displays remaining capacity in error messages

### 3. **Visual Indicators**
- **Section Buttons**: Display current capacity (e.g., "15/30")
- **Color-Coded Badges**:
  - 🟢 **Green** (0-23 students): Section has good capacity
  - 🟡 **Yellow** (24-29 students): Section is filling up
  - 🔴 **Red** (30 students): Section is FULL
- **FULL Badge**: Displayed on buttons when capacity reached
- **Disabled Buttons**: Full sections are automatically disabled

### 4. **Dynamic Updates**
- Section capacity badges update in real-time
- Buttons become disabled when section reaches capacity
- Visual feedback when assigning students
- Modal view shows capacity indicator (e.g., "G-11 Baby breath 25/30 students")

## 🔧 Technical Implementation

### Backend Logic
```php
// Maximum students per section
$maxStudentsPerSection = 30;

// Check current section count
$sectionCounts[$sectionKey] = StudentEnrollment::where(...)
    ->count();

// Validate before assignment
if ($sectionCounts[$sectionKey] >= $maxStudentsPerSection) {
    $errors[] = "Cannot assign: Section is full (maximum 30 students)";
    continue;
}
```

### Frontend Logic
```javascript
// Maximum capacity constant
const MAX_STUDENTS_PER_SECTION = 30;

// Check before assignment
if (currentCount + newStudentsCount > MAX_STUDENTS_PER_SECTION) {
    alert(`Cannot assign: Section would exceed maximum capacity...`);
    return;
}
```

## 📊 Visual Examples

### Section Button States

#### Available (Green)
```
[G-11 Baby breath] [STEM] [15/30] ✅
```

#### Near Full (Yellow)
```
[G-11 Baby breath] [STEM] [27/30] ⚠️
```

#### Full (Red - Disabled)
```
[G-11 Baby breath] [STEM] [30/30] [FULL] 🚫
```

### Modal View with Capacity
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
G-11 Baby breath [27/30 students] ⚠️
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## 🎮 How It Works

### Assignment Flow
1. **Select Students**: Check students you want to assign
2. **Click Section Button**: Click on target section
3. **Capacity Check**: System verifies available space
4. **If Space Available**:
   - ✅ Students assigned
   - Badge updates to show new count
   - Success message displayed
5. **If Section Full**:
   - ❌ Assignment blocked
   - Alert shows: "Section is full (maximum 30 students)"
   - Shows current capacity and remaining space

### Error Messages
```
Cannot assign Maria Santos to G-11 Baby breath: 
Section is full (maximum 30 students)

Current: 30/30 students
Trying to add: 1 students
Remaining capacity: 0 students
```

## 🧪 Testing Checklist

- [x] Assign students to empty section - works ✅
- [x] Fill section to exactly 30 students - works ✅
- [x] Try to assign 31st student - blocked ✅
- [x] Section button shows correct count ✅
- [x] Section button disabled when full ✅
- [x] Color changes based on capacity ✅
- [x] Modal shows capacity indicator ✅
- [x] Error message is clear and helpful ✅
- [x] Transfer student between sections - works ✅
- [x] Save assignments with full section - blocks ✅

## 📝 User Benefits

1. **Prevents Overcrowding**: Maintains reasonable class sizes
2. **Clear Feedback**: Always know section capacity status
3. **Early Warning**: Yellow badge alerts when section is filling up
4. **Can't Overassign**: System prevents exceeding limit
5. **Easy Visibility**: See all section capacities at a glance

## 🔍 Additional Notes

### Capacity Calculation
- Counts all students enrolled in the section for the active academic year
- Updates in real-time as students are assigned/removed
- Persists across page refreshes (loaded from database)

### Edge Cases Handled
- ✅ Moving student from one section to another (doesn't count as "new" student)
- ✅ Updating existing enrollment (capacity not affected)
- ✅ Multiple students assigned at once (all validated together)
- ✅ Page refresh (capacities reload from database)

### Configuration
To change the maximum capacity, update these values:

**Backend** (`AssigningListController.php`):
```php
$maxStudentsPerSection = 30; // Change this value
```

**Frontend** (`index.blade.php`):
```javascript
const MAX_STUDENTS_PER_SECTION = 30; // Change this value
```

⚠️ **Important**: Both values must match!

## 🔒 Additional Protection Points

The 30-student capacity limit is enforced in multiple places:

### 1. **AssigningListController** (Bulk Assignment)
- Used in: Admin Assigning List page
- Validates when saving multiple student assignments
- Shows detailed error for each student that can't be assigned

### 2. **StudentEnrollmentController** (Individual Enrollment)
- Used in: Student Enrollment management page
- Validates when creating new enrollment
- Validates when updating/moving student to different section
- Returns to form with error message if section is full

This multi-layer protection ensures the limit is always enforced regardless of how students are assigned.

## 📚 Related Features

- Student Assignment System
- Section Management
- Academic Year Management
- Student Enrollment

## 📁 Files Modified

1. ✅ `app/Http/Controllers/Admin/AssigningListController.php`
   - Added capacity validation in `saveAssignments()` method
   - Tracks section counts during bulk operations
   - Provides detailed error messages

2. ✅ `app/Http/Controllers/Admin/StudentEnrollmentController.php`
   - Added capacity validation in `store()` method
   - Added capacity validation in `update()` method
   - Prevents exceeding limit through enrollment management

3. ✅ `resources/views/admin/assigning_list/index.blade.php`
   - Added frontend capacity validation
   - Added real-time capacity display on buttons
   - Added dynamic badge updates
   - Added capacity indicator in modals

4. ✅ `SECTION_CAPACITY_LIMIT.md`
   - Complete documentation of the feature

## 🎉 Summary

Every section now has a **hard limit of 30 students** to ensure:
- Manageable class sizes
- Better teacher-student ratio
- Quality education delivery
- Clear capacity visibility

The system provides **clear visual feedback** and **prevents overassignment** through both frontend and backend validation.
