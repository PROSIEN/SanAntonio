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
    <?php
        error_reporting(0);
        session_start();
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 45)) {
            // last request was more than 45 segundos
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in
            header('location: login.html');
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
    ?>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png"></a>
                <br><br>
            </div>
            <!-- /.navbar-header -->
            <!-- Genrera valores -->
            <ul class="nav navbar-top-links navbar-right ">
                <li>
                  <a href="ticket-eecc.php"><button type="button" class="btn btn-info btn-lg btn-block btn-nav-bar"><i class="fa fa-print"></i> Imprimir Ticket</button></a> <!-- con este boton quiero q llame a la funcion, pero no hace ná :c -->
                </li>
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
<?php
    //resetear a moneda chilena
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

    $url = "http://localhost:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awsecu?wsdl";
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

    class cliente{
        var $titulo;
        var $rut;
        var $nombres;
        var $tarjeta;
        var $fecfacturacion;
        var $credito;
        var $disponible;
        var $atrazado;
        var $pagarhasta;
        var $montoapagar;
        var $transacciones;
        var $pagos;
        var $comprasavances;
        var $cargosyvctos;
        var $caepre;
        var $caecompra;
        var $prox4vencimientos;
        var $barra;
        var $glosa1;
        var $glosa2;
    }

    $var_client = new cliente();
    $tmp = explode("<glosa2>", $result->Xml);
    $var_client->glosa2 = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<glosa1>", $tmp);
    $var_client->glosa1 = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<barra>", $tmp);
    $var_client->barra = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<proximos4vencimientos>", $tmp);
    $var_client->prox4vencimientos = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<caecompra>", $tmp);
    $var_client->caecompra = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<caepre>", $tmp);
    $var_client->caepre = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("cargosyvctos>", $tmp);
    $var_client->cargosyvctos = "$".moneda_chilena(trim(strip_tags($tmp[1])));
    $tmp = $tmp[0];

    $tmp = explode("<comprasavances>", $tmp);
    $var_client->comprasavances ="$".moneda_chilena(trim(strip_tags($tmp[1])));
    $tmp = $tmp[0];

    $tmp = explode("<pagos>", $tmp);
    $var_client->pagos ="$".moneda_chilena(trim(strip_tags($tmp[1])));
    $tmp = $tmp[0];

    $tmp = explode("<Transacciones>", $tmp);
    $var_client->transacciones = trim($tmp[1]);
    $tmp = $tmp[0];

    $tmp = explode("<montoapagar>", $tmp);
    $var_client->montoapagar ="$".moneda_chilena(trim(strip_tags($tmp[1])));
    $tmp = $tmp[0];

    $tmp = explode("<pagarhasta>", $tmp);
    $var_client->pagarhasta = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<atrazado>", $tmp);
    $var_client->atrazado = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<disponible>", $tmp);
    $var_client->disponible = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<credito>", $tmp);
    $var_client->credito = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<fecfacturacion>", $tmp);
    $var_client->fecfacturacion = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<tarjeta>", $tmp);
    $var_client->tarjeta = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<Nombres>", $tmp);
    $var_client->nombres = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<rut>", $tmp);
    $var_client->rut = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<titulo>", $tmp);
    $var_client->titulo = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp=explode("<Transaccion>", $var_client->transacciones);
    unset($tmp[0]);

    $i=0;
    foreach ($tmp as $key) {
        $movimientos=explode("<Movimientos>", $key);
        $titulo[$i]=trim(strip_tags($movimientos[0]));
        $mov[$i]=$movimientos[1];
        $i++;
        unset($movimientos);
    }

    $n=0;
    foreach ($mov as $key) {
        $trx1[$n] = explode("<Movimiento>", $key);
        unset($trx1[$n][0]);
        $n++;
    }

    $trx = array();
    foreach ($trx1 as $key) {
        $trx = array_merge($trx,$key);
    }

    class movimiento{
        var $fecha;
        var $trx;
        var $des;
        var $ndocto;
        var $cuota;
        var $monto;
    }

    $n=0;
    foreach ($trx as $key ) {
        $tmp = explode("<monto>", $key);
        $movimientos[$n] = new movimiento();
        $movimientos[$n]->monto="$".moneda_chilena(trim(strip_tags($tmp[1])));
        $tmp1 = explode("<couta>", $tmp[0]);
        if(!isset($tmp1[1])) $tmp1[1] = null;
        $movimientos[$n]->cuota = trim(strip_tags($tmp1[1]));
        $tmp = explode("<nrodocto>", $tmp1[0]);
        $movimientos[$n]->ndocto = trim(strip_tags($tmp[1]));
        $tmp1 = explode("<desc>", $tmp[0]);
        $movimientos[$n]->des = trim(strip_tags($tmp1[1]));
        $tmp = explode("<trx>", $tmp1[0]);
        $movimientos[$n]->trx = trim(strip_tags($tmp[1]));
        $movimientos[$n]->fecha = trim(strip_tags($tmp[0]));
        $n++;
    }

?>

        <div id="page-wrapper" style="margin-left: 75px; margin-right: 75px;">
            <?php if(!isset($movimientos)): ?>
                <div class="row">
                    <br><br><br>
                    <div class="alert alert-success text-center">
                        No registra estado de cuenta para este periodo
                    </div>
                </div>
            <?php else: ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Estado de cuenta tarjeta de crédito<br>
                        <small>La información mostrada a continuación corresponde hasta la fecha de facturación</small>
                    </h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Estado de Cuenta emitido el <?php print_r($var_client->fecfacturacion) ?> </h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Rut Titular</th>
                                            <td><?php print_r($var_client->rut)?></td>
                                        </tr>
                                        <tr>
                                            <th>Nombre Titular</th>
                                            <td><?php print_r($var_client->nombres)?></td>
                                        </tr>
                                        <tr>
                                            <th>Numero Tarjeta</th>
                                            <td><?php printf($var_client->tarjeta)?></td>
                                        </tr>
                                        <tr>
                                            <th>CAE Prepago</th>
                                            <td><?php print_r($var_client->caepre)?>%</td>
                                        </tr>
                                        <tr>
                                            <th>CAE Compra</th>
                                            <td><?php print_r($var_client->caecompra)?>%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Resumen de Cuenta</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                Cancelar Antes de
                                            </div>
                                            <div class="panel-body">
                                                <div class="huge"><?php print_r($var_client->pagarhasta)?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                Monto total
                                            </div>
                                            <div class="panel-body">
                                                <div class="huge"><?php print_r($var_client->montoapagar)?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p>Transacciones del periodo</p>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Tipo</th>
                                                    <th>Descripcion</th>
                                                    <th>N° Documento</th>
                                                    <th>Cuota</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($movimientos as $key):?>
                                                <tr>
                                                    <td><?php print_r($key->fecha) ?></td>
                                                    <td><?php print_r($key->trx) ?></td>
                                                    <td><?php print_r($key->des) ?></td>
                                                    <td><?php print_r($key->ndocto) ?></td>
                                                    <td><?php print_r($key->cuota) ?></td>
                                                    <td><?php print_r($key->monto) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p>Movimientos del periodo de Facturación</p>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Pagos</th>
                                                    <th>Compras/Avances</th>
                                                    <th>Cargos y Vencimientos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php print_r($var_client->pagos) ?></td>
                                                    <td><?php print_r($var_client->comprasavances)?></td>
                                                    <td><?php print_r($var_client->cargosyvctos)?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <br><br><br><br>

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
<footer>
    <div class="row">
        <div class="col-lg-12 text-center">

        </div>
    </div>
</footer>
</html>
