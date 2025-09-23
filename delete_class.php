<?php 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $class_id = intval($_POST['id']);

    // Check if students exist in this class
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM students WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        // Redirect with error message
        header("Location: view_classes.php?msg=error_students");
        exit();
    } else {
        $stmt_del = $conn->prepare("DELETE FROM classes WHERE id=?");
        $stmt_del->bind_param("i", $class_id);

        if ($stmt_del->execute()) {
            // Redirect with success message
            header("Location: view_classes.php?msg=deleted");
            exit();
        } else {
            // Redirect with SQL error
            header("Location: view_classes.php?msg=error");
            exit();
        }
        $stmt_del->close();
    }

    $stmt->close();
} else {
    header("Location: view_classes.php");
    exit();
}
