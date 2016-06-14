<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")$settings = $CIP->settings;

if($_GET["cip"]==1){
  $CIP->loadHistory();
  $IPs = $CIP->checkIPs();
  $CIP->saveHistory($CIP->history);
}
//print_r($IPs);

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>Check IP</title>
 </head>
 <body>
  <h1>Check IP Address</h1>
  <hr />

  <p>IP Address List</p>
  <h3>Target Network[<?php echo($settings["target"]."] Exclude Address[".$settings["exclude"]); ?>]</h3>
<?php echo("<form><input type=hidden name=cip value=1><input type=submit value=execute></form>"); ?>
  <table border=1>
<?php
  if($IPs != "")
  {
    foreach($IPs as $ip)
    {
      echo("<tr><td><a href=./getHostInfo.php?hostip=$ip>$ip</a></td>");
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

