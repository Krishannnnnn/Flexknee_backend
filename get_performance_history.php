<?php
require_once 'db_connect.php';

if (!isset($_GET['user_id'])) {
    sendJson(['error' => 'Missing user_id'], 400);
}

$userId = $_GET['user_id'];

$stmt = $conn->prepare("SELECT * FROM performance_records WHERE user_id = ? ORDER BY record_date ASC");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    // Format date to "MMM dd" for App compatibility
    $row['formatted_date'] = date("M d", strtotime($row['record_date'])); 
    $history[] = $row;
}

sendJson(['history' => $history]);

$stmt->close();
$conn->close();
?>
