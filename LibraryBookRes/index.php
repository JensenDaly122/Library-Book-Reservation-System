<?php
include 'header.php';
?>
    <h2>Welcome to the Library Book Reservation System</h2>
    <p>This system allows registered users to search for library books and reserve them for later use.</p>
    
    <?php if (!isset($_SESSION['username'])): ?>
        <p>Please <a href="login.php">log in</a> or <a href="register.php">register</a> to start using the system functionality.</p>
    <?php else: ?>
        <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. Use the navigation bar above to:</p>
        <ul>
            <li><a href="search.php">Search & Reserve a Book</a></li>
            <li><a href="view_reservations.php">View Your Reserved Books</a></li>
        </ul>
    <?php endif; ?>
<?php
include 'footer.php';
?>