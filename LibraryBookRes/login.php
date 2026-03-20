<?php
include 'db_connect.php'; 
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    
    $username = $conn->real_escape_string($_POST['username']);
    
   
    $password = $_POST['password']; 

    $sql = "SELECT Username, Password FROM users WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['Password']) {
            session_start();
            $_SESSION['username'] = $username;
            header("Location: search.php");
            exit;
        } else {
           
            $error_message = "Invalid username or password.";
        }
    } else {
        
        $error_message = "Invalid username or password.";
    }
}

$success_message = isset($_GET['registration_success']) ? "Registration successful! Please log in." : "";

include 'header.php'; 
?>

    <h2>User Login</h2>

    <?php if (!empty($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <input type="submit" value="Login">
        
        <p style="margin-top: 15px;">New user? <a href="register.php">Register here</a>.</p>
    </form>

<?php 
include 'footer.php';
?>