<?php
include "connect.php";

if(isset($_POST['register']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO users
            (name,email,password,role)
            VALUES
            ('$name','$email',
             '$hashedPassword','User')";

    if(mysqli_query($conn,$sql))
    {
        echo "Registration Successful";
    }
    else
    {
        echo mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
</head>
<body>

<h2>Registration Form</h2>

<form method="POST">

Name:
<input type="text"
name="name"
required>

<br><br>

Email:
<input type="email"
name="email"
required>

<br><br>

Password:
<input type="password"
name="password"
required>

<br><br>

<input
type="submit"
name="register"
value="Register">

</form>

<br>

<a href="login.php">
Login Here
</a>

</body>
</html>