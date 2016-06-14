<?php
require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";

$CIP = new checkIPs();
//$CIP->saveSetting($settings);
//$CIP->loadSetting();
//print_r($CIP->settings);

//$CIP->loadHistory();

//$CIP->checkIPs();
//print_r($CIP->history);

//$CIP->saveHistory($CIP->history);

//print_r($CIP->getLocalArpTable());

//print_r($CIP->getArpTable());

$CIP->loadVMHistory();
print_r($CIP->vmhistory);
print_r($CIP->vmhistory["00505694042D"]);
?>
