<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php?message=" . urlencode("You have been successfully logged out."));
exit;
?>