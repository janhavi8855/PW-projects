<?php

if(isset($_POST['theme']))
{
    $theme = $_POST['theme'];

    setcookie("theme", $theme, time() + 3600);

    header("Location: theme.php");
}

$bg = "white";
$text = "black";

if(isset($_COOKIE['theme']))
{
    if($_COOKIE['theme'] == "dark")
    {
        $bg = "black";
        $text = "white";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Theme Selection</title>
</head>

<body style="background-color: <?php echo $bg; ?>; color: <?php echo $text; ?>;">

<h2>Select Theme</h2>

<form method="POST">

<select name="theme">
    <option value="light">Light</option>
    <option value="dark">Dark</option>
</select>

<button type="submit">Apply</button>

</form>

</body>
</html>