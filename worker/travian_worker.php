<?
include_once("travian.lib4.php");
$dbhost="HOST";
$dbuser="USER";
$dbpassword="PASS";
$dbname="DB";
$path_dump="PATH";
$db1=mysql_connect($dbhost,$dbuser,$dbpassword);
@mysql_select_db($dbname,$db1);

$olduser="";
$user="";
while(1){

$sqlu="SELECT distinct tuser,tserver FROM trabajos WHERE terminado='N' ORDER BY id ASC, tuser";

$qu=mysql_query($sqlu,$db1);

while($rsu=mysql_fetch_assoc($qu)){
 $tmp_user=$rsu[tuser];
 $tmp_server=$rsu[tserver];
// $sql0="SELECT * FROM trabajos WHERE tuser='$tmp_user' and tserver='$tmp_server' and terminado='N' Order By id limit 1";

 $sql0="SELECT trabajos.*,usuarios.queue FROM trabajos LEFT JOIN usuarios ON usuarios.login=trabajos.login WHERE tuser='$tmp_user' and tserver='$tmp_server' and terminado='N' and queue='A' Order By trabajos.id limit 1"; 

  $q0=mysql_query($sql0,$db1);
  while($res=mysql_fetch_assoc($q0)){
  $id=$res[id];
  $user=$res[tuser];
  $tr=new Travian($user,$res[tpass],$res[tserver]);
  if($tr->login()){
   $realizado=false;
   $ip=$tr->ip;
   echo "\n$user, $tr->madera_hora/$tr->madera , $tr->barro_hora/$tr->barro, $tr->hierro_hora/$tr->hierro, $tr->cereal_hora/$tr->cereal, $tr->consumo_hora/hora\n";
   if($res[tipo]=="U"){
    echo "upgrade $res[parcela]\n";
      if($tr->upgrade($res[parcela])) $realizado=true;
   }else{
    echo "construir $res[parcela],$res[edificio]\n";
     if($tr->build($res[parcela],$res[edificio])) $realizado=true;
   }
   if($realizado){
        $sql_t="UPDATE trabajos SET terminado='Y', fechat=NOW(), ip='$ip' WHERE id='$id'";
        $q2=mysql_query($sql_t);
      }else{
    echo "id=$id no_realizado\n";
     }
    //volcar parcela
    $pserver=substr($res[tserver],0,2);
    $aldea=$tr->get_aldea();
    $aldeac=$tr->get_aldea_centro();
    $file=fopen("$path_dump/$pserver-$user-a.html","w+");
     fwrite($file,$aldea,strlen($aldea));
    fclose($file);
    
    $file=fopen("$path_dump/$pserver-$user-c.html","w+");
     fwrite($file,$aldeac,strlen($aldeac));
    fclose($file);

  $tr->logout();
  }else{
   echo "NO_Login $user, $res[pass], $res[server]\n";
   }
  $olduser=$user;
 }//trabajos
 system("./tor_nueva_ip.sh");
 echo "nueva ip dormir 50\n";
 sleep(50); //dormir un minuto

}
echo "Loop:\n";
sleep(90);
}//loop infinito