<?php
include("header.php");
  if (!$stream=$_GET['n']) exit;

  include_once("incsan.php");
  sanV($stream);


$cmd = "ps aux | grep '/i_$stream -i rtmp'";
exec($cmd, $output, $returnvalue);
//var_dump($output);

$transcoding = 0;
foreach ($output as $line) if (strstr($line, "ffmpeg"))
{
$columns = preg_split('/\s+/',$line);
$cmd = "kill -9 " . $columns[1];
exec($cmd, $output, $returnvalue);
echo "<BR>Closing ".$columns[1]." CPU: ".$columns[2]." Mem: ".$columns[3];
$transcoding = 1;
}

if (!$transcoding)
{
echo "Transcoder not found for $stream";
}
?>
