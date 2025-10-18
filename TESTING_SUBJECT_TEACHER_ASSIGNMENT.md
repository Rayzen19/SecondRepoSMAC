# Testing Subject-Teacher Assignment - Step by Step

## Prerequisites Check ✅
- [x] Academic year active (tested - ID: 7)
- [x] Teachers profiled for subjects (38 links)
- [x] Advisers exist (6 records)
- [x] Backend assignment works (tested successfully)

## Test Steps

### 1. Clear Browser Cache
- Press `Ctrl + Shift + Delete`
- Clear cached images and files
- OR just do a hard refresh: `Ctrl + Shift + R`

### 2. Open the Page
```
http://127.0.0.1:8000/admin/section-advisers
```

### 3. Open Developer Console
- Press `F12`
- Click the **Console** tab
- Clear any existing logs (trash icon)

### 4. Assign an Adviser (if not already done)
- Find **ABM Section 1** (or any section)
- Select a teacher from the **Adviser** dropdown
- Click **"Save All Adviser Assignments"** button at the top
- Wait for green success message

### 5. Open Subject-Teacher Modal
- Click the **users icon** (🧑‍🤝‍🧑) button next to ABM Section 1
- Modal should open showing subjects
- **Check console** - you should see:
  ```
  Fetching teachers for subject 1, rowId: subj-ABM-11-1, current: null
  Teachers API response for subject 1: {success: true, count: 2, teachers: [...]}
  Found 2 teacher(s) for subject 1
  ```

### 6. Select a Teacher
- Find **"Oral Communication"** row (first subject)
- Click the dropdown in the "Assigned Teacher" column
- You should see 2 teachers:
  - John rays Barrogo
  - clyde Bitancor
- Select **John rays Barrogo**

### 7. Click Save Button
- Click the **"💾 Save"** button next to that dropdown
- Button should show "Saving..." briefly
- **Check console** - you should see:
  ```
  === Save Subject Teacher ===
  Row ID: subj-ABM-11-1
  Strand: ABM
  Grade: 11
  Subject ID: 1
  Select value (raw): 15
  Selected Teacher ID (parsed): 15
  Payload: { "strand_code": "ABM", "grade_level": "11", "subject_id": 1, "teacher_id": 15 }
  Posting to URL: http://127.0.0.1:8000/admin/section-advisers/save-subject-teacher
  Response status: 200
  Response ok: true
  Response text: {"success":true,"message":"Teacher assigned successfully."}
  ✅ Save successful
  === Save Subject Teacher Complete ===
  ```

### 8. Verify Success
- Green alert should appear at top: **"Teacher assigned successfully"**
- "Assigned Teacher" column should update to show: **"John rays Barrogo"** (in green/bold)
- Button returns to **"💾 Save"**

### 9. Verify Persistence
- Close the modal
- Reopen it (click users icon again)
- "Oral Communication" row should show **"John rays Barrogo"** already selected

## If It Still Doesn't Work

Copy the **ENTIRE console output** and share it, including:
- All "Fetching teachers..." messages
- All "=== Save Subject Teacher ===" blocks
- Any error messages (in red)
- Response status and data

Also check:
- Network tab in DevTools
- Look for the POST request to `/admin/section-advisers/save-subject-teacher`
- Click on it and check the Response tab
