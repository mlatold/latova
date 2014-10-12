<?php if(!defined('LAT')) die("Access Denied.");

class kernel_parse
{
	function initialize() {
		$this->bbtag = new latova();
	}


	//   Array Strip Slashes
	// +-------------------------+
	// Strip slashes from everything within the array

	function array_strip($val)
	{
		if(is_array($val))
		{
			return array_map(array(&$this, "array_strip"), $val);
		}
		else
		{
			return stripslashes($val);
		}
	}


	//   As Array
	// +-------------------------+
	// Outputs things as an array

	function as_array($func, $val, $exp="")
	{
		if(!is_array($val))
		{
			if($exp != "")
			{
				return array(call_user_func(array(&$this, $func), $val, $exp));
			}
			else
			{
				return array(call_user_func(array(&$this, $func), $val));
			}
		}
		else
		{
			if($exp != "")
			{
				foreach($val as $n => $v)
				{
					$new_val[$n] = call_user_func(array(&$this, $func), $v, $exp);
				}
			}
			else
			{
				foreach($val as $n => $v)
				{
					$new_val[$n] = call_user_func(array(&$this, $func), $v);
				}
			}
			return $new_val;
		}
	}


	//   Whitelist
	// +-------------------------+
	// Checks strings not whitelisted in an array

	function whitelist($val, $exp)
	{
		$exp_upper = array_map("strtoupper", $exp);

		if(!in_array(strtoupper($val), $exp_upper))
		{
			return $exp[0];
		}
		return $val;
	}


	//   Preg Whitelist
	// +-------------------------+
	// Removes characters not whitelisted and returns result

	function preg_whitelist($val, $exp)
	{
		return preg_replace("{[^".$exp."]}", "", $val);
	}


	//   Unsigned Integer
	// +-------------------------+
	// Sets integer to a number equal to or greater than zero

	function unsigned_int($val)
	{
		$val = intval($val);

		if($val < 0)
		{
			$val = 0;
		}

		return $val;
	}


	//   Signed Integer
	// +-------------------------+
	// Gives back an integer

	function signed_int($val)
	{
		return intval($val);
	}


	//   Ranged Integer (Inclusive)
	// +-------------------------+
	// Gives back a ranged integer within range

	function ranged_int($val, $range)
	{
		$val = intval($val);
		if($val > $range[1] || $val < $range[0])
		{
			return $range[0];
		}
		return $val;
	}


	//   Line Text
	// +-------------------------+
	// Cleans text for database and display, but uses \n for any new lines used

	function ln_text($val, $save_spacing=false)
	{
		return trim(preg_replace(array("{<br />}", "{&#0{0,}32;}", "{\"}", "{\'}", "{<}", "{>}", "{\\\}", "{&amp;}"), array("\n", " ", "&quot;", "&#039;", "&lt;", "&gt;", "&#92;", "&amp;amp;"), $val), " \n\0\x0B\xA0");
	}


	//  BR Line Text
	// +-------------------------+
	// Cleans text for database and display, but uses <br> for linebreaks

	function br_text($val, $save_spacing=false)
	{
		return trim(preg_replace(array("{&#0{0,}32;}", "{\"}", "{\'}", "{<}", "{>}", "{\\\}", "{\n}"), array(" ", "&quot;", "&#039;", "&lt;", "&gt;", "&#92;", "<br />"), $this->save_spacing($val, $save_spacing)), " \n\0\x0B\xA0");
	}


	//  No Line Text
	// +-------------------------+
	// Cleans text for database and display, will return on one line

	function no_text($val, $save_spacing=false)
	{
		return trim(preg_replace(array("{\n}", "{&#0{0,}32;}", "{\"}", "{\'}", "{<}", "{>}", "{\\\}"), array("", " ", "&quot;", "&#039;", "&lt;", "&gt;", "&#92;"), $this->save_spacing($val, $save_spacing)), " \n\0\x0B\xA0");
	}


