<?php

date_default_timezone_set('Europe/Paris');
class User {

  function __construct(){}

  function randomString( $len=32 ){
    // Initialise string
    $s = '';
    // Possibles carac
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for( $i=0 ; $i<$len ; $i++ ){
      // Choisi une lettre au hasard dans $letters
      $char = $letters[mt_rand( 0 , strlen( $letters )-1 )];
      $s .= $char; //Ajoute a la string
    }
    return $s;
  }

  function hash( $password , $salt , $created_at ){
    // renverse la date et enleve les /
    $date = sha1( strrev( (string) $created_at ) );
    // Bcrypt
    return crypt($salt . $password . $date . $salt , '$2a$12$' . $salt);
  }

  function salt(){
    $firstSalt = substr( str_replace( '+' , '.' , base64_encode( sha1( microtime( true ) , true ) ) ) , 0 , 22 );
    return $firstSalt;
  }

  function register( $userName , $email,  $userPassword ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    if( $this->exists( $userName ) )
      return false;

    $salt = $this->salt(); //Génère un grain avec l'username
    $date = time();
    $password = $this->hash( $userPassword , $salt , $date ); //Hash le mdp avec le grain
    //Insere un user en BD
    $q1 = sprintf( "INSERT INTO users (username, password, rand, created_at, mail) VALUES ('%s', '%s', '%s', '%s', '%s')" ,
            mysql_real_escape_string( $userName ) ,
            mysql_real_escape_string( $password ) ,
            mysql_real_escape_string( $salt ) ,
            mysql_real_escape_string( $date ),
            mysql_real_escape_string( $email )
          );
    if( mysql_query( $q1 ) ){
      return mysql_insert_id();
    } else {
    die( mysql_error() ); // ERREUR
    return false;
    }
  }

  function update( $userName , $email, $oldPassword , $newPassword ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    if( !$this->exists( $userName ) )
      return false;
    $q1 = sprintf( "SELECT password, rand, created_at FROM users WHERE username='%s'" ,
            mysql_real_escape_string( $userName )
          );
    $r1 = mysql_fetch_array( mysql_query( $q1 ) );
    $oldHashDB = $this->hash( $r1['password'] , $r1['rand'] , $r1['created_at'] );
    $oldHashIn = $this->hash( $oldPassword , $r1['rand'] , $r1['created_at'] );
    if( $oldHashDB == $oldHashIn ){
      $salt = $this->salt();
      $newHash = $this->hash( $newPassword , $salt , $r1['created_at'] );
      $q2 = sprintf( "UPDATE users SET password='%s', rand='%s' WHERE username='%s'" ,
              mysql_real_escape_string( $newHash ) ,
              mysql_real_escape_string( $salt ) ,
              mysql_real_escape_string( $userName )
            );
      if( mysql_query( $q2 ) ){
        setLoggedIn( $userName , $email, $newPassword );
        return true;
      }
    }
  }

  function verify( $userName , $userPassword ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // Prend certaines infos de l'user
    $q1 = sprintf( "SELECT password, rand, created_at FROM users WHERE username='%s'" ,
            mysql_real_escape_string( $userName )
          );
    $r1 = mysql_fetch_array( mysql_query( $q1 ) );
    $ph = $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] );
    // Retourne si oui ou non ça match
    return ( $r1['password'] == $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] ) );
  }

  function setLoggedIn($userName, $email, $userPassword) {
    // UPDATE la session
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $userName;
    $_SESSION['email'] = $email;
    $_SESSION['userPassword'] = $userPassword;
  }

  //Verifie si on est loggé
  function isLoggedIn() {
    return ( isset( $_SESSION['loggedIn'] )
             && $_SESSION['loggedIn']
             && $this->verify( $_SESSION['userName'], $_SESSION['userPassword'] ) );
  }

  //Redirige sur une page
  function redirectTo($page) {
    if( !headers_sent() ){
      header( 'Location: ' . $page . '.php' );
    }
    die( '<a href="'.$page.'.php">Go to '.$page.'.php</a>' );
  }

  //Retourne les infos d'un user
  function userInfo( $userName ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // Retourne toutes les infos pour pas le faire en session
    $q1 = sprintf( "SELECT * FROM users WHERE username='%s'" ,
            mysql_real_escape_string( $userName )
          );

    return mysql_fetch_array( mysql_query( $q1 ) );
  }

  function userInfoId( $UID ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    $q1 = sprintf( "SELECT * FROM users WHERE id=%s" ,
            (int) $UID
          );

    return mysql_fetch_array( mysql_query( $q1 ) );
  }

  //Deconnection
  function logOut(){
    // Si l'user est connecté
    if( isset( $_SESSION['loggedIn'] ) ){
      // Unset les variables de session
      unset( $_SESSION['loggedIn'] , $_SESSION['userName'] , $_SESSION['email'], $_SESSION['userPassword'] );
      // Redirection
      $this->redirectTo( '../cart' );
    }
  }

  function exists( $userName ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // Verifie si l'user existe en BD
    $q1 = sprintf( "SELECT username FROM users WHERE username = '%s'" ,
            mysql_real_escape_string( $userName )
          );
    return (bool) mysql_num_rows( mysql_query( $q1 ) );
  }

  function search( $field , $term ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    $sql_field = false;

    switch( $field ){
      case 'id' :
        $sql_field = 'id';
        break;
      case 'username' :
        $sql_field = 'username';
        break;
    }
    if( !$sql_field )
      return false;
    $q1 = sprintf( "SELECT * from users WHERE %s LIKE '%%%s%%'" ,
            mysql_real_escape_string( $term )
          );
    $r1 = mysql_query( $q1 );
    if( !mysql_num_rows( $r1 ) )
      return false;
    return $r1;
  }


  function string_shorten( $text , $len ){
    // Strip any linebreaks or multiple-spaces
    $text = preg_replace( array( "/\n|\r/" , '\s\s+' ) , ' ' , $text );
    // Split the text using the wordwrap() function
    $lines = explode( "\n" , wordwrap( $text , $len ) );
    // Get the First Line and add continuation ... sign
    return $lines[0].'...'; //Return the value
  }

  function checkLevel( $i ){
    $levels = array( 'Normal' , 'Moderator' , 'Admin' );
    return $levels[$i];
  }

}
