<?php
/**
 * CashSecond - Admin Authentication Guard
 * Fixed Password Protection: Update@786
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_PASSWORD', 'Update@786');

/**
 * Check if the admin is authenticated
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication or redirect to login
 */
function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
        header('Location: login.php?redirect=' . $redirect);
        exit;
    }
}
