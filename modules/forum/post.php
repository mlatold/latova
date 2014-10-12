<?php if(!defined('LAT')) die("Access Denied.");

class module_post
{
	function initialize()
	{
		$this->lat->core->load_class("forum", "moderate");
		$this->lat->core->load_class("default", "content");
		$this->lat->nav[] = array($this->lat->lang['forum_list'], "pg=forum");
		$this->lat->show->default_search = "topic_1";

		// TODO: Remove guest restrictions
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		switch($this->lat->input['do'])
		{
			// Quote
			case "quote":
				$this->quote($this->lat->session->get_cookie("multiquote"), $this->lat->get_input->unsigned_int("tid"), $this->lat->input['id']);
				$this->lat->session->out_cookie("multiquote", "");
				break;
			// New Topic
			case "topic":
				$this->post_form(1);
				break;
			// Submit Topic
			case "submit_topic":
				$this->submit_form(1);
				break;
			// New Reply
			case "reply":
				if($cookie = $this->lat->session->get_cookie("multiquote"))
				{
					$this->quote($cookie, $this->lat->input['id']);
					$this->lat->session->out_cookie("multiquote", "");
				}
				else
				{
					$this->post_form(2);
				}
				break;
			// Submit Post
			case "submit_reply":
				$this->submit_form(2);
				break;
			// New Topic
			case "edit_post":
				$this->post_form(3);
				break;
			// Submit Topic
			case "submit_edit":
				$this->submit_form(3);
				break;
			// Not a page!
			default:
				$this->lat->core->error("err_input");
				break;
		}

		$nav = $this->lat->inc->forum->generate_list(array("type" => "redirect", "selected" => $this->forum_id));
		$nav_lang = $this->lat->lang['jump_to_forum'];
		eval("\$this->lat->output .= ".$this->lat->skin['footer_nav']);
	}


	// +-------------------------+
	//	 Quote
	// +-------------------------+
	// Gets the quoted post and loads up the new reply function

	function quote($multiquote, $tid=0, $pid=0)
	{
		$tid = intval($tid);
		$pid = intval($pid);

		// Get it from selected post cookie always first if possible...
		$multiquote = explode("|", $multiquote);
		$multiquote = $this->lat->parse->as_array("unsigned_int", $multiquote);

		if(!$multiquote)
		{
			$multiquote[] = $pid;
		}
		elseif(!in_array($pid, $multiquote))
		{
			$multiquote[] = $pid;
		}

		if(!empty($multiquote))
		{
			// Query: Get the reply
			$query = array("select" => "r.id, r.poster_id, r.data, t.fid, t.title, u.name",
						   "from"   => "topic_reply r",
						   "left"   => array("topic t on(r.tid=t.id)",
											 "user u on(r.poster_id=u.id)"),
						   "where"  => "t.id={$tid} AND r.id in (".implode(",", $multiquote).")");

			while($msg = $this->lat->sql->query($query))
			{
				// Insure we have the correct name
				if($msg['poster_name'] == "")
				{
					$msg['poster_name'] = $msg['name'];
				}

				// Check which bbtags we're going to strip from being quoted
				foreach($this->lat->cache['bbtag'] as $bbtag)
				{
					if($bbtag['no_quote'])
					{
						if($bbtag['opt'])
						{
							while(preg_match("{\[{$bbtag['tag']}=.+?\].+?\[/{$bbtag['tag']}\]}si", $msg['data']))
							{
								$msg['data'] = preg_replace("{\[{$bbtag['tag']}=.+?\].+?\[/{$bbtag['tag']}\]}si", "", $msg['data']);
							}
						}
						else
						{
							while(preg_match("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}", $msg['data']))
							{
								$msg['data'] = preg_replace("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}", "", $msg['data']);
							}
						}
					}
				}

				// Remove excess quote tags and trim
				$msg['data'] = preg_replace("{\[/quote\]}", "", $msg['data']);
				$msg['data'] = trim($msg['data']);

				// If we have post content, quote it!
				if($msg['data'] != "")
				{
					if($this->lat->input['data'])
					{
						$this->lat->input['data'] .= "\n\n";
					}

					$msg['name'] = str_replace("&", "&amp;", $msg['name']);
					$msg['name'] = str_replace("]", "&amp;amp;#93;", $msg['name']);

					$this->lat->input['data'] .= "[quote={$msg['name']}][user:{$msg['poster_id']}][post:{$msg['id']}]{$msg['data']}\n[right][post]{$msg['id']}[/post][/right][/quote]\n";
				}
			}

			$this->lat->input['id'] = $tid;
		}

		// Open up reply form
		$this->post_form(2);
	}


	// +-------------------------+
	//	 Post form
	// +-------------------------+
	// Outputs form for every post related thing

