<?
include_once("curl-tor.lib4.php");

function checkValidIp($cidr) {
    // Checks for a valid IP address or optionally a cidr notation range
    // e.g. 1.2.3.4 or 1.2.3.0/24
   if(!eregi("EXPR", $cidr)) {
       $return = FALSE;
   } else {
       $return = TRUE;
   }
    if ( $return == TRUE ) {
        $parts = explode("/", $cidr);
        $ip = $parts[0];
        $netmask = $parts[1];
        $octets = explode(".", $ip);
        foreach ( $octets AS $octet ) {
            if ( $octet > 255 ) {
                $return = FALSE;
            }
        }
        if ( ( $netmask != "" ) && ( $netmask > 32 ) ) {
            $return = FALSE;
        }
    }
    return $return;
}

class Travian {
        var $user;
        var $password;
        var $aldea_;
        //RECURSOS
        var $madera;
        var $madera_hora;
        var $madera_almacen;
        var $barro;
        var $barro_hora;
        var $barro_almacen;
        var $hierro;
        var $hierro_hora;
        var $hierro_almacen;
        var $cereal;
        var $cereal_almacen;
        var $cereal_hora;
        var $cosumo_hora;
        var $prod_hora;
  
      //
        var $debug;
        var $ip; 
        var $curl_;
        var $dorf1_;
        var $dorf2_;
//funciones
   function Travian($user,$pass,$server,$debug=0){
      $this->user=$user;
      $this->pass=$pass;
      $this->server=$server;
      $this->debug=$debug;
   } 

  function login(){
     $debug=$this->debug;
     $this->curl_=new Curl();
     $ip=$this->curl_->get('zeuz.filtropc.com/ip.php');
    
     if(strlen($ip)<=16){
       $this->d_("\n\nIP:$ip\n");
       $this->ip=$ip;
       $t_index=$this->curl_->get($this->server);
       //sacar variables para el post 
       preg_match('#<input.*name=\"login\".*value=\"(.*)\">#',$t_index,$match);
       $login_var=$match[1];
       preg_match('#<input.*name=\"w\".*value=\"(.*)\">#',$t_index,$match);
       $w_var=$match[1];
       preg_match('#<input.*class=\"fm.*type=\"text\".*name=\"(.*)\".*value=.*>#',$t_index,$match);
       $user_var=$match[1];
       preg_match('#<input.*class=\"fm.*type=\"password\".*name=\"(.*)\".*value=.*>#',$t_index,$match);
       $pass_var=$match[1];
       preg_match('#<p align=\"center\"><input.*type=\"hidden\".*name=\"(.*)\".*value=\"\"\>#',$t_index,$match);
       $x_var=$match[1];

       $this->d_("\n w=$w_var, login_var=$login_var, user_var=$user_var, pass_var=$pass_var, x_var=$x_var\n");
       $w_var="1024%3A768";
       $x_var_value="&s1.x=0&s1.y=0";
       //hacer login
      $aldea = $this->curl_->post($this->server."/dorf1.php", array('w' => $w_var, 'login' => $login_var, $user_var => $this->user, $pass_var => $this->pass,$x_var=>$x_var_value ));
    
       if($this->login_valido_($aldea)){
           $this->dorf1_=$aldea;
           $this->lee_recursos_();
           return true;
       }else{
         return false;
       }
     }else{
       return false;
     }
  }

  function login_valido_($aldea){
    return(preg_match('#<div id=\"lres0\">#',$aldea,$match));
   }
 
  function lee_recursos_(){
        $aldea=str_replace("\n","",$this->dorf1_);
        // extraer tabla de recursos
        preg_match('#<div id=\"lres0\">(.*)</div><div id=\"ltime\">#',$aldea,$match);
        // echo "$match[1]";
        $tabla_rec=$match[1];
        preg_match('#\"Madera\".*<td id=l4 title=(.*)>(.*)</td>.*\"Barro\">#',$tabla_rec,$matchr);
        // echo "\n$matchr[1]\n$matchr[2]";
        $this->madera_hora=$matchr[1];
        list($this->madera,$this->madera_almacen)=split('/',$matchr[2]);
        
         preg_match('#\"Barro\".*<td id=l3 title=(.*)>(.*)</td>.*\"Hierro\">#',$tabla_rec,$matchr);
         // echo "\n$matchr[1]\n$matchr[2]";
         $this->barro_hora=$matchr[1];
         list($this->barro,$this->barro_almacen)=split('/',$matchr[2]);
 
         preg_match('#\"Hierro\".*<td id=l2 title=(.*)>(.*)</td>.*\"Cereales\">#',$tabla_rec,$matchr);
         // echo "\n$matchr[1]\n$matchr[2]";
         $this->hierro_hora=$matchr[1];
         list($this->hierro,$this->hierro_almacen)=split('/',$matchr[2]);

         preg_match('#\"Cereales\".*<td id=l1 title=(.*)>(.*)</td>.*\"Consumo cereales\">#',$tabla_rec,$matchr);
         //echo "\n$matchr[1]\n$matchr[2]"; 
         $this->cereal_hora=$matchr[1];
         list($this->cereal,$this->cereal_almacen)=split('/',$matchr[2]);

         preg_match('#\"Consumo cereales\">&nbsp;(.*)</td></tr></table>#',$tabla_rec,$matchr);
         // echo "\n$matchr[0]\n$matchr[1]";
         list($this->consumo_hora,$this->prod_hora)=split('/',$matchr[1]);

   }
      
  function upgrade($parcela){
     $tmp_build=$this->curl_->get($this->server."/build.php?id=".$parcela);    
  
      //buscar el link de upgrade en recursos
    if($parcela>=1 && $parcela<=18){
     if (preg_match("#<a href=\"(dorf[12].php\?a=".$parcela."\&c=.*)\">.*</a>#",$tmp_build,$match)){
        $link_upgrade=$match[1];
        $this->d_("\n\n $link_upgrade\n"); 
        $tmp_cmd=$this->curl_->get($this->server."/".$link_upgrade);
        return true;
       }else{
          return false;
       }
    }
    //buscar el link upgrade en edificios
    if($parcela>=19 && $parcela<=40){
      if (preg_match("#<a href=\"(dorf[12].php\?a=[1-9][1-9]\&id=".$parcela."\&c=.*)\">.*</a>#",$tmp_build,$match)){
        $link_upgrade=$match[1];
        $this->d_("\n\n $link_upgrade\n");
        $tmp_cmd=$this->curl_->get($this->server."/".$link_upgrade);
        return true;
       }else{
          return false;
       }
    }
   } 


  function build($parcela,$edificio){
     //que no sea edificio principal, plaza de reuniones o muralla
     if($parcela>=19 && $parcela<=38 && $pacerla!=24){
       $tmp_build=$this->curl_->get($this->server."/build.php?id=".$parcela);
        if (preg_match("#<a href=\"(dorf[12].php\?a=".$edificio."\&id=".$parcela."\&c=.*)\">.*</a>#",$tmp_build,$match)){
        $link_upgrade=$match[1];
        $this->d_("\n\n $link_upgrade\n");
        $tmp_cmd=$this->curl_->get($this->server."/".$link_upgrade);
        return true;
       }
     } 
    return false;
    }

  function logout(){
     $this->curl_->get($this->server."/logout.php");
    }

  function d_($str){
   if($this->debug) echo $str;
     }
}