<?php 
include 'db.php';

$message = "";
$row = ['id'=>'','class_name'=>'','section'=>'']; // default values

$id = null;

// Load class data if ID exists via POST (from edit button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT * FROM classes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    if ($data) {
        $row = $data;
    } else {
        // ID not found in DB
        $id = null; // treat as invalid
    }
}

// Handle form submission (only allow update if ID exists)
if (isset($_POST['class_name'], $_POST['section'], $_POST['id'])) {
    if (empty($_POST['id']) || $id === null) {
        $message = "❌ Class not found.";
    } else {
        $id = intval($_POST['id']);
        $class_name = trim($_POST['class_name']);
        $section = trim($_POST['section']);

        // PHP Validations
        if ($class_name == "" || $section == "") {
            $message = "❌ Class Name and Section are required.";
        } elseif (!preg_match("/^[A-Za-z0-9 ]+$/", $class_name)) {
            $message = "❌ Class Name can only contain letters, numbers, and spaces.";
        } elseif (!preg_match("/^[A-Za-z]$/", $section)) {
            $message = "❌ Section must be a single letter.";
        } else {
            // Check duplicate class + section (excluding current id)
            $check = $conn->prepare("SELECT id FROM classes WHERE class_name=? AND section=? AND id != ?");
            $check->bind_param("ssi", $class_name, $section, $id);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $message = "❌ Class <b>$class_name $section</b> already exists!";
            } else {
                // Update class
                $stmt = $conn->prepare("UPDATE classes SET class_name=?, section=? WHERE id=?");
                $stmt->bind_param("ssi", $class_name, $section, $id);

                if ($stmt->execute()) {
                    $message = "✅ Class Updated Successfully!";
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $check->close();
        }

        // Refresh form values
        $row['class_name'] = $class_name;
        $row['section'] = $section;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>✏️ Edit Class</h2>

    <?php if ($message): ?>
        <p class="message <?php echo strpos($message,'✅') !== false ? 'success':'error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="form" onsubmit="return validateForm()">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="class_name">Class Name:</label>
        <input type="text" name="class_name" id="class_name" value="<?php echo htmlspecialchars($row['class_name']); ?>" required>
        <span id="class_name_error" class="error"></span>

        <label for="section">Section:</label>
        <input type="text" name="section" id="section" value="<?php echo htmlspecialchars($row['section']); ?>" required>
        <span id="section_error" class="error"></span>

        <button type="submit" class="btn">Update</button>
    </form>

    <a href="view_classes.php" class="btn back">⬅ Back</a>

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
