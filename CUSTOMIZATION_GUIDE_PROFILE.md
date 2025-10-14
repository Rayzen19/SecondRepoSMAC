# Student Profile - Customization Guide

## 🎨 Common Customizations

### 1. Change Profile Picture Size Limit

**Current**: 2MB maximum

**To change to 5MB:**

Edit `app/Http/Controllers/Student/ProfileController.php`:

```php
// Line ~63
$request->validate([
    'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB = 5120KB
]);
```

Also update the message in the view:

Edit `resources/views/student/profile/show.blade.php`:

```blade
<!-- Line ~73 -->
<small class="text-muted">Accepted: JPG, JPEG, PNG, GIF (Max: 5MB)</small>
```

---

### 2. Add More Allowed Image Formats

**To add WebP and SVG support:**

```php
$request->validate([
    'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
]);
```

---

### 3. Auto-Resize Images on Upload

**Install Intervention Image:**

```bash
composer require intervention/image
```

**Update ProfileController:**

```php
use Intervention\Image\Facades\Image;

public function updateProfilePicture(Request $request)
{
    $user = Auth::guard('student')->user();
    $student = Student::findOrFail($user->user_pk_id);

    $request->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Delete old profile picture if exists
    if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
        Storage::disk('public')->delete($student->profile_picture);
    }

    // Process and resize image
    $image = Image::make($request->file('profile_picture'))
        ->fit(500, 500) // Resize to 500x500
        ->encode('jpg', 80); // Convert to JPG with 80% quality

    // Generate unique filename
    $filename = 'profile_' . $student->id . '_' . time() . '.jpg';
    $path = 'profile_pictures/' . $filename;

    // Store processed image
    Storage::disk('public')->put($path, $image);
    
    $student->update([
        'profile_picture' => $path,
    ]);

    return redirect()->route('student.profile.show')
        ->with('success', 'Profile picture updated successfully!');
}
```

---

### 4. Add Profile Picture to Sidebar

**Edit `resources/views/student/components/template.blade.php`:**

Add this after the logo section (around line 30):

```blade
<!-- Student Info with Profile Picture -->
<div class="sidebar-profile text-center py-3 border-bottom">
    @php
        $student = session('student');
    @endphp
    @if($student && isset($student['profile_picture']) && $student['profile_picture'])
        <img src="{{ asset('storage/' . $student['profile_picture']) }}" 
             alt="Profile" 
             class="rounded-circle mb-2" 
             style="width: 60px; height: 60px; object-fit: cover;">
    @else
        <div class="bg-secondary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" 
             style="width: 60px; height: 60px;">
            <i class="ti ti-user" style="font-size: 30px; color: white;"></i>
        </div>
    @endif
    <h6 class="mb-0">{{ $student['name'] ?? 'Student' }}</h6>
    <small class="text-muted">{{ $student['student_number'] ?? '' }}</small>
</div>
```

---

### 5. Add Image Validation in JavaScript (Client-side)

**Add to `resources/views/student/profile/show.blade.php`:**

```blade
@section('content')
<!-- existing content -->

<script>
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    // Check file size (2MB = 2097152 bytes)
    if (file.size > 2097152) {
        alert('File is too large. Maximum size is 2MB.');
        this.value = '';
        return;
    }
    
    // Check file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select a JPG, PNG, or GIF image.');
        this.value = '';
        return;
    }
    
    // Preview image before upload
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.createElement('div');
        preview.innerHTML = `
            <div class="mt-2 p-2 border rounded">
                <img src="${e.target.result}" style="max-width: 200px; max-height: 200px;" class="rounded">
                <p class="mb-0 mt-2 small">Ready to upload: ${file.name}</p>
            </div>
        `;
        // Insert preview below file input
        document.getElementById('profile_picture').parentNode.appendChild(preview);
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
```

---

### 6. Add Last Updated Timestamp

**Update `resources/views/student/profile/show.blade.php`:**

Add this in the Personal Information card:

