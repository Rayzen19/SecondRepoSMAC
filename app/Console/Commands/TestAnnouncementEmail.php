<?php

namespace App\Console\Commands;

use App\Jobs\SendAnnouncementNotifications;
use App\Models\Announcement;
use Illuminate\Console\Command;

class TestAnnouncementEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcement:test-email {announcement_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test announcement email notifications by sending to all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $announcementId = $this->argument('announcement_id');

        if ($announcementId) {
            // Test with specific announcement
            $announcement = Announcement::find($announcementId);
            
            if (!$announcement) {
                $this->error("Announcement with ID {$announcementId} not found!");
                return 1;
            }
        } else {
            // Get the latest active announcement
            $announcement = Announcement::active()->latest()->first();
            
            if (!$announcement) {
                $this->error('No active announcements found! Please create an announcement first.');
                return 1;
            }
        }

        $this->info("Testing email notifications for announcement:");
        $this->line("ID: {$announcement->id}");
        $this->line("Title: {$announcement->title}");
        $this->newLine();

        if ($this->confirm('Do you want to send test emails to all users?', true)) {
            $this->info('Dispatching email notification job...');
            
            SendAnnouncementNotifications::dispatch($announcement);
            
            $this->newLine();
            $this->info('✓ Email notification job has been queued!');
            $this->line('The emails will be sent by the queue worker.');
            $this->newLine();
            $this->line('To process the queue, run: php artisan queue:work');
            $this->line('Or use the provided script: start-queue-worker.bat (Windows) or start-queue-worker.sh (Linux/Mac)');
            
            return 0;
        }

        $this->info('Test cancelled.');
        return 0;
    }
}
