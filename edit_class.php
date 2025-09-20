<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Class</h2>

    <?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $result = $conn->query("SELECT * FROM classes WHERE id=$id");
        $row = $result->fetch_assoc();
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $id = $_POST['id'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];

        $sql = "UPDATE classes SET class_name='$class_name', section='$section' WHERE id=$id";
        if ($conn->query($sql)) {
            echo "<p style='color:green;'>✅ Class Updated Successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
        }
    }
    ?>

    <form method="POST" class="form">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        
        <label>Class Name:</label>
        <input type="text" name="class_name" value="<?php echo $row['class_name']; ?>" required>

        <label>Section:</label>
        <input type="text" name="section" value="<?php echo $row['section']; ?>" required>

        <button type="submit" class="btn">Update</button>
    </form>

    <a href="view_classes.php" class="btn back">⬅ Back</a>
</body>
</html>
