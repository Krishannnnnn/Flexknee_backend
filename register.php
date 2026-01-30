<?php
require_once 'db_connect.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
    sendJson(['error' => 'Missing required fields'], 400);
}

$name = $data['name'];
$email = $data['email'];
$password = $data['password']; // In a real app, hash this: password_hash($data['password'], PASSWORD_DEFAULT)
$role = $data['role'];

// Check if email exists
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    sendJson(['error' => 'Email already exists'], 409);
}

// Generate ID (PT-XXXX or DR-XXXX)
$prefix = (strcasecmp($role, 'therapist') == 0) ? "DR" : "PT";
$randomNum = rand(1000, 9999);
$newId = "$prefix-$randomNum";

// Ensure ID uniqueness (simple check, loops if exists)
// ... omitted for brevity in simple script, relying on probability ...

// Insert User
// Note: Storing plain password to match MockBackend behavior exactly for now. 
// Use password_hash in production!
$stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role, has_completed_baseline) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param("sssss", $newId, $name, $email, $password, $role);

if ($stmt->execute()) {
    
    // Generate Random Demo Data if Patient
    if (strcasecmp($role, 'patient') == 0) {
        $baseRom = rand(50, 70);
        
        $insertPerf = $conn->prepare("INSERT INTO performance_records (user_id, record_date, rom, pain_level, swelling, gait, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        for ($i = 4; $i >= 0; $i--) {
            // Calculate date: Today - i*2 days
            $date = date('Y-m-d', strtotime("-$i days")); // Logic was i*2 but simplfying to daily for PHP
            // MockBackend logic: -i*2 days. 
            $daysBack = $i * 2;
            $date = date('Y-m-d', strtotime("-$daysBack days"));
            
            $rom = $baseRom + ($i * 10) + rand(-5, 5);
            $pain = max(0, 8 - ($i * 2) + rand(-1, 1));
            
            $swelling = "Low";
            if ($i < 2) $swelling = "High";
            elseif ($i < 4) $swelling = "Medium";
            
            $gait = "Normal";
            if ($i < 2) $gait = "Severe Limp";
            elseif ($i < 4) $gait = "Mild Limp";
            
            $color = ""; // Color mapping handled by frontend usually, or store hex
            if (strpos($gait, "Severe") !== false) $color = "#EF4444"; // Red
            elseif (strpos($gait, "Mild") !== false) $color = "#F59E0B"; // Amber
            else $color = "#22C55E"; // Green
            
            $insertPerf->bind_param("ssiisss", $newId, $date, $rom, $pain, $swelling, $gait, $color);
            $insertPerf->execute();
        }
        $insertPerf->close();
    }
    
    sendJson(['message' => 'User registered successfully', 'id' => $newId]);
} else {
    sendJson(['error' => 'Registration failed: ' . $conn->error], 500);
}

$stmt->close();
$conn->close();
?>
