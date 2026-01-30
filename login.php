<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    sendJson(['error' => 'Missing credentials'], 400);
}

$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Validate password (plain text match for now per MockBackend)
    if ($password === $user['password']) {
        unset($user['password']); // Don't send password back
        
        // Convert TinyInt to Boolean for JSON
        $user['has_completed_baseline'] = (bool)$user['has_completed_baseline'];
        
        sendJson(['message' => 'Login successful', 'user' => $user]);
    } else {
        sendJson(['error' => 'Invalid password'], 401);
    }
} else {
    sendJson(['error' => 'User not found'], 404);
}

$stmt->close();
$conn->close();
?>
