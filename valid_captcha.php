<?php
session_start();
date_default_timezone_set('Europe/Paris');
//Captcha
include( 'Captcha.php' );
//User class
include( 'storescripts/class_user.php' );
//Generateur de code d'activation
include( 'storescripts/class_invoiceNumberPaypal.php' );

//Si user pas loggé, redirection
$user = new User();

if( !$user->isLoggedIn() ){
  $user->redirectTo( 'login' );
} else {
    //Sinon on récupère ses infos
    $info = $user->userInfo($_SESSION['userName']);
    $userMail = $info['mail'];
}

//Creation de l'objet Captcha
use Oz\Recaptcha\Captcha;
$sitekey = '6Lf46wMTAAAAAGhYQUrmy5_u6qJ1cmh8-gxkIAni';
$secret = '6Lf46wMTAAAAAOqcnKyA9_OTWB5SIjNGZN-E5Rz2';

$captcha = new Captcha($sitekey, $secret);
$is_verified = false;
//Verification
if ( isset($_POST[Captcha::RESPONSE_FIELD_KEY]) )
{
    $is_verified = $captcha->verify($_POST[Captcha::RESPONSE_FIELD_KEY]);
}


$itemToSet;
//Si captcha ok && item est séléctionné -> ote l'élément en BD, génére code et mail
if ( $is_verified && (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "" ))
{
    // Connect to the MySQL database
    require_once "storescripts/class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // On recupere la quantité de l'article encore en stock
    $qte = array();
    $username = $_SESSION['userName'];
    $field = "qte_".$_POST['item_to_adjust'];
    $sql = mysql_query("SELECT $field FROM users WHERE username='$username' LIMIT 1");

    while ($row = mysql_fetch_array($sql)) {
        array_push($qte,$row[$field]);
    }

    //Si la qte est > 0
    if($qte[0] > 0){
        //UPDTAE a qte -1
        $q1 = sprintf( "UPDATE users SET %s='%s' WHERE username='%s'" ,
            mysql_real_escape_string( $field ),
            mysql_real_escape_string( $qte[0]-1 ),
            mysql_real_escape_string( $username )
        );

        if ( mysql_query( $q1 ) );
        $itemToSet = $_POST['item_to_adjust'];

        //Creer un code d'activation
        $invoice = new invoiceNumberPaypal();
        $inv = $invoice->getInvoiceNumber();

/*        $q2 = sprintf( "UPDATE users SET %s='%s' WHERE username='%s'" ,
            mysql_real_escape_string( $field ),
            mysql_real_escape_string( $qte[0]-1 ),
            mysql_real_escape_string( $username )
        );

        if ( mysql_query( $q1 ) );
*/
        //Envoi par mail du code
        require '/PHPMailer-master/PHPMailerAutoload.php'; //or select the proper destination for this file if your page is in some   //other folder

        ini_set("SMTP","ssl://smtp.gmail.com");
        ini_set("smtp_port","465"); //No further need to edit your configuration files.
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com"; // SMTP server
        $mail->SMTPSecure = "ssl";
        $mail->Username = "coz.samuel@gmail.com"; //account with which you want to send mail. Or use this account. i dont care :-P
        $mail->Password = "samu1992"; //this account's password.
        $mail->Port = "465";
        $mail->IsSMTP();  // telling the class to use SMTP
        $rec1=$userMail; //receiver. email addresses to which u want to send the mail.
        $mail->AddAddress($rec1);

        // Mail yourself the details
        //mail("samuel.coz@praxedo.com", "NORMAL IPN RESULT YAY MONEY!", $req, "From: PXO_SELL_1@seller.fr");
        $mail->Subject  = "CODE ACTIVATION PRAXEDO";
        $mail->Body     = "Voici votre code d'activation : ".$inv;
        $mail->WordWrap = 200;
        $mail->Send();
    } else {
        $user->redirectTo( 'index' );
    }
} else {
    $user->redirectTo( 'index' );
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Validation Captcha</title>

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


    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-md-9">

                <div class="col-md-12">
                     <div class="jumbotron">
                         <div class="panel-body text-center">
                            <span class="glyphicon glyphicon-<?php echo $is_verified ? 'ok' : 'remove' ?>" style="font-size:120px;text-align: center "></span>

                            <h1>VALIDATION</VAR></h1>
                            <p>Merci <?php echo $_SESSION['userName']; ?></p>
                            <p>Vous venez de convertir votre achat en code d'activation</p>
                            <p>Voici le code d'activation généré</p>
                            <br/>
                            <h3><?php echo $inv; ?></h3>
                            <br/>
                            <p>Ce code vous a également été envoyé sur votre adresse mail : <?php echo $userMail; ?></p>
                            <p>Cliquez sur ce bouton pour revenir à l'accueil</p>
                            <a class="btn btn-lg btn-success" href="/members.php" role="button">Retour espace Membre</a>
                            <a class="btn btn-lg btn-<?php echo $is_verified ? 'success' : 'danger' ?>" href="/" role="button">Retour Accueil ...</a>
                        </div>
                     </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /.container -->

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Praxedo 2015</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>

