<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "automob"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters from the request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// SQL base query
$sql = "SELECT ProductID, ProductName, ProductDetails, Category, Price, Availability, ProductImage FROM product WHERE 1";

// Apply search filter
if ($search != '') {
    $sql .= " AND ProductName LIKE '%" . $conn->real_escape_string($search) . "%'";
}

// Apply category filter
if ($category != '') {
    $sql .= " AND Category = '" . $conn->real_escape_string($category) . "'";
}

// Apply sorting
if ($sort == 'price-asc') {
    $sql .= " ORDER BY Availability DESC, Price ASC";
} elseif ($sort == 'price-desc') {
    $sql .= " ORDER BY Availability DESC, Price DESC";
} elseif ($sort == 'in-stock') {
    $sql .= " ORDER BY Availability DESC"; // In Stock first
} elseif ($sort == 'out-of-stock') {
    $sql .= " ORDER BY Availability ASC"; // Out of Stock first
}

$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch categories for the filter dropdown
$categoryQuery = "SELECT DISTINCT Category FROM product";
$categoryResult = $conn->query($categoryQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Home Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #333;
            color: white;
            padding: 20px 40px;
            text-align: right;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar div {
            display: inline-block;
            margin-right: 20px;
            font-size: 16px;
        }

        .navbar div:first-child {
            float: left;
            margin-left: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .main {
            padding: 40px 20px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-section form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #search-bar, #category-dropdown, #sort-dropdown {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        #product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 23%;
            box-sizing: border-box;
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 200px;
            height: 200px;
            border-radius: 5px;
        }

        .product-card h4 {
            font-size: 16px;
            margin: 10px 0;
        }

        .price {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 14px;
            margin: 5px 0;
        }

        form {
            margin-top: 10px;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-left: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button[type="submit"] {
            padding: 8px 16px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        button[type="submit"]:disabled {
            background-color: #bbb;
            cursor: not-allowed;
        }

        button[type="submit"]:hover:not(:disabled) {
            background-color: #555;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div><a href="index.php" style="color: white;">AutoMob</a></div>

        <?php
        session_start();
        if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
            <div>Account</div>
            <div><a href="my_orders.php" style="color: white;">My Orders</a></div>
            <div><a href="cart.php" style="color: white;">Cart</a></div>
            <div><a href="logout.php" style="color: white;">Log out</a></div>
        <?php else: ?>
            <div><a href="login.php" style="color: white;">Log in</a></div>
        <?php endif; ?>
    </div>

    <!-- Main Section -->
    <div class="main">
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" id="filter-form">
                <input style="width:465px" type="text" name="search" placeholder="Search..." id="search-bar" value="<?php echo htmlspecialchars($search); ?>">
                <select name="category" id="category-dropdown" style="width:310px">
                    <option value="">All Categories</option>
                    <?php while ($catRow = $categoryResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($catRow['Category']); ?>" <?php if ($category == $catRow['Category']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($catRow['Category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select name="sort" id="sort-dropdown" style="width:310px">
                    <option value="price-asc" <?php if ($sort == 'price-asc') echo 'selected'; ?>>Price: Low to High</option>
                    <option value="price-desc" <?php if ($sort == 'price-desc') echo 'selected'; ?>>Price: High to Low</option>
                    <option value="in-stock" <?php if ($sort == 'in-stock') echo 'selected'; ?>>In Stock First</option>
                    <option value="out-of-stock" <?php if ($sort == 'out-of-stock') echo 'selected'; ?>>Out of Stock First</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Product Cards -->
        <div id="product-container">
            <?php if (empty($products)): ?>
                <p>No products found</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        <h4><?php echo htmlspecialchars($product['ProductName']); ?></h4>
                        <h4 style='font-weight:400'><?php echo htmlspecialchars($product['ProductDetails']); ?></h4>
                        <p class="price">৳ <?php echo htmlspecialchars($product['Price']); ?></p>
                        <p><?php echo $product['Availability'] ? 'In Stock' : 'Out of Stock'; ?></p>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                            <label for="quantity-<?php echo $product['ProductID']; ?>">Quantity:</label>
                            <input type="number" id="quantity-<?php echo $product['ProductID']; ?>" name="quantity" min="1" max="99" value="1">
                            <button type="submit" <?php if (!$product['Availability']) echo 'disabled'; ?>>Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
