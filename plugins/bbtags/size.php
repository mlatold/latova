<?php

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