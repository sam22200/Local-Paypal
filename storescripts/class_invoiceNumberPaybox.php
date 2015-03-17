<?php
  require_once 'i_invoiceNumberGenerator.php';
  class invoiceNumberPaybox implements iInvoiceNumberGenerator {

    var $invoiceNumber;
    var $listProducts;
    var $finalStringComputed;

    function __construct(){
      $this->computeInvoiceNumber();
    }

    protected function computeInvoiceNumber() {
      // Connect to the MySQL database
      require_once "class_connexion.php";
      $connection = new createConnection();
      $connection->connectToDatabase();
      $connection->selectDatabase();

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
      $generatedInvoiceNumber = 0;
      $generatedInvoiceNumber=$numberGenerator.$invoiceRandom;
      //Sanitize
      $generatedInvoiceNumber=mysql_real_escape_string($generatedInvoiceNumber);

      //Check that the invoice number has not been used before
      //by comparing the generated numbers with those stored in MySQL database
      while($fetch = mysql_fetch_array(mysql_query("SELECT InvoiceNumber FROM transactions WHERE InvoiceNumber='$generatedInvoiceNumber'"))) {
        //Invoice number already exists, generate another invoice number
        $numberGenerator= mt_rand(1000,10000000000);
        $generatedInvoiceNumber=$numberGenerator.$invoiceRandom;
      }

      $connection->closeConnection();
      //retourne le resultat
      $this->invoiceNumber = $generatedInvoiceNumber;
    }

    public function setListProduct($list){
      $this->listProducts = $list;
      $this->mergeString();
    }

    protected function mergeString(){
      $this->finalStringComputed = "$this->listProducts|".$this->invoiceNumber;
    }

    public function getInvoiceNumber(){
      return $this->finalStringComputed;
    }
}
?>
