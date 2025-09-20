<?php include 'db.php'; ?>

<?php
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);
$student = $conn->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $class_id = $_POST['class_id'];
    $dob = $_POST['dob'];
    $parent_contact = $_POST['parent_contact'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE students SET name=?, roll_no=?, class_id=?, dob=?, parent_contact=?, address=? WHERE id=?");
    $stmt->bind_param("siisssi", $name, $roll_no, $class_id, $dob, $parent_contact, $address, $id);

    if ($stmt->execute()) {
        header("Location: view_students.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <h2>Edit Student</h2>
    <form method="POST" class="form">
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
        <input type="number" name="roll_no" value="<?php echo $student['roll_no']; ?>" required>

        <select name="class_id" required>
            <?php
            $classes = $conn->query("SELECT * FROM classes");
            while ($row = $classes->fetch_assoc()) {
                $selected = ($row['id'] == $student['class_id']) ? "selected" : "";
                echo "<option value='".$row['id']."' $selected>".$row['class_name']." - ".$row['section']."</option>";
            }
            ?>
        </select>

        <input type="date" name="dob" value="<?php echo $student['dob']; ?>" required>
        <input type="text" name="parent_contact" value="<?php echo $student['parent_contact']; ?>" required>
        <textarea name="address" required><?php echo $student['address']; ?></textarea>

        <button type="submit" class="btn">Update Student</button>
    </form>

    <a href="view_students.php" class="btn back">⬅ Back</a>
</body>
</html>
