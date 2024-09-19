<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

$name = $_POST['name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$password = $_POST['password'];
$userType = $_POST['user_type'];

if ($userType == 'customer') {
    $sql = "INSERT INTO customer (Name, Username, Email, Phone, Address, Gender, Password) 
            VALUES ('$name', '$username', '$email', '$phone', '$address', '$gender', '$password')";
} else {
    $sql = "INSERT INTO seller (Name, Username, Email, Phone, Address, Gender, Password) 
            VALUES ('$name', '$username', '$email', '$phone', '$address', '$gender', '$password')";
}

if ($conn->query($sql) === TRUE) {
    $_SESSION['username'] = $username;
    $_SESSION['user_type'] = $userType;
    header("Location: index.php"); // Redirect to home page or dashboard
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
