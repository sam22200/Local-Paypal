<?php
date_default_timezone_set('Europe/Paris');
/* USER.CLASS.PHP CONFIG - CREATED BY 'Poppins' 2011
-----------------------------------------------------
--This is used to set the database details for user.class.php
--to access and use. They must be set correctly and then stored
--in the same directory as user.class.php.
--
--The names of the variables are pretty self explanitory.
-----------------------------------------------------
*/

//Database creditentials (EDIT THESE)
$dbHost     = '127.0.0.1';
$dbDatabase = 'transac';
$dbUsername = 'sam22200';
$dbPassword = '22200sam';

$loginAttemptCount = 3;
$loginAttemptTime  = strtotime( '-2 minutes' );

//Database connect (Most of the time these can be kept the same)
$dbCon = mysql_pconnect( $dbHost , $dbUsername , $dbPassword ) or die( mysql_error() );
mysql_select_db( $dbDatabase , $dbCon );
?>