<?php if(!defined('LAT')) die("Access Denied.");

class module_member
{
	function initialize()
	{
		$this->lat->core->load_cache("setting");

		switch($this->lat->input['do'])
		{
			case "profile":
				$this->profile();
				break;
			case "online":
				$this->online();
				break;
			case "history":
				$this->history();
				break;
			default:
				$this->member_list();
				break;
		}
	}


	// +-------------------------+
	//	 Name History
	// +-------------------------+
	// Show the name history

	function history()
	{
		$this->lat->show->no_layout = true;
		$this->lat->session->no_update = true;

		$query = array("select" => "h.*",
					   "from"   => "user_history h",
					   "where"  => "h.uid={$this->lat->input['id']}",
					   "order"  => "time DESC");

		// Load each smiley into its own row if it has permission
		while($nh = $this->lat->sql->query($query))
		{
			if(!$nh['time'])
			{
				$time = $this->lat->lang['registration'];
			}
			else
			{
				$time = $this->lat->show->make_time($this->lat->user['long_date'], $nh['time']);
			}

			eval("\$name .=".$this->lat->skin['name_history_row']);
		}

		eval("\$this->lat->output =".$this->lat->skin['name_history']);
	}


	// +-------------------------+
	//	 Profile
	// +-------------------------+
	// View default page of profile

	function profile()
	{
		// Check incoming user ID
		$this->lat->input['id'] = intval($this->lat->input['id']);

		if($this->lat->input['id'] < 1)
		{
			$this->lat->show->error("err_input");
		}

		// Query: Fetch the user and profile information
		$query = array("select" => "p.*, u.*",
					   "from"   => "user u",
					   "left"   => "user_profile p on(u.id=p.uid)",
					   "where"  => "u.id=".$this->lat->input['id']);

		$profile = $this->lat->sql->query($query);

		// Couldn't find the user
		if(!$this->lat->sql->num())
		{
			$this->lat->show->error("err_no_user");
		}

		$this->lat->title = $this->lat->lang['viewing_profile'].$profile['name'];
		$this->lat->content = $profile['name'];
		$this->lat->nav[] = $this->lat->lang['viewing_profile'].$profile['name'];

		// Get the avatar and photo information
		$profile['avatar'] = $this->lat->show->make_avatar(array("url"    => $profile['avatar_url'],
																 "width"  => $profile['avatar_width'],
																 "height" => $profile['avatar_height'],
																 "type"   => $profile['avatar_type']));

		$profile['photo'] = $this->lat->show->make_photo(array("url"    => $profile['photo_url'],
															   "width"  => $profile['photo_width'],
															   "height" => $profile['photo_height'],
															   "type"   => $profile['photo_type']));

		if(!$profile['photo'])
		{
			$profile['photo'] = $this->lat->lang['no_photo'];
		}

		// Generate Times
		$days_passed = ceil((time() - $profile['registered']) / 86400);

		$profile['registered'] = $this->lat->show->make_time($this->lat->user['long_date'], $profile['registered']);
		$profile['last_login'] = $this->lat->show->make_time($this->lat->user['long_date'], $profile['last_login']);

		$content_data_links = explode(",", $this->lat->cache['storage']['profile_content_links']);
		$content_data_stats = explode(",", $this->lat->cache['storage']['profile_content_stats']);

		// Generate content links
		if(!empty($content_data_links))
		{
			foreach($content_data_links as $cdl)
			{
				if($this->lat->lang['conl_'.$cdl])
				{
					$content['links'][] = "&middot; <a href=\"{$this->lat->url}".str_replace("<!-- USER -->", $this->lat->input['id'], $this->lat->lang['conl_'.$cdl])."</a>";
				}
			}
			$content['links'] = implode("<br />", $content['links']);
		}

		if(!empty($content_data_stats))
		{
			foreach($content_data_stats as $cds)
			{
				if($this->lat->lang['cons_'.$cds])
				{
					$content['stats'][] = "<b>{$this->lat->lang['cons_'.$cds]}</b>{$profile[$cds]}".str_replace("<!-- NUM -->", $this->lat->parse->number(($profile[$cds] / $days_passed), 2), $this->lat->lang['per_day']);
				}
			}

			$content['stats'] = implode("<br />", $content['stats']);

			if($content['stats'])
			{
				$content['stats'] = "<br />".$content['stats'];
			}
		}

		$this->lat->lang['send_pm'] = str_replace("<!-- NAME -->", $profile['name'], $this->lat->lang['send_pm']);

		$this->lat->parse->recache(array("fetch" => &$profile,
										 "item"  => "signature",
										 "table" => "user_profile",
										 "where" => "uid=".$profile['id'],
										 "gid"   => $profile['gid']));


		// Generate Signature
		if($profile['signature_cached'])
		{
			if($this->lat->cache['config']['sig_height'])
			{
				$profile['signature_cached'] = "<div style='width: 100%; max-height: {$this->lat->cache['config']['sig_height']}px; overflow: auto;'>{$profile['signature_cached']}</div>";
			}

			eval("\$profile['signature_cached'] =".$this->lat->skin['sig']);
		}

		// Custom profile settings
		foreach($profile as $pname => $pval)
		{
			if(substr($pname, 0, 8) == "profile_")
			{
				// This is an IM
				if($this->lat->cache['setting'][$pname]['im'] && $pval)
				{
					$im = substr($pname, 8);

					eval("\$profile['im'] .=".$this->lat->skin['im']);
				}
				// This is a normal setting
				elseif($this->lat->cache['setting'][$pname]['in_pro'])
				{
					if($this->lat->cache['setting'][$pname]['type'] == 1)
					{
						$option = explode("\n", $this->lat->cache['setting'][$pname]['content']);

						foreach($option as $optval)
						{
							$optval = explode("|", $optval);
							if($optval[0] == $pval)
							{
								$pval = $optval[1];
							}
						}
					}
					elseif($this->lat->cache['setting'][$pname]['check'] == 3 && $pval)
					{
						$pval = "<a href=\"{$pval}\">{$pval}</a>";
					}

					if(!$pval)
					{
						$pval = $this->lat->lang['no_info'];
					}

					eval("\$profile['data'] .=".$this->lat->skin['other_row']);
				}
			}
		}

		if($profile['data'])
		{
			eval("\$profile['data'] =".$this->lat->skin['other']);
		}

		$query = array("select" => "count(h.uid) as num",
					   "from"   => "user_history h",
					   "where"  => "h.uid={$this->lat->input['id']}");

		$used = $this->lat->sql->query($query);
		$name_changes = $this->lat->parse->unsigned_int($used['num'] - 1);

		if($used['num'] > 0)
		{
			$name_history = "<a href=\"javascript:view_name();\">".$this->lat->lang['view_history']."</a>";
		}

		eval("\$this->lat->output .=".$this->lat->skin['profile']);
	}


