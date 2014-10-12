<?php

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