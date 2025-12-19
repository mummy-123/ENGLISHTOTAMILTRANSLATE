<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';

$user_id = $_GET['user_id'] ?? null;
$limit = intval($_GET['limit'] ?? 20);

try {
    if ($user_id) {

        // IMPORTANT FIX: LIMIT cannot be parameterized in some MySQL engines
        $sql = "SELECT * FROM translations WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

    } else {

        $sql = "SELECT * FROM translations ORDER BY created_at DESC LIMIT $limit";
        $stmt = $pdo->query($sql);
    }

    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true, 'data'=>$rows]);

} catch (Exception $e) {

    // 👇 IMPORTANT: SHOW REAL ERROR
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()     // full error instead of “Server error”
    ]);
}
