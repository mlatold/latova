<?php if(!defined('LAT')) die("Access Denied.");

class kernel_get_input
{
	//   Input Initalize
	// +-------------------------+
	// Load up the inputs and get them ready to be used

	function initialize()
	{
		// Remove magic quotes if needed
		if (get_magic_quotes_gpc())
		{
			$_COOKIE = $this->lat->parse->array_strip($_COOKIE);
			$_POST = $this->lat->parse->array_strip($_POST);
			$_FILES = $this->lat->parse->array_strip($_FILES);
		}

		// Custom GET retrival
		parse_str(str_replace(";", "&", $_SERVER['QUERY_STRING']), $get);

		// Short page formats
		$short_pg = explode("\n", $this->lat->cache['config']['short_page']);

		// Determine if we're using a short page format
		if(!$get['pg'])
		{
			foreach($short_pg as $sp)
			{
				list($spg, $pg, $do) =  explode("|", $sp);
				if($get[$spg] != "")
				{
					$get['pg'] = $pg;
					$get['do'] = $do;
					$get['id'] = $get[$spg];
					break;
				}
			}
		}

		// Combine GETs and POSTs into one large raw data array
		while(list($name, $value) = each($get))
		{
			$this->lat->raw_input[strtolower($name)] = $value;
		}

		while(list($name, $value) = each($_POST))
		{
			$this->lat->raw_input[strtolower($name)] = $value;
		}

		// Clean some page inputs
		$this->preg_whitelist("pg", "a-z_");
		$this->preg_whitelist("do", "a-z_");
		$this->unsigned_int("id");

		// Clean the incoming page
		if($this->lat->input['pg'] == "")
		{
			$this->lat->input['pg'] = $this->lat->cache['config']['default_page'];
		}
	}


	//   Get Variable
	// +-------------------------+
	// Quickly determines what we need to return for the input

	function set_var($name, $val)
	{
		$this->lat->input[$name] = $val;
		return $val;
	}


	//   Whitelist
	// +-------------------------+
	// Checks strings not whitelisted in an array

	function as_array($func, $val, $exp="")
	{
		return $this->set_var($val, $this->lat->parse->as_array($func, $this->lat->raw_input[$val], $exp));
	}


	//   Whitelist
	// +-------------------------+
	// Checks strings not whitelisted in an array

	function whitelist($val, $exp)
	{
		return $this->set_var($val, $this->lat->parse->whitelist($this->lat->raw_input[$val], $exp));
	}


	//   Preg Whitelist
	// +-------------------------+
	// Removes characters not whitelisted and returns result

	function preg_whitelist($val, $exp)
	{
		return $this->set_var($val, $this->lat->parse->preg_whitelist($this->lat->raw_input[$val], $exp));
	}


	//   Unsigned Integer
	// +-------------------------+
	// Sets integer to a number equal to or greater than zero

	function unsigned_int($val)
	{
		return $this->set_var($val, $this->lat->parse->unsigned_int($this->lat->raw_input[$val]));
	}


	//   Signed Integer
	// +-------------------------+
	// Gives back an integer

	function signed_int($val)
	{
		return $this->set_var($val, $this->lat->parse->signed_int($this->lat->raw_input[$val]));
	}


	//   Ranged Integer (Inclusive)
	// +-------------------------+
	// Gives back a ranged integer within range

	function ranged_int($val, $range)
	{
		return $this->set_var($val, $this->lat->parse->ranged_int($this->lat->raw_input[$val], $range));
	}


	//   Line Text
	// +-------------------------+
	// Cleans text for database and display, but uses \n for any new lines used

	function ln_text($val)
	{
		return $this->set_var($val, $this->lat->parse->ln_text($this->lat->raw_input[$val]));
	}


	//  BR Line Text
	// +-------------------------+
	// Cleans text for database and display, but uses <br> for linebreaks

	function br_text($val)
	{
		return $this->set_var($val, $this->lat->parse->br_text($this->lat->raw_input[$val]));
	}


	//  No Line Text
	// +-------------------------+
	// Cleans text for database and display, will return on one line

	function no_text($val)
	{
		return $this->set_var($val, $this->lat->parse->no_text($this->lat->raw_input[$val]));
	}

	//  No HTML Text
	// +-------------------------+
	// Cleans text for display

	function no_html($val)
	{
		return $this->set_var($val, $this->lat->parse->no_html($this->lat->raw_input[$val]));
	}


	//  SQL Text
	// +-------------------------+
	// Safe for SQL queries but unsafe for HTML display

	function sql_text($val)
	{
		return $this->set_var($val, $this->lat->parse->sql_text($this->lat->raw_input[$val]));
	}


	//  SQL Text
	// +-------------------------+
	// Safe for SQL queries but unsafe for HTML display, also makes ln into br

	function br_sql_text($val)
	{
		return $this->set_var($val, $this->lat->parse->br_sql_text($this->lat->raw_input[$val]));
	}


	//  Is Email
	// +-------------------------+
	// Returns an email, but only if its valid

	function is_email($val)
	{
		return $this->set_var($val, $this->lat->parse->is_email($this->lat->raw_input[$val]));
	}


	//	 Form Checkbox
	// +-------------------------+
	// Return checkbox values for a form

	function form_checkbox($val)
	{
		return $this->set_var($val, $this->lat->parse->form_checkbox($this->lat->raw_input[$val]));
	}


	//	 Form Select
	// +-------------------------+
	// Returns values for radio buttons, dropdown boxes and so on

	function form_select($val)
	{
		return $this->set_var($val, $this->lat->parse->form_select($this->lat->raw_input[$val]));
	}


	//	 Form Select
	// +-------------------------+
	// Returns values for radio buttons, dropdown boxes and so on

	function form_radio($val)
	{
		return $this->set_var($val, $this->lat->parse->form_radio($this->lat->raw_input[$val]));
	}
}
?>