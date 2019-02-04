<head>
<?php
$n=$_GET["n"];

include("incsan.php");
sanV($n);
if (!$n) exit;

$swfurl="live_watch.swf?ssl=1&n=".urlencode($n);
$bgcolor="#333333";
?><style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #000;
	font-size: 15px;
	color: #EEE;
}

a
{
	color: #FF6699;
	font-weight: normal;
	text-decoration: none;
}
-->
</style><title><?=$n?> Live Video Streaming</title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#000000"><p><strong>You are watching:
        <?=strip_tags($n)?>
        </strong><br>
        Channel Demo Page - Edit channel.php to change this</p>
      <p><strong><br />
      </strong></p>
      <?php
$agent = $_SERVER['HTTP_USER_AGENT'];
if( strstr($agent,'iPhone') || strstr($agent,'iPod') || strstr($agent,'iPad'))
echo "<p><b>iOS detected: If your browser does not support Flash plugin, WebRTC or a transcoded HLS stream could be available (if supported/configured by host): <BR><a href='webrtc-play.php?n=$n'>WebRTC Playback</a> <BR> <a href='ls_transcoder.php?n=$n'>Stream Transcoder</a>  </b></p><BR>";
	  ?>
      </td>
  </tr>
  <tr>
    <td height=400 bgcolor="#333333">

	<object width="100%" height="100%" type="application/x-shockwave-flash" data="<?=$swfurl?>">

      <param name="movie" value="<?=$swfurl?>" />
      <param bgcolor="<?=$bgcolor?>" />
      <param name="scale" value="noscale" />
      <param name="salign" value="lt" />
      <param name="allowFullScreen" value="true" />
      <param name="allowscriptaccess" value="always" />

    </object>

	</td>
  </tr>
</table>

	<?php
	include("flash_detect.php");
	?>
		<p><font color="#FFFFFF" face="Arial, Helvetica, sans-serif">The flash workspace above can have any size. Any of the panels can be disabled from vs_login.php .
	  <BR>You can also embed just <a href="video_small.php?n=<?=$n?>">plain video</a> (<a href="video.php?n=<?=$n?>">fullpage video</a>) or send mobile users without flash to a <a href="htmlchat.php?n=<?=$n?>">plain html external text chat interface</a>. </font></p>
	  <BR>If configured, also check <a target="_blank" href="webrtc-play.php?n=<?=urlencode($n)?>">WebRTC Playback</a> - HTML5 playback with WebRTC technology.
		<p>If server supports required tools HTTP Live Streaming may be available: <a target="_blank" href="ls_transcoder.php?n=<?=urlencode($n)?>">Stream Transcoder</a> (for iPhone and iPad, if available).</p>
      <p><a href="http://www.videowhisper.com/?p=Live+Streaming">Video Whisper Live Streaming</a></p>
