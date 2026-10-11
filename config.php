<?php
// CKS Online Jobs Email Configuration
define('RECIPIENT_EMAIL', 'sigeitattoo12@gmail.com');
define('FROM_EMAIL', 'noreply@cksonlinejobs.com');
define('SITE_NAME', 'CKS Online Jobs');
define('SITE_URL', 'https://' . $_SERVER['HTTP_HOST']);

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');
?>