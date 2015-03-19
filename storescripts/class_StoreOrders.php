<?php
class StoreOrders {
    // Eléments de notre panier
    protected $username;
    protected $ref;

    public function __construct($UID, $ref) {
        $this->username = $UID;
        $this->ref = $ref;
    }

    public function storeInBase(){
        // Connect to the MySQL database
        require_once "class_connexion.php";
        $connection = new createConnection();
        $connection->connectToDatabase();
        $connection->selectDatabase();

        //The query for inserting our new user into the DB
        $q1 = sprintf( "INSERT INTO orders (username, ref) VALUES ('%s', '%s')" ,
                mysql_real_escape_string( $this->username ) ,
                mysql_real_escape_string( $this->ref )
              );
        if( mysql_query( $q1 ) )
          return mysql_insert_id();
        die( mysql_error() ); // Run it. If it doesn't go through stop the script and display the error.
        return false;
    }

    public function getRef(){
        return $this->ref;
    }

    public function getUsername(){
        return $this->username;
    }
}
?>
