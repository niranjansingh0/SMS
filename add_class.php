<?php include 'db.php'; ?>

<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST['class_name']);
    $section = trim($_POST['section']);

    // PHP Validation
    if ($class_name == "" || $section == "") {
        $message = "❌ Class Name and Section are required.";
    } elseif (!preg_match("/^[A-Za-z0-9 ]+$/", $class_name)) {
        $message = "❌ Class Name can only contain letters, numbers, and spaces.";
    } elseif (!preg_match("/^[A-Za-z]$/", $section)) {
        $message = "❌ Section must be a single letter.";
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

    <form method="POST" class="form" onsubmit="return validateForm()">
        <label for="class_name">Class Name:</label>
        <input type="text" name="class_name" id="class_name" placeholder="Class Name (e.g. Class 1)" required>
        <span id="class_name_error" class="error"></span>

        <label for="section">Section:</label>
        <input type="text" name="section" id="section" placeholder="Section (e.g. A)" required>
        <span id="section_error" class="error"></span>
        
        <div class="button-container">
        <button type="submit" class="btn">Add Class</button>
        </div>
    </form>

    <div style="text-align:center; margin-top:20px;">
        <a href="view_classes.php" class="btn btn-sm edit" >📖 View Classes</a>
        <a href="index.php" class="btn back">⬅ Back</a>
    </div>

    <script>
    function validateForm() {
        let valid = true;
        document.querySelectorAll(".error").forEach(e => e.textContent = "");

        let className = document.getElementById("class_name").value.trim();
        if (className === "" || !/^[A-Za-z0-9 ]+$/.test(className)) {
            document.getElementById("class_name_error").textContent = "Class Name can only contain letters, numbers, and spaces.";
            valid = false;
        }

        let section = document.getElementById("section").value.trim();
        if (section === "" || !/^[A-Za-z]$/.test(section)) {
            document.getElementById("section_error").textContent = "Section must be a single letter.";
            valid = false;
        }

        return valid;
    }
    </script>
</body>
</html>
