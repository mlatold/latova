<?php if(!defined('LAT')) die("Access Denied.");

class kernel_show
{
	//   Show Initialize
	// +-------------------------+
	// Start counting the script execution time

	function initialize()
	{
		$this->exec = $this->lat->core->timer();
	}


	//	 Render Page
	// +-------------------------+
	// Output the page and finish script execution

	function render()
	{
		// Output HTML with the global layout
		if(!$this->no_layout && !$this->ajax)
		{
			if($this->lat->user['pm_notify'])
			{
				// Query: Fetch the single private message from the database
				$query = array("select" => "m.id, m.sent_from, m.title, m.sent_date",
							   "from"   => "kernel_msg m",
							   "where"  => "m.unread=1 AND m.sent_to=".$this->lat->user['id'],
							   "limit"  => 1,
							   "order"  => "m.sent_date DESC");

				$pm_fetch = $this->lat->sql->query($query);

				eval("\$this->lat->output .=".$this->lat->skin['pm']);

				// Query: Update user to no longer display private message notifications
				$query = array("update"	  => "user u",
							   "set"	  => array("u.pm_notify" => 0),
							   "where"	  => "u.id=".$this->lat->user['id'],
							   "shutdown" => 1,
							   "low"	  => 1);

				$this->lat->sql->query($query);

				$this->js_end[] = "show_pm(0);";
			}

			$this->lat->session->update_session();
			$raw_stats['active'] = array();

			// Stats
			if("forum" == $this->lat->input['pg'] && !$this->lat->input['id'] && !$this->lat->input['do'] && !$this->error_occured)
			{
				// Query: Fetch users active in the last session_length time
				$query = array("select" => "s.uid, s.spider, s.last_cn, s.last_pg, s.last_do",
							   "user"   => array("u user_"),
							   "from"   => "kernel_session s",
							   "left"   => "user u ON (s.uid=u.id)",
							   "where"  => "(s.uid != '{$this->lat->user['id']}' OR s.uid=0) AND s.sid != '{$this->lat->user['sid']}' AND s.last_time > ".(time() - ($this->lat->cache['config']['session_length'] * 60)).$where,
							   "order"  => "last_time DESC");

				if($this->lat->user['id'])
				{
					$raw_stats['members']++;

					// Let's try to see where our user is at
					$location = $this->lat->lang['on_'.$this->lat->input['pg'].'-'.$this->lat->input['do']];

					// Can't find anywhere, use the generic page
					if($location == "")
					{
						$location = $this->lat->lang['on_unknown'];
					}
					// Output where they are on our website
					else
					{
						$location = str_replace(array("<!-- URL -->", "<!-- EXTRA -->"), array($this->lat->url, $this->lat->content), $location);
					}

					$raw_stats['active'][] = $this->make_username($this->lat->user, "", "", " title='{$location}'");
				}
				else
				{
					$raw_stats['guests']++;
				}

				$raw_stats['users'] = 1;

				while($fetch = $this->lat->sql->query($query))
				{
					$raw_stats['users']++;

					// Not a spider
					if(!$fetch['spider'])
					{
						// Add the user to the active list
						if($fetch['user_id'] == $this->lat->user['id'] && $fetch['user_id'])
						{
							$raw_stats['members']++;
						}
						elseif($fetch['user_id'])
						{
								// Let's try to see where our user is at
								$location = $this->lat->lang['on_'.$fetch['last_pg'].'-'.$fetch['last_do']];

								// Can't find anywhere, use the generic page
								if($location == "")
								{
									$location = $this->lat->lang['on_unknown'];
								}
								// Output where they are on our website
								else
								{
									$location = str_replace(array("<!-- URL -->", "<!-- EXTRA -->"), array($this->lat->url, $fetch['last_cn']), $location);
								}

							$raw_stats['active'][] = $this->make_username($fetch, "user_", "", " title='{$location}'");
							$raw_stats['members']++;
						}
						// Just a guest...
						else
						{
							$raw_stats['guests']++;
						}
					}
					else
					{
						$raw_stats['spiders']++;
						$bot = explode("|", $fetch['spider']);

						// A bot with a URL
						if($bot[1])
						{
							$raw_stats['active'][] = "<span class='spider'><a href='{$bot[1]}'>{$bot[0]}</a></span>";
						}
						// A normal bot
						else
						{
							$raw_stats['active'][] = "<span class='spider'>{$bot[0]}</span>";
						}
					}
				}

				// More than one person online
				if($raw_stats['users'] == 1)
				{
					$this->lat->lang['online_main'] = str_replace("<!-- USERS -->", $this->lat->lang['one_user'], $this->lat->lang['online_main']);
				}
				// We're the only person online
				else
				{
					$this->lat->lang['online_main'] = str_replace("<!-- USERS -->", str_replace("<!-- NUM -->", intval($raw_stats['users']), $this->lat->lang['multi_user']), $this->lat->lang['online_main']);
				}

				// Just one user online
				if($raw_stats['members'] == 1)
				{
					$this->lat->lang['online_main'] = str_replace("<!-- MEMBERS -->", $this->lat->lang['one_member'], $this->lat->lang['online_main']);
				}
				// Many or no users online
				else
				{
					$this->lat->lang['online_main'] = str_replace("<!-- MEMBERS -->", str_replace("<!-- NUM -->", intval($raw_stats['members']), $this->lat->lang['multi_member']), $this->lat->lang['online_main']);
				}

				if($raw_stats['guests'] == 1)
				{
					$this->lat->lang['online_main'] = str_replace("<!-- GUESTS -->", $this->lat->lang['one_guest'], $this->lat->lang['online_main']);
				}
				// Many or no guests online
				else
				{
					$this->lat->lang['online_main'] = str_replace("<!-- GUESTS -->", str_replace("<!-- NUM -->", intval($raw_stats['guests']), $this->lat->lang['multi_guest']), $this->lat->lang['online_main']);
				}

				// Spider
				if($raw_stats['spiders'] == 1)
				{
					$this->lat->lang['online_main'] = str_replace("<!-- SPIDERS -->", $this->lat->lang['one_spider'], $this->lat->lang['online_main']);
				}
				// Many or no spiders online
				else
				{
					$this->lat->lang['online_main'] = str_replace("<!-- SPIDERS -->", str_replace("<!-- NUM -->", intval($raw_stats['spiders']), $this->lat->lang['multi_spider']), $this->lat->lang['online_main']);
				}

				// There aren't any users to list as being online
				if(empty($raw_stats['active']))
				{
					$active = $this->lat->lang['members_off'];
				}
				// List online users
				else
				{
					$active = implode(", ", $raw_stats['active']);
				}

				$statistics = array();

				foreach($this->lat->cache['page'] as $page)
				{
					if($page['system'])
					{
						$this->lat->core->load_module($page['name']);
						$s = $this->lat->module->$page['name']->latova_system(array("type" => "statistics", "user_count" => $raw_stats['users']));
						if(is_array($s))
						{
							$statistics = array_merge($statistics, $s);
						}
						elseif($s != "")
						{
							$statistics[] = $s;
						}
					}
				}

				$statistics = implode("<br />", $statistics);

				// Output user stats
				eval("\$this->lat->output .=".$this->lat->skin['site_statistics']);
			}

			// Generate a menu for the top
			foreach($this->lat->cache['page'] as $page)
			{
				if($page['can_search'])
				{
					for($i=1;$i<=$page['can_search'];$i++)
					{
						if($this->default_search == $page['name'].'_'.$i)
						{
							$sel = " selected=\"selected\"";
						}
						$qs_opt .= "<option value=\"{$i}{$page['name']}\"{$sel}>{$this->lat->lang['qs_'.$page['name'].'_'.$i]}</option>";
						$sel = "";
					}
				}

				if($page['menu'])
				{
					$menu[] = $page['name'];
					$menu_num++;
				}
			}

			// Sort menu items alphabetically
			sort($menu);

			$first = " class=\"first\"";
			foreach($menu as $m)
			{
				if(!$this->lat->cache['page'][$m]['menu_url'])
				{
					$menu_url = $this->lat->url."pg=".$this->lat->cache['page'][$m]['name'];
				}
				else
				{
					$menu_url = str_replace("{url}", $this->lat->url, $this->lat->cache['page'][$m]['menu_url']);
				}

				if($this->lat->cache['page'][$m]['name'] == "search")
				{
					$menu_url .= ";p=".$this->lat->input['pg'];
				}

				eval("\$menu_bar .=".$this->lat->skin['menu']);
				$first = "";
			}

			// Quick search
			if($qs_opt)
			{
				eval("\$qsearch =".$this->lat->skin['qs']);
			}

			$date = $this->make_time(str_replace(array("[", "]"), "", $this->lat->user['long_date']), time());

			// Title in uppercase
			$title = strtoupper($this->lat->cache['config']['script_name']);

			if($this->lat->input['pg'] != $this->lat->cache['config']['default_page'] || $this->lat->input['do'] || $this->lat->input['id'])
			{
				// Navigation home link
				$nav = "<a href='{$this->lat->url}'>{$this->lat->cache['config']['script_name']}</a>";

				// Parse each navigation point
				if(is_array($this->lat->nav))
				{
					foreach($this->lat->nav as $nav_num => $nav_each)
					{
						// Remove first link if its the first page
						if($nav_each[1] == "pg=".$this->lat->cache['config']['default_page'] && !$nav_skip)
						{
							$nav_skip = true;
						}
						// Array (includes link + name)
						elseif(is_array($nav_each))
						{
							if($nav_each[1] != "" && $nav_num != count($this->lat->nav) - 1)
							{
								$nav .= " <b>&raquo;</b> <a href='{$this->lat->url}{$nav_each[1]}'>{$nav_each[0]}</a>";
							}
							elseif($nav_each)
							{
								$nav .= " <b>&raquo;</b> ".$nav_each[0];
							}
						}
						// String (just the name)
						else
						{
							$nav .= " <b>&raquo;</b> ".$nav_each;
						}
					}
				}
				elseif($this->lat->nav && !is_array($this->lat->nav))
				{
					$nav .= " <span class=\"raquo\">&raquo;</span> ".$this->lat->nav;
				}

				eval("\$nav =".$this->lat->skin['nav']);
			}

			$php_end = $this->lat->core->timer();
			$php_time = $this->lat->parse->number($php_end - $this->exec, 5);
			$this->lat->sql->sql_time = $this->lat->parse->number($this->lat->sql->sql_time, 5);
			$stats = str_replace(array("<!-- SQL TIME -->", "<!-- SQL QUERIES -->", "<!-- TIME -->"), array($this->lat->sql->sql_time, $this->lat->sql->sql_queries, $php_time), $this->lat->lang['technical_stats']);
			$select = "&nbsp;";

			eval("\$output =".$this->lat->skin['layout']);

			// == Copyright ==

			// This is open source software. You are free to edit and distribute it at
			// your own free will - however you are NOT allowed to alter copyright notices!

			// I've worked thousands of hours on this and give it out for free!
			// Please support the open source movement and this software!

			$output = str_replace("<!-- COPYRIGHT -->", "<a href=\"https://github.com/mikelat/latova\" target=\"_blank\">Powered by Latova &copy; ".date("Y")." Michael Lat</a>", $output);


			if(DEBUG)
			{
				$div = $this->div_state("debug");

				$sql = "<ul><li>".implode("</li><li>", $this->lat->sql->sql_out)."</li></ul>";

				eval("\$this->lat->output .=".$this->lat->skin['sql']);
				unset($sql);
				unset($this->lat->sql->sql_out);
			}

			$this->lat->get_input->preg_whitelist("action", "A-Za-z0-9_");

			// Form Level Errors
			if(!empty($this->lat->form_error))
			{
				foreach($this->lat->form_error as $form_err)
				{
					if($this->lat->lang[$form_err] != "")
					{
						$form_err = $this->lat->lang[$form_err];
					}

					if($form_errors_parsed)
					{
						$form_errors_parsed .= "<br />";
					}
					$form_errors_parsed .= " &raquo; ".$form_err;
				}

				eval("\$form_error =".$this->lat->skin['form_error']);

				if($this->lat->cache['page'][$this->lat->input['pg']]['cp'])
				{
					$this->lat->output = str_replace("<!-- CP HEADER -->", $form_error, $this->lat->output);
				}
				else
				{
					$this->lat->output = $form_error.$this->lat->output;
				}
			}
			// Action Successful
			elseif(!$this->error_occured && $this->lat->user['act'] != "")
			{
				if($this->lat->lang['act_'.$this->lat->user['act']] != "")
				{
					$this->lat->user['act'] = $this->lat->lang['act_'.$this->lat->user['act']];
				}
				eval("\$action_success .=".$this->lat->skin['action']);

				if($this->lat->cache['page'][$this->lat->input['pg']]['cp'])
				{
					$this->lat->output = str_replace("<!-- CP HEADER -->", $action_success, $this->lat->output);
				}
				else
				{
					$this->lat->output = $action_success.$this->lat->output;
				}
			}

			// Logged in links
			if($this->lat->user['id'])
			{
				$this->lat->lang['inbox_header'] = str_replace("<!-- NUM -->", $this->lat->user['pm_unread'], $this->lat->lang['inbox_header']);
				$pms = "<a href=\"{$this->lat->url}pg=msg\">{$this->lat->lang['inbox_header']}</a>";
				if($this->lat->user['group']['superadmin']) {
					$cp = "<a href=\"{$this->lat->url}pg=cp\">{$this->lat->lang['cp']}</a></li><li>";
				}
				eval("\$user_html =".$this->lat->skin['logged_in']);
			}
			// Guest links
			else
			{
				eval("\$user_html =".$this->lat->skin['logged_out']);
			}
		}
		else
		{
			$output = $this->lat->output;
		}

		//$this->lat->title = strip_tags($this->lat->title);

		$this->head[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->lat->config['STORAGE_PATH']}css/{$this->skin_id}.css\" />";
		$this->head[] = "<link rel=\"shortcut icon\" href=\"{$this->lat->config['ROOT_PATH']}favicon.ico\" />";
		$this->head[] = "<meta http-equiv=\"Pragma\" content=\"no-cache\" />";
		$this->head[] = "<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />";
		$this->head[] = "<meta http-equiv=\"Expires\" content=\"-1\" />";
		$this->head[] = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$this->lat->lang['charset']};\" />";
		$this->head = implode("\n", $this->head);

