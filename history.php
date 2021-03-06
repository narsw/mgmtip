<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")$settings = $CIP->settings;
$CIP->loadHistory();
$CIP->loadVMHistory();

ksort($CIP->history);

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>IP Histry</title>
 </head>
 <body>
  <h1>IP Address History</h1>
  <hr />

  <p>IP Address List</p>
  <h3>target:<?php echo($settings["target"]); ?></h3>
  <table border=1>
<?php
/*  if($CIP->history != "")
  {
    foreach($CIP->history as $ip => $type)
    {
      echo("<tr><td><a href=./getHostInfo.php?hostip=$ip>$ip</a></td>");
      for($i=0; $i<20; $i++)
      {
        if($type["ip"][$i]!="")echo("<td>".date("Y-m-d H:i:s",$type["ip"][$i]["datetime"])."</td>");
        else echo("<td>-</td>");
      }
      echo("</tr>");
    }
  }
*/
?>
  </table>
  <hr />
  <table border=1>
<?php
  if($CIP->history != "")
  {
    foreach($CIP->history as $ip => $type)
    {
      $mac1 = $type["mac"][0]["value"];
      echo("<tr><td><a href=./getHostInfo.php?hostip=$ip>$ip</a></td>");
      echo("<td>".$CIP->vmhistory[$mac1][0]["value"]."</td>");
      for($i=0; $i<20; $i++)
      {
        //if($type["mac"][$i]!="")echo("<td>".$type["mac"][$i]["value"]."<br />".date("Y-m-d H:i:s",$type["mac"][$i]["datetime"])."</td>");
        if($type["mac"][$i]!="")echo("<td>".$type["mac"][$i]["value"]."</td>");
        else echo("<td>-</td>");
      }
      echo("</tr>");
    }
  }
?>
  </table>
  <hr />
  <table border=1>
<?php
  if($CIP->vmhistory != "")
  {
    foreach($CIP->vmhistory as $mac => $item)
    {
      echo("<tr><td>$mac</td>");
      for($i=0; $i<20; $i++)
      {
        //if($type["mac"][$i]!="")echo("<td>".$type["mac"][$i]["value"]."<br />".date("Y-m-d H:i:s",$type["mac"][$i]["datetime"])."</td>");
        if($item[$i]!="")echo("<td>".$item[$i]["value"]."</td>");
        else echo("<td>-</td>");
      }
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

