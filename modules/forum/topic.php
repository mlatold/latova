<?php if(!defined('LAT')) die("Access Denied.");

class module_topic
{
	function initialize()
	{
		$this->lat->core->load_class("forum", "moderate");
		$this->lat->nav[] = array($this->lat->lang['forum_list'], "pg=forum");
		$this->lat->show->default_search = "topic_1";

		switch($this->lat->input['do'])
		{
			// Get Last Post
			case "last":
				$this->last_post();
				break;
			// Find Specific Post
			case "find":
				$this->find_post();
				break;
			// Get Last Unread
			case "unread":
				$this->unread();
				break;
			// Moderate
			case "moderate":
				$this->moderate();
				break;
			// Delete a single post
			case "delete_post":
				$this->delete_post();
				break;
			// Render Topic
			default:
				$this->view_topic();
				break;
		}

		$nav = $this->lat->inc->forum->generate_list(array("type" => "redirect", "selected" => $this->forum_id));
		$nav_lang = $this->lat->lang['jump_to_forum'];
		eval("\$this->lat->output .= ".$this->lat->skin['footer_nav']);
	}


	// +-------------------------+
	//	Poll
	// +-------------------------+
	// Vote or show results in a poll!

	function poll()
	{
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		// Query: Get the topic for permission verification and such
		$query = array("select" => "t.poll_votes, t.fid, t.poll, count(tid) as voted",
					   "from"   => "topic t",
					   "left"   => array("poll_vote v on(v.tid=t.id AND (v.ip='{$this->lat->user['ip']}' OR v.uid={$this->lat->user['id']}))"),
					   "where"  => "t.id=".$this->lat->get_input->unsigned_int("tid"),
					   "group"  => "t.poll");

		$topic = $this->lat->sql->query($query);

		if(!$this->lat->sql->num())
		{
			$this->lat->core->error("err_no_topic");
		}

		// Check permissions
		$pre_forum = $this->lat->inc->forum->load_profile($topic['fid']);

		if(!$pre_forum['view_index'] && !$pre_forum['view_topics'] && !$pre_forum['view_posts'])
		{
			$this->lat->core->error("err_no_topic");
		}

		if(!$pre_forum['view_posts'])
		{
			$this->lat->core->error("errpreposts");
		}

		if($topic['voted'])
		{
			$this->lat->core->error("errvoted");
		}

		if(!$topic['poll'])
		{
			$this->lat->core->error("err_input");
		}

		if($this->lat->raw_input['vote'])
		{
			// Query: Get the poll and vote data from the database
			$query = array("select" => "p.id, p.pid, p.question, p.options, p.type",
						   "from"   => "poll p",
						   "where"  => "p.tid=".$this->lat->input['tid']);

			while($poll = $this->lat->sql->query($query))
			{
				$poll['options'] = unserialize($poll['options']);

				if($poll['type'] == 1)
				{
					$numopt = count($poll['options']) + 1;
					for($i=1; $i != $numopt; $i++)
					{
						if($this->lat->raw_input['poll-'.$poll['pid'].'-'.$i])
						{
							$poll['options'][$i][1]++;

							// Insert a new anti-vote record
							$pdata = array("tid" => $this->lat->input['tid'],
										   "pid" => $poll['id'],
										   "uid" => $this->lat->user['id'],
										   "ip"  => $this->lat->user['ip'],
										   "opt" => $i);

							$insert = $this->lat->sql->parse_insert($pdata);
							$insert_data[] = $insert['data'];
						}
					}

					if(empty($insert_data))
					{
						$pdata = array("tid" => $this->lat->input['tid'],
									   "pid" => $poll['id'],
									   "uid" => $this->lat->user['id'],
									   "ip"  => $this->lat->user['ip'],
									   "opt" => 0);

						$insert = $this->lat->sql->parse_insert($pdata);
						$insert_data[] = $insert['data'];
					}

				}
				else
				{
					$this->lat->get_input->unsigned_int("poll-".$poll['pid']);
					if(!$this->lat->input['poll-'.$poll['pid']] || empty($poll['options'][$this->lat->input['poll-'.$poll['pid']]]))
					{
						$this->lat->core->error("errnovote");
					}

					$poll['options'][$this->lat->input['poll-'.$poll['pid']]][1]++;

					// Insert a new anti-vote record
					$pdata = array("tid" => $this->lat->input['tid'],
								   "pid" => $poll['id'],
								   "uid" => $this->lat->user['id'],
								   "ip"  => $this->lat->user['ip'],
								   "opt" => $this->lat->input['poll-'.$poll['pid']]);

					$insert = $this->lat->sql->parse_insert($pdata);
					$insert_data[] = $insert['data'];
				}

				// Query: Update the poll
				$polla = array("update" => "poll",
							   "set"    => array("options" => serialize($poll['options'])),
							   "where"  => "id=".$poll['id']);

				$this->lat->sql->query($polla);
			}


			// Query: Update number of votes
			$query = array("update" => "topic",
						   "set"    => array("poll_votes=" => "poll_votes+1"),
						   "where"  => "id=".$this->lat->input['tid']);

			$this->lat->sql->query($query);
		}
		else
		{
			// Insert a null vote record
			$pdata = array("tid" => $this->lat->input['tid'],
						   "uid" => $this->lat->user['id'],
						   "ip"  => $this->lat->user['ip']);

			$insert = $this->lat->sql->parse_insert($pdata);
			$insert_data[] = $insert['data'];
		}

		// Query: Insert link topics into the database
		$query = array("pinsert" => "poll_vote",
					   "name"    => $insert['name'],
					   "data"    => $insert_data);

		$this->lat->sql->query($query);

		if($this->lat->input['st'] != "")
		{
			$extra_url .= ";st=".$this->lat->input['st'];
		}

		$this->lat->core->redirect($this->lat->url."topic={$this->lat->input['tid']}".$extra_url, "action=vote");
	}


	// +-------------------------+
	//	 Forum Moderate
	// +-------------------------+
	// Forum array moderation selection

