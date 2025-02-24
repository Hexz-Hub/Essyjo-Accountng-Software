<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "essyjo_management");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $company = $_POST['company'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle file upload
    // $profile_picture = $_FILES['profile_picture'];
    // $target_dir = "uploads/";
    // $target_file = $target_dir . basename($profile_picture["name"]);
    // move_uploaded_file($profile_picture["tmp_name"], $target_file);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone_number, company, profile_picture, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $email, $phone_number, $company, $target_file, $password);

    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.php'>Login</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            color: #333;
        }

        .container label {
            display: block;
            margin-bottom: 5px;
            color: #555;
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
            background:rgb(8,110,68);
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
            color:rgb(8,110,68);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Customer Registration</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="fullname" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone Number:</label>
            <input type="text" name="phone_number" required>

            <label>Company/Entity:</label>
            <input type="text" name="company">

            <label>Profile Picture:</label>
            <input type="file" name="profile_picture" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already registered? Login here</a>
    </div>
</body>
</html>
