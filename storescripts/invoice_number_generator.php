<?php

  //Since this is an included script, it needs to be protected with direct file access, so that the public users cannot just execute this script using a web browser

  if ('invoice_number_generator.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
  die ('<h2>Direct File Access Prohibited</h2>');
  }
  else {

    function invoiceNumber(){

      //genere un numero d'invoice 1000 et 10000000000
      $numberGenerator = mt_rand(1000,10000000000);

      //genere la clé invoice de facon random
      $chars = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';

      $max = strlen($chars)-1;
      $invoiceRandom = null;
      for($i=0; $i < 15; $i++) {
        $invoiceRandom .= $chars{mt_rand(0, $max)};
      }

      //Combination des deux nombres generes
      $generatedInvoiceNumber=$numberGenerator.$invoiceRandom;
      //Sanitize
      $generatedinvoicenumber=mysql_real_escape_string($generatedinvoicenumber);

      //Check that the invoice number has not been used before
      //by comparing the generated numbers with those stored in MySQL database
      while($fetch = mysql_fetch_array(mysql_query("SELECT InvoiceNumber FROM transactions WHERE InvoiceNumber='$generatedInvoiceNumber'"))) {
        //Invoice number already exists, generate another invoice number
        $numberGenerator= mt_rand(1000,10000000000);
        $generatedInvoiceNumber=$numberGenerator.$invoiceRandom;
      }

      mysql_close();
      //retourne le resultat
      return $generatedInvoiceNumber;
    }
}

?>
