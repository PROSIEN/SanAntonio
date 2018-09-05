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

function formato_tipo($detalle){
	while(strlen($detalle) < 12){
		$detalle = $detalle . " ";
	}
	return $detalle;
}

function formato_num($detalle){
	while(strlen($detalle) < 10){
		$detalle = $detalle . " ";
	}
	return $detalle;
}
    // FALTA RECIBIR LOS DATOS DE RUT Y PASS
    $url = "http://localhost:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awsmys?wsdl";
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

require __DIR__ . '\autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$nombre_impresora = "BIXOLON-F310II";
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);

/*
	Imprimimos un mensaje. Podemos usar
	el salto de línea o llamar muchas
	veces a $printer->text()
*/
$printer->setJustification(Printer::JUSTIFY_CENTER);
try{
	$logo = EscposImage::load("img/logo-ticket.png", false);
    $printer->bitImage($logo);
}catch(Exception $e){/*No hacemos nada si hay error*/}
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("Movimientos y Saldos\n");
$printer->text("Desde el ".$var_client->desde." hasta el ".$var_client->hasta."\n");
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->feed(1);
$printer->text("Nombre:" . $var_client->nombres . "\n");
$printer->text("Rut:   " . $var_client->rut . "\n");
$printer->feed(1);
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("------------------------------------------\n");
if (!isset($tran)) {
  $printer->text("No registra Movimientos en este periodo\n");
  $printer->feed(1);
}
else {
  $printer->setJustification(Printer::JUSTIFY_LEFT);
  $printer -> setEmphasis(true);
  $printer->text("Fecha    Tipo         Documento   Monto\n");
  $printer -> setEmphasis(false);
  $n=0; $i=1;
  foreach ($tran as $key) {
  	$printer->text($key->fecha." ".formato_tipo($key->tipo)." ".formato_num($key->num)."  ".$key->monto."\n");
  }
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer->text("------------------------------------------\n");
  $printer->qrCode($_SESSION['qr'],Printer::QR_ECLEVEL_L,5);
  $printer->feed(1);
  $printer->text($var_client->glosa1 . "\n");
}
$printer->text("Comprobante válido solo por el día\n");
date_default_timezone_set("America/New_York");
$printer->text(date("d-m-Y") . "\n");

$printer->feed(); // se alimenta la impresora, por defecto 3 lineas blancas
$printer->cut();
$printer->close();
header('location: mys.php');
?>