		// Start replacing everything into the global layout
		$output = str_replace("<!-- TITLE -->",      $this->lat->title,	 $output);
		$output = str_replace("<!-- USER -->",	     $user_html,		 $output);
		$output = str_replace("<!-- DATA -->",	     $this->lat->output, $output);
		$output = str_replace("<!-- MODULES -->",    $menu_bar,			 $output);
		$output = str_replace("<!-- NAVIGATION -->", $nav,				 $output);
		$output = str_replace("<!-- HEAD -->",	     $this->head,		 $output);
		$output = str_replace("<!-- CP HEADER -->",  "",                 $output);

		$output = preg_replace_callback("{\<lang:(.+?)\>}is", array($this, "convert_word"), $output);
		$output = preg_replace_callback("{\<!-- (NEW )?INT --\>}is",  array($this, "int"), $output);

		if($this->num)
		{
			$this->js_end[] = "do_image({$this->num});";
		}

		$this->js_vars['cookie_domain'] = $this->lat->cache['config']['cookie_domain'];
		$this->js_vars['cookie_path'] = $this->lat->cache['config']['cookie_path'];
		$this->js_vars['cookie_prefix'] = $this->lat->cache['config']['cookie_prefix'];
		$this->js_vars['image_url'] = $this->lat->image_url;
		$this->js_vars['url'] = $this->lat->url;

