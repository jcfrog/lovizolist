<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$db = getDb();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->query(
        'SELECT l.id, l.name, l.created_at,
                COUNT(i.id) AS total_count,
                SUM(CASE WHEN i.is_checked = 1 THEN 1 ELSE 0 END) AS checked_count
         FROM lists l
         LEFT JOIN items i ON i.list_id = l.id
         WHERE l.archived = 0
         GROUP BY l.id
         ORDER BY l.created_at ASC'
    );
    respond($stmt->fetchAll());
}

if ($method === 'POST') {
    $data = jsonBody();
    $name = trim((string) ($data['name'] ?? ''));

    if ($name === '' || mb_strlen($name) > 100) {
        respond(['error' => 'Nom de liste invalide'], 422);
    }

    $stmt = $db->prepare('INSERT INTO lists (name) VALUES (?)');
    $stmt->execute([$name]);

    respond(['id' => (int) $db->lastInsertId(), 'name' => $name], 201);
}

if ($method === 'DELETE') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        respond(['error' => 'Identifiant invalide'], 422);
    }

    $stmt = $db->prepare('DELETE FROM lists WHERE id = ?');
    $stmt->execute([$id]);

    respond(['success' => true]);
}

respond(['error' => 'Méthode non supportée'], 405);
