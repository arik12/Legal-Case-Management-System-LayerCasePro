
<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Insert query
$sql = "INSERT INTO contact (name, email, subject, message)
        VALUES ('$name', '$email', '$subject', '$message')";

if (mysqli_query($conn, $sql)) {
    echo "Message sent successfully!";
} else {
    echo "Something went wrong!";
}

mysqli_close($conn);
?>
