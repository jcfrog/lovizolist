<?php

declare(strict_types=1);

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'lovizolist');
define('DB_USER', 'lovizolist_app');
define('DB_PASS', 'changez-moi');

// Code PIN partagé pour accéder au site (à changer !)
define('FAMILY_PIN', '1234');

// Intervalle de rafraîchissement automatique (ms) pour voir les mises à jour des autres membres
define('POLL_INTERVAL_MS', 4000);

session_start();
