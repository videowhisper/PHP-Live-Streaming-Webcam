<?php
	//detect ffmpeg
	echo "<BR>FFMPEG Detection: ";
	$cmd = '/usr/local/bin/ffmpeg -version';
	$output = '';
	exec($cmd, $output, $returnvalue);
	if ($returnvalue == 127)  echo "<b>Warning: not detected: $cmd</b>";
	else
	{
		echo "detected";
		echo '<BR>' . $output[0];
		echo '<BR>' . $output[1];

		$cmd = '/usr/local/bin/ffmpeg -codecs';
		exec($cmd, $output, $returnvalue);

		//detect codecs
		if ($output) if (count($output))
			{
				echo "<br>Codecs:";
				foreach (array('h264', 'vp6', 'faac','speex', 'nellymoser') as $cod)
				{
					$det=0; $outd="";
					echo "<BR>$cod codec: ";
					foreach ($output as $outp) if (strstr($outp,$cod)) { $det=1; $outd=$outp; };
					if ($det) echo "detected ($outd)"; else echo "<b>missing: please configure and install ffmpeg with $cod</b>";
				}
			}

	}
