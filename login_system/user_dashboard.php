<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>

<title>User Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow p-4">

<h1 class="text-success">
User Dashboard
</h1>

<h3>
Welcome <?php echo $_SESSION['username']; ?>
</h3>

<div class="alert alert-info mt-3">
User Logged In Successfully
</div>

</div>

</div>

</div>

</div>

</body>
</html>