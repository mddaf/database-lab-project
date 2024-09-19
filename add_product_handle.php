<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'seller') {
    echo "You must be logged in as a seller to access this page.";
    exit();
}

$seller = $_SESSION['username'];

// Fetch product details from the form submission
$productName = $_POST['product_name'];
$category = $_POST['category'];
$productDetails = $_POST['product_details'];
$price = $_POST['price'];

// Check if a custom category is provided
if ($category === 'other') {
    $customCategory = $_POST['custom_category'];
    if (!empty($customCategory)) {
        $category = $customCategory; // Use the custom category
    } else {
        echo "Please enter a custom category.";
        exit();
    }
}

// Handle file upload for product image
$uploadDir = 'uploads/';
$imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
$imageFileName = uniqid() . '.' . $imageFileType; // Generate a unique file name
$targetFile = $uploadDir . $imageFileName;

// Check if image file is an actual image or fake image
$check = getimagesize($_FILES["product_image"]["tmp_name"]);
if ($check === false) {
    echo "File is not an image.";
    exit();
}

// Check if file already exists (shouldn't happen due to uniqid(), but good to check)
if (file_exists($targetFile)) {
    echo "Sorry, file already exists.";
    exit();
}

// Check file size (5MB limit here, adjust as needed)
if ($_FILES["product_image"]["size"] > 5000000) {
    echo "Sorry, your file is too large.";
    exit();
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    exit();
}

// Attempt to upload the file
if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
    echo "Sorry, there was an error uploading your file.";
    exit();
}

// Prepare SQL statement to insert new product
$sql = "INSERT INTO product (ProductName, Category, ProductDetails, Price, Seller, Availability, ProductImage) VALUES (?, ?, ?, ?, ?, 'YES', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssss', $productName, $category, $productDetails, $price, $seller, $targetFile);

// Execute the statement
if ($stmt->execute()) {
    echo "New product added successfully.";
    // Redirect to seller home or success page
    header("Location: seller_home.php");
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
