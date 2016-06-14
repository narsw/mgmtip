<?php
require_once("checkIPs.class.php");

$CIP = new checkIPs();
$CIP->loadSetting();
if($CIP->settings != "")
{
  if($CIP->settings["target"] != ""){
    $CIP->loadHistory();
    $IPs = $CIP->checkIPs();
    $CIP->saveHistory($CIP->history);
  }
  if($CIP->settings["sw_ipaddress"] != ""){
    $CIP->loadHistory();
    $IPs = $CIP->getARP();
    $CIP->saveHistory($CIP->history);
  }
//print_r($IPs);
}
?>

