<?php
//called by plain video interface (live_video.swf)

include("inc.php");
$username="VV".base_convert((time()-1224350000).rand(0,10),10,36);

$room=$_GET['room_name'];

$msg="";
$loggedin=1;
if (in_array($username,$ban_names))
{
	$loggedin=0;
	$msg=urlencode("<a href=\"http://www.videowhisper.com\">You are not allowed to watch. Contact for details.</a>");
}

?>server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&serverProxy=best&serverRTMFP=<?=$rtmfp_server?>&p2pGroup=VideoWhisper&enableRTMP=1&enableP2P=0&supportRTMP=1&supportP2P=0&alwaysRTMP=0&alwaysP2P=0&bufferLive=0.2&bufferFull=0.2&welcome=Welcome!&username=<?=$username?>&userType=0&overLogo=logo.png&overLink=http://www.videowhisper.com&msg=<?=$msg?>&visitor=1&loggedin=<?=$loggedin?>&showCredit=1&showViewers=1&viewersMessage=<?=urlencode(' viewers live')?>&disconnectOnTimeout=1&statusInterval=10000&offlineMessage=Channel+Offline&liveMessage=Live&noSound=0&controlSpacing=6&controlSize=24&s3d=1&s3dStream=&loadstatus=1