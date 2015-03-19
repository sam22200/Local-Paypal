<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Page Tests</title>

</head>

<body>

<table>
<?php

session_start();
// Place db host name. Sometimes "localhost" but
// sometimes looks like this: >>      ???mysql??.someserver.net
$db_host = "127.0.0.1";
// Place the username for the MySQL database here
$db_username = "sam22200";
// Place the password for the MySQL database here
$db_pass = "22200sam";
// Place the name for the MySQL database here
$db_name = "transac";

try {
    $dbh = new PDO('mysql:host=127.0.0.1;dbname=transac', $db_username, $db_pass);

$param = "3";
$str = "";
$str .= 'SELECT payment_date, mc_gross from orders, transactions WHERE transactions.InvoiceNumber=orders.ref AND orders.username="'.$param.'"';
echo $str;
echo "</br>";

$aa;
$i = 0;
    foreach($dbh->query($str) as $row) {
        $aa[$i] = $row;
        $i++;
          foreach ($row as $key => $value) {
            echo "<tr>";
            echo "<td>";
            echo $key;
            echo "</td>";
            echo "<td>";
            echo $value;
            echo "</td>";
            echo "</tr>";
          }
    }



    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}


?>
</table>

<?php
foreach($aa as $r) {
    echo "</br>";
    echo $r['payment_date'];
    echo "  --  " ;
    echo $r['mc_gross'];
    echo "</br>";
}
?>
</body>


</html>