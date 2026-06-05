<?php

include "connect.php";

$result=mysqli_query($conn,"SELECT * FROM users");

while($row=mysqli_fetch_assoc($result))
{
    echo $row['id']." ";
    echo $row['name']." ";
    echo $row['email']."<br>";
}

?>