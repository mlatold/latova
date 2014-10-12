<?php
/*
	LETTERS NUMBERS AND SPACES IN OPTION
	-------------
	Cleans the option value to only have letters (case insensitive), numbers, and spaces

	By Michael Lat
	http://www.latova.com/
*/

class bb_letters_numbers_spaces
{
	function initialize($value, $option="", $argument=array())
	{
		$option = preg_replace("{[^A-Za-z0-9 ]}", "", $option);
		return array($value, $option);
	}
}
?>