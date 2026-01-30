<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['old_password']) || !isset($data['new_password'])) {
    sendJson(['error' => 'Missing fields'], 400);
}

$id = $data['id'];
$oldPass = $data['old_password'];
$newPass = $data['new_password'];

// Fetch current password
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    sendJson(['error' => 'User not found'], 404);
}

// Verify old password (assuming plain text storage for now as per previous context, 
// but prepared for switch to password_verify if hashing occurs later)
if ($oldPass !== $user['password']) {
    sendJson(['error' => 'Incorrect current password'], 401);
}

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("ss", $newPass, $id);

if ($stmt->execute()) {
    sendJson(['message' => 'Password updated successfully']);
} else {
    sendJson(['error' => 'Update failed: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
