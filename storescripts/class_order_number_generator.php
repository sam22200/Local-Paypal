<?php

  class orderNumberGenerator {

    var $number;

    function __construct(){}

    protected function setNumber($n){
      $this->number = $n;
    }

    public function getNumber(){
      return $this->number;
    }

    public function computeOrderNumber() {
      // Connect to the MySQL database
      require_once "class_connexion.php";
      $connection = new createConnection();
      $connection->connectToDatabase();
      $connection->selectDatabase();

      //genere un numero d'invoice 1000 et 10000000000
      $numGenerated = "PXO";

      //genere la clé invoice de facon random
      $chars = '0123456789';

      $max = strlen($chars)-1;
      $numRandom = null;
      for($i=0; $i < 8; $i++) {
        $numRandom .= $chars{mt_rand(0, $max)};
      }

      //Combination des deux nombres generes
      $numGenerated .= $numRandom;
      //Sanitize
      $numGenerated = mysql_real_escape_string($numGenerated);

      //Check that the invoice number has not been used before
      //by comparing the generated numbers with those stored in MySQL database
      while($fetch = mysql_fetch_array(mysql_query("SELECT txn_id FROM transactions WHERE txn_id='$numGenerated'"))) {
        //Invoice number already exists, generate another invoice number
        $this->computeOrderNumber();
      }

      $connection->closeConnection();
      //retourne le resultat
      $this->setNumber($numGenerated);
    }

}
?>
