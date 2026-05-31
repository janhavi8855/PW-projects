<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>

<h2>Upload Profile Picture</h2>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="image">

<button type="submit" name="upload">Upload</button>

</form>

<?php

if(isset($_POST['upload']))
{
    $filename = $_FILES['image']['name'];
    $tempname = $_FILES['image']['tmp_name'];

    move_uploaded_file($tempname, $filename);

    echo "<h3>Image Uploaded Successfully</h3>";

    echo "<img src='$filename' width='200'>";
}

?>

</body>
</html>