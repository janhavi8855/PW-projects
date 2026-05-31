<?php

session_start();

function isAdmin()
{
    return isset($_SESSION['role']) &&
           $_SESSION['role'] == 'admin';
}

function isUser()
{
    return isset($_SESSION['role']) &&
           $_SESSION['role'] == 'user';
}

?>