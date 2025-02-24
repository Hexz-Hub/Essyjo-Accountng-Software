<?php
require_once "db_connection.php";

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $query = "UPDATE customers SET approved = 0 WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_users.php?success=User Disapproved");
    } else {
        header("Location: manage_users.php?error=Disapproval Failed");
    }
}
?>
