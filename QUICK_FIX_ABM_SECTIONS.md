# Quick Fix Guide: Remove "All Sections" for ABM

## 🎯 The Issue

ABM shows "All Sections (ID: NULL)" because Jupiter section wasn't linked to ABM strand.

## ✅ Already Fixed (Automatic)

✓ Created link: **2025-2026 + ABM + Jupiter** in database

## 🚀 What You Need to Do NOW

### Step 1: Go to Section & Advisers Page

Navigate to:
```
http://127.0.0.1:8000/admin/section-advisers
```

### Step 2: Select ABM - Grade 11

You should now see **Jupiter** section in the ABM list!

### Step 3: Assign an Adviser

1. Find the **Jupiter** row under ABM
2. Select an adviser from the dropdown
3. Click **"Save All Adviser Assignments"** (green button at top)

⚠️ **Important:** You MUST assign an adviser before you can assign teachers to subjects!

### Step 4: Assign Teacher to Subject

1. Click the **users icon** (🧑‍🤝‍🧑) next to Jupiter section
2. Modal opens showing "Grade 11 Subject-Teacher Assignments"
3. Find **Oral Communication (ENG102)**
4. Select **John Raymond Barrogo** from the dropdown
5. Click the **Save** button (💾)

### Step 5: Verify

Refresh the teacher portal. ABM should now show:
```
✅ Subject: Oral Communication
   Section: JUPITER (instead of "All Sections")
   Students: [correct count]
```

## 🔍 Expected Result

**Before:**
```
STEM - Oral Communication → MARCH ✅
ABM  - Oral Communication → All Sections (ID: NULL) ❌
```

**After:**
```
STEM - Oral Communication → MARCH ✅
ABM  - Oral Communication → JUPITER ✅
```

## ⚠️ Troubleshooting

**If Jupiter doesn't appear in the ABM list:**
```powershell
php scripts/check_abm_sections.php
```
Should show: `ABM Sections in 2025-2026: 1`

**If you can't assign teachers:**
- Make sure you assigned an adviser first!
- Check the browser console for errors

**If "All Sections" still shows:**
- Verify you clicked Save after selecting the teacher
- Check that the green checkmark appeared
- Refresh the teacher portal page

---

**Total time needed:** ~2 minutes

**Difficulty:** Easy - just a few clicks in the admin panel!
