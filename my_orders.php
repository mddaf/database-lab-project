<?php
session_start();
include 'db_connection.php'; 

if (!isset($_SESSION['username'])) {
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
            <p>You must be logged in to view your orders.</p>
            <a href="login.php">Login</a>
        </div>
    </body>
    </html>
    ';
    exit();
}

$username = $_SESSION['username'];

// Fetch orders from the orders table
$sql = "SELECT * FROM orders WHERE Username = '$username'";
$orderResult = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .navbar {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
        }

        .navbar a {
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }

        .navbar a.logo {
            font-size: 24px;
            font-weight: bold;
        }

        h2 {
            color: #333;
            margin: 20px 0;
            text-align: center;
        }

        .order {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .order h3 {
            color: #444;
        }

        .order p {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }

        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 16px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        hr {
            border: 0;
            height: 1px;
            background-color: #ddd;
            margin: 40px 0;
        }

        a {
            color: #007BFF;
            text-decoration: none;
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
        <div><a href="index.php" class="logo">AutoMob</a></div>
        <div>
            <a href="logout.php">Log Out</a>
            <a href="cart.php">Cart</a>
            <a href="#">Account</a>
        </div>
    </div>

    <?php
    if ($orderResult->num_rows > 0) {
        echo "<h2>Your Orders</h2>";
        while ($order = $orderResult->fetch_assoc()) {
            $orderID = $order['OrderID'];
            $orderDate = $order['OrderDate'];
            $shippingAddress = $order['ShippingAddress'];
            $paymentType = $order['PaymentType'];

            echo "<div class='order'>
                    <h3>Order ID: $orderID</h3>
                    <p>Order Date: $orderDate</p>
                    <p>Shipping Address: $shippingAddress</p>
                    <p>Payment Type: $paymentType</p>";

            // Check if payment type is mobile banking and fetch details if so
            if ($paymentType == 'Mobile Banking') {
                $paymentSql = "SELECT BankingOption FROM payment WHERE OrderID = $orderID";
                $paymentResult = $conn->query($paymentSql);

                if ($paymentResult->num_rows > 0) {
                    $paymentDetails = $paymentResult->fetch_assoc();
                    $bankingOption = $paymentDetails['BankingOption'];
                    echo "<p>Banking Option: $bankingOption</p>";
                }
            }

            // Fetch order details from the order_details table
            $detailsSql = "SELECT ProductID, Quantity FROM order_details WHERE OrderID = $orderID";
            $detailsResult = $conn->query($detailsSql);

            if ($detailsResult->num_rows > 0) {
                echo "<table>
                        <tr><th>Product Name</th><th>Quantity</th><th>Price</th></tr>";
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
                                <td>$productName</td>
                                <td>$quantity</td>
                                <td>৳ {$itemTotalPrice}/-</td>
                            </tr>";
                    }
                }

                echo "<tr>
                        <td colspan='2'>Total</td>
                        <td>৳ {$totalPrice}/-</td>
                    </tr>";
                echo "</table>";
            } else {
                echo "<p>No details found for this order.</p>";
            }

            echo "</div><hr>";
        }
    } else {
        echo "<div class='no-orders-box'>
                <div class='no-orders'>
                    <h2>Your have no Orders.</h2>
                    <a href='index.php'>Continue Shopping</a>
                </div>
              </div>";
    }


    $conn->close();
    ?>

</body>
</html>
