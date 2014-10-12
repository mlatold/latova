<?php if(!defined('LAT')) die("Access Denied.");

class inc_forum
{
	function initialize()
	{
		$this->lat->core->load_cache("forum_mod");
		$this->lat->core->load_cache("forum");
		$this->lat->core->load_cache("forum_profile");

		if(!$this->lat->user['forum_cutoff'] && $this->lat->user['id'])
		{
			$query = array("update"	  => "user",
						   "set"      => array("forum_cutoff" => time() - 86400),
						   "where"    => "id=".$this->lat->user['id'],
						   "shutdown" => 1);

			$this->lat->sql->query($query);

			$this->lat->user['forum_cutoff'] = time() - 86400;
		}

		if(!$this->lat->user['id'])
		{
			$this->lat->user['forum_cutoff'] = time() - 86400;
		}
	}


	// +-------------------------+
	//	 Check Forums
	// +-------------------------+
	// Checks if inputted forums are actual forums

	function check_forums($forum)
	{
		$forum = array_unique($this->lat->parse->as_array("unsigned_int", $forum));

		if(empty($forum))
		{
			return;
		}

		foreach($forum as $fid)
		{
			if($this->lat->cache['forum'][$fid]['parent'] > 0 && $this->lat->cache['forum'][$fid]['link'] == "")
			{
				$new_forum[] = $fid;
			}
		}
		return $new_forum;
	}


	// +-------------------------+
	//	 Sync Forums
	// +-------------------------+
	// Sync forum statistics and last poster

	function sync_forum($forum)
	{
		$forum = $this->check_forums($forum);

		if(empty($forum))
		{
			return false;
		}

		$forums_done = array();

		foreach($forum as $fid)
		{
			// Query: Count topics
			$query = array("select" => "count(id) as num",
						   "from"   => "topic",
						   "where"  => "hidden=0 AND moved=0 AND fid=".$fid);

			$topic = $this->lat->sql->query($query);

			// Query: Count posts
			$query = array("select" => "count(r.id) as num",
						   "from"   => "topic_reply r",
						   "left"   => array("topic t on (t.id=r.tid)"),
						   "where"  => "t.hidden=0 AND t.moved=0 AND t.fid=".$fid);

			$post = $this->lat->sql->query($query);

			$f = $fid;
			$all_forums = array();

			while($this->lat->cache['forum'][$f]['parent'])
			{
				$all_forums[] = $f;
				$all_forums = array_unique(array_merge($this->generate_subforums($f), $all_forums));

				if(!in_array($all_forums, $forums_done))
				{
					$forums_done[] = $all_forums;

					$query = array("select"  => "t.id, t.title, t.last_id, t.last_time, u.name",
								   "from"    => "topic t",
								   "left"    => array("user u on(t.last_id=u.id)"),
								   "where"   => "t.hidden=0 AND t.moved=0 AND t.fid in (".implode(",", $all_forums).")",
								   "order"   => "t.last_time DESC",
								   "limit"   => 1,
								   "no_save" => 1);

					$last_topic = $this->lat->sql->query($query);

					$set = array("last_topic"      => intval($last_topic['id']),
								 "last_topic_name" => $last_topic['title'],
								 "last_name"       => $last_topic['name'],
								 "last_id"         => intval($last_topic['last_id']),
								 "last_time"       => intval($last_topic['last_time']));

					if($f == $fid)
					{
						$set['last_topic_time'] = intval($last_topic['last_time']);
						$set['topics'] = $topic['num'];
						$set['posts'] = $post['num'];
					}

					// Query: Update forum in the database
					$query = array("update"	=> "forum",
								   "set"    => $set,
								   "where"  => "id=".$f);

					$this->lat->sql->query($query);
				}

				$f = $this->lat->cache['forum'][$f]['parent'];
			}
		}

		$this->lat->sql->cache("forum");
		return true;
	}


	// +-------------------------+
	//	 Sync Topics
	// +-------------------------+
	// Sync topics statistics and last poster

	function sync_topic($topic)
	{
		$topic = array_unique($this->lat->parse->as_array("unsigned_int", $topic));

		if(empty($topic))
		{
			return false;
		}

		foreach($topic as $tid)
		{
			// Query: Count topics
			$query = array("select" => "count(id) as num",
						   "from"   => "topic_reply",
						   "where"  => "hidden=0 AND tid=".$tid);

			$reply = $this->lat->sql->query($query);

			$query = array("select" => "t.fid, r.poster_id, r.poster_time",
						   "from"   => "topic_reply r",
						   "left"   => array("topic t on (r.tid=t.id)"),
						   "where"  => "r.hidden=0 AND r.tid=".$tid,
						   "order"  => "r.poster_time DESC",
						   "limit"  => 1);

			$last_post = $this->lat->sql->query($query);
			$fid[] = $last_post['fid'];

			// Query: Update forum in the database
			$query = array("update"	=> "topic",
						   "set"    => array("posts"     => $reply['num'],
						   					 "last_id"   => $last_post['poster_id'],
											 "last_time" => $last_post['poster_time']),
						   "where"  => "id=".$tid);

			$this->lat->sql->query($query);

		}

		$this->sync_forum($fid);
		return true;
	}


