<?php if(!defined('LAT')) die("Access Denied.");

class module_global
{
	function initialize()
	{
		switch($this->lat->input['do'])
		{
			// Search form
			case "search":
				$this->search();
				break;
			// Search parse
			case "submit_search":
				$this->submit_search();
				break;
			// Search as terms
			case "view_search":
				$this->view_search();
				break;
			// Smilies popup
			case "smilies":
				$this->smilies();
				break;
			// BBtags popup
			case "bbtags":
				$this->bbtags();
				break;
			// Captcha Image
			case "img":
				$this->captcha();
				break;
			// There is no page here!
			default:
				$this->lat->core->error("err_input");
				break;
		}
	}


	// +-------------------------+
	//	 Latova System
	// +-------------------------+
	// Kernel level maintenance and things

	function latova_system($data)
	{
		switch($data['type'])
		{
			case "statistics":
				// Did we hit a new active user limit?
				if($data['user_count'] > $this->lat->cache['storage']['stats_max_users'])
				{
					// Query: Update database with new user count
					$query = array("update"	  => "kernel_storage",
								   "set" 	  => array("data" => $data['user_count']),
								   "where"	  => "label='stats_max_users'");

					$this->lat->sql->query($query);

					// Query: Update database with new time
					$query = array("update"	  => "kernel_storage",
								   "set" 	  => array("data" => time()),
								   "where"	  => "label='stats_max_time'");

					$this->lat->sql->query($query);

					$this->lat->sql->cache("storage");
				}

				// User statistics
				$last = $this->lat->show->get_name_array(array($this->lat->cache['storage']['stats_last_userid'], 0), "user_");
				$this->lat->lang['member_stats'] = str_replace(array("<!-- NUM -->", "<!-- USER -->"), array($this->lat->cache['storage']['stats_users'], $this->lat->show->make_username($last[$this->lat->cache['storage']['stats_last_userid']], "user_")), $this->lat->lang['member_stats']);
				$this->lat->lang['most_users'] = str_replace(array("<!-- USERS -->", "<!-- DATE -->"), array($this->lat->cache['storage']['stats_max_users'], $this->lat->show->make_time($this->lat->user['long_date'], $this->lat->cache['storage']['stats_max_time'], 1)), $this->lat->lang['most_users']);

				return array($this->lat->lang['member_stats'], $this->lat->lang['most_users']);
				break;
			case "reparse":
				$query = array("update" => "kernel_msg",
							   "set"    => array("data_reparse" => 1));

				if($data['user'])
				{
					$query['where'] .= "((sent_from={$data['user']} AND folder='sent') OR (sent_to={$data['user']} AND folder!='sent'))";
				}

				if($data['text'])
				{
					if($query['where'])
					{
						$query['where'] .= " AND ";
					}

					$query['where'] .= "data LIKE '%{$data['text']}%'";
				}

				$this->lat->sql->query($query);

				$query = array("update" => "user_profile",
							   "set"    => array("signature_reparse" => 1));

				if($data['user'])
				{
					$query['where'] .= "uid=".$data['user'];
				}

				if($data['text'])
				{
					if($query['where'])
					{
						$query['where'] .= " AND ";
					}

					$query['where'] .= "signature LIKE '%{$data['text']}%'";
				}

				$this->lat->sql->query($query);
				break;
		}
	}


	// +-------------------------+
	//	 Captcha
	// +-------------------------+
	// Generate the captcha image

