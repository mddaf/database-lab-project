<?php
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type'])) {
    echo "You must be logged in to checkout.";
    exit();
}

$user = $_SESSION['username'];

// Check if the cart is empty
$sql = "SELECT * FROM cart_items WHERE Username = '$user'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<h2>Your Cart is Empty</h2>";
    echo "<a href='index.php'>Continue Shopping</a>";
    exit();
}

// Get form data for shipping address and payment method
$shippingAddress = $_POST['shipping_address'];
$paymentType = $_POST['payment_method']; // 'cod' or 'mobile_banking'
$bankingOption = isset($_POST['banking_option']) ? $_POST['banking_option'] : null;
$accountNumber = isset($_POST['account_number']) ? $_POST['account_number'] : null;
$transactionId = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;

// Insert order data into the orders table
$orderSql = "INSERT INTO orders (Username, OrderDate, ShippingAddress, PaymentType) 
             VALUES ('$user', NOW(), '$shippingAddress', '$paymentType')";

if ($conn->query($orderSql) === TRUE) {
    $orderID = $conn->insert_id; // Get the last inserted OrderID

    // Fetch cart items directly from the database
    $cartSql = "SELECT ProductID, Quantity FROM cart_items WHERE Username = '$user'";
    $cartResult = $conn->query($cartSql);

    if ($cartResult->num_rows > 0) {
        $values = []; // Initialize an array to hold the values for the multi-row insert

        while ($row = $cartResult->fetch_assoc()) {
            $productId = $row['ProductID'];
            $quantity = $row['Quantity'];
            $values[] = "($orderID, $quantity, $productId)";
        }

        // Prepare the multi-row insert query for order_details
        $detailsSql = "INSERT INTO order_details (OrderID, Quantity, ProductID) VALUES " . implode(',', $values);

        if ($conn->query($detailsSql) !== TRUE) {
            echo "Error inserting order details: " . $conn->error;
        }
    } else {
        echo "No cart items found.";
    }

    if ($paymentType === 'Mobile Banking'){
        $paymentSql = "INSERT INTO payment (OrderID, AccountNumber, TransactionID, BankingOption) 
                        VALUES ($orderID, '$accountNumber', '$transactionId', '$bankingOption')";
        $conn->query($paymentSql);
    } else{
        $paymentSql = "INSERT INTO payment (OrderID) 
                        VALUES ($orderID)";
        $conn->query($paymentSql);
    }

    // Insert into payment table


    // Display the success message
    echo "<!DOCTYPE html>
          <html lang='en'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Checkout Confirmation</title>
              <style>
                  body {
                      font-family: Arial, sans-serif;
                      margin: 0;
                      padding: 0;
                      background-color: #f4f4f4;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      height: 100vh;
                  }
                  .checkout-container {
                      background-color: #fff;
                      border-radius: 8px;
                      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                      max-width: 600px;
                      width: 100%;
                      padding: 20px;
                      text-align: center;
                  }
                  h2 {
                      color: #28a745;
                      font-size: 24px;
                      margin-bottom: 20px;
                  }
                  p {
                      font-size: 16px;
                      color: #555;
                      margin-bottom: 20px;
                  }
                  a {
                      display: inline-block;
                      text-decoration: none;
                      color: #45a049;
                      border: 1px solid #45a049;
                      padding: 10px 20px;
                      border-radius: 5px;
                      transition: background-color 0.3s, color ease;
                  }
                  a:hover {
                    background-color: #45a049;
                    color: #ffffff;
                  }
              </style>
          </head>
          <body>
              <div class='checkout-container'>
                  <h2>Checkout Successful!</h2>
                  <p>Thank you for your order. You can view your order details by clicking the button below.</p>
                  <a href='my_orders.php'>View My Orders</a>
              </div>
          </body>
          </html>";

    // Clear the cart items from the database
    $conn->query("DELETE FROM cart_items WHERE Username = '$user'");

    // Clear the cart session as well
    unset($_SESSION['cart']);
} else {
    echo "Error: " . $orderSql . "<br>" . $conn->error;
}

$conn->close();
?>
