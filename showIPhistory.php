<?php

require_once("checkIPs.class.php");

$settings["target"] = "172.31.4.0/24";
$settings["command"] = "/usr/bin/nmap -PB -n ";
$settings["pattern"] = "/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/";
$registed = false;

$path_history="/var/www/html/mgmtip/history2.txt";
$CIP = new checkIPs();
$CIP->loadSetting();
//if($CIP->settings != "")$settings = $CIP->settings;
$CIP->loadHistory($path_history);
$IPs = null;

$req_ip = $_GET["req_ip"];
$req_prefix = $_GET["req_prefix"];

if($CIP->settings != "")
{
  if($CIP->history != ""){
    $iparray = $CIP->history;
    $CIP->history = null;
  }else{
    $iparray = array();
  }
  headerHtml($iparray);

  if($req_ip != null)
  {
    showIpHtml($iparray,$req_ip);
  }else if($req_prefix != null)
  {
    showListHtml($iparray,$req_prefix);
  }else{
    $prefix = "10.44.141.";
    showListHtml($iparray,$prefix);

    $prefix = "10.44.68.";
    showListHtml($iparray,$prefix);
  }
}

/*
ipListに格納された内容を一覧表示
*/
function showList($iplist,$prefix)
{
  for($i=1; $i<255; $i++)
  {
    $ip = $prefix.$i;
    if(array_key_exists($ip, $iplist))
    {
      echo($ip."\t");
      foreach($iplist[$ip] as $n)
      {
        if($n == 1)echo("!");
        else echo(".");
      }
      echo(PHP_EOL);
    }
  }
}

/*
ipListに格納された内容をHTML形式で一覧表示
*/
function showListHtml($iplist,$prefix)
{
  echo("<table border=1>".PHP_EOL);
  showTableHeader($iplist);
  for($i=1; $i<255; $i++)
  {
    $ip = $prefix.$i;
    if(array_key_exists($ip, $iplist))
    {
      $cnt = 0;
      echo("<tr>".PHP_EOL);
      echo("<td>".$ip."</td><td>");
      foreach($iplist[$ip] as $n)
      {
        $cnt++;
        if($n == 1)echo("!");
        else echo(".");
        if($cnt>5){
          echo("</td><td>");
          $cnt = 0;
        }
      }
      echo("</td>".PHP_EOL);
      echo("</tr>".PHP_EOL);
    }
  }
  echo("</table>".PHP_EOL);
}

/*
単一のIPについてHTMLで表を作成する
*/
function showIpHtml($iplist,$ip)
{
  echo("<table border=1>".PHP_EOL);
  showTableHeader($iplist);
  if(array_key_exists($ip, $iplist))
  {
    $cnt = 0;
    echo("<tr>".PHP_EOL);
    echo("<td>".$ip."</td><td>");
    foreach($iplist[$ip] as $n)
    {
      $cnt++;
      if($n == 1)echo("!");
      else echo(".");
      if($cnt>5){
        echo("</td><td>");
        $cnt = 0;
      }
    }
    echo("</td>".PHP_EOL);
    echo("</tr>".PHP_EOL);
  }
  echo("</table>".PHP_EOL);
}

/*
表のヘッダをHTMLで作成
*/
function headerHtml($iplist)
{
  $info = getInfo($iplist);
  echo("<h1>IP Adress Activity</h1>".PHP_EOL);
  echo("<hr>".PHP_EOL);
  echo("<h3>IP Address Count : ".$info["ip_count"]."</h3>".PHP_EOL);
  echo("<h3>Active IP Address Count : ".$info["active_ip_count"]."</h3>".PHP_EOL);
  echo("<hr>".PHP_EOL);
  echo("".PHP_EOL);
}

/*
IPListの情報を取得
　IPの数
　死活確認結果の数
*/
function showTableHeader($iplist)
{
  $info = getInfo($iplist);
  $cnt = 1;
  echo("<tr>".PHP_EOL);
  echo("<th></th>".PHP_EOL);
  for($i=0; $i<$info["activity_count"]; $i++)
  {
    if($i%6==0)
    {
      echo("<th>".($cnt+1)."時間</th>".PHP_EOL);
      $cnt+=2;
    }
  }
  echo("</tr>".PHP_EOL);
}

function getInfo($iplist)
{
  $ip = "10.44.141.1";
  $info["activity_count"] = 0;
  $info["ip_count"] = 0;
  $info["active_ip_count"] = 0;

  if($iplist != null)
  {
    $info["ip_count"] = count($iplist);
    if(array_key_exists($ip,$iplist))
    {
      //echo(count($iplist[$ip]));
      $info["activity_count"] = count($iplist[$ip]);
    }
    foreach($iplist as $ips)
    {
      if($ips[0]==1)$info["active_ip_count"]++;
    }
  }
  return($info);
}

?>
