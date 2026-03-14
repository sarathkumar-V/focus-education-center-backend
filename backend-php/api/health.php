<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => 'The Focus Education Centre Backend API is running'
]);
