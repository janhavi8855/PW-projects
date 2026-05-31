<?php
include 'db.php';

if(isset($_POST['register']))
{
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users_login(username,email,password)
              VALUES('$username','$email','$hashed_password')";

    if(mysqli_query($conn, $query))
    {
        echo "Registration Successful";
    }
    else
    {
        echo "Error";
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Register</h2>

<form method="POST">

Username:
<input type="text" name="username"><br><br>

Email:
<input type="email" name="email"><br><br>

Password:
<input type="password" name="password"><br><br>

<button type="submit" name="register">
Register
</button>

</form>

</body>
</html>