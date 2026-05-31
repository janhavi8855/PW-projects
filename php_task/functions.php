<?php

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function validateInput($data)
{
    if(empty($data))
    {
        return "Field cannot be empty";
    }
    else
    {
        return "Valid Input";
    }
}

echo sanitizeInput("  Hello User  ");

echo "<br><br>";

echo validateInput("Janhavi");

?>