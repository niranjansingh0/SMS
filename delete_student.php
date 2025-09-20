<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM students WHERE id = $id");
}

header("Location: view_students.php");
exit();
?>
