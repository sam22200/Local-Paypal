<?php
require '../PHPMailer-master/PHPMailerAutoload.php'; //or select the proper destination for this file if your page is in some   //other folder

ini_set("SMTP","ssl://smtp.gmail.com"); 
ini_set("smtp_port","465"); //No further need to edit your configuration files.
$mail = new PHPMailer();
$mail->SMTPDebug = 1;
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // SMTP server
$mail->SMTPSecure = "ssl";
$mail->Username = "coz.samuel@gmail.com"; //account with which you want to send mail. Or use this account. i dont care :-P
$mail->Password = "samu1992"; //this account's password.
$mail->Port = "465";
$mail->IsSMTP();  // telling the class to use SMTP
$rec1="coz.samuel@gmail.com"; //receiver. email addresses to which u want to send the mail.
$mail->AddAddress($rec1);

if ($_SERVER['REQUEST_METHOD'] != "POST") die ("No Post Variables");
// Initialize the $req variable and add CMD key value pair
$req = 'cmd=_notify-validate';
// Read the post from PayPal
foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}
// Now Post all of that back to PayPal's server using curl, and validate everything with PayPal
// We will use CURL instead of PHP for this for a more universally operable script (fsockopen has issues on some environments)
$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
//$url = "https://www.paypal.com/cgi-bin/webscr";
$curl_result=$curl_err='';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
curl_setopt($ch, CURLOPT_HEADER , 0);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$curl_result = @curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

$req = str_replace("&", "\n", $req);  // Make it a nice list in case we want to email it to ourselves for reporting

// Check that the result verifies
if (strpos($curl_result, "VERIFIED") !== false) {
    $req .= "\n\nPaypal Verified OK";
} else {
	$req .= "\n\nData NOT verified from Paypal!";
	//mail("samuel.coz@praxedo.com", "IPN interaction not verified", "$req", "From: PXO_SELL_1@seller.fr");
	exit();
}

/* CHECK THESE 4 THINGS BEFORE PROCESSING THE TRANSACTION, HANDLE THEM AS YOU WISH
1. Make sure that business email returned is your business email
2. Make sure that the transactions payment status is completed
3. Make sure there are no duplicate txn_id
4. Make sure the payment amount matches what you charge for items. (Defeat Price-Jacking) */

// Check Number 1 ------------------------------------------------------------------------------------------------------------
$receiver_email = $_POST['receiver_email'];
if ($receiver_email != "PXO_SELL_1@seller.fr") {
	$message = "Investigate why and how receiver email is wrong. Email = " . $_POST['receiver_email'] . "\n\n\n$req";
    //mail("samuel.coz@praxedo.com", "Receiver Email is incorrect", $message, "From: PXO_SELL_1@seller.fr" );
    exit(); // exit script
}
// Check number 2 ------------------------------------------------------------------------------------------------------------
if ($_POST['payment_status'] != "Completed") {
	// Handle how you think you should if a payment is not complete yet, a few scenarios can cause a transaction to be incomplete
}
// Connect to database ------------------------------------------------------------------------------------------------------
require_once 'connect_to_mysql.php';
// Check number 3 ------------------------------------------------------------------------------------------------------------


$this_txn = $_POST['txn_id'];
$sql = mysql_query("SELECT id FROM transactions WHERE txn_id='$this_txn' LIMIT 1");
$numRows = mysql_num_rows($sql);
if ($numRows > 0) {
    $message = "Duplicate transaction ID occured so we killed the IPN script. \n\n\n$req";
    //mail("samuel.coz@praxedo.com", "Duplicate txn_id in the IPN system", $message, "From: PXO_SELL_1@seller.fr" );
    exit(); // exit script
}



