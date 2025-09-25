<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>View Classes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

   <h2 style="text-align:center;">📖 Class List</h2>
   
   <?php
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == "deleted") {
            echo "<p style='color:green; text-align:center;'>✅ Class deleted successfully.</p>";
        } elseif ($_GET['msg'] == "error_students") {
            echo "<p style='color:red; text-align:center;'>❌ Cannot delete this class. Students are assigned to it.</p>";
        } elseif ($_GET['msg'] == "error") {
            echo "<p style='color:red; text-align:center;'>❌ Error deleting class.</p>";
        }
    }
    ?>
    

    <table>
        <tr>
            <th>Serial No.</th>
            <th>Class Name</th>
            <th>Section</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM classes");
        $serial = 1; // Start serial from 1
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$serial."</td>
                    <td>".$row['class_name']."</td>
                    <td>".$row['section']."</td>
                    <td>
                       <form method='POST' action='edit_class.php' style='display:inline;'>
                           <input type='hidden' name='id' value='".$row['id']."'>
                           <button type='submit' class='btn btn-sm edit'>✏ Edit</button>
                       </form>
                       <form method='POST' action='delete_class.php' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this class?');\">
                           <input type='hidden' name='id' value='".$row['id']."'>
                           <button type='submit' class='btn btn-sm delete'>🗑 Delete</button>
                       </form>
                
                    </td>
                  </tr>";
            $serial++; 
        }
        ?>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <a href="add_class.php" class="btn">➕ Add New Class</a>
        <a href="index.php" class="btn back" >⬅ Back</a>
    </div>
</body>
</html>
