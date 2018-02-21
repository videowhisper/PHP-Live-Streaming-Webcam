<?php
/*
This script is called by viewer applications from time to time and controls viewer session (can terminate session by returning a "disconnect" variable). 
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
*/

$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];
$message=$_POST[m];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

$disconnect=""; //anything else than "" will disconnect with that message

include_once("settings.php");


include_once("incsan.php");
sanV($room);
sanV($session);

//session time management: configure in settings.php

if ($room && $session)
{
	//code to keep track of viewer's watch time (in all rooms) and also reset as configured

	if ($limitUser)
	{
		//create folders
		$dir="uploads";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		$dir.="/_users";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		//reset counter after some time
		$toReset=0;

		$counterFile =  "$dir/$session";
		$resetFile = $counterFile . '.reset';

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
		if (file_exists($counterFile))
		{
			$oldTime = implode(file($counterFile));
			$timeUsedU = $oldTime + ($currentTime-$lastTime);
		}

		//save new time
		$dfile = fopen($counterFile,"w");
		fputs($dfile, $timeUsedU);
		fclose($dfile);
	} else $timeUsedU=0;

	//code to keep track of total channel use time:
	//all usage time ads up (broadcaster + viewers): 10 min broadcast to 2 viewers = 30 min total usage
	//this is reset from lb_status.php

	if ($limitChannel)
	{
		//get time
		$dir= "uploads";
		if (file_exists("$dir/$room/online/$room"))
		{
			$oldTime = implode(file("$dir/$room/online/$room"));
			$timeUsedC = $oldTime + ($currentTime-$lastTime);
		}

		//save time
		$dir="uploads";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		$dir.="/$room";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		$dir.="/online";
		if (!file_exists($dir)) mkdir($dir);
		@chmod($dir, 0777);

		$dfile = fopen($dir."/$room","w");
		fputs($dfile, $timeUsedC);
		fclose($dfile);
	} else $timeUsedC = 0;

}
else
{
	$disconnect = "No valid room or session!";
}

//select maximum between user and total channel usage time or current session time if both disabled
$timeUsed = max($timeUsedC, $timeUsedU, $currentTime);

?>timeTotal=<?php echo $maximumSessionTime?>&timeUsed=<?php echo $timeUsed?>&lastTime=<?php echo $currentTime?>&disconnect=<?php echo $disconnect?>&loadstatus=1