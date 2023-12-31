<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php
require_once __DIR__ . '/../../private/config/init.php';

// Innloggings sjekk, omdirigerer hvis ikke logget inn
checkLoggedIn();

// Initialiserer selectedCourses array
$selectedCourses = [];

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

// Skjemabehandling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Oppdater profil informasjon
    if (isset($_POST['name'], $_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $experience = $_SESSION['Role'] === 'hjelpelærer' ? $_POST['experience'] : null;
        $specializations = $_SESSION['Role'] === 'hjelpelærer' ? $_POST['specializations'] : null;

        $updateResult = $userInstance->updateUser($_SESSION['UserID'], $name, $email, $experience, $specializations);
        $flashMessage = $updateResult ? "Profilinformasjon oppdatert. " : "Feil ved oppdatering av profilinformasjon. ";
    }

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

    // Etter alle oppdateringer, sett en flash melding og omdiriger
    setFlashMessage("Profil oppdatert.");
    header("Location: profile.php");
    exit;
}

// Henter brukerinformasjonen basert på sesjon
$userInfo = $userInstance->getUserById($_SESSION['UserID']);

// Henter tilleggsinformasjon avhengig av rollen
if ($_SESSION['Role'] === 'hjelpelærer') {
    $assistantDetails = $userInstance->getAssistantDetails($_SESSION['UserID']);
    $allCourses = $courseInstance->getAllCourses();
    $availability = $userInstance->getAvailabilityByAssistant($_SESSION['UserID']);
} elseif ($_SESSION['Role'] === 'student') {
    $bookings = $bookingInstance->getBookingsByUserId($_SESSION['UserID'], 'student');
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
        <?php if ($_SESSION['Role'] === 'hjelpelærer'): ?>
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
                <?php foreach ($allCourses as $course): ?>
                <li>
                    <label>
                        <input type="checkbox" value="<?php echo htmlspecialchars($course['CourseID']); ?>" name="course[]" />
                        <?php echo htmlspecialchars($course['CourseName']); ?>
                    </label>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>



        <!-- ...tilgjengelighetsinnstillinger... -->
        <!-- ...kursvalg... -->
        
        <button type="submit" class="btn">Lagre Endringer</button>
    </form>
</div>

<script>

// Custom dropdown
$(".checkbox-dropdown").click(function () {
    $(this).toggleClass("is-active");
});

$(".checkbox-dropdown ul").click(function(e) {
    e.stopPropagation();
});


</script>
</body>
</html>
