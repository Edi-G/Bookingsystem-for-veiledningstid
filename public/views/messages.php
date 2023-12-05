<?php
require_once("../../private/config/init.php");

// Innloggings sjekk, omdirigerer hvis ikke logget inn
checkLoggedIn();

// Hent meldinger for den innloggede brukeren
$userId = $_SESSION['UserID'];
//$userMessages = $messageInstance->getMessagesByUserId($userId);

// Skjemabehandling for å sende meldinger
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiverId'], $_POST['messageContent'])) {
        $senderId = $_SESSION['UserID'];
        $receiverId = $_POST['receiverId'];
        $messageContent = $_POST['messageContent'];

        $sendMessageResult = $messageInstance->sendMessage($senderId, $receiverId, $messageContent);

        // Håndtere resultatet av å sende meldingen
        if ($sendMessageResult) {
            echo "Meldingen ble sendt vellykket.";
        } else {
            echo "Det oppstod en feil ved sending av meldingen.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Meldinger</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="form-container">
    <h2>Meldinger</h2>
    <div class="message-list">
        <!-- Vis alle meldinger for brukeren -->
        <ul>
            <?php foreach ($userMessages as $message): ?>
                <li>
                    <strong>Fra:</strong> <?= $message['SenderID'] ?><br>
                    <strong>Melding:</strong> <?= $message['MessageContent'] ?><br>
                    <strong>Tid:</strong> <?= $message['Timestamp'] ?><br>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="message-form">
        <!-- Skjema for å sende meldinger -->
        <form method="post">
            <div class="form-group">
                <label for="receiverId">Mottaker ID:</label>
                <input type="text" id="receiverId" name="receiverId">
            </div>
            <div class="form-group">
                <label for="messageContent">Melding:</label>
                <textarea id="messageContent" name="messageContent"></textarea>
            </div>
            <button type="submit" class="btn">Send Melding</button>
        </form>
    </div>
</div>

</body>
</html>