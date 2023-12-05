<?php
require_once __DIR__ . '/../../private/config/init.php';

// Innloggings sjekk, omdirigerer hvis ikke logget inn
checkLoggedIn();

// Bruker Course-instans til å hente kursdata
$allCourses = $courseInstance->getAllCourses();

$assistantTeacherId = null;
$selectedCourseId = $_POST['course'] ?? $allCourses[0]['CourseID'];
$date = $_POST['date'] ?? null;
$chosenTime = $_POST['chosenTime'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookTime'])) {
    // Prosseserer bookingen
    $studentId = $_SESSION['UserID'];
    $assistantTeacherId = $_POST['teacher'];
    $courseId = $_POST['course'];
    $startTime = $date . ' ' . $chosenTime;
    $endTime = (new DateTime($startTime))->add(new DateInterval('PT30M'))->format('Y-m-d H:i:s');

    $bookingMade = $bookingInstance->createBooking($studentId, $assistantTeacherId, $courseId, $startTime, $endTime);

    if ($bookingMade) {
    } else {  
    }
}

// Hent hjelpelærere forFetch assistant teachers for the selected course
$assistantTeachersQuery = "SELECT u.UserID, u.FullName FROM assistantteachercourses atc JOIN users u ON atc.AssistantTeacherID = u.UserID WHERE atc.CourseID = ?";
$assistantTeachersStmt = $connection->prepare($assistantTeachersQuery);
$assistantTeachersStmt->bind_param("i", $selectedCourseId);
$assistantTeachersStmt->execute();
$assistantTeachersResult = $assistantTeachersStmt->get_result();
$assistantTeachers = $assistantTeachersResult->fetch_all(MYSQLI_ASSOC);
$assistantTeachersStmt->close();

// Bestem hjelpe lærer ID basert på skjema innsending
$assistantTeacherId = $_POST['teacher'] ?? null; // Henter hjelpelærer ID fra skjema innsending 

// Hent tilgjengelige tider hvis en dato og hjelpelærer har blitt valgt 
$availableSlots = [];
if ($date && $assistantTeacherId) {
    $availableSlots = $bookingInstance->getAvailableSlots($assistantTeacherId, $date);
}

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
            <form id="bookingForm" action="booking.php" method="post">
                <div class="form-group">
                    <label for="courseSelect">Kurs:</label>
                    <select id="courseSelect" name="course">
                        <?php foreach ($allCourses as $course) : ?>
                            <option value="<?= htmlspecialchars($course['CourseID']) ?>" <?= (isset($_POST['course']) && $_POST['course'] == $course['CourseID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['CourseName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="teacherSelect">Hjelpelærer:</label>
                    <select id="teacherSelect" name="teacher">
                        <?php if (count($assistantTeachers) > 0) : ?>
                            <?php foreach ($assistantTeachers as $teacher) : ?>
                                <option value="<?= htmlspecialchars($teacher['UserID']) ?>" <?= (isset($_POST['teacher']) && $_POST['teacher'] == $teacher['UserID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option>Ingen tilgjengelige hjelpelærere</option>
                        <?php endif; ?>
                    </select>
                </div>

                <label for="dateSelect">Dato:</label>
                <input type="date" id="dateSelect" name="date" onchange="this.form.submit()" value="<?= isset($_POST['date']) ? $_POST['date'] : '' ?>" required>

                <input type="hidden" name="chosenTime" id="hiddenTimeSlot" value="">

                <div class="form-group">
                <?php if (!empty($availableSlots)) : ?>
                    <label for="timeSelect">Tid:</label>
                    <?php foreach ($availableSlots as $timeSlot) : ?>
                        <div class="time-slot">
                            <input type="radio" id="time-<?= $timeSlot ?>" name="chosenTime" value="<?= $timeSlot ?>" <?= $chosenTime == $timeSlot ? 'checked' : '' ?>>
                            <label for="time-<?= $timeSlot ?>"><?= $timeSlot ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <p>Ingen ledige tider for valgt dato.</p>
                    <?php endif; ?>
                </div>

                <button type="submit" name="bookTime" class="btn">Bestill Time</button>
            </form>
        </div>
    </div>
</body>
</html>