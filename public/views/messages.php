<?php
require_once __DIR__ . '/../../private/config/init.php';

// Bruk Message-instans til å sende og hente meldinger
$messageInstance = new Message($dbConnection);

// Hent og vis meldinger for en bestemt bruker
$userId = 1;
$userMessages = $messageInstance->getMessagesByUserId($userId);

?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Meldinger</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="message-container">
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
        <form id="messageForm" action="../../private/classes/Message.php" method="post">
            <input type="hidden" name="senderId" value="<?= $userId ?>">
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
