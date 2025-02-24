<?php
session_start();
require_once "db_connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $price = floatval($_POST["price"]);
    $quantity_in_packs = intval($_POST["quantity_in_packs"]);

    if (!empty($name) && $price > 0 && $quantity_in_packs > 0) {
        $insert_product_query = "INSERT INTO products (name, price, quantity_in_packs) 
                                 VALUES ('$name', '$price', '$quantity_in_packs')";
        if (mysqli_query($conn, $insert_product_query)) {
            echo "<script>alert('Product added successfully');</script>";
        } else {
            echo "<script>alert('Error adding product');</script>";
        }
    } else {
        echo "<script>alert('Invalid product details');</script>";
    }
}

// Fetch all orders
$orders_query = "SELECT * FROM sales";
$orders_result = mysqli_query($conn, $orders_query);

// Fetch all products
$products_query = "SELECT * FROM products";
$products_result = mysqli_query($conn, $products_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Essyjo Pharma Clinic</title>
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

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        .logout {
            text-align: right;
            margin-bottom: 10px;
        }

        .logout a {
            text-decoration: none;
            background: rgb(8, 110, 68);
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .form-container {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .form-container input {
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
        }

        .form-container button {
            background: rgb(8, 110, 68);
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
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
        <h2>Manage Orders</h2>

        <!-- Orders List -->
        <h3>Orders List</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td><?php echo $order['product_name']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['paid'] ? "Paid" : "Pending"; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Add Product Form -->
        <div class="form-container">
            <h3>Add New Product</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="number" name="quantity_in_packs" placeholder="Stock Quantity" required>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <!-- Product List -->
        <h3>Product List</h3>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Actions</th>
            </tr>
            <?php while ($product = mysqli_fetch_assoc($products_result)) { ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['quantity_in_packs']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </div>
</body>

</html>
