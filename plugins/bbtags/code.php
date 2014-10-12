<?php
/*
	CODE BBTAG
	-------------
	Readies content for code

	By Michael Lat
	http://www.latova.com/
*/

class bb_code
{
	function initialize($value, $option="", $argument=array())
	{
		// Spacing stuff

		// PHP stuff
		$value = preg_replace("{&lt;\?php(.*)\?&gt;}msei", "\$this->highlight_php('\\1')", $value);
		$value = preg_replace("{	}", "&nbsp; &nbsp; ", $value);
		$value = preg_replace("{\s\s}", "&nbsp; ", $value);

		// Kill smilies
		$value = preg_replace("{<!-- smi(.+?) -->.+?<!-- endsmi -->}", "\\1", $value);

		return $value;
	}

	function highlight_php($value)
	{
		$value = stripslashes($value);
		$value = $this->lat->parse->br_to_ln($value);
		$value = $this->lat->parse->unsafe_text($value);
		$value = str_replace("&#46;", ".", $value);

		return highlight_string("<?php".$value."?>", true);
	}
}
?>
