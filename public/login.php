<?php 
require_once __DIR__ . '/../private/config/init.php';

$errorMsg = array();

// Viser feilmelding for et spesifikt felt
function displayErrorMessage($errors, $fieldName) {
    if (isset($errors[$fieldName])) {
        echo "<div class='error-messages'>" . htmlspecialchars($errors[$fieldName]) . "</div>";
    }
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validering
    if (empty($email)) {
        $errorMsg['email'] = 'E-post må oppgis.';
    }

    if (empty($password)) {
        $errorMsg['password'] = 'Passord må oppgis.';
    }

    if (empty($errorMsg)) {
        $existingUserQuery = $connection->prepare("SELECT * FROM users WHERE Email = ?");
        $existingUserQuery->bind_param("s", $email);
        $existingUserQuery->execute();
        $result = $existingUserQuery->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['Password'])) {
                // Passord er korrekt, opprett brukersesjon
                $_SESSION['loggedin'] = true;
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['Role'] = $user['Role'];
                header('Location: views/profile.php');
                exit;
            } else {
                $errorMsg['login'] = 'Feil brukernavn eller passord.';
            }
        } else {
            $errorMsg['login'] = 'Feil brukernavn eller passord.';
        }
        $existingUserQuery->close();
    }
}

// Sjekker om en "logout" GET-parameter er satt og viser en melding om at brukeren er logget ut
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    echo '<div class="logout-message">Du har blitt logget ut.</div>';
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Logg Inn</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="form-loginandregister">
    <h2>Logg Inn</h2>

    <form method="post" action="login.php">
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" placeholder="E-post" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <?php displayErrorMessage($errorMsg, 'email'); ?>

        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" placeholder="Passord">
        <?php displayErrorMessage($errorMsg, 'password'); ?>

        <div class="error-message-placeholder">
        <?php displayErrorMessage($errorMsg, 'login'); ?>
        </div>

        <input type="submit" name="login" value="Logg Inn">

        <div class="register-link">
        Har du ikke en konto? <a href="register.php">Opprett en bruker</a>
        </div>
    </form>
</div>
</body>
</html>
