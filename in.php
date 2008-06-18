<?
include("config.inc.php");

$db1=mysql_connect($dbhost,$dbuser,$dbpassword);
@mysql_select_db($dbname,$db1);
$resultados="";
$clientip=$_SERVER['REMOTE_ADDR'];


function login($password){
global $db1;
global $smarty;
global $username;
global $clientip;
global $queue;

$query = mysql_query("SELECT *  FROM usuarios WHERE login='$username' AND pass='$password'",$db1);
$result= mysql_fetch_assoc($query);

if ($result==''){
      echo "Error..IP:$clientip";
    exit;
}
 $smarty->display("header.tpl");
 status($msg);
}

function status($msg){
 global $smarty;
 global $username;
 global $resultados;
 global $clientip;
 global $queue;

 $q1=mysql_query("SELECT * FROM usuarios where login='$username';"); 
 $res=mysql_fetch_array($q1);
 $queue=$res[queue];

 $q1= mysql_query("SELECT * FROM edificios;");
  while($res=mysql_fetch_array($q1)){
    $edificios.="<OPTION value=\"".$res[id]."\">".$res[nombre]."</OPTION>\n";
 }

 // renglones de activos de el usuario actual
 if ($username==="admin"){
  $q1= mysql_query("SELECT * FROM activos ORDER BY login ASC;");
 }
 else{
  $q1= mysql_query("SELECT trabajos.*,nombre FROM trabajos left join edificios on edificios.id=edificio WHERE login='$username' and terminado='N' ORDER BY id ASC;");
 }

 while($res=mysql_fetch_assoc($q1)){
     $chk="<input type=\"checkbox\" name=\"chk[]\"  value=\"".$res[id]."\" >";
     if($username==="admin"){
        $us=$res[1];
     }else{
       $us="*****"; 
      }
     if($res[tipo]==="U"){
        $tipo="Upgrade";
       }else{
        $tipo="Construir";
      }
     $smarty->assign(CHECK,$chk);
     $smarty->assign(SERVER,$res[tserver]);
     $smarty->assign(USER,$res[tuser]);
     $smarty->assign(PARCELA,$res[parcela]);
     $smarty->assign(EDIFICIO,$res[nombre]);  
     $smarty->assign(TIPO,$tipo);
     $smarty->assign(FECHAQ,$res[fechaq]);
     $lserver=substr($res[tserver],0,2);  
     $link_parcela="view.php?user=$res[tuser]&server=$lserver";
     $smarty->assign(LINKP,$link_parcela);
     $rowactivos.=$smarty->fetch("rowactivos.tpl");
     $nactivos++; 
}
 $boton_quitar="<input type=\"submit\" name=\"submit\" value=\"Quitar\">";
 if ($nactivos){
 $smarty->assign("ACTIVOS",$rowactivos);
 $smarty->assign("BOTON_QUITAR",$boton_quitar);
 }
 if($queue=='A'){
 $boton_queue="<input type=\"submit\" name=\"submit\" value=\"Pausar\">";
 }else{
 $boton_queue="<input type=\"submit\" name=\"submit\" value=\"Reanudar\">";
 }
 $smarty->assign("BOTON_QUEUE",$boton_queue);

 $q2= mysql_query("SELECT trabajos.*,nombre,SUBSTRING(tserver,1,2) as srv FROM trabajos left join edificios on edificios.id=edificio WHERE login='$username' and terminado='Y' ORDER BY fechat DESC");
 

 while($res=mysql_fetch_assoc($q2)){
     if($username==="admin"){
        $us=$res[1];
     }else{
       $us="*****";
      }
     if($res[tipo]==="U"){
        $tipo="Upgrade";
       }else{
        $tipo="Construir";
      }
  
     $smarty->assign(SERVER,$res[srv]);
     $smarty->assign(IP,$res[ip]);
     $smarty->assign(USER,$res[tuser]);
     $smarty->assign(PARCELA,$res[parcela]);
     $smarty->assign(EDIFICIO,$res[nombre]);
     $smarty->assign(TIPO,$tipo);
     $smarty->assign(FECHAT,$res[fechat]);

     $lserver=substr($res[tserver],0,2);
     $link_parcela="view.php?user=$res[tuser]&server=$lserver";
     $smarty->assign(LINKP,$link_parcela);

        $rowreali.=$smarty->fetch("rowreali.tpl");
    $nreali++;
}

 if ($nreali>0){
 $smarty->assign("REALIZADOS",$rowreali);
 }


 $smarty->assign("MSG",$msg);
 $smarty->assign("EDIFICIOS",$edificios);
 $smarty->assign("USERNAME",$username);
 $smarty->assign("OP",$option); 
 $smarty->assign("IP",$clientip);
 $smarty->assign("RESULTADOSB",$resultados); 
if ($username==="admin"){
   
   $smarty->display('admin.tpl');
  }else{
 $smarty->display('in.tpl');
 }
 echo "<br>$submit<br>"; 
}

/*************************************/
function agrega($username,$server,$user,$pass,$parcela,$parcelas,$edificio){
//echo"agrega($username,$server,$user,$pass,$parcela,$edificio);";
global $db1;
global $smarty;
global $username;
global $clientip;

if (strlen($parcelas)>2&strlen($user)>2&&strlen($pass)>2){
  $p=Array();
  $p=split(",",$parcelas);
  $tipo="U";
  $edificio="";
  foreach($p as $parcela){
  if($parcela>0&&$parcela<41){
   $sql0="INSERT INTO trabajos values ('','$username','$server','$user','$pass','$parcela','$edificio','$tipo','N','',NOW(),'')";
   // echo $sql0;
   $q1=mysql_query($sql0,$db1);
   }
  }
  $msg="Trabajos agregados";

}else{
if($parcela>0&&strlen($user)>2&&strlen($pass)>2&&$parcela<41){
 $tipo="U";
 if ($parcela>18 && strlen($edificio)<1){
   $tipo="U";
   }
 if ($parcela<19 && strlen($edificio)<1){
   $tipo="U";
  }
 if($parcela>18 && $edificio>0 && $parcela<39 && $parcela!=26){
  $tipo="B";
 }
 if($parcela<19||$parcela>38|| $parcela==26) $edificio="";

 $sql0="INSERT INTO trabajos values ('','$username','$server','$user','$pass','$parcela','$edificio','$tipo','N','',NOW(),'')";
// echo $sql0;
 $q1=mysql_query($sql0,$db1);
 $msg="Trabajo agregado";
 }else{
  $msg="Error";
 }
}
 status($msg);
}
function quitar($username,$chk){
 global $username;
// echo "$username,$chk";
 $cuantos=count($chk); 
 if ($cuantos<1){
     $msg="NO HA SELECCIONADO NINGUN CLON A QUITAR";
     status($msg);
     return;
 }
 foreach($chk as $id){
    $q1="DELETE FROM trabajos where id='$id' and terminado='N' and login='$username';";
   mysql_query($q1);
 } 
 $msg="";
 status($msg); 
}
/*******************************************/
function toogle_q($username){
 global $db1;
 $q0=mysql_query("update usuarios set queue=IF(queue='A','D','A') where login='$username'");
 $msg="";
 status($msg);
}
/***************************************************/
switch($submit){
   case 'Agregar':
           agrega($username,$server,$user,$pass,$parcela,$parcelas,$edificio);  
          break; 
   case 'Quitar':
           quitar($username,$chk);
           break;
   case 'Pausar':
   case 'Reanudar':
           toogle_q($username);
           break;
   default:
           login($password);
          break;
}


?>