<?
include_once("curl-tor.lib4.php");
$c=new Curl;

for ($i=1;$i<10;$i++){
 $out=$c->get("zeuz.filtropc.com/ip.php");
 echo"$out\n";
 system("./tor_nueva_ip.sh");
 sleep(60*3); //3min
}
?>