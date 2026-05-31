<?php

session_start();

include 'db.php';

$message = "";

if(isset($_POST['login']))
{
    $email = $_POST['email'];

    $password = $_POST['password'];

    $query = "SELECT * FROM users_login WHERE email='$email'";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password']))
        {
            $_SESSION['username'] = $user['username'];

            header("Location: admin_dashboard.php");

            exit();
        }
        else
        {
            $message = "Invalid Password";
        }
    }
    else
    {
        $message = "Email Not Found";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow p-4">

<h2 class="text-center mb-4">
Login Form
</h2>

<?php

if($message != "")
{
    echo "<div class='alert alert-danger'>$message</div>";
}

?>

<form method="POST">

<div class="mb-3">

<label>Email</label>

<input type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input type="password"
name="password"
class="form-control"
required>

</div>

<button type="submit"
name="login"
class="btn btn-primary w-100">

Login

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>