<?php
// Start session for authentication
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESSYJO Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgb(8,110,68);
            padding: 10px 20px;
            color: white;
        }

        .navbar h1 {
            margin: 0;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            margin: 0 15px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
        }

        .navbar ul li a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #eef9f2;
            padding: 50px 20px;
            margin-bottom: 20px;
        }

        .hero-text {
            width: 50%;
        }

        .hero-text h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: rgb(8,110,68);
        }

        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .hero-text a {
            padding: 10px 20px;
            background-color: rgb(8,110,68);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .hero-text a:hover {
            background-color: white;
            color: rgb(8,110,68);
        }

        .hero-image {
            width: 45%;
        }

        .hero-image img {
            width: 100%;
            border-radius: 10px;
        }

        /* Modules Section */
        .modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .module {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .module h2 {
            color: rgb(8,110,68);
            margin-bottom: 15px;
        }

        .module p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .module a {
            padding: 10px 20px;
            background-color: rgb(8,110,68);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .module a:hover {
            background-color: black;
        }

        /* Footer */
        footer {
            background-color: rgb(8,110,68);
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar" style="font-size: 16px;">
        <h1>ESSYJO Management System</h1>
        <ul>
        <li><a href="<?= isset($_SESSION['email']) ? 'customer.php' : 'login.php' ?>">Customers</a></li>
            <li><a href="<?= isset($_SESSION['email']) ? 'sales.php' : 'login.php' ?>">Sales</a></li>
            <li><a href="<?= isset($_SESSION['email']) ? 'report.php' : 'login.php' ?>">Reports</a></li>
        </ul>
        <div class="user-info">
            <?php if (isset($_SESSION['email'])): ?>
                Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?> | 
                <a href="logout.php" style="color: white;">Logout</a>
            <?php else: ?>
                Guest | <a href="login.php" style="color: white;">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-text">
            <h1>Manage Your Pharmacy with Ease</h1>
            <p>ESSYJO Management System is designed to help you store and manage customer data, track sales transactions, calculate profit and loss, and generate insightful reports.</p>
            <a href="<?= isset($_SESSION['email']) ? 'get_started.php' : 'login.php' ?>">Get Started</a>
        </div>
        <div class="hero-image">
            <img src="./img/Essyjo.png" alt="essyjo">
        </div>
    </div>

    <!-- Modules Section -->
    <div class="modules">
        <div class="module">
            <h2>Customer Management</h2>
            <p>Register and manage customer profiles easily. Search, filter, and update customer details in no time.</p>
            <a href="<?= isset($_SESSION['email']) ? 'customer.php' : 'login.php' ?>">Manage Customers</a>
        </div>
        <div class="module">
            <h2>Sales Management</h2>
            <p>Record and track all sales transactions with ease. Generate reports for better decision-making.</p>
            <a href="<?= isset($_SESSION['email']) ? 'sales.php' : 'login.php' ?>">View Sales</a>
        </div>
        <div class="module">
            <h2>Financial Reports</h2>
            <p>Calculate profit and loss, generate balance sheets, and get financial insights at your fingertips.</p>
            <a href="<?= isset($_SESSION['email']) ? 'reports.php' : 'login.php' ?>">Generate Reports</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 ESSYJO Management System. All rights reserved.</p>
    </footer>

</body>
</html>
