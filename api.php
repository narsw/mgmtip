<?php
require_once("checkIPs.class.php");
$CIP = new checkIPs();
$CIP->loadSetting();
$CIP->loadHistory();

//$CIP->checkIPs();
//$CIP->saveHistory($CIP->history);

if($_GET["cmd"] == "config")echo(json_encode($CIP->settings));
else if($_GET["cmd"] == "history")echo(json_encode($CIP->history));

//print_r($_GET);
?>
