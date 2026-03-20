<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) 
{ 
    header("Location: login.php");
    exit; 
}

$username = $conn->real_escape_string($_SESSION['username']);
$isbn = isset($_GET['isbn']) ? $conn->real_escape_string($_GET['isbn']) : '';
$redirect_message = "Reservation removal failed.";

if (!empty($isbn)) 
{
    $conn->begin_transaction();
    $success = true;
    
    try {
       
        $delete_sql = "DELETE FROM reservations WHERE ISBN = '$isbn' AND Username = '$username'";
        if (!$conn->query($delete_sql)) {
            $success = false;
        }

 
        $update_sql = "UPDATE book SET Reserve = 'N' WHERE ISBN = '$isbn'";
        if (!$conn->query($update_sql)) 
{
            $success = false;
        }

        if ($success) 
{
            $conn->commit();
            $redirect_message = "Reservation removed successfully.";
        } 
else {
            $conn->rollback();
            $redirect_message = "Reservation removal failed due to a database issue.";
        }

    } catch (Exception $e) 
{
        $conn->rollback();
        $redirect_message = "Reservation removal failed due to system error.";
    }
} 
else {
    $redirect_message = "Error: Invalid book identifier.";
}

$conn->close();

header("Location: view_reservations.php?message=" . urlencode($redirect_message));
exit;
?>