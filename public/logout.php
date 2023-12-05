<?php
require_once __DIR__ . '/../private/config/init.php';

// Slett alle brukersesjonsvariabler
$_SESSION = array();

// Hvis sesjonsinformasjonen lagres i informasjonskapsler, slett informasjonskapselen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Slett selve sesjonen
session_destroy();

// Omdiriger tilbake til login-siden med en GET-parameter for å vise en melding
header("Location: login.php?logout=1");
exit;
?>