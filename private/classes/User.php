<?php

class User {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Henter brukerdetaljer basert på ID
    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE UserID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Returnerer liste over hjelpelærere for et spesifikt kurs
    public function getAssistantsByCourse($courseId) {
        $query = "SELECT u.* FROM users u 
                  JOIN assistantteachercourses atc ON u.UserID = atc.AssistantTeacherID 
                  WHERE atc.CourseID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Henter tilgjengeligheten for en hjelpelærer
    public function getAvailabilityByAssistant($assistantId) {
        $query = "SELECT * FROM assistantteacheravailability WHERE AssistantTeacherID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $assistantId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Oppdaterer brukerinformasjon
    public function updateUser($userId, $name = null, $email = null, $experience = null, $specializations = null) {
        // Sjekk om e-posten er unik
        $checkQuery = "SELECT * FROM users WHERE Email = ? AND UserID != ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param("si", $email, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
        // E-post ikke tilgjengelig
        $checkStmt->close();
        throw new Exception("E-postadressen er allerede i bruk.");
    }
    $checkStmt->close();

        // Oppdater grunnleggende brukerinformasjon hvis angitt
        if ($name !== null || $email !== null) {
            $query = "UPDATE users SET ";
            $params = [];
            $paramTypes = '';

            if ($name !== null) {
                $query .= "FullName = ?, ";
                $params[] = $name;
                $paramTypes .= 's';
            }

            if ($email !== null) {
                $query .= "Email = ?, ";
                $params[] = $email;
                $paramTypes .= 's';
            }

            $query = rtrim($query, ', '); // Fjerner det siste kommaet
            $query .= " WHERE UserID = ?";
            $params[] = $userId;
            $paramTypes .= 'i';

            $stmt = $this->db->prepare($query);
            $stmt->bind_param($paramTypes, ...$params);
            $stmt->execute();
            $stmt->close();
        }

        // Oppdaterer hjelpelærerinformasjon hvis tilgjengelig og rollen er hjelpelærer
        if ($experience !== null && $specializations !== null && $_SESSION['Role'] === 'hjelpelærer') {
            // Først sjekk om det eksisterer detaljer
            $details = $this->getAssistantDetails($userId);
        if ($details) {
            // Detaljer finnes, oppdater dem
            $query = "UPDATE assistantteacherdetails SET Experience = ?, Specializations = ? WHERE AssistantTeacherID = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssi", $experience, $specializations, $userId);
            } else {
            // Ingen detaljer finnes, opprett dem
            $query = "INSERT INTO assistantteacherdetails (AssistantTeacherID, Experience, Specializations) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iss", $userId, $experience, $specializations);
            }
            $stmt->execute();
            $stmt->close();
        }   
    }

    // Legger til ny tilgjengelighet for en hjelpelærer
    public function addAvailability($assistantId, $day, $startTime, $endTime)
    {
        // Sjekker først om det allerede finnes en tilgjengelighet for denne dagen
        $checkQuery = "SELECT * FROM assistantteacheravailability WHERE AssistantTeacherID = ? AND Day = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param("is", $assistantId, $day);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $checkStmt->close();

        // Hvis det finnes, oppdaterer den eksisterende posten
        if ($result->num_rows > 0) {
            $query = "UPDATE assistantteacheravailability SET StartTime = ?, EndTime = ? WHERE AssistantTeacherID = ? AND Day = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssis", $startTime, $endTime, $assistantId, $day);
        } else {
            // Hvis ikke, legger til en ny 
            $query = "INSERT INTO assistantteacheravailability (AssistantTeacherID, Day, StartTime, EndTime) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isss", $assistantId, $day, $startTime, $endTime);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }


    public function getAssistantDetails($assistantTeacherId) {
        $query = "SELECT * FROM assistantteacherdetails WHERE AssistantTeacherID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $assistantTeacherId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $details = $result->fetch_assoc();
            $stmt->close();
            return $details;
        } else {
            $stmt->close();
            return null; 
        }
    }
}
?>