<?php require_once __DIR__ . '/../private/config/init.php'; ?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Logg Inn</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="form-container">
    <h2>Logg Inn</h2>
    <form method="post" action="login.php">
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" placeholder="E-post" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" placeholder="Passord">

        <input type="submit" name="login" value="Logg Inn">
    </form>

    <div class="register-link">
        Har du ikke en konto? <a href="register.php">Opprett en bruker</a>
    </div>
</div>

<div class="message-container">
    
<?php
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Sjekk mot databasen
        $existingUserQuery = $connection->prepare("SELECT * FROM users WHERE Email = ?");
        $existingUserQuery->bind_param("s", $email);
        $existingUserQuery->execute();
        $result = $existingUserQuery->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['Password'])) {
                // Passord er korrekt, opprett brukersesjon
                $_SESSION['loggedin'] = true;
                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['role'] = $user['Role'];
                // Omdiriger til brukerdashboard
                header('Location: dashboard.php');
                exit;
            } else {
                echo "<div class='error-messages'>Feil brukernavn eller passord.</div>";
            }
        } else {
            echo "<div class='error-messages'>Oppgi en gyldig e-postadresse.</div>";
        }
        $existingUserQuery->close();
    }
?>

</div>
</body>
</html>
