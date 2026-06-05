<!DOCTYPE html>
<html>
<body>

<form method="POST">

Email:
<input type="email" name="email"><br><br>

Password:
<input type="password" name="password"><br><br>

<input type="submit" name="login" value="Login">

</form>

</body>
</html>

<?php

include "connect.php";

if(isset($_POST['login']))
{
    $email=$_POST['email'];
    $password=$_POST['password'];

    $sql="SELECT * FROM users
          WHERE email='$email'
          AND password='$password'";

    $result=mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)>0)
    {
        header("Location: dashboard.php");
    }
    else
    {
        echo "Invalid Login";
    }
}

?>