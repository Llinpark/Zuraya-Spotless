<?php
// Simple contact form handler for Zuraya Spotless
// Uses a honeypot field named "website" to trap bots.

$receiving_email_address = 'info@zuraya.co.ke';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request';
    exit;
}

// Honeypot: if this field is filled, silently accept (treat as spam)
$honeypot = isset($_POST['website']) ? trim($_POST['website']) : '';
if ($honeypot !== '') {
    // pretend success to the client to avoid giving feedback to bots
    echo 'OK';
    exit;
}

// Collect and sanitize input
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : 'New message from website';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    echo 'Please complete all required fields.';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Please provide a valid email address.';
    exit;
}

// Build email
$mail_subject = '[' . 'Zuraya Spotless' . '] ' . $subject;
$body = "Name: $name\n";
$body .= "Email: $email\n";
if ($phone !== '') { $body .= "Phone: $phone\n"; }
$body .= "\nMessage:\n" . $message . "\n";
$body .= "\n--\nThis message was sent from the Zuraya Spotless website.";

$headers = "From: " . $name . " <" . $email . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Attempt to send using PHPMailer if available (recommended)
// Try non-Composer PHPMailer bundle in forms/PHPMailer/src/
$localPHPMailer = __DIR__ . '/PHPMailer/src/PHPMailer.php';
if (file_exists($localPHPMailer)) {
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        $smtpHost = getenv('SMTP_HOST');
        if ($smtpHost) {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER') ?: '';
            $mail->Password = getenv('SMTP_PASS') ?: '';
            $secure = getenv('SMTP_SECURE');
            if ($secure) {
                $mail->SMTPSecure = $secure;
            }
            $mail->Port = getenv('SMTP_PORT') ?: 587;
        } else {
            $mail->isMail();
        }

        $mail->setFrom($email, $name);
        $mail->addAddress($receiving_email_address);
        $mail->addReplyTo($email, $name);
        $mail->Subject = $mail_subject;
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';

        $mail->send();
        echo 'OK';
        exit;
    } catch (Exception $e) {
        error_log('PHPMailer (local) error: ' . $e->getMessage());
        // continue to other fallbacks
    }
} else {
    // Try Composer autoload if present (optional)
    $composerAutoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($composerAutoload)) {
        require_once $composerAutoload;
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $smtpHost = getenv('SMTP_HOST');
            if ($smtpHost) {
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = getenv('SMTP_USER') ?: '';
                $mail->Password = getenv('SMTP_PASS') ?: '';
                $mail->SMTPSecure = getenv('SMTP_SECURE') ?: PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = getenv('SMTP_PORT') ?: 587;
            } else {
                $mail->isMail();
            }

            $mail->setFrom($email, $name);
            $mail->addAddress($receiving_email_address);
            $mail->addReplyTo($email, $name);
            $mail->Subject = $mail_subject;
            $mail->Body = $body;
            $mail->CharSet = 'UTF-8';

            $mail->send();
            echo 'OK';
            exit;
        } catch (Exception $e) {
            error_log('PHPMailer (composer) error: ' . $e->getMessage());
        }
    }
}

// Final fallback: PHP mail()
if (@mail($receiving_email_address, $mail_subject, $body, $headers)) {
    echo 'OK';
} else {
    echo 'Could not send email. Please try again later.';
}
?>
