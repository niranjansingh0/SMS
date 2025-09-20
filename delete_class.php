<?php include 'db.php'; ?>

<?php
if (isset($_GET['id'])) {
    $class_id = intval($_GET['id']);

    // Check if students exist in this class
    $result = $conn->query("SELECT COUNT(*) AS total FROM students WHERE class_id = $class_id");
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        // Redirect with error message
        header("Location: view_classes.php?msg=error_students");
        exit();
    } else {
        if ($conn->query("DELETE FROM classes WHERE id = $class_id")) {
            // Redirect with success message
            header("Location: view_classes.php?msg=deleted");
            exit();
        } else {
            // Redirect with SQL error
            header("Location: view_classes.php?msg=error");
            exit();
        }
    }
} else {
    header("Location: view_classes.php");
    exit();
}
?>
