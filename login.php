<?php

session_start();
date_default_timezone_set('Europe/Paris');
//User class
include( 'storescripts/class_user.php' );

$user = new User();

if( $user->isLoggedIn() ){
  $user->redirectTo( 'cart' );
}

if( isset( $_SESSION['try'] ) ){
  foreach( $_SESSION['try'] as $k => $v ){
    if( $v <= $loginAttemptTime ){
      unset( $_SESSION['try'][$k] );
    }
  }
}

if( isset( $_POST['username'] ) ){
	if( $user->verify( $_POST['username'] , $_POST['password'] ) ){
		$user->setLoggedIn($_POST['username'], $_POST['password']);
    unset( $_SESSION['try'] );
		$user->redirectTo('cart');
	} else {
    $message = 'Error. Incorrect username or password.';
    $_SESSION['try'][] = time();
  }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>User.class - Login</title>
<link rel="stylesheet" type="text/css" href="css/reset.css" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
<!--WEB FONTS -->
<link href="http://fonts.googleapis.com/css?family=Lato:100&v2" rel="stylesheet" type="text/css">
<!--&&&&&&&&&-->
</head>
<body>
<div id="container">
  <h1>Login</h1>
  <div class="Sendtext">
    <form method="post">
<?php
if( isset( $ ) ){
?>
      <p><?php echo $message; ?></p>
<?php
}
if( count( $_SESSION['try'] )<$loginAttemptCount ){
?>
      <p><b>Username:</b></p>
      <input type="text" name="username" style=" margin-bottom: 10px;"/>
      <p><b>Password:</b></p>
      <input type="password" name="password" />
      </div>
      <input type="submit" value="login" class="button"/>
<?php
}else{
?>
      <p><b>You have attempted this password too many times. Please try again later.</b></p>
<?php
}
?>
    </form>
  </div>
</div>
</body>
</html>
