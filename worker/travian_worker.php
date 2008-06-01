<?
include_once("travian.lib4.php");
$dbhost="HOST";
$dbuser="USER";
$dbpassword="PASS";
$dbname="DB";
$db1=mysql_connect($dbhost,$dbuser,$dbpassword);
@mysql_select_db($dbname,$db1);

$olduser="";
$user="";
$sql0="SELECT * FROM trabajos WHERE terminado='N' ORDER BY id ASC";

while(1){

 $q0=mysql_query($sql0,$db1);

 while($res=mysql_fetch_assoc($q0)){
  $id=$res[id];
  $user=$res[tuser];
  //echo "[$olduser,$user]\n";
  if($olduser!==$user){
       system("./tor_nueva_ip.sh");
      echo "nueva ip dormir 60\n";
       sleep(60); //dormir un minuto
    }else{
     echo "mismo user dormir 30\n";
       sleep(30);
     }
   
 $tr=new Travian($user,$res[tpass],$res[tserver]);
    if($tr->login()){
   $realizado=false;
   $ip=$tr->ip;
   echo "\n$user, $tr->madera_hora/$tr->madera , $tr->barro_hora/$tr->barro, $tr->hierro_hora/$tr->hierro,$tr->cereal_hora/$tr->cereal, $tr->consumo/hora\n";
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
  $tr->logout();
  }else{
   echo "NO_Login $user, $res[pass], $res[server]\n";
   }
  $olduser=$user;
 }//trabajos
sleep(30);
}//loop infinito