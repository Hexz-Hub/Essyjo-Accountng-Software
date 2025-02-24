<?php
session_start();
require_once "db_connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    $update_query = "UPDATE customers SET approved = 0 WHERE id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_users.php?success=User disapproved successfully");
        exit();
    } else {
        header("Location: manage_users.php?error=Failed to disapprove user");
        exit();
    }
}
?>
