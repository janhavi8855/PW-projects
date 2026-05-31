<?php
session_start();
?>

<!DOCTYPE html>
<html>
<body>

<h2>
Welcome
<?php echo $_SESSION['username']; ?>
</h2>

<a href="logout.php">Logout</a>

</body>
</html>