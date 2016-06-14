<?php
require_once("PHPTelnet.php");

class checkIPs
{
//Property
var $path_config = "settings.cfg";
var $settings = "";
var $history = "";
var $vmhistory = "";

function checkIPs()
{
  if($this->settings != "")
  {
    $result = $this->_nmap();
    //print_r($result);
    if($result != "")
    {
      foreach($result as $ip)
      {
        $this->addHistory($ip);  
      }
    }
    return($result);
  }
}

function _nmap()
{
 //print_r($this->settings);
  if($this->settings != "")
  {
    $command = $this->settings["command"];
    if($this->settings["exclude"] != "")$command .= "--exclude " . $this->settings["exclude"];
    $command .= " ".$this->settings["target"];
    $IPs = "";
    exec($command,$result);

    $matches = preg_grep($this->settings["pattern"], $result);
    //print_r($matches);

    foreach($matches as $line)
    {
       preg_match($this->settings["pattern"], $line, $match);
       if(count($match)>0)$IPs[]=$match[0];
    }
    return($IPs);
  }
}

function getHostInfo($hostip)
{
  if($hostip != "")
  {
    $result = $this->_nmap_host_info($hostip);
    return($result);
  }
}

function _nmap_host_info($hostip)
{
  if($this->settings != "")
  {
    $command = $this->settings["command_gethost"];
    if($this->settings["exclude"] != "")$command .= "--exclude " . $this->settings["exclude"];
    $command .= " ".$hostip;
//echo($command);
    $IPs = "";
    exec($command,$result);

    //$matches = preg_grep($this->settings["pattern"], $result);
    //print_r($matches);
/*
    foreach($matches as $line)
    {
       preg_match($this->settings["pattern"], $line, $match);
       if(count($match)>0)$IPs[]=$match[0];
    }
    return($IPs);
*/
    return($result);
  }
}

function getARP()
{ 
  if($this->settings["sw_ipaddress"] != "" && $this->settings["sw_passwd"] != "")
  {
    $result = $this->_getStatus_cisco();
    if($result != "")
    {
      $arp = $this->_analyseCiscoResult($result);
      //print_r($arp);
      if($arp != "")
      {
        foreach($arp as $mac)
        {
          if($mac["Address"] != "" && $mac["HardwareAddr"] != "" ) $this->addMACHistory($mac["Address"],$mac["HardwareAddr"]);
        }
      }
    }
    return($arp);
  }
}

function _getStatus_cisco()
{
  $res = "";
  $telnet = new PHPTelnet();
  $result = $telnet->Connect($this->settings["sw_ipaddress"], $this->settings["sw_passwd"]);

  if ($result == 0) {
    // Enter Enable
    $telnet->DoCommand('en', $result);

    //Enter Enable Mode Password
    $telnet->DoCommand($this->settings["sw_enpasswd"], $result);

    //set terminal length 0
    $telnet->DoCommand('ter len 0 ', $result);

    //Show ARP
    $telnet->DoCommand('sh ip arp | include Internet', $result);
    //echo "Res:".$result."\r\n";
    $res = $result;

    $telnet->Disconnect();
  }
  else
  {
    result("Fail");
  }
  return $res;
}

function _analyseCiscoResult($result)
{
  $arp = "";
  $lines = explode("\r\n",$result);
  $lines = preg_grep("/ARPA/",$lines);

  foreach($lines as $line)
  {
    $tmp = preg_split("/[ ]+/",$line);
    if($tmp != "")
    {
      $arpresult["Protocol"]=$tmp[0];
      $arpresult["Address"]=$tmp[1];
      $arpresult["Age"]=$tmp[2];
      $arpresult["HardwareAddr"]=$tmp[3];
      $arpresult["Type"]=$tmp[4];
      $arpresult["Interface"]=$tmp[5];
      $arp[] = $arpresult;
    }
  }
  return($arp);
}

function getLocalArpTable()
{
  $table = array();
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exec('arp -a', $output); // for Windows
  }
  else {
    exec('/sbin/arp -a -n', $output); // for Linux(debian)
  }
  foreach ($output as $line) {
    if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}).*([0-9a-f]{2}[\-:][0-9a-f]{2}[\-:][0-9a-f]{2}[\-:][0-9a-f]{2}[\-:][0-9a-f]{2}[\-:][0-9a-f]{2})/i', $line, $matches)) 
    {
      $ip = $matches[1];
      $mac = strtoupper(str_replace(array('-', ':'), '', $matches[2]));
      $table[$mac] = $ip;
      $this->addMACHistory($ip,$mac);
    }
  }
  return($table);
}

function getArpTable()
{
  $table = array();
  $ip = '10.44.68.';
  for($i=1; $i<255; $i++)
  {
    $ipaddr = $ip.$i;
    exec('/sbin/arping -D -w 1 -I eth1 '.$ipaddr.' | grep Unicast', $output);
    foreach ($output as $line) 
    {
      if (preg_match('/.*\[(.*)\]/',$line,$matches))
      {
        if(count($matches)>1)
        {
          $table[$ipaddr] = $matches[1];
          echo("$ipaddr : ".$matches[1].PHP_EOL);
          break;
        }
      }
    }
  }
  return($table);
}