	//  No HTML Text
	// +-------------------------+
	// Cleans text for display

	function no_html($val, $save_spacing=true)
	{
		return trim(preg_replace(array("{\"}", "{\'}", "{<}", "{>}", "{\\\}"), array("&quot;", "&#039;", "&lt;", "&gt;", "&#92;"), $this->save_spacing($val, $save_spacing)), " \n\0\x0B\xA0");
	}


	//  Save spacing
	// +-------------------------+
	// Check if we should remove excessive spacing

	function save_spacing($val, $save_spacing=false)
	{
		if(!$save_spacing)
		{
			$val = preg_replace("{\s+}", " ", $val);
		}
		return $val;
	}


	//  SQL Text
	// +-------------------------+
	// Text is safe to be used in SQL queries, but keeps HTML.

	function sql_text($val)
	{
		return mysql_real_escape_string($val);
	}


	//  BR SQL Text
	// +-------------------------+
	// Text is safe to be used in SQL queries, but keeps HTML. Also makes newlines into html breaks.

	function br_sql_text($val)
	{
		return $this->sql_text(preg_replace(array("{\n}", "{\r}"), array("<br />", ""), $val));
	}


	//  HTML text
	// +-------------------------+
	// Converts text so that text is converted from safe to html
	// USE WITH CAUTION!

	function unsafe_text($val)
	{
		return preg_replace(array("{&quot;}", "{&#039;}", "{&lt;}", "{&gt;}", "{&#92;}"), array("\"", "'", "<", ">", "\\"), $val);
	}


	//  Is Email
	// +-------------------------+
	// Returns an email, but only if its valid

	function is_email($val)
	{
		if(!preg_match("{^[0-9A-Za-z\_\.\-]+@[0-9A-Za-z\-]+\.[0-9A-Za-z\.\-]+$}i", $val))
		{
			return;
		}
		return $val;
	}


	//	 Check URL
	// +-------------------------+
	// Returns link but only if its valid

	function is_url($val)
	{
		if(!preg_match("{^http\://[0-9a-z-]+(\.[0-9a-z-]+)*(\:[0-9]+)?(/.*)?}i", $val))
		{
			return;
		}
		return $val;
	}


	//	 Form Checkbox
	// +-------------------------+
	// Return checkbox values for a form

	function form_checkbox($val)
	{
		if($val)
		{
			return " checked=\"checked\"";
		}
	}


	//	 Form Select
	// +-------------------------+
	// Returns values for radio buttons, dropdown boxes and so on

	function form_select($val)
	{
		if($val !== "")
		{
			return array($val => " selected=\"selected\"");
		}
	}


	//	 Form Select
	// +-------------------------+
	// Returns values for radio buttons, dropdown boxes and so on

	function form_radio($val)
	{
		if($val !== "")
		{
			return array($val => " checked=\"checked\"");
		}
	}

	//	 Check Length
	// +-------------------------+
	// Account for HTML entities when calculating strlen

	function get_length($val)
	{
		return strlen(preg_replace("{&#([0-9]+);}", "*", $val));
	}

	//	 Hash
	// +-------------------------+
	// Makes a super hash, 72 characters long. Very unlikely to have collisions.
	// Do not hash sensitive data with this function.

	function hash($val)
	{
		return md5($val).sha1($val);
	}


	//	 Number
	// +-------------------------+
	// Puts a number into a better format

	function number($val, $dpoint=0)
	{
		return number_format($val, $dpoint, $this->lat->cache['config']['decimal_format'], $this->lat->cache['config']['number_format']);
	}


	//	 Size Image
	// +-------------------------+
	// Restricts height and width of an image

	function size_image($values, $max)
	{

		// The size is within limis and works fine
		if(($values[0] <= $max[0] && $values[1] <= $max[1]) || $max[0] < 1 || $max[1] < 1)
		{
			return $values;
		}

		$ratio = $values[0] / $values[1];

		if (($max[0] / $max[1]) > $ratio)
		{
			$values[1] = $max[1];
			$values[0] = $max[1] * $ratio;
		}
		else
		{
			$values[0] = $max[0];
			$values[1] = $max[0] / $ratio;
		}

		return $values;
	}


