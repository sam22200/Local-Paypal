<?php
//Mail Order

// Connect to the MySQL database
$db_host = "127.0.0.1";
// Place the username for the MySQL database here
$db_username = "sam22200";
// Place the password for the MySQL database here
$db_pass = "22200sam";
// Place the name for the MySQL database here
$db_name = "transac";

// Run the actual connection here
mysql_connect("$db_host","$db_username","$db_pass") or die ("could not connect to mysql");
mysql_select_db("$db_name") or die ("no database");

/*$q1 = sprintf ( "INSERT INTO test (username, password) VALUES ('%s','%s')" ,
            mysql_real_escape_string( "Je suis " ) ,
            mysql_real_escape_string( "ligne 19" )
            );

if (mysql_query( $q1 )) ;//{echo "BONJOUR";} else {echo "AUREVOIR";}

*/
if ( isset($_GET['data']))
{


if (mysql_query( $q1 )) ;//{echo "BONJOUR";} else {echo "AUREVOIR";}
/*            //Envoi le mail
            $q1 = sprintf( "SELECT mail FROM users WHERE id=%s" ,
                (int) $arp->getUsername()
            );

            $row = mysql_fetch_array( mysql_query( $q1 ) );
            $email = $row['mail'];
            $template = new EmailText($arp->getRef(), $arp->getDate(), $arp->getUsername(), $arp->getAmount()/100, "CARTE", $arp->getType(), $arp->getList() , "EUR");
            $template->computeSubject();
            $template->computeBody();*/


            $arrStr = explode( '|' , $_GET['data']); // 0 : usernameID; 1 : List Art; 2: RefInvoive; 3:orderNum

            $q1 = sprintf( "SELECT username,mail FROM users WHERE id='%s' LIMIT 1" ,
                mysql_real_escape_string( $arrStr[0] )
            );
            $rowP = mysql_fetch_array( mysql_query( $q1 ) );
            $username = $rowP['username'];
            $email = $rowP['mail'];

            //Envoi par mail du code
            require_once '../PHPMailer-master/PHPMailerAutoload.php'; //or select the proper destination for this file if your page is in some   //other folder
            $mail = new PHPMailer();
            $mail->SMTPAuth = true;
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPSecure = "ssl";
            $mail->Username = "coz.samuel@gmail.com";
            $mail->Password = "samu1992";
            $mail->Port = "465";
            $mail->IsSMTP();
            $mail->AddAddress($email);
            $mail->Subject  = "CONFIRMATION DE COMMANDE";
            $mail->Body = "Chere ".$username."\n\nNous vous confirmons votre commande numéro :\n".$arrStr[3]."\n\nVoici la liste des produits qui constitue cette commande : \n".$arrStr[1]."\n\nLes forfaits seront utilisables une fois que le paiement sera validé. Vous recevrez un email de confirmation de paiement si tout s'est bien déroulé. \n\n";
            $mail->WordWrap = 200;
            $mail->Send();

/*  // Run the actual connection here
mysql_connect("$db_host","$db_username","$db_pass") or die ("could not connect to mysql");
mysql_select_db("$db_name") or die ("no database");

$q1 = sprintf ( "INSERT INTO test (username, password) VALUES ('%s','%s')" ,
            mysql_real_escape_string( "Je suis " ) ,
            mysql_real_escape_string( "ligne 70" )
            );
if (mysql_query( $q1 )) ;*/
/*Username
date
list
*/

}?>