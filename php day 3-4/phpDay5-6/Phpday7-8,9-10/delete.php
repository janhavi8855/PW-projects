<?php

include "connect.php";

$sql="DELETE FROM users
      WHERE id=1";

if(mysqli_query($conn,$sql))
{
    echo "Deleted Successfully";
}
else
{
    echo mysqli_error($conn);
}

?>