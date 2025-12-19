<?php
require 'config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['full_name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(["success"=>false,"message"=>"All fields required"]);
    exit;
}

/* Check if already registered */
$check = $pdo->prepare("SELECT id FROM users WHERE email=?");
$check->execute([$email]);
if ($check->fetch()) {
    echo json_encode(["success"=>false,"message"=>"Email already registered"]);
    exit;
}

/* Generate OTP */
$otp = rand(100000,999999);
$expiry = date("Y-m-d H:i:s", time() + 600); // 10 minutes
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

/* Remove old OTPs */
$pdo->prepare("DELETE FROM email_codes WHERE email=?")->execute([$email]);

/* Store temp data */
$stmt = $pdo->prepare("
INSERT INTO email_codes (email, code, purpose, expires_at, temp_name, temp_password)
VALUES (?,?,?,?,?,?)
");
$stmt->execute([$email,$otp,'signup',$expiry,$name,$hashPassword]);

/* Send Email */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nakkalakeerthana143@gmail.com';
    $mail->Password = 'vciotdxdevgyheml';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('nakkalakeerthana143@gmail.com', 'Tamil Translator');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Email Verification OTP';
    $mail->Body = "<h2>Your OTP: $otp</h2><p>Valid for 10 minutes</p>";

    $mail->send();

    echo json_encode(["success"=>true,"message"=>"OTP sent to email"]);

} catch (Exception $e) {
    echo json_encode(["success"=>false,"message"=>"Email sending failed"]);
}