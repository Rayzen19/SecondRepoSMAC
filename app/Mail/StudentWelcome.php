<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentWelcome extends Mailable
{
	use Queueable, SerializesModels;

	public string $studentName;
	public string $email;
	public string $password;
	public string $loginUrl;

	/**
	 * Create a new message instance.
	 */
	public function __construct(string $studentName, string $email, string $password, ?string $loginUrl = null)
	{
		$this->studentName = $studentName;
		$this->email = $email;
		$this->password = $password;
		$this->loginUrl = $loginUrl ?: route('student.auth.loginForm');
	}

	/**
	 * Build the message.
	 */
	public function build(): self
	{
		return $this
			->subject('Welcome to ' . config('app.name'))
			->view('emails.student_welcome')
			->with([
				'studentName' => $this->studentName,
				'email' => $this->email,
				'password' => $this->password,
				'loginUrl' => $this->loginUrl,
				'appName' => config('app.name'),
			]);
	}
}

