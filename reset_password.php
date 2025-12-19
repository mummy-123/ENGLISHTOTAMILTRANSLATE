<?php
// reset_password.php
require 'config/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$code = trim($input['code'] ?? '');
$new_password = $input['new_password'] ?? '';

if (!$email || !$code || !$new_password) {
    echo json_encode(['success'=>false,'message'=>'Missing fields']);
    exit;
}

// verify code (reuse logic)
$stmt = $pdo->prepare("SELECT * FROM email_codes WHERE email=? AND code=? AND purpose='reset' AND used=0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
$stmt->execute([$email,$code]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['success'=>false,'message'=>'Invalid or expired code']);
    exit;
}

try {
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password_hash=? WHERE email=?")->execute([$hash,$email]);
    $pdo->prepare("UPDATE email_codes SET used=1 WHERE id=?")->execute([$row['id']]);
    echo json_encode(['success'=>true,'message'=>'Password reset successful']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
