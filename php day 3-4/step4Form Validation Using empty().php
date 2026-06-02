<?php

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $name = $_POST['name'];

    if(empty($name))
    {
        echo "Name is required";
    }
    else
    {
        echo "Welcome " . $name;
    }
}

?>

<form method="POST">

Name:
<input type="text" name="name">

<input type="submit">

</form>