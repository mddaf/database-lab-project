<?php
session_start();
include 'db_connection.php'; // Ensure this file correctly sets up $conn for the database connection

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'seller') {
    echo "You must be logged in as a seller to access this page.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];

    // Update Product Image
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Validate file type (only allow images)
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $file_name = $_FILES['product_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            // Move the uploaded file to the desired folder
            $new_file_name = 'uploads/' . uniqid() . '.' . $file_ext; // Ensure unique name
            move_uploaded_file($_FILES['product_image']['tmp_name'], $new_file_name);

            // Update the product image in the database
            $sql = "UPDATE product SET ProductImage = '$new_file_name' WHERE ProductID = $product_id";
            if ($conn->query($sql) === TRUE) {
                echo "Product image updated successfully!";
            } else {
                echo "Error updating product image: " . $conn->error;
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Update Product Name
    if (isset($_POST['product_name'])) {
        $newProductName = $_POST['product_name'];

        $sql = "UPDATE product SET ProductName = ? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $newProductName, $product_id);

        if ($stmt->execute()) {
            echo "Product name updated successfully.";
        } else {
            echo "Error updating product name: " . $conn->error;
        }

        $stmt->close();
    }

    // Update Product Details
    if (isset($_POST['product_details'])) {
        $productDetails = $_POST['product_details'];

        $sql = "UPDATE product SET ProductDetails = ? WHERE ProductID = ? AND Seller = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sis', $productDetails, $product_id, $_SESSION['username']);

        if ($stmt->execute()) {
            echo "Product details updated successfully.";
        } else {
            echo "Error updating product details: " . $stmt->error;
        }

        $stmt->close();
    }

    // Update Price
    if (isset($_POST['price'])) {
        $newPrice = $_POST['price'];

        if (!is_numeric($newPrice) || $newPrice <= 0) {
            echo "Invalid price value.";
            exit();
        }

        $sql = "UPDATE product SET Price = ? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $newPrice, $product_id);

        if ($stmt->execute()) {
            echo "Price updated successfully.";
        } else {
            echo "Error updating price: " . $conn->error;
        }

        $stmt->close();
    }

    // Update Availability
    if (isset($_POST['availability'])) {
        $availability = $_POST['availability'];

        if ($availability !== 'YES' && $availability !== 'NO') {
            echo "Invalid availability value.";
            exit();
        }

        $sql = "UPDATE product SET Availability = ? WHERE ProductID = ? AND Seller = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sis', $availability, $product_id, $_SESSION['username']);

        if ($stmt->execute()) {
            echo "Availability updated successfully.";
        } else {
            echo "Error updating availability: " . $stmt->error;
        }

        $stmt->close();
    }

    // Update Category
    if (isset($_POST['category'])) {
        $new_category = $_POST['category'];
        $custom_category = isset($_POST['custom_category']) ? $_POST['custom_category'] : '';

        $new_category = trim($conn->real_escape_string($new_category));
        $custom_category = trim($conn->real_escape_string($custom_category));

        $final_category = ($new_category === 'other' && !empty($custom_category)) ? $custom_category : $new_category;

        $sql = "UPDATE product SET Category = '$final_category' WHERE ProductID = $product_id";

        if ($conn->query($sql) === TRUE) {
            echo "Category updated successfully.";
        } else {
            echo "Error updating category: " . $conn->error;
        }
    }

    $conn->close();

    // Redirect back to seller_home.php
    header("Location: seller_home.php");
    exit();
}
?>