	// +-------------------------+
	//	 Online List
	// +-------------------------+
	// A detailed list of every user which is online

	function online()
	{
		$this->lat->title = $this->lat->lang['online_list'];
		$this->lat->nav[] = $this->lat->lang['online_list'];

		$content = 30;
		$this->lat->get_input->unsigned_int("st");

		// Query: Get number of valid spiderless sessions
		$query = array("select" => "count(sid) as num",
					   "from"   => "kernel_session",
					   "where"  => "spider=''");

		$sess = $this->lat->sql->query($query);

		$arr_page = array("total"   =>  $sess['num'],
		                  "cap"     => $content,
		                  "links"   => 4,
		                  "url"     => $this->lat->url."pg=member;do=online");

		$pages = $this->lat->show->make_pages($arr_page);

		if(!$this->lat->input['st'])
		{
			$sfetch['name'] = $this->lat->show->make_username($this->lat->user, "", $this->lat->lang['guest']);

			if($this->lat->user['id'])
			{
				$sfetch['last_time'] = $this->lat->show->make_time("[]", time());
				$location = $this->lat->lang['on_member-online'];
			}
			else
			{
				$sfetch['last_time'] = $this->lat->show->make_time("[]", time());
				$location = $this->lat->lang['on_member-online'];
			}

			eval("\$session_rows .=".$this->lat->skin['online_row']);
		}

		// Query: Get valid sessions
		$query = array("select" => "s.sid, s.last_time, s.last_pg, s.last_do, s.last_id, s.last_cn",
					   "user"   => "u user_",
					   "from"   => "kernel_session s",
					   "left"   => "user u ON (s.uid=u.id)",
					   "where"  => "s.spider='' AND last_time > ".(time() - ($this->lat->cache['config']['session_length'] * 60)),
					   "limit"  => $this->lat->input['st'].",".$content,
					   "order"  => "last_time DESC");

		while($sfetch = $this->lat->sql->query($query))
		{
			if($sfetch['sid'] != $this->lat->user['sid'])
			{
				$sfetch['name'] = $this->lat->show->make_username($sfetch, "user_", $this->lat->lang['guest']);

				// Just return the relative time... sessions are too short for anything else...
				$sfetch['last_time'] = $this->lat->show->make_time("[]", $sfetch['last_time']);

				// Let's try to see where our user is at
				$location = $this->lat->lang['on_'.$sfetch['last_pg'].'-'.$sfetch['last_do']];

				// Can't find anywhere, use the generic page
				if($location == "")
				{
					$location = $this->lat->lang['on_unknown'];
				}
				// Output where they are on our website
				else
				{
					$location = str_replace(array("<!-- ID -->", "<!-- URL -->", "<!-- EXTRA -->"), array($sfetch['last_id'], $this->lat->url, $sfetch['last_cn']), $location);
				}

				eval("\$session_rows .=".$this->lat->skin['online_row']);
			}
		}

		eval("\$this->lat->output .=".$this->lat->skin['online_list']);
	}


