<?php
if (!$_POST["username"]||$_POST["username"]=="Studio") $username="Studio".rand(100,999);
else $username=$_POST["username"];
include("incsan.php");
sanV($username);
if (!$username) exit;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 15px;
	color: #EEE;
}

a
{
	color: #CC5577;
	font-weight: normal;
	text-decoration: none;
}
-->
</style>
<title>VideoWhisper Live Broadcast</title>
</head>
<body bgcolor="#333333">
<?php
$swfurl="live_broadcast.swf?room=" . $username;
$bgcolor="#333333";
$baseurl="";
$wmode="transparent";
?>
<object width="100%" height="500" id="videowhisper_livebroadcast" type="application/x-shockwave-flash" data="<?=$swfurl?>">
<param name="movie" value="<?=$swfurl?>"></param><param bgcolor="<?=$bgcolor?>" /><param name="scale" value="noscale" /> </param><param name="salign" value="lt"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<param name="base" value="<?=$baseurl?>"/><param name="wmode" value="<?=$wmode?>" /></object>


      <p>This html content is editable. The flash workspace above can have any size. Various settings can be configured from vc_login.php .
	  <BR>
	  Test <a target="_blank" href="channel.php?n=<?=urlencode($username)?>">channel page</a> (where people can also chat live), <a target="_blank" href="video_small.php?n=<?=$username?>">plain video</a> (<a target="_blank" href="video.php?n=<?=$username?>">fullpage video</a>), just <a target="_blank" href="htmlchat.php?n=<?=urlencode($username)?>">plain html external text only chat</a> (for old mobile access), <a target="_blank" href="videotext.php?n=<?=urlencode($username)?>">plain video with floating html text</a> (read only), <a target="_blank" href="ls_transcoder.php?n=<?=urlencode($username)?>">Transcoder for HTML5 live video playback</a> (HLS for Safari & iOS/Android, MPEG-DASH for Chrone & Android, if available).</p>
	  <P>Ordering a <a target="_blan k" href="http://www.videowhisper.com/?p=Invest#level1">license</a> removes banner ads and usage limitations (for licensed domain).</P>
	  <p>For more details about this edition see VideoWhisper <a target="_blank" href="http://www.videowhisper.com/?p=PHP+Live+Streaming">PHP Live Streaming</a> page. This plain php edition is for easy integration with other sites and frameworks. For building a turnkey site with member/channel management, backend, see <a href="http://www.videowhisper.com/?p=WordPress+Live+Streaming">WordPress Live Streaming</a> edition.</p></td>
</body>
</html>
