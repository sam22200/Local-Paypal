<?php

session_start();
date_default_timezone_set('Europe/Paris');
require_once( 'storescripts/class_user.php' );

// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

$user = new User();

if( !$user->isLoggedIn() ){
  $user->redirectTo('signin');
} else {
  $info = $user->userInfo($_SESSION['userName']);
}

$orders_str ='';

  $q = sprintf( "SELECT username, ref FROM orders WHERE username='%s'" ,
         mysql_real_escape_string( $info['id'] )
       );
  $r = mysql_query( $q );

  if( !mysql_num_rows( $r ) ) {
    $orders_str = "<tr>Pas encore d'achat...</tr>";
  } else {
    $orders = mysql_fetch_array( $r );

    foreach($orders as $order){
      $orders_str .='<tr>';
      $orders_str .='<td>' . $order['ref'] . '</td>
                        <td>'. date("Y-m-d H:i:s", $info['created_at']) .'</td>
                        <td>100</td>
                        <td><span class="label label-success"><a href="#">OUVRIR</a></span>
                        </td>';
      $orders_str .='</tr>';
    }
  }


/*  $q = sprintf( "SELECT * FROM users WHERE username='%s'" ,
         mysql_real_escape_string( $info['username'] )
       );
  $r = mysql_query( $q );

  if( !mysql_num_rows( $r ) ) {
    $orders_str = "<tr>Pas encore d'achat...</tr>";
  } else {
    $orders = mysql_fetch_array( $r );

    foreach($orders as $order){
      $orders_str .='<tr>';
      $orders_str .='<td>' . $info['username'].' '.$order['qte_413'] . '</td>
                        <td>'. date('c') .'</td>
                        <td>100</td>
                        <td><span class="label label-success">OUVRIR</span>
                        </td>';
      $orders_str .='</tr>';
    }
  }*/
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