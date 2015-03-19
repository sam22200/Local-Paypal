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
?>



<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identification</title>


    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="style/shop-homepage.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
      <?php include_once("template_header.php");?>

  <div class="jumbotron">
    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">S'identifier</div>
                        <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Mot de passe oublié?</a></div>
                    </div>

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                        <form id="loginform" class="form-horizontal" role="form" method="post">

                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                                    </div>

                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>



                            <div class="input-group">
                                      <div class="checkbox">
                                        <label>
                                          <input id="login-remember" type="checkbox" name="remember" value="1"> Se souvenir
                                        </label>
                                      </div>
                                    </div>


                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                      <input id="btn-login" class="btn btn-success" type="submit" name="button" value="S'identifier  " />
                                      <a id="btn-fblogin" href="#" class="btn btn-primary">S'identifier avec Facebook</a>

                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                            Toujours pas de compte!
                                        <a href="register.php" onClick="">
                                            S'enregistrer ici
                                        </a>
                                        </div>
                                    </div>
                                </div>
                            </form>



                        </div>
                    </div>
        </div>

    </div>

  </div> <!-- Jumbotron -->

      <?php include_once("template_footer.php");?>
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


</body>

</html>