<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 300px;
            /* text-align: center; */
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center">Login</h2>
        <form method="POST" action="login_handler.php">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <select name="user_type" required>
                <option value="customer">Customer</option>
                <option value="seller">Seller</option>
            </select><br>
            <button type="submit">Login</button>
        </form>

        <a href="signup.php" style="text-align: center">Don't have an account? Sign up here</a>
    </div>
</body>
</html>
