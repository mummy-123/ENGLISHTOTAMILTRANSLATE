<?php
// send_code.php
require 'config/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$purpose = $input['purpose'] ?? 'verify'; // 'verify' or 'reset'

if (!$email) {
    echo json_encode(['success'=>false,'message'=>'Email required']);
    exit;
}

$code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
$expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

try {
    // optional link to user id if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare("INSERT INTO email_codes (user_id,email,code,purpose,expires_at) VALUES (?,?,?,?,?)");
    $stmt->execute([$user['id'] ?? null, $email, $code, $purpose, $expires]);

    // In a real app: send email or SMS. For testing we just return code in response.
    echo json_encode(['success'=>true,'message'=>'Code generated','code'=>$code]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
