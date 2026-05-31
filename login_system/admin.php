<?php
session_start();

if(!isset($_SESSION['username']))
{
    die("Please Login First");
}
?>

<!DOCTYPE html>
<html>
<body>

<h1>Admin Panel</h1>

<h2>Welcome <?php echo $_SESSION['username']; ?></h2>

<a href="logout.php">Logout</a>

</body>
</html>