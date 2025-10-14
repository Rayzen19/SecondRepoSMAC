# Quick Test Guide - Student Profile Feature

## Prerequisites
✅ Storage link created: `php artisan storage:link`
✅ Web server running (Apache/XAMPP)
✅ Database connected and migrated

## Test Checklist

### Test 1: View Profile
1. [ ] Log in as a student at `/student/login`
2. [ ] Click "My Profile" in the sidebar
3. [ ] Verify you see:
   - Default avatar or existing profile picture
   - Student number, name, gender, birthdate
   - Email, mobile number, address
   - Program and academic year
   - Guardian information
   - "Edit Profile" button
   - "Upload New Picture" form
   - "Change Password" button

**Expected Result:** All information displays correctly with proper formatting

---

### Test 2: Upload Profile Picture
1. [ ] Go to profile page (`/student/profile`)
2. [ ] Click "Choose File" in Profile Picture section
3. [ ] Select an image file (JPG, PNG, or GIF)
4. [ ] Click "Upload New Picture"
5. [ ] Verify:
   - Success message appears
   - New image displays in profile
   - Old image is replaced (if there was one)
   - "Remove Picture" button appears

**Expected Result:** Image uploads successfully and displays immediately

**Test with invalid file:**
- [ ] Try uploading a text file (.txt) - should show validation error
- [ ] Try uploading file > 2MB - should show validation error

---

### Test 3: Remove Profile Picture
1. [ ] Go to profile page (with uploaded picture)
2. [ ] Click "Remove Picture" button
3. [ ] Confirm the deletion
4. [ ] Verify:
   - Success message appears
   - Default avatar shows instead of photo
   - "Remove Picture" button disappears

**Expected Result:** Picture is removed and default avatar shows

---

### Test 4: Edit Profile Information
1. [ ] Go to profile page
2. [ ] Click "Edit Profile" button
3. [ ] Update the following fields:
   - [ ] Email address
   - [ ] Mobile number
   - [ ] Address
   - [ ] Guardian name
   - [ ] Guardian contact
   - [ ] Guardian email
4. [ ] Click "Save Changes"
5. [ ] Verify:
   - Success message appears
   - Redirected to profile page
   - All changes are visible

**Expected Result:** Profile updates successfully

**Test validation:**
- [ ] Try invalid email format - should show error
- [ ] Leave required fields empty - should show error
- [ ] Use another student's email - should show "already taken" error
- [ ] Click "Cancel" - should return to profile without saving

---

### Test 5: Change Password
1. [ ] Go to profile page
2. [ ] Click "Change Password" in Security section
3. [ ] Enter:
   - Current password
   - New password (min 8 characters)
   - Confirm new password
4. [ ] Click "Change Password"
5. [ ] Verify:
   - Success message appears
   - Redirected to profile page
6. [ ] Log out
7. [ ] Try logging in with OLD password - should fail
8. [ ] Log in with NEW password - should succeed

**Expected Result:** Password changes and login works with new password

**Test validation:**
- [ ] Wrong current password - should show error
- [ ] Password < 8 chars - should show error
- [ ] Password confirmation mismatch - should show error
- [ ] Click "Cancel" - should return to profile without changing

---

### Test 6: Navigation
1. [ ] From dashboard, click "My Profile" in sidebar
2. [ ] Verify active state highlights "My Profile"
3. [ ] Click "Edit Profile"
4. [ ] Click "Back to Profile" button
5. [ ] Click "Change Password"
6. [ ] Click "Back to Profile" button

**Expected Result:** Navigation works smoothly between all profile pages

---

### Test 7: Responsive Design
Test on different screen sizes:
1. [ ] Desktop view (1920x1080) - layout looks good
2. [ ] Tablet view (768x1024) - layout adjusts properly
3. [ ] Mobile view (375x667) - all content accessible

**Expected Result:** Page is responsive and usable on all devices

---

### Test 8: Security
1. [ ] Log out as student
2. [ ] Try accessing `/student/profile` directly
3. [ ] Verify redirect to login page
4. [ ] Log in as different student
5. [ ] Verify you only see YOUR profile

**Expected Result:** Proper authentication and authorization enforced

---

### Test 9: File Storage
After uploading a profile picture:
1. [ ] Check `storage/app/public/profile_pictures/` directory exists
2. [ ] Verify uploaded image file is present
3. [ ] Check `public/storage` symlink exists
4. [ ] Verify image accessible at `/storage/profile_pictures/[filename]`

**Expected Result:** Files stored correctly and accessible via URL

---

### Test 10: Error Handling
Test various error scenarios:
1. [ ] Invalid file upload - proper error message
2. [ ] Duplicate email/contact - proper error message
3. [ ] Missing required fields - proper error message
4. [ ] Wrong password - proper error message
5. [ ] All errors display in user-friendly format

**Expected Result:** Clear, helpful error messages for all validation failures

---

## Common Issues & Solutions

### Issue: Images not displaying
**Solution:** Run `php artisan storage:link` and check APP_URL in .env

### Issue: Upload fails
**Solution:** Check php.ini for upload_max_filesize (should be ≥2M)

### Issue: Validation errors
**Solution:** Check database for duplicate emails/contacts

### Issue: Password change not working
**Solution:** Verify current password is correct

---

## Success Criteria
✅ All profile information displays correctly
✅ Profile picture upload/remove works
✅ Profile editing saves correctly
✅ Password change works and validates
✅ Navigation works smoothly
✅ Responsive on all screen sizes
✅ Security properly enforced
✅ Validation errors display clearly
✅ No console errors
✅ No PHP errors in logs

---

## Browser Testing
Test in multiple browsers:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

All features should work consistently across browsers.

---

## Performance Check
- [ ] Profile page loads quickly (< 1 second)
- [ ] Image upload completes in reasonable time (< 3 seconds)
- [ ] No excessive database queries (check Laravel Debugbar if installed)

---

## Final Verification
Once all tests pass:
1. [ ] Test with actual student account
2. [ ] Upload a real profile picture
3. [ ] Update actual contact information
4. [ ] Change to a secure password
5. [ ] Verify everything persists after logout/login

**All tests passed?** ✅ Feature is ready for production!

---

## Report Issues
If any test fails, check:
1. Laravel logs: `storage/logs/laravel.log`
2. PHP error logs
3. Browser console for JavaScript errors
4. Network tab for failed requests

Document the error message and steps to reproduce.
