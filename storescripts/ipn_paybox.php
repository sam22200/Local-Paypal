<?php

/*//if ($_SERVER['REQUEST_METHOD'] != "GET") die ("No Passed Variables");

require_once 'storescripts/class_AuthorizeResponsePaybox.php';
// Connect to the MySQL database
require_once "storescripts/class_connexion.php";
$connection = new createConnection();
$connection->connectToDatabase();
$connection->selectDatabase();

$arp = new AuthorizeResponsePaybox($_SERVER['QUERY_STRING']);
$arp->storeTransac();


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

//mail("samuel.coz@praxedo.com", "NORMAL IPN RESULT YAY MONEY!", $req, "From: PXO_SELL_1@seller.fr");
$mail->Subject  = "TEST PAYBOX!";
$mail->Body     = "ENVOI MAIL";
$mail->WordWrap = 200;
$mail->Send();
*/

// Connect to the MySQL database
$db_host = "db4free.net:3306/adminpxo";
// Place the username for the MySQL database here
$db_username = "sam22200";
// Place the password for the MySQL database here
$db_pass = "22200sam";
// Place the name for the MySQL database here
$db_name = "adminpxo";

// Run the actual connection here
mysql_connect("$db_host","$db_username","$db_pass") or die ("could not connect to mysql");
mysql_select_db("$db_name") or die ("no database");

$q1 = sprintf ( "INSERT INTO admin (username, password) VALUES ('%s','%s')" ,
            mysql_real_escape_string( "ani" ) ,
            mysql_real_escape_string( "anissa" )
            );

mysql_query( $q1 );
?>
