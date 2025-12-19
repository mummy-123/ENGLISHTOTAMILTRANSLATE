<?php
// upload_document.php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Use POST']);
    exit;
}

// Accept file input named 'file' and optional user_id
$user_id = $_POST['user_id'] ?? null;

if (!isset($_FILES['file'])) {
    echo json_encode(['success'=>false,'message'=>'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$allowed = ['pdf','doc','docx','txt'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    echo json_encode(['success'=>false,'message'=>'Invalid file type']);
    exit;
}

$target_folder = __DIR__ . '/uploads/';
if (!is_dir($target_folder)) mkdir($target_folder, 0777, true);

$unique = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest = $target_folder . $unique;

if (move_uploaded_file($file['tmp_name'], $dest)) {
    $stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, filepath, original_name, size_bytes) VALUES (?,?,?,?,?)");
    $stmt->execute([$user_id, $unique, 'uploads/'.$unique, $file['name'], $file['size']]);
    echo json_encode(['success'=>true,'message'=>'Uploaded','file'=>['filename'=>$unique,'path'=>'uploads/'.$unique]]);
} else {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Upload failed']);
}
