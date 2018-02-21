<?php
	include_once('incsan.php');

				$room_name = sanV($_POST['r']);
				$caller = sanV($_POST['s']);
				$target = sanV($_POST['t']);

				$username = sanV($_POST['u']);
				$private = sanV($_POST['p']);
				$amount = floatval($_POST['a']);
				$label = sanV($_POST['l']);
				$message = sanV($_POST['m']);

				$sound = sanV($_POST['snd']);

				$ztime = time();

				$balance = 0;

				if ($sound) $soundCode = "sound://$sound;;";
				$publicMessage = $soundCode. '<B>Tip from ' . $username . '</B>: ' . $label . " (#$amount)";

				$privateMessage = '<B>' . $username . ' (Tip #'.$amount.')</B>: ' . $message;

				echo 'success=1&amount=' . $paid . '&balance=' . $balance. '&sound=' .urlencode($sound) . '&privateMessage=' .urlencode($privateMessage). '&publicMessage=' .urlencode($publicMessage) . '&ownMessage=' .urlencode($ownMessage);
?>