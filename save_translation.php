<?php
// save_translation.php
require 'config/db.php';
$input = json_decode(file_get_contents('php://input'), true);

// For dev, client should pass user_id (or token mapping)
$user_id = $input['user_id'] ?? null;
$source_lang = $input['source_lang'] ?? 'en';
$target_lang = $input['target_lang'] ?? 'ta';
$source_text = $input['source_text'] ?? '';
$translated_text = $input['translated_text'] ?? '';
$type = $input['translation_type'] ?? 'text';

if (!$source_text || !$translated_text) {
    echo json_encode(['success'=>false,'message'=>'source and translated text required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO translations (user_id, source_lang, target_lang, source_text, translated_text, translation_type) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$user_id, $source_lang, $target_lang, $source_text, $translated_text, $type]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success'=>true,'message'=>'Saved','translation_id'=>$id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
