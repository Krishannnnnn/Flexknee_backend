CREATE DATABASE IF NOT EXISTS flexknee_ai;
USE flexknee_ai;

CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(20) PRIMARY KEY, -- "PT-XXXX" or "DR-XXXX"
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL, -- "patient" or "therapist"
    phone VARCHAR(20),
    has_completed_baseline TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS performance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    record_date DATE NOT NULL,
    rom INT NOT NULL,
    pain_level INT NOT NULL, -- Key column often missed
    swelling VARCHAR(20), -- "Low", "Medium", "High"
    gait VARCHAR(50), -- "Normal", "Mild Limp", "Severe Limp"
    color VARCHAR(20), -- Hex code or name (e.g., "Green", "#4CAF50")
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id VARCHAR(20) NOT NULL,
    receiver_id VARCHAR(20) NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);
