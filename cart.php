<?php
session_start(); // Start session first thing in script
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Paris');

//User class
include( 'storescripts/class_user.php' );
$user = new User();
// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

//Invoice generator number
require_once "storescripts/class_invoiceNumberPaypal.php";
//Invoice generator number
require_once "storescripts/class_invoiceNumberPaybox.php";
//Invoice generator number
require_once "storescripts/class_paybox.php";
//Mobile Detect
require_once "mobile_detect.php";
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 1 (if user attempts to add something to the cart from the product page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['pid'])) {
    $pid = $_POST['pid'];
	$wasFound = false;
	$i = 0;
	// If the cart session variable is not set or cart array is empty
	if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
	    // RUN IF THE CART IS EMPTY OR NOT SET
		$_SESSION["cart_array"] = array(0 => array("item_id" => $pid, "quantity" => 1));
	} else {
		// RUN IF THE CART HAS AT LEAST ONE ITEM IN IT
		foreach ($_SESSION["cart_array"] as $each_item) {
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $pid) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $pid, "quantity" => $each_item['quantity'] + 1)));
					  $wasFound = true;
				  } // close if condition
		      } // close while loop
	       } // close foreach loop
		   if ($wasFound == false) {
			   array_push($_SESSION["cart_array"], array("item_id" => $pid, "quantity" => 1));
		   }
	}
	header("location: cart.php");
    exit();
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 2 (if user chooses to empty their shopping cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['cmd']) && $_GET['cmd'] == "emptycart") {
    unset($_SESSION["cart_array"]);
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 3 (if user chooses to adjust item quantity)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "") {
    // execute some code
	$item_to_adjust = $_POST['item_to_adjust'];
	$quantity = $_POST['quantity'];
	$quantity = preg_replace('#[^0-9]#i', '', $quantity); // filter everything but numbers
	if ($quantity >= 100) { $quantity = 99; }
	if ($quantity < 1) { $quantity = 1; }
	if ($quantity == "") { $quantity = 1; }
	$i = 0;
	foreach ($_SESSION["cart_array"] as $each_item) {
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $item_to_adjust) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $item_to_adjust, "quantity" => $quantity)));
				  } // close if condition
		      } // close while loop
	} // close foreach loop
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 4 (if user wants to remove an item from cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] != "") {
    // Access the array and run code to remove that array index
 	$key_to_remove = $_POST['index_to_remove'];
	if (count($_SESSION["cart_array"]) <= 1) {
		unset($_SESSION["cart_array"]);
	} else {
		unset($_SESSION["cart_array"]["$key_to_remove"]);
		sort($_SESSION["cart_array"]);
	}
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 5  (render the cart for the user to view on the page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cartOutputB = "";
$cartTotal = "";
$pp_checkout_btn = "";
$product_id_array = "";
$myPbxMasterStr = "";
$myPbxVisaStr = "";
$isEmpty = !isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1;

if ($isEmpty) {
    $cartOutputB = "<h3 align='center'>Votre panier est vide</h3>";
} else {
	// Start PayPal Checkout Button
	$pp_checkout_btn .= '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="PXO_SELL_1@seller.fr">';
	// Start the For Each loop
	$i = 0;
    foreach ($_SESSION["cart_array"] as $each_item) {
		$item_id = $each_item['item_id'];
		$sql = mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1");
		while ($row = mysql_fetch_array($sql)) {
			$product_name = $row["product_name"];
			$price = $row["price"];
			$details = $row["details"];
		}
		$pricetotal = $price * $each_item['quantity'];
		$cartTotal = $pricetotal + $cartTotal;
		setlocale(LC_ALL, "fr_FR");
        //$pricetotal = money_format("%10.0n", $pricetotal);
		// Dynamic Checkout Btn Assembly
		$x = $i + 1;
		$pp_checkout_btn .= '<input type="hidden" name="item_name_' . $x . '" value="' . $product_name . '">
        <input type="hidden" name="amount_' . $x . '" value="' . $price . '">
        <input type="hidden" name="quantity_' . $x . '" value="' . $each_item['quantity'] . '">  ';
		// Create the product array variable
		$product_id_array .= "$item_id-".$each_item['quantity'].",";
		// Dynamic table row assembly
    $cartOutputB .= '<div class="container">';

      $cartOutputB .= '<div class="rom" style="border:1px solid #ddd;">';

        $cartOutputB .= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 lg-offset-1  text-center">';
          $cartOutputB .= '<img class="img-responsive img-thumbnail img-center center-block" src="inventory_images/' . $item_id . '.jpg" alt="' . $product_name. '" />';
        $cartOutputB .= '</div>';
        $cartOutputB .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-center">';
          $cartOutputB .= '<span class="badge" style = "">' . $price . 'EUR</span>';
        $cartOutputB .= '</div>';

        $cartOutputB .= '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-3  text-center">';
          $cartOutputB .= '<form action="cart.php" method="post">';
            $cartOutputB .= '<div class="input-group">';
              $cartOutputB .= '<input name="quantity" type="text" class="form-control" value="' . $each_item['quantity'] . '" maxlength="2" />';
              $cartOutputB .= '<span class="input-group-btn">';
                $cartOutputB .= '<input class="btn btn-danger" name="adjustBtn' . $item_id . '" type="submit" value="Changer" />';
              $cartOutputB .= '</span>';
            $cartOutputB .= '</div>';
            $cartOutputB .= '<input name="item_to_adjust" type="hidden" value="' . $item_id . '" />';
          $cartOutputB .= '</form>';
        $cartOutputB .= '</div>';

        $cartOutputB .= '</div>';
          $cartOutputB .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-center"><span class="badge" style = "">' . $pricetotal . '</span>';
        $cartOutputB .= '</div>';

        $cartOutputB .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2  text-center">';
          $cartOutputB .= '<form action="cart.php" method="post">';
            $cartOutputB .= '<input class="btn btn-danger" name="deleteBtn' . $item_id . '" type="submit" value="X" />';
            $cartOutputB .= '<input name="index_to_remove" type="hidden" value="' . $i . '" />';
          $cartOutputB .= '</form>';
        $cartOutputB .= '</div>';

      $cartOutputB .= '</div>';

    $cartOutputB .= '</div>';

		$i++;
    }

	setlocale(LC_ALL, "fr_FR");
  //$cartTotal = money_format("%10.2n", $cartTotal);
  $cartTotalNumber = $cartTotal;
	$cartTotal = "<div style='margin-top:12px;' align='right'>Total Panier : ".$cartTotal."</div>";
    $invoice = new invoiceNumberPaypal();
    $inv = $invoice->getInvoiceNumber();
    // Finish the Paypal Checkout Btn
  	$pp_checkout_btn .= '<input type="hidden" name="custom" value="' . $product_id_array . '">
  	<input type="hidden" name="notify_url" value="http://pxo.t.proxylocal.com/storescripts/my_ipn.php">
  	<input type="hidden" name="return" value="http://pxo.t.proxylocal.com/checkout_complete.php">
  	<input type="hidden" name="rm" value="2">
  	<input type="hidden" name="cbt" value="Return to The Store">
  	<input type="hidden" name="cancel_return" value="http://pxo.t.proxylocal.com/paypal_cancel.html">
  	<input type="hidden" name="lc" value="FR">
  	<input type="hidden" name="currency_code" value="EUR">
    <input type="hidden" name="invoice" value="'. $inv .'">
  	<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - its fast, free and secure!">
  	</form>';

  }

$payment_str = "";
if( $user->isLoggedIn() && !$isEmpty){
//PAYBOX BUTTONS
    $productsList = "";
    foreach ($_SESSION["cart_array"] as $each_item) {
      $product_id = $each_item['item_id'];
      $product_qte = $each_item['quantity'];
      $productsList .= ",$product_id-".$product_qte;
    }
    $productsList = ltrim($productsList,',');
    $invPbx = new invoiceNumberPaybox();
    $invPbx->setListProduct($productsList);
    $inv2 = $invPbx->getInvoiceNumber();
    // URL du serveur (ici la pre-production pour les tests)
    if (!$isEmpty){
      $detect = new Mobile_Detect();
      ($detect->isMobile()) ? $PAYBOX_DOMAIN_SERVER = "https://preprod-tpeweb.paybox.com/cgi/ChoixPaiementMobile.cgi" : $PAYBOX_DOMAIN_SERVER = "https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi";
      $PBX_SITE = "1999888";
      $PBX_RANG = "32";
      $PBX_IDENTIFIANT = "1686319";
      $PBX_EFFECTUE = "http://pxo.t.proxylocal.com/checkout_complete_paybox.php";
      $PBX_REPONDRE_A = "pxo.t.proxylocal.com/storescripts/ipn_paybox.php";
      $PBX_ANNULE = "http://pxo.t.proxylocal.com/paypal_cancel.html";
      $PBX_TYPEPAIEMENT = "CARTE";
      $PBX_TYPECARTE = "VISA";
      $PBX_TOTAL = $cartTotalNumber*100;
      ($detect->isMobile()) ? $PBX_SOURCE = "XHTML" : $PBX_SOURCE = "HTML";
      $PBX_DEVISE = 978;
      $PBX_CMD = $inv2;
      $PBX_PORTEUR = "test@paybox.com";
      $PBX_RETOUR = "auto:A;montant:M;type:C;date:W;ref:R;id:S;erreur:E;bin6:N;signature:K";
      $PBX_HASH ="SHA512";
      $PBX_TIME = date("c");
      $PBX_IMG = "inventory_images/visa_64.png";

      //HMAC DE TEST  0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF
      $myPbxVisaBtn = new Paybox($PAYBOX_DOMAIN_SERVER, $PBX_SITE, $PBX_RANG, $PBX_IDENTIFIANT, $PBX_EFFECTUE, $PBX_ANNULE, $PBX_TYPEPAIEMENT, $PBX_TYPECARTE , $PBX_TOTAL, $PBX_SOURCE, $PBX_DEVISE ,$PBX_CMD, $PBX_PORTEUR, $PBX_RETOUR, $PBX_HASH, $PBX_TIME, $PBX_IMG);
      $myPbxVisaStr = $myPbxVisaBtn->computePbxBtn();
      $PBX_TYPECARTE = "EUROCARD_MASTERCARD";
      $PBX_IMG = "inventory_images/master_64.png";

      $myPbxMasterBtn = new Paybox($PAYBOX_DOMAIN_SERVER, $PBX_SITE, $PBX_RANG, $PBX_IDENTIFIANT, $PBX_EFFECTUE, $PBX_ANNULE, $PBX_TYPEPAIEMENT, $PBX_TYPECARTE , $PBX_TOTAL, $PBX_SOURCE, $PBX_DEVISE ,$PBX_CMD, $PBX_PORTEUR, $PBX_RETOUR, $PBX_HASH, $PBX_TIME, $PBX_IMG);
      $myPbxMasterStr = $myPbxMasterBtn->computePbxBtn();
    }

$payment_str .= '<div id="paybox-elements">
            <div class="container"><!-- container PBX Visa-->
              <div class="row text-center">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">'.$myPbxVisaStr.'
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">'.$myPbxMasterStr.'
                </div>
              </div>
            </div> <!-- /container -->
          <div id="paypal-elements">
            <div class="container">

              <div class="row text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">'.$pp_checkout_btn.'
                </div>
              </div>

            </div> <!-- container -->
          </div><!-- /paypal-elements -->


        </div><!-- /PageContent -->';
} else {
  if ( !$user->isLoggedIn()){
    $payment_str .= '<div class = "row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 col-xs-offset-3 text-center">';
    $payment_str .= '<div class="alert alert-info" role="alert">IL FAUT VOUS LOGGER AFIN DE PROCEDER A L ACHAT ... </br></br>Pour vous identifier, cliquez <a href="signin.php">ici</a></br> Désirez vous créer un <a href="register.php">compte</a></div>';
    $payment_str .= '</div>';
    $payment_str .= '</div>';
  }
}

//$connection->closeConnection();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//FR" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Votre panier</title>

    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style/shop-homepage.css" rel="stylesheet">
    <link href="style/style.css"  rel="stylesheet" media="screen" />
  </head>

  <body>
    <div id="mainWrapper">

      <?php include_once("template_header.php");?>

      <div class="jumbotron">

        <div id="pageContent">
          <div id="cart-attributes">

            <div class="container">

                <div class="row">

                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  text-center"><span class="label label-default" style = "">PRODUIT</span>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  text-center"><span class="label label-default" style = "">PRIX</span>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3  text-center"><span class="label label-default" style = "">QUANTITE</span>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  text-center"><span class="label label-default" style = "">TOTAL</span>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-2  text-center"><span class="label label-default" style = "">RETIRER</span>
                    </div>

                </div><!-- row -->

            </div> <!-- container -->

          </div><!-- cart-attributes -->

          <div id="cart-elements">
            <?php echo $cartOutputB; ?>
          </div>

          <div id="checkout-elements">

            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <a style="" href="cart.php?cmd=emptycart">Cliquez ici pour vider le panier</a>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">

                  <span class="badge" style = "">
                      <?php echo $cartTotal; ?>
                  </span>

              </div>
            </div>

          </div><!-- checkout-elements -->

          <?php echo $payment_str; ?>


        </div><!-- /PageContent -->

      </div> <!-- /jumbotron -->

      <?php include_once("template_footer.php");?>
      <!-- jQuery -->
      <script src="js/jquery.js"></script>

      <!-- Bootstrap Core JavaScript -->
      <script src="js/bootstrap.min.js"></script>
    </div>
  </body>

</html>