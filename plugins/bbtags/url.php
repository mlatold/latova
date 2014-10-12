<?php
/*
	URL BBTAG
	-------------
	Returns a parsed url (with url name)

	By Michael Lat
	http://www.latova.com/
*/

class bb_url
{
	function initialize($value, $option="", $argument=array())
	{
		$this->return = true;
		if($option != "")
		{
			return $this->lat->parse->url_parse(array("url" => $option, "name" => $value));
		}
		else
		{
			return $this->lat->parse->url_parse(array("url" => $value));
		}
	}
}
?>