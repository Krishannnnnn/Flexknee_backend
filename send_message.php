<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['text'])) {
    sendJson(['error' => 'Missing fields'], 400);
}

$sender = $data['sender_id'];
$receiver = $data['receiver_id'];
$text = $data['text'];

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $sender, $receiver, $text);

if ($stmt->execute()) {
    sendJson(['message' => 'Message sent', 'id' => $stmt->insert_id]);
} else {
    sendJson(['error' => 'Failed: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
