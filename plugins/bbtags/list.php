<?php
/*
	LIST BBTAG
	-------------
	Makes a list... checks it twice...

	By Michael Lat
	http://www.latova.com/
*/
class bb_list
{
	function initialize($value, $option="", $argument=array())
	{
		$this->return = true;

		if(strpos($value, "[*]") !== false)
		{
			$value = preg_replace("{\[\*\]}is", "</li><li>", $value);
			$value = preg_replace("{<ol style=\"(.+?)\">.+?</li>}is", "<ol style=\"\\1\">", $value);
		}
		
		switch($option)
		{
			case "1":
				$style = "decimal";
				break;
			case "i":
				$style = "lower-roman";
				break;
			case "I":
				$style = "upper-roman";
				break;
			case "a":
				$style = "lower-latin";
				break;
			case "A":
				$style = "upper-latin";
				break;
			case "*":
				$style = "disc";
				break;
			case "o":
				$style = "circle";
				break;
			case "#":
				$style = "square";
				break;
			default:
				$style = "inherit";
				break;
		}
		return "<ol style=\"list-style: {$style}\">{$value}</li></ol>";
	}

}
?>