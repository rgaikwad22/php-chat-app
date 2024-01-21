<?php
session_start();

// Clear session variables
$_SESSION = array();

// Expire the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Expire the security token cookie
setcookie('security_token', '', time() - 42000, '/');

// Redirect to the login page
header('Location: ../index.php');
exit();
?>