<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = trim($_POST['pin'] ?? '');
    $name = trim($_POST['name'] ?? '');

    if ($pin === '' || $name === '') {
        $error = 'Merci de remplir tous les champs.';
    } elseif (!hash_equals(FAMILY_PIN, $pin)) {
        $error = 'Code incorrect.';
    } elseif (mb_strlen($name) > 50) {
        $error = 'Prénom trop long.';
    } else {
        login($name);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Connexion - Liste de courses</title>
<link rel="icon" type="image/png" href="assets/img/apple-touch-icon.png">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Courses">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <main class="login-card">
        <h1>🛒 Liste de courses</h1>
        <p class="subtitle">Accès famille</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <label for="name">Ton prénom</label>
            <input type="text" id="name" name="name" required maxlength="50" autofocus
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

            <label for="pin">Code famille</label>
            <input type="password" id="pin" name="pin" required inputmode="numeric" maxlength="20">

            <button type="submit">Entrer</button>
        </form>
    </main>
</body>
</html>
