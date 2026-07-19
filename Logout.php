<?php
// Start (or resume) the session so we can clear it
session_start();

// Wipe all session variables
$_SESSION = [];

// Destroy the session cookie itself, if one exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session data on the server
session_destroy();

// Send the user back to the landing page
header("Location: ./LawyerCaseManagement_Landing Page.html");
exit;
