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
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/animar.js"></script>
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
error_reporting(0);
session_start();
date_default_timezone_set("America/New_York");
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 360)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in
    header('location: login.html');
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (isset($_POST['rut']) and isset($_POST['pass'])){
    //inicio variables de sesion con los datos recibidos
    $rutsdv = str_replace(".", "", $_POST['rut']);
    $rutsdv=str_replace("-", "", $rutsdv);
    $dv=substr($rutsdv, -1);
    $rutsdv=substr($rutsdv, 0, -1);
    $_SESSION['id_usuario']=$rutsdv."-".$dv;
    $_SESSION['pass_usuario']=$_POST['pass'];
    $_SESSION['valid_usuario']='SI';
    $_SESSION['emp_usuario']=1;
    unset($rutsdv);
    unset($dv);

  }
//saque todo esto del if, para poder usarlo sin necesidad de POST
    $url = "http://localhost:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awssal?wsdl";
    $par = array(
      'Empcod' => $_SESSION['emp_usuario'],
      'Rut' => $_SESSION['id_usuario'],
      'Pass' => $_SESSION['pass_usuario'],
      'Validar' => $_SESSION['valid_usuario'],
      'Xml' => null,
      'Errcode' => null,
      'Errdesc' => null);
    $client = new SoapClient($url); // funcion(url,opciones) si uso wsdl opciones null
    $result = $client->Execute($par);
    $tag = array(
        '0' => "<glosa2>",
        '1' => "<glosa1>",
        '2' => "<barra>",
        '3' => "<monto4>",
        '4' => "<fecha4>",
        '5' => "<monto3>",
        '6' => "<fecha3>",
        '7' => "<monto2>",
        '8' => "<fecha2>",
        '9' => "<monto1>",
        '10' => "<fecha1>",
        '11' => "<monultfacturacion>",
        '12' => "<ultfacturacion>",
        '13' => "<vencido>",
        '14' => "<mesvencido>",
        '15' => "<disponible>",
        '16' => "<deuda>",
        '17' => "<credito>",
        '18' => "<diapago>",
        '19' => "<nombres>",
        '20' => "<rut>",
        '21' => "<hora>",
        '22' => "<fecha>",
        '23' => "<titulo>"
    );
    //hasta aqui
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
                        <a href="login.html"><button class="btn btn-outline btn-danger">Volver</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

<?php
  // Si la contraseña es menor a 5 caracteres
  if (strlen($_SESSION['pass_usuario']) < 5) {
    header('location: pass.php');
  }
