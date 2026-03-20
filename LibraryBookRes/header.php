<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
$logged_in = isset($_SESSION['username']);
?>
<!DOCTYPE html>

<head>
    <title>Library Book Reservation System</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<div id="header">
    <h1> Library Reservation</h1>
    <div id="navigation">
        <a href="index.php">Home</a>
        <?php if ($logged_in): ?>
            <a href="search.php">Search & Reserve Book</a>
            <a href="view_reservations.php">View Reserved Books</a>
            <span style="color: yellow; margin-left: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div id="content">