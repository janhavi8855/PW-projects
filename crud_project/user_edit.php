<?php
include 'db.php';

$id = $_GET['id'];

$query = "SELECT * FROM users WHERE id=$id";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update = "UPDATE users
               SET name='$name',
               email='$email',
               role='$role'
               WHERE id=$id";

    mysqli_query($conn, $update);

    header("Location: user_list.php");
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>Edit User</h2>

<form method="POST">

Name:
<input type="text" name="name"
value="<?php echo $row['name']; ?>"><br><br>

Email:
<input type="email" name="email"
value="<?php echo $row['email']; ?>"><br><br>

Role:
<input type="text" name="role"
value="<?php echo $row['role']; ?>"><br><br>

<button type="submit" name="update">
Update User
</button>

</form>

</body>
</html>