	// +-------------------------+
	//	 User List
	// +-------------------------+
	// Generates a list of users

	function member_list()
	{
		$this->lat->title = $this->lat->lang['member_list'];
		$this->lat->nav[] = $this->lat->lang['member_list'];
		$this->lat->cache['storage']['user_list_rows'] = unserialize($this->lat->cache['storage']['user_list_rows']);
		$this->lat->get_input->unsigned_int("st");

		foreach($this->lat->cache['storage']['user_list_rows'] as $row => $width)
		{
			$userhead .= <<<HTML

		<th width="{$width}%">
			{$this->lat->lang['userl_'.$row]}
		</th>
HTML;
		}

		$this->lat->get_input->preg_whitelist("sort", "A-Za-z_");

		if(in_array($this->lat->input['sort'], explode(",", $this->lat->cache['storage']['user_list_order'])))
		{
			$order = $this->lat->input['sort']." DESC";
			$sort_url = ";sort={$this->lat->input['sort']}";
		}
		else
		{
			$order = "name ASC";
		}

		$content = 30;

		// Query: Get all of the users
		$query = array("select" => "u.*, p.*",
					   "from"   => "user u",
					   "left"   => array("user_profile p ON (p.uid=u.id)"),
					   "order"  => $order,
					   "limit"  => $this->lat->input['st'].",".$content);

		while($user = $this->lat->sql->query($query))
		{
			$cells = "";
			$num = 1;
			$user['gid'] = $this->lat->cache['group'][$user['gid']]['name'];

			foreach($this->lat->cache['storage']['user_list_rows'] as $row => $width)
			{
				if($row != "username")
				{
					if($num == 1)
					{
						$num = 2;
					}
					else
					{
						$num = 1;
					}

					$cells .= <<<HTML

		<td class="cell_{$num}" align="center">
			<span class="text">{$user[$row]}</span>
		</td>
HTML;
				}
			}

			$user['name'] = "<a href=\"{$this->lat->url}member={$user['id']}\">{$user['name']}</a>";

			$userhtml .= <<<HTML
	<tr>
		<td class="cell_1_first">
			{$user['name']}
		</td>{$cells}
	</tr>
HTML;
		}

		// Query: Get total users from the database
		$query = array("select" => "count(u.id) as num",
					"from"   => "user u");

		$u = $this->lat->sql->query($query);

		$arr_page = array("total"   => $u['num'],
		                  "cap"     => $content,
		                  "links"   => 4,
		                  "url"     => $this->lat->url."pg=member".$sort_url);

		$pages = $this->lat->show->make_pages($arr_page);

		$this->lat->output = <<<HTML
<div class="bdr">
	<h1>{$this->lat->lang['member_list']}</h1>
	<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
		<tr>{$userhead}
		</tr>{$userhtml}
	</table>
</div>
{$pages}
HTML;
	}
}
?>