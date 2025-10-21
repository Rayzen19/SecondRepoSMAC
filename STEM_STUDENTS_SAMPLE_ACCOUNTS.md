# 20 STEM Student Account Samples

## Overview
This document contains the login credentials for 20 sample STEM student accounts created in the system.

**Academic Year:** 2025-2026  
**Program/Strand:** STEM (Science, Technology, Engineering and Mathematics)  
**Status:** Active  
**Created:** October 20, 2025

---

## Student Login Credentials

| No. | Name | Student Number | Email | Password |
|-----|------|----------------|-------|----------|
| 1 | Maria Cruz | 2025-00001 | maria.cruz460@stem.student.test | Stem@7022 |
| 2 | Juan Cruz | 2025-00002 | juan.cruz429@stem.student.test | Stem@9042 |
| 3 | Sofia Garcia | 2025-00003 | sofia.garcia559@stem.student.test | Stem@9787 |
| 4 | Miguel Santos | 2025-00004 | miguel.santos342@stem.student.test | Stem@4917 |
| 5 | Isabella Reyes | 2025-00005 | isabella.reyes209@stem.student.test | Stem@8196 |
| 6 | Gabriel Mendoza | 2025-00006 | gabriel.mendoza522@stem.student.test | Stem@5681 |
| 7 | Mia Rivera | 2025-00007 | mia.rivera907@stem.student.test | Stem@8159 |
| 8 | Luis Morales | 2025-00008 | luis.morales751@stem.student.test | Stem@2483 |
| 9 | Ana Ortega | 2025-00009 | ana.ortega622@stem.student.test | Stem@1831 |
| 10 | Carlos Ramos | 2025-00010 | carlos.ramos752@stem.student.test | Stem@5555 |
| 11 | Elena Fernandez | 2025-00011 | elena.fernandez762@stem.student.test | Stem@7361 |
| 12 | Diego Lopez | 2025-00012 | diego.lopez149@stem.student.test | Stem@8282 |
| 13 | Camila Martinez | 2025-00013 | camila.martinez805@stem.student.test | Stem@3327 |
| 14 | Rafael Perez | 2025-00014 | rafael.perez773@stem.student.test | Stem@7889 |
| 15 | Valentina Sanchez | 2025-00015 | valentina.sanchez431@stem.student.test | Stem@3227 |
| 16 | Adrian Gomez | 2025-00016 | adrian.gomez737@stem.student.test | Stem@9581 |
| 17 | Luna Torres | 2025-00017 | luna.torres356@stem.student.test | Stem@9488 |
| 18 | Sebastian Diaz | 2025-00018 | sebastian.diaz591@stem.student.test | Stem@6884 |
| 19 | Chloe Aguilar | 2025-00019 | chloe.aguilar835@stem.student.test | Stem@8165 |
| 20 | Nathan Castro | 2025-00020 | nathan.castro324@stem.student.test | Stem@2165 |

---

## How to Login

1. Navigate to the student login page
2. Enter the **Email** from the table above
3. Enter the corresponding **Password**
4. Click "Login"

---

## Student Details

All students have the following characteristics:

- **Program:** STEM (Science, Technology, Engineering and Mathematics)
- **Status:** Active
- **Academic Year:** 2025-2026
- **Age Range:** 15-18 years old
- **Address:** Sample Address, City, Province
- **Guardian Information:** Auto-generated for each student

---

## Database Tables Affected

### `students` table
- Created 20 new student records
- Student numbers: 2025-00001 to 2025-00020
- All have encrypted passwords stored in `generated_password_encrypted` field

### `users` table
- Created 20 corresponding user accounts
- Type: `student`
- All emails are verified (`email_verified_at` is set)
- Passwords are hashed using Laravel's Hash facade

---

## Testing Purposes

These sample accounts can be used for:
- Testing student login functionality
- Testing STEM-specific features
- Enrollment testing
- Grade management testing
- Attendance tracking
- Student profile viewing
- Dashboard functionality

---

## Seeder Information

**Seeder Class:** `StemStudentsSampleSeeder`  
**Location:** `database/seeders/StemStudentsSampleSeeder.php`

To run this seeder again:
```bash
php artisan db:seed --class=StemStudentsSampleSeeder
```

**Note:** Running the seeder multiple times will create additional students with incremented student numbers.

---

## Security Notes

⚠️ **Important:**
- These are sample/test accounts only
- Passwords are visible in this document for testing purposes
- Do NOT use these patterns for production accounts
- Consider removing or updating these accounts before going live
- All passwords follow the pattern: `Stem@XXXX` (where XXXX is a random 4-digit number)

---

## Quick Access Credentials (First 5 Students)

For quick testing, here are the first 5 students:

1. **Maria Cruz**
   - Email: `maria.cruz460@stem.student.test`
   - Password: `Stem@7022`
   - Student #: 2025-00001

2. **Juan Cruz**
   - Email: `juan.cruz429@stem.student.test`
   - Password: `Stem@9042`
   - Student #: 2025-00002

3. **Sofia Garcia**
   - Email: `sofia.garcia559@stem.student.test`
   - Password: `Stem@9787`
   - Student #: 2025-00003

4. **Miguel Santos**
   - Email: `miguel.santos342@stem.student.test`
   - Password: `Stem@4917`
   - Student #: 2025-00004

5. **Isabella Reyes**
   - Email: `isabella.reyes209@stem.student.test`
   - Password: `Stem@8196`
   - Student #: 2025-00005

---

**Generated on:** October 20, 2025  
**System:** NEWSMAC School Management System
