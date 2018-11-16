<head>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
</head>
<?php
require_once "db.php";
abstract class functions
{
    function ShowError($Error)
    {
        return "<p style='color: red'>".$Error."</p>";
    }
    function ShowSeccful($Seccful)
    {
        return "<p style='color: green'>".$Seccful."</p>";
    }
    function Filter($input)
    {
        if (filter_var($input,FILTER_VALIDATE_INT) == true)
        {
            $Filterd=filter_var($input,FILTER_SANITIZE_NUMBER_INT);
            return $Filterd;
        } elseif (filter_var($input,FILTER_VALIDATE_EMAIL) == true)
        {
            $Filterd=filter_var($input,FILTER_SANITIZE_EMAIL);
            return $Filterd;
        }
        else
        {
            $Filterd=filter_var($input,FILTER_SANITIZE_STRING);
            return $Filterd;
        }
    }
    function getSalt()
    {
        $salt='';
        $SaltChrest=array_merge(range('A','Z'),range('a','z'),range(0,9));
        $len=64;
        for ($i=20;$i<$len;$i++)
        {
            $salt.= $SaltChrest[array_rand($SaltChrest)];
        }
        return $salt;
    }
    function Hashing($Password)
    {
       $salt=$this->getSalt();
        $options=[
         'salt'=> $salt,
         'cost'=>10
        ];
        $Hashed=password_hash($Password,1,$options);
        return $Hashed;
    }
}
class SignIn extends functions
{
function LogIn($username,$password)
{
    $Fusername=$this->Filter($username);
    $Fpassword=$this->Filter($password);
    if (!empty($Fusername) && !empty($Fpassword))
    {
        $db=new DataBase();
        $con=$db->Connect();
        $sql="SELECT * FROM `users` where `username` = ?";
        $res=$db->getData($con,$sql,[
            $Fusername
        ]);
        if (count($res)>0)
        {
            if (password_verify($Fpassword,$res[0]['password']))
            {
                if (!isset($_SESSION))
                {
                    session_start();
                    session_regenerate_id();
                }
                $_SESSION['user']=$Fusername;
                $_SESSION['id']=$res[0]['id'];
                header("Location:MangeProudts.php");
                die();
            } else
            {
                echo $this->ShowError("Check your password");
            }
        } else
        {
            echo $this->ShowError("Sorry Username not found");
        }
    } else
    {
        echo $this->ShowError("All feilds is req");
    }
}
}
class CheckLogin extends functions
{
    function Check($SessionOfID)
    {
        $db=new DataBase();
        $con=$db->Connect();
        $sql="SELECT * FROM `users` where `id`=?";
        $res=$db->getData($con,$sql,[$SessionOfID]);
        if (count($res) <= 0)
        {
            header("Location:login.php");
            die();
        }
    }
}
class AddProduct extends functions
{
    function Add($name,$price,$username)
    {
        $Fname=$this->Filter($name);
        $Fprice=$this->Filter($price);
        if (!empty($Fname) && !empty($Fprice))
        {
            $db=new DataBase();
            $con=$db->Connect();
            $sql="SELECT * FROM `products` where `name` = ?";
            $res=$db->getData($con,$sql,[$Fname]);
            if (count($res)>0)
            {
                echo $this->ShowError("The Product is Exist");
            } else
            {
                $sql="INSERT INTO `products`( `name`, `price`,`add_by`) VALUES (?,?,?)";
                $res=$db->setData($con,$sql,[
                    $Fname,
                    $Fprice,
                    $username
                ]);
                if (count($res)>0)
                {
                    unset($Fprice);
                    unset($Fname);
                    echo $this->ShowSeccful("Done");
                } else
                {
                    echo $this->ShowError("Failed");
                }
            }
        } else
        {
            echo $this->ShowError("Sorry All fields Is req");

        }
    }
}
class getProducts extends functions
{
    function get()
    {
        $db=new DataBase();
        $con=$db->Connect();
        $sql="SELECT * FROM `products`";
        $res=$db->getData($con,$sql);
        if (count($res)>0)
        {
            echo "<table>
<tr style='background: yellow'>
<th>Product Name</th>
<th>Price</th>
<th>Action</th>
</tr>";
            foreach ($res as $re)
            {
                echo "<tr style='background: green'>";
                echo "<td>".$re['name']."</td>";
                echo "<td>".$re['price']."</td>";
                $id=$re['id'];
                echo "<td><form method='post'>
<input type='hidden' value='$id' name='id'>
<input type='submit' name='del' class='btn-danger' value='Delete'>
<button type='submit' name='edit' class='btn-outline-dark' ><a href='edit.php?id=$id' style='text-decoration: none'>Edit</a></button>
</form>
</td>";
                echo "</tr>";
            }
            echo "</table>";
            if (isset($_POST['del']))
            {
                $sql="DELETE FROM `products` WHERE `id` = ?";
                $res=$db->setData($con,$sql,[
                    $this->Filter($_POST['id'])
                ]);
                if (count($res)>0)
                {
                    echo $this->ShowSeccful("Done");
                } else
                {
                    echo $this->ShowError("Failed");
                }
            }
        } else
        {
            echo $this->ShowError("NO PRod");
        }

    }
}
class EditProduct extends functions
{
function Edit($id,$ProdName,$price)
{
    $Fprodname=$this->Filter($ProdName);
    $Fprice=$this->Filter($price);
    $Fid=$this->Filter($id);
    if (!empty($Fprodname) && !empty($Fprice) && !empty($Fid))
    {
        $db=new DataBase();
        $con=$db->Connect();
        $sql="UPDATE `products` SET `name`=?,`price`=? WHERE `id` = ?";
        $res=$db->setData($con,$sql,[
            $Fprodname,
            $Fprice,
            $Fid
        ]);
        if (count($res)>0)
        {
            echo $this->ShowSeccful("done");
        } else
        {
            echo $this->ShowError("Fail");
        }
    } else
    {
        echo $this->ShowError("All Fileds Is req");
    }
}
}
class submitEdit extends functions
{
    function sub()
    {
        if (isset($_GET['id']))
        {
            $edit=new EditProduct();
            if (!empty($edit->Filter($_GET['id'])))
            {
                $dataBase=new DataBase();
                $con=$dataBase->Connect();
                $query="SELECT * FROM `products` where id=?";
                $res=$dataBase->getData($con,$query,[$edit->Filter($_GET['id'])]);
                if (count($res)>0)
                {
                    $name=$res[0]['name'];
                    $price=$res[0]['price'];
                    echo "<form method='post'>
<input type='text' name='name' class='form-group' value='$name'>
<br>
<input type='number' name='price' class='form-group' value='$price'>
<br>
<input type='submit' class='btn-primary' value='Save' name='edit'>
</form>";
                    if (isset($_POST['edit']))
                    {
                        $edit->Edit($_GET['id'],$_POST['name'],$_POST['price']);
                    }
                } else
                {
                    echo $edit->ShowError("No Product With this id");
                }
            }
        }
    }
}
class AddSellProduct extends functions
{
    function AddSell($product, $price, $date_sell, $numberOfproducts)
    {
        $Fprod=$this->Filter($product);
        $Fprice=$this->Filter($price);
        $Fnumber=$this->Filter($numberOfproducts);
        $Fdate=$this->Filter($date_sell);
        if (!empty($Fprod) && !empty($Fprice) && !empty($Fnumber) && !empty($Fdate))
        {
            $db=new DataBase();
            $con=$db->Connect();

            $query="INSERT INTO `sell_products`( `product`, `price`, `sell_time`, `the_number`) VALUES (?,?,?,?)";
            $res=$db->setData($con,$query,[
                $Fprod,
                $Fprice,
                $Fdate,
                $Fnumber
            ]);
            if (count($res)>0)
            {
                echo  $this->ShowSeccful("Done");
            } else
            {
                echo $this->ShowError("failed");
            }
        }else
        {
            echo $this->ShowError("can not any field be requerid");
        }
    }
}
class SearchByName extends functions
{
    function Search($name)
    {
        $Fname=$this->Filter($name);
        if (!empty($Fname))
        {
          $db=new DataBase();
          $con=$db->Connect();
          $sql = "SELECT *  FROM `products` WHERE `name` LIKE '%$Fname%'";
          $res=$db->getData($con,$sql,[$Fname]);
          if (count($res)>0)
          {
              echo "<form method='post'>";
              echo "<select name='prodname' >";
              foreach ($res as $re)
              {
                  $vName=$re['name'];
                  echo "<option value='$vName'>".$vName."</option>";
              }
              echo "</select>";
              echo "<select name='prodprice' >";
              foreach ($res as $re1)
              {
                  $vPrice=$re1['price'];
                  echo "<option value='$vPrice'>".$vPrice."</option>";
              }
              echo "</select>";
              echo "<select name='num' >";
              for ($i=1;$i<=30;$i++)
              {
                  echo "<option value='$i'>".$i."</option>";
              }
              echo "</select>";
              echo "<input type='submit' name='submit' value='Save' class='btn-primary'>";
              echo "</form>";
          }else
          {
              echo $this->ShowError("F");
          }
        }else
        {
            echo $this->ShowError("FFF");
        }
    }
}
class getTotalSell extends functions
{
    function get()
    {
        $db=new DataBase();
        $con=$db->Connect();
        $q="SELECT  `sell_time` FROM `sell_products`";
        $rels=$db->getData($con,$q);
        if (count($rels)>0)
        {
            echo "<form method='post'>";
            echo "<select name='date'>";
            for ($x=0;$x<count($rels);$x+=2)
            {
                $date=$rels[$x]['sell_time'];
                echo "<option value='$date'>".$date."</option>";
            }
            echo "</select>";
            echo "<input name='sum' type='submit' class='btn-primary' value='Sum by Date'>";
            echo "</form>";

        }else
        {
            echo $this->ShowError("No sells");
        }

    }
}
class SumByDate extends functions
{
    function sum($date)
    {
        $Fdate=$this->Filter($date);
        if (!empty($Fdate))
        {
            $db=new DataBase();
            $con=$db->Connect();
            $q="SELECT *  FROM `sell_products` WHERE `sell_time` = '$Fdate'";
            $reslt=$db->getData($con,$q,[$Fdate]);
            if (count($reslt)>0)
            {
               $finalRes='';
               for ($i=0;$i<count($reslt);$i++)
               {

                   if (count($reslt)>2)
                   {
                       for ($j=1;$j<$i;$j++)
                       {
                           $Price1=$reslt[$i]['price'];
                           $num1=$reslt[$i]['the_number'];
                           $price2=$reslt[$j]['price'];
                           $num2=$reslt[$j]['the_number'];
                           $finalRes .= ($Price1*$num1) + ($price2*$num2);
                       }

                   }

               }
                if (count($reslt) == 1)
                {
                    $Price1=$reslt[0]['price'];
                    $num1=$reslt[0]['the_number'];
                    $finalRes .= $Price1*$num1;
                }
                elseif (count($reslt) == 2 )
                {
                    $Price1=$reslt[0]['price'];
                    $num1=$reslt[0]['the_number'];
                    $price2=$reslt[1]['price'];
                    $num2=$reslt[1]['the_number'];
                    $finalRes .= ($Price1*$num1) + ($price2*$num2);
                }
               echo "Total of Sells: ".$finalRes." IQD";
            }else
            {
                echo $this->ShowError("F");
            }
        }
    }
}