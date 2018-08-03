<meta charset="UTF-8">
<html>
    <head>
		<script src="js/jquery-3.3.1.min.js"></script>	    
		<script src="js/adapter.js"></script>
        <script src="js/vwrtc-publish.js"></script>
		<style>
		.div-section {margin-bottom: 8px;}
		</style>
    </head>
    <body>
<?php
	$streamName = filter_var($_GET["n"],FILTER_SANITIZE_STRING);
	if (!$streamName) echo 'Error: Channel name is required!';
	
	include('settings.php');
?>			    
		<div class="div-section">VideoWhisper WebRTC - Publish Channel Stream: <?php echo $streamName ?></div>
		<div class="div-section">
		<video id="localVideo" autoplay muted style="height:480px;"></video>
		</div>
		
		<script type="text/javascript">	
			var userAgent = navigator.userAgent;
		    var wsURL = "<?php echo $wsURLWebRTC ?>";
			var streamInfo = {applicationName:"<?php echo $applicationWebRTC ?>", streamName:"<?php echo $streamName ?>", sessionId:"[empty]"};
			var userData = {param1:"value1"};
			var videoBitrate = 360;
			var audioBitrate = 64;
			var videoFrameRate = "29.97";
			var videoChoice = "42e01f";
			var audioChoice = "opus";
			
			browserReady();	
		</script>

		<div>
			<span id="sdpDataTag"></span>
		</div>
		
		<div class="div-section">
		 + <a target="_blank" href="webrtc-play.php?n=<?=urlencode($streamName)?>">WebRTC Playback</a> HTML5 playback with WebRTC technology
		 <BR>+ <a target="_blank" href="channel.php?n=<?=urlencode($streamName)?>">RTMP Playback</a> Full Flash channel interface with playback over RTMP (no sound in Flash without transcoding)	 
		</div>
    </body>
</html>