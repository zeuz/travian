<form name action=in.php method=post>
<table width="100%"  border="0">
  <tr>
    <td width="4%">&nbsp;</td>
    <td width="21%"><b>Agregar trabajo:</b> </td>
    <td width="18%">&nbsp;</td>
    <td width="23%">&nbsp;</td>
    <td width="33%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp; </td>
    <td>&nbsp; </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Server
      <select name="server" size="1">
        <option value="s2.travian.net" selected>s2</option>
		<option value="s1.travian.net">s1</option>
		<option value="s3.travian.net">s3</option>
		<option value="s4.travian.net">s4</option>
		<option value="s5.travian.net">s5</option>
		<option value="s6.travian.net">s6</option>
      </select></td>
    <td>User: 
      <input name="user" type="text" size="12"></td>
    <td>Password:
      <input name="pass" type="password" size="12"></td>
    <td>&nbsp; </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Upgrade:</td>
    <td>Parcela:
      <input name="parcela" type="text" size="5" maxlength="2"></td>
    <td>no se puede construir sobre parcela 40=muralla, 39=plaza de reuniones, 26=edificio principal<td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Construir:</td>
    <td><select name="edificio">{$EDIFICIOS}
    <OPTION value="" selected> </OPTION>
    </select></td>
    <td><input type="submit" name="submit" value="Agregar"></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
   <td>{$MSG}</td>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
  </tr>


  <tr>
  <td>&nbsp;</td>
    <td><b>Trabajos por hacer: </b></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 <tr>
  <td>&nbsp;</td>
  <td align="right"></td>
  <td>&nbsp; </td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
    <td width="1%">&nbsp;</td> 
 </tr>
 {$ACTIVOS}
  <tr>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
   <td>{$BOTON_QUITAR}</td>
  </tr>
     
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>


  <tr>
     <td>&nbsp;</td>
    <td><b>Trabajos realizados:</b></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 {$REALIZADOS}
 

 <tr>
  <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>

</table>
<input type="hidden" name="username" value="{$USERNAME}">
<input type="hidden" name="op" value "">
</form><br><br>
