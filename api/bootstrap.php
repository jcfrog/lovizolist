<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

function jsonBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function respond($data, int $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}
