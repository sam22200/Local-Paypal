<?php

session_start();
unset($_SESSION["cart_array"]);


if ($_SERVER['REQUEST_METHOD'] != "GET") die ("No Passed Variables");

//Verifier la reponse de paybox et faire les inscriptions en BD
require_once 'storescripts/class_AuthorizeResponsePaybox.php';
//Inscription dans quantites
require_once "storescripts/class_StoreQte.php";
//Inscription dans orders
require_once "storescripts/class_StoreOrders.php";

// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

try {
    //Creation handler d'autorisation
    $arp = new AuthorizeResponsePaybox($_SERVER['QUERY_STRING']);
    //Si on a pu mettre en BD la trnasac
    if ($arp->storeTransac()){
        //On renseigne les BD Orders et Quantités
        $storeOrders = new StoreOrders($arp->getUsername(), $arp->getRef());
        $storeOrders->storeInBase();
        $storeQte = new StoreQte($arp->getUsername(), $arp->getList());
        $storeQte->storeInBase();

        //Verification a enlever
        if ($somme = $arp->computeChecks()){
            $valid =  "Tout est Cohérent !";
        }else {
            $valid =  "NON Cohérent !";
        }
    }
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
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

    <title>Paiement Validé</title>

    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <!-- <link href="css/shop-homepage.css" rel="stylesheet"> -->

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
                            <span class="glyphicon glyphicon-ok" style="font-size:120px;text-align: center "></span>

                            <h1>Paiement Validé</VAR></h1>
                            <h1>Paiment Valide (code erreur + signature)</VAR></h1>
                            <p>Cliquez sur ce bouton pour revenir à l'accueil</p>
                            <a class="btn btn-lg btn-success" href="/convertItems.php" role="button">Convertir l'achat</a>
                            <a class="btn btn-lg btn-success" href="/" role="button">Retour Accueil</a>
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

