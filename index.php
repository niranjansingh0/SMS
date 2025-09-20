<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>School Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats {
            margin: 20px auto;
            padding: 15px;
            width: 300px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #f9f9f9;
        }
        .stats h3 {
            margin: 10px 0;
            color: #333;
        }
        .container {
            text-align: center;
            margin-top: 20px;
        }
        
    </style>
</head>
<body>
    <h2 style="text-align:center;">🏫 School Student Management System</h2>

    <?php
    // Fetch totals
    $student_count = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
    $class_count = $conn->query("SELECT COUNT(*) AS total FROM classes")->fetch_assoc()['total'];
    ?>

    <div class="stats">
        <h3>Total Students: <?php echo $student_count; ?></h3>
        <h3>Total Classes: <?php echo $class_count; ?></h3>
    </div>

    <div class="container">
        <a href="add_student.php" class="btn">➕ Add Student</a>
        <a href="view_students.php" class="btn">📋 View Students</a>
        <a href="add_class.php" class="btn">➕ Add Class</a>
        <a href="view_classes.php" class="btn">📖 View Classes</a>
    </div>
</body>
</html>
