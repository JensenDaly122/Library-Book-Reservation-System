<?php
include 'db_connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
  
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile = $_POST['mobile'];
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $surname = $conn->real_escape_string($_POST['surname']);
    $addr1 = $conn->real_escape_string($_POST['addr1']);
    $city = $conn->real_escape_string($_POST['city']);

   
    if (empty($username) || empty($password) || empty($confirm_password) || empty($mobile) || empty($firstName) || empty($surname) || empty($addr1) || empty($city)) 
        {
        $error_message = "All details must be entered.";
    }
    else if (!ctype_digit($mobile) || strlen($mobile) !== 10) {
        $error_message = "Mobile phone number must be numeric and 10 characters long.";
    }
    else if (strlen($password) !== 6) 
        {
        $error_message = "Password must be exactly six characters long.";
    }
    else if ($password !== $confirm_password) {
        $error_message = "Password and confirmation do not match.";
    }
    else {
        
        $check_sql = "SELECT Username FROM users WHERE Username = '$username'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) 
{
            $error_message = "Error: Username already exists. Please choose a unique username.";
        } 
else {
       
            $plain_password = $conn->real_escape_string($password); 

         
            $sql = "INSERT INTO users (Username, Password, FirstName, Surname, AddressLine1, City, Mobile) 
                    VALUES ('$username', '$plain_password', '$firstName', '$surname', '$addr1', '$city', '$mobile')"; 
            
            if ($conn->query($sql) === TRUE) 
{
                header("Location: login.php?registration_success=1");
                exit;
            } 
else {
                $error_message = "Error inserting user: " . $conn->error;
            }
        }
    }
}

include 'header.php';
?>
<h3>User Registration</h3>
<?php if (!empty($error_message)) echo "<p class='error'>$error_message</p>"; ?>
<form method="post" action="register.php">
    <label>Username (Unique):</label> <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"><br>
    
    <label>First Name:</label> <input type="text" name="firstName" required value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>"><br>
    <label>Surname:</label> <input type="text" name="surname" required value="<?php echo isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : ''; ?>"><br>
    
    <label>Address Line 1:</label> <input type="text" name="addr1" required value="<?php echo isset($_POST['addr1']) ? htmlspecialchars($_POST['addr1']) : ''; ?>"><br>
    <label>City:</label> <input type="text" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>"><br>
    
    <label>Mobile (10 Digits):</label> <input type="text" name="mobile" required value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>"><br>
    
    <label>Password (6 Chars):</label> <input type="password" name="password" required><br>
    <label>Confirm Password:</label> <input type="password" name="confirm_password" required><br>
    
    <input type="submit" value="Register">
</form>
<?php
include 'footer.php';
?>