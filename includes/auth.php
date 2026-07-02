<?php

declare(strict_types=1);

function isLoggedIn(): bool
{
    return !empty($_SESSION['family_member']);
}

function currentMember(): ?string
{
    return $_SESSION['family_member'] ?? null;
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function login(string $name): void
{
    $_SESSION['family_member'] = $name;
}

function logout(): void
{
    $_SESSION = [];
    session_destroy();
}
