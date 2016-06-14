<?php
require_once("checkIPs.class.php");

$CIP = new checkIPs();
$CIP->loadSetting();
$CIP->loadVMHistory();

if($_FILES["vminfofile"] != "")
{
  if (is_uploaded_file($_FILES["vminfofile"]["tmp_name"])) {
    if (move_uploaded_file($_FILES["vminfofile"]["tmp_name"], "files/vminfo.txt")) {
      chmod("files/vminfo.txt", 0644);
      echo "vminfo.txtをアップロードしました。";
    } else {
      echo "ファイルをアップロードできません。";
    }
  } else {
    echo "ファイルが選択されていません。";
  }

  $file = 'files/vminfo.txt';
  $temp = fopen($file, "r");
 
  while (($data = fgetcsv($temp)) !== FALSE) {
    $mac = $data[0];
    $vm = str_replace("\r","",$data[1]);
    if($mac!= "") $CIP->addVMHistory($mac, $vm);
    //print_r($data);
  }
  fclose($temp);

  if($CIP->vmhistory != "")
  {
    $CIP->saveVMHistory($CIP->vmhistory);
  } 

}
else if($_GET['getweb']==1)
{
  $file = 'http://10.44.68.1:18080/script/kekka.txt';
  $temp = fopen($file, "r");

  while (($data = fgetcsv($temp)) !== FALSE) {
    $mac = $data[0];
    $vm = str_replace("\r","",$data[1]);
    if($mac!= "") $CIP->addVMHistory($mac, $vm);
    //print_r($data);
  }
  fclose($temp);

  if($CIP->vmhistory != "" && $_GET['getwebregist']==1)
  {
    $CIP->saveVMHistory($CIP->vmhistory);
  }
}
//print_r($IPs);
//print_r($CIP->vmhistory);

?>


<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
 <head>
  <title>Load VM Information</title>
 </head>
 <body>
  <h1>Load VM Information</h1>
  <hr />
  <h3>Load CSV File</h3>
  <form action="loadVMInfo.php" method="post" enctype="multipart/form-data">
    <input type="file" name="vminfofile">
    <input type="submit" value=Load>
  </form>
  <hr />
  <p><a href="http://10.44.68.118/mgmtip/loadVMInfo.php?getweb=1&getwebregist=1">Regist Web</a></p>
  <hr />
  <div id="macinfo" name="macinfo">
<?php
  if($CIP->vmhistory != "")
  {
    echo("<table border=1><tr><td>MAC Address</td><td>VM Name</td></tr>");
    foreach($CIP->vmhistory as $mac => $vm)
    {
      echo("<tr><td>".$mac."<br />");
      foreach ($vm as $item)
      {
        echo("<td>".$item["value"]."</td>");
      }
      echo("</tr>");
    }
    echo("</table>");
  }
?>
  </div>
  <hr />
  <footer>
   2014/06/23
  </footer>
 </body>
</html>

