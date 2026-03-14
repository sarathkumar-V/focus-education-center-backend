<?php
$db = require_once __DIR__ . '/../database.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $results = $db->query("SELECT * FROM blogs ORDER BY date DESC");
    $data = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
} elseif ($method === 'POST') {
    $input = file_get_contents('php://input');
    $payload = json_decode($input, true);

    $title = $payload['title'] ?? null;
    $content = $payload['content'] ?? null;
    $imageUrl = $payload['imageUrl'] ?? null;
    $author = $payload['author'] ?? 'Admin';

    if (!$title || !$content) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content are required']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO blogs (title, content, imageUrl, author) VALUES (:title, :content, :imageUrl, :author)');
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
    $stmt->bindValue(':imageUrl', $imageUrl, SQLITE3_TEXT);
    $stmt->bindValue(':author', $author, SQLITE3_TEXT);

    $result = $stmt->execute();

    if ($result) {
        http_response_code(201);
        echo json_encode(['id' => $db->lastInsertRowID(), 'message' => 'Blog created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    exit;
} elseif ($method === 'PUT') {
    $input = file_get_contents('php://input');
    $payload = json_decode($input, true);

    $id = $payload['id'] ?? null;
    $title = $payload['title'] ?? null;
    $content = $payload['content'] ?? null;
    $imageUrl = $payload['imageUrl'] ?? null;

    if (!$id || !$title || !$content) {
        http_response_code(400);
        echo json_encode(['error' => 'ID, title and content are required']);
        exit;
    }

    $stmt = $db->prepare('UPDATE blogs SET title = :title, content = :content, imageUrl = :imageUrl WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
    $stmt->bindValue(':imageUrl', $imageUrl, SQLITE3_TEXT);

    $result = $stmt->execute();

    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'Blog updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    exit;
} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        exit;
    }

    $stmt = $db->prepare('DELETE FROM blogs WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    if ($result) {
        http_response_code(200);
        echo json_encode(['message' => 'Blog deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}