	// +-------------------------+
	//   Cache
	// +-------------------------+
	// Parses content with bbtags and whatever

	function cache(&$data, $parse=array())
	{
		$parse['data'] = $data;

		if($parse['data'] == "")
		{
			return;
		}

		if($parse['gid'] == "")
		{
			$parse['gid'] = $this->lat->user['group']['id'];
		}

		$this->bb_profile = $this->load_profile($parse['type']);
		$this->lat->core->load_cache("autoparse");
		$this->type = intval($parse['type']);
		$parse['data'] = $this->swear_filter($parse['data']);

		if(!isset($this->preg_embed))
		{
			// Parse Code bbtags first, and find out which bbtags shouldn't be imbedded ;)
			foreach($this->lat->cache['bbtag'] as $bbtag)
			{
				if($bbtag['no_embed'])
				{
					if($bbtag['opt'])
					{
						$no_embed[] = $bbtag['tag']."=.+?";
					}

					if($bbtag['opt'] != 1)
					{
						$no_embed[] = $bbtag['tag'];
					}

					$this->no_close[] = $bbtag['tag'];
				}
			}

			// Makes a imbed regular expression string for us
			if(!empty($no_embed))
			{
				$this->preg_embed = "{\[(".implode("|", $no_embed).")\]".str_repeat("(.+?)\[(".implode("|", $no_embed).")\]", 4)."}i";
			}
		}

		$parse['data'] = $this->br_text($parse['data'], true);

		if(!array_key_exists("bb", $parse))
		{
			$parse['bb'] = 1;
		}

		if(!array_key_exists("smi", $parse))
		{
			$parse['smi'] = 1;
		}

		$this->sig = $parse['sig'];

		if($parse['smi'])
		{
			$parse['data'] = $this->smilize($parse['data'], $this->profile[$parse['type']][$parse['gid']]);
		}

		if($parse['bb'])
		{
			// Parse Code bbtags first, and find out which bbtags shouldn't be imbedded ;)
			foreach($this->lat->cache['bbtag'] as $bbtag)
			{
				if($bbtag['clean'])
				{
					if($bbtag['opt'])
					{
						$parse['data'] = preg_replace_callback("{\[{$bbtag['tag']}=(.+?)\](.+?)\[/{$bbtag['tag']}\]}si",
							function($matches) use ($bbtag) {
								return $this->bb_clean($matches[1], $matches[2], $bbtag['id'], 1);
							}, $parse['data']);
					}

					if($bbtag['opt'] != 1)
					{
						$parse['data'] = preg_replace_callback("{\[{$bbtag['tag']}](.+?)\[/{$bbtag['tag']}\]}si",
							function($matches) use ($bbtag) {
								return $this->bb_clean("", $matches[1], $bbtag['id'], 1);
							}, $parse['data']);
					}
				}

				// If you put bbcode in options, they'll screw up anyway. Remove em!
				if($bbtag['opt'])
				{
					$parse['data'] = preg_replace_callback("{\[{$bbtag['tag']}=(.+?)\](.+?)\[/{$bbtag['tag']}\]}si",
						function($matches) use ($bbtag) {
							return $this->bb_clean($matches[1], $matches[2], $bbtag['id']);
						}, $parse['data']);
				}
			}

			$parse['data'] = preg_replace_callback("{(\s|^|>)((ftp:\/\/|news:\/\/|http:\/\/|https:\/\/|www\.)([0-9a-zA-Z-]+\.[0-9a-zA-Z-\.].+?))(\s|$|<)}i",
				function($matches) {
					return $this->url_parse(array('url' => $matches[2], 'prefix' => $matches[1], 'suffix' => $matches[5], 'autoparse' => 1));
				}, $parse['data']);


			// Time to parse bbtags! Yay!
			foreach($this->lat->cache['bbtag'] as $bbtag)
			{
				if($bbtag['opt'])
				{
					while(preg_match("{\[{$bbtag['tag']}=(.+?)\](.+?)\[/{$bbtag['tag']}\]}si", $parse['data']))
					{
						$parse['data'] = preg_replace_callback("{\[{$bbtag['tag']}=(.+?)\](.+?)\[/{$bbtag['tag']}\]}si",
							function($matches) use ($bbtag) {
								return $this->bbtag($bbtag['id'], $matches[2], $matches[1]);
							}, $parse['data']);
					}
				}

				if($bbtag['opt'] != 1)
				{
					while(preg_match("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}si", $parse['data']))
					{
						$parse['data'] = preg_replace_callback("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}si",
							function($matches) use ($bbtag) {
								return $this->bbtag($bbtag['id'], $matches[1]);
							}, $parse['data']);
					}
				}
			}

			// If there is imbedded bbtags that messed up, get rid of the tags which could cause a problem
			if(!empty($this->was_imbed) && !empty($no_close))
			{
				$parse['data'] = preg_replace("{\[/(".implode("|", $this->no_close).")\]}i", "", $parse['data']);
			}
		}
		else
		{
			foreach($this->lat->cache['bbtag'] as $bbtag)
			{
				if($bbtag['tag'] == "img")
				{
					$this->img = $bbtag['id'];
					break;
				}
			}

			$parse['data'] = preg_replace_callback("{(\s|^|>)((ftp:\/\/|news:\/\/|http:\/\/|https:\/\/|www\.)([0-9a-zA-Z-]+\.[0-9a-zA-Z-\.].+?))(\s|$|<)}i",
				function($matches) {
					return $this->url_parse(array('url' => $matches[2], 'prefix' => $matches[1], 'suffix' => $matches[5], 'autoparse' => 1));
				}, $parse['data']);
		}

		if(!empty($this->user_replace))
		{
			$user_replace = $this->lat->show->get_name_array($this->user_replace);

			foreach($user_replace as $id => $user)
			{
				$parse['data'] = str_replace("<user:{$id}>", $this->lat->show->make_username($user), $parse['data']);
			}
		}

		unset($this->user_replace);
		unset($this->was_imbed);
		unset($this->bbtag_num);
		unset($this->smiley_num);
		unset($this->img_num);
		unset($this->mda_num);

		return $parse['data'];
	}