// Check number 4 ------------------------------------------------------------------------------------------------------------
$product_id_string = $_POST['custom'];
$product_id_string = rtrim($product_id_string, ","); // remove last comma
// Explode the string, make it an array, then query all the prices out, add them up, and make sure they match the payment_gross amount
$id_str_array = explode(",", $product_id_string); // Uses Comma(,) as delimiter(break point)
$fullAmount = 0;
foreach ($id_str_array as $key => $value) {

	$id_quantity_pair = explode("-", $value); // Uses Hyphen(-) as delimiter to separate product ID from its quantity
	$product_id = $id_quantity_pair[0]; // Get the product ID
	$product_quantity = $id_quantity_pair[1]; // Get the quantity
	$sql = mysql_query("SELECT price FROM products WHERE id='$product_id' LIMIT 1");
    while($row = mysql_fetch_array($sql)){
		$product_price = $row["price"];
	}
	$product_price = $product_price * $product_quantity;
	$fullAmount = $fullAmount + $product_price;
}
$fullAmount = number_format($fullAmount, 2);
$grossAmount = $_POST['mc_gross'];
//Change the number currency by deleting the comas within the string number
$fullAmount = preg_replace('/[,]/','', $fullAmount );
if ($fullAmount != $grossAmount) {
        $message = "Variable fullAmount = ". gettype($fullAmount) . "  : " . $fullAmount . "\nVariable grossAmount = ". gettype($grossAmount) . "  : " . $grossAmount . "\n Possible Price Jack: " . $_POST['mc_gross'] . " != $fullAmount \n\n\n$req";
        //mail("samuel.coz@praxedo.com", "Price Jack or Bad Programming", $message, "From: PXO_SELL_1@seller.fr" );
        exit(); // exit script
}


// END ALL SECURITY CHECKS NOW IN THE DATABASE IT GOES ------------------------------------
////////////////////////////////////////////////////
// Homework - Examples of assigning local variables from the POST variables
$txn_id = $_POST['txn_id'];
$payer_email = $_POST['payer_email'];
$custom = $_POST['custom'];
//mail("samuel.coz@praxedo.com", "POST Variable log", json_encode($_POST) , "From: PXO_SELL_1@seller.fr" );
// Place the transaction into the database
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$payment_date = $_POST['payment_date'];
$mc_gross = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$payment_type = $_POST['payment_type'];
$payment_status = $_POST['payment_status'];
$txn_type = $_POST['txn_type'];
$payer_status = $_POST['payer_status'];
$address_street = $_POST['address_street'];
$address_city = $_POST['address_city'];
$address_state = $_POST['address_state'];
$address_zip = $_POST['address_zip'];
$address_country = $_POST['address_country'];
$address_status = $_POST['address_status'];
$notify_version = $_POST['notify_version'];
$verify_sign = $_POST['verify_sign'];
$payer_id = $_POST['payer_id'];
$mc_currency = $_POST['mc_currency'];
$mc_fee = $_POST['mc_fee'];
$invoice = $_POST['invoice'];

$sql = mysql_query("INSERT INTO transactions (product_id_array, payer_email, first_name, last_name, payment_date, mc_gross, payment_currency, txn_id, receiver_email, payment_type, payment_status, txn_type, payer_status, address_street, address_city, address_state, address_zip, address_country, address_status, notify_version, verify_sign, payer_id, mc_currency, mc_fee, invoiceNumber)
   VALUES('$custom','$payer_email','$first_name','$last_name','$payment_date','$mc_gross','$payment_currency','$txn_id','$receiver_email','$payment_type','$payment_status','$txn_type','$payer_status','$address_street','$address_city','$address_state','$address_zip','$address_country','$address_status','$notify_version','$verify_sign','$payer_id','$mc_currency','$mc_fee','$invoice')") or die ("unable to execute the query");


mysql_close();

// Mail yourself the details
//mail("samuel.coz@praxedo.com", "NORMAL IPN RESULT YAY MONEY!", $req, "From: PXO_SELL_1@seller.fr");
$mail->Subject  = "NORMAL IPN RESULT YAY MONEY!";
$mail->Body     = $req;
$mail->WordWrap = 200;
if(!$mail->Send()) {
echo 'Message was not sent!.';
echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
echo  //Fill in the document.location thing
'<script type="text/javascript">
                        if(confirm("Your mail has been sent"))
                        document.location = "/";
        </script>';
}
$mail->Subject  = "POST VARIABLE";
$mail->Body     = json_encode($_POST);
$mail->WordWrap = 200;
if(!$mail->Send()) {
echo 'Message was not sent!.';
echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
echo  //Fill in the document.location thing
'<script type="text/javascript">
                        if(confirm("Your mail has been sent"))
                        document.location = "/";
        </script>';
}
?>