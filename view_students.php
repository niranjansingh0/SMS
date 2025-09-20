<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit { background: #007bff; color: #fff; } 
        .delete { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <h2>📋 Student Records</h2>

    <form method="GET" class="form">
        <label for="class_id">Filter by Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php
            $result = $conn->query("SELECT * FROM classes");
            while ($row = $result->fetch_assoc()) {
                $selected = (isset($_GET['class_id']) && $_GET['class_id'] == $row['id']) ? "selected" : "";
                echo "<option value='".$row['id']."' $selected>".$row['class_name']." - ".$row['section']."</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>

    <?php
    if (isset($_GET['class_id'])) {
        $class_id = intval($_GET['class_id']);
        $result = $conn->query("SELECT s.*, c.class_name, c.section 
                                FROM students s 
                                JOIN classes c ON s.class_id = c.id
                                WHERE s.class_id = $class_id");

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
                        <a href='edit_student.php?id=".$row['id']."' class='btn btn-sm edit'>✏ Edit</a>
                        <a href='delete_student.php?id=".$row['id']."' class='btn btn-sm delete' onclick=\"return confirm('Are you sure you want to delete this student?');\">🗑 Delete</a>
                    </td>
                  </tr>";
            $serial++;
        }
        echo "</table>";

        // Print button to print all student details
        echo "<form method='post' action='' style='margin-top:10px;'>
            <button type='submit' name='print_details' class='btn print'>🖨 Print All Details</button>
        </form>";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['print_details'])) {
            // Fetch all student details for printing
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
    }
    ?>

    <a href="index.php" class="btn back">⬅ Back</a>
</body>
</html>
