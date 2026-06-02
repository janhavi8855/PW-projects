<?php
session_start();

if(isset($_POST['username']))
{
    $_SESSION['username'] = $_POST['username'];

    header("Location: step2welcome.php");
    exit();
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

    <br><br>

    <input type="submit" value="Login">

</form>

</body>
</html>