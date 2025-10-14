Email setup for student welcome mail

1) Configure .env for mail (example using Mailtrap or Gmail SMTP)

Mailtrap example:

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-school.test
MAIL_FROM_NAME="Smart School"

Gmail SMTP example (require app password):

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=johnraymondbarrogo08@gmail.com
MAIL_PASSWORD=B@rr0g0123
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yjohnraymondbarrogo08@gmail.com
MAIL_FROM_NAME="Smart School"

2) Ensure config cache is cleared after editing .env

php artisan config:clear

3) Where it's used

- When an admin creates a student at Admin > Students > Create, the app:
  - Generates a strong temporary password;
  - Creates a linked user account (type=student);
  - Sends the StudentWelcome email to the student's personal email with login details and link to the student login page.
