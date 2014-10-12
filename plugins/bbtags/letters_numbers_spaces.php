<?php

class bb_letters_numbers_spaces
{
	function initialize($value, $option="", $argument=array())
	{
		$option = preg_replace("{[^A-Za-z0-9 ]}", "", $option);
		return array($value, $option);
	}
}
?>