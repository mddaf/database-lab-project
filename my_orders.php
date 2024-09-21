
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
            width: 50%;
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


        
    </style>
</head>
<body>

    <!-- Navigation Bar -->

    <div class="navbar">
        <div> <a href="customer_home.php" class="logo">AutoMob</a></div>
        <div>
            
            
            <a href="logout.php">Log Out</a>
            <a href="cart.php">Cart</a>
            <a href="">Account</a>
            
        </div>
    </div>

</body>
</html>





<?php
session_start();
include 'db_connection.php'; 

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your orders.";
    exit();
}

$username = $_SESSION['username'];

// Fetch orders from the orders table
$sql = "SELECT * FROM orders WHERE Username = '$username'";
$orderResult = $conn->query($sql);

if ($orderResult->num_rows > 0) {
    echo "<h2>Your Orders</h2>";
    while ($order = $orderResult->fetch_assoc()) {
        $orderID = $order['OrderID'];
        $orderDate = $order['OrderDate'];
        // $totalPrice = $order['TotalPrice'];
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
        // $detailsSql = "SELECT ProductName, Quantity, TotalPrice FROM order_details WHERE OrderID = $orderID";
        // $detailsResult = $conn->query($detailsSql);

        // if ($detailsResult->num_rows > 0) {
        //     echo "<table border='1'>
        //             <tr><th>Product Name</th><th>Quantity</th><th>Price</th></tr>";
        //     $totalPrice = 0;
        //     while ($detail = $detailsResult->fetch_assoc()) {
        //         $productName = $detail['ProductName'];
        //         $quantity = $detail['Quantity'];
        //         $itemTotalPrice = $detail['TotalPrice'];
        //         $totalPrice += $itemTotalPrice;

        //         echo "<tr>
        //                 <td>$productName</td>
        //                 <td>$quantity</td>
        //                 <td>$$itemTotalPrice</td>
        //               </tr>";
        //     }
        //     echo "<tr>
        //     <td colspan='2'>Total</td>
        //     <td>\${$totalPrice}</td>
        //     <td></td>
        //   </tr>";
        //     echo "</table>";
        // } else {
        //     echo "<p>No details found for this order.</p>";
        // }

        // echo "</div><hr>";


        // Fetch order details from the order_details table
        $detailsSql = "SELECT ProductID, Quantity FROM order_details WHERE OrderID = $orderID";
        $detailsResult = $conn->query($detailsSql);

        if ($detailsResult->num_rows > 0) {
            echo "<table border='1'>
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
    echo "<h2>You have no orders.</h2>";
    echo "<a href='customer_home.php'>Continue Shopping</a>";
}

$conn->close();
?>