	function captcha()
	{
		if($this->lat->cache['config']['gd_version'] == 0)
		{
			@header("HTTP/1.1 200 OK");
			@header("Content-type: image/png");
			@header("Cache-Control: no-store, no-cache, must-revalidate");
			@header("Cache-Control: pre-check=0, post-check=0, max-age=0");
			@header("Expires: 0");
			@header("Pragma: no-cache");
			@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			if(!DEBUG)
			{
				ob_start("ob_gzhandler");
			}
			echo file_get_contents($this->lat->config['STORAGE_PATH']."captcha/num_".substr($this->lat->user['captcha'], $this->lat->get_input->ranged_int("n", array(1, 5)) - 1, 1).".png");
			ob_end_flush();

			exit();
		}
		else
		{
			// Create the image
			$im = imagecreatetruecolor(250, 50);
			$back = imagecreatefrompng ($this->lat->config['STORAGE_PATH']."captcha/".mt_rand(1, 7).".png");
			imagecopy ($im, $back, 0, 0, 0, 0, 250, 50);

			// Random Arcs
			for($i=0; $i<15; $i++)
			{
				if($i % 2)
				{
					$ang1 = mt_rand(270, 330);
					$ang2 = mt_rand(30, 90);
				}
				else
				{
					$ang1 = mt_rand(30, 90);
					$ang2 = mt_rand(270, 330);
				}

				imagearc($im, mt_rand(50, 250), mt_rand(10, 40), mt_rand(100, 200), mt_rand(10, 40), $ang1, $ang2, imagecolorallocate($im, mt_rand(140, 200), mt_rand(140, 200), mt_rand(130, 255)));
			}

			// Add random text
			for($i=1; $i<6; $i++)
			{
				$letter = mt_rand(1, 60);

				// These letters can be confused, so remove them
				if(in_array($letter, array(49, 46, 24, 18)))
				{
					$letter--;
				}

				if($letter > 35)
				{
					$letter = chr($letter + 62);
				}
				elseif($letter > 9)
				{
					$letter = chr($letter + 55);
				}

				$this->lat->session->captcha .= $letter;
				imagettftext($im, mt_rand(17, 20), mt_rand(-50, 50), (mt_rand(28, 30) * $i * 1.6) - 20, mt_rand(25, 35), imagecolorallocate($im, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150)), $this->lat->config['STORAGE_PATH']."captcha/vera.ttf", $letter);
			}

			// Do not cache the image
			@header("HTTP/1.1 200 OK");
			@header("Content-type: image/png");
			@header("Cache-Control: no-store, no-cache, must-revalidate");
			@header("Cache-Control: pre-check=0, post-check=0, max-age=0");
			@header("Expires: 0");
			@header("Pragma: no-cache");
			@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			imagepng($im);
			$this->lat->session->captcha = strtoupper($this->lat->session->captcha);
			$this->lat->session->no_location = true;
			$this->lat->session->update_session();
			$this->lat->sql->exec_shutdown();
			exit();
		}
	}


	// +-------------------------+
	//	 Smilies Popup
	// +-------------------------+
	// Give us all our smilies

	function smilies()
	{
		$this->lat->show->no_layout = true;
		$this->lat->session->no_update = true;

		// Load each smiley into its own row if it has permission
		foreach($this->lat->cache['icon'] as $smilie)
		{
			if($smilie['is_post'])
			{
				eval("\$smilies .=".$this->lat->skin['smilies_pop_row']);
			}
		}

		eval("\$this->lat->output =".$this->lat->skin['smilies_pop']);
	}


	// +-------------------------+
	//	 BBtags
	// +-------------------------+
	// Give us all our bbtags

	function bbtags()
	{
        $this->lat->core->load_cache("autoparse");
		$this->lat->show->no_layout = true;
		$this->lat->session->no_update = true;
		$this->lat->get_input->unsigned_int("type");

		// Load each smiley into its own row if it has permission
		if(!empty($this->lat->cache['autoparse']))
		{
			foreach($this->lat->cache['autoparse'] as $ap)
			{
				if(!$ap['type'])
				{
					$image[] = $ap['data'];
				}
				else
				{
					$video[] = "<a href=\"http://www.{$ap['site']}\" target=\"_blank\">{$ap['site']}</a>";
				}
			}
		}

		$this->lat->parse->bb_profile = $this->lat->parse->load_profile($this->lat->input['type']);
		$this->lat->parse->type = $this->lat->input['type'];

		foreach($this->lat->cache['bbtag'] as $bbtag)
		{
			if($bbtag['opt'] != 1)
			{
				if($bbtag['example'] == "")
				{
					$bbtag['example'] = $this->lat->lang['bb_example'];
				}

				$example = "[{$bbtag['tag']}]{$bbtag['example']}[/{$bbtag['tag']}]";
				$result = $this->lat->parse->bbtag($bbtag['id'], addslashes($bbtag['example']), "", true);

				eval("\$bbtags .=".$this->lat->skin['bbtags_pop_row']);
			}

			if($bbtag['opt'] != 0)
			{
				if($bbtag['example_opt2'] == "")
				{
					$bbtag['example_opt2'] = $this->lat->lang['bb_example_opt'];
				}

				if($bbtag['example_opt'] == "")
				{
					$bbtag['example_opt'] = $this->lat->lang['bb_example'];
				}

				$example = "[{$bbtag['tag']}={$bbtag['example_opt2']}]{$bbtag['example_opt']}[/{$bbtag['tag']}]";
				$result = $this->lat->parse->bbtag($bbtag['id'], addslashes($bbtag['example_opt']), addslashes($bbtag['example_opt2']), true);

				eval("\$bbtags .=".$this->lat->skin['bbtags_pop_row']);
			}
		}

		$this->lat->lang['about_autoparse'] = str_replace("<!-- IMAGE EXT -->", implode(", ", $image), $this->lat->lang['about_autoparse']);
		$this->lat->lang['about_autoparse'] = str_replace("<!-- MEDIA EXT -->", implode(", ", $video), $this->lat->lang['about_autoparse']);
		$this->lat->lang['about_autoparse'] = str_replace("<!-- IMAGE LIMIT -->", $this->lat->parse->number($this->lat->parse->bb_profile['img']), $this->lat->lang['about_autoparse']);
		$this->lat->lang['about_autoparse'] = str_replace("<!-- MEDIA LIMIT -->", $this->lat->parse->number($this->lat->parse->bb_profile['mda']), $this->lat->lang['about_autoparse']);
		eval("\$this->lat->output =".$this->lat->skin['bbtags_pop']);
	}


	// +-------------------------+
	//	 Search
	// +-------------------------+
	// Open up a search page

	function search($error="")
	{
		$this->lat->title = $this->lat->lang['search'];
		$this->lat->nav[] = $this->lat->lang['search'];
		$first = " class=\"first\"";
		$this->lat->get_input->no_text("p");

		foreach($this->lat->cache['page'] as $page)
		{
			if($page['can_search'])
			{
				if($this->lat->input['p'] == "" || !$this->lat->cache['page'][$this->lat->input['p']]['can_search'])
				{
					$this->lat->input['p'] = $page['name'];
				}

				if($this->lat->input['p'] == $page['name'])
				{
					$html = $this->lat->lang['s_'.$page['name']];
				}
				else
				{
					$html = "<a href=\"{$this->lat->url}pg=global;do=search;p={$page['name']}\">{$this->lat->lang['s_'.$page['name']]}</a>";
				}
				$html = "<li{$first}>{$html}</li>";
				$first = "";

				$search_pages .= $html;
			}
		}

		$pg = $this->lat->input['p'];
		$this->lat->core->load_module($pg);
		$this->lat->module->$pg->latova_system(array("type" => "search_form"));

		eval("\$this->lat->output =".$this->lat->skin['search']);
	}


	// +-------------------------+
	//	 Submit Search
	// +-------------------------+
	// Parses a search query

	function submit_search()
	{
		$pg = $this->lat->get_input->no_text("p");

		// Use quick search settings
		if($this->lat->get_input->whitelist("quick", array(0, 1)))
		{
			$this->lat->get_input->no_text("quick_type");
			$this->lat->raw_input['p'] = substr($this->lat->input['quick_type'], 1);
			$pg = $this->lat->get_input->no_text("p");
			$type = substr($this->lat->input['quick_type'], 0, 1);

			if(!$this->lat->cache['page'][$pg]['can_search'])
			{
				$this->lat->core->error("err_input");
			}

			$this->lat->core->load_module($pg);
			$this->lat->module->$pg->latova_system(array("type" => "search_quick", "search_type" => $type));
		}

		if($this->lat->get_input->no_text("usr") != "")
		{
			$query = array("select" => "u.id",
						   "from"   => "user u",
						   "where"  => "u.name='{$this->lat->input['usr']}'");

			if($this->lat->sql->num($query))
			{
				$user = $this->lat->sql->query($query);
			}
			else
			{
				$this->lat->core->error("err_no_name");
			}
		}

		$this->lat->core->redirect($this->lat->url.$this->url_search($user['id']));
	}


	// +-------------------------+
	//	 View Search
	// +-------------------------+
	// Gets results!

	function view_search($usr=0)
	{
		if($this->lat->raw_input['search_user'])
		{
			$this->lat->raw_input['usr'] = $this->lat->raw_input['search_user'];
		}
		$this->lat->raw_input['terms'] = $this->lat->raw_input['search'];

		$pg = $this->lat->get_input->no_text("p");
		$this->lat->get_input->no_text("search");
		$this->lat->get_input->unsigned_int("usr");
		$this->lat->core->load_module($pg);

		$this->lat->search_url = $this->url_search($this->lat->input['usr']);
		$shash = $this->lat->parse->hash($this->lat->search_url);

		$query = array("select" => "s.shash",
					   "from"   => "kernel_search s",
					   "where"  => "s.ip='{$this->lat->user['ip']}' AND time > ".(time() - $this->lat->cache['config']['search_time']));

		if($this->lat->sql->num($query) > $this->lat->cache['config']['search_num'])
		{
			$this->lat->core->error("err_search_flood");
		}

		$query = array("delete"   => "kernel_search",
					   "where"    => "time < ".(time() - 1800),
					   "shutdown" => 1);

		$this->lat->sql->query($query);

		$query = array("select" => "s.*",
					   "from"   => "kernel_search s",
					   "where"  => "s.gid={$this->lat->user['gid']} AND shash='{$shash}' AND pg='{$this->lat->input['p']}'");

		if($this->lat->sql->num($query))
		{
			$result = $this->lat->sql->query($query);
		}
		else
		{
			$s_exec = $this->lat->module->$pg->latova_system(array("type" => "search_execute"));

			if(empty($s_exec))
			{
				$this->lat->core->error("err_no_search");
			}

			$result['shash'] = $shash;
			$result['gid'] = $this->lat->user['gid'];
			$result['ip'] = $this->lat->user['ip'];
			$result['pg'] = $this->lat->input['p'];
			$result['time'] = time();
			$result['content'] = implode(",", $s_exec);

			$query = array("insert"	 => "kernel_search",
						   "data" => array("shash"   => $shash,
										   "gid"     => $this->lat->user['gid'],
										   "ip"      => $this->lat->user['ip'],
										   "pg"      => $this->lat->input['p'],
										   "time"    => time(),
										   "content" => implode(",", $s_exec)),
						   "shutdown" => 1);

			$this->lat->sql->query($query);
		}

		$this->lat->module->$pg->latova_system(array("type" => "search_view", "result" => $result));
	}

	function url_search($usr_id)
	{
		$pg = $this->lat->get_input->no_text("p");

		if(!$this->lat->cache['page'][$pg]['can_search'] || $this->lat->parse->get_length($this->lat->input['terms']) > 255)
		{
			$this->lat->core->error("err_input");
		}

		$this->lat->get_input->no_text("usr");
		$this->lat->get_input->no_text("terms");

		// No search query
		if($this->lat->input['terms'] == "" && $this->lat->input['usr'] == "")
		{
			$this->lat->core->error("err_search");
		}

		if($this->lat->input['terms'] != "")
		{
			$terms = str_replace(array("+", "-", "<", ">", "(", ")", "~", "*", "\""), "", $this->lat->raw_input['terms']);
			$terms = explode(" ", preg_replace("{\s+}", " ", $terms));

			foreach($terms as $t)
			{
				if(strlen($t) < 4)
				{
					$this->lat->core->error("err_search_short");
				}
			}

			$redirect = "search=".urlencode($this->lat->input['terms']);
		}

		if($redirect != "" && $usr_id > 0)
		{
			$redirect .= ";usr=".$usr_id;
		}
		elseif($redirect == "")
		{
			$redirect = "search_user=".$usr_id;
		}

		$redirect .= ";p=".$this->lat->input['p'];
		$this->lat->core->load_module($pg);
		$url = $this->lat->module->$pg->latova_system(array("type" => "search_url"));

		if($this->lat->input['terms'] != "")

		if(!empty($url))
		{
			foreach($url as $n => $u)
			{
				$redirect .= ";{$n}={$u}";
			}
		}

		return $redirect;
	}
}
?>
