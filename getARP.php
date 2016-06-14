<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")$settings = $CIP->settings;

if($_GET["arp"]==1){
  $CIP->loadHistory();
  $MACs = $CIP->getARP();
  $lMACs = $CIP->getLocalArpTable();
  $CIP->saveHistory($CIP->history);
}
//print_r($IPs);

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>get MAC Address</title>
 </head>
 <body>
  <h1>get MAC Address</h1>
  <hr />

  <p>IP Address List</p>
  <h3>target:<?php echo($settings["sw_ipaddress"]); ?></h3>
<?php echo("<form><input type=hidden name=arp value=1><input type=submit value=execute></form>"); ?>
  <table border=1>
<?php
  if($MACs != "")
  {
    foreach($MACs as $mac)
    {
      echo("<tr>");
      foreach($mac as $item)
      {
        echo("<td>$item</td>");
      }
      echo("</tr>");
    }
  }
?>
  </table>
  <hr />
  <table border=1>
<?php
  if($lMACs != "")
  {
    foreach($lMACs as $mac => $ip)
    {
      echo("<tr>");
      echo("<td>$ip</td>");
      echo("<td>$mac</td>");
      echo("</tr>");
    }
  }
?>
  </table>
  <hr />
  <footer>
   2014/06/23
  </footer>
 </body>
</html>