	// +-------------------------+
	//   BB Clean
	// +-------------------------+
	// Cleans bbtags of other bbtags that could deform it

	function bb_clean($opt="", $body="", $id, $cbody=0)
	{
		$opt = stripslashes($opt);
		$body = stripslashes($body);

		if($cbody)
		{
			$body = preg_replace("{\[}", "&#91;", $body);
			$body = preg_replace("{\]}", "&#93;", $body);
		}
		else
		{
			$opt = preg_replace("{\[}", "&#91;", $opt);
			$opt = preg_replace("{\]}", "&#93;", $opt);
		}

		if(!empty($opt))
		{
			return "[{$this->lat->cache['bbtag'][$id]['tag']}={$opt}]{$body}[/{$this->lat->cache['bbtag'][$id]['tag']}]";
		}
		else
		{
			return "[{$this->lat->cache['bbtag'][$id]['tag']}]{$body}[/{$this->lat->cache['bbtag'][$id]['tag']}]";
		}
	}


	// +-------------------------+
	//   URL Parse
	// +-------------------------+
	// Parses link or url

	function url_parse($data)
	{
		$this->lat->parse->array_strip($data);
		$lowerurl = strtolower($data['url']);

		if($data['error'])
		{
			return $data['prefix'].str_replace(".", "&#46;", $data['url']).$data['suffix'];
		}

		// Add the http:// if it doesn't start with anything like that... (disables javascript and makes it more like a "link")
		if(substr($lowerurl, 0, 4) == "www." || (substr($lowerurl, 0, 7) != "http://" && substr($lowerurl, 0, 8) != "https://" && substr($lowerurl, 0, 6) != "ftp://" && substr($lowerurl, 0, 7) != "news://"))
		{
			$data['url'] = "http://".$data['url'];
		}

		// Check to see if our URL is now valid one more time
		if(!preg_match("{(\s|^|>)((ftp:\/\/|news:\/\/|http:\/\/|https:\/\/|www\.)([0-9a-zA-Z-]+\.[0-9a-zA-Z-\.].+?))(\s|$|<)}i", $data['url']))
		{
			if(!$data['no_return'])
			{
				if($name != "")
				{
					return $data['prefix'].$this->error_tag("url", $data['name'], $data['url']).$data['suffix'];
				}
				else
				{
					return $data['prefix'].$this->error_tag("url", $data['url']).$data['suffix'];
				}
			}
			else
			{
				return;
			}
		}

		if($data['autoparse'])
		{
			foreach($this->lat->cache['autoparse'] as $ap)
			{
				switch($ap['type'])
				{
					case 0:
						if(preg_match("{{$ap['data']}$}i", $data['url']) && $this->bb_profile['img'] && $this->img_num < $this->bb_profile['img'])
						{
							$this->img_num++;
							return $data['prefix']."<div style=\"display: none\" id=\"div_<!-- NEW INT -->\" class=\"big_img\"><script type=\"text/javascript\">img_type[<!-- INT -->]={$this->type};</script><img id=\"img_<!-- INT -->\" src=\"{$data['url']}\" alt=\"img\" /><div class=\"img_zoom\" id=\"zoom_<!-- INT -->\"><a href=\"{$data['url']}\" target=\"_blank\"><img src=\"<!-- IMAGE -->zoom.png\" alt=\"+\" />&nbsp;<lang:enlarge></a></div></div><noscript><img src=\"{$data['url']}\" alt=\"img\" /></noscript>".$data['suffix'];
						}
						break;
					case 1:
						$arr_url = parse_url($data['url']);
						if(preg_match("{{$ap['site']}$}", $arr_url['host']))
						{
							parse_str($arr_url['query'], $par_url);
							$this->no_text($par_url);
							$data['suffix'] = trim($data['suffix']);
							$data['prefix'] = trim($data['prefix']);
							$out = str_replace("<!-- VIDEO -->", $par_url[$ap['data']], $ap['content']);
						}
						break;
					case 2:
						$arr_url = parse_url($data['url']);
						if(preg_match("{{$ap['site']}$}", $arr_url['host']))
						{
							preg_match_all("{{$ap['data']}}i", $data['url'], $par_url);
							$this->no_text($par_url);
							$data['suffix'] = trim($data['suffix']);
							$data['prefix'] = trim($data['prefix']);
							$out = str_replace("<!-- VIDEO -->", $par_url[0][0], $ap['content']);
						}
						break;
				}

				if($out)
				{
					$embed_hash = $this->hash($data['url']);
					eval("\$return =".$this->lat->skin['embed']);
					return $data['prefix'].$return.$data['suffix'];
				}
			}
		}

		// No name was given, so lets generate one
		if($data['name'] == "" && !$data['no_return'])
		{
			if(strlen($data['url']) > 45)
			{
				$data['name'] = substr($data['url'], 0, 25)."...".substr($data['url'], strlen($data['url']) - 15);
			}
			else
			{
				$data['name'] = $data['url'];
			}
		}

		$data['url'] = strip_tags($data['url']);
		$data['url'] = preg_replace("{\[(.+?)\]}si", "", $data['url']);

		// Return a url or a link
		if(!$data['no_return'])
		{
			return $data['prefix']."<a href=\"{$data['url']}\" target=\"_blank\">{$data['name']}</a>".$data['suffix'];
		}
		else
		{
			return $data['prefix'].$data['url'].$data['suffix'];
		}
	}


