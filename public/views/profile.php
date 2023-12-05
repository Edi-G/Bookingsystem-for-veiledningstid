<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php

require_once __DIR__ . '/../../private/config/init.php';

// Innloggings sjekk, hvis ikke omdiringeres det
checkLoggedIn();

// Hent brukrens info basert på sesjon 
$userInfo = $userInstance->getUserById($_SESSION['UserID']);

// Initialiserer selectedCourses array
$selectedCourses = [];

// Skjemabehandling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_availability'])) {
        // Prosseserer tilgjengelighets oppdatering 
        $day = filter_input(INPUT_POST, 'day');
        $startTime = filter_input(INPUT_POST, 'startTime');
        $endTime = filter_input(INPUT_POST, 'endTime');

        $updateSuccess = $userInstance->addAvailability(
            $_SESSION['UserID'],
            $day,
            $startTime,
            $endTime
        );

        setFlashMessage($updateSuccess ? "Tilgjengelighet oppdatert." : "Feil ved oppdatering av tilgjengelighet.");
        header('Location: profile.php');
        exit;
    }

    // Handle the edit booking request
    if (isset($_POST['edit_booking'])) {
        $_SESSION['bookingIdToEdit'] = $_POST['booking_id'];
        header('Location: profile.php');
        exit;
    }
    
    if (isset($_POST['update_booking_time'])) {
        $bookingId = $_POST['booking_id'];
        $newStartTime = $_POST['newStartTime'];
        $newEndTime = $_POST['newEndTime'];

        // Validerer og formaterer dato og tidV
        $newStartTimeFormatted = date('Y-m-d H:i:s', strtotime($newStartTime));
        $newEndTimeFormatted = date('Y-m-d H:i:s', strtotime($newEndTime));

        if ($bookingInstance->updateBookingTime($bookingId, $newStartTimeFormatted, $newEndTimeFormatted)) {
            setFlashMessage("Booking time updated.");
        } else {
            setFlashMessage("Error updating booking time.");
        }
        header('Location: profile.php');
        exit;
    }

    // Kansellering
    if (isset($_POST['cancel_booking'])) {
        $bookingId = $_POST['booking_id'];
        $cancelSuccess = $bookingInstance->cancelBooking($bookingId);

        if ($cancelSuccess) {
            // Suksess melding
            setFlashMessage("Booking cancelled.");
        } else {
            // Error melding
            setFlashMessage("Error cancelling booking.");
        }

        // Omdirigerer Redirect to refresh the data on the page
        header('Location: profile.php');
        exit;
    }

    // Hent bruker informasjon avhengig av rolle
    if (isset($_POST['name'], $_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $experience = $_SESSION['Role'] === 'hjelpelærer' ? $_POST['experience'] : null;
        $specializations = $_SESSION['Role'] === 'hjelpelærer' ? $_POST['specializations'] : null;

        $updateResult = $userInstance->updateUser($_SESSION['UserID'], $name, $email, $experience, $specializations);
        $flashMessage = $updateResult ? "Profilinformasjon oppdatert. " : "Feil ved oppdatering av profilinformasjon. ";
    }

    if (isset($_POST['course'])) {
        // Oppdater kursene hvis en hjelpelærer og de valgte kursene har blitt sendt inn
        if ($_SESSION['Role'] === 'hjelpelærer') {
            $assistantTeacherId = $_SESSION['UserID'];

            // Start en transaksjon
            $connection->begin_transaction();

            try {
                // Slett de nåværende kursene relatert til hjelpelæreren
                $stmt = $connection->prepare("DELETE FROM assistantteachercourses WHERE AssistantTeacherID = ?");
                $stmt->bind_param("i", $assistantTeacherId);
                $stmt->execute();
                $stmt->close();

                // Hvis noen nye kurs er valgt, legg dem inn i databasen
                if (isset($_POST['course'])) {
                    $stmt = $connection->prepare("INSERT INTO assistantteachercourses (AssistantTeacherID, CourseID) VALUES (?, ?)");
                    foreach ($_POST['course'] as $courseId) {
                        $stmt->bind_param("ii", $assistantTeacherId, $courseId);
                        $stmt->execute();
                    }
                    $stmt->close();
                }

                // Gjennomfør transaksjonen
                $connection->commit();
                setFlashMessage("Courses updated successfully.");
            } catch (mysqli_sql_exception $exception) {
                // En feil oppstod, avbryt transaksjonen
                $connection->rollback();
                setFlashMessage("An error occurred while updating courses.");
            }
        }
    }
    // Etter alle oppdateringer, sett en flash melding og omdiriger
    setFlashMessage("Profil oppdatert.");
    header("Location: profile.php");
    exit;
}

