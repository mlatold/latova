<?php

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
