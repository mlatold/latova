<?php

class bb_html
{
	function initialize($value, $option="", $argument=array())
	{
		$this->return = true;
		return $this->unsafe_text($value);
	}
}
?>
