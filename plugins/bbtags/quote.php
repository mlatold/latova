<?php

class bb_quote
{
	function initialize($value, $option="", $argument=array())
	{
		$this->option = true;

		if($argument['user'])
		{
			$argument['user'] = intval($argument['user']);
			$option = "<user:{$argument['user']}>";
			$this->lat->parse->user_replace[] = $argument['user'];
		}

		if($option != "")
		{
			return array($value, $option." <lang:said>");
		}
		else
		{
			return array($value, "<lang:quote>");
		}
	}
}
?>