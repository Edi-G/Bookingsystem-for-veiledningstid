<?php
require_once __DIR__ . '/../../private/config/init.php';

// Innloggings sjekk, omdirigerer hvis ikke logget inn
checkLoggedIn();

// Bruker Course-instans til å hente kursdata
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

<?php include("../templates/navbar.php"); ?>

<div class="form-container">
    <h2>Book Veiledningstime</h2>
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
                <label for="teacherSelect">Hjelpelærer:</label>
                <select id="teacherSelect" name="teacher">
                    <!-- Hjelpelærere vil bli lastet inn via JavaScript/AJAX -->
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

<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    var courseSelect = document.getElementById('courseSelect');
    var teacherSelect = document.getElementById('teacherSelect');

    // Funksjon for å hente hjelpelærere basert på valgt kurs
    function fetchAssistants(courseId) {
        fetch('../../private/actions/getAssistants.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'courseId=' + courseId
        })
        .then(response => response.json())
        .then(data => {
            teacherSelect.innerHTML = ''; // Tømmer tidligere hjelpelærere
            data.forEach(function (teacher) {
                var option = document.createElement('option');
                option.value = teacher.UserID;
                option.textContent = teacher.FullName; // Anta at dette er navnefeltet i databasen
                teacherSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
    }

    // Event listener for kursvalg
    courseSelect.addEventListener('change', function () {
        var selectedCourse = this.value;
        fetchAssistants(selectedCourse);
    });
});
</script> -->
</body>
</html>
