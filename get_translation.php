<?php
// get_translation.php?id=123
require 'config/db.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false,'message'=>'id required']); exit; }
$stmt = $pdo->prepare("SELECT * FROM translations WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { echo json_encode(['success'=>false,'message'=>'Not found']); exit; }
echo json_encode(['success'=>true,'data'=>$row]);
