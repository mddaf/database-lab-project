<?php
session_start(); // Start the session at the very top
include 'db_connection.php'; // Include your database connection file

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your orders.";
    exit();
}

$username = $_SESSION['username']; // Get the logged-in username

// Query to fetch cart items
$detailsSql = "SELECT * FROM cart_items WHERE Username = '$username'";
$detailsResult = $conn->query($detailsSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navigation Bar */
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

        /* Main Content */
        .main {
            padding: 80px 20px 20px; /* Add top padding to account for fixed navbar */
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }

        /* Buttons and Links */
        .action-links a, .continue-shopping, .checkout-button {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }

        .continue-shopping, .checkout-button {
            background-color: #4CAF50;
        }
        .continue-shopping:hover, .checkout-button:hover {
            background-color: #45a049;
        }

        .action-links a:hover {
            background-color: #c82333;
        }

        .remove-link {
            background-color: #dc3545;
        }

        /* Form Styles */
        form {
            background-color: white;
            border-radius: 5px;
        }
        form h3 {
            margin-top: 0;
        }
        textarea, input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .payment-methods {
            margin-bottom: 20px;
        }
        .payment-methods label {
            margin-right: 20px;
        }
        .checkout-button {
            width: 100%;
            text-align: center;
        }

        /* Mobile Banking Details */
        #mobile-banking-details {
            display: none;
        }

        .no-orders-box {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh; /* Adjusted for the navbar */
        }

        .no-orders {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }

        .no-orders a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #45a049;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .no-orders a:hover {
            background-color: #3a8b41;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div><a href="index.php">AutoMob</a></div>
        <div><a href="#">Account</a></div>
        <div><a href="my_orders.php">My Orders</a></div>
        <div><a href="logout.php">Log out</a></div>
    </div>

    <div class="main">
        <?php
        if ($detailsResult->num_rows > 0) {
            echo "<h2>Your Cart</h2>";
            echo "<table>
                    <tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Action</th></tr>";
            $totalPrice = 0;

            while ($detail = $detailsResult->fetch_assoc()) {
                $productID = $detail['ProductID'];
                $quantity = $detail['Quantity'];

                // Fetch ProductName and Price from the product table based on ProductID
                $productSql = "SELECT ProductName, Price FROM product WHERE ProductID = $productID";
                $productResult = $conn->query($productSql);

                if ($productResult->num_rows > 0) {
                    $product = $productResult->fetch_assoc();
                    $productName = $product['ProductName'];
                    $price = $product['Price'];
                    $itemTotalPrice = $price * $quantity;
                    $totalPrice += $itemTotalPrice;

                    echo "<tr>
                            <td>{$productName}</td>
                            <td>{$quantity}</td>
                            <td>৳ {$itemTotalPrice}/-</td>
                            <td class='action-links'><a href='remove_from_cart.php?product_id={$productID}' class='remove-link'>Remove</a></td>
                        </tr>";
                }
            }

            echo "<tr>
                    <td colspan='2'><strong>Total</strong></td>
                    <td><strong>৳ {$totalPrice}/-</strong></td>
                    <td></td>
                </tr>";
            echo "</table>";
            echo "<a href='index.php' class='continue-shopping'>Continue Shopping</a>";

            // Checkout form
            echo "<form method='POST' action='checkout.php'>
                    <h3>Shipping Address:</h3>
                    <textarea name='shipping_address' required></textarea>
                    
                    <h3>Payment Options:</h3>
                    <div class='payment-methods'>
                        <input type='radio' name='payment_method' value='Cash On Delivery' id='cod' checked> 
                        <label for='cod'>Cash on Delivery</label>
    
                        <input type='radio' name='payment_method' value='Mobile Banking' id='mobile_banking'> 
                        <label for='mobile_banking'>Mobile Banking</label>
                    </div>
    
                    <div id='mobile-banking-details'>
                        <label for='banking_option'>Select Banking Option:</label>
                        <select name='banking_option' id='banking_option'>
                            <option value='Bkash'>Bkash</option>
                            <option value='Nagad'>Nagad</option>
                            <option value='Rocket'>Rocket</option>
                        </select>
    
                        <label for='account_number'>Account Number:</label>
                        <input type='text' name='account_number' id='account_number'>
    
                        <label for='transaction_id'>Transaction ID:</label>
                        <input type='text' name='transaction_id' id='transaction_id'>
                    </div>
    
                    <button type='submit' class='checkout-button'>Checkout</button>
                  </form>";
        } else {
            echo "<div class='no-orders-box'>
                    <div class='no-orders'>
                        <h2>Your Cart is Empty</h2>
                        <a href='index.php'>Continue Shopping</a>
                    </div>
                  </div>";
        }

        $conn->close();
        ?>
    </div>

    <script>
        // JavaScript to toggle the mobile banking details
        document.getElementById('mobile_banking').addEventListener('change', function() {
            document.getElementById('mobile-banking-details').style.display = 'block';
        });
        document.getElementById('cod').addEventListener('change', function() {
            document.getElementById('mobile-banking-details').style.display = 'none';
        });
    </script>

</body>
</html>
