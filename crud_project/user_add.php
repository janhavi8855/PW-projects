<?php
include 'db.php';

if(isset($_POST['submit']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "INSERT INTO users(name,email,role)
              VALUES('$name','$email','$role')";

    mysqli_query($conn, $query);

    header("Location: user_list.php");
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Add User</h2>

<form method="POST">

Name:
<input type="text" name="name"><br><br>

Email:
<input type="email" name="email"><br><br>

Role:
<input type="text" name="role"><br><br>

<button type="submit" name="submit">Add User</button>

</form>

</body>
</html>