		foreach($this->js_vars as $jvn => $jvv)
		{
			if(substr($jvv, 0, 1) == "{")
			{
				$js .= "\nvar {$jvn} = {$jvv}";
			}
			elseif(is_int($jvv))
			{
				$js .= "\nvar {$jvn} = {$jvv};";
			}
			else
			{
				$js .= "\nvar {$jvn} = \"{$jvv}\";";
			}
		}

		if($this->num)
		{
			if($this->lat->user['dont_resize_imgs'])
			{
				$js .= "\nvar img_type = new Array();\nvar img_w = new Array(0, 0, 0, 0);\nvar img_h = new Array(0, 0, 0, 0);";
			}
			else
			{
				$js .= "\nvar img_type = new Array();\nvar img_w = new Array({$this->lat->cache['config']['img_0_w']}, {$this->lat->cache['config']['img_1_w']}, {$this->lat->cache['config']['img_2_w']}, {$this->lat->cache['config']['img_3_w']});\nvar img_h = new Array({$this->lat->cache['config']['img_0_h']}, {$this->lat->cache['config']['img_1_h']}, {$this->lat->cache['config']['img_2_h']}, {$this->lat->cache['config']['img_3_h']});";
			}
		}

		$js = "<script type=\"text/javascript\">\n<!--{$js}\n-->\n</script>";
		$this->js_files[] = $this->lat->config['MODULES_PATH']."default/js_universal";
		$this->js_files[] = $this->lat->config['PLUGINS_PATH']."other/jquery";

