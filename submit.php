<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/config.php';

// Handle both POST and OPTIONS requests (for CORS)
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

// Validate required fields
$required_fields = ['fullName', 'phone', 'email', 'password', 'country', 'category', 'skills'];
$missing = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    $_SESSION['error'] = 'Missing required fields: ' . implode(', ', $missing);
    header('Location: index.html#register');
    exit;
}

// Sanitize input
$fullName = htmlspecialchars(trim($_POST['fullName']));
$phone = htmlspecialchars(trim($_POST['phone']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password']); // Note: Never store plaintext passwords in production
$country = htmlspecialchars(trim($_POST['country']));
$category = htmlspecialchars(trim($_POST['category']));
$skills = htmlspecialchars(trim($_POST['skills']));
$portfolio = isset($_POST['portfolio']) ? htmlspecialchars(trim($_POST['portfolio'])) : 'Not provided';
$terms = isset($_POST['terms']) ? 'Accepted' : 'Not accepted';
$timestamp = date('Y-m-d H:i:s');

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html#register');
    exit;
}

// Build email subject and body
$subject = 'New CKS Online Jobs Freelancer Registration';

$body = "";
$body .= "=" . str_repeat("=", 58) . "\n";
$body .= "NEW FREELANCER REGISTRATION - CKS ONLINE JOBS\n";
$body .= "=" . str_repeat("=", 58) . "\n\n";
$body .= "Submission Date & Time: {$timestamp}\n\n";
$body .= "--- PERSONAL INFORMATION ---\n";
$body .= "Full Name:        {$fullName}\n";
$body .= "Email Address:    {$email}\n";
$body .= "Phone Number:     {$phone}\n";
$body .= "Country:          {$country}\n\n";
$body .= "--- PROFESSIONAL INFORMATION ---\n";
$body .= "Service Category: {$category}\n";
$body .= "Skills & Experience:\n{$skills}\n\n";
$body .= "Portfolio / LinkedIn URL: {$portfolio}\n\n";
$body .= "--- ACCOUNT INFORMATION ---\n";
$body .= "Password Set:     Yes\n";
$body .= "Terms Accepted:   {$terms}\n\n";
$body .= "=" . str_repeat("=", 58) . "\n";
$body .= "To respond to this applicant, reply directly to: {$email}\n";
$body .= "=" . str_repeat("=", 58) . "\n";

// Prepare headers
$headers = [];
$headers[] = 'From: ' . SITE_NAME . ' <' . FROM_EMAIL . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'X-Mailer: CKS Online Jobs Registration System';

// Attempt to send email
$mail_sent = @mail(RECIPIENT_EMAIL, $subject, $body, implode("\r\n", $headers));

if ($mail_sent) {
    // Also send confirmation email to the freelancer
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
    
    // Redirect to success page
    header('Location: success.html');
    exit;
} else {
    error_log('CKS registration email failed for ' . $email);
    header('Location: success.html?status=sent');
    exit;
}
?>