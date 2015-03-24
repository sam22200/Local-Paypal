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

// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

$username = $info['username'];

//Recupere les qtes d'achats de user
$sql = mysql_query("SELECT qte_413, qte_414, qte_415 FROM users WHERE username='$username' LIMIT 1");
$qte = array();
while ($row = mysql_fetch_array($sql)) {
  array_push($qte,$row["qte_413"]);
  array_push($qte,$row["qte_414"]);
  array_push($qte,$row["qte_415"]);
}

$ref = array("413", "414", "415");
$color = array("red", "blue", "green");
$color_b = array("danger", "primary", "success");
$i = 0;
$output_str = "";
//Si il n'a rien en stock
if (!$qte[0] && !$qte[1] && !$qte[2]){
  $output_str .= "AUCUN JETON DISPONIBLE...";
} else {
  //Sinon creation des templates avec bouton convertir
  for($i = 0; $i <= 2; $i++){
    if ($qte[$i]){
      $output_str .= '<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">

              <!-- PRICE ITEM -->
              <div class="panel price panel-'.$color[$i].'">
                <div class="panel-heading  text-center">
                <h3>FORFAIT '. ($i+1)  . ' MOIS</h3>
                </div>
                <div class="panel-body text-center">
                  <p class="lead" style="font-size:40px"><strong>€50 / Mois</strong></p>
                </div>
                <ul class="list-group list-group-flush text-center">
                  <li class="list-group-item"><i class="icon-ok text-danger"></i> Usage personnel</li>
                  <li class="list-group-item"><i class="icon-ok text-danger"></i> Usage illimité</li>
                  <li class="list-group-item"><i class="icon-ok text-danger"></i> Support 24/7</li>
                </ul>
                <div class="panel-footer text-center">
                <form id="convertir'.$ref[$i].'" class="form-horizontal" action="conversion.php" method="post">
                  <input class="btn btn-lg btn- block btn-'.$color_b[$i].'" name="convertBtn'.$ref[$i].'" type="submit" value="CONVERTIR" />
                  <input name="item_to_adjust" type="hidden" value="'.$ref[$i].'" />
                </form>
                </div>
                <div class="panel-info text-center">
                  <span class="badge" href="#">'.$qte[$i].' Restants</span>
                </div>
              </div>
              <!-- /PRICE ITEM -->


        </div>';
      }
  }
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
    <title>Convertir</title>


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

  <?php echo $output_str;?>

</div> <!-- /row-->
</div> <!-- /container-->


</div> <!-- Jumbotron -->

      <?php include_once("template_footer.php");?>
    </div> <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    </body>


</html>