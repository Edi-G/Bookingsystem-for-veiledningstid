<?php

class Message {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Send en melding
    public function sendMessage($senderId, $receiverId, $messageContent) {
        $query = "INSERT INTO messages (SenderID, ReceiverID, MessageContent, Timestamp, IsRead) VALUES (?, ?, ?, NOW(), 0)";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("iis", $senderId, $receiverId, $messageContent);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Hent alle meldinger for en bestemt bruker
    public function getMessagesByUserId($userId) {
        $query = "SELECT * FROM messages WHERE RecieverID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $messages;
    }

    // Markere en melding som lest
    public function markMessageAsRead($messageId) {
        $query = "UPDATE messages SET IsRead = 1 WHERE MessageID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $messageId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>
