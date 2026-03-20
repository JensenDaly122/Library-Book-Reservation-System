<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['isbn'])) {
    header("Location: search.php");
    exit;
}

$username = $conn->real_escape_string($_SESSION['username']);
$isbn = $conn->real_escape_string($_GET['isbn']);
$date = date("Y-m-d");

$check_sql = "SELECT * FROM book WHERE ISBN = '$isbn'";
$check_res = $conn->query($check_sql);

if (!$check_res) {
    die("Error checking book status");
}

if ($check_res->num_rows > 0) {
    $book = $check_res->fetch_assoc();
    $flag = isset($book['Reserve']) ? $book['Reserve'] : 'N';
    
    if (trim(strtoupper($flag)) === 'Y') {
        echo "<script>alert('Book is already reserved'); window.location='search.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Book not found'); window.location='search.php';</script>";
    exit;
}

$conn->begin_transaction();
$success = true;

$sql_res = "INSERT INTO reservations (ISBN, Username, ReservedDate) VALUES ('$isbn', '$username', '$date')";
if (!$conn->query($sql_res)) {
    $success = false;
}

$sql_book = "UPDATE book SET Reserve = 'Y' WHERE ISBN = '$isbn'";
if (!$conn->query($sql_book)) {
    $success = false;
}

if ($success) {
    $conn->commit();
    header("Location: view_reservations.php");
    exit;
} else {
    $conn->rollback();
    echo "Error processing reservation";
}

$conn->close();
?>