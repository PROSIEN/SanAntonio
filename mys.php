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
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/animar.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <?php
        error_reporting(0);
        session_start();
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 360)) {
            // last request was more than 30 minutes ago
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in
            header('location: login.html');
        }
    ?>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png" style="margin-top: -15px;"></a>
            </div>
            <!-- /.navbar-header -->

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

    $url = "http://172.16.31.111:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awsmys?wsdl";
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
    
    class cliente {
        var $titulo;
        var $desde;
        var $hasta;
        var $hora;
        var $rut;
        var $nombres;
        var $transacciones;
        var $diapago;
        var $credito;
        var $deuda;
        var $fecultfac;
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

    $tmp = explode("<fecultfac>", $tmp);
    $var_client->fecultfac = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<deuda>", $tmp);
    $var_client->deuda = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<credito>", $tmp);
    $var_client->credito = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<diapago>", $tmp);
    $var_client->diapago = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<Transacciones>", $tmp);
    $var_client->transacciones = trim($tmp[1]);
    $tmp = $tmp[0];

    $tmp = explode("<nombres>", $tmp);
    $var_client->nombres = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<rut>", $tmp);
    $var_client->rut = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<hora>", $tmp);
    $var_client->hora = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<hasta>", $tmp);
    $var_client->hasta = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<desde>", $tmp);
    $var_client->desde = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $tmp = explode("<titulo>", $tmp);
    $var_client->titulo = trim(strip_tags($tmp[1]));
    $tmp = $tmp[0];

    $trx = explode("<Transaccion>", $var_client->transacciones);
    unset($trx[0]);

    class transaccion{
        var $fecha;
        var $tipo;
        var $num;
        var $monto;
    }
    
    $n=0;
    foreach ($trx as $key) {
        $tran[$n] = new transaccion();
        $tmp = explode("<monto>", $key);
        $tran[$n]->monto = "$".moneda_chilena(trim(strip_tags($tmp[1])));
        $tmp = $tmp[0];

        $tmp = explode("<doc>", $tmp);
        $tran[$n]->num = trim(strip_tags($tmp[1]));
        $tmp = $tmp[0];

        $tmp = explode("<transaccion>", $tmp);
        $tran[$n]->tipo = trim(strip_tags($tmp[1]));
        $tran[$n]->fecha = trim(strip_tags($tmp[0]));
        $n++;
    }
   
?>
        <div id="page-wrapper">
            <?php if(!isset($tran)):?>
                <div class="row">
                    <br><br><br>
                    <div class="alert alert-info text-center">
                        No registra movimientos para este periodo
                    </div>
                </div>
            <?php else: ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Movimientos de su tarjeta </h1>
                </div>
                    <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Desde el <?php print_r($var_client->desde)?> hasta <?php print_r($var_client->hasta)?> sus movimientos son:
                        </div>
                        <div class="panel-body">
                            <div class="table-responsible">
                                <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>N° Documento</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($tran as $key):?>
                                    <tr>
                                        <td><?php print_r($key->fecha)?></td>
                                        <td><?php print_r($key->tipo)?></td>
                                        <td><?php print_r($key->num)?></td>
                                        <td><?php print_r($key->monto)?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

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
            <a href="ticket-mys.php"><button type="button" class="btn btn-primary btn-lg" onclick="animar()"><i class="fa fa-print"></i> Imprimir Ticket</button></a> <!-- con este boton quiero q llame a la funcion, pero no hace ná :c -->
        </div>
    </div>
</footer>

</html>
