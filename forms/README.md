PHPMailer (non-Composer) setup

This project supports using PHPMailer without Composer by placing the PHPMailer sources under `forms/PHPMailer/src/`.

Steps to install PHPMailer manually (no Composer):

1. Download PHPMailer from GitHub: https://github.com/PHPMailer/PHPMailer
2. Extract the `src/` folder from the release and place it here:

   forms/PHPMailer/src/

   The folder should contain files like `PHPMailer.php`, `SMTP.php`, and `Exception.php`.

3. Configure SMTP environment variables (optional, recommended for reliable delivery):

   - `SMTP_HOST` (e.g. `smtp.mailgun.org`)
   - `SMTP_USER` (SMTP username)
   - `SMTP_PASS` (SMTP password)
   - `SMTP_PORT` (e.g. `587`)
   - `SMTP_SECURE` (e.g. `tls` or `ssl`)

   How to set env vars depends on your hosting environment. If env vars are not set, the script will fall back to the local `mail()` transport.

4. The contact handler will attempt to use a local PHPMailer bundle (`forms/PHPMailer/src/`) first, then Composer autoload (`../vendor/autoload.php`) if present, and finally PHP's native `mail()` as a fallback.

Security note: keep your SMTP credentials out of the repository. Use environment variables or your host's secure settings.
