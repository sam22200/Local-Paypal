<?php

class createConnection //Class pour la connexion avec la BD
{
    var $host="127.0.0.1";
    var $username="sam22200";
    Var $password="22200sam";
    var $database="transac";
    var $myconn;

    // Creer la connexion avec la BD
    function connectToDatabase()
    {
        $conn= mysql_connect($this->host,$this->username,$this->password);
        if(!$conn)// Teste la connexion
        {
            die ("Connexion à la Base de Données impossible.");
        }
        else
        {
            $this->myconn = $conn;
            //echo "Connexion etablie.";
        }

        return $this->myconn;
    }

    // Selectionne la BD
    function selectDatabase()
    {
        mysql_select_db($this->database);
        if(mysql_error())
        {
            //echo "Ne trouve pas la BD :  ".$this->database;
        }
         //echo "DB sélectionnée..";
    }

    // Ferme la connexion
    function closeConnection()
    {
        if (isset($this->myconn) && is_resource($this->myconn)) {
            mysql_close($this->myconn);
        } else {
            mysql_close();
        }
        //echo "Connexion fermée.";
    }
}
?>