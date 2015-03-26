<?php

session_start();

unset($_SESSION["cart_array"]);

date_default_timezone_set('Europe/Paris');
include( 'storescripts/class_user.php' );

$user = new User();

$user->logOut();

?>