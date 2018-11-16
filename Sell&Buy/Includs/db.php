<?php
class  DataBase
{
    function Connect()
    {
        try
        {
            $dbType="mysql";
            $dbHost="localhost";
            $dbName="Sell&Buy";
            $dbUser="root";
            $dbPass="";
            $dbOptions=[PDO::MYSQL_ATTR_INIT_COMMAND=>"set Names utf8"];
            $connect= new PDO("$dbType:host=$dbHost;dbname=$dbName", $dbUser, $dbPass,$dbOptions);
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connect;
        }
        catch(PDOException  $e )
        {
            echo "Error: "."<p style='color:red '>" . $e."</p>";
        }
    }



    function getData($db,$query,$parm = []) {
        $stmt = $db->prepare($query);
        $stmt->execute($parm);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    function setData($db,$query,$parm = []) {
        $stmt = $db->prepare($query);
        $stmt->execute($parm);
        $count = $stmt->rowCount();
        return $count;
    }
}

?>

