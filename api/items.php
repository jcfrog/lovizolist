<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$db = getDb();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $listId = (int) ($_GET['list_id'] ?? 0);
    if ($listId <= 0) {
        respond(['error' => 'list_id invalide'], 422);
    }

    $stmt = $db->prepare(
        'SELECT id, list_id, name, is_checked, added_by, checked_by, created_at, checked_at
         FROM items
         WHERE list_id = ?
         ORDER BY is_checked ASC, created_at ASC'
    );
    $stmt->execute([$listId]);
    $items = $stmt->fetchAll();
    foreach ($items as &$item) {
        $item['is_checked'] = (int) $item['is_checked'];
    }
    respond($items);
}

if ($method === 'POST') {
    $data = jsonBody();
    $listId = (int) ($data['list_id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));

    if ($listId <= 0 || $name === '' || mb_strlen($name) > 150) {
        respond(['error' => 'Données invalides'], 422);
    }

    $stmt = $db->prepare('INSERT INTO items (list_id, name, added_by) VALUES (?, ?, ?)');
    $stmt->execute([$listId, $name, currentMember()]);

    respond([
        'id' => (int) $db->lastInsertId(),
        'list_id' => $listId,
        'name' => $name,
        'is_checked' => 0,
        'added_by' => currentMember(),
        'checked_by' => null,
    ], 201);
}

if ($method === 'PATCH') {
    $data = jsonBody();
    $id = (int) ($data['id'] ?? 0);
    $isChecked = !empty($data['is_checked']);

    if ($id <= 0) {
        respond(['error' => 'Identifiant invalide'], 422);
    }

    if ($isChecked) {
        $stmt = $db->prepare('UPDATE items SET is_checked = 1, checked_by = ?, checked_at = NOW() WHERE id = ?');
        $stmt->execute([currentMember(), $id]);
    } else {
        $stmt = $db->prepare('UPDATE items SET is_checked = 0, checked_by = NULL, checked_at = NULL WHERE id = ?');
        $stmt->execute([$id]);
    }

    respond(['success' => true]);
}

if ($method === 'DELETE') {
    $listId = (int) ($_GET['clear_checked_for_list'] ?? 0);
    if ($listId > 0) {
        $stmt = $db->prepare('DELETE FROM items WHERE list_id = ? AND is_checked = 1');
        $stmt->execute([$listId]);
        respond(['success' => true]);
    }

    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        respond(['error' => 'Identifiant invalide'], 422);
    }

    $stmt = $db->prepare('DELETE FROM items WHERE id = ?');
    $stmt->execute([$id]);

    respond(['success' => true]);
}

respond(['error' => 'Méthode non supportée'], 405);
