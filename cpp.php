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
    <?php
      // Si la contraseña es menor a 5 caracteres
      if (strlen($_SESSION['pass_usuario']) < 5) {
        header('location: pass.php');
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

    $fecha = getdate();
    if ($fecha['mon']<10) {
        $mes = "0".$fecha['mon'];
    }
    else{
        $mes = $fecha['mon'];
    }
    $hoy = $fecha['year'] . "/" . $mes . "/" . $fecha['mday'];
    $url = "http://172.16.31.111:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awscpp?wsdl";
    $par = array(
        'Empcod' => $_SESSION['emp_usuario'],
        'Fecini' => $hoy,
        'Fecfin' => $hoy,
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
        '3' => "<fecultestcuenta>",
        '4' => "<disponible>",
        '5' => "<vencido>",
        '6' => "<deuda>",
        '7' => "<credito>",
        '8' => "<diapago>",
        '9' => "<Vencimientos>",
        '10' => "<nombres>",
        '11' => "<rut>",
        '12' => "<hora>",
        '13' => "<hasta>",
        '14' => "<desde>",
        '15' => "<desde>",
        '16' => "<titulo>"
    );
    $tmp = $result->Xml;
    $n=0;
    foreach ($tag as $valor) {
            $tmp1 = explode($valor, $tmp);
            if (! isset($tmp1[1])) { //si la variable no está definida
                $tmp1[1] = null;
            }
            $tag[$n] = ($tmp1[1]);
            $tmp = $tmp1[0];
            $n++;
    }
    if (strip_tags(trim($tag[6])) != "0") {
        # code...
        //Separar las compras en un arreglo
        $compxmes = explode("<Vencimiento>", $tag[9]);
        unset($compxmes[0]);
        //separar valor todal de cada compra

        $n = 1;
        foreach ($compxmes as $key) {
            $tmp = explode("<total>", $key);
            $total[$n] = trim(strip_tags($tmp[1]));
            $key = $tmp[0];
            $compras[$n] = $tmp[0];
            $n++;
        }
        unset($total[0]);
        unset($tmp);
        unset($compxmes);

        $n=0;
        foreach ($compras as $key) {
            $tmp = explode("<Compras>", $compras[$n+1]);
            unset($tmp[0]);
            $item[$n] = $tmp;
            $n++;
        }
        //libero espacio de variables
        unset($compra);
        unset($tmp);
        //Guardar compras por fecha
        $n=0;
        foreach ($item as $key ) {
            $i=1;
            foreach ($item[$n] as $value) {
               $compra[$n] = $item[$n][$i];
            }
            $n++;
        }
        // Quitar el tag <compra>
        $n=0;
        foreach ($compra as $key) {
            $tmp = explode("<compra>", $key);
            $compra[$n] = $tmp[0];
            $n++;
        }
        // Dividir por articulo comprado
        unset($tmp);
        unset($item);
        $n=0;
        foreach ($compra as $key) {
        $tmp = explode("<fecvcto>", $key);
        unset($tmp[0]);
        $item[$n] = $tmp;
        $n++;
        }
        unset($compra);
        $n=0;
        foreach ($item as $key) {
            foreach ($key as $value) {
                $compra[$n] = $value;
                $n++;
            }
        }
        unset($item);
        unset($compxmes);
        unset($tmp);
        unset($tmp1);

        class articulo{
            var $fecha;
            var $detalle;
            var $cuota;
            var $saldo;
        }
        //Guardar arreglo de objetos con datos de cada compra
        $n=0;
        foreach ($compra as $key ) {
            $tmp = explode("<saldo>", $key);
            $compxmes[$n] = new articulo();
            $compxmes[$n]->saldo = "$".moneda_chilena(trim(strip_tags($tmp[1])));
            $tmp1 = explode("<cuota>", $tmp[0]);
            $compxmes[$n]->cuota = trim(strip_tags($tmp1[1]));
            $tmp = explode("<detalle>", $tmp1[0]);
            $compxmes[$n]->detalle = trim(strip_tags($tmp[1]));
            $compxmes[$n]->fecha = trim(strip_tags($tmp[0]));
            $n++;
            unset($tmp);
            unset($tmp1);
        }
        unset($key);
    }
?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Hola! <?php print_r($_SESSION['nombre_usuario']);?> </h1>
                </div>
                    <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cuotas Pendientes de Pago
                        </div>
                        <div class="panel-body">
                            <?php if (strip_tags(trim($tag[6])) != "0"):?>
                            <div class="table-responsible">
                                <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Detalle</th>
                                        <th>Cuota</th>
                                        <th>A pagar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $n=0; $i=1;
                                foreach ($compxmes as $key):?>
                                    <tr>
                                        <td><?php print_r($key->fecha)?></td>
                                        <td><?php print_r($key->detalle)?></td>
                                        <td><?php print_r($key->cuota)?></td>
                                        <td><?php print_r($key->saldo)?></td>
                                    </tr>
                                    <?php if (isset($compxmes[$n+1]) and $compxmes[$n+1]->fecha != $key->fecha):?>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                            <th>Total</th>
                                            <th>$<?php print_r(moneda_chilena($total[$i]))?></th>
                                            <?php $i++;?>
                                        </tr>
                                    <?php endif;?>
                                    <?php if (isset($compxmes[$n+1]) == false):?>
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                            <th>Total</th>
                                            <th>$<?php print_r(moneda_chilena($total[$i]))?></th>
                                            <?php $i++;?>
                                        </tr>
                                    <?php endif; $n++; ?>
                                <?php endforeach; ?>
                                </tbody>
                                </table>
                            </div>
                            <?php else:?>
                            <div class="alert alert-success">No registra cuotas pendientes</div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
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
            <a href="ticket-cpp.php"><button type="button" class="btn btn-primary btn-lg" onclick="animar()"><i class="fa fa-print"></i> Imprimir Ticket</button></a> <!-- con este boton quiero q llame a la funcion, pero no hace ná :c -->
        </div>
    </div>
</footer>

</html>
