<?php
//This script controls login and parameters to broadcasting inteface (is called by live_broadcast.swf)

include("inc.php");

$username=$_GET['room_name'];
$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);

$userPicture=urlencode("defaultpicture.png");
$userLink=urlencode("http://www.videowhisper.com/");

$msg="";
$loggedin=1;
if (in_array($username,$ban_names))
{
	$loggedin=0;
	$msg=urlencode("<a href=\"http://www.videowhisper.com\">You are not allowed to broadcast. Contact for details.</a>");
}

if (!$username)
{
	$loggedin=0;
	$msg=urlencode("No channel name received. Use the channel creation form or an integration that implements this variable.");
}

function baseURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }

 return substr($pageURL,0,strrpos($pageURL,"/"))."/";
}


$base=baseURL();
$linkcode=$base."channel.php?n=".urlencode($username);
$imagecode=$base."snapshots/".urlencode($username).".jpg";
$swfurl=$base."live_watch.swf?n=".urlencode($username);
$swfurl2=$base."live_video.swf?n=".urlencode($username);

$embedcode =<<<EMBEDEND
<object width="100%" height="350"><param name="movie" value="$swfurl" /><param name="base" value="$base" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="scale" value="noscale" /><param name="salign" value="lt" /><embed src="$swfurl" base="$base" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="350" scale="noscale" salign="lt"></embed></object>
EMBEDEND;
$embedvcode =<<<EMBEDEND2
<object width="320" height="240"><param name="movie" value="$swfurl2" /><param name="base" value="$base" /><param name="scale" value="exactfit"/><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><embed src="$swfurl2" base="$base" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="240" scale="exactfit"></embed></object>
EMBEDEND2;

//replace bad words or expression
$filterRegex=urlencode("(?i)(fuck|cunt)(?-i)");
$filterReplace=urlencode(" ** ");

//layout obtained by sending in public chat box "/videowhisper layout"; fill in new line between layoutEND markers
$layoutCode=<<<layoutEND
id=0&label=Webcam&x=10&y=40&width=242&height=235&resize=true&move=true; id=1&label=Chat&x=260&y=40&width=340&height=235&resize=true&move=true; id=2&label=Users&x=610&y=40&width=180&height=235&resize=true&move=true
layoutEND;

if ($manualArchiving)
{
$manualArchivingStart = $manualArchiving . '&action=startRecording&streamname=' . urlencode($username);
$manualArchivingStop = $manualArchiving . '&action=stopRecording&streamname=' . urlencode($username);
}

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&serverProxy=best&serverRTMFP=<?=$rtmfp_server?>&p2pGroup=VideoWhisper&enableRTMP=1&enableP2P=0&supportRTMP=1&supportP2P=0&alwaysRTMP=1&alwaysP2P=0&room=<?=$username?>&welcome=Welcome!&username=<?=$username?>&userType=3&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&overLogo=logo.png&overLink=http://www.videowhisper.com&webserver=&msg=<?=$msg?>&loggedin=<?=$loggedin?>&linkcode=<?=urlencode($linkcode)?>&embedcode=<?=urlencode($embedcode)?>&embedvcode=<?=urlencode($embedvcode)?>&imagecode=<?=urlencode($imagecode)?>&room_limit=&showTimer=1&showCredit=1&disconnectOnTimeout=1&statusInterval=10000&camWidth=640&camHeight=480&camFPS=30&micRate=22&camBandwidth=75000&camPicture=50&micGain=50&bufferLive=0.2&bufferFull=0.2&videoCodec=H264&codecProfile=main&codecLevel=3.1&soundCodec=NellyMoser&soundQuality=9&showCamSettings=1&advancedCamSettings=1&camMaxBandwidth=250000&disableBandwidthDetection=0&limitByBandwidth=1&configureSource=1&generateSnapshots=1&snapshotsTime=60000&onlyVideo=0&noVideo=0&noEmbeds=0&noTitle=0&defaultTitle=LIVE&labelColor=FFFFFF&writeText=1&floodProtection=1&privateTextchat=1&filterRegex=<?=$filterRegex?>&filterReplace=<?=$filterReplace?>&externalInterval=5000&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=0&verboseLevel=2&pauseAllowed=1&pauseOnStart=0&manualArchivingStart=<?=urlencode($manualArchivingStart)?>&manualArchivingStop=<?=urlencode($manualArchivingStop)?>&loadstatus=1

