<?php
/*
	COLOR BBTAG
	-------------
	The only accepted color values are hex codes and color names

	By Michael Lat
	http://www.latova.com/
*/

class bb_color
{
	function initialize($value, $option="", $argument=array())
	{
		$option = strtolower(preg_replace("{[^#A-Za-z0-9]}", "", $option));
		
		if(in_array($option, array("blue", "red", "purple", "gray", "green", "orange")))
		{
			$option = "class=\"color_{$option}\"";
		}
		else
		{
			$option = "style=\"color: {$option}\"";
		}
		
		return array($value, $option);
	}
}

?>
