<?
$path="PONER PATH";

if(strlen($user)>2&&strlen($server)>=2){
$fa=date("d/m/Y H:i:s.", filectime("$path/$server-$user-a.html"));
$f=fopen("$path/$server-$user-a.html","r");
  while (!feof($f)) {
        $tmp.= fgets($f, 4096);
     }
  fclose ($f);
$aldea=str_replace("\n","",$tmp);
$tmp="";
$f=fopen("$path/$server-$user-c.html","r");
  while (!feof($f)) {
        $tmp.= fgets($f, 4096);
     }
  fclose ($f);
$aldeac=str_replace("\n","",$tmp);

preg_match("#</head><body onload=\"start\(\)\">(.*)</body>#",$aldea,$match);
$parte1=$match[1];
$parte1=preg_replace("#(<table id=\"navi_table\".*</table>)</div><div id=\"lmid1\">#",' ',$parte1);


preg_match("#</head><body onload=\"start\(\)\">(.*)</body>#",$aldeac,$match);
$parte2=$match[1];
preg_match("#(<div id=\"lmid2\">.*)<div id=\"lres0\">#",$parte2,$match);
$parte2=$match[1];

echo "<htlm><head><link rel=stylesheet type=\"text/css\" href=\"unx.css\"><META HTTP-EQUIV=\"refresh\" content=\"180\"> <META HTTP-EQUIV=\"content-type\" content=\"text/html; charset=UTF-8\"> <body>\n";
echo "<table><tr><td>$parte1</td></tr>\n";
echo "<tr><td>Ultimo Acceso: $fa</td></tr>";
echo "<tr><td>$parte2</td></tr></table>";
echo "\n</body></html>";

}

?>
