<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'seller') {
    echo "You must be logged in as a seller to access this page.";
    exit();
}

$seller = $_SESSION['username'];

// Fetch search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// SQL query to fetch products put up for sale by the seller
$sql = "SELECT ProductID, ProductName, Category, ProductDetails, Price, Availability, ProductImage FROM product WHERE Seller = '$seller'";

// Apply search filter
if ($search != '') {
    $sql .= " AND ProductName LIKE '%" . $conn->real_escape_string($search) . "%'";
}

// Apply category filter
if ($category != '') {
    $sql .= " AND Category = '" . $conn->real_escape_string($category) . "'";
}

$result = $conn->query($sql);

// Fetch all categories for the dropdown
$categoryQuery = "SELECT DISTINCT Category FROM product";
$categoryResult = $conn->query($categoryQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Home</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navbar Styles */
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
            max-width: 1200px;
            margin: auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Search and Filter Styles */
        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: space-between;
            align-items: center;
            
        }

        form input[type="text"],
        form select,
        form button {
            padding: 10px;
            font-size: 16px;
        }

        form input[type="text"] {
            width: 200px;
        }

        form button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #555;
        }

        /* Product Grid Styles */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            /* text-align: center; */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 200px;
            height: 200px;
            /* object-fit: cover; */
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .product-card h3 {
            font-size: 20px;
            margin: 10px 0;
            color: #333;
        }

        .product-card p {
            font-size: 14px;
            color: #666;
        }
        .btn form{
            margin:0px;
        }
        .product-card .btn {
            display: flex;
            /* justify-content: center; */
            gap: 10px;
            /* margin-top: 10px; */
        }

        .product-card .edit-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .product-card .edit-button:hover {
            background-color: #0056b3;
        }

        .product-card .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .product-card .delete-button:hover {
            background-color: #c82333;
        }

        .product-card .edit-button a {
            color: white;
            text-decoration: none;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f9f9f9;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
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
    <h2>My Products</h2>
    <form method="GET" action="seller_home.php">
        <input style="width:50%" type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
        <select style="width:40%" name="category">
            <option value="">Select Category</option>
            <!-- Populate categories dynamically -->
            <?php while ($catRow = $categoryResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($catRow['Category']); ?>" <?php if ($category == $catRow['Category']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($catRow['Category']); ?>
                </option>
            <?php endwhile; ?>
           
        </select>
        <div id="custom-category" style="display: <?php echo ($category == 'other') ? 'block' : 'none'; ?>;">
            <label for="custom_category">Custom Category:</label>
            <input type="text" name="custom_category" value="<?php echo isset($_GET['custom_category']) ? htmlspecialchars($_GET['custom_category']) : ''; ?>">
        </div>
        <button type="submit">Filter</button>
    </form>

    <script>
        // JavaScript to toggle custom category input
        document.querySelector('select[name="category"]').addEventListener('change', function() {
            if (this.value === 'other') {
                document.getElementById('custom-category').style.display = 'block';
            } else {
                document.getElementById('custom-category').style.display = 'none';
            }
        });
    </script>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <!-- Display Current Image -->
                    <img src="<?php echo $row['ProductImage']; ?>" alt="<?php echo $row['ProductName']; ?>">

                    <h3><?php echo $row['ProductName']; ?></h3>
                    <p><?php echo $row['ProductDetails']; ?></p>
                    <p>৳ <?php echo $row['Price']; ?></p>
                    <p>Availability: <?php echo $row['Availability'] == 'YES' ? 'Yes' : 'No'; ?></p>
                    <p>Category: <?php echo htmlspecialchars($row['Category']); ?></p>

                    <div class="btn">
                        <!-- Edit Button -->
                        <button class="edit-button"><a href="update_form.php?product_id=<?php echo $row['ProductID']; ?>" >Edit</a></button>

                        <!-- delete button -->
                        <form method="POST" action="delete_product.php">
                            <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                            <button class="delete-button" type="submit" onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</button>
                        </form>
      
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // JavaScript to toggle custom category input for each product
    document.querySelectorAll('#category-update-select').forEach(function(selectElement) {
        selectElement.addEventListener('change', function() {
            const customCategoryDiv = this.parentElement.querySelector('.update-custom-category');
            if (this.value === 'other') {
                customCategoryDiv.style.display = 'block';
            } else {
                customCategoryDiv.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
