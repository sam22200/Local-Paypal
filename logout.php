<?php

session_start();
date_default_timezone_set('Europe/Paris');
include( 'storescripts/class_user.php' );

$user = new User();

$user->logOut();
?>