?>
<body onunload="spinneroff();" onload="update_qrcode();">
    <!-- <div id="wrapper">  Ese id ya estaba -->
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png" style="margin-top: -15px;"></a>
            </div>
            <!--subi todo esto para poder usar las funciones desde mas arriba-->
            <?php
                $tmp = $result->Xml;
                $n=0;
                //crea arreglo con los datos del cliente
                foreach ($tag as $valor) {
                        $tmp1 = explode($valor, $tmp);
                        if (! isset($tmp1[1])) { //si la variable no está definida
                            $tmp1[1] = null;
                        }
                        $tag[$n] = trim(strip_tags($tmp1[1])); //limpia espacios y tags
                        $tmp = $tmp1[0];
                        $n++;
                }
                //separa nombre
                $name = explode(" ", $tag[19]);
                //resetear a moneda chilena
                $_SESSION['nombre_usuario']=ucfirst(strtolower($name[0])); //esto es para ocupar la variable de sesion y saber si se destruye o no
                function moneda_chilena($numero){
                    $numero = (string)$numero;
                    $puntos = floor((strlen($numero)-1)/3);
                    $tmp = "";
                    $pos = 1;
                    for($i=strlen($numero)-1; $i>=0; $i--){
                        $tmp = $tmp.substr($numero, $i, 1);
                        if($pos%3==0 && $pos!=strlen($numero))
                        $tmp = $tmp.".";
                        $pos = $pos + 1;
                    }
                    return strrev($tmp);
                }
                $_SESSION['qr'] =str_replace("*", "", $tag[2]) ;
            ?>
            <!-- /.navbar-header -->
            <!-- Genrera valores -->

            <ul class="nav navbar-top-links navbar-right">
                <li><a href="#"><i class="fa fa-user fa-fw"></i><?php print_r($_SESSION['id_usuario'])?></a>
                </li>
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="dashboard.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-dashboard fa-fw"></i> Inicio</button></a>
                        </li>
                        <li>
                            <a href="eecc.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-calendar-check-o fa-fw"></i> Estado de Cuenta</button></a>

                        </li>
                        <li>
                            <a href="cpp.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-bar-chart-o fa-fw"></i> Cuotas pendientes</button></a>

                        </li>
                        <li>
                            <a href="mys.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-folder fa-fw"></i> Movimientos</button></a>

                        </li>
                        <!--
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Estado de cuenta</a>
                        </li>
                        -->
                        <li>
                            <a href="pass.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-lock fa-fw"></i> Contraseña</button></a>

                        </li>
                        <li><a href="salir.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-sign-out fa-fw"></i> Salir</button></a> <!-- te cambio el link, para destruir las variables de sesion -->
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
                    <h1 class="page-header">Hola! <?php print_r($_SESSION['nombre_usuario']);?> </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-dollar fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php print_r(moneda_chilena($tag[15]));?></div>
                                    <div>Cupo Disponible</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-credit-card fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php print_r(moneda_chilena($tag[17]));?></div>
                                    <div>Cupo Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-money fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <?php if($tag[13] == "0"): ?> <!-- Si no tiene monto vencido -->
                                    <div class="huge"><?php print_r(moneda_chilena($tag[9]));?></div>
                                    <div> <!-- muestra el siguiente monto a vencer -->
                                        <?php if($tag[9] != 0): ?> <!-- si el siguiente monto no es 0 muestra la fecha -->
                                        Deuda a pagar al <?php print_r($tag[10]);?>
                                        <?php endif;?>
                                    </div>
                                    <?php else: ?>
                                        <div class="huge"><?php print_r(moneda_chilena($tag[13]));?></div>
                                        <div>Monto Vencido desde <?php print_r($tag[14]);?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-area-chart fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php print_r(moneda_chilena($tag[16]));?></div>
                                    <div>Deuda Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"> Próximos vencimientos</div>
                        <div class="panel-body">
                            <?php if($tag[3] == 0 AND $tag[5] == 0 AND $tag[7] == 0 AND $tag[9] == 0):?>
                                <div class="alert alert-success">
                                No registra próximos vencimientos
                                </div>
                            <?php else:?>
                            <div class="table-responsible">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($tag[13] != 0): // si tiene deuda?>
                                         <tr>
                                            <td><?php print_r($tag[10])?></td>
                                            <td>$<?php print_r(moneda_chilena($tag[9]))?> </td>
                                        </tr>
                                    <?php endif;?>
                                    <?php if($tag[7] != 0): ?>
                                        <tr>
                                            <td><?php print_r($tag[8])?></td>
                                            <td>$<?php print_r(moneda_chilena($tag[7]))?> </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($tag[5] !=0):?>
                                        <tr>
                                            <td><?php print_r($tag[6])?></td>
                                            <td>$<?php print_r(moneda_chilena($tag[5]))?> </td>
                                        </tr>
                                    <?php endif;?>
                                    <?php if($tag[3] != 0):?>
                                        <tr>
                                            <td><?php print_r($tag[4])?></td>
                                            <td>$<?php print_r(moneda_chilena($tag[3]))?> </td>
                                        </tr>
                                    <?php endif;?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Información adicional
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info">
                                Su dia de pago registrado en nuestro sistema es el <?php print_r($tag[18]) ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12 text-center">
                    <button type="button" class="btn btn-primary btn-lg" onclick="print_ticket();animar()"><i class="fa fa-print"></i> Imprimir Ticket</button> <!-- con este boton quiero q llame a la funcion, pero no hace ná :c -->
                </div>
            </div>
        </div>
        <!-- Dibujo del ticket -->
        <div class="example-print">
            <div class="row" style="font-size: 12px;">
                <div class="col-md-2">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center" >
                            <img src="img/Logo-Autoconsulta.png">
                        </div>
                        <div class="panel panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th>Nombre:</th>
                                        <td><?php print_r($tag[19])?></td>
                                    </tr>
                                    <tr>
                                        <th>Rut:</th>
                                        <td><?php print_r($tag[20])?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Cupo Disponible</th>
                                        <td>$<?php print_r(moneda_chilena($tag[15])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Deuda Total</th>
                                        <td>$<?php print_r(moneda_chilena($tag[16]));?></td>
                                    </tr>
                                    <tr>
                                        <th>Cupo Total</th>
                                        <td>$<?php print_r(moneda_chilena($tag[17]));?></td>
                                    </tr>
                                    <tr>
                                        <th>Dia de pago</th>
                                        <td><?php print_r($tag[18])?></td>
                                    </tr>
                                    <?php if ($tag[13] == "0"): ?>
                                        <?php if ($tag[9] == 0): ?>
                                            <tr><th>No registra deuda</th>
                                                <td>-</td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <th>Deuda a pagar al <?php print_r($tag[10])?></th>
                                                <td>$<?php print_r(moneda_chilena($tag[9])) ?></td>
                                            </tr>
                                        <?php endif;?>
                                    <?php else: ?>
                                        <tr>
                                            <th>Monto vencido desde <?php print_r($tag[14]) ?></th>
                                            <td>$<?php print_r(moneda_chilena($tag[13])) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <p><strong>Próximos Vencimientos</strong></p>
                            <?php if($tag[3] == 0 AND $tag[5] == 0 AND $tag[7] == 0 AND $tag[9] == 0):?>
                                <p>No registra próximos vencimientos</p>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <?php if ($tag[13] != 0):?>
                                                <td><?php print_r($tag[10])?></td>
                                            <?php endif; ?>
                                            <?php if ($tag[7] != 0):?>
                                                <td><?php print_r($tag[8])?></td>
                                            <?php endif; ?>
                                            <?php if ($tag[5] != 0):?>
                                                <td><?php print_r($tag[6])?></td>
                                            <?php endif; ?>
                                            <?php if ($tag[3] != 0):?>
                                                <td><?php print_r($tag[4])?></td>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($tag[13] != 0):?>
                                            <td>$<?php print_r(moneda_chilena($tag[9]))?></td>
                                        <?php endif; ?>
                                        <?php if ($tag[7] != 0):?>
                                            <td>$<?php print_r(moneda_chilena($tag[7]))?></td>
                                        <?php endif; ?>
                                        <?php if ($tag[5] != 0):?>
                                            <td>$<?php print_r(moneda_chilena($tag[5]))?></td>
                                        <?php endif; ?>
                                        <?php if ($tag[3] != 0):?>
                                            <td>$<?php print_r(moneda_chilena($tag[3]))?></td>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                            </div>

                            <br>
                            <div style="margin-top: -60px;">
                                <form style="visibility: hidden;">
                                <textarea name="msg"><?php print_r($_SESSION['qr']) ?></textarea>
                                </form>
                            </div>
                            <div class="text-center" id="qr"></div>
                            <p class="text-center"><?php print_r($tag[2])?></p>
                            <p class="text-center"><?php print_r($tag[1])?></p>
                            <p class="text-center">Comprobante válido solo por el día</p>
                            <?php $hoy = getdate(); ?>
                            <p class="text-center"><?php echo $hoy['mday']."/".$hoy['mon']."/".$hoy['year']; ?> </p>
                        </div>
                    </div>
                </div>
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
