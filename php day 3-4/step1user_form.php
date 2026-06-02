<!DOCTYPE html>
<html>
<head>
    <title>User Form</title>
</head>
<body>

<h2>User Details Form</h2>

<form method="POST">

    Name:
    <input type="text" name="name"><br><br>

    Email:
    <input type="email" name="email"><br><br>

    Age:
    <input type="number" name="age"><br><br>

    <input type="submit" value="Submit">

</form>

<?php

if(isset($_POST['name']))
{
    echo "<h3>User Details</h3>";
    echo "Name: ".$_POST['name']."<br>";
    echo "Email: ".$_POST['email']."<br>";
    echo "Age: ".$_POST['age'];
}

?>

</body>
</html>