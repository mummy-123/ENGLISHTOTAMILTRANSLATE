<?php
// feedback.php
require 'config/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? null;
$name = $input['name'] ?? null;
$email = $input['email'] ?? null;
$rating = intval($input['rating'] ?? 0);
$message = $input['message'] ?? null;

$stmt = $pdo->prepare("INSERT INTO feedback (user_id,name,email,rating,message) VALUES (?,?,?,?,?)");
$stmt->execute([$user_id,$name,$email,$rating,$message]);
echo json_encode(['success'=>true,'message'=>'Thanks for feedback']);
