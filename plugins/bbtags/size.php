<?php
/*
	FONT SIZE BBTAG
	-------------
	Makes sure that the font bbtag isn't to large or small, and is clean

	By Michael Lat
	http://www.latova.com/
*/

class bb_size
{
	function initialize($value, $option="", $argument=array())
	{
		$option = intval($option);
		if($option < 8)
		{
			$option = 8;
		}
		elseif($option > 30)
		{
			$option = 30;
		}
		
		return array($value, $option);
	}
}
?>