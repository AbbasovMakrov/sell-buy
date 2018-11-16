<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body>
<form class="form-group" method="post">
    <input type="text" class="form-group" name="user">
    <br>
    <input type="password" class="form-group" name="pass">
    <br>
    <input type="submit" class="btn-primary" value="Login" name="sub">
</form>
<?php
require_once "Includs/Classes.php";
if (isset($_POST['sub']))
{
    $logIN=new SignIn();
    $logIN->LogIn($_POST['user'],$_POST['pass']);
}
?>
</body>
</html>