<?php
session_start();
require_once "db_connection.php";

$error = "";
$success = "";

// Handle admin registration
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the email already exists
    $check_query = "SELECT * FROM admins WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email already exists!";
    } else {
        $query = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $query)) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Failed to register. Try again.";
        }
    }
}

// Handle admin login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Essyjo Pharma Clinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: rgb(8, 110, 68);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: white;
            color: rgb(8, 110, 68);
        }
        .toggle {
            margin-top: 10px;
            color: blue;
            cursor: pointer;
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleForm() {
            document.getElementById("login-form").classList.toggle("hidden");
            document.getElementById("register-form").classList.toggle("hidden");
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Admin Portal</h2>
        
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

        <!-- Login Form -->
        <div id="login-form">
            <h3>Login</h3>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <p class="toggle" onclick="toggleForm()">New admin? Register here</p>
        </div>

        <!-- Registration Form -->
        <div id="register-form" class="hidden">
            <h3>Register</h3>
            <form action="" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register">Register</button>
            </form>
            <p class="toggle" onclick="toggleForm()">Already registered? Login here</p>
        </div>
    </div>
</body>
</html>