# Term-Based Score Recording System

## Overview
This implementation adds support for separate score records for **Midterm** and **Finals** within each semester. Previously, the system only tracked scores per semester (1st and 2nd), but now teachers can maintain distinct records for each term.

## Database Changes

### Migration: `2025_10_25_000000_add_term_to_subject_records_table.php`

Added a new `term` column to the `subject_records` table:

```php
$table->enum('term', ['midterm', 'finals'])->nullable()->after('quarter');
```

This column stores the term type for each assessment record.

## Model Updates

### SubjectRecord Model
Updated the `$fillable` array to include the new `term` field:

```php
protected $fillable = [
    'academic_year_strand_subject_id',
    'name',
    'description',
    'max_score',
    'type',
    'quarter',      // Stores '1st' or '2nd' (semester)
    'term',         // NEW: Stores 'midterm' or 'finals'
    'date_given',
    'remarks',
];
```

## Controller Updates

### ClassRecordController - `studentShow()` Method

**Key Changes:**

1. **Term Filtering**: Added support for filtering records by term (midterm/finals)
   ```php
   $selectedTerm = $request->query('term');
   
   if ($selectedTerm && in_array($selectedTerm, ['midterm', 'finals'])) {
       $recordsQuery->where('term', $selectedTerm);
   }
   ```

2. **Enhanced Data Structure**: Records are now filtered by both quarter (semester) and term
   ```php
   $qRecs = $allRecords->filter(function ($r) use ($qKey, $selectedTerm) {
       $matchesQuarter = $r->quarter === $qKey;
       
       if ($selectedTerm && in_array($selectedTerm, ['midterm', 'finals'])) {
           return $matchesQuarter && $r->term === $selectedTerm;
       }
       
       return $matchesQuarter;
   })->values();
   ```

3. **View Data**: Passes the selected term to the view for UI state management

### ClassRecordController - `storeAssessment()` Method

**Key Changes:**

1. **Validation**: Added `term_type` field to validation rules
   ```php
   'term_type' => ['nullable', 'in:midterm,finals'],
   ```

2. **Data Storage**: Stores the term type when creating new assessments
   ```php
   $termType = $data['term_type'] ?? null;
   
   SubjectRecord::create([
       // ... other fields
       'term' => $termType,
   ]);
   ```

## View Updates

### student_show.blade.php

**Key Changes:**

1. **Term Filter UI**: Already exists in the original code
   - Dropdown to select Midterm or Finals
   - Filters are applied via query parameters

2. **Add Assessment Modal**: Enhanced to include term selection
   ```html
   <div class="col-md-4">
       <label class="form-label">Term</label>
       <select name="term_type" class="form-select" required>
           <option value="">Select Term</option>
           <option value="midterm">Midterm</option>
           <option value="finals">Finals</option>
       </select>
   </div>
   ```

3. **JavaScript Updates**: 
   - Auto-selects the current filtered term when adding new assessments
   - Properly sets hidden form fields for term context

## How It Works

### Data Flow

1. **Viewing Scores by Term**:
   ```
   User selects "Midterm" from filter
   ↓
   Query parameter: ?term=midterm
   ↓
   Controller filters: ->where('term', 'midterm')
   ↓
   Only midterm assessments are displayed
   ```

2. **Adding New Assessments**:
   ```
   User clicks "Add WW/PT/QA" button
   ↓
   Modal opens with term pre-selected
   ↓
   Teacher fills assessment details
   ↓
   Submit creates SubjectRecord with term='midterm' or 'finals'
   ↓
   Record is saved with both quarter (semester) and term
   ```

3. **Score Calculation**:
   - Scores are calculated per filtered view
   - When viewing "Midterm only", only midterm assessments are included
   - When viewing "Finals only", only finals assessments are included
   - When viewing "All Terms", all assessments are included

## Database Structure

### Complete Record Structure

```
subject_records
├── id
├── academic_year_strand_subject_id (links to subject assignment)
├── name (e.g., "WW1", "PT2", "QA1")
├── description
├── max_score
├── type ('written work', 'performance task', 'quarterly assessment')
├── quarter ('1st' or '2nd' - represents semester)
├── term ('midterm' or 'finals' - NEW!)
├── date_given
├── remarks
└── timestamps

subject_record_results
├── id
├── subject_record_id (links to assessment)
├── student_id
├── raw_score (student's score on this assessment)
├── base_score
├── final_score
└── timestamps
```

## Usage Examples

### Example 1: View Midterm Scores Only
```
URL: /teacher/class-records/{assignment}/students/{student}?term=midterm

Result: Shows only assessments where term='midterm'
```

### Example 2: Create a Midterm Assessment
```
POST /teacher/class-records/{assignment}/assessments

Data:
- category: 'written_work'
- term: 'first-semester'
- term_type: 'midterm'  ← NEW
- name: 'WW1 - Midterm Quiz'
- max_score: 50
- date_given: '2025-10-25'

Creates: SubjectRecord with quarter='1st' and term='midterm'
```

### Example 3: Complete Workflow
1. Teacher navigates to student's class record
2. Selects "Midterm" from the term filter
3. Clicks "Add WW" button
4. Modal auto-fills term as "Midterm"
5. Teacher enters assessment details
6. Saves → Record stored with term='midterm'
7. Students see this assessment only when viewing Midterm scores

## Benefits

1. **Separation of Concerns**: Clear distinction between midterm and finals scores
2. **Flexible Reporting**: Teachers can view/report scores by term
3. **Accurate Tracking**: Better alignment with actual academic term structure
4. **Backward Compatible**: Existing records without term values still work (nullable column)
5. **User-Friendly**: Automatic term pre-selection based on current filter

## Migration Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Model updated with term field
3. ✅ Controller handles term filtering and storage
4. ✅ View includes term selection in assessment creation

## Testing Recommendations

1. **Create Midterm Assessment**:
   - Filter by "Midterm"
   - Add a Written Work
   - Verify term='midterm' in database

2. **Create Finals Assessment**:
   - Filter by "Finals"
   - Add a Performance Task
   - Verify term='finals' in database

3. **View Filtering**:
   - Create assessments for both terms
   - Switch filters and verify correct assessments display
   - Check "All Terms" shows all assessments

4. **Score Calculation**:
   - Enter scores for midterm assessments
   - Enter scores for finals assessments
   - Verify totals are calculated correctly per term

## Future Enhancements

1. **Term-Based Grade Reports**: Generate separate report cards for midterm and finals
2. **Comparison Analysis**: Compare student performance between midterm and finals
3. **Progress Tracking**: Show improvement/decline from midterm to finals
4. **Bulk Operations**: Mass-create assessments for a specific term
5. **Term Settings**: Configure term-specific weightings (if midterm/finals have different weights)

## Notes

- The `quarter` field represents **semester** (1st or 2nd)
- The `term` field represents **period within semester** (midterm or finals)
- This creates a hierarchical structure: Academic Year → Semester → Term → Assessment
- All existing records will have NULL term values (backward compatible)
- Teachers can optionally not specify a term for assessments that span both periods

## Related Files

- Migration: `database/migrations/2025_10_25_000000_add_term_to_subject_records_table.php`
- Model: `app/Models/SubjectRecord.php`
- Controller: `app/Http/Controllers/Teacher/ClassRecordController.php`
- View: `resources/views/teacher/class_records/student_show.blade.php`
