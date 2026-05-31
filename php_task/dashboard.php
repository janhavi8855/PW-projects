<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>
<?php
if(isset($_SESSION['username']))
{
    echo "Welcome " . $_SESSION['username'];
}
else
{
    echo "Please login first";
}
?>
</h2>

</body>
</html>