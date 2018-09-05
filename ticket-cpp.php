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
	return $cuota;
}

function formato_detalle($detalle){
	while(strlen($detalle) < 13){
		$detalle = $detalle . " ";
	}
	return $detalle;
}
    // FALTA RECIBIR LOS DATOS DE RUT Y PASS
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
            $compxmes[$n]->cuota = formato_cuota(trim(strip_tags($tmp1[1])));
            $tmp = explode("<detalle>", $tmp1[0]);
            $compxmes[$n]->detalle = trim(strip_tags($tmp[1]));
            $compxmes[$n]->fecha = trim(strip_tags($tmp[0]));
            $n++;
            unset($tmp);
            unset($tmp1);
        }
        unset($key);
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
$printer->text("Cuotas Pendientes de Pago\n");
$tag[10] = strip_tags(trim($tag[10]));
$tag[11] = strip_tags(trim($tag[11]));
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->feed(1);
$printer->text("Nombre:" . $tag[10] . "\n");
$printer->text("Rut:   " . $tag[11] . "\n");
$printer->feed(1);
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("----------------------------------------\n");
$printer->setJustification(Printer::JUSTIFY_LEFT);
if (strip_tags(trim($tag[6])) != 0) {
  $printer -> setEmphasis(true);
	$printer->text("Fecha     Detalle        Cuota   A Pagar\n");
  $printer -> setEmphasis(false);
	$n=0; $i=1;
	foreach ($compxmes as $key) {
		$printer->text($key->fecha."  ".formato_detalle($key->detalle)."  ".$key->cuota."   ".$key->saldo."\n");
		if (isset($compxmes[$n+1]) and $compxmes[$n+1]->fecha != $key->fecha) {
			$printer->text("                                 --------\n");
			$printer->text("                         Total:  $".moneda_chilena($total[$i])."\n");
			$printer->feed(1);
			$i++;
		}
		if (isset($compxmes[$n+1]) == false) {
			$printer->text("                                 --------\n");
			$printer->text("                         Total:  $".moneda_chilena($total[$i])."\n");
			$printer->feed(1);
			$i++;
		}
		$n++;
	}
	$tag[6]=strip_tags(trim($tag[6]));
	$printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer -> setEmphasis(true);
	$printer->text("Deuda Total:  $".moneda_chilena($tag[6])."\n");
  $printer -> setEmphasis(false);
}
else {
	$printer->setJustification(Printer::JUSTIFY_CENTER);
	$printer->feed(1);
  $printer -> setEmphasis(true);
	$printer->text("No registra cuotas pendientes");
  $printer -> setEmphasis(false);
	$printer->feed(2);
}

$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("----------------------------------------\n");
$printer->qrCode($_SESSION['qr'],Printer::QR_ECLEVEL_L,5);
$printer->feed(1);
$printer->text($var_client->glosa1 . "\n");
$printer->text("Comprobante válido solo por el día\n");
date_default_timezone_set("America/New_York");
$printer->text(date("d-m-Y") . "\n");

$printer->feed(); // se alimenta la impresora, por defecto 3 lineas blancas
$printer->cut();
$printer->close();
header('location: cpp.php');
?>
