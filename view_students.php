<?php 
include 'db.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>📋 Student Records</h2>

    <!-- Filter by class -->
    <form method="POST" class="form">
        <label for="class_id">Filter by Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php
            $result = $conn->query("SELECT * FROM classes");
            while ($row = $result->fetch_assoc()) {
                $selected = (isset($_POST['class_id']) && $_POST['class_id'] == $row['id']) ? "selected" : "";
                echo "<option value='".$row['id']."' $selected>".$row['class_name']." - ".$row['section']."</option>";
            }
            ?>
        </select>

        <div class="button-container">
        <button type="submit" name="filter_submit" class="btn">Filter</button>
        </div>
    </form>

<?php
if (isset($_POST['filter_submit']) && !empty($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);

    $result = $conn->query("SELECT s.*, c.class_name, c.section 
                            FROM students s 
                            JOIN classes c ON s.class_id = c.id
                            WHERE s.class_id = $class_id");

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Serial</th>
                <th>Name</th>
                <th>Roll No</th>
                <th>Class</th>
                <th>Section</th>
                <th>DOB</th>
                <th>Parent Contact</th>
                <th>Address</th>
                <th>Actions</th>
              </tr>";
        $serial = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$serial."</td>
                    <td>".$row['name']."</td>
                    <td>".$row['roll_no']."</td>
                    <td>".$row['class_name']."</td>
                    <td>".$row['section']."</td>
                    <td>".$row['dob']."</td>
                    <td>".$row['parent_contact']."</td>
                    <td>".$row['address']."</td>
                    <td>
                        <form method='POST' action='edit_student.php' style='display:inline;'>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button type='submit' class='btn btn-sm edit'>✏ Edit</button>
                        </form>
                        <form method='POST' action='delete_student.php' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this student?');\">
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button type='submit' class='btn btn-sm delete'>🗑 Delete</button>
                        </form>

                    </td>
                  </tr>";
            $serial++;
        }
        echo "</table>";

        // Print Button Form
        echo "<form method='POST' style='margin-top:10px;'>
                <input type='hidden' name='class_id' value='".$class_id."'>
                <button type='submit' name='print_details' class='btn print'>🖨 Print All Details</button>
              </form>";

    } else {
        echo "<p class='center-message'>No students found for this class.</p>";
    }
}

// Handle Print Button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['print_details']) && !empty($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']); 

    $detailsResult = $conn->query("SELECT s.*, c.class_name, c.section 
                                   FROM students s 
                                   JOIN classes c ON s.class_id = c.id
                                   WHERE s.class_id = $class_id"); 

    $detailsArray = [];
    while ($row = $detailsResult->fetch_assoc()) {
        $detailsArray[] = $row;
    }

    echo "<script>
        var students = ".json_encode($detailsArray).";
        var printWindow = window.open('', '', 'height=600,width=900');
        printWindow.document.write('<h2>Student Details</h2><table border=\"1\" cellpadding=\"5\"><tr><th>Name</th><th>Roll No</th><th>Class</th><th>Section</th><th>DOB</th><th>Parent Contact</th><th>Address</th></tr>');
        for(var i=0; i<students.length; i++) {
            printWindow.document.write('<tr>' +
                '<td>' + students[i].name + '</td>' +
                '<td>' + students[i].roll_no + '</td>' +
                '<td>' + students[i].class_name + '</td>' +
                '<td>' + students[i].section + '</td>' +
                '<td>' + students[i].dob + '</td>' +
                '<td>' + students[i].parent_contact + '</td>' +
                '<td>' + students[i].address + '</td>' +
            '</tr>');
        }
        printWindow.document.write('</table>');
        printWindow.document.close();
        printWindow.print();
    </script>";
}

?>

<a href="index.php" class="btn back">⬅ Back</a>
</body>
</html>
