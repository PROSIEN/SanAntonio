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

    <style type="text/css">
        img {
            height: 50%;
            width: 50%;
        }
    </style>

    <script type="text/javascript">
        function imprimir(){
            window.print();
        }
    </script>

</head>
<?php
session_start();

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
   
?>

<body onload="update_qrcode()"> <!-- aqui se carga el QR con la informacion -->
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
                    <br>
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
                                        <td><?php print_r(moneda_chilena($tag[9])) ?></td>
                                    </tr>
                                <?php endif;?>
                            <?php else: ?>
                                <tr>
                                    <th>Monto vencido desde <?php print_r($tag[14]) ?></th>
                                    <td><?php print_r(moneda_chilena($tag[13])) ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <br><p><strong>Próximos Vencimientos</strong></p>
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
                    <?php $barra = str_replace("*", "", $tag[2]) ?>
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
                    <p class="text-center" onload="imprimir()"><?php echo $hoy['mday']."/".$hoy['mon']."/".$hoy['year']; ?> </p>
                </div>
            </div>
        </div>
    </div>
        
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>    
</body>
</html>