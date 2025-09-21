<?php
require __DIR__ . '/../conn.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false]);
    exit;
}
echo json_encode($_SESSION['user']);
