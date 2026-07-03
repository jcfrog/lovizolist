<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Liste de courses</title>
<link rel="icon" type="image/png" href="assets/img/apple-touch-icon.png">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Courses">
<link rel="stylesheet" href="assets/css/style.css">
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body x-data="app()" x-init="init()">

<header class="topbar">
    <h1>🛒 Courses</h1>
    <div class="member">
        <span><?= htmlspecialchars(currentMember()) ?></span>
        <a href="logout.php" class="logout">Quitter</a>
    </div>
</header>

<main class="container">

    <!-- Vue: liste des listes -->
    <section x-show="!currentList" x-cloak>
        <form class="add-row" @submit.prevent="createList()">
            <input type="text" x-model="newListName" placeholder="Nouvelle liste (ex: Carrefour)" maxlength="100">
            <button type="submit" :disabled="!newListName.trim()">Créer</button>
        </form>

        <ul class="lists">
            <template x-for="list in lists" :key="list.id">
                <li class="list-card" @click="openList(list)">
                    <div class="list-info">
                        <span class="list-name" x-text="list.name"></span>
                        <span class="list-progress" x-text="list.checked_count + ' / ' + list.total_count"></span>
                    </div>
                    <button class="icon-btn danger" @click.stop="deleteList(list)" aria-label="Supprimer la liste">✕</button>
                </li>
            </template>
        </ul>

        <p class="empty" x-show="lists.length === 0" x-cloak>Aucune liste pour l'instant. Créez-en une !</p>
    </section>

    <!-- Vue: contenu d'une liste -->
    <section x-show="currentList" x-cloak>
        <div class="list-header">
            <button class="back-btn" @click="closeList()">← Listes</button>
            <h2 x-text="currentList?.name"></h2>
        </div>

        <form class="add-row" @submit.prevent="addItem()">
            <input type="text" x-model="newItemName" placeholder="Ajouter un article..." maxlength="150" x-ref="itemInput">
            <button type="submit" :disabled="!newItemName.trim()">Ajouter</button>
        </form>

        <ul class="items">
            <template x-for="item in sortedItems()" :key="item.id">
                <li class="item-row" :class="{ checked: item.is_checked }">
                    <label class="item-label">
                        <input type="checkbox" :checked="item.is_checked" @change="toggleItem(item)">
                        <span x-text="item.name"></span>
                    </label>
                    <button class="icon-btn danger" @click="deleteItem(item)" aria-label="Supprimer l'article">✕</button>
                </li>
            </template>
        </ul>

        <p class="empty" x-show="items.length === 0" x-cloak>Liste vide. Ajoutez votre premier article !</p>

        <button class="clear-btn" x-show="hasCheckedItems()" x-cloak @click="clearChecked()">
            Nettoyer les articles cochés
        </button>
    </section>

</main>

<script>
    window.APP_CONFIG = { pollIntervalMs: <?= (int) POLL_INTERVAL_MS ?> };
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
