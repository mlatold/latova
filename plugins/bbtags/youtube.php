<?php
/*
	YOUTUBE BBTAG
	-------------
	Parses a URL or the video ID for the youtube video

	By Michael Lat
	http://www.latova.com/
*/

class bb_youtube
{
	function initialize($value, $option="", $argument=array())
	{
		if(preg_match("{[^A-Za-z0-9_\-]}", $value))
		{
			$value = @parse_url($value);
			parse_str($value['query'], $value);
			$value = $value['v'];
		}
		
		if(preg_match("{[^A-Za-z0-9_\-]}", $value))
		{
			$this->error = true;
		}
		
		return $value;
	}
}
?>