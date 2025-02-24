
<?php
session_start();
// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Store user information in the session
            $_SESSION['email'] = $user['email'];
            $_SESSION['fullname'] = $user['fullname']; // Example: store additional info if needed
            header("Location: dashboardd.php"); // Redirect to dashboard
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No user found with that email address.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .container button {
            width: 100%;
            padding: 10px;
            background: rgb(8,110,68);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .container button:hover {
            background: rgb(8,110,68);
        }

        .container a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: rgb(8,110,68);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Customer Login</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>