```blade
<div class="col-12 mt-3">
    <small class="text-muted">
        <i class="ti ti-clock me-1"></i>
        Last updated: {{ $student->updated_at->diffForHumans() }}
    </small>
</div>
```

---

### 7. Add Email Notification on Profile Update

**Create a new Mailable:**

```bash
php artisan make:mail ProfileUpdatedMail
```

**Edit `app/Mail/ProfileUpdatedMail.php`:**

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function build()
    {
        return $this->subject('Profile Updated')
                    ->markdown('emails.student.profile-updated');
    }
}
```

**Create view `resources/views/emails/student/profile-updated.blade.php`:**

```blade
@component('mail::message')
# Profile Updated

Hello {{ $student->first_name }},

Your profile has been successfully updated.

@component('mail::button', ['url' => route('student.profile.show')])
View Profile
@endcomponent

If you did not make this change, please contact the administrator immediately.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

**Update ProfileController:**

```php
use App\Mail\ProfileUpdatedMail;
use Illuminate\Support\Facades\Mail;

public function update(Request $request)
{
    // ... existing code ...
    
    $student->update($validated);
    
    // Send email notification
    Mail::to($student->email)->send(new ProfileUpdatedMail($student));
    
    return redirect()->route('student.profile.show')
        ->with('success', 'Profile updated successfully!');
}
```

---

### 8. Add Profile Completion Percentage

**Add method to Student model:**

```php
// app/Models/Student.php

public function getProfileCompletionAttribute(): int
{
    $fields = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'address',
        'guardian_name',
        'guardian_contact',
        'guardian_email',
        'profile_picture',
        'birthdate',
    ];
    
    $completed = 0;
    foreach ($fields as $field) {
        if (!empty($this->$field)) {
            $completed++;
        }
    }
    
    return round(($completed / count($fields)) * 100);
}
```

**Display in profile view:**

```blade
<!-- Add to show.blade.php -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h6 class="mb-2">Profile Completion</h6>
        <div class="progress" style="height: 20px;">
            <div class="progress-bar" 
                 role="progressbar" 
                 style="width: {{ $student->profile_completion }}%;"
                 aria-valuenow="{{ $student->profile_completion }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ $student->profile_completion }}%
            </div>
        </div>
        @if($student->profile_completion < 100)
            <small class="text-muted mt-2 d-block">
                Complete your profile to unlock all features!
            </small>
        @endif
    </div>
</div>
```

---

### 9. Add Avatar Generator (For students without pictures)

**Use UI Avatars service:**

```blade
<!-- In show.blade.php -->
@if($student->profile_picture)
    <img src="{{ asset('storage/' . $student->profile_picture) }}" 
         alt="Profile Picture" 
         class="rounded-circle mb-3" 
         style="width: 200px; height: 200px; object-fit: cover;">
@else
    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=200&background=0D8ABC&color=fff" 
         alt="Avatar" 
         class="rounded-circle mb-3" 
         style="width: 200px; height: 200px;">
@endif
```

---

### 10. Add Drag & Drop Upload

**Replace file input section in show.blade.php:**

```blade
<div class="upload-area border-2 border-dashed rounded p-4 text-center mb-2" 
     id="dropArea"
     style="border-color: #ddd; cursor: pointer;">
    <i class="ti ti-upload" style="font-size: 48px; color: #999;"></i>
    <p class="mb-2">Drag & drop your photo here</p>
    <p class="text-muted small mb-0">or click to browse</p>
    <input type="file" 
           name="profile_picture" 
           id="profile_picture" 
           class="d-none" 
           accept="image/*">
</div>

<script>
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('profile_picture');

// Click to browse
dropArea.addEventListener('click', () => fileInput.click());

// Drag & Drop events
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => {
        dropArea.style.borderColor = '#0D8ABC';
        dropArea.style.backgroundColor = '#f8f9fa';
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => {
        dropArea.style.borderColor = '#ddd';
        dropArea.style.backgroundColor = 'transparent';
    });
});

dropArea.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        // Trigger form submission or preview
        fileInput.dispatchEvent(new Event('change'));
    }
});
</script>
```

