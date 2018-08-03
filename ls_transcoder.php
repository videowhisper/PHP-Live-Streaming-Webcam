<?php
include("header.php");
?>
<div class="info">
<?php
if (!$stream=$_GET['n']) exit;


include_once("incsan.php");
sanV($stream);
include_once("settings.php");

echo "<H3>".$stream."</h3>";


$upath = getcwd() . "/uploads/$stream/";


$cmd = "ps aux | grep '/i_$stream -i rtmp'";
exec($cmd, $output, $returnvalue);
//var_dump($output);

$transcoding = 0;

foreach ($output as $line) if (strstr($line, "ffmpeg"))
	{
		$columns = preg_split('/\s+/',$line);
		echo "Transcoder Active (".$columns[1]." CPU: ".$columns[2]." Mem: ".$columns[3].") <a target=_blank href='ls_transcoderoff.php?n=" . $stream . "'>Close</a>";
		$transcoding = 1;
		//var_dump($columns);
	}

if (!$transcoding)
{
	echo "Initiating Transcoder... Open/reload page in Safari in few moments to see preview. Transcoding process automatically ends if/when source stream is offline.";
	$log_file =  $upath . "videowhisper_transcode.log";

// audion + video
//	$cmd ="/usr/local/bin/ffmpeg -vcodec libx264 -s 480x360 -r 15 -vb 512k -x264opts vbv-maxrate=364:qpmin=4:ref=4 -coder 0 -bf 0 -analyzeduration 0 -level 3.1 -g 30 -maxrate 768k -acodec libfaac -ac 2 -ar 22050 -ab 96k -level 3.1 -g 30 -maxrate 768k -acodec libfaac -ac 2 -ar 22050 -ab 96k -x264opts vbv-maxrate=364:qpmin=4:ref=4 -threads 1 -rtmp_pageurl http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . " -rtmp_swfurl http://".$_SERVER['HTTP_HOST']." -f flv " . $rtmp_server . "/i_". $stream . " -i " . $rtmp_server ."/". $stream . " >&$log_file & ";

//audio transcode only, when using h264 web encoding
	$cmd = $ffmpeg_call . " -f flv " . $rtmp_server_ffmpeg . "/i_". $stream . " -i " . $rtmp_server_ffmpeg ."/". $stream . " >&$log_file & ";

	exec($cmd, $output, $returnvalue);

	if ($returnvalue == 127)  echo "<b>Failed starting FFMPEG: $cmd</b>";
	echo '<BR>' . $output[0];
	echo '<BR>' . $output[1];


}

//killall -9 ffmpeg

$hlsurl = $httpstreamer . 'i_' . $stream . '/playlist.m3u8';
$mpegdashurl = $httpdash . 'i_' . $stream . '/manifest.mpd';


?>

<p>iOS live stream link (open with Safari or test with VLC): <a href="<?php echo $httpstreamer?>i_<?php echo $stream?>/playlist.m3u8"><br />
  <?php echo $stream?> Video</a></p>

<H4>HLS : Safari, iOS, Android</H4>
<p>HTML5 live video embed below should acessible only in Safari (PC or iOS/Android):</p>
<video width="640" height="480" autobuffer autoplay controls="controls">o
  <p>&nbsp;</p>
 <source src="<?php echo $hlsurl ?>" type='video/mp4'>
    <div class="fallback">
	    <p>You must have an HTML5 capable browser.</p>
	</div>
</video>
<br>URL: <?php echo $hlsurl ?>

<H4>MPEG DASH : Chrome, Android</H4>
<p>HTML5 live video embed below should acessible only in PC Chrome and Android:</p>

<script src="https://cdn.dashjs.org/latest/dash.all.min.js"></script>
<video width="640" height="480" data-dashjs-player src="<?php echo $mpegdashurl ?>" autoplay controls="controls">
    <div class="fallback">
	    <p>You must have an HTML5 capable browser.</p>
	</div>
</video>
<br>URL: <?php echo $mpegdashurl ?> 
<br>* Requires both player and stream url to use HTTP or HTTPS. SSL can be configured on dedicated Wowza servers.

<p>Playback of live transcoded streams over HTTP requires packetizers available with <a href="http://www.videowhisper.com/?p=Wowza+Media+Server+Hosting">Wowza Hosting</a> and optimisations available with VideoWhisper plans.<br />
 Due to HTTP based live streaming technology limitations, video can have several seconds latency and interruptions. Use a browser with flash support or mobile app for full experience including fast and reliable interactions based on RTMP.</p>

</div>
