<?php
$db = require_once __DIR__ . '/../database.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $results = $db->query("SELECT * FROM enquiries ORDER BY createdAt DESC");
    $data = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
} elseif ($method === 'POST') {
    $input = file_get_contents('php://input');
    $payload = json_decode($input, true);

    $studentName = $payload['studentName'] ?? null;
    $studentClass = $payload['studentClass'] ?? null;
    $board = $payload['board'] ?? null;
    $parentPhone = $payload['parentPhone'] ?? null;
    $message = $payload['message'] ?? null;

    if (!$studentName || !$parentPhone) {
        http_response_code(400);
        echo json_encode(['error' => 'Student Name and Parent Phone are required']);
        exit;
    }

    $cleanedPhone = str_replace('+91 ', '', $parentPhone);
    if (!preg_match('/^[6-9]\d{9}$/', $cleanedPhone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid Indian mobile number format']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO enquiries (studentName, studentClass, board, parentPhone, message) VALUES (:studentName, :studentClass, :board, :parentPhone, :message)');
    $stmt->bindValue(':studentName', $studentName, SQLITE3_TEXT);
    $stmt->bindValue(':studentClass', $studentClass, SQLITE3_TEXT);
    $stmt->bindValue(':board', $board, SQLITE3_TEXT);
    $stmt->bindValue(':parentPhone', $parentPhone, SQLITE3_TEXT);
    $stmt->bindValue(':message', $message, SQLITE3_TEXT);
    
    $result = $stmt->execute();

    if ($result) {
        http_response_code(201);
        echo json_encode(['id' => $db->lastInsertRowID(), 'message' => 'Enquiry submitted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}
