<?php

include "connect.php";

if(isset($_POST['register']))
{
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password=$_POST['password'];

    $hashedPassword=password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql="INSERT INTO users(name,email,password)
          VALUES('$name','$email','$hashedPassword')";

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