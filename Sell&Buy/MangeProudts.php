<?php
require_once "Includs/Classes.php";
require_once "Includs/checkLogin.php";
?>
<html>
<head>
    <title>Mange Proucdts</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body>
<form class="form-group" method="post">
    <label>Insert New Product</label><br>
    <input type="text" class="form-group" name="prod" placeholder="Name of Product">
    <br>
    <input type="number" class="form-group" name="price" placeholder="Price of Product">
    <br>
    <input type="submit" class="btn-primary" value="Add" name="sub">
</form>
<form method="post">
    <input type="search" name="se" class="form-group" id="se" placeholder="Search for name">
</form>

<?php
if (isset($_POST['sub']))
{
$Add=new AddProduct();
$Add->Add($_POST['prod'],$_POST['price'],$_SESSION['user']);
}
if (isset($_POST['se']))
{
    $serch=new SearchByName();
    $serch->Search($_POST['se']);
    echo
    "
    <script>
          var xmlhttp=new XMLHttpRequest();
        var url='MangeProudts.php';
        xmlhttp.open('POST',url,true);
        xmlhttp.send();
    </script>
    ";
}
$mange=new getProducts();
$mange->get();
$timeSell=date("Y/m/d");
if (isset($_POST['submit']))
{
    $add=new AddSellProduct();
    $add->AddSell($_POST['prodname'],$_POST['prodprice'],$timeSell,$_POST['num']);
}
$getp=new getTotalSell();
$getp->get();
if (isset($_POST['sum']))
{
    $sum=new SumByDate();
    $sum->sum($_POST['date']);
}
?>
</body>
</html>