# Quick Guide: 30 Students Maximum Per Section

## 🎯 What Changed?
**Each section can now hold a maximum of 30 students.**

## 📊 How to Check Section Capacity

### On Assigning List Page
Look at the section buttons - they show:
- Current count / Maximum (e.g., "15/30")
- Color indicator:
  - 🟢 **Green Badge** = Plenty of space (0-23 students)
  - 🟡 **Yellow Badge** = Getting full (24-29 students)
  - 🔴 **Red Badge + FULL** = No space left (30 students)

### Example Button Display
```
[G-11 Baby breath] [STEM] [15/30] 🟢    ← Has space
[G-11 Sampaguita] [ABM] [27/30] 🟡     ← Almost full
[G-11 Rose] [HUMSS] [30/30] [FULL] 🔴  ← Full (disabled)
```

## ✅ How to Assign Students

### Step 1: Select Students
- Check the boxes next to students you want to assign
- Or click "Select All" to choose all visible students

### Step 2: Click Section Button
- Click on the section button where you want to assign students
- **Note**: Full sections are grayed out and can't be clicked

### Step 3: System Checks
- System automatically checks if there's enough space
- If yes → Students are assigned ✅
- If no → You'll see an error message ❌

### Step 4: Save
- Click "Save Assignments" button to save to database

## ❌ What Happens If Section is Full?

### Error Message Example
```
Cannot assign students: Section G-11 Baby breath is full or would exceed maximum capacity.

Current: 28/30 students
Trying to add: 5 students
Remaining capacity: 2 students
```

### What to Do
1. **Assign fewer students** - Only assign the remaining capacity
2. **Choose different section** - Pick another section with space
3. **Remove students first** - Remove some students from the full section

## 🔄 Moving Students Between Sections

When you reassign a student from one section to another:
- Old section: Count decreases by 1
- New section: Count increases by 1
- System still checks if new section has space

## 💡 Tips

### Planning Assignments
1. **Check capacities first** - Look at all section counts before starting
2. **Assign strategically** - Fill sections evenly to avoid overcrowding
3. **Watch the colors** - Yellow badges mean you should plan carefully

### If You Need More Space
- **Option 1**: Create additional sections for the same strand/grade
- **Option 2**: Reassign some students to other sections
- **Option 3**: Contact administrator to adjust capacity (requires code change)

## 🎨 Visual Guide

### Healthy Distribution
```
Section A: 🟢 22/30 students ← Good
Section B: 🟢 23/30 students ← Good
Section C: 🟢 21/30 students ← Good
```

### Needs Rebalancing
```
Section A: 🔴 30/30 students [FULL] ← Full!
Section B: 🟢 15/30 students        ← Has space
Section C: 🟢 14/30 students        ← Has space
```
**Suggestion**: Move some students from A to B or C

## 📱 Where This Applies

The 30-student limit is enforced in:
1. ✅ **Admin Assigning List** - When assigning students to sections
2. ✅ **Student Enrollment Management** - When creating/editing enrollments
3. ✅ **All Assignment Methods** - No way to bypass this limit

## ⚠️ Important Notes

- **Limit is per section** - Each section has its own 30-student limit
- **Applies to current year** - Based on active academic year
- **Cannot be exceeded** - System will always block overassignment
- **Saves to database** - Capacity persists across sessions

## 🆘 Common Questions

**Q: Can I temporarily exceed 30 students?**
A: No, the limit is hard-coded and cannot be exceeded.

**Q: What if I need to assign 31 students to a section?**
A: You must create a second section or use an existing section with space.

**Q: Can I change the limit from 30 to something else?**
A: Yes, but requires admin/developer to modify the code (contact IT).

**Q: Do dropped/inactive students count toward the limit?**
A: Yes, all enrollments count regardless of status. Remove them if needed.

**Q: Can I see which students are in a section?**
A: Yes! Click the eye icon or section name to view the student list.

## 🎯 Best Practices

1. **Monitor capacities regularly** - Check section counts weekly
2. **Balance sections** - Try to keep similar numbers in each section
3. **Plan ahead** - Consider capacity when creating sections
4. **Communicate** - Let teachers know about section assignments
5. **Document decisions** - Keep notes on why sections are assigned certain ways

---

**Need Help?** Contact your system administrator or IT support.
