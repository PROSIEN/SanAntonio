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

function formato_cuota($cuota){
  if (strlen($cuota) == 3) {
    $tmp = explode("/", $cuota);
    $tmp[1] = str_replace("/","",$tmp[1]);
    $cuota = "0".$tmp[0]."/0".$tmp[1];
  }
  if (strlen($cuota) == 4) {
    $tmp = explode("/", $cuota);
    $tmp[1] = str_replace("/","",$tmp[1]);
    $cuota = "0".$tmp[0]."/".$tmp[1];
  }
  if ($cuota == "") {
    $cuota = "-----";
  }
  return $cuota;
}

function formato_monto($detalle){
	while(strlen($detalle) < 14){
		$detalle = $detalle . " ";
	}
	return $detalle;
}

function formato_des($detalle){
	while(strlen($detalle) < 13){
		$detalle = $detalle . " ";
	}
  while (strlen($detalle) >13) {
    $detalle = substr($detalle, 0, -1);
  }
	return $detalle;
}

$url = "http://172.16.31.111:8080/xwcycgx15je/servlet/com.xwcycgx15.autoconsulta.awsecu?wsdl";
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
$var_client->cargosyvctos ="$" . moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<comprasavances>", $tmp);
$var_client->comprasavances ="$" . moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<pagos>", $tmp);
$var_client->pagos = "$".moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<Transacciones>", $tmp);
$var_client->transacciones = trim(($tmp[1]));
$tmp = $tmp[0];

$tmp = explode("<montoapagar>", $tmp);
$var_client->montoapagar = "$".moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<pagarhasta>", $tmp);
$var_client->pagarhasta = trim(strip_tags($tmp[1]));
$tmp = $tmp[0];

$tmp = explode("<atrazado>", $tmp);
$var_client->atrazado = "$".moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<disponible>", $tmp);
$var_client->disponible = "$".moneda_chilena(trim(strip_tags($tmp[1])));
$tmp = $tmp[0];

$tmp = explode("<credito>", $tmp);
$var_client->credito = "$".moneda_chilena(trim(strip_tags($tmp[1])));
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
    $movimientos[$n]->monto="$" . moneda_chilena(trim(strip_tags($tmp[1])));
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

require __DIR__ . '\autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$nombre_impresora = "BIXOLON-F310II";
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);

$printer->setJustification(Printer::JUSTIFY_CENTER);
try{
	$logo = EscposImage::load("img/logo-ticket.png", false);
    $printer->bitImage($logo);
}catch(Exception $e){/*No hacemos nada si hay error*/}
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->feed(1);
if (!isset($movimientos)) {
  // code...
  $printer->text("Nombre: " . $var_client->nombres . "\n");
  $printer->text("Rut:    " . $var_client->rut . "\n");
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer->feed(1);
  $printer->text("No registra EE.CC para este periodo\n");
  $printer->feed(1);
  $printer->text("Comprobante válido solo por el día\n");
  date_default_timezone_set("America/New_York");
  $printer->text(date("d-m-Y") . "\n");

  $printer->feed();
  $printer->cut();
  $printer->close();
  header('location: eecc.php');
}
else {
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer->text("Estado de Cuenta\n");
  $printer->text("Emitido el día " . $var_client->fecfacturacion . "\n");
  $printer->feed(1);
  $printer->setJustification(Printer::JUSTIFY_LEFT);
  $printer->text("Nombre:     " . $var_client->nombres . "\n");
  $printer->text("Rut:        " . $var_client->rut . "\n");
  $printer->text("Crédito:    " . $var_client->credito . "\n");
  $printer->text("Disponible: " . $var_client->disponible . "\n");
  $printer->text("Atrazado:   " . $var_client->atrazado . "\n");
  $printer->text("CAE prepago:" . $var_client->caepre . "\n");
  $printer->text("CAE compra: " . $var_client->caecompra . "%\n");
  $printer->feed(1);
  $printer->text("   Pagar Hasta             Monto\n");
  $printer->setTextSize(2,2);
  $printer->setEmphasis(true);
  $printer->text($var_client->pagarhasta . "    " . $var_client->montoapagar . "\n");
  $printer->setEmphasis(false);
  $printer->setTextSize(1,1);
  $printer->feed(1);
  $printer->text("------------------------------------------\n");
  $printer->setJustification(Printer::JUSTIFY_LEFT);
  $printer -> setEmphasis(true);
  $printer->text("Fecha    Descripción    Cuota   Monto\n");
  $printer -> setEmphasis(false);
  foreach ($movimientos as $key) {
  	$printer->text($key->fecha." ".formato_des($key->des)."  ".formato_cuota($key->cuota)."   ".$key->monto."\n");
  }
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer->text("------------------------------------------\n");
  $printer->setJustification(Printer::JUSTIFY_LEFT);
  $printer->text("Movimientos del Periodo de Facturación\n");
  $printer->setEmphasis(true);
  $printer->text("Pagos       Compras/Avances     Cargos\n");
  $printer->setEmphasis(false);
  $printer->text(formato_monto($var_client->pagos) . "" . formato_monto($var_client->comprasavances) . "    " . formato_monto($var_client->cargosyvctos) . "\n");
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer->text("------------------------------------------\n");
  $printer->qrCode($_SESSION['qr'],Printer::QR_ECLEVEL_L,5);
  $printer->feed(1);
  $printer->text($var_client->glosa1 . "\n");
  $printer->text("Comprobante válido solo por el día\n");
  date_default_timezone_set("America/New_York");
  $printer->text(date("d-m-Y") . "\n");

  $printer->feed(); // se alimenta la impresora, por defecto 3 lineas blancas
  $printer->cut();
  $printer->close();
  header('location: eecc.php');
}
?>
