<?php

session_start();

if(
!isset($_SESSION['id_usuario'])
)
{
    header("Location: login.php");
    exit();
}

if(
$_SESSION['id_rol'] != 1
&&
$_SESSION['id_rol'] != 2
)
{
    header("Location: dashboard.php");
    exit();
}

?>