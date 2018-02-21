<head>
<?php
$n=$_GET["n"];

include_once("incsan.php");
include_once("settings.php");

sanV($n);
if (!$n) exit;

$url = $httpdash . urlencode($n).'/manifest.mpd';

$agent = $_SERVER['HTTP_USER_AGENT'];
if( strstr($agent,'iPhone') || strstr($agent,'iPod') || strstr($agent,'iPad'))
	echo "<p><b>iOS detected: A HLS transcoded stream could be available (if supported by host): <a href='ls_transcoder.php?n=$n'>iOS Transcoder</a></b></p>";


?><style type="text/css">
<!--
body {
	background-color: #000;
}
-->
</style>
<title><?php echo $n?> Live Video Streaming</title>

<script src="http://cdn.dashjs.org/latest/dash.all.min.js"></script>
<style>
    video {
       width: 640px;
       height: 360px;
    }
</style>

</head>
<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<div id="videowhisper_video">
       <video data-dashjs-player autoplay src="<?php echo $url ?>" controls></video>
</div>

</body>