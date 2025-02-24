<?php
session_start();
require_once "db_connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all users
$users_query = "SELECT id, fullname, email, phone_number FROM customers";
$users_result = mysqli_query($conn, $users_query) or die(mysqli_error($conn));

// Fetch all orders
$orders_query = "SELECT id, customer_name, product_name, quantity, paid FROM sales";
$orders_result = mysqli_query($conn, $orders_query) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Essyjo Pharma Clinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        nav {
            background: #086E44;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .logout {
            text-align: right;
            margin-bottom: 10px;
        }

        .logout a {
            text-decoration: none;
            background: #086E44;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-unpaid {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_orders.php">Manage Orders</a>

    </nav>

    <div class="container">
        <div class="logout">
            <a href="admin_logout.php">Logout</a>
        </div>
        <h2>Admin Dashboard</h2>

        <h3>Users List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <h3>Orders List</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                    <td>
                        <?php if ($order['paid']) { ?>
                            <span class="status-paid">Paid</span>
                        <?php } else { ?>
                            <span class="status-unpaid">Unpaid</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="update_order.php?id=<?php echo $order['id']; ?>">Update</a> |
                        <a href="delete_order.php?id=<?php echo $order['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>

</html>