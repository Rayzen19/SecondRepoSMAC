# Testing Guide: Grades & Decision Support System

## Prerequisites
- ✅ Laravel application is running
- ✅ Database is properly configured
- ✅ Student account exists in the database
- ✅ Student has enrollment records with grades

## Quick Start Testing

### 1. Start the Server
```powershell
# Navigate to project directory
cd c:\xampp\htdocs\NEWSMAC

# Start Laravel development server
php artisan serve
```

### 2. Login as Student
1. Open browser: `http://localhost:8000/student/login`
2. Enter student credentials
3. Click Login

### 3. Test Grades Page
1. Look for "Grades" in the sidebar (icon: 📈)
2. Click on "Grades"
3. You should see:
   - Academic Year dropdown
   - Semester dropdown
   - Grades table with subjects
   - Average calculation
   - Performance section with donut chart

### 4. Test Decision Support System
1. Look for "Decision Support System" in the sidebar (icon: 📊)
2. Click on "Decision Support System"
3. You should see:
   - Three summary cards at the top
   - Academic Strengths section
   - Areas for Improvement section
   - Personalized Recommendations
   - Study Tips & Resources
   - Performance Analysis
   - Suggested Action Items

## Detailed Test Cases

### Test Case 1: Grades Page - Filter Functionality
**Steps:**
1. Navigate to Grades page
2. Select different Academic Year from dropdown
3. Select different Semester from dropdown

**Expected Result:**
- Page reloads with updated grades
- Grades reflect selected year/semester
- Average updates accordingly

**Status:** [ ] Pass [ ] Fail

---

### Test Case 2: Grades Page - Grade Display
**Steps:**
1. Check grades table

**Expected Result:**
- All enrolled subjects are displayed
- Subject codes and names are correct
- Grades are color-coded:
  - Green badge for grades ≥ 90
  - Blue badge for grades 80-89
  - Yellow badge for grades < 80
- Average is calculated correctly

**Status:** [ ] Pass [ ] Fail

---

### Test Case 3: Grades Page - Performance Chart
**Steps:**
1. Scroll to Performance section
2. Check donut chart

**Expected Result:**
- Chart.js donut chart is displayed
- Chart shows 7 categories with different colors
- Legend matches chart colors
- Performance bars show percentages

**Status:** [ ] Pass [ ] Fail

---

### Test Case 4: Grades Page - Print Functionality
**Steps:**
1. Click "Report" button

**Expected Result:**
- Browser print dialog opens
- Page is formatted for printing

**Status:** [ ] Pass [ ] Fail

---

### Test Case 5: Decision Support - Summary Cards
**Steps:**
1. Navigate to Decision Support System page
2. Check three summary cards at top

**Expected Result:**
- Overall Average card shows correct average
- Strong Subjects card shows count of subjects ≥ 90
- Needs Improvement card shows count of subjects < 80
- Cards have appropriate colors (blue, green, yellow)

**Status:** [ ] Pass [ ] Fail

---

### Test Case 6: Decision Support - Strengths & Weaknesses
**Steps:**
1. Check Academic Strengths section
2. Check Areas for Improvement section

**Expected Result:**
- Strengths list shows subjects with grades ≥ 90
- Each strength has subject name and grade badge
- Weaknesses list shows subjects with grades < 80
- Each weakness has subject name and grade badge
- If no data, appropriate message is shown

**Status:** [ ] Pass [ ] Fail

---

### Test Case 7: Decision Support - Recommendations
**Steps:**
1. Check Personalized Recommendations section

**Expected Result:**
- Recommendations are displayed in numbered alerts
- Recommendations are appropriate to performance level
- If weak subjects exist, specific recommendations are shown

**Status:** [ ] Pass [ ] Fail

---

### Test Case 8: Decision Support - Study Tips
**Steps:**
1. Scroll to Study Tips & Resources section

**Expected Result:**
- Time Management tips are displayed
- Effective Study Strategies are displayed
- Each tip has a checkmark icon
- Tips are organized in two columns

**Status:** [ ] Pass [ ] Fail

---

### Test Case 9: Sidebar Navigation
**Steps:**
1. Click "Grades" in sidebar
2. Verify "Grades" is highlighted
3. Click "Decision Support System" in sidebar
4. Verify "Decision Support System" is highlighted