		foreach($this->js_files as $jf)
		{
			$js .= "\n<script src=\"{$jf}.js\" type=\"text/javascript\"></script>";
		}

		$this->extra_html[] = "<div class=\"help_box\" style=\"display:none\" id=\"help\"></div>";

		if(!empty($this->js_end))
		{
			$this->extra_html[] = "\n<script type=\"text/javascript\">\n<!--\n".implode("\n", $this->js_end)."\n-->\n</script>";
		}
		$this->extra_html = implode("\n", $this->extra_html);

		$output = str_replace("<!-- IMAGE -->",	$this->lat->image_url, $output);
		$output = str_replace("<!-- EXTRA -->", $this->extra_html,	   $output);
		$output = str_replace("<!-- URL -->",   $this->lat->url,	   $output);
		$output = str_replace("<!-- JS -->",    $js,				   $output);

		if(DEBUG)
		{
			$this->lat->sql->exec_shutdown();
		}

		// Headers... since this is a dynamic content website, caching is a no-no
		@header("HTTP/1.1 200 OK");
		@header("Content-type: text/html; charset={$this->lat->lang['charset']}");
		@header("Cache-Control: no-cache, must-revalidate, max-age=0");
		@header("Expires: 0");
		@header("Pragma: no-cache");

		// Compress the output

		if(!DEBUG)
		{
			ob_start("ob_gzhandler");
		}
		echo $output;
		ob_end_flush();

