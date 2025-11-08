<?php
// webhook.php
$secret = '<your-secret>'; // long random string

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $signature)) {
  http_response_code(403);
  exit('Invalid signature');
}

// Run deploy script.
shell_exec('/home/username/public_html/repo/deploy_from_github.sh > /dev/null 2>&1 &');
http_response_code(200);
echo 'Deployment triggered';