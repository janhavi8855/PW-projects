<?php

include 'db.php';

$id = $_GET['id'];

$query = "SELECT * FROM users_login WHERE id=$id";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{
    $username = $_POST['username'];

    $email = $_POST['email'];

    $role = $_POST['role'];

    $update = "UPDATE users_login
               SET username='$username',
               email='$email',
               role='$role'
               WHERE id=$id";

    mysqli_query($conn, $update);

    header("Location: admin_dashboard.php");
}

?>

<!DOCTYPE html>
<html>
<body>

<h2>Edit User</h2>

<form method="POST">

Username:
<input type="text" name="username"
value="<?php echo $row['username']; ?>">

<br><br>

Email:
<input type="email" name="email"
value="<?php echo $row['email']; ?>">

<br><br>

Role:
<input type="text" name="role"
value="<?php echo $row['role']; ?>">

<br><br>

<button type="submit" name="update">
Update
</button>

</form>

</body>
</html>