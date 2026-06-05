<?php

include "connect.php";

$sql="UPDATE users
      SET role='Admin'
      WHERE id=1";

if(mysqli_query($conn,$sql))
{
    echo "Updated Successfully";
}
else
{
    echo mysqli_error($conn);
}

?>