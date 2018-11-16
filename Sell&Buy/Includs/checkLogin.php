<?php
require_once "Classes.php";
if (!isset($_SESSION))
{
    session_start();
}
$check=new CheckLogin();
$check->Check($_SESSION['id']);