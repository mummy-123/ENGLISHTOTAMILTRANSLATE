<?php
require 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(["success"=>false,"message"=>"Invalid credentials"]);
    exit;
}

if ($user['is_verified'] != 1) {
    echo json_encode(["success"=>false,"message"=>"Email not verified"]);
    exit;
}

echo json_encode([
    "success"=>true,
    "message"=>"Login successful",
    "user"=>[
        "id"=>$user['id'],
        "name"=>$user['full_name'],
        "email"=>$user['email']
    ]
]);