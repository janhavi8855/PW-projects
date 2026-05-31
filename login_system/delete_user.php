<?php

include 'db.php';

$id = $_GET['id'];

$query = "DELETE FROM users_login WHERE id=$id";

mysqli_query($conn, $query);

header("Location: admin_dashboard.php");

?>