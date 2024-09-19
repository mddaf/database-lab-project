<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'seller') {
    echo "You must be logged in as a seller to delete products.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['product_id'];

    // Delete the product from the database
    $sql = "DELETE FROM product WHERE ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);

    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the seller home page
    header("Location: seller_home.php");
    exit();
}
?>
