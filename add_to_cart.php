<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your orders.";
    exit();
}

$productID = $_POST['product_id'];
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Get the quantity from the form, default to 1
$username = $_SESSION['username']; // Assuming the user is logged in

// Check if the product is already in the user's cart
$sql = "SELECT * FROM cart_items WHERE Username = '$username' AND ProductID = $productID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Product already in cart, update quantity
    $sql = "UPDATE cart_items SET Quantity = Quantity + $quantity WHERE Username = '$username' AND ProductID = $productID";
} else {
    // Product not in cart, insert new item with the given quantity
    $sql = "INSERT INTO cart_items (Username, ProductID, Quantity, AddedAt) 
            VALUES ('$username', $productID, $quantity, NOW())";
}

if ($conn->query($sql) === TRUE) {
    // Success, redirect back to the customer home or cart page
    header("Location: customer_home.php");
} else {
    // Handle the error if the query fails
    echo "Error: " . $conn->error;
}

$conn->close();
exit();
?>
