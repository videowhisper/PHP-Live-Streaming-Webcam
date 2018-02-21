<?php

$old = 30*24*3600;

function deltree($path) {
global $old;
$delete_this = 1;
	  if (is_dir($path)) {
		  if (version_compare(PHP_VERSION, '5.0.0') < 0) {
			$entries = array();
		  if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) $entries[] = $file;

			closedir($handle);
		  }
		  } else {
			$entries = @scandir($path);
			if ($entries === false) $entries = array(); // just in case scandir fail...
		  }

		foreach ($entries as $entry) {
		  if ($entry != '.' && $entry != '..') {
			if (deltree($path.'/'.$entry)==-1) $delete_this=0;
		  }
		}
		
		if ($delete_this) if (time()-filemtime($path)> $old) return @rmdir($path);
		else return -1;
	  } else {
		if ($delete_this) if (time()-filemtime($path)> $old) return @unlink($path);
		else return -1;
	  }
	  return -1;
	}
	
	function cleanUp($dir)
	{
	global $old;
	
	echo "<BR><small>Cleaning up old files in $dir ...";
	$k=0;
	$handle=opendir($dir);
		while (($file = readdir($handle))!==false) 
		{
			if (($file != ".") && ($file != ".."))
			{
				if (is_dir("$dir/" . $file)) deltree($dir."/".$file);
				elseif (time()-filemtime("$dir/" . $file)> $old) @unlink("$dir/" . $file);
				$k++;
				
				if ($k%50==0)
				{
				echo " ."; 
				flush();
				}
			}
		}
	closedir($handle); 
	echo "<BR>Finished cleaning up $k items.</small>";
	}
	
	cleanUp('uploads');
	cleanUp('snapshots');
?>