	// +-------------------------+
	//   Parse BBtag
	// +-------------------------+
	// Parses a database bbtag

	function bbtag($id, $value, $option="", $override=false)
	{
		$value = stripslashes($value);
		$option = stripslashes($option);

		if(trim($value) == "")
		{
			return;
		}

		// Make sure we aren't imbedding anything too much
		if(!empty($this->preg_embed) && preg_match($this->preg_embed, $value))
		{
			$this->was_imbed = true;
			return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
		}

		// Have we overused this bbtag?
		if($this->lat->cache['bbtag'][$id]['inherit_img'] && !$override)
		{
			if($this->img_num >= $this->bb_profile['img'] && $this->bb_profile['img'])
			{
				return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
			}
		}

		if($this->lat->cache['bbtag'][$id]['inherit_mda'] && !$override)
		{
			if($this->mda_num >= $this->bb_profile['mda'] && $this->bb_profile['mda'])
			{
				return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
			}
		}

		if(!$this->lat->cache['bbtag'][$id]['inherit_img'] && !$this->lat->cache['bbtag'][$id]['inherit_mda'] && !$override)
		{
			if($this->bbtag_num[$this->lat->cache['bbtag'][$id]['id']] >= $this->bb_profile[$id] && $this->bb_profile[$id] > 0)
			{
				return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
			}
			else
			{
				$this->bbtag_num[$this->lat->cache['bbtag'][$id]['id']]++;
			}
		}

		$replace = $this->lat->cache['bbtag'][$id]['replace_with'];
		$this->argument_done = false;
		$value = preg_replace_callback("{(\[(.+?)\])}si", function($matches) use ($argument) {
			return $this->bbtag_argument($matches[1], $matches[2], $argument);
		}, $value);

		if($this->lat->cache['bbtag'][$id]['file'])
		{
			$file = $this->lat->cache['bbtag'][$id]['file'];

			if(!isset($this->bbtag->$file))
			{
				global $lat;
				require_once($lat->config['PLUGINS_PATH']."bbtags/".$this->lat->cache['bbtag'][$id]['file'].".php");

				$class = "bb_".$this->lat->cache['bbtag'][$id]['file'];
				$this->bbtag->$file = new $class;
				$this->bbtag->$file->lat = &$lat;
			}

			$this->bbtag->$file->replace = $this->lat->cache['bbtag'][$id]['replace_with'];
			$this->bbtag->$file->override = $override;
			$value = $this->bbtag->$file->initialize($value, $option, $argument);
			$this->bbtag->$file->override = false;

			if(is_array($value))
			{
				list($value, $option) = $value;
			}

			if($this->bbtag->$file->error)
			{
				$this->bbtag->$file->error = false;
				return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
			}

			if($this->bbtag->$file->return)
			{
				$this->bbtag->$file->value = false;
				return $value;
			}

			if($this->bbtag->$file->option)
			{
				$this->bbtag->$file->option = false;
				$override_option = true;
			}

			if($this->bbtag->$file->replace)
			{
				$replace = $this->bbtag->$file->replace;
			}
		}

		if($value == "")
		{
			return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], "", $option);
		}

		// Return a bbtag with option
		if(($this->lat->cache['bbtag'][$id]['opt'] && $option != "") || $override_option)
		{
			if($option == "")
			{
				return $this->error_tag($this->lat->cache['bbtag'][$id]['tag'], $value, $option);
			}
			// bbtags and new lines are not premitted in options (it can cause odd results)
			$option = preg_replace("{\[.+?\].+?\[/.+?\]}si", "", $option);
			$option = preg_replace("{<br />}", "", $option);

			return str_replace(array("<!-- data -->", "<!-- optn -->"), array($value, $option), $replace);
		}
		else
		{
			return str_replace("<!-- data -->", $value, $replace);
		}
	}


	// +-------------------------+
	//   bbtag Error
	// +-------------------------+
	// Returns bbtag in HTML entities so it is no longer parsed

	function bbtag_argument($full, $val, &$var)
	{
		if($this->argument_done == true)
		{
			return $full;
		}

		if(strpos($full, "=") != false || strpos($full, ":") == false)
		{
			$this->argument_done = true;
			return $full;
		}
		$val = explode(":", $val);
		$val[0] = strtolower($val[0]);
		$var[$val[0]] = $val[1];
	}


	// +-------------------------+
	//   bbtag Error
	// +-------------------------+
	// Returns bbtag in HTML entities so it is no longer parsed

	function error_tag($bbtag, $text, $option="")
	{
		// bbtag has an option
		if($option == "")
		{
			return "&#91;{$bbtag}&#93;{$text}&#91;/{$bbtag}&#93;";
		}
		// bbtag has no option
		else
		{
			return "&#91;{$bbtag}={$option}&#93;{$text}&#91;/{$bbtag}&#93;";
		}
	}


	// +-------------------------+
	//   Smilize
	// +-------------------------+
	// Parses smilies

	function smilize($text)
	{
		$this->lat->core->load_cache("icon");
		foreach($this->lat->cache['icon'] as $smiley)
		{
			// Make sure we're within limits!
			if($smiley['txt'] != "" && $this->smiley_num < $this->bb_profile['smi'])
			{
				while(preg_match("{(\W|\n|^)".preg_quote($smiley['txt'])."(\W|\n|$)}e", $text))
				{
					$text = preg_replace_callback("{(\W|\n|^)".preg_quote($smiley['txt'])."(\W|\n|$)}", function($matches) use ($smiley) {
						return $this->smiley($smiley, $matches[1], $matches[2]);
					}, $text);

					if($this->smiley_num >= $this->bb_profile['smi'])
					{
						break;
					}
				}
			}
		}

		return $text;
	}


	// +-------------------------+
	//   Smiley
	// +-------------------------+
	// Parses one smiley!

	function smiley($smiley, $prefix, $suffix)
	{
		$prefix = stripslashes($prefix);
		$suffix = stripslashes($suffix);

		// Looks like we're at the limit
		if($this->smiley_num >= $this->bb_profile['smi'])
		{
			return $prefix.$smiley['txt'].$suffix;
		}

		$this->smiley_num++;

		return $prefix."<!-- smi{$smiley['txt']} --><img src=\"{$this->lat->config['STORAGE_PATH']}smilies/{$smiley['image']}\" border=\"0\" alt=\"\" style=\"vertical-align:middle\" /><!-- endsmi -->".$suffix;
	}


	// +-------------------------+
	//   Swear Filter
	// +-------------------------+
	// Remove naughty words from a string

	function swear_filter($text)
	{
		if(empty($this->lat->cache['filter']))
		{
			$this->lat->core->load_cache("filter");

			if(!empty($this->lat->cache['filter']))
			{
				foreach($this->lat->cache['filter'] as $filter)
				{
					if($filter['replace_with'] == "")
					{
						$this->lat->cache['filter'][$filter['id']]['replace_with'] = str_repeat($this->lat->cache['config']['filter_character'], strlen($filter['word']));
					}
				}
			}
		}

		if(!empty($this->lat->cache['filter']))
		{
			foreach($this->lat->cache['filter'] as $filter)
			{
				if($filter['word'])
				{
					if($filter['type'])
					{
						$text = preg_replace("{".preg_quote($filter['word'])."}i", $filter['replace_with'], $text);
					}
					else
					{
						$text = preg_replace("{\b".preg_quote($filter['word'])."\b}i", $filter['replace_with'], $text);
					}
				}
			}
		}

		return $text;
	}


	// +-------------------------+
	//   Re-Cache
	// +-------------------------+
	// Re-Caches an item

	function recache($data)
	{
		if(!$data['item'])
		{
			$data['item'] = "data";
		}
		if($data['fetch'][$data['item'].'_reparse'])
		{
			if(!array_key_exists("bb", $data))
			{
				$data['bb'] = 1;
			}

			if(!array_key_exists("smi", $data))
			{
				$data['smi'] = 1;
			}

			$raw_data = $data['fetch'][$data['item']];

			$data_cached = $this->cache($raw_data, array("bb" => $data['bb'], "smi" => $data['smi'], "gid" => $data['gid'], "type" => $data['type']));

			// Unfortunately, we can't error out if the text is too big. This will account for very little posts on the internets.
			$data_cached = substr($data_cached, 0, 65535);

			// Query: Update the cached data in the reply
			$query = array("update"   => $data['table'],
						   "set"      => array($data['item'] => $this->lat->parse->sql_text($raw_data),
											   $data['item']."_cached"  => $this->lat->parse->sql_text($data_cached),
											   $data['item']."_reparse" => 0),
						   "where"    => $data['where'],
						   "shutdown" => 1);

			$this->lat->sql->query($query);

			$data['fetch'][$data['item'].'_cached'] = $data_cached;

			return true;
		}
		return false;
	}


	// +-------------------------+
	//	 Load Profile
	// +-------------------------+
	// Load up a bbtag permission profile, taking all the highest permissions possible

	function load_profile($type, $gid=0)
	{
		$this->lat->core->load_cache("bbtag");
		$this->lat->core->load_cache("bbtag_profile");

		if($gid < 1)
		{
			$gid = $this->lat->user['group']['id'];
		}

		if($this->profile[$type][$gid] != "")
		{
			return $this->profile[$type][$gid];
		}

		$this->profile[$type][$gid]['img'] = 0;
		$this->profile[$type][$gid]['mda'] = 0;
		$this->profile[$type][$gid]['smi'] = 0;

		// Parse each permission profile
		if(!empty($this->lat->cache['bbtag_profile']))
		{
			foreach($this->lat->cache['bbtag_profile'] as $profile)
			{
				if($profile['type'] == $type && in_array($gid, explode(",", $profile['groups'])))
				{
					$profile_tags = unserialize($profile['bbtags']);

					// Take all higher permissions and set them
					if(!empty($profile_tags))
					{
						foreach($profile_tags as $pname => $pval)
						{
							if($pval == -1 && !$this->profile[$type][$gid][$pname])
							{
								$this->profile[$type][$gid][$pname] = 0;
							}
							elseif($pval > 0 && $this->profile[$type][$gid][$pname] != -1 && $this->profile[$type][$gid] < $pval)
							{
								$this->profile[$type][$gid][$pname] = $pval;
							}
						}
					}

					if($this->profile[$type][$gid]['img'] < $profile['max_img'])
					{
						$this->profile[$type][$gid]['img'] = $profile['max_img'];
					}

					if($this->profile[$type][$gid]['mda'] < $profile['max_mda'])
					{
						$this->profile[$type][$gid]['mda'] = $profile['max_mda'];
					}

					if($this->profile[$type][$gid]['smi'] < $profile['max_smi'])
					{
						$this->profile[$type][$gid]['smi'] = $profile['max_smi'];
					}

					if($this->profile[$type][$gid]['chr'] < $profile['max_chr'])
					{
						$this->profile[$type][$gid]['chr'] = $profile['max_chr'];
					}

					if($set_default == false)
					{
						foreach($this->lat->cache['bbtag'] as $bbtag)
						{
							if($bbtag['inherit_img'])
							{
								$this->profile[$type][$gid][$bbtag['id']] = &$this->profile[$type][$gid]['img'];
							}
							elseif($bbtag['inherit_mda'])
							{
								$this->profile[$type][$gid][$bbtag['id']] = &$this->profile[$type][$gid]['mda'];
							}
							elseif($this->profile[$type][$gid][$bbtag['id']] == "")
							{
								$this->profile[$type][$gid][$bbtag['id']] = -1;
							}
						}
						$set_default = true;
					}
				}
			}
		}

		return $this->profile[$type][$gid];
	}
}
?>
