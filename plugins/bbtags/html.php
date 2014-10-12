<?php
/*
	HTML
	-------------
	Coverts anything in the html bbtag to html... dangerous! :o

	By Michael Lat
	http://www.latova.com/
*/

class bb_html
{
	function initialize($value, $option="", $argument=array())
	{
		$this->return = true;
		return $this->unsafe_text($value);
	}
}
?>
