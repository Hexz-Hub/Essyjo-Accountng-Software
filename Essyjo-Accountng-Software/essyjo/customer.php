<?php
session_start();
$conn = new mysqli("localhost", "root", "", "essyjo_management");

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Handle customer addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $entity = $_POST['entity'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO customer_list (name, location, entity, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $entity, $created_at);
    $stmt->execute();
    header("Location: customer.php");
}


// Handle customer update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_customer'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $entity = $_POST['entity'];

    $stmt = $conn->prepare("UPDATE customer_list SET name=?, location=?, entity=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $location, $entity, $id);
    $stmt->execute();
    header("Location: customer.php");
}

// Handle customer deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM customer_list WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: customer.php");
}

// Fetch customers
$result = $conn->query("SELECT * FROM customer_list ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - ESSYJO</title>
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
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin-top: 20px;
        }
        h2 {
            color: rgb(8,110,68);
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background: rgb(8,110,68);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: white;
            color: rgb(8,110,68);
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
            background: rgb(8,110,68);
            color: white;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .edit-btn {
            background: orange;
            color: white;
        }
        .delete-btn {
            background: red;
            color: white;
        }
        #editForm {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    border-radius: 10px;
    z-index: 1000; /* Ensures it appears on top */
    width: 50%; /* Adjust width as needed */
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
    <h2>Add New Customer</h2>
    <form method="POST">
        <input type="hidden" name="add_customer" value="1">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Entity Type:</label>
        <select name="entity">
            <option value="Retail">Retail</option>
            <option value="Wholesale">Wholesale</option>
        </select>

        <button type="submit">Add Customer</button>
    </form>

    <h2>Customer List</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Location</th>
            <th>Entity</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['location']); ?></td>
            <td><?= htmlspecialchars($row['entity']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
            <td class="actions">
                <button class="edit-btn" onclick="editCustomer('<?= $row['id'] ?>', '<?= $row['name'] ?>', '<?= $row['location'] ?>', '<?= $row['entity'] ?>')">Edit</button>
                <a href="customer.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete-btn">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
</div>
 <!-- Footer -->
 <footer>
        <p>&copy; 2025 ESSYJO Management System. All rights reserved.</p>
    </footer>

<!-- Edit Form (Initially Hidden) -->
<div class="container" id="editForm" style="display: none;">
    <h2>Edit Customer</h2>
    <form method="POST">
        <input type="hidden" name="update_customer" value="1">
        <input type="hidden" id="editId" name="id">
        <label>Name:</label>
        <input type="text" id="editName" name="name" required>

        <label>Location:</label>
        <input type="text" id="editLocation" name="location" required>

        <label>Entity Type:</label>
        <select id="editEntity" name="entity">
            <option value="Retail">Retail</option>
            <option value="Wholesale">Wholesale</option>
        </select>

        <button type="submit">Update Customer</button>
    </form>
</div>

<script>
function editCustomer(id, name, location, entity) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editLocation').value = location;
    document.getElementById('editEntity').value = entity;
    document.getElementById('editForm').style.display = 'block';
}
</script>

</body>
</html>
