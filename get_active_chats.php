<?php
require_once 'db_connect.php';

if (!isset($_GET['therapist_id'])) {
    sendJson(['error' => 'Missing therapist_id'], 400);
}

$tid = $_GET['therapist_id'];

// Find all unique users who have exchanged messages with this therapist
// Get latest message for each user to show preview
$sql = "
    SELECT 
        u.id, 
        u.name, 
        u.role,
        (SELECT message_text FROM messages m2 
         WHERE (m2.sender_id = u.id AND m2.receiver_id = ?) 
            OR (m2.sender_id = ? AND m2.receiver_id = u.id)
         ORDER BY m2.created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages m3 
         WHERE (m3.sender_id = u.id AND m3.receiver_id = ?) 
            OR (m3.sender_id = ? AND m3.receiver_id = u.id)
         ORDER BY m3.created_at DESC LIMIT 1) as last_timestamp
    FROM users u
    WHERE u.id IN (
        SELECT DISTINCT sender_id FROM messages WHERE receiver_id = ?
        UNION
        SELECT DISTINCT receiver_id FROM messages WHERE sender_id = ?
    )
    ORDER BY last_timestamp DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $tid, $tid, $tid, $tid, $tid, $tid);
$stmt->execute();
$result = $stmt->get_result();

$chats = [];
while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

sendJson(['chats' => $chats]);

$stmt->close();
$conn->close();
?>
