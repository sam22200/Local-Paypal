<?php
session_start();
date_default_timezone_set('Europe/Paris');

//Cap
include( 'Captcha.php' );
//User class
include( 'storescripts/class_user.php' );
$user = new User();

if( !$user->isLoggedIn() ){
  $user->redirectTo( 'login' );
}

// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

//Si on a bien recu les informations en Post de ConvertItems
if (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "") {

 // On recupere la quantité de l'article encore en stock
  $qte = array();
  $ref = array(0 => '413',1 =>  '414',2 =>  '415');
  $username = $_SESSION['userName'];
  $field = "qte_".$_POST['item_to_adjust'];
  $sql = mysql_query("SELECT $field FROM users WHERE username='$username' LIMIT 1");

  while ($row = mysql_fetch_array($sql)) {
    array_push($qte,$row[$field]);
  }

  //Si la qte est > 0
  if($qte[0] > 0){
    $q1 = sprintf( "SELECT product_name, details, category, subcategory FROM products WHERE id='%s' LIMIT 1" ,
        mysql_real_escape_string( $_POST['item_to_adjust'] )
    );
    $rowP = mysql_fetch_array( mysql_query( $q1 ) );

    $prodDetails = $rowP['details'];
    $prodName = $rowP['product_name'];
    $prodCategory = $rowP['category'];
    $prodSubCategory = $rowP['subcategory'];

  } else {
    $user->redirectTo( 'index' );
  }
}

?>


<?php
//Création du Captcha
//use Oz\Recaptcha\Captcha;
use Oz\Recaptcha\Captcha;

$sitekey = '6Lf46wMTAAAAAGhYQUrmy5_u6qJ1cmh8-gxkIAni';
$secret = '6Lf46wMTAAAAAOqcnKyA9_OTWB5SIjNGZN-E5Rz2';

$captcha = new Captcha($sitekey, $secret);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversion</title>


    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="style/shop-homepage.css" rel="stylesheet">

    <script src="<?php echo Captcha::SCRIPT_URL ?>"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
      <?php include_once("template_header.php");?>

  <div class="jumbotron">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-1 text-center">
                <p> <?php echo $prodName; ?></p>
                <p> <?php echo $prodDetails; ?> </p>
                <p> Pour obtenir le code </p>
                <p> 1. Cliquez sur 'Je ne suis pas un robot' </p>
                <p> 2. Cliquez ensuite sur le bouton 'Obtenir le code' </p>

            </div>
        </div> <!-- row -->

        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-1 text-center">
            <p> </p>
            <form method="post" action="valid_captcha.php">
              <div class="captcha-wrapper" style="border-style: solid;  background-color: white;  border-color: #FF6666;">
                <?php echo $captcha->getHTML() . PHP_EOL ?>
              </div>
              <input name="item_to_adjust" type="hidden" value=<?php echo $_POST['item_to_adjust'] ?>  />
              </br>
              <input class="btn btn-primary" type="submit" value="Obtenir le code" />
            </form>

          </div>


        </div> <!-- row -->
    </div> <!-- container -->

  </div> <!-- Jumbotron -->

      <?php include_once("template_footer.php");?>
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

</body>

</html>