---

### 11. Add Activity Log

**Create migration:**

```bash
php artisan make:migration create_student_activity_logs_table
```

```php
public function up()
{
    Schema::create('student_activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->onDelete('cascade');
        $table->string('action'); // 'profile_updated', 'picture_uploaded', etc.
        $table->text('description')->nullable();
        $table->string('ip_address')->nullable();
        $table->timestamps();
    });
}
```

**Log activity in controller:**

```php
use App\Models\StudentActivityLog;

public function update(Request $request)
{
    // ... existing code ...
    
    $student->update($validated);
    
    // Log activity
    StudentActivityLog::create([
        'student_id' => $student->id,
        'action' => 'profile_updated',
        'description' => 'Updated profile information',
        'ip_address' => $request->ip(),
    ]);
    
    return redirect()->route('student.profile.show')
        ->with('success', 'Profile updated successfully!');
}
```

---

### 12. Change Password Strength Requirements

**For stronger passwords:**

```php
use Illuminate\Validation\Rules\Password;

$request->validate([
    'current_password' => 'required',
    'password' => [
        'required',
        'confirmed',
        Password::min(8)
            ->letters()       // Must contain letters
            ->mixedCase()     // Must contain upper & lowercase
            ->numbers()       // Must contain numbers
            ->symbols()       // Must contain symbols
            ->uncompromised() // Check if password leaked in data breach
    ],
]);
```

---

## 🎨 UI Customization

### Change Color Scheme

Find and replace in profile views:

- Primary button: `btn-primary` → `btn-success` (green)
- Danger button: `btn-danger` → `btn-warning` (yellow)
- Background: `bg-white` → `bg-light`

### Add Icons to Form Labels

```blade
<label for="email" class="form-label">
    <i class="ti ti-mail me-1"></i>Email Address <span class="text-danger">*</span>
</label>
```

### Custom Badge Colors for Status

```blade
@php
    $statusColors = [
        'active' => 'success',
        'graduated' => 'info',
        'dropped' => 'danger',
    ];
    $color = $statusColors[$student->status] ?? 'secondary';
@endphp
<span class="badge bg-{{ $color }}">{{ ucfirst($student->status) }}</span>
```

---

## 📱 Add Mobile-Specific Features

### Use Camera on Mobile

```blade
<input type="file" 
       name="profile_picture" 
       accept="image/*" 
       capture="user"> <!-- Opens camera on mobile -->
```

---

## 🔧 Performance Optimization

### Cache Profile Data

```php
use Illuminate\Support\Facades\Cache;

public function show()
{
    $user = Auth::guard('student')->user();
    
    $student = Cache::remember("student_profile_{$user->user_pk_id}", 600, function () use ($user) {
        return Student::findOrFail($user->user_pk_id);
    });
    
    return view('student.profile.show', compact('student'));
}
```

**Clear cache on update:**

```php
public function update(Request $request)
{
    // ... existing code ...
    
    $student->update($validated);
    
    // Clear cache
    Cache::forget("student_profile_{$student->id}");
    
    return redirect()->route('student.profile.show')
        ->with('success', 'Profile updated successfully!');
}
```

---

## 🎁 Bonus Features

### Add QR Code for Student ID

```bash
composer require simplesoftwareio/simple-qrcode
```

```blade
{!! QrCode::size(200)->generate($student->student_number) !!}
```

### Export Profile as PDF

```bash
composer require barryvdh/laravel-dompdf
```

```php
use PDF;

public function exportPDF()
{
    $user = Auth::guard('student')->user();
    $student = Student::findOrFail($user->user_pk_id);
    
    $pdf = PDF::loadView('student.profile.pdf', compact('student'));
    return $pdf->download('profile.pdf');
}
```

---

**Need more customizations?** Check the Laravel documentation for validation, file uploads, and email notifications!
