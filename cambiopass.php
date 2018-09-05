<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AutoConsulta</title>
    <link rel="shorcut icon" href="img/sanantonio.ico">

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body style="font-size: 24px;">
<?php session_start();
date_default_timezone_set("America/New_York");
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 360)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in
    header('location: login.html');
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
?>
    <div id="wrapper">

        <!-- Navigation -->
       <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png"></a>
            </div>
            <!-- /.navbar-header -->
            <!-- Genrera valores -->
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a href="dashboard.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-dashboard fa-fw"></i> Inicio</button></a>
                </li>
                <li>
                    <div class="dropdown">
                        <button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-navicon"></i> Cuenta <i class="fa fa-angle-down"></i></button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  
                            <a class="dropdown-item" href="eecc.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-calendar-check-o fa-fw"></i> Estado de cuenta</button></a>

                            <a class="dropdown-item" href="cpp.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-bar-chart-o fa-fw"></i> Cuotas Pendientes</button></a>

                            <a class="dropdown-item" href="mys.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-folder fa-fw"></i> Movimientos</button></a>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="pass.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-lock fa-fw"></i> Contraseña</button></a>
                </li>
                <li>
                    <a href="salir.php"><button type="button" class="btn btn-primary btn-lg btn-block btn-nav-bar"><i class="fa fa-sign-out fa-fw"></i> Salir</button></a> <!-- te cambio el link, para destruir las variables de sesion -->
                </li>
            </ul>
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Cambio de Contraseña</h1>
                </div>
            </div>
<?php
    $oldpass=$_POST['oldpass'];
    $newpass=$_POST['newpass'];
    $newpass1=$_POST['newpass1'];
?>
<!--Validar nueva pass-->
<?php if($newpass != $newpass1):?>
            <div class="row text-center">
                <div class="col-lg-8">
                    <div class="alert alert-danger">
                        ERROR: Nueva contraseña no coincide
                    </div>
                    <a href="pass.php"><button type="button" class="btn btn-outline btn-danger">Volver</button></a>
                </div>
            </div>
<!--Si coinciden las nuevas credenciales-->
<?php else:
    $emp=1;
    $validate='SI';
    $url="http://localhost:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awscambiopass?wsdl";
    $par = array(
        'Empcod' => $_SESSION['emp_usuario'] ,
        'Rut' => $_SESSION['id_usuario'],
        'Pass' => $oldpass,
        'Validar' => $validate,
        'Nueva' => $newpass,
        'Errcode' => null,
        'Errdesc' => null);
    $client = new SoapClient($url);
    $result = $client->Execute($par);
?>
            <!-- /.row -->
    <?php if ($result->Errcode == '0'): ?>
            <div class="row text-center">
                <div class="col-lg-8">
                    <div class="alert alert-success text-center">
                        Se ha modificado correctamente la contraseña
                    </div>
                    <a href="salir.php"><button type="button" class="btn btn-outline btn-success">Volver a Login</button></a>
                </div>
            </div>
    <?php else:?>
            <div class="row text-center">
                <div class="col-lg-8">
                    <div class="alert alert-danger text-center">
                        <?php print_r($result->Errdesc);?>
                    </div>
                    <a href="pass.php"><button type="button" class="btn btn-outline btn-danger">Volver</button></a>
                </div>
            </div>
    <?php endif; ?>
        </div>
<?php endif;?>
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="vendor/raphael/raphael.min.js"></script>
    <script src="vendor/morrisjs/morris.min.js"></script>
    <script src="data/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
