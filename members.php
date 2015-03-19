<?php

session_start();
date_default_timezone_set('Europe/Paris');
include( 'storescripts/class_user.php' );

$user = new User();

if( !$user->isLoggedIn() ){
  $user->redirectTo('signin');
} else {
  $info = $user->userInfo($_SESSION['userName']);
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Membre</title>


    <!-- Bootstrap Core CSS -->
    <link href="style/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="style/shop-homepage.css" rel="stylesheet">

        <!-- Custom CSS -->
    <link href="style/members.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    <div id="mainWrapper">
      <?php include_once("template_header.php");?>

<div class="jumbotron">

			<div id="container">
				<p>Espace Membre</p>

				<div class="row">
      <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
           <A href="edit.html" >Modifier Profil</A>

        <A href="logout.php" >[Déconnexion]</A>
       <br>
      </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >


          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $info['username']; ?></h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=100" class="img-circle"> </div>

                <!--<div class="col-xs-10 col-sm-10 hidden-md hidden-lg"> <br>
                  <dl>
                    <dt>DEPARTMENT:</dt>
                    <dd>Administrator</dd>
                    <dt>HIRE DATE</dt>
                    <dd>11/12/2013</dd>
                    <dt>DATE OF BIRTH</dt>
                       <dd>11/12/2013</dd>
                    <dt>GENDER</dt>
                    <dd>Male</dd>
                  </dl>
                </div>-->
                <div class=" col-md-9 col-lg-9 ">
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td>Département:</td>
                        <td>Développement</td>
                      </tr>
                      <tr>
                        <td>Date de Naissance</td>
                        <td>01/24/1988</td>
                      </tr>

                         <tr>
                             <tr>
                        <td>Sexe</td>
                        <td>Homme</td>
                      </tr>
                        <tr>
                        <td>Adresse </td>
                        <td>80 Rue du Taitbout, PARIS</td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><a href="mailto:<?php isset($info['mail']) ? $email = $info['mail'] : $email = 'info@support.com'; echo $email; ?>"><?php echo $email;?></a></td>
                      </tr>
                        <td>Numéro Téléphone</td>
                        <td>01-33-79-40-20(Fixe)<br><br>06-81-50-46-54(Mobile)
                        </td>
                      <tr>
                        <td>Actif depuis le:</td>
                        <td><?php echo date("Y-m-d H:i:s", $info['created_at']);?></td>
                      </tr>

                      </tr>

                    </tbody>
                  </table>

                  <a href="#" class="btn btn-primary">Convertir mes achats</a>
                  <a href="myorders.php" class="btn btn-primary">Mes achats</a>
                </div>
              </div>
            </div>
                 <div class="panel-footer">
                        <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                        <span class="pull-right">
                            <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                        </span>
                    </div>

          </div>
        </div>
      </div>
				<div class="logout"><i><a href="logout.php">[Déconnexion]</a><i></div>


			</div> <!--/Container -->


</div>

      <?php include_once("template_footer.php");?>
    </div> <!-- /Main Wrapper -->

    <!-- members -->
    <script src="js/members.js"></script>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    </body>


</html>