// Retrieve booking details for the overlay form if a booking edit was requested
if (isset($_SESSION['bookingIdToEdit'])) {
    $bookingIdToEdit = $_SESSION['bookingIdToEdit'];
    $bookingDetails = $bookingInstance->getBookingById($bookingIdToEdit);
    unset($_SESSION['bookingIdToEdit']); // Clear the session variable
}

// Bare hent kurs hvis brukeren er en hjelpelærer
if ($_SESSION['Role'] === 'hjelpelærer') {
    $assistantTeacherId = $_SESSION['UserID'];

    // Hent valgte kurs for hjelpelæreren
    $stmt = $connection->prepare("SELECT CourseID FROM assistantteachercourses WHERE AssistantTeacherID = ?");
    $stmt->bind_param("i", $assistantTeacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $selectedCourses[] = $row['CourseID'];
    }
    $stmt->close();
}

// Henter tilleggsinformasjon avhengig av rollen
if ($_SESSION['Role'] === 'hjelpelærer') {
    $assistantDetails = $userInstance->getAssistantDetails($_SESSION['UserID']);
    $allCourses = $courseInstance->getAllCourses();
    $availability = $userInstance->getAvailabilityByAssistant($_SESSION['UserID']);
    $bookings = $bookingInstance->getDetailedBookingsByUserId($_SESSION['UserID'], 'hjelpelærer');
} elseif ($_SESSION['Role'] === 'student') {
    $bookings = $bookingInstance->getDetailedBookingsByUserId($_SESSION['UserID'], 'student');
}

?>

<!DOCTYPE html>
<html lang="no">

