<?php
require_once __DIR__ . '/../../private/config/init.php';

// Oppretter en Course-instans og bruker den til å hente kursdata
$courseInstance = new Course($connection);
$allCourses = $courseInstance->getAllCourses();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Booking</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="container">
    <h1>Book Veiledningstime</h1>

    <div class="booking-form">
        <form id="bookingForm" action="../../private/classes/Booking.php" method="post">
            <div class="form-group">
                <label for="courseSelect">Kurs:</label>
                <select id="courseSelect" name="course">
                    <?php
                    foreach ($allCourses as $course) {
                        echo "<option value=\"{$course['CourseID']}\">{$course['CourseName']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <select id="teacherSelect" name="teacher">
                    <?php
                    // Anta at $teachers er en array hentet fra databasen
                    foreach ($teachers as $teacher) {
                        echo "<option value=\"{$teacher['id']}\">{$teacher['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dateSelect">Dato:</label>
                <input type="date" id="dateSelect" name="date">
            </div>

            <div class="form-group">
                <label for="timeSelect">Tid:</label>
                <input type="time" id="timeSelect" name="time">
            </div>

            <button type="submit" class="btn">Bestill Time</button>
        </form>
    </div>

    <div id="calendar">
        <!-- Eksempelvis kan PHP-script generere tilgjengelige tidsluker basert på valgt hjelpelærer og kurs -->
    </div>

</div>
</body>
</html>
