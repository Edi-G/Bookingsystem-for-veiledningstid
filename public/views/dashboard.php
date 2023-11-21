<?php 
require_once __DIR__ . '/../../private/config/init.php';

// Sjekk om brukeren er logget inn og hent brukerinformasjon
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Hent brukerinformasjon fra sesjonen
$userId = $_SESSION['userid'];
$userRole = $_SESSION['role'];

// Inkluder nødvendige filer
require_once __DIR__ . '/../../private/templates/header.php';
require_once __DIR__ . '/../../private/templates/navbar.php';

// Hent data fra databasen basert på brukerrolle
// ...

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/main.css"> 
</head>
<body>

<?php echo $navbar; // Her vises navbar fra inkludert fil ?>

<div class="dashboard-container">
    <h1>Velkommen til Dashboardet, <?php echo htmlspecialchars($userFullName); ?></h1>
    
    <?php if ($userRole === 'student'): ?>
        <!-- Dashboard-innhold for studenter -->
        <div>
            <h2>Dine kommende bookinger</h2>
            <!-- Logikk for å hente og vise studentens bookinger -->
        </div>
        <div>
            <h2>Book en veiledningstime</h2>
            <!-- Logikk for å vise skjema for booking -->
        </div>
    <?php elseif ($userRole === 'hjelpelærer'): ?>
        <!-- Dashboard-innhold for hjelpelærere -->
        <div>
            <h2>Din tilgjengelighet</h2>
            <!-- Logikk for å vise og endre hjelpelærerens tilgjengelighet -->
        </div>
        <div>
            <h2>Bookede veiledningstimer</h2>
            <!-- Logikk for å vise bookinger som studenter har gjort med hjelpelæreren -->
        </div>
    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/../../private/templates/footer.php';
?>

</body>
</html>