<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
<?php include("../templates/navbar.php"); ?>
    <div class="container">
        <div class="form-container">
            <h2>Profil</h2>
            <form method="post">
                <!-- Delt informasjon for alle brukere -->
                <div class="form-group">
                    <label for="name">Navn:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userInfo['FullName']); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-post:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['Email']); ?>">
                </div>

                <!-- Spesifikk informasjon for hjelpelærere -->
                <?php if ($_SESSION['Role'] === 'hjelpelærer') : ?>
                    <div class="form-group">
                        <label for="experience">Erfaring:</label>
                        <textarea id="experience" name="experience"><?php echo isset($assistantDetails['Experience']) ? htmlspecialchars($assistantDetails['Experience']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="specializations">Spesialiseringer:</label>
                        <textarea id="specializations" name="specializations"><?php echo isset($assistantDetails['Specializations']) ? htmlspecialchars($assistantDetails['Specializations']) : ''; ?></textarea>
                    </div>
                    <div class="checkbox-dropdown">
                        Velg Kurs
                        <ul class="checkbox-dropdown-list">
                            <?php foreach ($allCourses as $course) : ?>
                                <li>
                                    <label>
                                        <input type="checkbox" value="<?php echo htmlspecialchars($course['CourseID']); ?>" name="course[]" <?php echo in_array($course['CourseID'], $selectedCourses) ? 'checked' : ''; ?> />
                                        <?php echo htmlspecialchars($course['CourseName']); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn">Lagre Endringer</button>
            </form>
        </div>

        <div class="availability-section">
            <!-- Faner for tilgjengelighet og bookingoversikt -->
            <ul class="tabs">
                <?php if ($_SESSION['Role'] === 'hjelpelærer') : ?>
                    <li class="tab-link current" data-tab="tab-availability">Oppdater tilgjengelighet</li>
                <?php endif; ?>
                <li class="tab-link" data-tab="tab-bookings">Oversikt over veiledningstimer</li>
            </ul>

            <?php if ($_SESSION['Role'] === 'hjelpelærer') : ?>
                <div id="tab-availability" class="tab-content current">
                    <form method="post" action="profile.php">
                        <label for="day">Dag:</label>
                        <input type="date" id="day" name="day" required>

                        <div class="time-inputs">
                            <label for="startTime">Starttid:</label>
                            <input type="time" id="startTime" name="startTime" required>

                            <label for="endTime">Sluttid:</label>
                            <input type="time" id="endTime" name="endTime" required>
                        </div>

                        <input type="submit" name="update_availability" value="Oppdater tilgjengelighet">
                    </form>
                </div>
            <?php endif; ?>

            <div id="tab-bookings" class="tab-content">
                <?php if (!empty($bookings)) : ?>
                    <ul>
                        <?php foreach ($bookings as $booking) : ?>
                            <li>

                                <strong>Kurs:</strong> <?php echo htmlspecialchars($booking['CourseName']); ?><br>
                                <?php if ($_SESSION['Role'] === 'student') : ?>
                                    <strong>Hjelpelærer:</strong> <?php echo htmlspecialchars($booking['FullName']); ?><br>
                                <?php endif; ?>
                                <?php if ($_SESSION['Role'] === 'hjelpelærer') : ?>
                                    <strong>Student:</strong> <?php echo htmlspecialchars($booking['FullName']); ?><br>
                                <?php endif; ?>
                                <strong>Tid:</strong> <?php echo htmlspecialchars($booking['StartTime']); ?> til <?php echo htmlspecialchars($booking['EndTime']); ?><br>
                                <strong>Status:</strong> <?php echo htmlspecialchars($booking['Status']); ?>


                                <!-- Buttons for actions -->
                                <div style="display: flex; gap: 20px; margin-top: -10px; margin-bottom: 25px;">
                                    <!-- Cancel Booking Button -->
                                    <form method="post" action="profile.php" style="margin: 0;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['BookingID']; ?>">
                                        <input type="submit" name="cancel_booking" value="Avbryt veiledningstime" style="width: auto; padding: 5px 10px;">
                                    </form>

                                    <!-- Edit Booking Time Button -->
                                    <?php if ($_SESSION['Role'] === 'hjelpelærer') : ?>
                                        <form method="post" action="profile.php" style="margin: 0;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['BookingID']; ?>">
                                            <input type="submit" name="edit_booking" value="Endre tid" style="width: auto; padding: 5px 10px;">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Ingen veiledningstimer funnet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (isset($bookingIdToEdit)) : ?>
        <div class="overlay">
            <div class="update-booking-form">
                <a href="profile.php" class="close-button">X</a>
                <h2>Update Booking Time</h2>
                <form method="post" action="profile.php">
                    <input type="hidden" name="booking_id" value="<?php echo $bookingIdToEdit; ?>">
                    <label for="newStartTime">Ny start tid:</label>
                    <input type="time" id="newStartTime" name="newStartTime" required>
                    <label for="newEndTime">Ny slutt tid:</label>
                    <input type="time" id="newEndTime" name="newEndTime" required>
                    <input type="submit" name="update_booking_time" value="Oppdater booking tid">
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Custom dropdown
        $(".checkbox-dropdown").click(function() {
            $(this).toggleClass("is-active");
        });

        $(".checkbox-dropdown ul").click(function(e) {
            e.stopPropagation();
        });

        document.querySelectorAll('.tab-link').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var tabId = this.getAttribute('data-tab');

                document.querySelectorAll('.tab-link').forEach(function(tab) {
                    tab.classList.remove('current');
                });
                document.querySelectorAll('.tab-content').forEach(function(tabContent) {
                    tabContent.classList.remove('current');
                });

                this.classList.add('current');
                document.getElementById(tabId).classList.add('current');
            });
        });
    </script>
</body>

</html>