	function post_form($type)
	{
		// $type value reference
		//   1 = new topic
		//   2 = new reply
		//   3 = editing post

		$this->lat->get_input->whitelist("show_sig", array(0, 1));
		$this->lat->get_input->whitelist("show_smi", array(0, 1));

		// Check permissions of making a new topic
		if($type == 1)
		{
			$pre_forum = $this->lat->inc->forum->load_profile($this->lat->input['id']);

			if(!$this->lat->cache['forum'][$this->lat->input['id']]['parent'] || $this->lat->cache['forum'][$this->lat->input['id']]['link'] != "")
			{
				$this->lat->core->error("err_no_forum");
			}

			if(!$pre_forum['view_index'] && !$pre_forum['post_topics'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if(!$pre_forum['post_topics'])
			{
				$this->lat->core->error("err_permission_topic");
			}

			if($this->lat->cache['forum'][$this->lat->input['id']]['link'] != "")
			{
				$this->lat->core->error("err_input");
			}

			$last_forum = $this->lat->input['id'];
			$this->forum_id = $this->lat->input['id'];
		}
		// Check topic permissions of making a new reply
		elseif($type == 2)
		{
			// Query: Get topic information
			$query = array("select" => "t.start_id, t.title, t.fid, t.locked, t.stick",
						   "from"   => "topic t",
						   "where"  => "t.id={$this->lat->input['id']}");

			$topic = $this->lat->sql->query($query);
			$this->forum_id = $topic['id'];

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_topic");
			}

			$pre_forum = $this->lat->inc->forum->load_profile($topic['fid']);

			if(!$pre_forum['view_posts'] && !$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['post_topics'] && !$pre_forum['post_replies_other'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if(!$pre_forum['post_replies_own'] && $topic['start_id'] == $this->lat->user['id'])
			{
				$this->lat->core->error("err_permission_replies");
			}

			if(!$pre_forum['post_replies_other'] && $topic['start_id'] != $this->lat->user['id'])
			{
				$this->lat->core->error("err_permission_replies");
			}

			if($this->lat->cache['forum'][$topic['fid']]['link'] != "")
			{
				$this->lat->core->error("err_input");
			}

			$last_forum = $topic['fid'];
		}
		// Check reply permissions for editing
		elseif($type == 3)
		{
			// Query: Get the reply from the database which we are editing
			$query = array("select" => "u.gid, r.poster_id, r.poster_time, r.data, r.on_smi, r.on_sig, t.fid, t.title, t.id, t.locked, t.stick, t.icon",
						   "from"   => "topic_reply r",
						   "left"   => array("topic t on(r.tid=t.id)",
											 "user u on(r.poster_id=u.id)"),
						   "where"  => "r.id={$this->lat->input['id']}");

			$message = $this->lat->sql->query($query);
			$this->forum_id = $message['id'];

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_post");
			}

			$this->lat->input['show_smi'] = $message['on_smi'];
			$this->lat->input['show_sig'] = $message['on_sig'];

			if(!$this->lat->cache['forum'][$message['fid']]['parent'])
			{
				$this->lat->core->error("err_no_forum");
			}

			$pre_forum = $this->lat->inc->forum->load_profile($message['fid']);

			if(!$pre_forum['view_posts'] && !$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['own_edit'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if((($pre_forum['own_edit'] && $message['poster_id'] != $this->lat->user['id']) || !$pre_forum['own_edit']) && !$this->lat->inc->moderate->can_mod($check_fid, "edit_posts") && !$this->lat->user['group']['supermod'])
			{
				$this->lat->core->error("err_permission_edit");
			}

			$last_forum = $message['fid'];
		}

		if(substr($this->lat->input['do'], 0, 7) != "submit_")
		{
			$this->lat->raw_input['show_sig'] = 1;
			$this->lat->raw_input['show_smi'] = 1;

			if($topic['locked'] || $message['locked'])
			{
				$this->lat->raw_input['lock'] = 1;
			}

			if($topic['stick'] || $message['stick'])
			{
				$this->lat->raw_input['astate'] = 1;
			}

			if($type == 3)
			{
				$this->lat->input['data'] = $message['data'];
				$this->lat->raw_input['show_sig'] = $message['on_sig'];
				$this->lat->raw_input['show_smi'] = $message['on_smi'];

				if($message['poster_time'] > time() - 300)
				{
					$this->lat->raw_input['last_edit'] = 1;
				}
			}
		}

		// Locked topic... check editing and replies to insure we have permission
		if(($type == 2 || $type == 3) && ($message['locked'] || $topic['locked']))
		{
			if($type == 3)
			{
				$check_fid = $message['fid'];
			}
			else
			{
				$check_fid = $topic['fid'];
			}

			if(!$this->lat->inc->moderate->can_mod($check_fid, "lock_topics"))
			{
				$this->lat->core->error("err_locked");
			}
		}

		$this->lat->inc->forum->nav_forums($last_forum);

		// Navigation settings for different post types
		if($type == 1)
		{
			$this->lat->nav[] = $this->lat->lang['new_topic'];
			$this->lat->title = $this->lat->lang['new_topic_in'].$this->lat->cache['forum'][$this->lat->input['id']]['name'];
			$this->lat->content = $this->lat->cache['forum'][$this->lat->input['id']]['name'];

			if($this->lat->cache['config']['max_polls'] > 0 && $this->lat->cache['config']['max_polls_opt'] > 1 && $this->lat->input['poll_number'] <  $this->lat->cache['config']['max_polls'] && $this->lat->input['poll_number'] >= 0)
			{
				eval("\$addpoll .=".$this->lat->skin['add_poll']);
			}

			eval("\$form_html .=".$this->lat->skin['new_topic']);

			$this->lat->lang['help_poptions'] = str_replace("<!-- NUM -->", $this->lat->cache['config']['max_polls_opt'], $this->lat->lang['help_poptions']);

			if($this->lat->input['poll_number'] > $this->lat->cache['config']['max_polls'] || !$this->lat->input['poll_number'])
			{
				$this->lat->input['poll_number'] = 0;
			}

			$this->lat->get_input->as_array("no_text", "pq");
			$this->lat->get_input->as_array("ln_text", "po");
			$this->lat->get_input->as_array("form_select", "pt");

			for($i=0; $i != $this->lat->input['poll_number']; $i++)
			{
				$pollnum = str_replace("<!-- NUM -->", $i + 1, $this->lat->lang['poll_num']);
				eval("\$form_html .=".$this->lat->skin['poll_make']);
			}
			$name = $this->lat->lang['new_topic_in']."<a href='{$this->lat->url}forum={$this->lat->input['id']}'>{$this->lat->cache['forum'][$this->lat->input['id']]['name']}</a>";
			$form = "pg=post;do=submit_topic;id=".$this->lat->input['id'];
		}
		elseif($type == 2)
		{
			$this->lat->nav[] = array($topic['title'], "topic=".$this->lat->input['id']);
			$this->lat->nav[] = $this->lat->lang['new_reply'];
			$this->lat->title = $this->lat->lang['new_reply_in'].$topic['title'];
			$this->lat->content = $topic['title'];
			$name = $this->lat->lang['new_reply_in']."<a href='{$this->lat->url}topic={$this->lat->input['id']}'>{$topic['title']}</a>";
			$form = "pg=post;do=submit_reply;id=".$this->lat->input['id'];
		}
		elseif($type == 3)
		{
			if(($pre_forum['own_edit_title'] && $message['start_id'] == $this->lat->user['id']) || $this->lat->inc->moderate->can_mod($message['fid'], "edit_posts"))
			{
				// Query: Check if it is the first reply
				$query = array("select" => "count(id) as num",
							   "from"   => "topic_reply r",
							   "where"  => "r.tid={$message['id']} AND r.poster_time < ".$message['poster_time']);

				$first = $this->lat->sql->query($query);

				if(!$first['num'])
				{
					$this->lat->input['title'] = $message['title'];
					eval("\$form_html .=".$this->lat->skin['new_topic']);
				}
			}

			$this->lat->nav[] = array($message['title'], "topic=".$message['id']);
			$this->lat->nav[] = $this->lat->lang['new_modify'];
			$this->lat->title = $this->lat->lang['new_modify_in'].$message['title'];
			$this->lat->content = $message['title'];
			$name = $this->lat->lang['edit_in']."<a href='{$this->lat->url}topic={$message['id']}'>{$message['title']}</a>";
			$form = "pg=post;do=submit_edit;id=".$this->lat->input['id'];
		}

		if($this->lat->user['id'])
		{
			$this->lat->get_input->form_checkbox("show_sig");
			$post_settings = "<label><input type=\"checkbox\" name=\"show_sig\" value=\"1\" class=\"form_check\"{$this->lat->input['show_sig']} /> {$this->lat->lang['show_signature']}</label>";
		}

		// Can use bbtags?
		if($pre_forum['use_bb'])
		{
			$buttons = $this->lat->inc->content->bbtag_buttons();
		}

		// Can use smilies? Show smilies checkbox
		if($pre_forum['use_smi'])
		{
			$smilies = $this->lat->inc->content->emoticon_table();
			$this->lat->get_input->form_checkbox("show_smi");

			if($post_settings)
			{
				$post_settings .= "<br />";
			}

			$post_settings .= "<label><input type=\"checkbox\" name=\"show_smi\" value=\"1\" class=\"form_check\"{$this->lat->input['show_smi']} /> {$this->lat->lang['show_smilies']}</label>";
		}
		else
		{
			$this->lat->input['show_smi'] = 0;
		}

		// Footer
		if($message['gid'] == "")
		{
			$gid = $this->lat->user['gid'];
		}
		else
		{
			$gid = $message['gid'];
		}

		$post_footer = $this->lat->inc->content->post_footer(2, $gid);


		// Previewing post?
		if($this->lat->raw_input['preview'] && !empty($this->lat->input['data']))
		{
			$preview_post = $this->lat->parse->cache($this->lat->input['data'], array("bb" => $pre_forum['use_bb'], "smi" => $this->lat->input['show_smi'], "type" => 2, "gid" => $gid));

			$lang_post = $this->lat->lang['preview_post'];
			eval("\$form_html .=".$this->lat->skin['preview_post']);
		}

		// Output post box and post settings
		$this->lat->get_input->form_checkbox("lock");
		$lang_settings = $this->lat->lang['post_settings'];
		$post_type = 2;
		eval("\$form_html .=".$this->lat->skin['post_box']);

		// Show post icons if making a new topic
		if($type == 1 || ($type == 3 && ($message['start_id'] == $this->lat->user['id'] || $this->lat->inc->moderate->can_mod($message['fid'], "edit_posts"))))
		{
			if($type == 3)
			{
				if(!isset($first))
				{
					$query = array("select" => "count(id) as num",
								   "from"   => "topic_reply r",
								   "where"  => "r.tid={$message['id']} AND r.poster_time < ".$message['poster_time']);

					$first = $this->lat->sql->query($query);
				}

				$this->lat->raw_input['post_icon'] = $message['icon'];
			}

			if($type == 1 || !$first['num'])
			{
				$icons = $this->lat->inc->content->post_icon_table();
				eval("\$form_html .=".$this->lat->skin['topic_settings']);
			}
		}

		// Name and form related settings
		if($type == 1)
		{
			if($this->lat->user['id'])
			{
				if($this->lat->user['group']['supermod'])
				{
					$this->lat->get_input->form_select("astate");
					$mod_options = "<select class=\"form_select\" name=\"astate\"><option value=\"0\"> </option><option value=\"1\"{$this->lat->input['astate'][1]}>{$this->lat->lang['sticky']}</option><option value=\"2\"{$this->lat->input['astate'][2]}>{$this->lat->lang['announce']}</option></select><br /><label><input type=\"checkbox\" name=\"lock\" value=\"1\"{$lockcheck} /> {$this->lat->lang['lock']}</label>";
				}
				else
				{
					if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "sticky_topics"))
					{
						$this->lat->get_input->form_checkbox("astate");
						$mod_options .= "<label><input type=\"checkbox\" name=\"astate\" value=\"1\"{$this->lat->input['astate']} /> {$this->lat->lang['sticky']}</label>";
					}

					if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "lock_topics"))
					{
						if($mod_options)
						{
							$mod_options .= "<br />";
						}

						$mod_options .= "<label><input type=\"checkbox\" name=\"lock\" value=\"1\"{$this->lat->input['lock']} /> {$this->lat->lang['lock']}</label>";
					}
				}

				if($mod_options)
				{
					eval("\$form_html .=".$this->lat->skin['topic_mod']);
				}
			}
		}
		elseif($type == 2 || $type == 3)
		{
			$this->lat->get_input->form_checkbox("move");

			if($this->lat->user['group']['supermod'])
			{
				$mod_sticky = $this->lat->lang['sticky'];
				$mod_lock = $this->lat->lang['lock'];
				$this->lat->get_input->form_checkbox("astate");

				if($topic['stick'] != 2 && $message['stick'] != 2)
				{
					$mod_options .= "<label><input type=\"checkbox\" class=\"form_check\" name=\"astate\" value=\"1\"{$this->lat->input['astate']} /> {$mod_sticky}</label><br />";
				}


				$mod_options .= "<label><input type=\"checkbox\" class=\"form_check\" name=\"lock\" value=\"1\"{$this->lat->input['lock']} /> {$mod_lock}</label><br /><label><input type=\"checkbox\" class=\"form_check\" name=\"move\" value=\"1\"{$this->lat->input['move']} /> {$this->lat->lang['move']}</label>";
			}
			else
			{
				$this->lat->get_input->form_checkbox("astate");
				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "sticky_topics"))
				{
					$mod_options .= "<label><input type=\"checkbox\" name=\"astate\" value=\"1\"{$this->lat->input['astate']} /> {$this->lat->lang['sticky']}</label>";
				}

				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "lock_topics"))
				{
					if($mod_options)
					{
						$mod_options .= "<br />";
					}

					$mod_options .= "<label><input type=\"checkbox\" name=\"lock\" value=\"1\"{$this->lat->input['lock']} /> {$this->lat->lang['lock']}</label>";
				}

				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "move_topics"))
				{
					if($mod_options)
					{
						$mod_options .= "<br />";
					}

					$mod_options .= "<label><input type=\"checkbox\" name=\"move\" value=\"1\"{$this->lat->input['move']} /> {$this->lat->lang['move']}</label>";
				}
			}

			if($type == 3 && $this->lat->inc->moderate->can_mod($this->lat->input['id'], "edit_last"))
			{
				$this->lat->get_input->form_select("last_edit");

				if($mod_options)
				{
					$mod_options .= "<br />";
				}

				$mod_options .= "<select class=\"form_select\" name=\"edit_last\"><option value=\"0\">{$this->lat->lang['edit_record']}</option><option value=\"1\"{$this->lat->input['last_edit'][1]}>{$this->lat->lang['edit_last']}</option><option value=\"2\"{$this->lat->input['last_edit'][2]}>{$this->lat->lang['edit_none']}</option></select>";
			}

			if($mod_options)
			{
				eval("\$form_html .=".$this->lat->skin['topic_mod']);
			}
		}

		$this->lat->show->js_files[] = $this->lat->config['MODULES_PATH']."default/js_post";

		eval("\$form_html .=".$this->lat->skin['posting_buttons']);
		eval("\$this->lat->output .=".$this->lat->skin['post_table']);

		// Get last 10 posts... for making replies only
		if($type == 2)
		{
			$pre_mod = $this->lat->inc->forum->load_moderator($topic['fid']);

			if(!$this->lat->user['group']['supermod'] && !$pre_mod['see_hidden_posts'])
			{
				$no_hidden = " AND r.hidden != 1";
			}

			// Query: Get last 10 posts
			$query = array("select" => "r.*",
						   "user"   => array("u last_"),
						   "from"   => "topic_reply r",
						   "left"   => array("user u on(r.poster_id=u.id)"),
						   "where"  => "r.tid=".$this->lat->input['id'].$no_hidden,
						   "limit"  => "10",
						   "order"  => "r.poster_time DESC");

			// Generate post outputs
			while($post = $this->lat->sql->query($query))
			{
				$post['poster_time'] = $this->lat->show->make_time($this->lat->user['long_date'], $post['poster_time']);
				$post['name'] = $this->lat->show->make_username($post, "last_", $post['poster_name']);

				if($post['data_reparse'])
				{
					$pre_forum = $this->lat->inc->forum->load_profile($topic['fid'], $post['last_gid']);
					$this->lat->parse->recache(array("fetch" => &$post,
													 "table" => "topic_reply",
													 "where" => "id=".$post['id'],
													 "bb"    => $pre_forum['use_bb'],
													 "smi"   => $post['on_smi'],
													 "gid"   => $post['last_gid'],
													 "type"  => 2));
				}

				if($post['hidden'] == 2 && !$this->lat->user['group']['supermod'] && !$pre_mod['see_delete_posts'])
				{
					$post['data_cached'] = "";
				}

				if($post['hidden'] == 1)
				{
					$post['data_cached'] = "<blockquote><span class=\"tiny_text\"><b>{$this->lat->lang['last_post_hidden']}</b></span></blockquote>".$post['data_cached'];
				}
				elseif($post['hidden'] > 1)
				{
					$post['data_cached'] = "<blockquote><span class=\"tiny_text\"><b>{$this->lat->lang['last_post_deleted']}</b></span></blockquote>".$post['data_cached'];
				}

				eval("\$post_html .=".$this->lat->skin['last_posts_row']);
			}

			eval("\$this->lat->output .=".$this->lat->skin['last_posts_table']);
		}

	}

	// +-------------------------+
	//	 Submit Topic
	// +-------------------------+
	// Submits a new topic to be posted

	function submit_form($type)
	{
		// $type value reference
		//   1 = new topic
		//   2 = new reply
		//   3 = editing post

		$this->lat->get_input->as_array("whitelist", "pt", array(0, 1));
		$this->lat->get_input->whitelist("astate", array(0, 1, 2));
		$this->lat->get_input->whitelist("show_sig", array(0, 1));
		$this->lat->get_input->whitelist("show_smi", array(0, 1));
		$this->lat->get_input->whitelist("lock", array("", 0, 1));
		$this->lat->get_input->whitelist("move", array(0, 1));
		$this->lat->get_input->unsigned_int("poll_number");
		$this->lat->get_input->ln_text("data");
		$this->lat->core->check_key_form();

		// Are we showing a signature?
		if($this->lat->input['show_sig'] != 1)
		{
			$this->lat->input['show_sig'] = 0;
		}

		// Are we displaying some smilies?
		if($this->lat->input['show_smi'] != 1)
		{
			$this->lat->input['show_smi'] = 0;
		}

		if(!$this->lat->input['id'] || $this->lat->input['id'] < 0)
		{
			$this->lat->core->error("err_input");
		}

		if($type == 1)
		{
			$this->lat->upcat = $this->lat->input['id'];

			if(!$this->lat->cache['forum'][$this->lat->input['id']]['parent'] || $this->lat->cache['forum'][$this->lat->input['id']]['link'] != "")
			{
				$this->lat->core->error("err_no_forum");
			}

			// Load permissions
			$pre_forum = $this->lat->inc->forum->load_profile($this->lat->input['id']);

			if(!$pre_forum['view_posts'] && !$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['post_topics'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if(!$pre_forum['post_topics'])
			{
				$this->lat->core->error("err_permission_topic");
			}

			$this->lat->get_input->no_text("title");

			// The title is too long
			if($this->lat->parse->get_length($this->lat->raw_input['title']) > 50)
			{
				$this->lat->form_error[] = "err_title_long";
			}
			// Title is not filled in
			elseif(!$this->lat->get_input->no_text("title"))
			{
				$this->lat->form_error[] = "err_title_none";
			}
			// The title is too short
			elseif($this->lat->parse->get_length($this->lat->raw_input['title']) < 3)
			{
				$this->lat->form_error[] = "err_title_short";
			}

			$this->lat->input['poll_number'] = intval($this->lat->input['poll_number']);
			if($this->lat->input['poll_number'] > $this->lat->cache['config']['max_polls'] || $this->lat->input['poll_number'] < 0)
			{
				$this->lat->input['poll_number'] = 0;
			}

			$this->lat->get_input->as_array("no_text", "pq");
			$this->lat->get_input->as_array("ln_text", "po");

			for($i=0; $i != $this->lat->input['poll_number']; $i++)
			{
				$pquestion = $this->lat->parse->no_text($this->lat->input['pq'][$i]);

				if($pquestion != "")
				{
					$pollnum++;
					$polloptnum = 0;
					$poll[$pollnum]['question'] = $pquestion;
					$poption = explode("\n", $this->lat->input['po'][$i]);
					foreach($poption as $popt)
					{
						$popt = $this->lat->parse->no_text($popt);
						if($popt != "")
						{
							if($this->lat->parse->get_length($popt) > 200)
							{
								$this->lat->form_error[] = "err_option_long";
								break 2;
							}

							$polloptnum++;
							$poll[$pollnum]['options'][$polloptnum][0] = $popt;
							$poll[$pollnum]['options'][$polloptnum][1] = 0;
						}
					}

					if($polloptnum < 2 || $polloptnum > $this->lat->cache['config']['max_polls_opt'])
					{
						$this->lat->form_error[] = "err_option";
						break;
					}

					if($this->lat->input['pt'][$i])
					{
						$poll[$pollnum]['type'] = 1;
					}
				}
			}

		}
		elseif($type == 2)
		{
			// Query: Get last topic information and permissions
			$query = array("select" => "t.start_id, t.title, t.fid, t.moved, t.locked, t.stick",
						   "from"   => "topic t",
						   "where"  => "t.id={$this->lat->input['id']}");

			$topic = $this->lat->sql->query($query);

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_topic");
			}

			$this->lat->upcon = $this->lat->input['id'];
			$this->lat->upcat = $topic['fid'];
			$pre_forum = $this->lat->inc->forum->load_profile($topic['fid']);

			if($topic['moved'])
			{
				$this->lat->core->error("err_input");
			}

			$pre_forum = $this->lat->inc->forum->load_profile($topic['fid']);

			if(!$this->lat->cache['forum'][$topic['fid']]['parent'] || $this->lat->cache['forum'][$topic['fid']]['link'] != "")
			{
				$this->lat->core->error("err_input");
			}

			if(!$pre_forum['view_posts'] && !$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['post_topics'] && !$pre_forum['post_replies_other'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if(!$pre_forum['post_replies_own'] && $topic['start_id'] == $this->lat->user['id'])
			{
				$this->lat->core->error("err_permission_replies");
			}

			if(!$pre_forum['post_replies_other'] && $topic['start_id'] != $this->lat->user['id'])
			{
				$this->lat->core->error("err_permission_replies");
			}

			if(!$this->lat->cache['forum'][$topic['fid']]['parent'])
			{
				$this->lat->core->error("err_no_forum");
			}
		}
		elseif($type == 3)
		{
			// Query: Get our current reply to check permissions
			$query = array("select" => "r.poster_id, r.poster_time, t.fid, t.id, t.locked, t.stick, u.gid",
						   "from"   => "topic_reply r",
						   "left"   => array("topic t on(r.tid=t.id)",
											 "user u on (r.poster_id=u.id)"),
						   "where"  => "r.id={$this->lat->input['id']}");

			$message = $this->lat->sql->query($query);

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_post");
			}

			$this->lat->upcon = $message['id'];
			$this->lat->upcat = $message['fid'];

			// Edits within 5 minutes won't be marked as edited
			if($message['poster_time'] < time() - 300 || $message['poster_id'] != $this->lat->user['id'])
			{
				$update_edit = true;
			}

			if(!$this->lat->cache['forum'][$message['fid']]['parent'])
			{
				$this->lat->core->error("err_no_forum");
			}

			$pre_forum = $this->lat->inc->forum->load_profile($message['fid']);

			if(!$pre_forum['view_posts'] && !$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['own_edit'])
			{
				$this->lat->core->error("err_no_topic");
			}

			if((($pre_forum['own_edit'] && $message['poster_id'] != $this->lat->user['id']) || !$pre_forum['own_edit']) && !$this->lat->inc->moderate->can_mod($message['fid'], "edit_posts"))
			{
				$this->lat->core->error("err_permission_edit");
			}
		}


		if(($type == 1 || $type == 2) && !$this->lat->form_error && !$this->lat->raw_input['preview'] && !$this->lat->raw_input['addpoll'])
		{
			$query = array("select" => "count(r.id) as num",
						   "from"   => "topic_reply r",
						   "where"  => "r.poster_time > ".(time() - $this->lat->user['group']['flood_post']." AND (r.poster_id={$this->lat->user['id']} OR r.poster_ip='{$this->lat->user['ip']}')"));

			$pc = $this->lat->sql->query($query);

			if($pc['num'])
			{
				$this->lat->form_error[] = str_replace("<!-- NUM -->", $this->lat->user['group']['flood_post'], $this->lat->lang['err_post_flood']);
			}
		}


		// Check to see if we can post in this locked topic
		if(($type == 2 || $type == 3) && ($message['locked'] || $topic['locked']))
		{
			if($type == 3)
			{
				$check_fid = $message['fid'];
			}
			else
			{
				$check_fid = $topic['fid'];
			}

			if(!$this->lat->inc->moderate->can_mod($check_fid, "lock_topics"))
			{
				$this->lat->core->error("err_locked");
			}
		}

		// No smilies permissions
		if(!$pre_forum['use_smi'])
		{
			$this->lat->input['show_smi'] = 0;
		}

		if($type == 3)
		{
			$post_profile = $this->lat->parse->load_profile(2, $message['gid']);
		}
		else
		{
			$post_profile = $this->lat->parse->load_profile(2);
		}


		// Post is too long for admin setting or the SQL field won't fit it
		if($this->lat->parse->get_length($this->lat->input['data']) > $post_profile['chr'] || strlen($this->lat->input['data']) > 65535)
		{
			$this->lat->form_error[] = "err_post_long";
		}
		// Data is not filled in
		elseif($this->lat->raw_input['data'] == "")
		{
			$this->lat->form_error[] = "err_post_none";
		}

		if($this->lat->raw_input['addpoll'])
		{
			$this->lat->input['poll_number']++;
			unset($this->lat->form_error);
		}

		if($type == 3)
		{
			$pre_forum = $this->lat->inc->forum->load_profile($message['fid'], $message['gid']);

			$data_cached = $this->lat->parse->cache($this->lat->input['data'], array("bb" => $pre_forum['use_bb'], "gid" => $message['gid'], "smi" => $this->lat->input['show_smi'], "type" => 2));

			if(strlen($data_cached) > 65535)
			{
				$this->lat->form_error[] = "err_post_long";
			}
		}

		// A mistake was made... or we are previewing!
		if($this->lat->form_error || $this->lat->raw_input['preview'] || $this->lat->raw_input['addpoll'])
		{
			if($this->lat->raw_input['preview'] && $this->lat->raw_input['quick_reply'])
			{
				unset($this->lat->form_error);
			}

			return $this->post_form($type);
		}

		if($type == 1)
		{
			if(!$this->lat->raw_input['quick_reply'])
			{
				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "lock_topics") && $this->lat->input['lock'])
				{
					$this->lat->input['lock'] = 2;
				}
				else
				{
					$this->lat->input['lock'] = 0;
				}

				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "sticky_topics") && $this->lat->input['astate'] == 1)
				{
					$stick = 1;
				}
				else
				{
					$stick = 0;
				}
			}

			$this->lat->get_input->unsigned_int("post_icon");

			if(!$this->lat->cache['icon'][$this->lat->input['post_icon']]['is_icon'])
			{
				$this->lat->input['post_icon'] = 0;
			}

			// Create a new topic
			$new_topic = array("forum"       => $this->lat->input['id'],
							   "title"       => $this->lat->input['title'],
							   "description" => $description,
							   "user_id"     => $this->lat->user['id'],
							   "user_name"   => $real_name,
							   "name"        => $username,
							   "data"        => $this->lat->raw_input['data'],
							   "sig"         => $this->lat->input['show_sig'],
							   "smi"         => $this->lat->input['show_smi'],
							   "icon"        => $this->lat->input['post_icon'],
							   "poll"        => $poll,
							   "stick"       => $stick,
							   "lock"        => $this->lat->input['lock'],
							   "increment"   => true);

			$tid = $this->lat->inc->forum->new_topic($new_topic);

			if($this->lat->input['astate'] == 2)
			{
				$this->lat->core->redirect($this->lat->url."pg=topic;do=moderate;mod=tannounce;item={$tid};tid={$tid};key={$this->lat->user['key']}", "new_topic");
			}
			else
			{
				$this->lat->core->redirect($this->lat->url."topic=".$tid, "new_topic");
			}
		}
		elseif($type == 2)
		{
			if(!$this->lat->raw_input['quick_reply'])
			{
				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "lock_topics"))
				{
					if($topic['locked'] && !$this->lat->input['lock'])
					{
						$topic_lock = 0;
					}
					elseif(!$topic['locked'] && $this->lat->input['lock'])
					{
						$topic_lock = 2;
					}
				}

				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "sticky_topics"))
				{
					if($topic['stick'] && !$this->lat->input['astate'])
					{
						$topic_stick = 0;
					}
					elseif(!$topic['stick'] && $this->lat->input['astate'])
					{
						$topic_stick = 1;
					}
				}
			}

			// Create a new reply
			$new_reply = array("topic"     => $this->lat->input['id'],
							   "user_id"   => $this->lat->user['id'],
							   "user_name" => $real_name,
							   "name"      => $username,
							   "data"      => $this->lat->raw_input['data'],
							   "sig"       => $this->lat->input['show_sig'],
							   "smi"       => $this->lat->input['show_smi'],
							   "stick"     => $topic_stick,
							   "lock"      => $topic_lock,
							   "increment" => true);

			$reply = $this->lat->inc->forum->new_reply($new_reply);

			if($this->lat->input['move'])
			{
				$this->lat->core->redirect($this->lat->url."pg=topic;do=moderate;mod=tmove;item={$this->lat->input['id']};key={$this->lat->user['key']}");
			}
			else
			{
				$this->lat->core->redirect($this->lat->url."post=".$reply);
			}
		}
		elseif($type == 3)
		{
			if(!$this->lat->raw_input['quick_reply'])
			{
				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "lock_topics"))
				{
					if($message['locked'] && !$this->lat->input['lock'])
					{
						$set['locked'] = 0;
					}
					elseif(!$message['locked'] && $this->lat->input['lock'])
					{
						$set['locked'] = 2;
					}
				}

				if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "sticky_topics"))
				{
					if($message['stick'] && !$this->lat->input['astate'])
					{
						$set['stick'] = 0;
					}
					elseif(!$message['stick'] && $this->lat->input['astate'])
					{
						$set['stick'] = 1;
					}
				}
			}

			if(($pre_forum['own_edit_title'] && $message['start_id'] == $this->lat->user['id']) || $this->lat->inc->moderate->can_mod($message['fid'], "edit_posts"))
			{
				// Query: Check if it is the first reply
				$query = array("select" => "count(id) as num",
							   "from"   => "topic_reply r",
							   "where"  => "r.tid={$message['id']} AND r.poster_time < ".$message['poster_time']);

				$first = $this->lat->sql->query($query);

				if(!$first['num'])
				{
					$set['title'] = $this->lat->get_input->no_text("title");;
				}
			}

			// Show post icons if making a new topic
			if($message['start_id'] == $this->lat->user['id'] || $this->lat->inc->moderate->can_mod($message['fid'], "edit_posts"))
			{
				$this->lat->get_input->unsigned_int("post_icon");

				if($this->lat->cache['icon'][$this->lat->input['post_icon']]['is_icon'])
				{
					$set['icon'] = $this->lat->input['post_icon'];
				}
			}

			if($this->lat->inc->moderate->can_mod($this->lat->input['id'], "edit_last"))
			{
				$this->lat->get_input->whitelist("edit_last", array(0, 1, 2));
			}
			else
			{
				if($message['poster_time'] > time() - 300)
				{
					$dont_add_edit = true;
				}
			}

			if(!$this->lat->input['edit_last'] && !$dont_add_edit)
			{
				$set_post['edit_ip'] = $this->lat->user['ip'];
				$set_post['edit_id'] = $this->lat->user['id'];
				$set_post['edit_time'] = time();
			}
			elseif($this->lat->input['edit_last'] == 2)
			{
				$set_post['edit_ip'] = "";
				$set_post['edit_id'] = 0;
				$set_post['edit_time'] = time();
			}

			$set_post['data_cached'] = $this->lat->parse->sql_text($data_cached);
			$set_post['data'] = $this->lat->input['data'];
			$set_post['on_smi'] = $this->lat->input['show_smi'];
			$set_post['on_sig'] = $this->lat->input['show_sig'];

			// Query: Update the post
			$query = array("update" => "topic_reply",
						   "set"    => $set_post,
						   "where"  => "id=".$this->lat->input['id']);

			$this->lat->sql->query($query);

			if(!empty($set))
			{
				// Query: Update the post
				$query = array("update" => "topic",
							   "set"    => $set,
							   "where"  => "id=".$message['id']);

				$this->lat->sql->query($query);
			}

			$this->lat->inc->forum->sync_topic($message['id']);

			if($this->lat->input['move'])
			{
				$this->lat->core->redirect($this->lat->url."pg=topic;do=moderate;mod=tmove;item={$message['id']};key={$this->lat->user['key']}");
			}
			else
			{
				$this->lat->core->redirect($this->lat->url."post={$this->lat->input['id']}");
			}
		}
	}
}
?>