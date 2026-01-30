<?php
require_once 'db_connect.php';

if (!isset($_GET['user1']) || !isset($_GET['user2'])) {
    sendJson(['error' => 'Missing params'], 400);
}

$u1 = $_GET['user1'];
$u2 = $_GET['user2'];

// Fetch conversation between two users (both directions)
$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $u1, $u2, $u2, $u1);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

sendJson(['messages' => $messages]);

$stmt->close();
$conn->close();
?>
