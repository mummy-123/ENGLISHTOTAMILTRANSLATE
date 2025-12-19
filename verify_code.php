<?php
require 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = strtolower(trim($data['email'] ?? ''));
$otp   = trim($data['otp'] ?? '');

$stmt = $pdo->prepare("
    SELECT * FROM email_codes
    WHERE email = ?
      AND code = ?
      AND purpose = 'signup'
      AND used = 0
      AND expires_at > NOW()
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$email, $otp]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or expired OTP"
    ]);
    exit;
}

/* Save user */
$insert = $pdo->prepare("
    INSERT INTO users (full_name, email, password_hash, is_verified)
    VALUES (?, ?, ?, 1)
");
$insert->execute([
    $row['temp_name'],
    $email,
    $row['temp_password']
]);

/* Mark OTP used */
$pdo->prepare("UPDATE email_codes SET used = 1 WHERE id = ?")
    ->execute([$row['id']]);

echo json_encode([
    "success" => true,
    "message" => "Email verified successfully"
]);