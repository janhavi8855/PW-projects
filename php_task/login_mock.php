<?php
session_start();

if(isset($_POST['username']))
{
    $_SESSION['username'] = $_POST['username'];

    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login Form</h2>

<form method="POST">

Username:
<input type="text" name="username">

<button type="submit">Login</button>

</form>

</body>
</html>