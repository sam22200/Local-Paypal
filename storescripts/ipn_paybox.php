<?php

$AUTHORIZED_IP = array('195.101.99.76',' 194.2.122.158', '195.25.7.166', '127.0.0.1');


// filtrage par adresse IP
$ip = $_SERVER['REMOTE_ADDR'];
//if(!in_array($ip, $AUTHORIZED_IP)) {
//    header('Unauthorized', true, 401);
//    exit();
//}

// ensemble param url (pour debugage/surveillance)
/*$param_url = '';
foreach($_GET as $k => $v) {
    $param_url .= urldecode($k).'='.urldecode($v).' ';
}*/

if ($_SERVER['QUERY_STRING'] != null) {

    //Verifier la reponse de paybox et faire les inscriptions en BD
    require_once 'class_AuthorizeResponsePaybox.php';
    //Inscription dans quantites
    require_once "class_StoreQte.php";
    //Inscription dans orders
    require_once "class_StoreOrders.php";
    //Template du mail
    require_once "class_user.php";

    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();


    try {
        //Creation handler d'autorisation
        $arp = new AuthorizeResponsePaybox($_SERVER['QUERY_STRING']);
        //Si on a pu mettre en BD la trnasac
        if ($arp->storeTransac()){
            //On renseigne les BD Orders et Quantités
            $storeOrders = new StoreOrders($arp->getUsername(), $arp->getOrder());
            $storeOrders->storeInBase();
            $storeQte = new StoreQte($arp->getUsername(), $arp->getList());
            $storeQte->storeInBase();

            //Envoi le mail
            $q1 = sprintf( "SELECT mail FROM users WHERE id=%s" ,
                (int) $arp->getUsername()
            );
            $row = mysql_fetch_array( mysql_query( $q1 ) );
            $email = $row['mail'];
            //Template du mail
            require_once "class_email_text.php";
            $template = new EmailText($arp->getRef(), $arp->getOrder(), $arp->getDate(), $arp->getUsername(), $arp->getAmount()/100, "CARTE", $arp->getType(), $arp->getList() , "EUR");
            $template->computeSubject();
            $template->computeBody();

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
            $mail->Subject  = $template->getSubject();
            $mail->Body = $template->getBody();
            $mail->WordWrap = 200;
            $mail->Send();

            //header('Location: checkout_complete_paybox.php');
        } else {
            //unset($_SESSION);
            //header('Location: index.php');
        }
    } catch (Exception $e) {
       // echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }

}


?>