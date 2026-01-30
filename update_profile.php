<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['name'])) {
    sendJson(['error' => 'Missing required fields'], 400);
}

$id = $data['id'];
$name = $data['name'];
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';

$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("ssss", $name, $email, $phone, $id);

if ($stmt->execute()) {
    sendJson(['message' => 'Profile updated successfully']);
} else {
    sendJson(['error' => 'Update failed: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
