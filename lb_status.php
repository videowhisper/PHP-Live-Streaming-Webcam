<?php
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
cam, mic = 0 none, 1 disabled, 2 enabled
*/

$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];
$message=$_POST[m];
$cam=$_POST[cam];
$mic=$_POST[mic];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

$disconnect=""; //anything else than "" will disconnect with that message

include_once("settings.php");

//code to keep track of online broadcasters and total channel time:
//all usage time ads up (broadcaster + viewers): 10 min broadcast to 2 viewers = 30 min total usage
//do not allow uploads to other folders
include_once("incsan.php");
sanV($room);
sanV($session);


if ($room&&$session)
{

	if ($limitChannel)
	{
		$dir= "uploads";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);
		$dir.="/$room";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);
		$dir.="/online";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		//reset counter after some time
		$toReset=0;
		$resetFile = "$dir/$session.reset";
		$counterFile =  "$dir/$session";

		if (file_exists($resetFile))
		{
			$lastReset = implode(file($resetFile));
			if ($lastReset+$resetTime<time()) $toReset=1;
		}else $toReset=1;


		//reset counter
		if ($toReset)
		{
			$dfile = fopen($counterFile,"w");
			fputs($dfile, "0");
			fclose($dfile);

			$dfile = fopen($resetFile ,"w");
			fputs($dfile, time());
			fclose($dfile);
		}

		//get time
		if (file_exists($counterFile ))
		{
			$oldTime = implode(file($counterFile));
			$timeUsed = $oldTime + ($currentTime-$lastTime);
		}


		//save time
		$dfile = fopen($counterFile ,"w");
		fputs($dfile, $timeUsed);
		fclose($dfile);
	} else $timeUsed = $currentTime;
}
else
{
	$disconnect = "No valid room or session!";
}

?>timeTotal=<?php echo $maximumSessionTime?>&timeUsed=<?php echo $timeUsed?>&lastTime=<?php echo $currentTime?>&disconnect=<?php echo $disconnect?>&loadstatus=1