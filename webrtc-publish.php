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
	
	//Chrome: supported
	//Safari: only broadcast
	//Firefox: only playback when using UDP, no broadcast H264?
?>			    
		<div class="div-section">VideoWhisper WebRTC - Publish Channel Stream: <?php echo $streamName ?></div>

		<div class="videowhisper-webrtc-camera">
		<video id="localVideo" class="videowhisper_htmlvideo" autoplay playsinline muted style="widht:640px;height:480px;"></video>
		</div>

		<div class="ui segment form">
			<span id="sdpDataTag">Connecting...</span>

<hr class="divider" />

    <div class="field inline">
        <label for="videoSource">Video Source </label><select class="ui dropdown" id="videoSource"></select>
    </div>

    <div class="field inline">
        <label for="videoResolution">Video Resolution </label><select class="ui dropdown" id="videoResolution"></select>
    </div>

	 <div class="field inline">
        <label for="audioSource">Audio Source </label><select class="ui dropdown" id="audioSource"></select>
    </div>

    		</div>
    	
		<div class="div-section">
		 + <a target="_blank" href="webrtc-play.php?n=<?=urlencode($streamName)?>">WebRTC Playback</a> HTML5 playback with WebRTC technology
		 <BR>+ <a target="_blank" href="channel.php?n=<?=urlencode($streamName)?>">RTMP Playback</a> Full Flash channel interface with playback over RTMP (no sound in Flash without transcoding)	 
	    <BR> + <a href="live_broadcast.php?username=<?=urlencode($username)?>&onlyoptions=1">Channel Options</a> (back)	    
		 
		</div>
		
		<script type="text/javascript">	
			
			var userAgent = navigator.userAgent;
			var wsURL = "<?php echo $wsURLWebRTC ?>";
			var streamInfo = {applicationName:"<?php echo $applicationWebRTC ?>", streamName:"<?php echo $streamName ?>", sessionId:"[empty]"};
			var userData = {param1:"value1","videowhisper":"webrtc-broadcast"};
			var videoBitrate = 600;
			var audioBitrate = 64;
			var videoFrameRate = "29.97";
			var videoChoice = "42e01f";
			var audioChoice = "opus";
		
jQuery( document ).ready(function() {					
			setTimeout(browserReady(),2000);
});			
		</script>

		<div class="ui segment">
			Technology Notes
			<br>- This solution implements WebRTC for streaming to/from media relay server (Wowza SE). This brings compatibility for most HTML5 mobile browsers, to allow streaming directly from site without configuring stand alone encoder apps. This implementation has advanced streaming sever configuration requirements available with <a href="https://videowhisper.com/?p=Wowza+Media+Server+Hosting#plans">VideoWhisper Turnkey Stream Hosting plans</a>.			
			<br>- On PC test by broadcasting from Chrome or Firefox as PC Safari did not encode stream correctly in some previous tests (requires transcoding). Mobile Safari iOS, Chrome Android broadcast should also work.		
			<br>- A more advanced solution is <a href="https://broadcastlivevideo.com">BroadcastLiveVideo.com</a> that implements automated transcoding between different formats, session management, channel and user management. WebRTC sessions can be managed with the <a href="https://www.videowhisper.com/?p=RTMP-Session-Control">VideoWhisper Session Control</a> server side functionality implemented in Broadcast Live Video solution.		
		</div>

		
    </body>
</html>