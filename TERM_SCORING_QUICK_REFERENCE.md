# Quick Reference: Term-Based Score Recording

## For Teachers

### How to Add Assessments with Terms

1. **Navigate to Student's Class Record**
   - Go to your class records
   - Select a subject assignment
   - Click on a student to view their individual record

2. **Select the Term (Optional but Recommended)**
   - Use the "Term" dropdown at the top
   - Choose "Midterm" or "Finals"
   - This helps organize your assessments

3. **Add Assessment**
   - Click "Add WW", "Add PT", or "Add QA" button
   - The modal will open with the current term pre-selected
   - Fill in:
     - Name (e.g., "Quiz 1")
     - Description (optional)
     - Date
     - Max Score (default: 100)
     - **Term: Midterm or Finals** ← Important!
   - Click "Submit"

4. **Enter Scores**
   - Scores appear in the assessment table
   - Enter individual scores for each assessment
   - Click "Save Scores" when done

### Viewing Scores by Term

**View Midterm Scores Only:**
- Select "Midterm" from the Term filter
- Only midterm assessments will be displayed
- Grades are calculated using only midterm scores

**View Finals Scores Only:**
- Select "Finals" from the Term filter
- Only finals assessments will be displayed
- Grades are calculated using only finals scores

**View All Scores:**
- Select "All Terms" from the filter
- Shows both midterm and finals assessments
- Useful for getting the complete picture

### Best Practices

1. **Always specify the term** when creating assessments
2. **Use consistent naming**: 
   - Midterm: "WW1-M", "PT1-M", "QA-Midterm"
   - Finals: "WW1-F", "PT1-F", "QA-Finals"
3. **Filter before adding** to auto-select the correct term
4. **Review both terms** before submitting final grades

## For Developers

### Database Query Examples

**Get all midterm assessments for a subject:**
```php
SubjectRecord::where('academic_year_strand_subject_id', $assignmentId)
    ->where('term', 'midterm')
    ->get();
```

**Get finals written work for first semester:**
```php
SubjectRecord::where('academic_year_strand_subject_id', $assignmentId)
    ->where('quarter', '1st')
    ->where('term', 'finals')
    ->where('type', 'written work')
    ->get();
```

**Calculate midterm grade only:**
```php
$midtermRecords = SubjectRecord::where('academic_year_strand_subject_id', $assignmentId)
    ->where('quarter', '1st')
    ->where('term', 'midterm')
    ->get();

// Calculate using these records only
```

### Creating Assessment via Code

```php
SubjectRecord::create([
    'academic_year_strand_subject_id' => 123,
    'name' => 'Midterm Quiz 1',
    'type' => 'written work',
    'quarter' => '1st',           // Semester
    'term' => 'midterm',          // Term within semester
    'max_score' => 50,
    'date_given' => '2025-10-25',
    'description' => 'Chapter 1-5 coverage'
]);
```

### API/Form Data Structure

**When submitting assessment form:**
```javascript
{
    category: 'written_work',
    term: 'first-semester',      // Maps to quarter '1st'
    term_type: 'midterm',        // The actual term
    name: 'Quiz 1',
    max_score: 50,
    date_given: '2025-10-25'
}
```

## Data Structure

```
Academic Year 2024-2025
├── 1st Semester (quarter='1st')
│   ├── Midterm (term='midterm')
│   │   ├── WW1, WW2, WW3...
│   │   ├── PT1, PT2...
│   │   └── QA
│   └── Finals (term='finals')
│       ├── WW4, WW5, WW6...
│       ├── PT3, PT4...
│       └── QA
└── 2nd Semester (quarter='2nd')
    ├── Midterm (term='midterm')
    │   ├── WW1, WW2, WW3...
    │   ├── PT1, PT2...
    │   └── QA
    └── Finals (term='finals')
        ├── WW4, WW5, WW6...
        ├── PT3, PT4...
        └── QA
```

## Common Issues & Solutions

### Issue: Assessment not showing up
**Solution:** Check the term filter. You might be viewing "Midterm" but the assessment was saved as "Finals" or vice versa.

### Issue: Can't find term selection when adding assessment
**Solution:** Make sure you're using the updated version. The term dropdown should be in the assessment modal.

### Issue: Old assessments don't have terms
**Solution:** That's normal. Old assessments have NULL term values and will appear in "All Terms" view. You can edit them to add a term if needed.

### Issue: Grades seem incorrect when filtering
**Solution:** Grades are calculated based on visible assessments only. If you're viewing "Midterm only", the grade reflects only midterm performance.

## Testing Checklist

- [ ] Create a midterm assessment
- [ ] Create a finals assessment
- [ ] Enter scores for both
- [ ] Filter by "Midterm" - verify only midterm shows
- [ ] Filter by "Finals" - verify only finals shows
- [ ] Filter by "All Terms" - verify both show
- [ ] Check that grades calculate correctly per filter
- [ ] Verify the modal pre-selects current filter's term
- [ ] Confirm assessments save with correct term value

## URL Parameters

- `?term=midterm` - Show midterm assessments only
- `?term=finals` - Show finals assessments only
- `?term=` or no parameter - Show all assessments
- Can combine with grade filter: `?term=midterm&grade_level=11`

## Migration Status

✅ Migration completed: `2025_10_25_000000_add_term_to_subject_records_table.php`

Run status check:
```bash
php artisan migrate:status
```

## Rollback (if needed)

If you need to remove the term column:
```bash
php artisan migrate:rollback --step=1
```

⚠️ Warning: This will delete all term data!
