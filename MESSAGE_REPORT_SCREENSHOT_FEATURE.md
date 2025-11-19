# Message Report Screenshot Feature - Implementation Summary

## Overview
Added screenshot/image attachment functionality to the message reporting system, allowing users to provide visual evidence when reporting inappropriate messages.

## Features Implemented

### 1. Frontend (Report Modal)
**File:** `resources/views/teacher/messages/messenger.blade.php`

#### HTML Changes:
- Added file input for screenshot upload (accepts image files only)
- Added preview container to show selected image before submission
- Added remove button to clear selected screenshot
- Maximum file size: 5MB

#### JavaScript Functionality:
- **Image Preview**: Automatically shows preview when user selects an image
- **File Validation**: 
  - Checks file size (max 5MB)
  - Validates file type (only image files allowed)
  - Shows alert messages for invalid files
- **Remove Function**: Clears the input and hides the preview
- **Form Submission**: Screenshot is automatically included in FormData when report is submitted

### 2. Backend (Controller)
**File:** `app/Http/Controllers/Teacher/MessageController.php`

#### Changes in `reportMessage()` method:
- Added validation for screenshot field: `'screenshot' => 'nullable|image|max:5120'` (5MB)
- Implemented file upload handling using Laravel Storage
- Stores uploaded screenshots in `storage/app/public/message_reports/` directory
- Saves file path in database for later retrieval

### 3. Database
**Migration:** `2025_11_10_101749_add_screenshot_path_to_message_reports_table.php`

#### Schema Changes:
- Added `screenshot_path` column (nullable string)
- Positioned after `details` column
- Stores relative path to uploaded screenshot

### 4. Model
**File:** `app/Models/MessageReport.php`

#### Changes:
- Added `screenshot_path` to `$fillable` array
- Allows mass assignment of screenshot path

### 5. Admin View (Report Details)
**File:** `resources/views/admin/message-reports/show.blade.php` (NEW)

#### Features:
- **Report Information Section**: Shows all report details including screenshot
- **Screenshot Display**: 
  - Thumbnail preview (max 300px height)
  - Click to view full size in modal
  - Link to download original image
- **Reported Message Section**: Shows the actual message content
- **Admin Review Section**: Shows review notes and reviewer information
- **Action Buttons**: Mark as reviewed, take action, or dismiss
- **Image Modal**: Full-size lightbox view for screenshot evidence

### 6. File Storage
- Storage location: `storage/app/public/message_reports/`
- Public access via: `public/storage/message_reports/` (symbolic link)
- Storage link already configured: `php artisan storage:link`

## User Flow

### Reporter Side:
1. User clicks "Report" on a message
2. Selects reason for report
3. Optionally adds details
4. **NEW:** Can attach screenshot evidence
   - Click "Choose File" button
   - Select image file (JPG, PNG, GIF, etc.)
   - Preview appears automatically
   - Can remove and choose different image
5. Submit report

### Admin Side:
1. View report list in admin dashboard
2. Click "View" to see full report details
3. **NEW:** View screenshot evidence if provided
   - Thumbnail shown in report details
   - Click to view full size
   - Can download original image
4. Review report and take appropriate action

## Technical Specifications

### File Validation:
- **Allowed Types**: All image MIME types (image/*)
- **Max Size**: 5MB (5120 KB)
- **Frontend Validation**: File type and size checked before upload
- **Backend Validation**: Laravel validates with `image|max:5120` rule

### Storage:
- **Disk**: `public` (configured in `config/filesystems.php`)
- **Path**: `message_reports/`
- **Naming**: Laravel auto-generates unique filename using hash

### Security:
- Only authenticated users can upload
- Only users involved in conversation can report
- File type restricted to images only
- File size limited to prevent abuse
- Stored outside web root (accessed via symbolic link)

## Routes Used

### Teacher Routes:
- `POST /teacher/messages/{message}/report` - Submit report with screenshot

### Admin Routes:
- `GET /admin/message-reports` - List all reports
- `GET /admin/message-reports/{report}` - View report details with screenshot
- `POST /admin/message-reports/{report}/status` - Update report status

## Database Schema

### Table: `message_reports`
```sql
- id (bigint, primary key)
- message_id (bigint, foreign key)
- reported_by (bigint, foreign key)
- reason (varchar)
- details (text, nullable)
- screenshot_path (varchar, nullable) ← NEW
- status (enum: pending/reviewed/dismissed/action_taken)
- reviewed_by (bigint, foreign key, nullable)
- admin_notes (text, nullable)
- reviewed_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Testing Checklist

- [ ] Upload image with report (should show preview)
- [ ] Submit report with image (should upload successfully)
- [ ] View report in admin panel (should display screenshot)
- [ ] Click screenshot to view full size (should open modal)
- [ ] Test file size validation (reject files > 5MB)
- [ ] Test file type validation (reject non-image files)
- [ ] Remove screenshot before submit (should allow submission without image)
- [ ] Upload different image formats (JPG, PNG, GIF)
- [ ] Check storage folder (files should be saved in `storage/app/public/message_reports/`)
- [ ] Check public access (images accessible via browser at `/storage/message_reports/filename`)

## Files Modified/Created

### Modified:
1. `resources/views/teacher/messages/messenger.blade.php` - Added upload field and JavaScript
2. `app/Http/Controllers/Teacher/MessageController.php` - Added file upload handling
3. `app/Models/MessageReport.php` - Added screenshot_path to fillable

### Created:
1. `database/migrations/2025_11_10_101749_add_screenshot_path_to_message_reports_table.php`
2. `resources/views/admin/message-reports/show.blade.php` - Full report detail view

### Deleted:
1. `database/migrations/2025_11_10_094839_create_message_reports_table.php` - Duplicate migration

## Notes

- Screenshots are **optional** - users can still report without evidence
- Admin can download original screenshot file for investigation
- Screenshot is preserved even if report status changes
- If message is deleted, screenshot evidence remains in database
- Storage disk 'public' must be configured in `config/filesystems.php`
- Symbolic link must exist: `public/storage` → `storage/app/public`

## Future Enhancements (Optional)

- [ ] Add multiple image uploads (gallery of evidence)
- [ ] Image compression before upload to save space
- [ ] Watermark screenshots with timestamp
- [ ] OCR text extraction from screenshots
- [ ] Image annotation tools (highlight/draw on screenshot)
- [ ] Automatic screenshot capture from browser
- [ ] Delete screenshot when report is dismissed (cleanup)
- [ ] Image viewing history in admin panel
