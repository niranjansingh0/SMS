# 🏫 School Management System (SMS)

This is a simple **PHP + MySQL** based School Management System (SMS).  
It allows adding students with proper validations, prevents duplicate roll numbers per class, and ensures secure database operations.

---

## 🚀 Features

- Add students with **JavaScript + PHP validation**
- Prevent duplicate roll numbers within the same class
- Secure **prepared statements** to stop SQL injection
- Use of **.env** file for database configuration
- Clean UI with styled buttons and responsive design

---

## 📥 Installation & Setup

### 1️⃣ Clone the repository
```bash
git clone https://github.com/niranjansingh0/SMS.git
cd SMS
```

### 2️⃣ Setup environment variables
Create a `.env` file.
Update `.env` with your database credentials:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=school_db
```

### 3️⃣ Import the database
Create a new MySQL database:
```sql
CREATE DATABASE school_db;
```

(Optional) Import schema if Required:
```bash
mysql -u root -p school_db < school_db.sql
```

### 4️⃣ Run the project
- Place files into `htdocs` (XAMPP) or `www` (WAMP)  
- Start Apache & MySQL  
- Open in browser:
```
http://localhost/SMS
```

---

## 📂 Project Structure
```
SMS/
│-- db.php           # Database connection 
│-- .env             # Environment variables (ignored in Git)
│-- style.css        # Stylesheet
│-- index.php        # Dashboard / Home
│-- add_student.php  # Add student form
│-- add_class.php    # Add class form 
│-- delete_class.php # Delete Classes
│-- delete_student.php # Delete Students
│-- edit_class.php   # Update the Class Name
│-- edit_student.php # Update Student Data
│-- view_classes.php # Show all classes
│-- view_students.php# Show all Students data
│-- .gitignore       # Git ignored files
│-- README.md        # Project documentation
```

---
 

## 👨‍💻 Author

- **Niranjan Singh**  
📌 Repo: [SMS](https://github.com/niranjansingh0/SMS)
