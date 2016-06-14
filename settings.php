<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["command_gethost"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")$settings = $CIP->settings;

if($_GET["target"]!="")
{
  if(preg_match($settings["pattern"], $_GET["target"])==1)
  {
    $settings["target"] = $_GET["target"];
  }
  if(preg_match($settings["pattern"], $_GET["exclude"])==1)
  {
    $settings["exclude"] = $_GET["exclude"];
  }
  if($_GET["sw_ipaddress"]!="")$settings["sw_ipaddress"] = $_GET["sw_ipaddress"];
  if($_GET["sw_passwd"]!="")$settings["sw_passwd"] = $_GET["sw_passwd"];
  if($_GET["sw_enpasswd"]!="")$settings["sw_enpasswd"] = $_GET["sw_enpasswd"];
  if($_GET["command"]!="")$settings["command"] = $_GET["command"];
  if($_GET["command_gethost"]!="")$settings["command_gethost"] = $_GET["command_gethost"];
  if($_GET["pattern"]!="")$settings["pattern"] = $_GET["pattern"];
  //if($_GET[""]!="")$settings[""] = $_GET[""];
  //if($_GET[""]!="")$settings[""] = $_GET[""];
  $CIP->saveSetting($settings);
  $registed = true;
}

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>Settings</title>
 </head>
 <body>
  <h1>Settings</h1>
  <?php if($registed==true)echo("<p>Registed</p>"); ?>
  <hr />

  <form action="./settings.php" method="GET">
  <p>Target IP Address List</p>

  <ul>
   <li>Example</li>
   <li>172.17.0.0/24</li>
   <li>172.31.17.0-200</li>
  </ul>

  <ul>
  <li>Tarrget IP
   <ul><li><input type="input" name="target" size=60 value="<?php echo($settings["target"]); ?>" /></li></ul>
  </li>
  <li>Exclude IP
   <ul><li><input type="input" name="exclude" size=60 value="<?php echo($settings["exclude"]); ?>" /></li></ul>
  </li>
  <li>Switch IP ( for get MAC adress )
   <ul><li><input type="input" name="sw_ipaddress" size=60 value="<?php echo($settings["sw_ipaddress"]); ?>" /></li></ul>
  </li>
  <li>Switch Password ( for get MAC adress )
   <ul><li><input type="input" name="sw_passwd" size=60 value="<?php echo($settings["sw_passwd"]); ?>" /></li></ul>
   <ul><li><input type="input" name="sw_enpasswd" size=60 value="<?php echo($settings["sw_enpasswd"]); ?>" /></li></ul>
  </li>
  <li>Base Commad ( for check IP Address )
   <ul><li><input type="input" name="command" size=60 value="<?php echo($settings["command"]); ?>" /></li></ul>
  </li>
  <li>Base Command ( for get Host Information )
   <ul><li><input type="input" name="command_gethost" size=60 value="<?php echo($settings["command_gethost"]); ?>" /></li></ul>
  </li>
  <li>Pattern ( for check IP Address Pattern )
   <ul><li><input type="input" name="pattern" size=60 value="<?php echo($settings["pattern"]); ?>" /></li></ul>
  </li>
  <li><input type="submit" value="Save" /></li>
  </ul>

  </form>

  <hr />
  <footer>
   2014/06/23
  </footer>
 </body>
</html>

