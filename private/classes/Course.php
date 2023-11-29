<?php

class Course {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Henter alle kursene
    public function getAllCourses() {
        $query = "SELECT * FROM courses";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $courses = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $courses;
    }

    // Finner et spesifikt kurs basert på ID
    public function getCourseById($courseId) {
        $query = "SELECT * FROM courses WHERE CourseID = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        $stmt->close();
        return $course;
    }

    // Legger til et nytt kurs i databasen
    public function addCourse($courseName) {
        $query = "INSERT INTO courses (CourseName) VALUES (?)";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param("ss", $courseName);

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
