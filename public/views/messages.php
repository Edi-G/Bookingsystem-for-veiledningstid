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
    
    // Håndtere markering av meldinger som lest
    if (isset($_POST['mark_as_read'])) {
        foreach ($_POST['mark_as_read'] as $messageId => $value) {
            $messageInstance->markMessageAsRead($messageId);
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
    <!-- Bruker SweetAlert2 for popup -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            <form method="post">
                <ul>
                    <?php foreach ($userMessages as $message) : ?>
                        <li>
                            <strong>Fra:</strong> <?= $message['SenderID'] ?><br>
                            <strong>Melding:</strong>
                            <?php if ($message['IsRead'] == 0) : ?>
                                <span style="color: green;">[ULEST]</span>
                            <?php endif; ?>
                            <?= $message['MessageContent'] ?><br>
                            <strong>Tid:</strong> <?= $message['Timestamp'] ?><br>
                            <input type="checkbox" name="mark_as_read[<?= $message['MessageID'] ?>]" value="1"> Marker som lest
                        </li>
                    <?php endforeach; ?>
                    <input type="submit" name="mark_as_read_submit" value="Marker valgte som lest">
                </ul>
            </form>
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

    <script>
        // JavaScript for å vise popup-melding etter at knappen er trykket
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            // Lytter etter skjemainnsending
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Forhindrer vanlig innsending

                // Finn alle valgte checkboxer
                const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');

                // Hvis minst én checkbox er valgt
                if (checkboxes.length > 0) {
                    // Viser popup-melding ved hjelp av SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Meldinger markert som lest!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        form.submit(); // Sender skjemaet etter popup-meldingen vises
                    });
                }
            });
        });
    </script>
</body>

</html>