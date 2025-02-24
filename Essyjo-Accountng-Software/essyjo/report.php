<?php
session_start();
include 'db_connection.php'; // Ensure database connection

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total sales and revenue
$totalSalesQuery = "SELECT COUNT(id) AS total_sales, SUM(total_price) AS total_revenue FROM sales";
$totalSalesResult = $conn->query($totalSalesQuery);
$totalSales = $totalSalesResult->fetch_assoc();

// Fetch top customers
$topCustomersQuery = "SELECT customer_name, COUNT(id) AS orders, SUM(total_price) AS total_spent 
                      FROM sales GROUP BY customer_name ORDER BY total_spent DESC LIMIT 5";
$topCustomersResult = $conn->query($topCustomersQuery);

// Fetch best-selling products
$bestSellingQuery = "SELECT product_name, SUM(quantity) AS total_sold 
                     FROM sales GROUP BY product_name ORDER BY total_sold DESC LIMIT 5";
$bestSellingResult = $conn->query($bestSellingQuery);

// Fetch least-selling products
$leastSellingQuery = "SELECT product_name, SUM(quantity) AS total_sold 
                      FROM sales GROUP BY product_name ORDER BY total_sold ASC LIMIT 5";
$leastSellingResult = $conn->query($leastSellingQuery);

// Payment status summary
$paymentQuery = "SELECT 
                    SUM(CASE WHEN paid = 1 THEN total_price ELSE 0 END) AS paid_amount,
                    SUM(CASE WHEN paid = 0 THEN total_price ELSE 0 END) AS unpaid_amount
                 FROM sales";
$paymentResult = $conn->query($paymentQuery);
$paymentStatus = $paymentResult->fetch_assoc();

// Export report to CSV
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=sales_report.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Customer Name', 'Product Name', 'Quantity', 'Total Price', 'Paid']);

    $salesQuery = "SELECT customer_name, product_name, quantity, total_price, paid FROM sales";
    $salesResult = $conn->query($salesQuery);
    while ($row = $salesResult->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgb(8, 110, 68);
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
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            margin-top: 20px;
            color: rgb(8, 110, 68);
        }

        h2 {
            border-bottom: 2px solid rgb(8, 110, 68);
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            background: rgb(8, 110, 68);
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background: #555;
        }

        .stats-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .stat {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            width: 30%;
        }

        .paid {
            color: green;
        }

        .unpaid {
            color: red;
        }

        /* Footer */
        footer {
            background-color: rgb(8, 110, 68);
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar" style="font-size: 16px;">
        <h1>ESSYJO Management System</h1>
        <ul>
        <li><a href="<?= isset($_SESSION['email']) ? 'dashboardd.php' : 'dashboardd.php' ?>">Home</a></li>
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

    <div class="container">
        <h2>Sales Report</h2>

        <div class="stats-box">
            <div class="stat">
                <h3>Total Sales</h3>
                <p><?= $totalSales['total_sales'] ?? 0 ?> transactions</p>
            </div>
            <div class="stat">
                <h3>Total Revenue</h3>
                <p>₦<?= number_format($totalSales['total_revenue'] ?? 0, 2) ?></p>
            </div>
            <div class="stat">
                <h3>Payment Status</h3>
                <p class="paid">Paid: ₦<?= number_format($paymentStatus['paid_amount'] ?? 0, 2) ?></p>
                <p class="unpaid">Unpaid: ₦<?= number_format($paymentStatus['unpaid_amount'] ?? 0, 2) ?></p>
            </div>
        </div>

        <h3>Top 5 Customers</h3>
        <table>
            <tr>
                <th>Customer</th>
                <th>Orders</th>
                <th>Total Spent</th>
            </tr>
            <?php while ($row = $topCustomersResult->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= $row['orders'] ?></td>
                    <td>₦<?= number_format($row['total_spent'], 2) ?></td>
                </tr>
            <?php } ?>
        </table>

        <h3>Best-Selling Products</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
            </tr>
            <?php while ($row = $bestSellingResult->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['total_sold'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <h3>Least-Selling Products</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
            </tr>
            <?php while ($row = $leastSellingResult->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['total_sold'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <form method="POST">
            <button type="submit" name="export_csv" class="btn">Download CSV Report</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 ESSYJO Management System. All rights reserved.</p>
    </footer>
</body>

</html>

<?php $conn->close(); ?>
