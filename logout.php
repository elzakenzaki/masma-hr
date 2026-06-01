<?php
session_start();
require_once 'config/supabase.php';

// Post logout signal up to Supabase cluster if valid token exists
if (isset($_SESSION['access_token'])) {
    supabase_api_request('/auth/v1/logout', [], 'POST');
}

// Clear all variables in local session storage array
$_SESSION = array();

// Thoroughly wipe cookies matching standard server profiles
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: index.php");
exit;