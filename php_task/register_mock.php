<!DOCTYPE html>
<html>
<head>
    <title>Register Form</title>
</head>
<body>

<h2>Registration Form</h2>

<form method="POST">

Name:
<input type="text" name="name"><br><br>

Email:
<input type="email" name="email"><br><br>

Role:
<input type="text" name="role"><br><br>

<button type="submit">Submit</button>

</form>

<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $user = array(
        "Name" => $_POST['name'],
        "Email" => $_POST['email'],
        "Role" => $_POST['role']
    );

    echo "<h2>User Details</h2>";

    echo "<table border='1' cellpadding='10'>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>";

    foreach($user as $key => $value)
    {
        echo "<tr>
                <td>$key</td>
                <td>$value</td>
              </tr>";
    }

    echo "</table>";
}

?>

</body>
</html>