		if(!DEBUG)
		{
			$this->lat->sql->exec_shutdown();
		}
	}


	//	 Int
	// +-------------------------+
	// Returns sequenced number

	function int($new="")
	{
		if($new)
		{
			$this->num++;
		}
		return $this->num;
	}


	//	 Convert Word
	// +-------------------------+
	// Used for post language processing

	function convert_word($word)
	{
		return $this->lat->lang[$word[1]];
	}


	//	 Find GD
	// +-------------------------+
	// Finds GD version

	function find_gd()
	{
		if($this->lat->cache['config']['gd_version'] == -1)
		{
			$gd = gd_info();
			$gd = $this->lat->parse->preg_whitelist($gd['GD Version'], "0-9.");

			if(version_compare($gd, '2.0') === -1)
			{
				$this->lat->cache['config']['gd_version'] = 0;
			}
			else
			{
				$this->lat->cache['config']['gd_version'] = 1;
			}
		}

		return $this->lat->cache['config']['gd_version'];
	}



	//	 Make Photo
	// +-------------------------+
	// Gets an photo of our user

	function make_photo($data)
	{
		if($data['url'] && $data['width'] && $data['height'] && $data['type'])
		{
			switch($data['type'])
			{
				// Uploaded Photo
				case 1:
					return "<img src=\"{$this->lat->cache['config']['photo_url']}{$data['url']}\" height=\"{$data['height']}\" width=\"{$data['width']}\" alt=\"\" />";
				// Linked Photo
				case 2:
					return "<img src=\"{$data['url']}\" height=\"{$data['height']}\" width=\"{$data['width']}\" alt=\"\" />";
			}
		}
	}


	// +-------------------------+
	//	 Make Avatar
	// +-------------------------+
	// Gets an avatar of our user

	function make_avatar($data)
	{
		if($data['url'] && $data['width'] && $data['height'] && $data['type'] && (!$this->lat->user['hide_ava'] || $data['force']))
		{
			switch($data['type'])
			{
				// Uploaded Photo
				case 1:
					return "<img src=\"{$this->lat->cache['config']['avatar_url']}{$data['url']}\" height=\"{$data['height']}\" width=\"{$data['width']}\" alt=\"\" />";
				// Linked Photo
				case 2:
					return "<img src=\"{$data['url']}\" height=\"{$data['height']}\" width=\"{$data['width']}\" alt=\"\" />";
				case 3:
					return "<img src=\"{$this->lat->cache['config']['gallery_url']}{$data['url']}\" height=\"{$data['height']}\" width=\"{$data['width']}\" alt=\"\" />";
			}
		}
	}


	// +-------------------------+
	//	 Make Size
	// +-------------------------+
	// Goes from bits to bites!

	function make_size($size, $long=0, $round=1)
	{
		// Over 1 KB
		if($size > 1023)
		{
			$size = $size / 1024;
		}
		// Byte(s)
		else
		{
			$size = round($size, $round);
			$suffix = $long ? $size == 1 ? $this->lat->lang['b'] : $this->lat->lang['bs'] : $this->lat->lang['bshort'];
			return $size.$suffix;
		}

		// Over 1 MB
		if($size > 1023)
		{
			$size = $size / 1024;
		}
		// Kilobyte(s)
		else
		{
			$size = round($size, $round);
			$suffix = $long ? $size == 1 ? $this->lat->lang['kb'] : $this->lat->lang['kbs'] : $this->lat->lang['kbshort'];
			return $size.$suffix;
		}

		// Over 1 GB
		if($size > 1023)
		{
			$size = $size / 1024;
		}
		// Megabyte(s)
		else
		{
			$size = round($size, $round);
			$suffix = $long ? $size == 1 ? $this->lat->lang['mb'] : $this->lat->lang['mbs'] : $this->lat->lang['mbshort'];
			return $size.$suffix;
		}

		// Gigabytes
		$size = round($size, $round);
		$suffix = $long ? $size == 1 ? $this->lat->lang['gb'] : $this->lat->lang['gbs'] : $this->lat->lang['gbshort'];
		return $size.$suffix;
	}


	// +-------------------------+
	//	 Make Time
	// +-------------------------+
	// Does time calculations to take into consideration timezones, DST and such.

	function make_time($format, $time=0)
	{
		$now = time();

		// No time was given
		if(!$time)
		{
			return "---";
		}

		// Our user has a timezone set
		if($this->lat->user['timezone'])
		{
			$time += ($this->lat->user['timezone'] * 3600);
			$now += ($this->lat->user['timezone'] * 3600);
		}

		// DST settings
		if($this->lat->user['dst'])
		{
			$time += 3600;
			$now += 3600;
		}

		// We have relative format!
		if(preg_match("{\[.*\]}", $format))
		{
			// One second ago?
			if(($time + 2) > $now)
			{
				return $this->lat->lang['right_now'];
			}
			// < 59 seconds ago?
			elseif(($time + 60) > $now)
			{
				return intval(gmdate("s", $now - $time)).$this->lat->lang['second_ago'];
			}
			// One minute ago?
			elseif(($time + 120) > $now)
			{
				return $this->lat->lang['one_minute'];
			}
			// < 59 Minutes ago?
			elseif(($time + 3600) > $now)
			{
				return intval(gmdate("i", $now - $time)).$this->lat->lang['minute_ago'];
			}
			// Today
			elseif(gmdate("dmy", $now) == gmdate("dmy", $time))
			{
				$relative = $this->lat->lang['today'];
			}
			// Yesterday!
			elseif(gmdate("dmy",$now - 86400) == gmdate("dmy",$time))
			{
				$relative = $this->lat->lang['yesterday'];
			}
			// Tommorow!
			elseif(gmdate("dmy",$now + 86400) == gmdate("dmy",$time))
			{
				$relative = $this->lat->lang['tommorow'];
			}
		}

		$date = gmdate($format, $time);

		// Relative Day
		if($relative)
		{
			$date = preg_replace("{\[.*\]}s", $relative, $date);
		}
		// Remove relative brackets
		else
		{
			$date = str_replace(array("[", "]"), "", $date);
		}

		return $date;

	}


	// +-------------------------+
	//	 Get Minimized
	// +-------------------------+
	// Brings back minimized values

	function div_state($name, $fade=false)
	{
		// Do we need to parse the cookie?
		if(empty($this->minimized_cookie))
		{
			$this->minimized_cookie = $this->lat->session->get_cookie("minimized");
			$this->minimized_cookie = explode("|", $this->minimized_cookie);
		}

		$is_min = in_array($name, $this->minimized_cookie);

		if($fade)
		{
			$fade_txt = ", true";
		}

		// Represents value on a minimized table
		if($is_min)
		{
			$div[0] = "<a href=\"javascript:toggle('{$name}'{$fade_txt});\"><img src=\"{$this->lat->image_url}expand.png\" alt=\"+\" id=\"img_{$name}\" /></a>";
			$div[1] = "display: none;";
		}
		else
		{
			$div[0] = "<a href=\"javascript:toggle('{$name}'{$fade_txt});\"><img src=\"{$this->lat->image_url}collapse.png\" alt=\"-\" id=\"img_{$name}\" /></a>";
		}

		return $div;
	}


	// +-------------------------+
	//	 Generate Pages
	// +-------------------------+
	// Generates page number links with HTML.

	function make_pages($data)
	{
		if(!$data['cap'])
		{
			return;
		}

		$total_pages = ceil($data['total'] / $data['cap']);

		if(!$data['st_var'])
		{
			$data['st_var'] = "st";
		}

		if(!$data['st_url'])
		{
			$data['st_url'] = ";{$data['st_var']}=";
		}

		if($total_pages > 0 && !$data['content'])
		{
			$current = ceil($this->lat->get_input->unsigned_int($data['st_var']) / $data['cap']);
			if($current >= $total_pages)
			{
				$current = $total_pages - 1;
			}

			// < Current page
			if($current)
			{
				for($i=($current - 1); $i >= ($current - $data['links']); $i--)
				{
					// We're finished the loop
					if($i == 0)
					{
						$prev_pages = "<li><a href='{$data['url']}'>".($i+1)."</a></li>".$prev_pages;
						break;
					}
					// We're at the beginning
					if($i == ($current - $data['links']))
					{
						$prev_first = "<li><a href='{$data['url']}'>&laquo;</a></li>";
						break;
					}
					$prev_pages = "<li><a href='{$data['url']}{$data['st_url']}".($i * $data['cap'])."'>".($i+1)."</a></li>".$prev_pages;
				}
			}

			// > Current page
			if($current != ($total_pages-1))
			{
				for($i=($current + 1); $i <= ($current + $data['links']); $i++)
				{
					// We're finished the loop
					if($i == ($total_pages-1))
					{
						$for_pages .= "<li><a href='{$data['url']}{$data['st_url']}".($i * $data['cap'])."'>".($i+1)."</a></li>";
						break;
					}
					// We're at the end
					if($i == ($current + $data['links']))
					{
						$for_first = "<li><a href='{$data['url']}{$data['st_url']}".(($total_pages-1) * $data['cap'])."'>&raquo;</a></li>";
						break;
					}
					$for_pages .= "<li><a href='{$data['url']}{$data['st_url']}".($i * $data['cap'])."'>".($i+1)."</a></li>";
				}
			}

			// Previous page
			if($prev_pages)
			{
				if($current - 1 > 0)
				{
					$prev = "<li><a href='{$data['url']}{$data['st_url']}".(($current - 1) * $data['cap'])."'>&lsaquo;</a></li>";
				}
				else
				{
					$prev = "<li><a href='{$data['url']}'>&lsaquo;</a></li>";
				}
			}

			// Next page
			if($for_pages)
			{
				$for = "<li><a href='{$data['url']}{$data['st_url']}".(($current + 1) * $data['cap'])."'>&rsaquo;</a></li>";
			}
		}
		elseif($data['content'])
		{
			if($total_pages < 2)
			{
				return;
			}

			for($i=0; $i < $data['links']; $i++)
			{
				if($i == $total_pages)
				{
					break;
				}

				if($i == $data['links'] - 1 && $total_pages != $data['links'])
				{
			   		$pages[] = "</ul>... <ul><li><a href=\"{$data['url']}{$data['st_url']}".(($total_pages - 1) * $data['cap'])."\">".$total_pages."</a></li>";
			   		break;
				}

				if($i == 0)
				{
					$pages[] = "<li><a href=\"{$data['url']}\">".($i+1)."</a></li>";
				}
				else
				{
					$pages[] = "<li><a href=\"{$data['url']}{$data['st_url']}".($i * $data['cap'])."\">".($i+1)."</a></li>";
				}
			}

			return "<div class=\"pages_content\"><ul>".implode($pages)."</ul></div>";
		}

		// Only 1 Page
		if($total_pages < 2)
		{
			return "<div class=\"pages\"><ul><li class=\"first\">1 {$this->lat->lang['page']}</li></ul></div>";
		}
		// More than one page
		else
		{
			return "<div class=\"pages\"><ul><li class=\"first\">{$total_pages} {$this->lat->lang['pages']}</li>".$prev_first.$prev.$prev_pages."<li class=\"current\">".($current+1)."</li>".$for_pages.$for.$for_first."</ul></div>";
		}
	}


	// +-------------------------+
	//	 Make Username
	// +-------------------------+
	// Allows for global username/guest name display changes

	function make_username($fetch, $pre="", $guest="", $extra="")
	{
		// This is a user, make a profile link!
		if($fetch[$pre.'id'])
		{
			$name = "<a href='{$this->lat->url}member={$fetch[$pre.'id']}'{$extra}>{$fetch[$pre.'name']}</a>";
		}
		elseif($guest)
		{
			$name = $guest;
		}
		else
		{
			$name = "<a href='{$this->lat->url}member={$fetch[$pre.'id']}'{$extra}>{$fetch[$pre.'name']}</a>";
		}

		return $name;
	}


	// +-------------------------+
	//	 Make Groupname
	// +-------------------------+
	// Allows for global group name display changes

	function make_groupname($id)
	{
		return $this->lat->cache['group'][$id]['name'];
	}


	// +-------------------------+
	//	 Make Signature
	// +-------------------------+
	// Make's a signature for the user

	function make_signature($sig)
	{
		if(!$this->lat->user['hide_sig'] && $sig != "")
		{
			if($this->lat->cache['config']['sig_height'])
			{
				$sig_height = " style=\"max-height: {$this->lat->cache['config']['sig_height']}px\"";
			}

			eval("\$sig =".$this->lat->skin['sig_show']);
		}
		else
		{
			$sig = "";
		}

		return $sig;
	}


	// +-------------------------+
	//	 Parse Online
	// +-------------------------+
	// Parse users online in a section

	function who_online($data)
	{
		if($this->lat->user['id'])
		{
			$raw_stats['active'][] = $this->make_username($this->lat->user);
			$raw_stats['members']++;
		}
		else
		{
			$raw_stats['guests']++;
		}

		$raw_stats['users'] = 1;

		if($this->lat->input['id'] > 0)
		{
			$last_id = "last_id={$this->lat->input['id']} AND ";
		}

		if(is_array($data['pg']))
		{
			$data['pg'] = implode("','", $data['pg']);
		}

		// Query: Get sessions active in this forum
		$query = array("select" => "s.uid, u.name",
		               "from"   => "kernel_session s",
		               "left"   => array("user u ON (s.uid=u.id)"),
		               "where"  => "(s.uid != '{$this->lat->user['id']}' OR s.uid=0) AND s.sid != '{$this->lat->user['sid']}' AND {$last_id}last_pg IN('{$data['pg']}') AND s.spider='' AND s.last_time > ".(time() - ($this->lat->cache['config']['session_length'] * 60)),
		               "order"  => "last_time DESC");

		// Calculate users
		while($fetch = $this->lat->sql->query($query))
		{
			if(!$this->lat->user['id'] || $this->lat->user['id'] != $fetch['uid'])
			{
				$raw_stats['users']++;
				if($fetch['uid'] == $this->lat->user['id'])
				{
					$raw_stats['members']++;
				}
				elseif($fetch['uid'])
				{
					$raw_stats['active'][] = "<a href='{$this->lat->url}profile={$fetch['uid']}'>{$fetch['name']}</a>";
					$raw_stats['members']++;
				}
				else
				{
					$raw_stats['guests']++;
				}
			}
		}

		// More than one person online
		if($raw_stats['users'] == 1)
		{
			$data['on'] = str_replace("<!-- USERS -->", $this->lat->lang['one_user'], $data['on']);
		}
		// We're the only person online
		else
		{
			$data['on'] = str_replace("<!-- USERS -->", str_replace("<!-- NUM -->", intval($raw_stats['users']), $this->lat->lang['multi_user']), $data['on']);
		}

		// Just one user online
		if($raw_stats['members'] == 1)
		{
			$data['on'] = str_replace("<!-- MEMBERS -->", $this->lat->lang['one_member'], $data['on']);
		}
		// Many or no users online
		else
		{
			$data['on'] = str_replace("<!-- MEMBERS -->", str_replace("<!-- NUM -->", intval($raw_stats['members']), $this->lat->lang['multi_member']), $data['on']);
		}

		if($raw_stats['guests'] == 1)
		{
			$data['on'] = str_replace("<!-- GUESTS -->", $this->lat->lang['one_guest'], $data['on']);
		}
		// Many or no guests online
		else
		{
			$data['on'] = str_replace("<!-- GUESTS -->", str_replace("<!-- NUM -->", intval($raw_stats['guests']), $this->lat->lang['multi_guest']), $data['on']);
		}

		// No users online
		if(!empty($raw_stats['active']))
		{
			$data['off'] = implode(", ", $raw_stats['active']);
		}

		eval("\$active_html =".$this->lat->skin['browsing_list']);

		return $active_html;
	}


	// +-------------------------+
	//	 Guest Profile
	// +-------------------------+
	// Makes a guest sidebar entry and returns it

	function guest_profile($data)
	{
		eval("\$profile =".$this->lat->skin['guest_profile']);

		return $profile;
	}


	// +-------------------------+
	//	 User Profile
	// +-------------------------+
	// Makes a user sidebar entry and returns it

	function user_profile($data, $no_avatar=0)
	{
		if(!$no_avatar)
		{
			$data['avatar'] = $this->make_avatar(array("url"    => $data['avatar_url'],
													   "width"  => $data['avatar_width'],
													   "height" => $data['avatar_height'],
													   "type"   => $data['avatar_type']));
		}

		$data['registered'] = $this->make_time("M jS, Y", $data['registered']);

		eval("\$profile =".$this->lat->skin['user_profile']);

		return $profile;
	}


	// +-------------------------+
	//	 Make Bar
	// +-------------------------+
	// Generates a single bar graphic like represtation of data

	function make_bar($data)
	{
		if($data['total'] == 0)
		{
			$width = 1;
		}
		else
		{
			$width = round(($data['value'] / $data['total']) * 100);

			if($width > 100)
			{
				$width = 100;
			}
			elseif($width < 1)
			{
				$width = 1;
			}
		}

		if(!$data['px'])
		{
			$width .= "%";
		}
		else
		{
			$width = $width * $data['px'];
			$width .= "px";
		}

		return "<hr class=\"bar\" align=\"left\" style=\"width: {$width}\" />";
	}

	// +-------------------------+
	//	 Make Bar
	// +-------------------------+
	// Grabs database names for possibily cached values

	function get_name_array($id, $prefix="")
	{
		$id = array_unique($id);

		if(empty($id))
		{
			return;
		}

		$query = array("user"    => array("u ".$prefix),
					   "from"    => "user u",
					   "where"   => "u.id in (".implode(",", $id).")");

		while($user = $this->lat->sql->query($query))
		{
			$u[$user[$prefix.'id']] = $user;
		}

		$this->lat->sql->kill($query);

		return $u;
	}


	// +-------------------------+
	//   Make Thumbnail
	// +-------------------------+
	// Generates a thumbnail from an image

	function make_thumb($data)
	{
		// We have to have GD library
		if(!$this->lat->show->find_gd())
		{
			return false;
		}

		// The size we're trying to change to...
		$new_dim = $this->lat->parse->size_image(array($data['width'], $data['height']), array($data['max_width'], $data['max_height']));
		$thumbnail = imagecreatetruecolor($new_dim[0], $new_dim[1]);

		// Get the file extention
		if($data['input_ext'] == "")
		{
			$data['input_ext'] = strtolower(substr($data['input'], strrpos($data['input'], ".") + 1));
		}
		else
		{
			$data['input_ext'] = strtolower($data['input_ext']);
		}

		// JPG!
		if(($data['input_ext'] == "jpeg" || $data['input_ext'] == "jpg") && function_exists("imagejpeg"))
		{
			$source = imagecreatefromjpeg($data['input']);
			$data['output_ext'] = "jpg";


			if($data['quality'] == "" || $data['quality'] > 8 || $data['quality'] < 0)
			{
				$data['quality'] = 100;
			}
			else
			{
				$data['quality'] = intval($data['quality'] * 11);
			}
		}
		// GIF!
		elseif($data['input_ext'] == "gif" && function_exists("imagegif"))
		{
			$source = imagecreatefromgif($data['input']);
		}
		// PNG!
		elseif($data['input_ext'] == "png" && function_exists("imagepng"))
		{
			imagealphablending($thumbnail, false);
			imagesavealpha($thumbnail, true);
			$source = imagecreatefrompng($data['input']);

			if($data['quality_png'] == "" || $data['quality_png'] > 8 || $data['quality_png'] < 0)
			{
				$data['quality_png'] = 0;
			}
			else
			{
				$data['quality_png'] = intval(($data['quality_png'] - 9) * -1);
			}
		}
		// OMG!
		else
		{
			return false;
		}

		if($data['output_ext'] == "")
		{
			$data['output_ext'] = strtolower(substr($data['output'], strrpos($data['output'], ".") + 1));
		}
		else
		{
			$data['output_ext'] = strtolower($data['input_ext']);
		}

		imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $new_dim[0], $new_dim[1], $data['width'], $data['height']);

		// JPG!
		if(($data['output_ext'] == "jpeg" || $data['output_ext'] == "jpg") && function_exists("imagejpeg"))
		{
			imagejpeg($thumbnail, $data['output'], $data['quality']);
		}
		// GIF!
		elseif($data['output_ext'] == "gif" && function_exists("imagegif"))
		{
			imagegif($thumbnail, $data['output']);
		}
		// PNG!
		elseif($data['output_ext'] == "png" && function_exists("imagepng"))
		{
			imagepng($thumbnail, $data['output'], $data['quality']);
		}
		// OMG!
		else
		{
			return false;
		}

		imagedestroy($source);
		imagedestroy($thumbnail);
		return true;
	}
}
?>
