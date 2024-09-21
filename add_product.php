<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <script>
        // JavaScript function to show/hide custom category input
        function toggleCustomCategory(selectElement) {
            const customCategoryInput = document.getElementById('custom-category');
            if (selectElement.value === 'other') {
                customCategoryInput.style.display = 'block';
                customCategoryInput.required = true;
            } else {
                customCategoryInput.style.display = 'none';
                customCategoryInput.required = false;
            }
        }
    </script>
    <style>
        /* General Styles */
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

        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        /* Form Styles */
        form {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
        }

        input[type="file"] {
            margin-bottom: 15px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        button[type="reset"] {
            background-color: #6c757d;
        }

        button[type="reset"]:hover {
            background-color: #5a6268;
        }

        /* Custom Category Styles */
        #custom-category {
            display: none;
        }

        #custom-category label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

    </style>
</head>
<body>
    <div class="navbar">
        <div><a href="seller_home.php" class="logo">AutoMob</a></div>
        <div>
            <a href="logout.php">Log Out</a>
            <a href="">Account</a>
            <a href="add_product.php">Add Product</a>
        </div>
    </div>
    <h2>Add New Product</h2>

    <?php
    // Include database connection
    include 'db_connection.php';

    // Fetch all categories for the dropdown
    $categoryQuery = "SELECT DISTINCT Category FROM product";
    $categoryResult = $conn->query($categoryQuery);
    ?>

    <form action="add_product_handle.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" required><br><br>

        <label for="category">Category:</label>
        <select name="category" onchange="toggleCustomCategory(this)" required>
            <option value="">Select Category</option>
            <?php while ($catRow = $categoryResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($catRow['Category']); ?>">
                    <?php echo htmlspecialchars($catRow['Category']); ?>
                </option>
            <?php endwhile; ?>
            <option value="other">Other</option>
        </select><br><br>

        <!-- Input field for custom category, initially hidden -->
        <div id="custom-category">
            <label for="custom_category">Custom Category:</label>
            <input type="text" name="custom_category"><br><br>
        </div>

        <label for="product_details">Details:</label>
        <textarea name="product_details" rows="4" cols="50" required></textarea><br><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required><br><br>

        <label for="product_image">Product Image:</label>
        <input type="file" name="product_image" accept="image/*" required><br><br>

        <button type="submit">Add Product</button>
        <button type="reset">Reset</button>
    </form>

    <?php
    $conn->close();
    ?>
</body>
</html>
