<!DOCTYPE html>
<html>
<head>
    <title>PHP Form</title>
</head>
<body>

<h2>Enter Your Name</h2>

<form method="POST">
    <input type="text" name="username" placeholder="Enter name">
    <button type="submit">Submit</button>
</form>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $name = $_POST['username'];

    echo "<h3>Hello, $name</h3>";
}
?>

</body>
</html>