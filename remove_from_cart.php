<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if product_id is set
if (isset($_GET['product_id'])) {
    $productID = $_GET['product_id'];
    $username = $_SESSION['username']; // Assuming the user is logged in

    // Delete the product from the cart_items table
    $sql = "DELETE FROM cart_items WHERE ProductID = $productID AND Username = '$username'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Item removed from the cart.";
    } else {
        echo "Error removing item: " . $conn->error;
    }

    $conn->close();
    
    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
} else {
    echo "Invalid request.";
    header("Location: cart.php");
    exit();
}
?>
