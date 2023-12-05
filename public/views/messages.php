<?php
require_once("../../private/config/init.php");

// Innloggings sjekk, omdirigerer hvis ikke logget inn
checkLoggedIn();

// Hent meldinger for den innloggede brukeren
$userId = $_SESSION['UserID'];
$userMessages = $messageInstance->getMessagesByUserId($userId);


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
    <style>
        .message-container {
            width: 70%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
        }

        .message-list {
            flex: 1;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            overflow-y: auto;
            max-height: 400px;
            list-style-type: none;
            margin: 0;
            padding-left: 0;
        }

        .message-list li {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        .message-list li strong {
            font-weight: bold;
        }

        .message-form {
            flex: 0 0 25%;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .message-form label,
        .message-form textarea,
        .message-form input[type="submit"] {
            display: block;
            margin-bottom: 10px;
        }

        .message-form textarea {
            height: 100px;
            resize: vertical;
        }

        .message-form input[type="submit"] {
            padding: 10px;
            background-color: #337ab7;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .message-form input[type="submit"]:hover {
            background-color: #286090;
        }
    </style>
</head>

<body>
    <?php include("../templates/navbar.php"); ?>
    <div class="message-container">
        <div class="message-list">
            <h2 style="padding-left: 20px;">Innboks</h2>
            <ul>
                <?php foreach ($userMessages as $message) : ?>
                    <li>
                        <strong>Fra:</strong> <?= $message['SenderID'] ?><br>
                        <strong>Melding:</strong> <?= $message['MessageContent'] ?><br>
                        <strong>Tid:</strong> <?= $message['Timestamp'] ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="message-form">
            <h2>Send Melding</h2>
            <form method="post">
                <label for="receiverId">Mottaker ID:</label>
                <input type="text" id="receiverId" name="receiverId">
                <label for="messageContent">Melding:</label>
                <textarea id="messageContent" name="messageContent"></textarea>
                <input type="submit" name="send_message" value="Send Melding">
            </form>
        </div>
    </div>
</body>

</html>