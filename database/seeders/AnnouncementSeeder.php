<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to School Year 2025-2026',
                'content' => 'We are excited to welcome all students, parents, and staff to the new academic year! This year brings new opportunities for growth, learning, and community building. Let\'s make it a great year together!',
                'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'published_at' => Carbon::now()->subDays(7),
                'expires_at' => null,
            ],
            [
                'title' => 'Enrollment Period Extended',
                'content' => 'Good news! We have extended our enrollment period until October 31, 2025. This gives you more time to complete your registration and secure your spot for the upcoming school year. Don\'t miss this opportunity!',
                'image_url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'published_at' => Carbon::now()->subDays(3),
                'expires_at' => Carbon::parse('2025-10-31 23:59:59'),
            ],
            [
                'title' => 'Parent-Teacher Conference Schedule',
                'content' => 'Mark your calendars! The first Parent-Teacher Conference of the school year will be held on November 15-16, 2025. This is a great opportunity to meet your child\'s teachers and discuss their progress. More details to follow.',
                'image_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'published_at' => Carbon::now()->subDays(1),
                'expires_at' => Carbon::parse('2025-11-16 23:59:59'),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        $this->command->info('✅ Sample announcements seeded successfully!');
    }
}