function _getMACAddr($mac)
{
  $search = Array(".",":");
  $mac = str_replace($search,"",$mac);
  $mac = strtoupper($mac);
  return($mac);
}

function isAllowNetwork($ip)
{
  $allowip ="10.44.141. 10.44.68. 172.17.17.";
  $allowip = explode(" ",$allowip);

  foreach($allowip as $allow)
  {
    $pattern = "/$allow/";
    if(preg_match($pattern, $ip)==1)return(true);
  }
  return(false);
}

function addVMHistory($mac,$vm)
{
  $mac = $this->_getMACAddr($mac);
  $value["datetime"] = time();
  $value["value"] = $vm;
  if($this->vmhistory != "")
  {
    if(array_key_exists($mac, $this->vmhistory))
    {
      $this->vmhistory[$mac][] = $value;
    } else {
      $this->vmhistory[$mac][] = $value;
    }
  }else{
    $this->vmhistory[$mac] = array();
    $this->vmhistory[$mac][] = $value;
  }
}

function addMACHistory($ip,$mac)
{
  $mac = $this->_getMACAddr($mac);
  $value["datetime"] = time();
  $value["value"] = $mac;
  if(!$this->isAllowNetwork($ip))return("");
  if($this->history != "")
  {
    if(array_key_exists($ip, $this->history))
    {
      //array_unshift($this->history[$ip]["mac"],$value);
      $this->history[$ip]["mac"][] = $value;
    } else {
      //array_unshift($this->history[$ip]["mac"],$value);
      $this->history[$ip]["mac"][] = $value;
    }
  }else{
    $this->history[$ip]["mac"] = array();
    //array_unshift($this->history[$ip]["mac"],$value);
    $this->history[$ip]["mac"][] = $value;
  }
}

function addHistory($ip)
{
  $value["datetime"] = time();
  $value["value"] = "1";

  if(!$this->isAllowNetwork($ip))return("");
  if($this->history != "")
  {
    if(array_key_exists($ip, $this->history))
    {
      array_unshift($this->history[$ip]["ip"],$value);
    } else {
      $this->history[$ip]["ip"] = array();
      array_unshift($this->history[$ip]["ip"],$value);
    }
  }else{
    $this->history[$ip]["ip"] = array();
    array_unshift($this->history[$ip]["ip"],$value);
  }
}

function loadVMHistory($path_history="/var/www/html/mgmtip/vmhistory.txt")
{
  if (file_exists($path_history)) {
    $tmp = file_get_contents($path_history);
    $this->vmhistory = json_decode($tmp, true);
  } else {
    $this->vmhistory = "";
  }
}

function saveVMHistory($history, $path_history="/var/www/html/mgmtip/vmhistory.txt")
{
  if (!file_exists($path_history)) {
    touch($path_history);
  } else {
    //echo ('すでにファイルが存在しています。file name:' . $path_history);
  }

  if (!file_exists($path_history) && !is_writable($path_history) || !is_writable(dirname($path_history)))
  {
    echo "書き込みできないか、ファイルがありません。",PHP_EOL;
    exit(-1);
  }

  $fp = fopen($path_history,'w') or dir('ファイルを開けません');

  fwrite($fp, sprintf(json_encode($history)));
  fclose($fp);
}

function loadHistory($path_history="/var/www/html/mgmtip/history.txt")
{
  if (file_exists($path_history)) {
    $tmp = file_get_contents($path_history);
    $this->history = json_decode($tmp, true);
  } else {
    $this->history = "";
  }
}

function saveHistory($history, $path_history="/var/www/html/mgmtip/history.txt")
{
  if (!file_exists($path_history)) {
    touch($path_history);
  } else {
    //echo ('すでにファイルが存在しています。file name:' . $path_history);
  }

  if (!file_exists($path_history) && !is_writable($path_history) || !is_writable(dirname($path_history)))
  {
    echo "書き込みできないか、ファイルがありません。",PHP_EOL;
    exit(-1);
  }

  $fp = fopen($path_history,'w') or dir('ファイルを開けません');

  fwrite($fp, sprintf(json_encode($history)));
  fclose($fp);
}

function loadSetting($path_config="/var/www/html/mgmtip/settings.cfg")
{
  if (file_exists($path_config)) {
    $tmp = file_get_contents($path_config);
    $this->settings = json_decode($tmp, true);
  } else {
    $this->settings = "";
  }
}

function saveSetting($settings, $path_config="/var/www/html/mgmtip/settings.cfg")
{
  if (!file_exists($path_config)) {
    touch($path_config);
  } else {
    //echo ('すでにファイルが存在しています。file name:' . $path_config);
  }  

  if (!file_exists($path_config) && !is_writable($path_config) || !is_writable(dirname($path_config)))
  {
    echo "書き込みできないか、ファイルがありません。",PHP_EOL;
    exit(-1);
  }

  $fp = fopen($path_config,'w') or dir('ファイルを開けません');

  fwrite($fp, sprintf(json_encode($settings)));
  fclose($fp);
  //print_r($settings);
} 

function raw_json_encode($input) {
    
    return preg_replace_callback(
        '/\\\\u([0-9a-zA-Z]{4})/',
        function ($matches) {
            return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
        },
        json_encode($input)
    );
}

}

?>
