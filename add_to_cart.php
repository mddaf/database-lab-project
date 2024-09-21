<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

if (!isset($_SESSION['username'])) {
    // User is not logged in, show the message
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Required</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f7f7f7;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .message-container {
                text-align: center;
                background-color: #ffffff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .message-container h1 {
                font-size: 24px;
                color: #333333;
                margin-bottom: 20px;
            }
            .message-container p {
                font-size: 16px;
                color: #555555;
                margin-bottom: 30px;
            }
            .message-container a {
                text-decoration: none;
                font-size: 16px;
                color: #45a049;
                border: 1px solid #45a049;
                padding: 10px 20px;
                border-radius: 5px;
                transition: background-color 0.3s, color 0.3s;
            }
            .message-container a:hover {
                background-color: #45a049;
                color: #ffffff;
            }
        </style>
    </head>
    <body>
        <div class="message-container">
            <h1>Login Required</h1>
            <p>You must be logged in to view your cart.</p>
            <a href="login.php">Login</a>
        </div>
    </body>
    </html>
    ';
    exit();
}

// If the user is logged in, continue with adding to the cart
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
    header("Location: index.php");
} else {
    // Handle the error if the query fails
    echo "Error: " . $conn->error;
}

$conn->close();
exit();