	// +-------------------------+
	//	 Sort Parent
	// +-------------------------+
	// Generate array where forums are sorted by parents with ID

	function sort_parent()
	{
		if(!empty($this->lat->cache['forum']))
		{
			if(empty($this->pforums))
			{
				foreach($this->lat->cache['forum'] as $f)
				{
					$this->pforums[$f['parent']][] = $f['id'];
				}
			}
			return $this->pforums;
		}
	}


	// +-------------------------+
	//	 Nav Forums
	// +-------------------------+
	// Generate forum navigation

	function nav_forums($last_forum)
	{
		if(!$last_forum)
		{
			return;
		}

		// Setup navigation format
		while($last_forum)
		{
			$this->lat->nav_forum[] = array($this->lat->cache['forum'][$last_forum]['name'], "forum=".$this->lat->cache['forum'][$last_forum]['id']);
			$last_forum = $this->lat->cache['forum'][$last_forum]['parent'];
		}

		$this->lat->nav = array_merge((array)$this->lat->nav, (array)array_reverse($this->lat->nav_forum));
	}


	// +-------------------------+
	//	 Get unread
	// +-------------------------+
	// Finds out if a forum should be marked as unread, all because of one of its subforums

	function get_unread($id)
	{
		if(empty($this->uforums))
		{
			$query = array("select" => "f.id, t.last_time",
						   "from"   => "forum f",
						   "left"   => array("topic t on (t.fid=f.id AND t.moved=0 AND t.last_time > {$this->lat->user['forum_cutoff']})",
						   					 "topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
						   "where"  => "t.last_time > r.time OR r.time IS NULL",
						   "group"  => "f.id",
						   "order"  => "t.last_time ASC");

			while($t = $this->lat->sql->query($query))
			{

				if(!$t['last_time'])
				{
					$this->uforums[$t['id']] = false;
				}
				else
				{
					$this->uforums[$t['id']] = true;
				}
			}
		}
		$this->sort_parent();

		return $this->get_unread_forum($id);
	}


	// +-------------------------+
	//	 Get Unread forum
	// +-------------------------+
	// Finds out if a forum is unread

	function get_unread_forum($id)
	{
		$forum_unread = false;

		if(!$this->lat->user['id'])
		{
			return;
		}

		if($this->uforums[$id] && $this->lat->cache['forum'][$id]['last_time'] && !$this->lat->cache['forum'][$id]['link'])
		{
			$forum_unread = true;
		}

		if($this->pforums[$id][0] && !$forum_unread)
		{
			// Start checking subforums
			foreach($this->pforums[$id] as $sub)
			{
				// Looks like there is an unread forum! No need to continue :)
				if($this->get_unread_forum($sub))
				{
					$forum_unread = true;
					break;
				}
			}
		}

		return $forum_unread;
	}


	// +-------------------------+
	//	 Generate Subforums
	// +-------------------------+
	// Take all the subforums of one forum

	function generate_subforums($id)
	{
		$this->sort_parent();

		$return = array($id);

		// Merge all subforums of each forum so we can have em all
		if(!empty($this->pforums[$id]))
		{
			foreach($this->pforums[$id] as $fid)
			{
				$return = array_merge((array)$return, (array)$this->generate_subforums($fid));
			}
		}

		return $return;
	}


	// +-------------------------+
	//	 Load Profile
	// +-------------------------+
	// Load up a permission profile, taking all the highest permissions possible

	function load_moderator($fid, $uid=0, $gid=0)
	{
		if(!$uid)
		{
			$uid = $this->lat->user['id'];
		}

		if(!$gid)
		{
			$gid = $this->lat->user['group']['id'];
		}

		if(!empty($this->lat->cache['forum_mod']))
		{
			foreach($this->lat->cache['forum_mod'] as $mod_profile)
			{
				if(in_array($fid, explode(",", $mod_profile['forums'])))
				{
					$is_mod = false;

					if(in_array($uid, explode(",", $mod_profile['uids'])))
					{
						$is_mod = true;
					}

					if(in_array($gid, explode(",", $mod_profile['groups'])))
					{
						$is_mod = true;
					}

					if($is_mod)
					{
						foreach($mod_profile as $mname => $mval)
						{
							if($mval == 1)
							{
								$pre_mod[$mname] = true;
							}
						}
					}
				}
			}
		}

		return $pre_mod;
	}


	// +-------------------------+
	//	 Load Profile
	// +-------------------------+
	// Load up a permission profile, taking all the highest permissions possible

	function load_profile($fid, $gid=-1)
	{
		if($gid < 0)
		{
			$gid = $this->lat->user['group']['id'];
		}

		// Parse each permission profile
		if(!empty($this->lat->cache['forum_profile']))
		{
			foreach($this->lat->cache['forum_profile'] as $profile)
			{
				if(in_array($fid, explode(",", $profile['forums'])) && in_array($gid, explode(",", $profile['groups'])))
				{
					// Take all higher permissions and set them
					foreach($profile as $pname => $pval)
					{
						if(!in_array($pname, array("forum", "groups", "name", "id")) && $pval)
						{
							$profile_return[$pname] = 1;
						}
					}
				}
			}
		}

		return $profile_return;
	}


	// +-------------------------+
	//	 Generate List
	// +-------------------------+
	// Creates a list for forums

	function generate_list($data)
	{
		if($data['type'] == "checkbox" || $data['type'] == "check" || $data['type'] == "c")
		{
			$data['type'] = "c";
		}
		elseif($data['type'] == "dropdown" || $data['type'] == "drop" || $data['type'] == "d")
		{
			$data['type'] = "d";
		}
		if($data['type'] == "redirect" || $data['type'] == "r")
		{
			$data['type'] = "r";
			$data['name'] = "id";
			$data['no_empty'] = true;
			$data['select_category'] = true;
		}

		// Permissions for every board
		if(!empty($this->lat->cache['forum_profile']))
		{
			foreach($this->lat->cache['forum_profile'] as $profile)
			{
				// Check if our group and forum permissions allow us to see this forum
				$profile['forums'] = explode(",", $profile['forums']);
				$profile['groups'] = explode(",", $profile['groups']);

				if((in_array($this->lat->user['group']['id'], $profile['groups']) && $profile['view_index']) || $this->lat->user['group']['supermod'])
				{
					foreach($profile['forums'] as $fid)
					{
						if($this->lat->cache['forum'][$fid]['link'] == "" && !$data['show_links'])
						{
							$this->permissions[$fid] = 1;
						}
					}
				}
			}
		}

		// Organize forums by parent ID
		if(empty($this->pforums))
		{
			foreach($this->lat->cache['forum'] as $f)
			{
				$this->pforums[$f['parent']][] = $f['id'];
			}
		}

		if(!array_key_exists("prefix", $data))
		{
			$data['prefix'] = "<h2>";
		}

		if(!array_key_exists("suffix", $data))
		{
			$data['suffix'] = "</h2>";
		}

		if(!array_key_exists("name", $data))
		{
			$data['name'] = "forums";
		}

		// Time to create the list
		foreach($this->pforums['0'] as $cat)
		{
			if(($this->permissions[$cat] && !empty($this->pforums[$cat])) || $data['skip_permissions'])
			{
				$option_sel = "";

				// Category forum
				if($data['select_category'])
				{
					if($data['type'] == "c")
					{
						if($data['selected'] === true || in_array($cat, (array)$data['selected']))
						{
							$option_sel = " checked=\"checked\"";
						}

						$option_html .= "<label><input type=\"checkbox\" name=\"{$data['name']}[]\" value=\"{$cat}\"{$option_sel} /> <b>{$this->lat->cache['forum'][$cat]['name']}</b></label><br />";
					}
					else
					{
						if($data['fid'] == $data['selected'])
						{
							$option_sel = " selected=\"selected\"";
						}

						$option_html .= "<option value=\"{$cat}\" style=\"font-weight: bold\"{$option_sel}>{$this->lat->cache['forum'][$cat]['name']}</option>";
					}
				}
				else
				{
					if($data['type'] == "c")
					{
						$option_html .= $data['prefix'].$this->lat->cache['forum'][$cat]['name'].$data['suffix'];
					}
					else
					{
						$option_html .= "<optgroup label=\"{$this->lat->cache['forum'][$cat]['name']}\">";
					}
				}

				// Start makin' options!
				foreach($this->pforums[$cat] as $pfid)
				{
					$data['fid'] = $pfid;
					$data['level'] = 0;
					$option_html .= $this->option_parse($data);
				}

				if($data['type'] != "c" && !$data['select_category'])
				{
					$option_html .= "</optgroup>";
				}
			}
		}

		if(!$option_html)
		{
			return;
		}

		if($data['type'] != "c")
		{
			if($data['type'] == "r")
			{
				$onchange = " onchange=\"redirect(this.options[this.selectedIndex].value, '{$this->lat->url}forum=')\"";
			}

			if(!$data['no_empty'])
			{
				$option_empty = "<option value=\"\"> </option>";
			}

			$option_html = "<select name=\"{$data['name']}\" class=\"form_select\"{$onchange}>{$option_empty}{$option_html}</select>";

			if($data['type'] == "r")
			{
				$option_html = "<form action=\"{$this->lat->url}pg=forum;do=url\" method=\"post\">{$option_html} <input type=\"submit\" class=\"form_button\" value=\"{$this->lat->lang['go']}\" /></form>";
			}
		}

		return $option_html;
	}


	// +-------------------------+
	//	 Option Parse
	// +-------------------------+
	// Parse options from the forum dropdown

	function option_parse($data)
	{
		if($this->permissions[$data['fid']] || $data['skip_permissions'])
		{
			if($data['type'] == "c")
			{
				if($data['selected'] === true || in_array($data['fid'], (array)$data['selected']))
				{
					$option_sel = " checked=\"checked\"";
				}

				// This is OUR forum (if given), which is automatically checked
				if($data['home'] == $data['fid'])
				{
					$option .= str_repeat("&nbsp; &nbsp; &nbsp; ", $data['level'] + 1)."<input type=\"checkbox\" name=\"{$data['name']}[]\" value=\"\" checked=\"checked\" disabled=\"disabled\" /> <i>{$this->lat->cache['forum'][$data['fid']]['name']} {$this->lat->lang['homeforum']}</i><br />";
				}
				// This is some other forum, doesn't matter about the checkin'
				else
				{
					$option .= str_repeat("&nbsp; &nbsp; &nbsp; ", $data['level'] + 1)."<label><input type=\"checkbox\" name=\"{$data['name']}[]\" value=\"{$data['fid']}\"{$option_sel} /> {$this->lat->cache['forum'][$data['fid']]['name']}</label><br />";
				}
			}
			else
			{
				if($data['fid'] == $data['selected'])
				{
					$option_sel = " selected=\"selected\"";
				}

				if($data['select_category'])
				{
					$extra_sp = " &nbsp; &nbsp; ";
				}

				$option .= "<option value=\"{$data['fid']}\"{$option_sel}>{$extra_sp}".str_repeat("&nbsp; &nbsp; ", $data['level'])."&raquo; {$this->lat->cache['forum'][$data['fid']]['name']}</option>";
			}

			$data['level']++;

			if(!empty($this->pforums[$data['fid']]))
			{
				foreach($this->pforums[$data['fid']] as $subf)
				{
					$data['fid'] = $subf;
					$option .= $this->option_parse($data);
				}
			}
		}

		return $option;
	}


	// +-------------------------+
	//	 New Topic
	// +-------------------------+
	// Creates a new topic in whatever forum

	function new_topic($data)
	{
		$data['user_id'] = intval($data['user_id']);

		if(!$data['time'])
		{
			$data['time'] = time();
		}

		if(!$data['user_id'])
		{
			$data['user_name'] = $data['name'];
		}

		if(!empty($data['poll']))
		{
			$poll_on = 1;
		}

		$data['lock'] = intval($data['lock']);
		if($data['lock'] < 0 || $data['lock'] > 2)
		{
			$data['lock'] = 0;
		}


		if($data['stick'] == 0)
		{
			$data['stick'] = "";
		}

		// Query: Insert topic into the database
		$query = array("insert"	 => "topic",
					   "data" => array("fid"         => $data['forum'],
									   "title"       => $data['title'],
									   "start_id"    => $data['user_id'],
									   "start_ip"    => $this->lat->user['ip'],
									   "start_name"  => $data['name'],
									   "start_time"  => $data['time'],
									   "last_id"     => $data['user_id'],
									   "last_name"   => $data['name'],
									   "last_time"   => $data['time'],
									   "locked"      => $data['lock'],
									   "stick"       => $data['stick'],
									   "posts"       => 1,
									   "poll"        => intval($poll_on),
									   "icon"        => intval($data['icon'])));

		$topic_id = $this->lat->sql->query($query);

		if(!empty($data['poll']))
		{
			foreach($data['poll'] as $pollid => $poll)
			{
				$pdata = array("tid"      => $topic_id,
							   "pid"      => $pollid,
							   "question" => $poll['question'],
							   "type"     => intval($poll['type']),
							   "options"  => serialize($poll['options']));

				$insert = $this->lat->sql->parse_insert($pdata);
				$insert_data[] = $insert['data'];
			}

			// Query: Insert link topics into the database
			$query = array("pinsert" => "poll",
						   "name"    => $insert['name'],
						   "data"    => $insert_data);

			$this->lat->sql->query($query);
		}

		$pre_forum = $this->load_profile($data['forum']);

		$post_cached = $this->lat->parse->cache($data['data'], array("bb" => $pre_forum['use_bb'], "smi" => intval($data['smi']), "type" => 2));
//die($this->lat->parse->sql_text($post_cached));
		// Query: Insert the post into the database
		$query = array("insert" => "topic_reply",
					   "data"   => array("tid"          => $topic_id,
										 "data"         => $this->lat->parse->sql_text($data['data']),
										 "data_cached"  => $this->lat->parse->sql_text($post_cached),
										 "poster_id"    => intval($data['user_id']),
										 "poster_name"  => $data['name'],
										 "poster_time"  => $data['time'],
										 "poster_ip"	=> $this->lat->user['ip'],
										 "on_sig"       => intval($data['sig']),
										 "on_smi"       => intval($data['smi'])));

		$this->lat->sql->query($query);

		if($data['user_id'] && $data['increment'])
		{
			// Query: Update the current user id
			$query = array("update"	=> "user",
					       "set"    => array("posts="  => "posts+1",
					       					 "topics=" => "topics+1"),
					       "where"  => "id={$data['user_id']}");

			$this->lat->sql->query($query);
		}

		$this->sync_forum($data['forum']);
		return $topic_id;
	}


	// +-------------------------+
	//	 New Reply
	// +-------------------------+
	// Creates a new reply in whatever forum

	function new_reply($data)
	{
		// Query: Get the very last post and its topic
		$query = array("select" => "r.data, r.poster_id, r.id, t.fid, t.title",
			           "from"   => "topic_reply r",
			           "left"   => array("topic t on(r.tid=t.id)"),
			           "where"  => "r.tid=".$data['topic'],
			           "limit"  => 1,
			           "order"  => "r.poster_time DESC");

		$post_check = $this->lat->sql->query($query);

		// The poster ID matches!
		if($post_check['poster_id'] == $this->lat->user['id'] && $this->lat->user['id'])
		{
			// The post is identical. Nobody makes two identical posts in a row... so lets just go to the end (we assume they pressed the button twice by accident)
			if($data['data'] == $post_check['data'])
			{
				$this->lat->core->redirect($this->lat->url."pg=topic;do=last;id={$data['topic']}");
			}
			// The post is different, so they are double posting. Do not increment post count for this.
			else
			{
				$data['increment'] = false;
			}
		}

		$data['user_id'] = intval($data['user_id']);

		if($data['stick'] === 0 || $data['stick'] === 1)
		{
			$set['stick'] = $data['stick'];
		}

		if($data['lock'] === 0 || $data['lock'] === 1 || $data['lock'] === 2)
		{
			$set['locked'] = $data['lock'];
		}

		if(!empty($set))
		{
			// Query: Update the current topic information
			$query = array("update" => "topic",
				           "set"    => $set,
				           "where"  => "id=".$data['topic']);

			//$this->lat->sql->query($query);
		}

		$pre_forum = $this->load_profile($post_check['fid']);

		$post_cached = $this->lat->parse->cache($data['data'], array("bb" => $pre_forum['use_bb'], "smi"  => intval($data['smi']), "type" => 2));

		// Query: Import reply into the databases
		$query = array("insert"	=> "topic_reply",
					   "data"	=> array("tid"          => $data['topic'],
										 "data"         => $this->lat->parse->sql_text($data['data']),
										 "data_cached"  => $this->lat->parse->sql_text($post_cached),
										 "poster_id"    => $data['user_id'],
										 "poster_name"  => $data['name'],
										 "poster_time"  => time(),
										 "poster_ip"    => $this->lat->user['ip'],
										 "on_sig"	    => $data['sig'],
										 "on_smi"	    => $data['smi']));

		$reply_id = $this->lat->sql->query($query);

		$this->sync_topic($data['topic']);

		if($data['user_id'] && $data['increment'])
		{
			// Query: Increment posts
			$query = array("update"	=> "user",
						   "set" 	=> array("posts=" => "posts+1"),
						   "where"	=> "id=".$data['user_id']);

			$this->lat->sql->query($query);
		}

		return $reply_id;
	}
}
?>
