<?php

session_start();
date_default_timezone_set('Europe/Paris');
require_once( 'storescripts/class_user.php' );


$user = new User();

if( !$user->isLoggedIn() ){
  $user->redirectTo('signin');
} else {
  $info = $user->userInfo($_SESSION['userName']);
}

/// RETRIEVE DE LA BASE
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

$str = "";
$str .= 'SELECT ref, payment_date, mc_gross from orders, transactions WHERE transactions.InvoiceNumber=orders.ref AND orders.username="'.$info['id'].'"';

//echo $str;
//echo "</br>";

$orders = array();
$i = 0;
    foreach($dbh->query($str) as $row) {
        $orders[$i] = $row;
        $i++;
    }

$orders_str ='';
if (!$orders) {
  $orders_str ='<tr><td>Aucun Achat ...</td></tr>';
} else {
  foreach($orders as $ach){
        $orders_str .='<tr>';
        $orders_str .='<td>' . substr( $ach['ref'] , 0 , 5 ) . '... ' . '</td>
                          <td>'. date("Y-m-d H:i:s", $ach['payment_date']) .'</td>
                          <td>'.$ach['mc_gross'].'</td>
                          <td><span class="label label-success"><a target="_blank" style="target-new: tab;" href="generate_pdf.php?ref='.$ach['ref'].'">OUVRIR</a></span>
                          </td>';
        $orders_str .='</tr>';
  }
}

    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
?>


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Achats</title>


    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="style/shop-homepage.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    <div id="mainWrapper">
      <?php include_once("template_header.php");?>

<div class="jumbotron">

  <div class="container">
    <div class="row">
      <div class="span5">
              <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date Achat</th>
                        <th>Montant</th>
                        <th>Facture</th>
                    </tr>
                </thead>
                <tbody>
                  <?php echo $orders_str; ?>
                </tbody>
              </table>
              </div> <!-- Span5 -->
    </div> <!-- Row -->
  </div> <!-- Container -->


</div> <!-- Jumbotron -->

      <?php include_once("template_footer.php");?>
    </div> <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    </body>


</html>