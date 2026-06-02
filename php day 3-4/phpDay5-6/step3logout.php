<?php

session_start();

session_destroy();

header("Location: step1login.php");

?>