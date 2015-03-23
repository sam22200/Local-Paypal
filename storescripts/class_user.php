<?php

date_default_timezone_set('Europe/Paris');
class User {

  function __construct(){}

  function randomString( $len=32 ){
    // Initialise a string
    $s = '';
    // Possible characters
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for( $i=0 ; $i<$len ; $i++ ){
      // Grab a random letter for $letters
      $char = $letters[mt_rand( 0 , strlen( $letters )-1 )];
      $s .= $char; //Add it to the string
    }
    return $s;
  }

  function hash( $password , $salt , $created_at ){
    // Reverses the date and removes the dashes
    $date = sha1( strrev( (string) $created_at ) );
    // Yay! Bcrypt
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

    $salt = $this->salt(); //Generate a salt using the username provided
    $date = time();
    $password = $this->hash( $userPassword , $salt , $date ); //Hash the password with the new salt
    //The query for inserting our new user into the DB
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
    die( mysql_error() ); // Run it. If it doesn't go through stop the script and display the error.
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

    // Grabbing all the user details with this query
    $q1 = sprintf( "SELECT password, rand, created_at FROM users WHERE username='%s'" ,
            mysql_real_escape_string( $userName )
          );
    $r1 = mysql_fetch_array( mysql_query( $q1 ) );
    $ph = $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] );
    // Return whether it is true or false
    return ( $r1['password'] == $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] ) );
  }

  function setLoggedIn($userName, $email, $userPassword) {
    //This function is self explanitory :)
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $userName;
    $_SESSION['email'] = $email;
    $_SESSION['userPassword'] = $userPassword;
  }

  function isLoggedIn() {
    return ( isset( $_SESSION['loggedIn'] )
             && $_SESSION['loggedIn']
             && $this->verify( $_SESSION['userName'], $_SESSION['userPassword'] ) );
  }

  function redirectTo($page) {
    if( !headers_sent() ){
      header( 'Location: ' . $page . '.php' );
    }
    die( '<a href="'.$page.'.php">Go to '.$page.'.php</a>' );
  }

  function userInfo( $userName ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // This function returns all user details to the front end. This is to save storing it all in sessions
    $q1 = sprintf( "SELECT * FROM users WHERE username='%s'" ,
            mysql_real_escape_string( $userName )
          );
    // Fetch and Return the array
    return mysql_fetch_array( mysql_query( $q1 ) );
  }

  function userInfoId( $UID ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // This function returns all user details to the front end. This is to save storing it all in sessions
    $q1 = sprintf( "SELECT * FROM users WHERE id=%s" ,
            (int) $UID
          );
    // Fetch and Return the array
    return mysql_fetch_array( mysql_query( $q1 ) );
  }

  function logOut(){
    // If they are logged in
    if( isset( $_SESSION['loggedIn'] ) ){
      // Unset the session variables
      unset( $_SESSION['loggedIn'] , $_SESSION['userName'] , $_SESSION['email'], $_SESSION['userPassword'] );
      // Redirect to the login page
      $this->redirectTo( '../cart' );
    }
  }

  function exists( $userName ){
    // Connect to the MySQL database
    require_once "class_connexion.php";
    $connection = new createConnection();
    $connection->connectToDatabase();
    $connection->selectDatabase();

    // Checks a user exists (for the register page)
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
