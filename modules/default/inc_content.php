<?php if(!defined('LAT')) die("Access Denied.");

class inc_content
{
	function initialize()
	{
		$this->lat->core->load_cache("icon");
		$this->lat->core->load_cache("bbtag");
	}


	// +-------------------------+
	//	 Post Icon Table
	// +-------------------------+
	// Generate a table with all the emoticons representing post icons

	function post_icon_table()
	{
		$this->lat->get_input->unsigned_int("post_icon");

		// Combine smilies to be post icons too! (if set)
		foreach($this->lat->cache['icon'] as $smilie)
		{
			if($smilie['is_icon'])
			{
				// "6" will be the row maximum...
				if($num == $this->lat->cache['config']['picon_table'])
				{
					$num = 0;
					$row++;
				}
				$num++;

				if($this->lat->input['post_icon'] == $smilie['id'])
				{
					$checked = " checked=\"checked\"";
					$checked_found = true;
				}
				else
				{
					$checked = "";
				}

				$td[$row][] = "<label><input type=\"radio\" name=\"post_icon\" value=\"{$smilie['id']}\"{$checked} /> <img src=\"{$this->lat->config['STORAGE_PATH']}smilies/{$smilie['image']}\" alt=\"{$smilie['txt']}\" /></label>";
			}
		}

		// Looks like we have no post icons :(
		if(empty($td))
		{
			return;
		}

		foreach($td as $val)
		{
			$tr[] = "<td class=\"picon\">".implode("</td><td class=\"picon\">", $val)."</td>";
		}

		$return = "<tr>".implode("</tr><tr>", $tr)."</tr>";

		$this->smi_on = 1;

		if(!$checked_found)
		{
			$checked = " checked=\"checked\"";
		}
		else
		{
			$checked = "";
		}

		// Return the icon table
		$return = "<table><tr><td colspan=\"{$this->lat->cache['config']['picon_table']}\" class=\"picon\"><label><input type=\"radio\" name=\"post_icon\" value=\"\"{$checked} /> {$this->lat->lang['none']}</label></td></tr>".$return."</table>";

		return $return;
	}


	// +-------------------------+
	//	 Emoticon Table
	// +-------------------------+
	// Generate a table with all the emoticons

	function emoticon_table()
	{
		// Once again, smilies from the database, and = 2 means it goes in this table ;)
		foreach($this->lat->cache['icon'] as $smilie)
		{
			if($smilie['is_post'] == 2)
			{
				if($num == $this->lat->cache['config']['smilies_table'])
				{
					$num = 0;
					$row++;
				}
				$num++;

				// The javascript should be loaded with the post form...
				$td[$row][] = "<a href='javascript:smilieInsert(\"{$smilie['txt']}\");'><img src=\"{$this->lat->config['STORAGE_PATH']}smilies/{$smilie['image']}\" alt=\"{$smilie['txt']}\" /></a>";
			}
		}

		foreach($td as $val)
		{
			$tr[] = "<td class=\"smilie\">".implode("</td><td class=\"smilie\">", $val)."</td>";
		}

		$smilies_html = "<tr>".implode("</tr><tr>", $tr)."</tr>";

		eval("\$smilies_return =".$this->lat->skin['smilies_table']);

		return $smilies_return;
	}


	// +-------------------------+
	//	 BBtag buttons
	// +-------------------------+
	// Generate BBtag buttons from the database!

	function bbtag_buttons()
	{
		$this->bb_on = 1;

		// Very unique feature... we can have permissions on bbtags and everything ;)
		foreach($this->lat->cache['bbtag'] as $bb)
		{
			// Our administrator doesn't want this bbtag to have a button
			if($bb['placement'] > 0)
			{
				$bname = strtoupper($this->lat->cache['bbtag'][$bb['id']]['tag']);

				// Optionless bbtag... just a button :)
				if(!$bb['opt'] || $bb['opt'] == 2)
				{
					$placement = substr($bb['placement'], 0, 1);

					eval("\$data[\$placement] .=".$this->lat->skin['bb_button']);
				}
				// Has an option! Can we have a dropdown box?
				else
				{
					if($bb['display'])
					{
						$opthtml = "<option value=\"\">{$bname}</option>";
						$display = explode("\n", $bb['display']);

						// Options rendering
						foreach($display as $option)
						{
							$option = explode("|", $option);
							$opthtml .= "<option value=\"{$option[1]}\" style=\"{$option[2]}\">{$option[0]}</option>";
						}

						$placement = substr($bb['placement'], 0, 1);

						eval("\$data[$placement] .=".$this->lat->skin['bb_dropdown']);
					}
				}
			}
		}

		// No bbtags returned :(
		if(empty($data))
		{
			return;
		}

		return "<div style=\"padding: 0px 0px 2px 0px\">".implode("</div><div style=\"padding: 0px 0px 2px 0px\">", $data)."</div>";
	}


	// +-------------------------+
	//	 Post footer
	// +-------------------------+
	// Generates a couple of links to add onto post form footer sides and such

	function post_footer($type, $gid=0)
	{
		if($gid < 1)
		{
			$gid = $this->lat->user['gid'];
		}

		$bb_profile = $this->lat->parse->load_profile($type, $gid);

		$html .= str_replace("<!-- NUM -->", $this->lat->parse->number($bb_profile['chr']), $this->lat->lang['chr_limit']);
		$html .= str_replace("<!-- NUM -->", $this->lat->parse->number($bb_profile['smi']), $this->lat->lang['smi_limit']);
		$html .= str_replace("<!-- NUM -->", $this->lat->parse->number($bb_profile['img']), $this->lat->lang['img_limit']);
		$html .= str_replace("<!-- NUM -->", $this->lat->parse->number($bb_profile['mda']), $this->lat->lang['mda_limit']);

		return $html;
	}
}
?>