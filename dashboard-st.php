<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AutoConsulta San Antonio</title>
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="js/qrcode.js"></script>
    <script type="text/javascript">
    function spinner() {
        document.getElementById('waitani').style.visibility = 'visible';
      setTimeout( spinneroff(), 2000);
      document.getElementById('contenido').style.visibility = 'hidden';
        }

    function spinneroff() {
        document.getElementById('waitani').style.visibility = 'visible';
      document.getElementById('contenido').style.visibility = 'hidden';
        }
    function print_ticket() {   
        window.print();     
    }
    </script>
    <style type="text/css"> 
        .example-print {
            display: none;
        }
        @media print {
           .example-screen {
               display: none;
            }
            .example-print {
               display: block;
            }
            img {
            height: 50%;
            width: 50%;
            }
        }
    </style>

</head>
<?php
session_start();
date_default_timezone_set("America/New_York");
/*if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 360)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in
    header('location: login-st.html');
}*/
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (isset($_POST['rut']) and isset($_POST['folio'])){
    //inicio variables de sesion con los datos recibidos
    $rutsdv = str_replace(".", "", $_POST['rut']);
    $rutsdv=str_replace("-", "", $rutsdv);
    $dv=substr($rutsdv, -1);
    $rutsdv=substr($rutsdv, 0, -1);
    $_SESSION['id_usuario']=$rutsdv;
    $_SESSION['pass_usuario']=$_POST['folio'];
    $_SESSION['valid_usuario']='SI';
    $_SESSION['emp_usuario']=5;
    unset($dv);
    }

    $Sttdocto = "ST";
    $st=$_SESSION['pass_usuario'];
    $emp=5;
    $url = "http://localhost:8080/xwabagx15JE/servlet/com.xwabagx15.webservice.awssteve?wsdl";
    $par = array(
        'Empcod' => $emp,
        'Sttdocto' => $Sttdocto,
        'Rut' => $rutsdv, 
        'Folio' => $st,
        'Xmlout' => null,
        'Errcode' => null,
        'Errdesc' => null);

    $client = new SoapClient($url); // funcion(url,opciones) si uso wsdl opciones null
    $result = $client->Execute($par);
    
?>
<?php if($result->Errcode !=0):?>
    <!-- codigo en caso de algun error de logueo -->

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Auto Consulta San Antonio</h3>
                    </div>
                    <div class="panel-body text-center">
                        <div class="alert alert-danger">
                            <?php print_r($result->Errdesc);?>
                        </div>
                        <a href="login-st.html"><button class="btn btn-outline btn-danger">Volver</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
<?php 
    class ServicioT {
        var $docto;
        var $folio;
        var $fecha;
        var $producto;
        var $stest;
        var $eventos;
    }

    $stclient = new ServicioT();
    $tmp = explode("<eventos>", $result->Xmlout);
    $stclient->eventos = $tmp[1];
    $tmp = $tmp[0];

    $tmp = explode("<stest>", $tmp);
    $stclient->stest = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<starti>", $tmp);
    $stclient->producto = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<stfec>", $tmp);
    $stclient->fecha = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<stfolio>", $tmp);
    $stclient->folio = trim(strip_tags($tmp[1]));
    $stclient->docto = trim(strip_tags($tmp[0]));

    $pd = explode("<evento>", $stclient->eventos);
    unset($pd[0]);

    class evento{
        var $fecha;
        var $hora;
        var $codigo;
        var $glosa;
        var $glosa2;
    }

    $n=0;
    foreach ($pd as $key) {
        $movimiento[$n] = new evento();
        $tmp = explode("<gloeve2>", $key);
        $movimiento[$n]->glosa2 = trim(strip_tags($tmp[1]));
        $tmp = $tmp[0];

        $tmp = explode("<gloeve>", $tmp);
        $movimiento[$n]->glosa = trim(strip_tags($tmp[1]));
        $tmp = $tmp[0];

        $tmp = explode("<codeve>", $tmp);
        $movimiento[$n]->codigo = trim(strip_tags($tmp[1]));
        $tmp = $tmp[0];

        $tmp = explode("<horst>", $tmp);
        $movimiento[$n]->hora = trim(strip_tags($tmp[1]));
        $movimiento[$n]->fecha = trim(strip_tags($tmp[0]));
        $n++;
    }
    $movimiento = array_reverse($movimiento);
?>

<body onunload="spinneroff();">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png" style="margin-top: -15px;"></a>
            </div>
            <!--subi todo esto para poder usar las funciones desde mas arriba-->
            <ul class="nav navbar-top-links navbar-right">
                <li><a href="#"><i class="fa fa-user fa-fw"></i><?php print_r($stclient->docto)?></a>
                </li>
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li><a href="index.html"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-sign-out fa-fw"></i> Salir</button></a> <!-- te cambio el link, para destruir las variables de sesion -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper" class="example-screen">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Estado de Servicio Técnico n° <?php print_r($stclient->folio) ?> </h1>
                </div>
            </div>
            <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-clock-o fa-fw"></i>Movimientos desde el <?php print_r($stclient->fecha)?> para el producto <?php print_r($stclient->producto) ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <ul class="timeline">
                            <?php
                                $n=0;
                                foreach ($movimiento as $key):
                                    if ($n%2==0):
                            ?>
                                <li>
                                    <div class="timeline-badge info">
                                        <i class="fa fa-wrench"></i>
                                    </div>
                                    <div class="timeline-panel" style="border: 1px solid #337BB8;">
                                        <div class="timeline-heading">
                                            <h4 class="timeline-title"><?php print_r($key->glosa) ?></h4>
                                            <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?php print_r($key->fecha) ?> a las <?php print_r($key->hora) ?></small>
                                            </p>
                                        </div>
                                        <div class="timeline-body">
                                            <p><?php print_r($key->glosa2) ?></p>
                                        </div>
                                    </div>
                                </li>
                                <?php else: ?>
                                <li class="timeline-inverted">
                                    <div class="timeline-badge info">
                                        <i class="fa fa-wrench"></i>
                                    </div>
                                    <div class="timeline-panel" style="border: 1px solid #337BB8;">
                                        <div class="timeline-heading">
                                            <h4 class="timeline-title"><?php print_r($key->glosa) ?></h4>
                                            <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?php print_r($key->fecha) ?> a las <?php print_r($key->hora) ?></small>
                                            </p>
                                        </div>
                                        <div class="timeline-body">
                                            <p><?php print_r($key->glosa2) ?></p>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <?php $n++; endforeach; ?>
                            </ul>
                        </div>
                        <!-- /.panel-body -->
                    </div>            
        </div>
        
        <!-- Termino de ticket -->
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
<?php endif; ?>
</html>