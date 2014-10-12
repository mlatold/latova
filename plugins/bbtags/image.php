<?php
/*
	IMG BBTAG
	-------------
	Gets back a valid picture URL

	By Michael Lat
	http://www.latova.com/
*/

class bb_image
{
	function initialize($value, $option="", $argument=array())
	{
		if($this->override)
		{
			$this->return = true;
			return "<img src=\"{$this->lat->image_url}online.png\" alt=\"img\" />";
		}

		if(!$this->lat->parse->is_url($value))
		{
			$this->error = true;
		}

		$this->replace = str_replace("<!-- IMAGE -->", $this->lat->image_url, $this->replace);
		$this->replace = str_replace("<!-- type -->", intval($this->lat->parse->type), $this->replace);

		return array($value, $option);
	}
}
?>