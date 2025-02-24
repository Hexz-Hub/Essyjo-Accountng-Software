<?php
session_start();
include 'db_connection.php'; // Ensure this file contains the database connection code

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products for the dropdown
$products = $conn->query("SELECT id, name, price, quantity_in_packs FROM products");
$product_list = [];
if ($products->num_rows > 0) {
    while ($row = $products->fetch_assoc()) {
        $product_list[] = $row;
    }
}

// Add a new sale
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_sale'])) {
    $customer_name = $_POST['customer_name'] ?? null;
    $product_id = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $paid = 0; // Default value for 'paid' column

    if ($customer_name && $product_id && $quantity) {
        // Get product details
        $stmt = $conn->prepare("SELECT name, price, quantity_in_packs FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product && $product['quantity_in_packs'] >= $quantity) {
            $total_price = $product['price'] * $quantity;
            $product_name = $product['name'];

            // Deduct quantity from stock
            $stmt = $conn->prepare("UPDATE products SET quantity_in_packs = quantity_in_packs - ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
            $stmt->close();

            // Insert sale record
            $stmt = $conn->prepare("INSERT INTO sales (customer_name, product_name, quantity, total_price, paid) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidi", $customer_name, $product_name, $quantity, $total_price, $paid);
            if ($stmt->execute()) {
                echo "Sale added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Not enough stock available.";
        }
    } else {
        echo "All fields are required.";
    }
}

// Update payment status
if (isset($_GET['toggle_paid'])) {
    $id = $_GET['toggle_paid'];
    $stmt = $conn->prepare("UPDATE sales SET paid = NOT paid WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: sales.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Delete sale record
if (isset($_GET['delete_sale'])) {
    $id = $_GET['delete_sale'];
    $stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: sales.php");
        exit();
    } else {
        echo "Error deleting sale: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all sales
$sales = $conn->query("SELECT id, customer_name, product_name, quantity, total_price, paid FROM sales");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
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
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            color: rgb(8, 110, 68);
        }

        h2 {
            border-bottom: 2px solid rgb(8, 110, 68);
            padding-bottom: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn {
            background: rgb(8, 110, 68);
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #555;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .status-btn {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .paid {
            background: green;
        }

        .not-paid {
            background: red;
        }

        footer {
            background-color: rgb(8, 110, 68);
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar" style="font-size: 16px;">
        <h1>ESSYJO Management System</h1>
        <ul>
        <li><a href="<?= isset($_SESSION['email']) ? 'dashboardd.php' : 'login.php' ?>">Home</a></li>
            <li><a href="<?= isset($_SESSION['email']) ? 'customer.php' : 'login.php' ?>">Customers</a></li>
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
        <h2>Add New Sale</h2>
        <form method="POST" action="sales.php">
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="product_id">Product Name</label>
                <select id="product_id" name="product_id" required onchange="updateQuantity()">
                    <option value="">Select a Product</option>
                    <?php foreach ($product_list as $product) { ?>
                        <option value="<?= $product['id'] ?>" data-quantity="<?= $product['quantity_in_packs'] ?>" data-price="<?= $product['price'] ?>">
                            <?= $product['name'] ?> - <?= $product['quantity_in_packs'] ?> packs available (₦<?= $product['price'] ?> per pack)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required min="1">
            </div>
            <input type="hidden" name="add_sale" value="1">
            <button type="submit" class="btn">Add Sale</button>
        </form>

        <h2>Sales List</h2>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Paid</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $sales->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td>₦<?= htmlspecialchars($row['total_price']) ?></td>
                    <td><a href="sales.php?toggle_paid=<?= $row['id'] ?>" class="status-btn <?= $row['paid'] ? 'paid' : 'not-paid' ?>"></a></td>
                    <td>
                        <a href="sales.php?delete_sale=<?= $row['id'] ?>" class="btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <footer>
        <p>&copy; 2025 ESSYJO Management System. All rights reserved.</p>
    </footer>
</body>

</html>

<?php $conn->close(); ?>