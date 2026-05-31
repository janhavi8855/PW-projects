<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">

<div class="row">

<div class="col-md-12">

<div class="card shadow p-4">

<h1 class="text-primary">
Admin Dashboard
</h1>

<h3>
Welcome <?php echo $_SESSION['username']; ?>
</h3>

<div class="alert alert-success mt-3">
Login Successful
</div>

</div>

</div>

</div>

</div>

</body>
</html>