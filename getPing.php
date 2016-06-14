<?php

$host = $_GET["hostip"];
$service_port = $_GET["port"];

if($service_port != null)
{
  $up = port_ping($host,$service_port);
}else{
  $up = ping($host);
}

if($up == TRUE) image(0);
else image(-1);

function ping($host)
{
        $r = exec(sprintf('ping -c 1 -W 2 %s', escapeshellarg($host)), 
                $res, $rval);
        //pingの結果を表示
        //print_r($r);
        return $rval === 0;
}

function port_ping($host,$port=80,$timeout=2)
{
        $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
        if ( ! $fsock ) {
        	return FALSE;
        } else{
           return TRUE;
        }
}

function image($flg)
{
  $img_ok = "img_ok.png";
  $img_ng = "img_ng.png";
  header('Content-type: image/jpeg');
  if($flg==0)$image = $img_ok;
  else $image = $img_ng;
  readfile('img/'.$image);
}

?>
