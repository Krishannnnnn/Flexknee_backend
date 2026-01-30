<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    sendJson(['error' => 'Missing ID'], 400);
}

$id = $data['id'];

$stmt = $conn->prepare("UPDATE users SET has_completed_baseline = 1 WHERE id = ?");
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    sendJson(['message' => 'Baseline marked as completed']);
} else {
    sendJson(['error' => 'Update failed: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
