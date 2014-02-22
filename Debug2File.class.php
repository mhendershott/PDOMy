<?php

/*
 * Debug/Logging/Error Handling
*/
class Debug2File
{
	private $loggingLevel = 7;
	
	function write ($code=7, $detail="Unknown") {

		/* Code levels
		0 - Emergency (emerg)
		1 - Alerts (alert)
		2 - Critical (crit)
		3 - Errors (err)
		4 - Warnings (warn)
		5 - Notification (notice)
		6 - Information (info)
		7 - Debug (debug)
		*/
		
		$loggingLevel = 7; // Set to highest level of debugging you wish to use.

		if ($code <= $this->loggingLevel) {
			$log = strtotime("now").";$code;$detail;$user;\n";
			$fp = fopen('debuglogs.txt', 'a');
			fwrite($fp, $log);
			fclose($fp);
		}

	

	}

}
?>
