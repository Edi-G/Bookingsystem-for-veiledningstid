<?php
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['courseId'])) {
    $courseId = $_POST['courseId'];
    $userInstance = new User($connection);
    $assistants = $userInstance->getAssistantsByCourse($courseId);

    // Returnerer data som JSON
    echo json_encode($assistants);
} else {
    echo json_encode([]);
}
?>
