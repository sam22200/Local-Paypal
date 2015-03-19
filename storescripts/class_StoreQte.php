<?php
class StoreQte {
    // Eléments de notre panier
    protected $qte413;
    protected $qte414;
    protected $qte415;
    protected $username;

    public function __construct($UID, $qteStr) {
        $this->username = $UID;

        $this->qte413 = 0;
        $this->qte414 = 0;
        $this->qte415 = 0;

        $this->storeQte($qteStr);
    }

    protected function storeQte($array){
      //Scinde la string des produits en Code et Qte
      $str = $array;
      $arrP = explode(',',$str);
      $i = 0;
      foreach($arrP as $p){
        $arr_tmp = explode('-',$p);
        $arr2[$i] = $arr_tmp;
        $i = $i+1;
      }

      foreach($arr2 as $pr){
        switch ($pr[0]) {
            case "413":
                $this->qte413 = $pr[1];
                break;
            case "414":
                $this->qte414 = $pr[1];
                break;
            case "415":
                $this->qte415 = $pr[1];
                break;
        }
      }

    }

    public function storeInBase(){

        // Connect to the MySQL database
        require_once "class_connexion.php";
        $connection = new createConnection();
        $connection->connectToDatabase();
        $connection->selectDatabase();

        $sql = mysql_query("SELECT * FROM users WHERE id='$this->username' LIMIT 1");
        while($row = mysql_fetch_array($sql)){
          $this->qte413 += $row["qte_413"];
          $this->qte414 += $row["qte_414"];
          $this->qte415 += $row["qte_415"];
        }

        //The query for inserting our new user into the DB
        $q1 = sprintf( "UPDATE users SET qte_413='%s', qte_414='%s', qte_415='%s' WHERE id='%s'" ,
                mysql_real_escape_string( $this->qte413 ) ,
                mysql_real_escape_string( $this->qte414 ) ,
                mysql_real_escape_string( $this->qte415 ) ,
                mysql_real_escape_string( $this->username )
              );
        if( mysql_query( $q1 ) )
          return mysql_insert_id();
        die( mysql_error() ); // Run it. If it doesn't go through stop the script and display the error.
        return false;
    }

    public function getQte413(){
        return $this->qte413;
    }


    public function getQte414(){
        return $this->qte414;
    }

    public function getQte415(){
        return $this->qte415;
    }

    public function getUsername(){
        return $this->username;
    }
}
?>