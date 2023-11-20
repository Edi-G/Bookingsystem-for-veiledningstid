<?php
// Start session management
session_start();

// Databasekonfigurasjonsparametre
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'veiledning_system');

// Autoloader for klasser
spl_autoload_register(function ($class_name) {
    // Korrigerer stien for å gå opp til "private" mappen og deretter inn i "classes"
    require_once __DIR__ . '/../classes/' . $class_name . '.php';
});


// Opprette en databaseforbindelse
$dbInstance = new Database();
$connection = $dbInstance->connect();

// Sesjonskontrollfunksjoner
function checkLoggedIn() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        redirect('/public/login.php');
    }
}

function checkRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // Håndterer uautorisert tilgang
        redirect('/public/error.php'); // Må endres til feilsiden
    }
}

// Hjelpefunksjoner
function redirect($url) {
    header("Location: $url");
    exit;
}

// Feilmeldingsfunksjon
function setFlashMessage($message) {
    $_SESSION['flash_message'] = $message;
}

function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        echo $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Fjern meldingen etter visning
    }
}

?>
