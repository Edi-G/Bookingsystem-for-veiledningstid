<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Registrering av bruker</title>
    <link rel="stylesheet" href="assets/css/registerAndLogin.css">
</head>
<body>

<div class="form-container">
    <h2>Registrer ny bruker</h2>
    <form method="post" action="register.php">
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" placeholder="E-post" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" placeholder="Passord" required>

        <label for="fullname">Fullt Navn:</label>
        <input type="text" id="fullname" name="fullname" placeholder="Fullt Navn" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>

        <label for="role">Rolle:</label>
        <select id="role" name="role">
            <option value="student">Student</option>
            <option value="hjelpelærer">Hjelpelærer</option>
        </select>

        <input type="submit" name="registrer" value="Registrer">
    </form>
</div>

<div class="message-container">

<?php
require_once __DIR__ . '/../private/config/init.php';

$errorMsg = array();
$successMsg = '';

if (isset($_POST['registrer'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $role = $_POST['role'];

    // Validering
    if (empty($email)) {
        $errorMsg[] = 'E-post må oppgis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg[] = 'Ugyldig e-postadresse.';
    }

    if (empty($password)) {
        $errorMsg[] = 'Passord må oppgis.';
    } elseif (strlen($password) < 8) {
        $errorMsg[] = 'Passordet må være minst 8 tegn.';
    }

    if (empty($fullname)) {
        $errorMsg[] = 'Fullt navn må oppgis.';
    }

    // Valider at rollen er korrekt
    if ($role !== 'student' && $role !== 'hjelpelærer') {
        $errorMsg[] = 'Ugyldig rolle valgt.';
    }

    // Sjekk om e-posten allerede er registrert
    if (!$errorMsg) {
        $existingUserQuery = $connection->prepare("SELECT * FROM users WHERE Email = ?");
        $existingUserQuery->bind_param("s", $email);
        $existingUserQuery->execute();
        if ($existingUserQuery->get_result()->num_rows > 0) {
            $errorMsg[] = 'En bruker med denne e-posten eksisterer allerede.';
        }
        $existingUserQuery->close();
    }

    // Hvis ingen feil er funnet, lagre data og vis suksessmelding
    if (!$errorMsg) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $insertUserQuery = $connection->prepare("INSERT INTO users (Email, Password, FullName, Role) VALUES (?, ?, ?, ?)");
        $insertUserQuery->bind_param("ssss", $email, $passwordHash, $fullname, $role);
        if ($insertUserQuery->execute()) {
            $successMsg = "Ny bruker er registrert.";
        } else {
            $errorMsg[] = "Det oppstod en feil under registrering.";
        }
        $insertUserQuery->close();
    }
}

// Vis feilmeldinger hvis det finnes
if (!empty($errorMsg)) {
    echo "Vennligst rett følgende feil:<br>";
    foreach ($errorMsg as $error) {
        echo htmlspecialchars($error) . "<br>";
    }
}

// Vis suksessmelding hvis brukeren er registrert
if ($successMsg !== '') {
    echo htmlspecialchars($successMsg);
}
?>

</div>
</body>
</html>
