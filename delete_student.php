<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $student_id = intval($_POST['id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        // Redirect with success
        header("Location: view_students.php?msg=deleted");
        exit();
    } else {
        // Redirect with error
        header("Location: view_students.php?msg=error");
        exit();
    }

    $stmt->close();
} else {
    // If accessed directly without POST id
    header("Location: view_students.php");
    exit();
}
?>
