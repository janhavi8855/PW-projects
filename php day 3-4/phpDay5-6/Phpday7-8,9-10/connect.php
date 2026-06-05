<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "user_db",
    3307
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

?>