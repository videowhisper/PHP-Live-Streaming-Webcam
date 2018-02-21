<?php
include("settings.php");

function append_log($text)
{
$dfile = fopen("vwlog.txt","a"); 
fputs($dfile,$text); 
fclose($dfile); 
}
?>