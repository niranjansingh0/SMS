<?php
include 'db.php';
$message = "";

// Initialize student array with empty/default values
$student = [
    'id'=>'',
    'name'=>'',
    'roll_no'=>'',
    'class_id'=>'',
    'dob'=>'',
    'parent_contact'=>'',
    'address'=>''
];

// Determine if ID is provided via POST 
$id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $student = $data;
    } else {
        // ID does not exist
        $id = null; // treat as invalid
    }
}

// Handle form submission (only allow update if ID exists in DB)
if (isset($_POST['submit'])) {

    if (empty($_POST['id']) || $id === null) {
        // Prevent update if ID is missing or invalid
        $message = "❌ Student not found.";
    } else {
        $name = trim($_POST['name']);
        $roll_no = trim($_POST['roll_no']);
        $class_id = intval($_POST['class_id']);
        $dob = $_POST['dob'];
        $parent_contact = trim($_POST['parent_contact']);
        $address = trim($_POST['address']);

        // Validations
        if (!preg_match("/^[A-Za-z ]+$/", $name)) {
            $message = "❌ Invalid name.";
        } elseif (!ctype_digit($roll_no)) {
            $message = "❌ Roll number must be numeric.";
        } elseif (!preg_match("/^[0-9]{10}$/", $parent_contact)) {
            $message = "❌ Parent contact must be 10 digits.";
        } elseif (strlen($address) < 5) {
            $message = "❌ Address too short.";
        } else {
            // Check for duplicate roll number
            $check = $conn->prepare("SELECT id FROM students WHERE roll_no=? AND class_id=? AND id!=?");
            $check->bind_param("sii", $roll_no, $class_id, $id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "❌ Roll number already exists in this class.";
            } else {
                // Update student
                $stmt = $conn->prepare("UPDATE students SET name=?, roll_no=?, class_id=?, dob=?, parent_contact=?, address=? WHERE id=?");
                $stmt->bind_param("ssisssi", $name, $roll_no, $class_id, $dob, $parent_contact, $address, $id);

                if ($stmt->execute()) {
                    $message = "✅ Student updated successfully!";
                    
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $check->close();
        }
        // refresh form values
        $student = [
            'id'=>$id,
            'name'=>$name,
            'roll_no'=>$roll_no,
            'class_id'=>$class_id,
            'dob'=>$dob,
            'parent_contact'=>$parent_contact,
            'address'=>$address
        ];        
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>✏️ Edit Student</h2>

<?php if ($message): ?>
<p class="message <?php echo strpos($message,'✅')!==false?'success':'error'; ?>">
    <?php echo $message; ?>
</p>
<?php endif; ?>

<form method="POST" class="form" onsubmit="return validateForm()">
    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
    <span id="name_error" class="error"></span>

    <label for="roll_no">Roll No:</label>
    <input type="text" name="roll_no" id="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required>
    <span id="roll_error" class="error"></span>

    <label for="class_id">Class:</label>
    <select name="class_id" id="class_id" required>
        <?php
        $classes = $conn->query("SELECT * FROM classes");
        while ($row = $classes->fetch_assoc()) {
            $selected = ($row['id']==$student['class_id'])?'selected':'';
            echo "<option value='{$row['id']}' $selected>{$row['class_name']} - {$row['section']}</option>";
        }
        ?>
    </select>
    <span id="class_error" class="error"></span>

    <label for="dob">DOB:</label>
    <input type="date" name="dob" id="dob" value="<?php echo $student['dob']; ?>" required>
    <span id="dob_error" class="error"></span>

    <label for="parent_contact">Parent Contact:</label>
    <input type="text" name="parent_contact" id="parent_contact" value="<?php echo $student['parent_contact']; ?>" required>
    <span id="phone_error" class="error"></span>

    <label for="address">Address:</label>
    <textarea name="address" id="address" required><?php echo htmlspecialchars($student['address']); ?></textarea>
    <span id="address_error" class="error"></span>

    <div class="button-container">
        <button type="submit" name="submit" class="btn">Update Student</button>
    </div>
</form>

<a href="view_students.php" class="btn back">⬅ Back</a>

<script>
//  JavaScript Validation 
function validateForm() {
    let valid = true;
    document.querySelectorAll(".error").forEach(e => e.textContent = "");

    let name = document.getElementById("name").value;
    if (!/^[A-Za-z ]+$/.test(name)) {
        document.getElementById("name_error").textContent = "Name must contain only letters and spaces.";
        valid = false;
    }

    let roll = document.getElementById("roll_no").value;
    if (!/^[0-9]+$/.test(roll)) {
        document.getElementById("roll_error").textContent = "Roll Number must be numeric.";
        valid = false;
    }

    let class_id = document.getElementById("class_id").value;
    if (class_id === "") {
        document.getElementById("class_error").textContent = "Please select a class.";
        valid = false;
    }

    let dob = new Date(document.getElementById("dob").value);
    let today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    let m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) { age--; }
    if (age < 3 || age > 20) {
        document.getElementById("dob_error").textContent = "Age must be between 3 and 20 years.";
        valid = false;
    }

    let phone = document.getElementById("parent_contact").value;
    if (!/^[0-9]{10}$/.test(phone)) {
        document.getElementById("phone_error").textContent = "Enter a valid 10-digit phone number.";
        valid = false;
    }

    let address = document.getElementById("address").value.trim();
    if (address.length < 5) {
        document.getElementById("address_error").textContent = "Address must be at least 5 characters.";
        valid = false;
    }

    return valid;
}
</script>
</body>
</html>
