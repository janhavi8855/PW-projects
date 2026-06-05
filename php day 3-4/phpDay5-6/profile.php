<?php

session_start();

if(!isset($_SESSION['username']))
{
    echo "Please Login First";
    exit();
}

?>

<h2>Welcome <?php echo $_SESSION['username']; ?></h2>

<a href="logout.php">Logout</a>