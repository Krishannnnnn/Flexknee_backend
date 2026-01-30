<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['rom'])) {
    sendJson(['error' => 'Missing fields'], 400);
}

$userId = $data['user_id'];
$date = $data['date'] ?? date('Y-m-d'); // Default today
$rom = $data['rom'];
$pain = $data['pain_level'] ?? 0;
$swelling = $data['swelling'] ?? "Low";
$gait = $data['gait'] ?? "Normal";
$color = $data['color'] ?? "#22C55E";

$stmt = $conn->prepare("INSERT INTO performance_records (user_id, record_date, rom, pain_level, swelling, gait, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiisss", $userId, $date, $rom, $pain, $swelling, $gait, $color);

if ($stmt->execute()) {
    sendJson(['message' => 'Record added successfully', 'id' => $stmt->insert_id]);
} else {
    sendJson(['error' => 'Failed to add record: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
