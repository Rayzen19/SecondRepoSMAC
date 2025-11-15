<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old password reset OTPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired OTPs...');

        // Delete expired or used OTPs older than 24 hours
        $deleted = DB::table('password_otps')
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                    ->orWhereNotNull('used_at');
            })
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        $this->info("Deleted {$deleted} expired/used OTP records.");
        
        return Command::SUCCESS;
    }
}
