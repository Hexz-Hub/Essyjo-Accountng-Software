<?php
session_start();
require_once "db_connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all users
$users_query = "SELECT id, fullname, email, phone_number, approved FROM customers";
$users_result = mysqli_query($conn, $users_query) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Essyjo Pharma Clinic</title>
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
            margin-top: 20px;
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

        .approve-btn {
            background: green;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
        }

        .disapprove-btn {
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
        }

        .delete-btn {
            background: darkred;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            text-decoration: none;
            background: #086E44;
            color: white;
            padding: 10px;
            border-radius: 5px;
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
        <h2>Manage Users</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Approval Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    <?php if (isset($_GET['success'])) { ?>
                        <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
                    <?php } ?>

                    <?php if (isset($_GET['error'])) { ?>
                        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
                    <?php } ?>

                    <td>
                        <?php if (!$user['approved']) { ?>
                            <a href="approve_user.php?id=<?php echo $user['id']; ?>" class="approve-btn">Approve</a>
                        <?php } else { ?>
                            <a href="disapprove_user.php?id=<?php echo $user['id']; ?>" class="disapprove-btn">Disapprove</a>
                        <?php } ?>
                        |
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>

                </tr>
            <?php } ?>
        </table>

        <div class="back-btn">
            <a href="admin_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>

</html>