<?php
//vs_login.php controls watch interface (video & chat & user list) login called by live_watch.swf

include("inc.php");
$username="VW".base_convert((time()-1224350000).rand(0,10),10,36);
$userType=0;
$visitor=1; //ask for username

$userPicture=urlencode("defaultpicture.png");
$userLink=urlencode("http://www.videowhisper.com/");

$room=$_GET['room_name'];

$msg="";
$loggedin=1;
if (in_array($username,$ban_names))
{
	$loggedin=0;
	$msg=urlencode("<a href=\"http://www.videowhisper.com\">You are not allowed to watch Contact for details.</a>");
}

if (in_array($room,$ban_names))
{
	$loggedin=0;
	$msg=urlencode("<a href=\"http://www.videowhisper.com\">This channel was disabled. Contact for details.</a>");
}


//replace bad words or expressions
$filterRegex=urlencode("(?i)(fuck|cunt)(?-i)");
$filterReplace=urlencode(" ** ");

//layout obtained by sending in public chat box "/videowhisper layout"; fill in new line between layoutEND markers
$layoutCode=<<<layoutEND
layoutEND;

if (!$welcome) $welcome="Welcome on <B>$room</B> live streaming channel!";

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&serverProxy=best&serverRTMFP=<?=$rtmfp_server?>&p2pGroup=VideoWhisper&enableRTMP=1&enableP2P=0&supportRTMP=1&supportP2P=0&alwaysRTMP=0&alwaysP2P=0&bufferLive=0.1&bufferFull=0.5&welcome=<?=urlencode($welcome)?>&username=<?=$username?>&userType=<?=$userType?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&overLogo=logo.png&overLink=http://www.videowhisper.com&msg=<?=$msg?>&visitor=<?=$visitor?>&loggedin=<?=$loggedin?>&showCredit=1&disconnectOnTimeout=1&offlineMessage=Channel+Offline&disableVideo=0&disableChat=0&disableUsers=0&layoutCode=<?=urlencode($layoutCode)?>&fillWindow=0&filterRegex=<?=$filterRegex?>&filterReplace=<?=$filterReplace?>&writeText=1&floodProtection=3&privateTextchat=1&externalInterval=6000&ws_ads=<?=urlencode("ads.php")?>&sendTip=1&adsTimeout=15000&adsInterval=240000&statusInterval=10000&verboseLevel=2&loadstatus=1