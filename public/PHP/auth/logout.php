<?php
require __DIR__ . '/../conn.php';

$_SESSION = [];
session_destroy();

echo json_encode(['ok'=>true]);