<?php include 'db.php'; ?>

<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $roll_no = trim($_POST['roll_no']);  
    $class_id = intval($_POST['class_id']);
    $dob = $_POST['dob'];
    $parent_contact = trim($_POST['parent_contact']);
    $address = trim($_POST['address']);

    // PHP Validations
    if (!preg_match("/^[A-Za-z ]+$/", $name)) {
        $message = "❌ Invalid name.";
    } elseif (!ctype_digit($roll_no) || strlen($roll_no) < 1) {
        $message = "❌ Roll number must be numeric and valid.";
    } elseif (!preg_match("/^[0-9]{10}$/", $parent_contact)) {
        $message = "❌ Parent contact must be 10 digits.";
    } elseif (strlen($address) < 5) {
        $message = "❌ Address too short.";
    } else {
        //  Check duplicate roll number within same class
        $check = $conn->prepare("SELECT id FROM students WHERE roll_no = ? AND class_id = ?");
        $check->bind_param("si", $roll_no, $class_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "❌ Roll number already exists in this class.";
        } else {
            //  Insert into database
            $stmt = $conn->prepare("INSERT INTO students (name, roll_no, class_id, dob, parent_contact, address) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisss", $name, $roll_no, $class_id, $dob, $parent_contact, $address);

            if ($stmt->execute()) {
                $message = "✅ Student added successfully!";
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
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <h2>➕ Add New Student</h2>

   <!-- Show success or error -->
   <?php if ($message): ?>
       <p class="message <?php echo strpos($message,'✅') !== false ? 'success':'error'; ?>">
           <?php echo $message; ?>
       </p>
   <?php endif; ?>
    
   <form method="POST" class="form" onsubmit="return validateForm()">
       <label for="name">Student Name:</label>
       <input type="text" name="name" id="name" placeholder="Student Name" required>
       <span id="name_error" class="error"></span>
       
       <label for="roll_no">Roll Number:</label>
       <input type="text" name="roll_no" id="roll_no" placeholder="Roll Number" required>
       <span id="roll_error" class="error"></span>
      
       <label for="class_id">Class:</label>
       <select name="class_id" id="class_id" required>
           <option value="">Select Class</option>
           <?php
           $result = $conn->query("SELECT * FROM classes");
           while ($row = $result->fetch_assoc()) {
               echo "<option value='".$row['id']."'>".$row['class_name']." - ".$row['section']."</option>";
           }
           ?>
       </select>
       <span id="class_error" class="error"></span>
      
       <label for="dob">Date of Birth:</label>
       <input type="date" name="dob" id="dob" required>
       <span id="dob_error" class="error"></span>
        
       <label for="parent_contact">Parent Contact:</label>
       <input type="text" name="parent_contact" id="parent_contact" placeholder="Parent Contact" required>
       <span id="phone_error" class="error"></span>
    
       <label for="address">Address:</label>
       <textarea name="address" id="address" placeholder="Address" required></textarea>
       <span id="address_error" class="error"></span>
       
       <div class="button-container">
       <button type="submit" class="btn">Add Student</button>
       </div>
   </form>


   <a href="index.php" class="btn back">⬅ Back</a>

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
   <!-- Developed by Niranjan singh -->
</body>
</html>
