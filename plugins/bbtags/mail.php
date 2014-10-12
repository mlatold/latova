<?php
/*
	MAIL BBTAG
	-------------
	Returns a parsed email address

	By Michael Lat
	http://www.latova.com/
*/

class bb_mail
{
	function initialize($value, $option="", $argument=array())
	{
		if(!$this->lat->parse->is_email($value))
		{
			$this->error = true;
		}
		
		return $value;
	}
}

?>
