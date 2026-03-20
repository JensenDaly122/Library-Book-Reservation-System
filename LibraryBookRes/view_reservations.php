<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) { 
    header("Location: login.php?message=Please log in to view reservations.");
    exit; 
}

$username = $_SESSION['username'];
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

$sql = "SELECT B.BookTitle, B.Author, R.ISBN, R.ReservedDate
        FROM reservations R
        JOIN book B ON R.ISBN = B.ISBN
        WHERE R.Username = '$username'
        ORDER BY R.ReservedDate DESC";

$reserved_result = $conn->query($sql);

include 'header.php';
?>

<h2>Your Reserved Books</h2>
<?php if (!empty($message)) echo "<p class='" . (strpos($message, 'success') !== false ? 'success' : 'error') . "'>$message</p>"; ?>

<?php if ($reserved_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Book Title</th>
            <th>Author</th>
            <th>Reserved Date</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $reserved_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['BookTitle']; ?></td>
            <td><?php echo $row['Author']; ?></td>
            <td><?php echo $row['ReservedDate']; ?></td>
            <td>
                <a href="remove_reservation.php?isbn=<?php echo $row['ISBN']; ?>" 
                   onclick="return confirm('Are you sure you want to remove this reservation?');">
                    Remove Reservation
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>You currently have no books reserved.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>