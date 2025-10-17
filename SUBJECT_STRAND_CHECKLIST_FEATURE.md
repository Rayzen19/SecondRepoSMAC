# Subject Strand Checklist Feature

## Overview
Added a checklist feature to both the **Subject Create** and **Subject Edit** pages that allows administrators to select which strands have a particular subject. This automatically manages the `strand_subjects` relationship and is reflected in the "Links" column.

## Changes Made

### 1. Controller Updates (`app/Http/Controllers/Admin/SubjectController.php`)

#### `create()` Method
- Fetches all active strands for the checklist
- Passes `allStrands` data to the create view

#### `store()` Method
- Added validation for `strand_ids` array (required, minimum 1 strand)
- Creates subject first, then links to all selected strands
- Each strand link gets default percentages:
  - Written Works: 20%
  - Performance Tasks: 60%
  - Quarterly Assessment: 20%
- Success message shows count of linked strands

#### `edit()` Method
- Loads subject with strand relationships
- Fetches all active strands for the checklist
- Identifies currently linked strands
- Passes data to the view

#### `update()` Method
- Added validation for `strand_ids` array (optional)
- Automatically syncs strand-subject relationships based on checkboxes:
  - **Unchecked strands**: Removes the relationship (soft deletes the `StrandSubject` record)
  - **Newly checked strands**: Creates new `StrandSubject` records with default percentages
  - **Already linked strands**: Keeps existing relationship intact

### 2. View Updates

#### Create Form (`resources/views/admin/subjects/create.blade.php`)
- **Replaced** single strand dropdown with multi-select checklist
- Removed individual percentage fields (uses defaults)
- Moved subject code and name to same row for better layout
- Shows all active strands in a responsive grid (3 columns on large screens, 2 on medium)
- Requires at least one strand to be selected
- Includes helpful text: "Select at least one strand. This subject will be linked to all checked strands."

#### Edit Form (`resources/views/admin/subjects/edit.blade.php`)
- Displays all active strands in a responsive grid (3 columns on large screens, 2 on medium)
- Shows each strand's code and name
- Pre-checks strands that currently have the subject
- Includes helpful text: "Check the strands that should include this subject. This will be reflected in the Links column."
- Styled with Bootstrap card for better visual organization

## How It Works

### Creating a New Subject
1. **Navigate to Create**: Go to `http://127.0.0.1:8000/admin/subjects/create`
2. **Fill in Details**: Enter subject code, name, description, units, type, and semester
3. **Select Strands**: Check boxes for all strands that should have this subject (at least one required)
4. **Save**: Click "Save" button
5. **Result**: Subject is created and automatically linked to all selected strands
6. **Success Message**: Shows "Subject created and linked to X strand(s) successfully"

### Editing an Existing Subject
1. **Navigate to Edit**: Go to `http://127.0.0.1:8000/admin/subjects/{id}/edit`
2. **View Checklist**: See all available strands with checkboxes (currently linked strands are pre-checked)
3. **Modify Selection**: Check/uncheck boxes to add/remove strand associations
4. **Save**: Click "Update" button
5. **Result**: The strand-subject relationships are automatically updated
6. **Links Column**: The changes will be reflected in the "Links" column on the subject list page

## Technical Details

- Uses existing `strand_subjects` pivot table
- Maintains data integrity by soft-deleting removed relationships
- Creates new relationships with sensible default grade percentages
- Preserves existing relationships when strands remain checked
- Handles old form input for validation error scenarios

## Benefits

- **User-Friendly**: Visual checklist instead of complex linking interface
- **Efficient**: Update multiple strand associations in one action
- **Transparent**: Clear indication of which strands have the subject
- **Consistent**: Automatically applies default percentages for new links
- **Reversible**: Uses soft deletes for removed relationships

## Example Use Cases

### Creating a Core Subject for All Strands
If you're creating "General Mathematics" which is required for all strands:
1. Navigate to the create subject page
2. Enter code: "GEN-MATH", name: "General Mathematics", etc.
3. Check all available strands (STEM, ABM, HUMSS, GAS, etc.)
4. Click Save
5. The subject is now available to all checked strands

### Creating a Specialized Subject
If you're creating "Calculus" which is only for STEM:
1. Navigate to the create subject page
2. Enter subject details
3. Check only "STEM" checkbox
4. Click Save
5. The subject only appears for STEM strand

### Editing to Add/Remove Strands
If you have a subject "General Mathematics" and need to add it to a new strand or remove from one:
1. Open the edit page for "General Mathematics"
2. Check the boxes for STEM, ABM, HUMSS, and any new strands
3. Uncheck any strands that should no longer have this subject
4. Click Update
5. The subject now appears in the Links column for the selected strands only

## Future Enhancements

Consider adding:
- Ability to set grade level and custom percentages directly from the checklist
- Visual indicators showing how many subjects each strand has
- Bulk operations for linking subjects to multiple strands
