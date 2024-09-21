<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

$username = $_POST['username'];
$password = $_POST['password'];
$userType = $_POST['user_type'];

// Determine the correct table to query based on user type
if ($userType == 'customer') {
    $sql = "SELECT * FROM customer WHERE Username = '$username' AND Password = '$password'";
} else {
    $sql = "SELECT * FROM seller WHERE Username = '$username' AND Password = '$password'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Set session variables
    $_SESSION['username'] = $username;
    $_SESSION['user_type'] = $userType;

    // Redirect based on user type
    if ($userType == 'customer') {
        header("Location: index.php"); // Redirect to customer home page
    } else {
        header("Location: seller_home.php"); // Redirect to seller home page
    }
    exit();
} else {
    echo "Invalid username or password";
}

$conn->close();
?>
