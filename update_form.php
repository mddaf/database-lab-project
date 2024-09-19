<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'seller') {
    echo "You must be logged in as a seller to access this page.";
    exit();
}

// Get the product ID from the query string
$productID = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($productID <= 0) {
    echo "Invalid product ID.";
    exit();
}

// Fetch product details from the database
$sql = "SELECT ProductID, ProductName, Category, ProductDetails, Price, Availability, ProductImage FROM product WHERE ProductID = ? AND Seller = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $productID, $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();

// Fetch all categories for the dropdown
$categoryQuery = "SELECT DISTINCT Category FROM product";
$categoryResult = $conn->query($categoryQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Product</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding:0px 40px;
        }

        .navbar a {
            float: right;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }



        .navbar a.logo {
            float: left;
            font-size: 24px;
            font-weight: bold;
            color: #f2f2f2;
        }



        /* Main Content Styles */
        .main {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, 
        .form-group textarea, 
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input[type="file"] {
            padding: 0;
        }

        .form-group button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        /* Image Styles */
        .form-group img {
            max-width: 200px;
            height: 200px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Custom Category Input */
        .update-custom-category {
            margin-top: 10px;
        }

        .update-custom-category input {
            display: block;
            width: calc(100% - 16px); /* Adjust for padding */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .update-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>

    <div class="navbar">
        <div> <a href="seller_home.php" class="logo">AutoMob</a></div>
        <div>
            <a href="logout.php">Log Out</a>
            <a href="">Account</a>
            <a href="add_product.php">Add Product</a>
        </div>
    </div>

<div class="main">
    <h2>Update Product</h2>
    <form method="POST" action="update_handler.php" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">

        <!-- Update Product Image -->
        <div class="form-group">
            <label for="product_image">Current Product Image:</label>
            <img src="<?php echo $product['ProductImage']; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
            <label for="product_image">Update Product Image:</label>
            <input type="file" name="product_image" accept="image/*">
        </div>

        <!-- Update Product Name -->
        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
        </div>

        <!-- Update Product Details -->
        <div class="form-group">
            <label for="product_details">Product Details:</label>
            <textarea name="product_details" required><?php echo htmlspecialchars($product['ProductDetails']); ?></textarea>
        </div>


        <!-- Update Category -->
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category-update-select">
                <!-- Populate categories dynamically -->
                <?php while ($catRow = $categoryResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($catRow['Category']); ?>" <?php if ($product['Category'] == $catRow['Category']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($catRow['Category']); ?>
                    </option>
                <?php endwhile; ?>
                <option value="other">Other</option>
            </select>
            <div class="update-custom-category" style="display: <?php echo ($product['Category'] == 'other') ? 'block' : 'none'; ?>;">
                <label for="custom_category">Custom Category:</label>
                <input type="text" name="custom_category" placeholder="Enter custom category">
            </div>
        </div>

        <!-- Update Price -->
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['Price']); ?>" required>
        </div>

        <!-- Update Availability -->
        <div class="form-group">
            <label for="availability">Availability:</label>
            <select name="availability">
                <option value="YES" <?php if ($product['Availability'] == 'YES') echo 'selected'; ?>>Yes</option>
                <option value="NO" <?php if ($product['Availability'] == 'NO') echo 'selected'; ?>>No</option>
            </select>
        </div>

       

        <button class="update-button" type="submit">Update Product</button>
    </form>
</div>

<script>
    // JavaScript to toggle custom category input
    document.querySelector('select[name="category"]').addEventListener('change', function() {
        const customCategoryDiv = document.querySelector('.update-custom-category');
        if (this.value === 'other') {
            customCategoryDiv.style.display = 'block';
        } else {
            customCategoryDiv.style.display = 'none';
        }
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
