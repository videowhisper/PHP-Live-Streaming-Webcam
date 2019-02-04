<?php
include("header.php");
?>

<div style="padding:20px"><form id="adminForm" name="adminForm" method="get" action="live_broadcast.php" onSubmit="return censorName()">
  <b>Provide a Label for your Channel</b><br>
  <br>
  Channel Name / Username
  			<script language="JavaScript">
			function censorName()
			{
				document.adminForm.username.value = document.adminForm.username.value.replace(/^[\s]+|[\s]+$/g, '');
				document.adminForm.username.value = document.adminForm.username.value.replace(/[^0-9a-zA-Z_\-]+/g, '-');
				document.adminForm.username.value = document.adminForm.username.value.replace(/\-+/g, '-');
				document.adminForm.username.value = document.adminForm.username.value.replace(/^\-+|\-+$/g, '');
				if (document.adminForm.username.value.length>2) return true;
				else return false;
			}
			</script>

	<input name="username" type="text" id="username" value="Studio" size="12" maxlength="12" onChange="censorName()"/>

<select name="onlyoptions" id="onlyoptions">
  <option value="0">Start Broadcast</option>	
  <option value="1">Only Options</option>
</select>
	
    <input type="submit" name="button" id="button" value="Access Channel" onClick="this.disabled=true; censorName(); this.value='Loading...'; adminForm.submit();" />
<?php
include("settings.php");
if (strstr($rtmp_server, "://localhost/")) echo "<P class='warning'>Warning: You are using a localhost based rtmp address ( $rtmp_server ). Unless you are just testing this with a rtmp server on your own computer, make sure you fill a <a href='http://www.videowhisper.com/?p=RTMP+Applications'>compatible rtmp address</a> in settings.php.</P>";
?>
<br><small>Select Only Options (do not Start Broadcast) to access channel options including  HTML5 WebRTC broadcasting.</small>
	</form>
	
	</div>

<div class="info">
  <p><b>Suggestions</b></p>
  <?php
include("flash_detect.php")
?>
  <p>
  When the application starts, flash will ask you if you want to start streaming your camera and microphone. Allow flash to send your stream and select the right video and audio devices you want to use. </p>
  <p>There are 2 ways to select hardware devices/drivers you'll use for broadcasting:<br>
    A. Click inside webcam preview panel and a settings panel will extend it. Click camera or microphone to select.<br>
    B. Right click Flash &gt; Settings... and browse to the webcam/microphone minitabs. </p>
  <p>    To see how others see what you're broadcasting:<br>
    1. Click the Channel Link box (the link will be automatically copied) <br>
    2. Paste the link in the address bar of a new browser window or tab </p>
<p>When broadcasting consider your video subscribers' average connection speed.<br>
  If you observe big latency and interruptions, decrease streaming bandwidth (click webcam preview panel, drag slider and apply). If streams is fluent you can try increasing bandwidth (and quality).
</p>
</div>

<div class="info">
For easy testing here's channel page of last user online:<br />
  <?php
if (file_exists("uploads/last.html")) include("uploads/last.html");
else echo "No snapshot found.";
?>
</div>

<div class="info">
Total usage limit:
<?php
include_once("settings.php");
echo ($maximumSessionTime?$maximumSessionTime/60000 . ' minutes each '. ($resetTime/24/3600) . ' days':'unlimited');
echo "<br>Channel time limit (total broadcaster and viewers time) enabled: $limitChannel <br>User view limit (on all channels) enabled: $limitUser";
?>
</div>

<div class="info">
Channel cleanup:<br />
<?php
include_once("clean_older.php");
?>
</div>
</BODY>
