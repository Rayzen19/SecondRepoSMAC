<?php

namespace App\Console\Commands;

use App\Mail\GuardianNotification;
use App\Mail\StudentWelcome;
use App\Models\AcademicYear;
use App\Models\Auth\GuardianUser;
use App\Models\Auth\StudentUser;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Strand;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CreateStudentAccount extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'students:create
        {--first-name= : Student first name}
        {--middle-name= : Student middle name}
        {--last-name= : Student last name}
        {--suffix= : Student suffix}
        {--gender= : Student gender (male/female)}
        {--birthdate= : Student birthdate (YYYY-MM-DD)}
        {--email= : Student email}
        {--mobile= : Student mobile number}
        {--address= : Student address}
        {--program= : Strand code to assign}
        {--status=active : Student status (active/graduated/dropped)}
        {--guardian-email= : Guardian email}
        {--guardian-first-name= : Guardian first name (when creating new guardian)}
        {--guardian-middle-name= : Guardian middle name (when creating new guardian)}
        {--guardian-last-name= : Guardian last name (when creating new guardian)}
        {--guardian-suffix= : Guardian suffix (when creating new guardian)}
        {--guardian-gender= : Guardian gender (male/female, when creating new guardian)}
        {--guardian-mobile= : Guardian mobile number (when creating new guardian)}
        {--guardian-address= : Guardian address (when creating new guardian)}
        {--skip-emails : Skip sending email notifications after creation}';

    /**
     * The console command description.
     */
    protected $description = 'Create a student account (and guardian account when needed) with generated credentials';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Student account creation ---');

        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->error('No active academic year found. Set one active before running this command.');
            return self::FAILURE;
        }

        $strands = Strand::orderBy('name')->get();
        if ($strands->isEmpty()) {
            $this->error('No strands found. Please seed strands first.');
            return self::FAILURE;
        }

        try {
            $studentData = $this->collectStudentData($strands);
            $guardianPlan = $this->collectGuardianPlan();
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        try {
            $result = DB::transaction(function () use ($academicYear, $studentData, $guardianPlan) {
                [$guardian, $guardianPassword, $guardianUserCreated] = $this->resolveGuardian($guardianPlan);

                $guardianContact = $guardian->mobile_number;
                $this->assertUniqueGuardianContact($guardianContact);

                $studentNumber = $this->generateStudentNumber();
                $studentPassword = Str::password(12, symbols: true);

                $student = Student::create([
                    'student_number' => $studentNumber,
                    'first_name' => $studentData['first_name'],
                    'middle_name' => $studentData['middle_name'],
                    'last_name' => $studentData['last_name'],
                    'suffix' => $studentData['suffix'],
                    'gender' => $studentData['gender'],
                    'birthdate' => $studentData['birthdate'],
                    'email' => $studentData['email'],
                    'mobile_number' => $studentData['mobile'],
                    'address' => $studentData['address'],
                    'guardian_name' => $this->formatGuardianName($guardian),
                    'guardian_contact' => $guardianContact,
                    'guardian_email' => $guardian->email,
                    'program' => $studentData['program'],
                    'academic_year' => $academicYear->name,
                    'academic_year_id' => $academicYear->id,
                    'status' => $studentData['status'],
                    'profile_picture' => null,
                ]);

                StudentUser::query()->withoutGlobalScopes()->create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'password' => Hash::make($studentPassword),
                    'type' => 'student',
                    'user_pk_id' => $student->id,
                    'email_verified_at' => now(),
                ]);

                $student->forceFill([
                    'generated_password_encrypted' => Crypt::encryptString($studentPassword),
                ])->save();

                if ($guardian->trashed()) {
                    $guardian->restore();
                }

                $student->guardians()->syncWithoutDetaching([$guardian->id]);

                return [
                    'student' => $student,
                    'student_password' => $studentPassword,
                    'guardian' => $guardian,
                    'guardian_password' => $guardianPassword,
                    'guardian_user_created' => $guardianUserCreated,
                ];
            });
        } catch (Throwable $exception) {
            $this->error('Creation failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        $student = $result['student'];
        $guardian = $result['guardian'];
        $studentPassword = $result['student_password'];
        $guardianPassword = $result['guardian_password'];
        $guardianUserCreated = $result['guardian_user_created'];

        $this->info('Student created successfully:');
        $this->table([
            'Student Number',
            'Student Email',
            'Student Password',
            'Guardian Email',
            'Guardian Password',
        ], [[
            $student->student_number,
            $student->email,
            $studentPassword,
            $guardian->email,
            $guardianPassword ? $guardianPassword : 'unchanged',
        ]]);

        if (!$this->option('skip-emails')) {
            $this->sendEmails($student, $studentPassword, $guardian, $guardianPassword, $guardianUserCreated);
        } else {
            $this->warn('Emails skipped by --skip-emails flag.');
        }

        $this->comment('Provide these credentials securely to the student (and guardian if applicable).');

        return self::SUCCESS;
    }

    /**
     * Gather student input from options or interactive prompts.
     */
    private function collectStudentData($strands): array
    {
        $firstName = $this->value('first-name', 'Student first name');
        $middleName = $this->value('middle-name', 'Student middle name', required: false, allowEmpty: true);
        $lastName = $this->value('last-name', 'Student last name');
        $suffix = $this->value('suffix', 'Student suffix', required: false, allowEmpty: true);

        $gender = $this->value('gender', 'Student gender (male/female)', validator: function (string $value) {
            $value = strtolower($value);
            if (!in_array($value, ['male', 'female'], true)) {
                throw new RuntimeException('Gender must be male or female.');
            }
            return $value;
        });

        $birthdate = $this->value('birthdate', 'Student birthdate (YYYY-MM-DD)', validator: function (string $value) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $value);
            } catch (Throwable) {
                throw new RuntimeException('Birthdate must use YYYY-MM-DD format.');
            }

            if ($date->isFuture()) {
                throw new RuntimeException('Birthdate cannot be in the future.');
            }

            return $date->format('Y-m-d');
        });

        $email = $this->value('email', 'Student email', validator: function (string $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Enter a valid email address.');
            }

            if (Student::withTrashed()->where('email', $value)->exists()) {
                throw new RuntimeException('A student with this email already exists.');
            }

            if (StudentUser::query()->withoutGlobalScopes()->where('email', $value)->exists()) {
                throw new RuntimeException('A user record with this email already exists.');
            }

            return $value;
        });

        $mobile = $this->value('mobile', 'Student mobile number', validator: function (string $value) {
            if (!preg_match('/^\d{10,15}$/', $value)) {
                throw new RuntimeException('Mobile number must be 10-15 digits.');
            }

            if (Student::withTrashed()->where('mobile_number', $value)->exists()) {
                throw new RuntimeException('Mobile number already used by another student.');
            }

            return $value;
        });

        $address = $this->value('address', 'Student address', required: false, allowEmpty: true);

        $program = $this->resolveProgram($strands);

        $status = $this->value('status', 'Student status (active/graduated/dropped)', validator: function (string $value) {
            $value = strtolower($value);
            if (!in_array($value, ['active', 'graduated', 'dropped'], true)) {
                throw new RuntimeException('Status must be active, graduated, or dropped.');
            }
            return $value;
        }, default: 'active');

        return [
            'first_name' => $firstName,
            'middle_name' => $middleName ?: null,
            'last_name' => $lastName,
            'suffix' => $suffix ?: null,
            'gender' => $gender,
            'birthdate' => $birthdate,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address ?: null,
            'program' => $program,
            'status' => $status,
        ];
    }

    /**
     * Resolve guardian plan (existing or to-be-created).
     */
    private function collectGuardianPlan(): array
    {
        $guardianEmail = $this->value('guardian-email', 'Guardian email', validator: function (string $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Enter a valid guardian email.');
            }
            return $value;
        });

        $existingGuardian = Guardian::withTrashed()->where('email', $guardianEmail)->first();
        if ($existingGuardian) {
            $this->info('Linking existing guardian: ' . $existingGuardian->name . ' [' . $existingGuardian->guardian_number . ']');
            return [
                'mode' => 'existing',
                'guardian' => $existingGuardian,
            ];
        }

        $firstName = $this->value('guardian-first-name', 'Guardian first name');
        $middleName = $this->value('guardian-middle-name', 'Guardian middle name', required: false, allowEmpty: true);
        $lastName = $this->value('guardian-last-name', 'Guardian last name');
        $suffix = $this->value('guardian-suffix', 'Guardian suffix', required: false, allowEmpty: true);

        $gender = $this->value('guardian-gender', 'Guardian gender (male/female)', validator: function (string $value) {
            $value = strtolower($value);
            if (!in_array($value, ['male', 'female'], true)) {
                throw new RuntimeException('Guardian gender must be male or female.');
            }
            return $value;
        });

        $mobile = $this->value('guardian-mobile', 'Guardian mobile number', validator: function (string $value) {
            if (!preg_match('/^\d{10,15}$/', $value)) {
                throw new RuntimeException('Guardian mobile number must be 10-15 digits.');
            }

            if (Guardian::withTrashed()->where('mobile_number', $value)->exists()) {
                throw new RuntimeException('Guardian mobile number already used by another guardian.');
            }

            return $value;
        });

        $address = $this->value('guardian-address', 'Guardian address', required: false, allowEmpty: true);

        return [
            'mode' => 'create',
            'data' => [
                'email' => $guardianEmail,
                'first_name' => $firstName,
                'middle_name' => $middleName ?: null,
                'last_name' => $lastName,
                'suffix' => $suffix ?: null,
                'gender' => $gender,
                'mobile_number' => $mobile,
                'address' => $address ?: null,
            ],
        ];
    }

    /**
     * Create or fetch guardian and (optionally) user account.
     */
    private function resolveGuardian(array $plan): array
    {
        if ($plan['mode'] === 'existing') {
            $guardian = $plan['guardian'];

            $guardianUser = GuardianUser::query()->where('email', $guardian->email)->first();
            if (!$guardianUser) {
                $password = Str::password(12, symbols: true);
                GuardianUser::query()->withoutGlobalScopes()->create([
                    'name' => $guardian->name,
                    'email' => $guardian->email,
                    'password' => Hash::make($password),
                    'type' => 'guardian',
                    'user_pk_id' => $guardian->id,
                    'email_verified_at' => now(),
                ]);

                $guardian->forceFill([
                    'generated_password_encrypted' => Crypt::encryptString($password),
                ])->save();

                return [$guardian, $password, true];
            }

            return [$guardian, null, false];
        }

        $data = $plan['data'];
        $guardianNumber = $this->generateGuardianNumber();

        $guardian = Guardian::create([
            'guardian_number' => $guardianNumber,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'address' => $data['address'],
            'status' => 'active',
            'profile_picture' => null,
        ]);

        $password = Str::password(12, symbols: true);

        GuardianUser::query()->withoutGlobalScopes()->create([
            'name' => $guardian->name,
            'email' => $guardian->email,
            'password' => Hash::make($password),
            'type' => 'guardian',
            'user_pk_id' => $guardian->id,
            'email_verified_at' => now(),
        ]);

        $guardian->forceFill([
            'generated_password_encrypted' => Crypt::encryptString($password),
        ])->save();

        return [$guardian, $password, true];
    }

    /**
     * Make sure guardian contact is unique across students (constraint on column).
     */
    private function assertUniqueGuardianContact(string $contact): void
    {
        if (Student::withTrashed()->where('guardian_contact', $contact)->exists()) {
            throw new RuntimeException('Guardian contact already used by another student. Update the guardian contact before proceeding.');
        }
    }

    private function generateStudentNumber(): string
    {
        $year = now()->format('Y');
        $prefix = $year . '-';

        $next = 1;
        $lastNumber = Student::withTrashed()
            ->where('student_number', 'like', $prefix . '%')
            ->orderByDesc('student_number')
            ->value('student_number');

        if ($lastNumber) {
            $next = (int) substr($lastNumber, strlen($prefix)) + 1;
        }

        do {
            $candidate = $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (Student::withTrashed()->where('student_number', $candidate)->exists());

        return $candidate;
    }

    private function generateGuardianNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'GRD-' . $year . '-';

        $next = 1;
        $lastNumber = Guardian::withTrashed()
            ->where('guardian_number', 'like', $prefix . '%')
            ->orderByDesc('guardian_number')
            ->value('guardian_number');

        if ($lastNumber) {
            $next = (int) substr($lastNumber, strlen($prefix)) + 1;
        }

        do {
            $candidate = $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (Guardian::withTrashed()->where('guardian_number', $candidate)->exists());

        return $candidate;
    }

    private function formatGuardianName(Guardian $guardian): string
    {
        $parts = [$guardian->first_name];
        if ($guardian->middle_name) {
            $parts[] = $guardian->middle_name;
        }
        $parts[] = $guardian->last_name;
        if ($guardian->suffix) {
            $parts[] = $guardian->suffix;
        }

        return trim(implode(' ', $parts));
    }

    private function sendEmails(Student $student, string $studentPassword, Guardian $guardian, ?string $guardianPassword, bool $guardianUserCreated): void
    {
        try {
            Mail::to($student->email)->send(new StudentWelcome($student->name, $student->email, $studentPassword));
            $this->info('Student welcome email queued.');
        } catch (Throwable $exception) {
            $this->warn('Could not send student welcome email: ' . $exception->getMessage());
        }

        if ($guardianPassword && $guardianUserCreated) {
            try {
                Mail::to($guardian->email)->send(new GuardianNotification(
                    $guardian->name,
                    $student->name,
                    $student->email,
                    $studentPassword,
                    $student->student_number,
                    $guardian->email,
                    $guardianPassword
                ));
                $this->info('Guardian notification email queued.');
            } catch (Throwable $exception) {
                $this->warn('Could not send guardian notification email: ' . $exception->getMessage());
            }
        }
    }

    private function value(string $option, string $prompt, bool $required = true, bool $allowEmpty = false, ?callable $validator = null, ?string $default = null): ?string
    {
        $optionValue = $this->option($option);

        if ($optionValue !== null) {
            $optionValue = trim((string) $optionValue);

            if ($optionValue === '' && $required && !$allowEmpty) {
                throw new RuntimeException("Option --{$option} cannot be empty.");
            }

            if ($optionValue === '' && $allowEmpty) {
                return null;
            }

            return $validator ? $validator($optionValue) : $optionValue;
        }

        if (!$this->input->isInteractive()) {
            if ($required && !$allowEmpty) {
                throw new RuntimeException("Missing required option --{$option} when running non-interactively.");
            }
            return null;
        }

        return $this->askValidated($prompt, $validator, $allowEmpty, $default);
    }

    private function askValidated(string $prompt, ?callable $validator = null, bool $allowEmpty = false, ?string $default = null): ?string
    {
        while (true) {
            $value = $this->ask($prompt, $default);
            $value = $value === null ? '' : trim($value);

            if ($value === '' && $allowEmpty) {
                return null;
            }

            if ($value === '' && !$allowEmpty) {
                $this->warn('This field is required.');
                continue;
            }

            if ($validator) {
                try {
                    return $validator($value);
                } catch (RuntimeException $exception) {
                    $this->error($exception->getMessage());
                }
            } else {
                return $value;
            }
        }
    }

    private function resolveProgram($strands): string
    {
        $optionProgram = $this->option('program');
        if ($optionProgram !== null) {
            $optionProgram = trim($optionProgram);
            $strand = $strands->firstWhere('code', $optionProgram);
            if (!$strand) {
                throw new RuntimeException("Unknown strand code: {$optionProgram}");
            }
            return $strand->code;
        }

        if (!$this->input->isInteractive()) {
            throw new RuntimeException('Missing required --program option when running non-interactively.');
        }

        $this->line('Available programs:');
        $rows = [];
        $normalized = $strands->values();
        foreach ($normalized as $index => $strand) {
            $rows[] = [
                $index + 1,
                $strand->code,
                $strand->name,
            ];
        }
        $this->table(['#', 'Code', 'Name'], $rows);

        while (true) {
            $choice = $this->ask('Select program by number');
            if (!is_numeric($choice)) {
                $this->warn('Enter a number from the list.');
                continue;
            }

            $index = (int) $choice - 1;
            if (!isset($rows[$index])) {
                $this->warn('Selection out of range.');
                continue;
            }

            return $normalized[$index]->code;
        }
    }
}
