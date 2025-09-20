<?php include 'db.php'; ?>

<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST['class_name']);
    $section = trim($_POST['section']);

    if ($class_name == "" || $section == "") {
        $message = "❌ Class Name and Section are required.";
    } else {
        // 🔍 Check if class + section already exists
        $check = $conn->prepare("SELECT id FROM classes WHERE class_name = ? AND section = ?");
        $check->bind_param("ss", $class_name, $section);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "❌ Class <b>$class_name $section</b> already exists!";
        } else {
            // ✅ Insert new class if not exists
            $stmt = $conn->prepare("INSERT INTO classes (class_name, section) VALUES (?, ?)");
            $stmt->bind_param("ss", $class_name, $section);

            if ($stmt->execute()) {
                $message = "✅ Class added successfully!";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2 style="text-align:center;">➕ Add New Class</h2>

    <?php if ($message): ?>
        <p class="message <?php echo strpos($message,'✅') !== false ? 'success':'error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="form">
        <input type="text" name="class_name" placeholder="Class Name (e.g. Class 1)" required>
        <input type="text" name="section" placeholder="Section (e.g. A)" required>
        <button type="submit" class="btn">Add Class</button>
    </form>

    <div style="text-align:center; margin-top:20px;">
        <a href="view_classes.php" class="btn btn-sm edit" >📖 View Classes</a>
        <a href="index.php" class="btn back">⬅ Back</a>
    </div>
</body>
</html>
