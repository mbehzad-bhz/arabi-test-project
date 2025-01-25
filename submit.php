<?php
require 'config.php';






if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $problem = mysqli_real_escape_string($conn, trim($_POST['problem']));
    $priority = mysqli_real_escape_string($conn, trim($_POST['priority']));
    $dueDate = mysqli_real_escape_string($conn, trim($_POST['dueDate']));
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    $date = mysqli_real_escape_string($conn, trim($_POST['date']));

    // Debug: Check if form data is collected
    error_log("Form Data: Name = $name, Subject = $subject, Priority = $priority, Date = $date");

    // Handle file input (if any)
    $file_input = null;
    if (isset($_FILES['file_input']) && $_FILES['file_input']['error'] == 0) {
        // File upload handling
        $fileTmpPath = $_FILES['file_input']['tmp_name'];
        $fileName = $_FILES['file_input']['name'];
        $fileSize = $_FILES['file_input']['size'];
        $fileType = $_FILES['file_input']['type'];
        
        // Validate file size
        $maxFileSize = 5 * 1024 * 1024; // 5MB max size
        if ($fileSize > $maxFileSize) {
            die('Error: File size exceeds the limit.');
        }

        // Define upload directory
        $upload_dir = 'uploads/';

        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate a unique file name to avoid overwriting
        $newFileName = uniqid() . '_' . basename($fileName);
        $uploadPath = $upload_dir . $newFileName;

        // Move the uploaded file to the 'uploads' folder
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            $file_input = $newFileName; // Store the new file name
        } else {
            error_log("Error moving file to uploads folder.");
            die('Error: File upload failed.');
        }
    }

    
    // Reset AUTO_INCREMENT if the table is empty
    $check_empty = "SELECT COUNT(*) AS count FROM request";
    $result = $conn->query($check_empty);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            $reset_auto_increment = "ALTER TABLE request AUTO_INCREMENT = 1";
            if (!$conn->query($reset_auto_increment)) {
                die("Error resetting auto-increment: " . $conn->error);
            }
        }
    } else {
        die("Error checking table: " . $conn->error);
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO request (name, subject, problem, priority, dueDate, comment, date, file_input) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log('Error preparing the SQL statement: ' . $conn->error);
        die('Error preparing the SQL statement: ' . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssss", $name, $subject, $problem, $priority, $dueDate, $comment, $date, $file_input);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        echo "Data submitted successfully!";
    } else {
        error_log("Error executing SQL: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
