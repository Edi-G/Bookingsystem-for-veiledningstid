<?php
// Start sesjon 
session_start();

// Databasekonfigurasjonsparametre
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'veiledning_system');

// Autoloader for klasser
spl_autoload_register(function ($className) {
    // Sti for å gå opp til "private" mappen og deretter inn i "classes"
    require_once __DIR__ . '/../classes/' . $className . '.php';
});

// Opprette databaseforbindelse
$dbInstance = new Database();
$connection = $dbInstance->connect();

// Opprette Booking-instans
$bookingInstance = new Booking($connection);

// Oppretter User-instans
$userInstance = new User($connection);

// Oppretter Course-instans 
$courseInstance = new Course($connection);

// Oppretter Message-instans
$messageInstance = new Message($connection);

// Sesjonskontrollfunksjoner
function checkLoggedIn() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        redirect('../login.php');
    }
}

function checkRole($role) {
    if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== $role) {
        // Håndterer uautorisert tilgang
        redirect('/public/login.php'); 
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
        unset($_SESSION['flash_message']); // Fjerner meldingen etter visning
    }
}

?>