	function moderate()
	{
		$this->lat->core->check_key();

		if($this->lat->raw_input['go_1'])
		{
			$this->lat->raw_input['mod'] = $this->lat->raw_input['mod_1'];
		}
		elseif($this->lat->raw_input['go_2'])
		{
			$this->lat->raw_input['mod'] = $this->lat->raw_input['mod_2'];
		}

		$this->lat->get_input->preg_whitelist("mod", "a-z_");

		if($this->lat->raw_input['vote'] || $this->lat->raw_input['show'])
		{
			$this->poll();
		}

		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_moderator_privileges");
		}

		if(!is_array($this->lat->raw_input['item']))
		{
			$this->lat->raw_input['item'] = @explode(",", $this->lat->raw_input['item']);
		}

		$this->lat->get_input->as_array("unsigned_int", "item");

		if(empty($this->lat->input['item']) && substr($this->lat->input['mod'], 0, 1) == "p")
		{
			$this->lat->input['item'] = explode("|", $this->lat->session->get_cookie("sel_posts"));
		}

		if($this->lat->get_input->unsigned_int("tid") && substr($this->lat->input['mod'], 0, 1) == "t")
		{
			$this->lat->input['item'] = array($this->lat->input['tid']);
		}

		if(empty($this->lat->input['item']))
		{
			$this->lat->core->error("err_no_item_moderate");
		}

		if(substr($this->lat->input['mod'], 0, 1) == "t")
		{
			$query = array("select" => "t.id, t.fid, t.start_id, t.locked",
						   "from"	=> "topic t",
						   "where"  => "t.id in (".implode(",", $this->lat->input['item']).")");

			while($tcheck = $this->lat->sql->query($query))
			{
				if($tcheck['start_id'] != $this->lat->user['id'])
				{
					$not_user = true;
				}

				if(!$this->lat->input['id'])
				{
					$this->lat->input['id'] = $tcheck['fid'];
				}

				if($tcheck['fid'] == $this->lat->input['id'])
				{
					$item_safe[] = $tcheck['id'];
				}

				if($tcheck['locked'] == 2)
				{
					$cant_unlock = true;
				}
			}

			$this->forum_id = $this->lat->input['id'];
		}
		else
		{
			$query = array("select" => "r.id, r.poster_id, t.fid",
						   "from"	=> "topic_reply r",
						   "left"   => array("topic t on (r.tid=t.id)"),
						   "where"  => "r.id in (".implode(",", $this->lat->input['item']).") AND r.tid={$this->lat->input['tid']}");

			while($pcheck = $this->lat->sql->query($query))
			{
				if($pcheck['poster_id'] != $this->lat->user['id'])
				{
					$not_user = true;
				}

				$item_safe[] = $pcheck['id'];
				$fid = $pcheck['fid'];
			}
		}

		if(empty($item_safe))
		{
			$this->lat->core->error("err_no_item_moderate");
		}

		$mod['id'] = $item_safe;
		$mod['no_clean'] = true;
		$profile = $this->lat->inc->forum->load_profile($this->lat->input['id']);
		$this->lat->get_input->unsigned_int("st");

		// Which moderator page to load up?
		switch($this->lat->input['mod'])
		{
			case "tstick":
				$mod['type'] = "stick";
				$mod['perm'] = "sticky_topics";

				if(!$this->lat->user['group']['supermod'])
				{
					$mod['extra'] = " AND stick=''";
				}
				break;
			case "tunstick":
				$mod['type'] = "unstick";
				$mod['perm'] = "sticky_topics";

				if(!$this->lat->user['group']['supermod'])
				{
					$mod['extra'] = " AND stick=''";
				}
				break;
			case "tannounce_submit":
				$mod['perm'] = "announce_topics";
				$mod['type'] = "announce";
				$mod['forum'] = $this->lat->raw_input['forums'];
				break;
			case "tannounce":
				$this->lat->inc->forum->nav_forums($this->lat->input['id']);
				$this->lat->title = $this->lat->lang['announce_topic'];
				$this->lat->nav[] = $this->lat->lang['announce_topic'];
				$mod['perm'] = "announce_topics";
				$mod['skin'] = "announce_topics";
				$this->lat->input['item'] = implode(",", $this->lat->input['item']);
				$dropdown = $this->lat->inc->forum->generate_list(array("type" => "checkbox", "home" => $this->lat->input['id'], "prefix" => "<div><b>", "suffix" => "</b></div>"));
				break;
			case "tunannounce":
				$mod['perm'] = "announce_topics";
				$mod['type'] = "unannounce";

				if(!$this->lat->user['group']['supermod'])
				{
					$mod['extra'] = " AND stick != 1 AND stick != ''";
				}
				break;
			case "tlock":
				$mod['perm'] = "lock_topics";
				$mod['type'] = "lock";
				if($profile['own_lock'] && !$not_user)
				{
					$mod['lock'] = 1;
					$mod['user'] = 1;
				}
				break;
			case "tunlock":
				$mod['perm'] = "lock_topics";
				$mod['type'] = "unlock";
				if($profile['own_lock'] && !$cant_unlock && !$not_user)
				{
					$mod['user'] = 1;
				}
				break;
			case "tpurge_confirm":
				$pg = "tpurge";
				$msg = $this->lat->lang['purge_topics_txt'];
				$this->lat->inc->forum->nav_forums($this->lat->input['id']);
				$this->lat->title = $this->lat->lang['purge_topics'];
				$this->lat->nav[] = $this->lat->lang['purge_topics'];
				$mod['perm'] = "purge_topics";
				$mod['skin'] = "delete_topics";
				$this->lat->input['item'] = implode(",", $this->lat->input['item']);
				break;
			case "tpurge":
				$mod['perm'] = "purge_topics";
				$mod['type'] = "purge";
				$this->lat->input['tid'] = 0;
				$this->lat->input['st'] = 0;
				break;
			case "tdelete":
				$mod['perm'] = "delete_topics";
				$mod['type'] = "delete";
				$this->lat->input['tid'] = 0;
				$this->lat->input['st'] = 0;
				if($profile['own_delete'] && !$not_user)
				{
					$mod['user'] = 1;
				}
				break;
			case "tdelete_confirm":
				$pg = "tdelete";
				$msg = $this->lat->lang['delete_topics_txt'];
				$this->lat->inc->forum->nav_forums($this->lat->input['id']);
				$this->lat->title = $this->lat->lang['delete_topics'];
				$this->lat->nav[] = $this->lat->lang['delete_topics'];
				$mod['perm'] = "delete_topics";
				$mod['skin'] = "delete_topics";
				$this->lat->input['item'] = implode(",", $this->lat->input['item']);
				break;
			case "tundelete":
				$mod['perm'] = "undelete_topics";
				$mod['type'] = "undelete";
				break;
			case "thide":
				$mod['perm'] = "hide_topics";
				$mod['type'] = "hide";
				break;
			case "tunhide":
				$mod['perm'] = "hide_topics";
				$mod['type'] = "unhide";
				break;
			case "tmove":
				$this->lat->inc->forum->nav_forums($this->lat->input['id']);
				$this->lat->title = $this->lat->lang['move_topics'];
				$this->lat->nav[] = $this->lat->lang['move_topics'];
				$mod['perm'] = "move_topics";
				$mod['skin'] = "move_topics";
				$this->lat->input['item'] = implode(",", $this->lat->input['item']);
				$dropdown = $this->lat->inc->forum->generate_list(array("type" => "dropdown"));
				break;
			case "tmove_submit":
				$mod['perm'] = "move_topics";
				$mod['type'] = "move";
				$mod['forum'] = $this->lat->raw_input['forums'];
				$mod['link'] = $this->lat->get_input->whitelist("link", array(0, 1));
				if($profile['own_move'] && !$not_user)
				{
					$mod['user'] = 1;
				}
				break;
			case "pdelete":
				$mod['perm'] = "delete_posts";
				$mod['type'] = "delete";
				if($profile['own_delete_posts'] && !$not_user)
				{
					$mod['user'] = 1;
				}
				break;
			case "pundelete":
				$mod['perm'] = "undelete_posts";
				$mod['type'] = "undelete";
				break;
			case "phide":
				$mod['perm'] = "hide_posts";
				$mod['type'] = "hide";
				break;
			case "punhide":
				$mod['perm'] = "hide_posts";
				$mod['type'] = "unhide";
				break;
			case "ppurge":
				$mod['perm'] = "purge_posts";
				$mod['type'] = "purge";
				break;
		}

