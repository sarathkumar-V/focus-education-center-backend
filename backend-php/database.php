<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$dbPath = __DIR__ . '/focus.db';
$db = new SQLite3($dbPath);

$db->exec("
    CREATE TABLE IF NOT EXISTS enquiries (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        studentName TEXT NOT NULL,
        studentClass TEXT,
        board TEXT,
        parentPhone NUMERIC(10) NOT NULL,
        message TEXT,
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

$db->exec("
    CREATE TABLE IF NOT EXISTS blogs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        imageUrl TEXT,
        author TEXT DEFAULT 'Admin',
        date DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

return $db;
