<?php

session_start();
date_default_timezone_set('Europe/Paris');

// Définition facultative du répertoire des polices systèmes
// Sinon tFPDF utilise le répertoire [chemin vers tFPDF]/font/unifont/
// define("_SYSTEM_TTFONTS", "C:/Windows/Fonts/");

require('tfpdf.php');

if (isset($_GET['ref']) AND $_GET['ref'] != "")
{
    //Template du mail
    require_once "storescripts/class_email_text.php";
    // Connect to the MySQL database
    require_once "storescripts/class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    require_once( 'storescripts/class_user.php' );

    $user = new User();

    if( !$user->isLoggedIn() ){
      $user->redirectTo('signin');
    } else {
      $info = $user->userInfo($_SESSION['userName']);
    }

    //Envoi le mail
    $q1 = sprintf( "SELECT * FROM transactions WHERE InvoiceNumber='%s'" ,
       mysql_real_escape_string( $_GET['ref'] )
    );
    $row = mysql_fetch_array( mysql_query( $q1 ) );

    $template = new EmailText($row['InvoiceNumber'], $row['txn_id'], $row['payment_date'], $info['id'], $row['mc_gross'], "CARTE", $row['payment_type'], "413-1,414-2" , "EUR");
    $template->computeSubject();
    $template->computeBody();

}
else
{
   echo 'Ref Inexistante !';
}




$pdf = new tFPDF();
$pdf->AddPage();

// Ajoute une police Unicode (utilise UTF-8)
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->SetFont('DejaVu','',14);

/*// Charge une chaîne UTF-8 à partir d'un fichier
$txt = file_get_contents('HelloWorld.txt');
$pdf->Write(8,$txt);*/

// Sélectionne une police standard (utilise windows-1252)
//$pdf->SetFont('Arial','',14);
$pdf->Ln(10);
$pdf->Write(5,$template->getSubject());
$pdf->Write(5,"\n\n\n\n");
$pdf->Write(5,$template->getBody());

//$row['product_id_array']

$pdf->Output();
?>