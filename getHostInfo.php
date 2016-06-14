<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")$settings = $CIP->settings;

if($_GET["hostip"] != ""){
  $CIP->loadHistory();
  $CIP->loadVMHistory();
  $HOST = $CIP->getHostInfo($_GET["hostip"]);
  //$CIP->saveHistory($CIP->history);
}
//print_r($IPs);

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>Host Information</title>
 </head>
 <body>
  <h1>Host Information</h1>
  <hr />

  <p>Host Information</p>
  <h3>Target Host IP[<?php echo($_GET["hostip"]); ?>]</h3>
<?php echo("<form><input name=hostip value=''><input type=submit value=execute></form>"); ?>
  <div id="macinfo" name="macinfo">
<?php
  if($CIP->history[$_GET["hostip"]] != "")
  {
    echo("<table border=1><tr><td>MAC Address</td>");
    foreach($CIP->history[$_GET["hostip"]]["mac"] as $mac)
    {
      $mac1 = $mac["value"];
      $vm = $CIP->vmhistory[$mac1];
if(array_key_exists("00505694042C",$CIP->vmhistory))echo("OK");
print_r("$mac1 : $vm");
      echo("<td>".$mac["value"]."<br />");
      echo($vm[0]["value"]."<br />");
      echo(date("Y-m-d H:i:s",$mac["datetime"])."</td>");
    }
    echo("</tr></table>");
  }
?>
  </div>
  <div id="hostinfo" name="hostinfo">
<?php
  if($HOST != "")
  {
    //print_r($HOST);
    //print(str_replace("\r\n","<br />",$HOST));
    foreach($HOST as $line)
    {
      print($line."<br />");
    }
  }
?>
  <hr />
  <footer>
   2014/06/23
  </footer>
 </body>
</html>

