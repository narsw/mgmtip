<?php

require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;
date_default_timezone_set('Asia/Tokyo');
$date = date("YmdHis");

$path_history="/var/www/html/mgmtip/history2.txt";
$path_history_bkp="/var/www/html/mgmtip/history/history2_".$date.".txt";
$CIP = new checkIPs();
$CIP->loadSetting();
//if($CIP->settings != "")$settings = $CIP->settings;
$CIP->loadHistory($path_history);
$IPs = null;

if($CIP->settings != "")
{
  if($CIP->history != ""){
    $iparray = $CIP->history;
    $CIP->history = null;
  }else{
    $iparray = array();
  }
  if($CIP->settings["target"] != ""){
    $IPs = $CIP->checkIPs();
  }
  //print_r($CIP->history);
  //print_r($IPs);}

  $prefix = "10.44.141.";
  $iparray = addList($iparray,$IPs,$prefix);

  $prefix = "10.44.68.";
  $iparray = addList($iparray,$IPs,$prefix);

  if(getHistoryCount($iparray) > 288)
  {
    //$newiparray = lotateList($iparray);
    //$CIP->saveHistory($newiparray, $path_history);
    //$CIP->saveHistory($iparray, $path_history_bkp);
    //print_r($newiparray);
  }else{
    //$CIP->saveHistory($iparray, $path_history);
  }
  $CIP->saveHistory($iparray, $path_history);
}

function addList($iplist,$IPs,$prefix)
{
  for($i=1; $i<255; $i++)
  {
    $ip = $prefix.$i;
    if(array_key_exists($ip, $iplist))
    {
      if(in_array($ip, $IPs))
      {
        array_unshift($iplist[$ip],"1");
      }else{
        array_unshift($iplist[$ip],"0");
      }
     }else{
      $iplist[$ip] = array();
      if(in_array($ip, $IPs))
      {
        array_unshift($iplist[$ip],"1");
      }else{
        array_unshift($iplist[$ip],"0");
     }
   }
  }
  return($iplist);
}

function lotateList($iplist)
{
  $newlist = array();
  foreach($iplist as $ip => $value)
  {
    //$tmp = array();
    if(count($value) > 144)
    {
      for($i=0; $i<144; $i++)
      {
        $newlist[$ip][$i] = $value[$i];
      }
    }else{
    
    }
    //array_unshift($newlist,$tmp);
  }
  return($newlist);
}

function getHistoryCount($iplist)
{
  $cnt = count($iplist["10.44.141.1"]);
  return($cnt); 
}

?>
