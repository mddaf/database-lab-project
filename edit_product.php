<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productDetails = $_POST['product_details'];
    $price = $_POST['price'];
    $availability = $_POST['availability'];
    $category = $_POST['category'];

    // Handle custom category if provided
    if ($category == 'other' && !empty($_POST['custom_category'])) {
        $category = $_POST['custom_category'];
    }

    // Update Product Image if provided
    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "uploads/"; // Define your upload directory
        $targetFile = $targetDir . basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile);

        // Update query including image
        $sql = "UPDATE product SET ProductName = ?, ProductImage = ?, ProductDetails = ?, Price = ?, Availability = ?, Category = ? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssdsis', $productName, $targetFile, $productDetails, $price, $availability, $category, $productID);
    } else {
        // Update query without image
        $sql = "UPDATE product SET ProductName = ?, ProductDetails = ?, Price = ?, Availability = ?, Category = ? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdsis', $productName, $productDetails, $price, $availability, $category, $productID);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Error updating product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the seller home page
    header("Location: seller_home.php");
    exit();
}
?>
