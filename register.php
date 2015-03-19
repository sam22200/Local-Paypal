<?php
session_start();
date_default_timezone_set('Europe/Paris');
//User class
include( 'storescripts/class_user.php' );
$user = new User();

if( $user->isLoggedIn() ){
  $user->redirectTo( 'cart' );
}

if( isset( $_POST['username'] ) ){
  // If register is successful.
    if( $user->register( $_POST['username'] , $_POST['password'] ) ){
        $message = 'Inscription effectuée ! Identifiez vous <a href="signin.php">ici</a>';
        $registered = TRUE;
    } else {
        $message = 'Désolé, cet Username est déjà utilisé. Réessayez !';
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
    <title>Enregistrement</title>


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
    <?php
if( isset( $message ) ){
  echo '<p>'.$message.'</p>';
}
if( !isset( $registered ) ){
?>

        <div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">S'enregistrer</div>
                            <div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="signin.php" onclick="">S'identifier</a></div>
                        </div>
                        <div class="panel-body" >
                            <form id="signupform" class="form-horizontal" role="form" method="post">

                                <div id="signupalert" style="display:none" class="alert alert-danger">
                                    <p>Erreur:</p>
                                    <span></span>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">Email</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="email" placeholder="Email Address">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="firstname" class="col-md-3 control-label">Nom utilisateur</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="username" placeholder="Username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-md-3 control-label">Mot de passe</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" name="password" placeholder="Password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="icode" class="col-md-3 control-label">Invitation Code</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="icode" placeholder="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <!-- Button -->
                                    <div class="col-md-offset-3 col-md-9">
                                        <input id="btn-signup" class="btn btn-info" type="submit" name="button" value=" S'inscrire" />
                                        <span style="margin-left:8px;">or</span>
                                    </div>
                                </div>

                                <div style="border-top: 1px solid #999; padding-top:20px"  class="form-group">

                                    <div class="col-md-offset-3 col-md-9">
                                        <button id="btn-fbsignup" type="button" class="btn btn-primary"><i class="icon-facebook"></i>   S'inscrire avec Facebook</button>
                                    </div>

                                </div>



                            </form>
                         </div>
                    </div>
        </div><!-- signupbox -->

<?php
}
?>

    </div><!-- container -->


  </div> <!-- Jumbotron -->

      <?php include_once("template_footer.php");?>
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


</body>

</html>