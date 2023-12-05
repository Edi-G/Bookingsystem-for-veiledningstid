<?php

class Booking {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Booke en veiledningstime
    public function createBooking($studentId, $assistantTeacherId, $courseId, $startTime, $endTime) {
        $query = "INSERT INTO bookings (StudentID, AssistantTeacherID, CourseID, StartTime, EndTime, Status) VALUES (?, ?, ?, ?, ?, 'confirmed')";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("iiiss", $studentId, $assistantTeacherId, $courseId, $startTime, $endTime);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // // Hente alle bookinger for en bestemt bruker
    // public function getBookingsByUserId($userId, $role) {
    //     if ($role == 'student') {
    //         $query = "SELECT * FROM bookings WHERE StudentID = ?";
    //     } elseif ($role == 'hjelpelærer') {
    //         $query = "SELECT * FROM bookings WHERE AssistantTeacherID = ?";
    //     } else {
    //         throw new Exception("Invalid role specified.");
    //     }
        
    //     $stmt = $this->db->prepare($query);

    //     if ($stmt === false) {
    //         throw new Exception("Unable to prepare statement: " . $this->db->error);
    //     }

    //     $stmt->bind_param("i", $userId);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $bookings = $result->fetch_all(MYSQLI_ASSOC);

    //     $stmt->close();
    //     return $bookings;
    // }

    // Hente alle bookinger for en bestemt bruker
    public function getDetailedBookingsByUserId($userId, $role)
    {
        if ($role === 'hjelpelærer') {
            $query = "SELECT b.*, c.CourseName, u.FullName 
        FROM bookings b
        JOIN courses c ON b.CourseID = c.CourseID
        JOIN users u ON b.StudentID = u.UserID
        WHERE b.AssistantTeacherID = ? AND b.Status != 'cancelled'";
        } else if ($role === 'student') {
            $query = "SELECT b.*, c.CourseName, u.FullName 
        FROM bookings b
        JOIN courses c ON b.CourseID = c.CourseID
        JOIN users u ON b.AssistantTeacherID = u.UserID
        WHERE b.StudentID = ? AND b.Status != 'cancelled'";
        } else {
            // Handle error or invalid role
            throw new Exception("Invalid role specified.");
        }
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Execution failed: " . $this->db->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    // Avbryte en booking
    public function cancelBooking($bookingId) {
        $query = "UPDATE bookings SET Status = 'cancelled' WHERE BookingID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $bookingId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Endre bookingtid
    public function updateBookingTime($bookingId, $newStartTime, $newEndTime) {
        $query = "UPDATE bookings SET StartTime = ?, EndTime = ? WHERE BookingID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("ssi", $newStartTime, $newEndTime, $bookingId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Hente spesifikk booking
    public function getBookingById($bookingId) {
        $query = "SELECT * FROM bookings WHERE BookingID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();

        $stmt->close();
        return $booking;
    }

}
