<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtp extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $otpCode;
    public string $userType;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $otpCode, string $userType = 'User')
    {
        $this->userName = $userName;
        $this->otpCode = $otpCode;
        $this->userType = ucfirst($userType);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Password Reset OTP - ' . config('app.name'))
            ->view('emails.password_reset_otp')
            ->with([
                'userName' => $this->userName,
                'otpCode' => $this->otpCode,
                'userType' => $this->userType,
                'appName' => config('app.name', 'SMAC'),
                'expiresIn' => 10, // minutes
            ]);
    }
}