		if(!$this->lat->inc->moderate->can_mod($this->lat->input['id'], $mod['perm']))
		{
			if(!$mod['user'])
			{
				$this->lat->core->error("err_moderator_privileges");
			}
		}
		else
		{
			$mod['lock'] = 2;
		}

		if($mod['skin'])
		{
			eval("\$this->lat->output .= ".$this->lat->skin[$mod['skin']]);
			return;
		}

		if(substr($this->lat->input['mod'], 0, 1) == "t")
		{
			if(!$this->lat->inc->moderate->topic($mod))
			{
				$this->lat->core->error("err_input");
			}
		}
		else
		{
			if(!$this->lat->inc->moderate->post($mod))
			{
				$this->lat->core->error("err_input");
			}
		}

		if(!$this->lat->input['tid'])
		{
			$this->lat->get_input->preg_whitelist("order", "a-z");
			$this->lat->get_input->preg_whitelist("sort_by", "a-z_");
			$this->lat->get_input->preg_whitelist("state", "A-Za-z0-9_");

			if($this->lat->input['sort_by'] != "")
			{
				$extra_url .= ";sort_by=".$this->lat->input['sort_by'];
			}

			if($this->lat->input['order'] != "")
			{
				$extra_url .= ";order=".$this->lat->input['order'];
			}

			if($this->lat->input['state'] != "")
			{
				$extra_url .= ";state=".$this->lat->input['state'];
			}

			if($this->lat->input['st'] != "")
			{
				$extra_url .= ";st=".$this->lat->input['st'];
			}

			$this->lat->core->redirect($this->lat->url."forum={$this->lat->input['id']}".$extra_url, "mod_forum");
		}
		else
		{
			if($this->lat->input['st'] != "")
			{
				$extra_url .= ";st=".$this->lat->input['st'];
			}

			$this->lat->core->redirect($this->lat->url."topic={$this->lat->input['tid']}".$extra_url, "mod_topic");
		}
	}


	// +-------------------------+
	//	 Find Last Unread Post
	// +-------------------------+
	// Gets the last post that the user didn't read

	function unread()
	{
		$this->lat->input['id'] = intval($this->lat->input['id']);

		// Query: Get topic and last read times
		$query = array("select" => "r.time",
					   "from"   => "topic t",
					   "left"   => array("topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
					   "where"  => "t.id=".$this->lat->input['id']);

		$topicsread = $this->lat->sql->query($query);

		if(!$topicsread['time'])
		{
			$topicsread['time'] = $this->lat->user['forum_cutoff'];
		}

		if(!$topicsread['time'])
		{
			$topicsread['time'] = time() - 86400;
		}

		// Query: Get the message after the one we last read
		$query = array("select" => "r.id",
					   "from"   => "topic_reply r",
					   "where"  => "r.tid={$this->lat->input['id']} AND r.poster_time > ".$topicsread['time'],
					   "order"  => "r.poster_time ASC",
					   "limit"  => 1);

		$post = $this->lat->sql->query($query);

		// We couldn't find an unread post, so just go to the end!
		if(!$this->lat->sql->num())
		{
			$this->last_post();
		}
		// We found something! Go to that post!
		else
		{
			$topicid = $this->lat->input['id'];
			$this->lat->input['id'] = $post['id'];
			$this->find_post($topicid);
		}
	}


	// +-------------------------+
	//	 Find Post
	// +-------------------------+
	// Finds whatever post it gets

	function find_post($tid=0)
	{
		$content = $this->lat->user['num_posts'];
		if(!$content)
		{
			$content = $this->lat->cache['config']['num_posts'];
		}

		if(!$tid)
		{
			// Query: Get the topic ID
			$query = array("select" => "tid",
						   "from"   => "topic_reply",
						   "where"  => "id={$this->lat->input['id']}");

			$topic = $this->lat->sql->query($query);
		}
		else
		{
			$topic['tid'] = $tid;
		}

		// Number of posts, so we can calculate the number of pages
		$query = array("select" => "count(mm.id) as num",
					   "from"   => "topic_reply m",
					   "left"   => "topic_reply mm on (m.tid=mm.tid)",
					   "where"  => "mm.id={$this->lat->input['id']} AND mm.poster_time > m.poster_time");

		$find = $this->lat->sql->query($query);

		$page = floor($find['num'] / $content) * $content;

		if($page)
		{
			$extra .= ";st={$page}";
		}

		// Go to the post
		$this->lat->core->redirect($this->lat->url."topic={$topic['tid']}{$extra}#{$this->lat->input['id']}");
	}

	// +-------------------------+
	//	 Last Post
	// +-------------------------+
	// Gets the last post from a topic

	function last_post()
	{
		$this->lat->input['id'] = intval($this->lat->input['id']);
		$content = $this->lat->user['num_posts'];
		if(!$content)
		{
			$content = $this->lat->cache['config']['num_posts'];
		}

		// Query: Get the last post from the database
		$query = array("select" => "t.posts, r.id",
					   "from"   => "topic t",
					   "left"   => "topic_reply r on (r.tid=t.id)",
					   "where"  => "t.id={$this->lat->input['id']}",
					   "limit"  => 1,
					   "order"  => "r.poster_time DESC");

		$last = $this->lat->sql->query($query);

		if($last['posts'] == 1)
		{
			$this->lat->core->redirect($this->lat->url."topic=".$this->lat->input['id']);
		}

		$page = floor(($last['posts'] - 1) / $content) * $content;

		$this->lat->core->redirect($this->lat->url."topic={$this->lat->input['id']};st={$page}#{$last['id']}");
	}

	// +-------------------------+
	//	 View Topic
	// +-------------------------+
	// Gets our topic & posts

	function view_topic($ids="")
	{
		$ids = $this->lat->parse->no_text($ids);
		$this->lat->get_input->unsigned_int("st");

		// Set up topic number maximums
		if(!$this->lat->user['num_posts'])
		{
			$this->lat->user['num_posts'] = $this->lat->cache['config']['num_posts'];
		}

		if(!$ids)
		{
			// Query: Get the topic and read information from the database
			$query = array("select" => "t.*, r.time",
						   "from"   => "topic t",
						   "left"   => array("topic_read r on(r.uid={$this->lat->user['id']} AND r.tid=t.id)"),
						   "where"  => "t.id=".$this->lat->input['id']);

			$topic = $this->lat->sql->query($query);

			$this->forum_id = $topic['fid'];

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_topic");
			}

			// Check permissions
			$pre_forum = $this->lat->inc->forum->load_profile($topic['fid']);

			if(!$pre_forum['view_index'] && !$pre_forum['view_topics'] && !$pre_forum['view_posts'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if(!$pre_forum['view_posts'])
			{
				$this->lat->core->error("errpreposts");
			}

			// Navigation forums
			$this->lat->inc->forum->nav_forums($topic['fid']);

			if($topic['hidden'] > 1)
			{
				$topic['title'] .= $this->lat->lang['deleted_suffix'];
			}
			elseif($topic['hidden'] == 1)
			{
				$topic['title'] .= $this->lat->lang['hidden_suffix'];
			}

			$this->lat->nav[] = $topic['title'];
			$this->lat->title = $topic['title'];
			$this->lat->content = $topic['title'];

			$pre_mod = $this->lat->inc->forum->load_moderator($topic['fid']);

			if($this->lat->user['id'])
			{
				// Move Topic permission
				if($this->lat->user['group']['supermod'] || $pre_mod['move_topics'])
				{
					$moderate_topic .= "<option value=\"tmove\" class=\"form_select_blue\">{$this->lat->lang['move']}</option>";
				}

				// Lock Topic permission
				if($this->lat->user['group']['supermod'] || $pre_mod['lock_topics'])
				{
					if(!$topic['locked'])
					{
						$moderate_topic .= "<option value=\"tlock\" class=\"form_select_blue\">{$this->lat->lang['lock']}</option>";
					}
					else
					{
						$moderate_topic .= "<option value=\"tunlock\" class=\"form_select_blue\">{$this->lat->lang['open']}</option>";
					}
				}

				// Delete Topic permission
				if($this->lat->user['group']['supermod'] || $pre_mod['hide_topics'])
				{
					if(!$topic['hidden'])
					{
						$moderate_topic .= "<option value=\"thide\" class=\"form_select_blue\">{$this->lat->lang['hide']}</option>";
					}
					elseif($topic['hidden'] == 1)
					{
						$moderate_topic .= "<option value=\"tunhide\" class=\"form_select_blue\">{$this->lat->lang['unhide']}</option>";
					}
				}

				// Move Topic permission
				if($this->lat->user['group']['supermod'] || $pre_mod['sticky_topics'])
				{
					if($topic['stick'] == 1)
					{
						$moderate_topic .= "<option value=\"tunstick\" class=\"form_select_green\">{$this->lat->lang['unsticky']}</option>";
					}
					else
					{
						$moderate_topic .= "<option value=\"tstick\" class=\"form_select_green\">{$this->lat->lang['sticky']}</option>";
					}
				}

				// Move Topic permission
				if($this->lat->user['group']['supermod'])
				{
					if($topic['stick'] && $topic['stick'] != 1)
					{
						$moderate_topic .= "<option value=\"tunannounce\" class=\"form_select_green\">{$this->lat->lang['unannounce']}</option>";
					}
					else
					{
						$moderate_topic .= "<option value=\"tannounce\" class=\"form_select_green\">{$this->lat->lang['announce']}</option>";
					}
				}

				// Delete Topic permission
				if(($this->lat->user['group']['supermod'] || $pre_mod['delete_topics']) && $topic['hidden'] <= 1)
				{
					$moderate_topic .= "<option value=\"tdelete\" class=\"form_select_red\">{$this->lat->lang['delete']}</option>";
				}

				// Delete Topic permission
				if(($this->lat->user['group']['supermod'] || ($pre_mod['undelete_topics'] && $pre_mod['see_delete_topics'])) && $topic['hidden'] > 1)
				{
					$moderate_topic .= "<option value=\"tundelete\" class=\"form_select_red\">{$this->lat->lang['undelete']}</option>";
				}

				// Delete Topic permission
				if($this->lat->user['group']['supermod'] || $pre_mod['purge_topics'])
				{
					$moderate_topic .= "<option value=\"tpurge\" class=\"form_select_red\">{$this->lat->lang['purge']}</option>";
				}

				// Hide Post permission
				if($this->lat->user['group']['supermod'] || $pre_mod['hide_posts'])
				{
					$moderate_post .= "<option value=\"phide\" class=\"form_select_blue\">{$this->lat->lang['hide']}</option>";
				}

				// Unhide Post permission
				if($this->lat->user['group']['supermod'] || $pre_mod['hide_posts'])
				{
					$moderate_post .= "<option value=\"punhide\" class=\"form_select_blue\">{$this->lat->lang['unhide']}</option>";
				}

				// Delete Post permission
				if($this->lat->user['group']['supermod'] || $pre_mod['delete_posts'])
				{
					$moderate_post .= "<option value=\"pdelete\" class=\"form_select_red\">{$this->lat->lang['delete']}</option>";
				}

				// Undelete Post permission
				if($this->lat->user['group']['supermod'] || $pre_mod['delete_posts'])
				{
					$moderate_post .= "<option value=\"pundelete\" class=\"form_select_red\">{$this->lat->lang['undelete']}</option>";
				}

				// Purge Post permission
				if($this->lat->user['group']['supermod'] || $pre_mod['purge_posts'])
				{
					$moderate_post .= "<option value=\"ppurge\" class=\"form_select_red\">{$this->lat->lang['purge']}</option>";
				}

				// Output topic selection box
				if($moderate_topic)
				{
					$moderator['option'] .= "<div style=\"float: left\"><select name=\"mod_1\" class=\"quick\"><option value=\"\" selected=\"selected\">{$this->lat->lang['topic_moderation']}</option>{$moderate_topic}</select> <input type=\"submit\" name=\"go_1\" class=\"quick_button\" value=\"{$this->lat->lang['go']}\" /></div>";
				}

				// Output post selection box
				if($moderate_topic)
				{
					$moderator['option'] .= "<div style=\"float: right\"><select name=\"mod_2\" class=\"quick\"><option value=\"\" selected=\"selected\">{$this->lat->lang['post_moderation']}</option>{$moderate_post}</select> <input type=\"submit\" name=\"go_2\" class=\"quick_button\" value=\"{$this->lat->lang['go']}\" /></div>";
				}

				if($moderator['option'])
				{
					$moderator['option'] = "<div class=\"clear\"></div>{$moderator['option']}<div style=\"clear: both\"></div>";
				}
			}

			if($topic['poll'] && $this->lat->user['id'])
			{
				if($this->lat->get_input->whitelist("showv", array(0, 1)))
				{
					// Query: Get the topic for permission verification and such
					$query = array("select" => "v.pid, v.opt",
								   "user"   => array("u poll_"),
								   "from"   => "poll_vote v",
								   "left"   => array("user u on(v.uid=u.id)"),
								   "where"  => "v.tid={$topic['id']}");

					while($pu = $this->lat->sql->query($query))
					{
						$pollusers[$pu['pid']][$pu['opt']][] = $this->lat->show->make_username($pu, "poll_");
					}
				}

				// Query: Get the poll and vote data from the database
				$query = array("select" => "p.id, p.pid, p.question, p.options, p.type, count(v.tid) as voted",
							   "from"   => "poll p",
							   "left"   => array("poll_vote v on(p.tid=v.tid AND (v.ip='{$this->lat->user['ip']}' OR v.uid={$this->lat->user['id']}))"),
							   "where"  => "p.tid={$topic['id']}",
							   "group"  => "p.id");

				while($poll = $this->lat->sql->query($query))
				{
					if($poll['voted'])
					{
						$voted = true;
					}


					$poll['data'] = "";
					$poll['options'] = unserialize($poll['options']);

					foreach($poll['options'] as $optnum => $opt)
					{
						if(!$voted)
						{
							// What kind of poll are we looking at?
							if($poll['type'])
							{
								$poll['data'][] = "<label><input type=\"checkbox\" name=\"poll-{$poll['pid']}-{$optnum}\" value=\"1\" style=\"vertical-align: middle\" />{$opt[0]} </label>";
							}
							else
							{
								$poll['data'][] = "<label><input type=\"radio\" name=\"poll-{$poll['pid']}\" value=\"{$optnum}\" style=\"vertical-align: middle\" />{$opt[0]} </label>";
							}
						}
						else
						{
							// We're viewing results
							$bar = $this->lat->show->make_bar(array("value" => $opt[1],
																	"total" => $topic['poll_votes']));
							if($topic['poll_votes'] > 0)
							{
								$percent = ($opt[1] / $topic['poll_votes']) * 100;
							}
							$lstat = str_replace(array("<!-- VOTES -->", "<!-- PERCENT -->"), array($opt[1], round($percent, 2)), $opt[1] == 1 ? $this->lat->lang['vote_stats'] : $this->lat->lang['votes_stats']);

							if($this->lat->input['showv'] && !empty($pollusers[$poll['id']][$optnum]))
							{
								$bar .= implode(", ", $pollusers[$poll['id']][$optnum]);
							}
							eval("\$poll['data'] .= ".$this->lat->skin['poll_cell']);
						}
					}

					// Show us the questions
					if(!$voted)
					{
						$poll['data'] = "<div class=\"indent\"><span class=\"tiny_text\">".implode("<br />", $poll['data'])."</span></div>";
						eval("\$polls .=".$this->lat->skin['poll_q']);
					}
					// Show us the results
					else
					{
						eval("\$polls .=".$this->lat->skin['poll_a']);
					}
				}

				if(!$voted)
				{
					eval("\$polls .=".$this->lat->skin['poll_buttons']);
				}
				else
				{
					$this->lat->lang['total_votes'] = str_replace("<!-- VOTES -->", $topic['poll_votes'], $this->lat->lang['total_votes']);

					if(!$this->lat->input['showv'])
					{
						$this->lat->lang['total_votes'] = "<a href=\"{$this->lat->url}topic={$topic['id']};showv=1\">{$this->lat->lang['total_votes']}</a>";
					}

					eval("\$polls .=".$this->lat->skin['poll_total']);
				}
			}
			// We're a guest...
			elseif($topic['poll'] && !$this->lat->user['id'])
			{
				$poll['question'] = $this->lat->lang['poll'];
				$poll['data'] = "<h3>{$this->lat->lang['poll_login']}</h3>";
			}

			$selected_posts = explode("|", $this->lat->session->get_cookie("selposts"));

			// Get the multiquote cookie :3
			$multiquote = $this->lat->session->get_cookie("multiquote");
			$multiquote = explode("|", $multiquote);

			if(!$this->lat->user['group']['supermod'] && !$pre_mod['see_hidden_posts'])
			{
				$no_hidden = "r.hidden != 1 AND ";
			}

			// Query: Get ALL post data related to this topic
			$query = array("select" => "r.*, p.*, u.registered, u.posts, u.user_title, u.avatar_url, u.avatar_width, u.avatar_height, u.avatar_type, u.gid",
						   "from"   => "topic_reply r",
						   "user"   => array("u post_", "e edit_", "h hidden_"),
						   "left"   => array("user u on(r.poster_id=u.id)",
											 "user e on(r.edit_id=e.id)",
											 "user h on(r.hidden_id=h.id)",
											 "user_profile p on(r.poster_id=p.uid)"),
						   "where"  => $no_hidden."r.tid=".$this->lat->input['id'],
						   "limit"  => $this->lat->input['st'].",".$this->lat->user['num_posts'],
						   "order"  => "r.poster_time ASC");
		}
		else
		{
			$topic['title'] = $this->lat->lang['search_results'];
			$this->lat->nav[] = array($this->lat->lang['search'], "pg=global;do=search");
			$this->lat->nav[] = $this->lat->lang['search_results'];
			$this->lat->title = $this->lat->lang['search_results'];

			if($this->lat->input['ort'] == 1)
			{
				$ort = "DESC";
			}
			else
			{
				$ort = "ASC";
			}

			switch($this->lat->input['odr'])
			{
				case "l":
					$odr = "r.poster_time";
					break;
				case "v":
					$odr = "t.views";
					break;
				case "r":
					$odr = "t.posts";
					break;
			}

			$query = array("select" => "r.*, p.*, t.fid, t.title, u.registered, u.posts, u.user_title, u.avatar_url, u.avatar_width, u.avatar_height, u.avatar_type, u.gid",
						   "from"   => "topic_reply r",
						   "user"   => array("u post_", "e edit_"),
						   "left"   => array("user u on(r.poster_id=u.id)",
											 "user_profile p on(r.poster_id=p.uid)",
											 "user e on(r.edit_id=e.id)",
											 "topic t on (r.tid=t.id)"),
						   "where"  => "r.hidden=0 AND r.id in ({$ids})",
						   "limit"  => $this->lat->input['st'].",".$this->lat->user['num_posts'],
						   "order"  => $odr." ".$ort);
		}

		$post_num = $this->lat->input['st'];

		if($this->lat->sql->num($query))
		{
			while($post = $this->lat->sql->query($query))
			{
				$post_num++;
				$post['num'] = "#".$post_num;

				if($pre_mod['see_ip'] || $this->lat->user['group']['supermod'])
				{
					$post['ip'] = str_replace("<!-- IP -->", $post['poster_ip'], $this->lat->lang['posted_ip']);
				}

				// Guest poster
				if(!$post['poster_id'])
				{
					$post['name'] = $this->lat->show->make_username($post, "", "poster_name");

					$profile = $this->lat->show->guest_profile($post);
				}
				// User poster
				else
				{
					$post['name'] = $this->lat->show->make_username($post, "post_");

					if($post['on_sig'])
					{
						if($new_sig[$post['poster_id']] != "")
						{
							$post['signature_cached'] = $new_sig[$post['poster_id']];
						}
						elseif($post['signature_reparse'])
						{
							$this->lat->parse->recache(array("fetch" => &$post,
															 "item"  => "signature",
															 "table" => "user_profile",
															 "where" => "uid=".$post['post_id'],
															 "gid"   => $post['post_gid']));

							$new_sig[$post['poster_id']] = $post['signature_cached'];
						}

						$post['signature_cached'] = $this->lat->show->make_signature($post['signature_cached']);

					}
					else
					{
						$post['signature_cached'] = "";
					}

					$post['profile_buttons'] .= "<a href=\"{$this->lat->url}pg=msg;do=new;user={$post['post_id']}\" class=\"small_button\"><img src=\"{$this->lat->image_url}send_pm.png\" title=\"{$this->lat->lang['send_pm_to_user']}\" alt=\"\" /></a>";

					// Get IM information
					foreach($post as $pname => $pval)
					{
						if(substr($pname, 0, 8) == "profile_")
						{
							if($this->lat->cache['setting'][$pname]['im'] && $pval)
							{
								$im_id = $post['poster_id'];
								$im = substr($pname, 8);
								eval("\$post['profile_buttons'] .=".$this->lat->skin['post_im']);
							}
						}
					}

					$no_av = 0;
					if($post['hidden'] == 2 && !$this->lat->user['group']['supermod'] && !$pre_mod['see_delete_posts'])
					{
						$post['signature_cached'] = "";
						$post['data_cached'] = "";
						$no_av = 1;
					}

					$profile = $this->lat->show->user_profile($post, $no_av);
				}

				if(!$ids)
				{
					if($post['hidden'] == 1)
					{
						$post['data_cached'] = "<blockquote><span class=\"tiny_text\">".str_replace(array("<!-- USER -->", "<!-- TIME -->"), array($this->lat->show->make_username($post, "hidden_"), $this->lat->show->make_time($this->lat->user['long_date'], $post['hidden_time'])), $this->lat->lang['post_hidden'])."</span></blockquote>".$post['data_cached'];
					}
					elseif($post['hidden'] > 1)
					{
						$post['data_cached'] = "<blockquote><span class=\"tiny_text\">".str_replace(array("<!-- USER -->", "<!-- TIME -->"), array($this->lat->show->make_username($post, "hidden_"), $this->lat->show->make_time($this->lat->user['long_date'], $post['hidden_time'])), $this->lat->lang['post_deleted'])."</span></blockquote>".$post['data_cached'];
					}

					// Hide a post
					if($post['hidden'] == 0 && ($this->lat->user['group']['supermod'] || $pre_mod['hide_posts']))
					{
						$post['buttons'] .= " <a href=\"{$this->lat->url}pg=topic;do=moderate;mod=phide;item={$post['id']};tid={$this->lat->input['id']};st={$this->lat->input['st']};key={$this->lat->user['key']}\" class=\"small_button\"><span>{$this->lat->lang['hide']}</span></a>";
					}
					// Unhide a post
					if($post['hidden'] == 1 && ($this->lat->user['group']['supermod'] || $pre_mod['hide_posts']))
					{
						$post['buttons'] .= " <a href=\"{$this->lat->url}pg=topic;do=moderate;mod=punhide;item={$post['id']};tid={$this->lat->input['id']};st={$this->lat->input['st']};key={$this->lat->user['key']}\" class=\"small_button\"><span>{$this->lat->lang['unhide']}</span></a>";
					}
					// Delete a post
					if($post['hidden'] == 0 && $this->lat->user['group']['supermod'] || ((($post['hidden'] == 0 && $pre_forum['own_delete_posts'] && $post['poster_id'] == $this->lat->user['id']) || $pre_mod['delete_posts']) && $this->lat->user['id']))
					{
						$post['buttons'] .= " <a onclick=\"return confirm('{$this->lat->lang['confirm_delete_post']}')\" href=\"{$this->lat->url}pg=topic;do=moderate;mod=pdelete;item={$post['id']};tid={$this->lat->input['id']};st={$this->lat->input['st']};key={$this->lat->user['key']}\" class=\"small_button_delete\"><span>{$this->lat->lang['delete']}</span></a>";
					}
					// Undelete a post
					if($post['hidden'] == 2 && ($this->lat->user['group']['supermod'] || $pre_mod['undelete_posts']))
					{
						$post['buttons'] .= " <a href=\"{$this->lat->url}pg=topic;do=moderate;mod=pundelete;item={$post['id']};tid={$this->lat->input['id']};st={$this->lat->input['st']};key={$this->lat->user['key']}\" class=\"small_button_delete\"><span>{$this->lat->lang['undelete']}</span></a>";
					}
					// Edit a post
					if($this->lat->user['group']['supermod'] || ((($post['hidden'] == 0 && $pre_forum['own_edit'] && $post['poster_id'] == $this->lat->user['id']) || $pre_mod['edit_posts']) && $this->lat->user['id'] && (!$topic['locked'] || $pre_mod['lock_topics'])))
					{
						$post['buttons'] .= " <a href=\"{$this->lat->url}pg=post;do=edit_post;id={$post['id']};key={$this->lat->user['key']}\" class=\"small_button\"><span>{$this->lat->lang['modify']}</span></a>";
					}
					// Quote a post
					if((($pre_forum['post_replies_own'] && $topic['start_id'] == $this->lat->user['id']) || ($pre_forum['post_replies_other'] && $topic['start_id'] != $this->lat->user['id'])) && ($this->lat->user['group']['supermod'] || (!$topic['locked'] && $post['hidden'] == 0) || $pre_mod['lock_topics']))
					{
						$post['buttons'] .= " <a href=\"{$this->lat->url}pg=post;do=quote;id={$post['id']};tid={$post['tid']};key={$this->lat->user['key']}\" class=\"small_button\"><span>{$this->lat->lang['quote']}</span></a>";

						if(in_array($post['id'], $multiquote))
						{
							$post['buttons'] .= " <a href=\"javascript:void(0);\" onclick=\"sel_post({$post['id']}, 0)\" title=\"{$this->lat->lang['multi_quote']}\" class=\"small_button\"><img src=\"{$this->lat->image_url}multi_on.png\" alt=\"-\" id=\"img_{$post['id']}\" /></a>";
						}
						else
						{
							$post['buttons'] .= " <a href=\"javascript:void(0);\" onclick=\"sel_post({$post['id']}, 0)\" title=\"{$this->lat->lang['multi_quote']}\" class=\"small_button\"><img src=\"{$this->lat->image_url}multi_off.png\" alt=\"+\" id=\"img_{$post['id']}\" /></a>";
						}
					}

					// No buttons :(
					if(!$post['buttons'])
					{
						$post['buttons'] = "&nbsp;";
					}

					// Post checkboxes
					if($moderate_post && $this->lat->user['id'])
					{
						if(in_array($post['id'], $selected_posts))
						{
							$post['checked'] = " checked=\"checked\"";
						}
						else
						{
							$post['checked'] = "";
						}

						$post['mod'] = "<input type=\"checkbox\" name=\"item[]\" value=\"{$post['id']}\" class=\"form_check\" onclick=\"sel_post({$post['id']}, this)\"{$post['checked']} /> ";
					}

					// Edited post indication
					if($post['edit_id'] && $post['edit_time'])
					{
						$post['signature_cached'] = "<blockquote><span class=\"tiny_text\">".str_replace(array("<!-- USER -->", "<!-- TIME -->"), array($this->lat->show->make_username($post, "edit_"), $this->lat->show->make_time($this->lat->user['long_date'], $post['edit_time'])), $this->lat->lang['last_edited'])."</span></blockquote>".$post['signature_cached'];
					}
				}
				else
				{
					$post['buttons'] = $this->lat->lang['posted_in']." <a href=\"{$this->lat->url}post={$post['id']}\">{$post['title']}</a>";
				}

				$post['poster_time'] = $this->lat->show->make_time($this->lat->user['long_date'], $post['poster_time']);

				if($post['data_reparse'])
				{
					if($ids)
					{
						$pre_forum = $this->lat->inc->forum->load_profile($post['fid'], $post['post_gid']);
					}
					else
					{
						$pre_forum = $this->lat->inc->forum->load_profile($topic['fid'], $post['post_gid']);
					}

					$this->lat->parse->recache(array("fetch" => &$post,
													 "table" => "topic_reply",
													 "where" => "id=".$post['id'],
													 "bb"    => $pre_forum['use_bb'],
													 "smi"   => $post['on_smi'],
													 "gid"   => $post['post_gid'],
													 "type"  => 2));
				}

				eval("\$posthtml .=".$this->lat->skin['post']);
			}
		}
		elseif(!$topic['moved'])
		{
			if($ids)
			{
				$this->lat->core->error("err_input");
			}
			elseif($post_num > 0)
			{
				$this->last_post();
				return;
			}
			else
			{
				$msg = $this->lat->lang['no_posts'];
				eval("\$posthtml .=".$this->lat->skin['no_content']);
			}
		}

		if(!$ids)
		{
			$arr_page = array("total"   => $topic['posts'],
			                  "cap"     => $this->lat->user['num_posts'],
			                  "links"   => 4,
			                  "url"     => $this->lat->url."topic=".$this->lat->input['id']);
		}
		else
		{
			$arr_page = array("total"   => count(explode(",", $ids)),
			                  "cap"     => $this->lat->user['num_posts'],
			                  "links"   => 4,
			                  "url"     => $this->lat->url.$this->lat->search_url);
		}

		$pages = $this->lat->show->make_pages($arr_page);

		if(!$ids)
		{
			if($topic['last_time'] != $topic['time'] && $this->lat->user['forum_cutoff'] < $topic['last_time'] && $this->lat->user['id'])
			{
				$time_update = $this->lat->user['forum_cutoff'];
				$last_topic = -1;

				// Query: Select possible unread topics
				$query = array("select" => "t.id, t.last_time, r.time",
							   "from"   => "topic t",
							   "left"   => array("topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
							   "where"  => "t.moved=0 AND t.last_time > {$this->lat->user['forum_cutoff']}",
							   "order"  => "t.last_time ASC");

				while($trcheck = $this->lat->sql->query($query))
				{
					if($this->lat->input['id'] == $trcheck['id'])
					{
						$trcheck['time'] = $topic['last_time'];
					}

					if($trcheck['last_time'] != $trcheck['time'])
					{
						$topics_unread = true;
						break;
					}
					else
					{
						$last_topic = $trcheck['id'];
						$time_update = $trcheck['last_time'];
					}
				}

				// At least one topic is unread
				if($topics_unread)
				{
					if($last_topic != $this->lat->input['id'])
					{
						// Set forum read time to current time for our indicators
						$query = array("replace"  => "topic_read",
									   "data"     => array("tid"   => $this->lat->input['id'],
														   "uid"   => $this->lat->user['id'],
														   "time"  => $topic['last_time']),
									   "shutdown" => 1);

						$this->lat->sql->query($query);
					}

					// Query: Update our last login (cookies count as a login)
					$query = array("update"   => "user",
								   "set"      => array("forum_cutoff" => $time_update),
								   "where"    => "id=".$this->lat->user['id'],
								   "shutdown" => 1);

					$this->lat->sql->query($query);


					// Query: Delete cutoff'ed entries
					$query = array("delete"   => "topic_read",
								   "where"    => "(time < {$time_update} OR tid={$last_topic}) AND uid=".$this->lat->user['id'],
								   "shutdown" => 1);

					$this->lat->sql->query($query);
				}
				// Entire forum is read
				else
				{
					// Query: Delete all read entries!
					$query = array("delete"   => "topic_read",
								   "where"    => "uid=".$this->lat->user['id'],
								   "shutdown" => 1);

					$this->lat->sql->query($query);

					// Query: Update forum cutoff
					$query = array("update"   => "user",
								   "set"      => array("forum_cutoff" => time()),
								   "where"    => "id=".$this->lat->user['id'],
								   "shutdown" => 1);

					$this->lat->sql->query($query);
				}
			}

			// Just go to the moved topic!
			if($topic['moved'])
			{
				$this->lat->core->redirect($this->lat->url."topic=".$topic['moved']);
			}

			$topic['last_unread'] = "&nbsp;";

			// Post new topic
			if($pre_forum['post_topics'])
			{
				$buttons .= "<a href=\"{$this->lat->url}pg=post;do=topic;id={$topic['fid']};key={$this->lat->user['key']}\"><big><img src=\"{$this->lat->image_url}button_new.png\" alt=\"\" />{$this->lat->lang['make_new_topic']}</big></a>";
			}

			// We have a locked button... do we have permissions to still post?
			if($topic['locked'] && ($this->lat->user['group']['supermod'] || $pre_mod['lock_topics']))
			{
				$buttons .= "<a href=\"{$this->lat->url}pg=post;do=reply;id={$topic['id']};key={$this->lat->user['key']}\"><big><img src=\"{$this->lat->image_url}button_lock.png\" alt=\"\" />{$this->lat->lang['locked']}</big></a>";
			}
			elseif($topic['locked'] && !$pre_mod['lock_topics'] && (($pre_forum['post_replies_own'] && $topic['start_id'] == $this->lat->user['id']) || ($pre_forum['post_replies_other'] && $topic['start_id'] != $this->lat->user['id'])))
			{
				$buttons .= "<big><img src=\"{$this->lat->image_url}button_lock.png\" alt=\"\" /><s>{$this->lat->lang['locked']}</s></big>";
			}

			// Quick reply button and new reply button
			if(!$topic['locked'] && (($pre_forum['post_replies_own'] && $topic['start_id'] == $this->lat->user['id']) || ($pre_forum['post_replies_other'] && $topic['start_id'] != $this->lat->user['id'])))
			{
				if($pre_forum['quick_reply'] && $this->lat->user['id'])
				{
					$qr = "<a href=\"javascript:qr_toggle();\"><big><img src=\"{$this->lat->image_url}button_talk.png\" alt=\"\" />{$this->lat->lang['qr']}</big></a>";
					eval("\$qr_html =".$this->lat->skin['quick_reply']);
				}
				$buttons .= "<a href=\"{$this->lat->url}pg=post;do=reply;id={$topic['id']};key={$this->lat->user['key']}\"><big><img src=\"{$this->lat->image_url}button_reply.png\" alt=\"\" />{$this->lat->lang['make_new_reply']}</big></a>";
			}

			$online = array("pg"  => array("topic"),
							"on"  => $this->lat->lang['online_topic'],
							"off" => $this->lat->lang['members_off_topic']);

			$active_users = $this->lat->show->who_online($online);

			// Update topic views :)
			$query = array("update"   => "topic",
						   "set"      => "views=views+1",
						   "where"    => "id=".$this->lat->input['id'],
						   "shutdown" => 1,
						   "low"      => 1);

			$this->lat->sql->query($query);
		}

		$this->lat->show->js_files[] = $this->lat->config['MODULES_PATH']."forum/js_topic";
		eval("\$this->lat->output .=".$this->lat->skin['topic_view']);
	}
}
?>