**Expected Result:**
- Active menu item is highlighted
- Navigation works correctly
- Icons display properly

**Status:** [ ] Pass [ ] Fail

---

### Test Case 10: Responsive Design
**Steps:**
1. Open in desktop browser
2. Resize to tablet width
3. Resize to mobile width

**Expected Result:**
- Layout adapts to screen size
- All content is accessible
- No horizontal scrolling
- Touch-friendly on mobile

**Status:** [ ] Pass [ ] Fail

---

## Browser Compatibility Testing

Test in multiple browsers:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

## Mobile Device Testing

Test on actual devices or emulators:
- [ ] iPhone/iOS
- [ ] Android
- [ ] Tablet

## Performance Testing

### Check Page Load Times:
- [ ] Grades page loads < 2 seconds
- [ ] Decision Support page loads < 2 seconds
- [ ] Chart.js loads properly
- [ ] No console errors

## Database Verification

### Check if data is correctly retrieved:
```sql
-- Check student enrollments
SELECT * FROM student_enrollments WHERE student_id = [YOUR_STUDENT_ID];

-- Check subject enrollments with grades
SELECT se.*, s.name as subject_name, s.code as subject_code
FROM subject_enrollments se
JOIN academic_year_strand_subjects ayss ON se.academic_year_strand_subject_id = ayss.id
JOIN subjects s ON ayss.subject_id = s.id
WHERE se.student_enrollment_id IN (
    SELECT id FROM student_enrollments WHERE student_id = [YOUR_STUDENT_ID]
);
```

## Common Issues & Solutions

### Issue 1: Chart not displaying
**Solution:**
- Check browser console for JavaScript errors
- Verify Chart.js CDN is accessible
- Clear browser cache

### Issue 2: No grades showing
**Solution:**
- Verify student has enrollments in database
- Check if grades are populated (fq_grade, sq_grade, etc.)
- Verify academic year is correct

### Issue 3: Sidebar items not highlighting
**Solution:**
- Check route names in `web.php`
- Verify `$routeIs()` function in template
- Clear Laravel route cache: `php artisan route:clear`

### Issue 4: Page not loading
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify all controllers are properly created
- Run `composer dump-autoload`

### Issue 5: Performance data showing 0%
**Solution:**
- This is expected with mock data
- Implement real performance calculation from assessment records
- Check `GradeController` line 75-82

## Automated Testing (Optional)

Create automated tests:

```php
// tests/Feature/GradePageTest.php
public function test_student_can_view_grades()
{
    $student = Student::factory()->create();
    
    $response = $this->actingAs($student, 'student')
                     ->get(route('student.grades.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('student.grades.index');
}

// tests/Feature/DecisionSupportTest.php
public function test_student_can_view_decision_support()
{
    $student = Student::factory()->create();
    
    $response = $this->actingAs($student, 'student')
                     ->get(route('student.decision-support.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('student.decision-support.index');
}
```

Run tests:
```powershell
php artisan test
```

## Acceptance Criteria

✅ **Must Have:**
- [ ] Grades page displays correctly
- [ ] Decision Support System page displays correctly
- [ ] Sidebar navigation works
- [ ] Data loads from database
- [ ] Charts render properly
- [ ] No console errors
- [ ] Responsive on mobile

✅ **Nice to Have:**
- [ ] Real performance data instead of mock
- [ ] PDF export functionality
- [ ] Email notifications
- [ ] Historical trend analysis

## Sign-Off

**Tested by:** _______________  
**Date:** _______________  
**Result:** [ ] Pass [ ] Fail  
**Notes:** _______________________________________________

---

## Next Steps After Testing

1. ✅ If all tests pass → Deploy to production
2. ⚠️ If issues found → Document and fix issues
3. 📝 Collect user feedback
4. 🔄 Iterate and improve based on feedback
5. 📈 Monitor usage analytics

## Support Resources

- **Documentation**: `README_GRADES_DSS.md`
- **Quick Reference**: `QUICK_REFERENCE_GRADES_DSS.md`
- **Architecture**: `ARCHITECTURE_GRADES_DSS.md`
- **Implementation Summary**: `IMPLEMENTATION_SUMMARY_GRADES_DSS.md`
