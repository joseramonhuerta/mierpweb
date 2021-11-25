<?php
require('fpdf.php');

class PDF extends FPDF
{
var $fechahora; //fechahora de impresion
var $dbd;       //conexion mysql
var $rows;      //numero de registros del reporte
var $totalpage; //total de paginas en el reporte calculadas de los numeros de registros
var $arearegs;  //espacio disponible para los registros por hoja
var $periodo;   //periodo del informe
var $cliente;   //variable para mostrar el cliente
var $totalimp;   //total general del reporte

function PDF($orientation='P',$unit='mm',$format='A4',$tarea=159)
{
    //Llama al constructor de la clase padre
    $this->FPDF($orientation,$unit,$format);
    //Iniciación de variables
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    date_default_timezone_set('America/Mazatlan');
    $this->fechahora = date('d/m/Y h:i a');
    $this->arearegs = $tarea;
}

function dbOpen($host,$user,$pass,$db)
{
   $this->dbd = mysql_connect($host, $user, $pass, TRUE, 131074);
		if (!$this->dbd){
		$this->Cell(0,7,'¡Error al conectarse a la base de datos '.utf8_encode(mysql_errno()).": ".mysql_error().'!');
      $this->Ln();
      return(0);
		}
	if(!mysql_select_db($db, $this->dbd)){
      $this->Cell(0,7,'¡Error: al seleccionar la base de datos '.utf8_encode(mysql_errno()).": ".mysql_error().'!');
      $this->Ln();
      return(0);
		}
   return(1);
}

function dbClose()
{
   mysql_close($this->dbd);
}

function dbQuery($query,$fields=null,$groups=null,$wfields=null,$align=null,$gfont='Arial',$gstyle='B',$gsize=10, $gfcR=0,$gfcG=0,$gfcB=0,$wi=0.1,$wg=0.1,$wm=1,$hr=3,$hg=3.2)
{
   $res = mysql_query($query, $this->dbd);
		if (!$res){
      $this->Cell(0,$hr,utf8_encode(mysql_errno()." : ".mysql_error()));
      $this->Ln($hr);
      }

  $this->rows = mysql_num_rows($res);
   //Parche para cuando la consulta no arroja resultados
   if($this->rows == 0){$this->totalpage = 1;return;}

   //Elementos del grupo
   $li = 0;
   if($groups[0]!=null){
      $rowtmp = "";
      while ($row = mysql_fetch_array($res)) {
         if($row[$groups[0]]!=$rowtmp){
            $li++;
            $rowtmp = $row[$groups[0]];
         }
      }
   }

   mysql_data_seek($res, 0);

   $this->totalpage = ceil((($this->rows * $hr) + ($li * 3 * $hg) + 10) / $this->arearegs);
   if($this->totalpage==0){$this->totalpage = 1;}

   $cols = mysql_num_fields($res);
   if(!isset($fields[0])){
      $fields = array();
      for($i=0;$i<$cols;$i++){
         $header = mysql_fetch_field($res, $i);
         $fields[] = $header->name;
      }
   }

   if($this->rows != 0){
	   //registros
      $rowtmp    = ""; //para chekar el ultimo grupo y hacer el cambio de grupo
      $cuentag   = 0;  //cuenta el numero de registros del grupo
      $totalsuc  = 0;  //total de importe por sucursal
      $totalgral = 0;  //total de depositos en el reporte
	   while ($row = mysql_fetch_array($res)){
         //grupo
         if($groups!=null){
               if($row[$groups[0]]!=$rowtmp){
                if($cuentag!=0){
                $f=$this->FontFamily;
                $s=$this->FontStyle;
                $p=$this->FontSizePt;
                $this->SetFont($f,'B',$p);
                $this->Cell(0,.0,'',1);
                $this->Ln(.1);
                $this->Cell(76,5,'Total:   $',0,0,'R');
                $this->Cell(18,5,number_format($totalsuc, 2, '.', ','),0,0,'R');
                $this->Ln(5);
                $totalsuc = 0;
                $this->SetFont($f,$s,$p);
                $cf=$this->TextColor;
                $this->SetTextColor(128,128,128);
                $this->Cell(0,5,'Total de Depósitos por Sucursal: '.$cuentag);
                $this->TextColor=$cf;
                $this->Ln(5);
                $this->Ln($hg*2);
                $cuentag=0;
                }
               $f=$this->FontFamily;
               $s=$this->FontStyle;
               $p=$this->FontSizePt;
               $c=$this->TextColor;
               $this->SetFont($gfont,$gstyle,$gsize);
               $this->SetTextColor($gfcR,$gfcG,$gfcB);
               $this->cell($wg,$hg,'');
               $this->cell($row[$groups[0]],$hg,$row[$groups[0]]);
               $this->Ln($hg);
               $rowtmp = $row[$groups[0]];
               $this->SetFont($f,$s,$p);
               $this->TextColor = $c;
            }
         }
         //registros
         $this->cell($wi,$hr,'');
		   for($i=0; $i<$cols; $i++){
            if(!isset($wfields[$i])){
               $a= '';
               if(isset($align[$i])){
                  $a=$align[$i];
                  if($a=='#' and isnumeric($row[$fields[$i]])){
                      $campo = number_format($row[$fields[$i]], 2, '.', ',');
                      $a='R';
                  }
                  else {
                    $campo = $row[$fields[$i]];
                  }
               }

               $this->cell($this->GetStringWidth($campo),$hr,$campo,0,0,$a);
            }
            else{
               $str = $row[$fields[$i]];
               $anchoTexto = $this->GetStringWidth($str);
               while($anchoTexto > $wfields[$i]){
                  $str=substr($str,0,strlen($str)-1);
                  $anchoTexto = $this->GetStringWidth($str)+0.5;
               }
               $a= '';
               if(isset($align[$i])){
                  $a=$align[$i];
                  if($a=='#' and is_numeric($row[$fields[$i]])){
                    $campo = number_format($str, 2, '.', ',');
                    $a='R';
                  }
                  else {
                    $campo = $str;
                  }
               }
               $this->cell($wfields[$i],$hr,$campo,0,0,$a);
            }
            $this->cell($wm,$hr,'');
         }
      $this->Ln($hr);
      $cuentag++;
      $totalsuc  = $totalsuc + $row[$fields[3]];
      $totalgral = $totalgral + $row[$fields[3]];
		}
    if($cuentag!=0){ //parte del pie de grupo y total de grupo
      $f=$this->FontFamily;
      $s=$this->FontStyle;
      $p=$this->FontSizePt;
      $this->SetFont($f,'B',$p);
      $this->Cell(0,.0,'',1);
      $this->Ln(.1);
      $this->Cell(76,5,'Total:   $',0,0,'R');
      $this->Cell(18,5,number_format($totalsuc, 2, '.', ','),0,0,'R');
      $this->Ln(5);
      $totalsuc = 0;
      $this->SetFont($f,$s,$p);
      $this->FontStyle = $s;
      $cf=$this->TextColor;
      $this->SetTextColor(128,128,128);
      $this->Cell(0,5,'Total de Depósitos por Sucursal: '.$cuentag);
      $this->TextColor=$cf;
      $this->Ln(5);
      $cuentag=0;
    }
   $this->totalimp = $totalgral;
   }
}

function dbNumRows(){
   return $this->rows;
}

function getPeriodo($str){
   $this->periodo = $str;
}

function getCliente($str){
  $this->cliente = $str;
}

function importeTotal(){
  return $this->totalimp;
}

function RoundedRect($x, $y, $w, $h,$r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

function Header()
{
   $this->SetFont('Arial','BI',20);
   $this->SetTextColor(232,224,169);
   //LOGO
   $this->Image('logo.jpg',218,7,50,20,'','http://www.tafconsulting.com.mx');
   $this->SetLineWidth(.5);
   $this->Line(5,15.8,213.3,15.8);
   //TITULO
   $this->Cell(0,7,'Depósitos Realizados');
   $this->Ln(13);
   //NOMBRE DEL CLIENTE
   $this->SetFont('Arial','B',14);
   $this->SetTextColor(232,224,169);
   $this->Cell(0.1,7,'');
   $this->Cell(0,7,$this->cliente);
   $this->Ln(7);
   //PERIODO DEL INFORME
   $this->Cell(0.1,7,'');
   $this->Cell(0,7,$this->periodo);
   $this->Ln(10);
   //RECTANGULO DE CABECERA DE REGISTROS
   $this->SetLineWidth(.2);
   $this->SetFillColor(232,224,169);
   $this->RoundedRect(5,34.2,268.2,8.4,1,'DF');
   //CABECERA DE REGISTROS
   $this->SetFont('Arial','',8);
   $this->SetTextColor(255,255,255);
   $this->Cell(2,7,'');
   $this->Cell(33,7,'BANCO');
   $this->Cell(41,7,'CUENTA EMPRESA');
   $this->Cell(30,7,'IMPORTE');
   $this->Cell(41,7,'FECHA Y HORA');
   $this->Cell(60,7,'REFERENCIA');
   $this->Cell(35,7,'AUTORIZACION');
   //espaciado del header
   $this->Ln(8);
}

function Footer()
{  $this->SetLineWidth(.3);
   $this->Line(5,203,270,203);
   $this->SetFontSize(8);
   $this->SetTextColor(128,128,128);
   $this->SetXY(5,200.5);
   $this->Cell(0,10,'Fecha y Hora de Impresión:  ' . $this->fechahora);
   $this->SetXY(252,200.5);
   $this->Cell(0,10,'Pagina '.$this->PageNo().'/'.$this->totalpage);
}

}//fin de las clase pdf

//paso de parametros en variables de la session


session_start();
if(!isset($_SESSION["id_usuario"]))
{
   session_destroy();
   echo '<meta http-equiv="Refresh" content="0;URL=index.php">';
   exit;
}
$id_usuario = $_SESSION["id_usuario"];

$cvecliente    = $_POST['cbxEmpresa'];
$id_sucursal   = $_POST['cbxSucursal'];
$nombreCliente = $_POST['hEmpresa'];
$fechaini      = $_POST['Fecha1'];
$fechafin      = $_POST['Fecha2'];

//$fechaini   = strtotime($fechaini);
//$fechafin   = strtotime($fechafin);

if(Trim($id_sucursal)==''){$id_sucursal = '%';}
if(Trim($fechaini)==''){$fechaini = date('Y/m/d',strtotime('1969-12-31'));}
//else {$af1 = split('-',$fechaini); $fechaini = $af[2].'-'.$af[1].'-'.$af[0];}
if(Trim($fechafin)==''){$fechafin = date('Y/m/d',strtotime('2038-01-01'));}
//else {$af1 = split('-',$fechafin); $fechafin = $af[2].'-'.$af[1].'-'.$af[0];}

/*//Completa la clave de empleado con ceros
if((strlen($cveempleado) < 7) and ($cveempleado != '%')){
  $cveempleado = '0000000'.$cveempleado;
  $cveempleado = substr($cveempleado,strlen($cveempleado)-7,7);
}
  */

$pdf=new PDF('L','mm','Letter');
$pdf->SetMargins(5,5,7);
$pdf->SetAutoPageBreak(1,13);
$pdf->getPeriodo('Periodo del '.$fechaini.' al '.$fechafin);
$pdf->getCliente($cvecliente.'-'.$nombreCliente);
//Primera página
$pdf->AddPage();
//simulacion de registros
$pdf->SetFont('Arial','',7);
$pdf->SetTextColor(0,0,0);

//debug variables
/*
$pdf->Cell(0,5,'Debug: '.$id_usuario.', '.$cvecliente.', '.date('Y-m-d',$fechaini).', '.date('Y-m-d',$fechafin).' fechafin post:'.$_POST['Fecha2']);
$pdf->Output();
return;
*/

if ($pdf->dbOpen('localhost','sicapuser','taf582','sai')){
   $campos = array('banco','ctabanco', 'signo','importe','fechadeposito','referencia','','autorizacion');
   $anchos = array(32,23,17,17,46,60,5,55);
   $align = array('','R','R','#','C','','','');
   $query = 'call spRepDepositos("'.$id_usuario.'","'.$id_sucursal.'","'.$cvecliente.'","'.$fechaini.'","'.$fechafin.'");';
   $pdf->dbQuery($query,$campos,array('nomsucursal'),$anchos,$align,'Arial','B',10,0,64,128,2,0.1);
   $pdf->Ln(5);
   $pdf->SetFont('Arial','B',7);
   $pdf->Cell(0,0,'',1);
   $pdf->Ln(.1);
   $pdf->Cell(76,5,'Total General:   $',0,0,'R');
   $pdf->Cell(18,5,number_format($pdf->importeTotal(), 2, '.', ','),0,0,'R');
   $pdf->Ln(5);
   $pdf->SetFont('Arial','',7);
   $pdf->SetTextColor(128,128,128);
   $pdf->Cell(0,5,'Total de Depósitos: '.$pdf->dbNumRows());
}
$pdf->dbClose();
$pdf->Output();
?>