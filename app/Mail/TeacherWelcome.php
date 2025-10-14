<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherWelcome extends Mailable
{
	use Queueable, SerializesModels;

	public string $teacherName;
	public string $email;
	public string $password;
	public string $loginUrl;

	/**
	 * Create a new message instance.
	 */
	public function __construct(string $teacherName, string $email, string $password, ?string $loginUrl = null)
	{
		$this->teacherName = $teacherName;
		$this->email = $email;
		$this->password = $password;
		$this->loginUrl = $loginUrl ?: 'http://127.0.0.1:8000/admin/login';
	}

	/**
	 * Build the message.
	 */
	public function build(): self
	{
		return $this
			->subject('Welcome to ' . config('app.name') . ' - Teacher Account')
			->view('emails.teacher_welcome')
			->with([
				'teacherName' => $this->teacherName,
				'email' => $this->email,
				'password' => $this->password,
				'loginUrl' => $this->loginUrl,
				'appName' => config('app.name'),
			]);
	}
}
