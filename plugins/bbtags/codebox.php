<?php

class bb_codebox
{
	function initialize($value, $option="", $argument=array())
	{
		$value = preg_replace("{<br />}", "\n", $value);

		// Kill smilies
		$value = preg_replace("{<!-- smi(.+?) -->.+?<!-- endsmi -->}", "\\1", $value);

		// Kill links
		$value = preg_replace("{<a href.+?>(.+?)</a>}", "\\1", $value);

		return $value;
	}
}
?>
