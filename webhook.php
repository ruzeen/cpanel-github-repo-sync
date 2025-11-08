<?php
// webhook.php
$secret = '9a3f6c4e1d8b2e7d90a71c5f8b19e3a2x4f6d8c7e1f9a3b0c2d7e6a5f4b1c9d8'; // long random string

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $signature)) {
  http_response_code(403);
  exit('Invalid signature');
}

// Run deploy script
shell_exec('/home/ruzeen/public_html/cpanel-github-repo-sync/deploy_from_github.sh > /dev/null 2>&1 &');
http_response_code(200);
echo 'Deployment triggered';