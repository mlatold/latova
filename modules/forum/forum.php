<?php if(!defined('LAT')) die("Access Denied.");

class module_forum
{
	function initialize()
	{
		$this->lat->core->load_class("forum", "forum");
		$this->lat->nav[] = array($this->lat->lang['forum_list'], "pg=forum");
		$this->lat->show->default_search = "topic_2";
		$this->lat->core->load_cache("icon");
		switch($this->lat->input['do'])
		{
			// Temporary Cache Updater
			case "cache":
				if($this->lat->user['gid'] == 1 || DEBUG)
				{
					$this->lat->title = "Cache Updated";
					$this->lat->nav = "Cache Updated";
					$this->lat->sql->cache();
					$this->lat->output = "<div class='bdr'><h1>Cache Update Successful</h1><div class='bdr2'>The latova cache has been updated/synced with the records in the database.<br><br><span class='tiny_text'>Please note that this page is not going to be in the final version of latova, it is a temporary page since no installer exists. Yeah I know theres an admincp now with the option, but if theres ever the case where you mess something up or if you're installing and need to sync the script_url config then this is why this page here still exists.</span></div></div>";
				}
				else
				{
					$this->lat->core->error("You don't have permission to do this.");
				}
				break;
			// View a forum
			case "view":
				$this->view_forum();
				break;
			// Mark a forum as read
			case "read_forum":
				$this->read_forum();
				break;
			// Mark all forums as read
			case "read_board":
				$this->read_board();
				break;
			// Redirect to forum
			case "url":
				$this->lat->core->redirect($this->lat->url."forum=".$this->lat->input['id']);
				break;
			// Output root forum list
			default:
				$this->forum_list();
				break;
		}

		if($this->lat->input['do'])
		{
			$nav = $this->lat->inc->forum->generate_list(array("type" => "redirect", "selected" => $this->forum_id));
			$nav_lang = $this->lat->lang['jump_to_forum'];
			eval("\$this->lat->output .= ".$this->lat->skin['footer_nav']);
		}
	}


	// +-------------------------+
	//	 Latova System
	// +-------------------------+
	// Kernel level maintenance and things

	function latova_system($data)
	{
		$this->lat->core->load_cache("forum");
		switch($data['type'])
		{
			case "statistics":
				$posts = 0;
				$topics = 0;
				foreach($this->lat->cache['forum'] as $f)
				{
					$posts += $f['posts'];
					$topics += $f['topics'];
				}

				return str_replace(array("<!-- POSTS -->", "<!-- TOPICS -->"), array($posts, $topics), $this->lat->lang['forum_stats']);
				break;
			case "reparse":
				$query = array("update" => "topic_reply",
							   "set"    => array("data_reparse" => 1));

				if($data['user'])
				{
					$query['where'] .= "poster_id=".$data['user'];
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
				break;
			case "search_form":
				$this->lat->core->load_class("forum", "forum");
				$dropdown = $this->lat->inc->forum->generate_list(array("type" => "checkbox", "selected" => true));

				if($this->lat->raw_input['odr'] == "")
				{
					$this->lat->raw_input['odr'] = "l";
				}
				if(!$this->lat->raw_input['ort'])
				{
					$this->lat->raw_input['ort'] = 1;
				}
				if(!$this->lat->raw_input['sra'])
				{
					$this->lat->raw_input['sra'] = 1;
				}

				$this->lat->get_input->form_select("odr");
				$this->lat->get_input->form_radio("ort");
				$this->lat->get_input->form_radio("sra");

				eval("\$this->lat->output =".$this->lat->skin['search_topic']);
				break;
			case "search_quick":
				$this->lat->raw_input['odr'] = "l";
				$this->lat->raw_input['ort'] = 1;

				if($data['search_type'] == 1)
				{
					$this->lat->raw_input['sra'] = 1;
				}
				else
				{
					$this->lat->raw_input['sra'] = 2;
				}
				$this->lat->input['forums'] = true;
				break;
			case "search_url":
				if($this->lat->get_input->no_text("fid"))
				{
					$forums = explode(",", $this->lat->input['fid']);
				}
				elseif($this->lat->raw_input['forums'])
				{
					$forums = $this->lat->raw_input['forums'];
				}
				elseif($this->lat->input['do'] != "submit_search")
				{
					$this->lat->input['forums'] = true;
				}

				$this->lat->core->load_class("forum", "forum");
				foreach($this->lat->cache['forum'] as $f)
				{
					$pre_forum = $this->lat->inc->forum->load_profile($f['id']);
					if($f['parent'] && $pre_forum['view_posts'] && $pre_forum['view_topics'])
					{
						if($this->lat->input['forums'] === true || @in_array($f['id'], $forums))
						{
							$search_forums[] = $f['id'];
						}
						else
						{
							$not_all = true;
						}
					}
				}

				if(empty($search_forums) && $this->lat->input['do'] == "submit_search")
				{
					$this->lat->core->error("err_no_forums");
				}

				$this->lat->get_input->whitelist("sra", array(1, 2));
				$this->lat->get_input->whitelist("ort", array(1, 2));
				$this->lat->get_input->whitelist("odr", array("l", "r", "v"));

				if($this->lat->input['sra'] != 1)
				{
					$url['sra'] = $this->lat->input['sra'];
				}

				if($this->lat->input['ort'] != 1)
				{
					$url['ort'] = $this->lat->input['ort'];
				}

				if($this->lat->input['odr'] != "l")
				{
					$url['odr'] = $this->lat->input['odr'];
				}

				if($not_all)
				{
					$url['fid'] = implode(",", $search_forums);
				}

				return $url;
			case "search_execute":
				if($this->lat->get_input->no_text("fid"))
				{
					$forums = explode(",", $this->lat->input['fid']);
				}
				else
				{
					$forums = true;
				}

				$this->lat->core->load_class("forum", "forum");
				foreach($this->lat->cache['forum'] as $f)
				{
					$pre_forum = $this->lat->inc->forum->load_profile($f['id']);
					if($f['parent'] && $pre_forum['view_posts'] && $pre_forum['view_topics'] && ($forums === true || @in_array($f['id'], $forums)))
					{
						$search_forums[] = $f['id'];
					}
				}

				if(empty($search_forums))
				{
					$this->lat->core->error("err_no_forums");
				}

				$this->lat->get_input->whitelist("sra", array(1, 2));
				$this->lat->get_input->whitelist("ort", array(1, 2));
				$this->lat->get_input->whitelist("odr", array("l", "r", "v"));

				if($this->lat->input['ort'] == 1)
				{
					$ort = "DESC";
				}
				else
				{
					$ort = "ASC";
				}

				if($this->lat->input['sra'] == 2)
				{
					if($this->lat->input['terms'])
					{
						$where = "(MATCH(t.title) AGAINST ('+({$this->lat->input['terms']})' IN BOOLEAN MODE) OR MATCH(r.data) AGAINST ('+({$this->lat->input['terms']})' IN BOOLEAN MODE)) AND ";
					}

					if($this->lat->input['usr'])
					{
						$where .= "t.start_id='{$this->lat->input['usr']}' AND ";
					}

					switch($this->lat->input['odr'])
					{
						case "l":
							$odr = "t.last_time";
							break;
						case "v":
							$odr = "t.views";
							break;
						case "r":
							$odr = "t.posts";
							break;
					}

					$query = array("select" => "DISTINCT t.id",
					               "from"   => "topic_reply r",
								   "left"   => "topic t on (r.tid=t.id)",
					               "where"  => $where."t.fid in(".implode(",", $search_forums).") AND t.moved=0 AND t.hidden=0",
					               "order"  => $odr." ".$ort,
					               "limit"  => 4000);
				}
				else
				{
					if($this->lat->input['terms'])
					{
						$where = "MATCH(r.data) AGAINST ('+({$this->lat->input['terms']})' IN BOOLEAN MODE) AND ";
					}

					if($this->lat->input['usr'])
					{
						$where .= "r.poster_id='{$this->lat->input['usr']}' AND ";
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

					$query = array("select" => "r.id",
					               "from"   => "topic_reply r",
								   "left"   => "topic t on (r.tid=t.id)",
					               "where"  => $where."t.fid in(".implode(",", $search_forums).") AND r.hidden=0",
					               "order"  => $odr." ".$ort,
					               "limit"  => 4000);
				}

				while($result = $this->lat->sql->query($query))
				{
					$ids[] = $result['id'];
				}

				return $ids;
			case "search_view":
				if($this->lat->input['sra'] == 2)
				{
					$this->lat->core->load_lang("forum");
					$this->lat->core->load_skin("forum");

					$this->view_forum($data['result']['content']);
				}
				else
				{
					$this->lat->core->load_lang("topic");
					$this->lat->core->load_skin("topic");

					$this->lat->core->load_module("topic");
					$this->lat->module->topic->view_topic($data['result']['content']);
				}
				break;
		}
	}


	// +-------------------------+
	//   Read Forum
	// +-------------------------+
	// Mark just one forum as read

	function read_forum()
	{
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		$this->lat->core->check_key();

		$this->lat->input['id'] = intval($this->lat->input['id']);

		// Query: First determine the latest topic that ISN'T from this forum
		$query = array("select" => "t.last_time",
		               "from"   => "topic t",
		               "left"   => array("topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
		               "where"  => "(t.last_time > r.time OR r.time IS NULL) AND t.fid != {$this->lat->input['id']} AND t.moved=0 AND t.last_time > ".$this->lat->user['forum_cutoff'],
		               "order"  => "t.last_time ASC",
		               "limit"  => 1);

		// Increase the cutoff point, if possible
		if($this->lat->sql->num($query))
		{
			$t = $this->lat->sql->query($query);

			if($t['last_time'] > $this->lat->user['forum_cutoff'])
			{
				$t['last_time']--;

				// Set forum read time to current time for our indicators
				$query = array("update"	  => "user",
				               "set"      => array("forum_cutoff" => $t['last_time']),
				               "where"    => "id=".$this->lat->user['id']);

				$this->lat->sql->query($query);

				$this->lat->user['forum_cutoff'] = $t['last_time'];

				$query = array("delete"   => "topic_read",
							   "where"    => "time <= {$t['last_time']} AND uid=".$this->lat->user['id']);

				$this->lat->sql->query($query);
			}
		}

		// Query: Get topics that are unread in this forum
		$query = array("select" => "t.id, t.last_time",
		               "from"   => "topic t",
	                   "left"   => array("topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
		               "where"  => "(t.last_time > r.time OR r.time IS NULL) AND t.fid={$this->lat->input['id']} AND t.moved=0 AND t.last_time > ".$this->lat->user['forum_cutoff']);

		// For each topic that is unread, make it read
		while($topic = $this->lat->sql->query($query))
		{
			$p_insert = $this->lat->sql->parse_insert(array("tid"  => $topic['id'],
			                                    			"uid"  => $this->lat->user['id'],
			                                    			"time" => $topic['last_time']));
			$insert['name'] = $p_insert['name'];
			$insert['data'][] = $p_insert['data'];
		}

		if(!empty($insert))
		{
			$query = array("preplace"  => "topic_read",
						   "name"     => $insert['name'],
			               "data"     => $insert['data']);

			$this->lat->sql->query($query);
		}

		// Redirect to the forum
		$this->lat->core->redirect($this->lat->url."forum={$this->lat->input['id']}", "read_forum");
	}


	// +-------------------------+
	//   Read Board
	// +-------------------------+
	// Mark the entire board as read

	function read_board()
	{
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		$this->lat->core->check_key();

		$query = array("update"	  => "user",
					   "set"      => array("forum_cutoff" => time()),
					   "where"    => "id=".$this->lat->user['id']);

		$this->lat->sql->query($query);

		$query = array("delete" => "topic_read",
		               "where"  => "uid=".$this->lat->user['id']);

		$this->lat->sql->query($query);

		// Redirect to index
		$this->lat->core->redirect($this->lat->url."pg=forum", "read_board");
	}


	// +-------------------------+
	//   View Forum
	// +-------------------------+
	// Outputs all the topics in the desired forum

	function view_forum($ids="")
	{
		$this->lat->get_input->unsigned_int("st");
		$this->forum_id = $this->lat->input['id'];

		// Set up topic number maximums
		if(!$this->lat->user['num_topics'])
		{
			$this->lat->user['num_topics'] = $this->lat->cache['config']['num_topics'];
		}

		if(!$ids)
		{
			$this->lat->get_input->preg_whitelist("order", "a-z");
			$this->lat->get_input->preg_whitelist("sort_by", "a-z_");

			// Forum doesn't exist, or is a weird input
			if(!$this->lat->input['id'])
			{
				$this->lat->core->error("err_input");
			}

			// Load permission Profile
			$pre_forum = $this->lat->inc->forum->load_profile($this->lat->input['id']);

			// Load moderation Profile
			$pre_mod = $this->lat->inc->forum->load_moderator($this->lat->input['id']);

			// The forum doesn't exist, or we have really low access
			if(!isset($this->lat->cache['forum'][$this->lat->input['id']]) || (!$pre_forum['view_topics'] && !$pre_forum['view_index'] && !$pre_forum['view_posts']))
			{
				$this->lat->core->error("err_no_forum");
			}

			// This is a linked forum
			if($this->lat->cache['forum'][$this->lat->input['id']]['link'] != "")
			{
				$query = array("update" => "forum",
				               "set"    => array("link_clicks=" => "link_clicks+1"),
				               "where"  => "id=".$this->lat->input['id']);

				$this->lat->sql->query($query);
				$this->lat->sql->cache("forum");

				$this->lat->core->redirect($this->lat->cache['forum'][$this->lat->input['id']]['link']);
			}

			// No permission to view topics!
			if(!$pre_forum['view_topics'])
			{
				$this->lat->core->error("err_permission_topics");
			}

			$this->lat->inc->forum->nav_forums($this->lat->input['id']);
			$this->lat->title = $this->lat->cache['forum'][$this->lat->input['id']]['name'];
			$this->lat->content = $this->lat->cache['forum'][$this->lat->input['id']]['name'];
		}

		// Is this an actual forum?
		if($this->lat->cache['forum'][$this->lat->input['id']]['parent'] || $ids)
		{
			if(!$ids)
			{
				$where = array("t.fid=".$this->lat->input['id']);

				// How are we ordering this
				switch($this->lat->get_input->preg_whitelist("state", "A-Za-z0-9_"))
				{
					case "open":
						$where[] = "t.locked = 0";
						$where[] = "t.poll = 0";
						break;
					case "polls":
						$where[] = "t.locked = 0";
						$where[] = "t.poll = 1";
						break;
					case "hot":
						$where[] = "t.locked = 0";
						$where[] = "t.poll = 0";
						$where[] = "t.posts > ".($this->lat->cache['config']['hot_topic'] - 1);
						break;
					case "hot_polls":
						$where[] = "t.locked = 0";
						$where[] = "t.poll = 1";
						$where[] = "t.posts > ".($this->lat->cache['config']['hot_topic'] - 1);
						break;
					case "moved":
						$where[] = "t.moved > 0";
						break;
					case "locked":
						$where[] = "t.locked > 0";
						break;
				}

				switch($this->lat->cache['forum'][$this->lat->input['id']]['topic_order'])
				{
					case 1:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "topic_date";
						break;
					case 2:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "topic_title";
						break;
					case 3:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "views";
						break;
					case 4:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "replies";
						break;
					case 5:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "creator";
						break;
					default:
						$this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] = "last_date";
						break;
				}

				// Default forum orientation settings
				if($this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] == 1)
				{
					$this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] = "asc";
				}
				else
				{
					$this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] = "desc";
				}

				// Default forum order settings
				if($this->lat->input['sort_by'] != "" && $this->lat->input['sort_by'] != $this->lat->cache['forum'][$this->lat->input['id']]['topic_order'])
				{
					$order_url .= ";sort_by=".$this->lat->input['sort_by'];
				}
				else
				{
					$this->lat->input['sort_by'] = $this->lat->cache['forum'][$this->lat->input['id']]['topic_order'];
				}

				// Did our viewer define an orientation?
				if($this->lat->input['order'] && $this->lat->input['order'] != $this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] && ($this->lat->input['order'] == "asc" || $this->lat->input['order'] == "desc"))
				{
					$order_url .= ";order=".$this->lat->input['order'];
				}
				else
				{
					$this->lat->input['order'] = $this->lat->cache['forum'][$this->lat->input['id']]['topic_or'];
				}

				// Placement of the orientation icon
				if($this->lat->input['order'] == "asc")
				{
					$img[$this->lat->input['sort_by']] = " <img src=\"{$this->lat->image_url}order_asc.png\" alt=\"^\" />";
				}
				else
				{
					$img[$this->lat->input['sort_by']] = " <img src=\"{$this->lat->image_url}order_dsc.png\" alt=\"v\" />";
				}

				// Sort links
				$sort_link_order['creator'] = ";sort_by=creator";
				$sort_link_order['topic_title'] = ";sort_by=topic_title";
				$sort_link_order['topic_date'] = ";sort_by=topic_date";
				$sort_link_order['views'] = ";sort_by=views";
				$sort_link_order['replies'] = ";sort_by=replies";
				$sort_link_order['last_date'] = ";sort_by=last_date";

				// Order for sort links
				if($this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] == "desc")
				{
					$sort_link_order['creator'] .= ";order=asc";
					$sort_link_order['topic_title'] .= ";order=asc";
					$sort_link_order['topic_date'] .= ";order=asc";
					$sort_link_order['views'] .= ";order=asc";
					$sort_link_order['replies'] .= ";order=asc";
					$sort_link_order['last_date'] .= ";order=asc";
				}
				$sort_link_order[$this->lat->input['sort_by']] = "";

				// Do we need to add sort by to the link if its not the forum default?
				if($this->lat->cache['forum'][$this->lat->input['id']]['topic_order'] != $this->lat->input['sort_by'])
				{
					$sort_link_order[$this->lat->input['sort_by']] = ";sort_by={$this->lat->input['sort_by']}";
				}

				// Do we need to add orientation to the link if its not the forum default?
				if($this->lat->input['order'] == "asc" && $this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] != "desc")
				{
					$sort_link_order[$this->lat->input['sort_by']] .= ";order=desc";
				}
				elseif($this->lat->input['order'] == "desc" && $this->lat->cache['forum'][$this->lat->input['id']]['topic_or'] != "asc")
				{
					$sort_link_order[$this->lat->input['sort_by']] .= ";order=asc";
				}

				// What kind of topics are we viewing?
				if($this->lat->input['state'] != "")
				{
					$sort_link_order['st'] .= ";state=".$this->lat->input['state'];
					$order_url .= ";state=".$this->lat->input['state'];
				}

				// Not viewing the first page, add the skip value
				if($this->lat->input['st'])
				{
					$sort_link_order['st'] .= ";st=".$this->lat->input['st'];
				}

				// Which order is selected (adds icon and reorders)
				switch($this->lat->input['sort_by'])
				{
					case "creator":
						$order_query = "u.name";
						break;
					case "topic_title":
						$order_query = "t.title";
						break;
					case "topic_date":
						$order_query = "t.start_time";
						break;
					case "views":
						$order_query = "t.views";
						break;
					case "replies":
						$order_query = "t.posts";
						break;
					default:
						$order_query = "t.last_time";
						break;
				}
				$orderlink['creator'] = "";
				$orderlink['topic_title'] = "";
				$orderlink['topic_date'] = "";
				$orderlink['views'] = "";
				$orderlink['replies'] = "";
				$orderlink['last_post'] = "";

				if($this->lat->input['skip'])
				{
					$order_state .= ";skip=".$this->lat->input['skip'];
				}

				if(!($this->lat->user['group']['supermod'] && $pre_mod['see_hidden_topics'] && $pre_mod['see_delete_topics']))
				{
					if($pre_mod['see_delete_topics'])
					{
						$where[] = "t.hidden != 1";
					}
					elseif($pre_mod['see_hidden_topics'])
					{
						$where[] = "t.hidden < 2";
					}
					else
					{
						$where[] = "t.hidden = 0";
					}
				}

				// Query: Take topics from the database
				$query = array("select" => "t.*, r.time",
				               "from"   => "topic t",
				               "user"   => array("u start_", "uu last_"),
				               "left"   => array("user u on(t.start_id=u.id)",
												 "user uu on(t.last_id=uu.id)",
												 "topic_read r on (t.id=r.tid AND r.uid={$this->lat->user['id']})"),
				               "where"  => "(".implode(" AND ", $where).") OR (t.stick LIKE '%,{$this->lat->input['id']},%' OR (t.stick=1 AND t.fid={$this->lat->input['id']}))",
				               "order"  => "t.stick DESC, {$order_query} ".strtoupper($this->lat->input['order']),
				               "limit"  => $this->lat->input['st'].",".$this->lat->user['num_topics']);
			}
			else
			{
				$this->lat->cache['forum'][0]['name'] = $this->lat->lang['search_results'];
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
						$odr = "t.last_time";
						break;
					case "v":
						$odr = "t.views";
						break;
					case "r":
						$odr = "t.posts";
						break;
				}

				$query = array("select" => "t.*",
				               "from"   => "topic t",
				               "user"   => array("u start_", "uu last_"),
				               "left"   => array("user u on(t.start_id=u.id)",
												 "user uu on(t.last_id=uu.id)"),
							   "where"  => "t.hidden=0 AND t.id in ({$ids})",
				               "order"  => $odr." ".$ort,
				               "limit"  => $this->lat->input['st'].",".$this->lat->user['num_topics']);
			}


			while($topic = $this->lat->sql->query($query))
			{
				// Who is the author?
				if($topic['start_id'])
				{
					$topic['author'] = $topic['start_username'];
				}
				else
				{
					$topic['author'] = $topic['start_name'];
				}

				$topic['author'] = $this->lat->show->make_username($topic, "start_", "start_name");

				// Hot topic?
				if($topic['posts'] >= $this->lat->cache['config']['hot_topic'])
				{
					$topic['hot_topic'] = "h";
				}

				// Poll or topic?
				if($topic['poll'])
				{
					$pt = "poll";
				}
				else
				{
					$pt = "topic";
				}

				$topic['last'] = $this->lat->show->make_time($this->lat->user['long_date'], $topic['last_time'])."<br />{$this->lat->lang['by']} ".$this->lat->show->make_username($topic, "last_");

				if(!$topic['last_time'])
				{
					$topic['last'] = $this->lat->lang['no_posts'];
				}

				// Read Topics
				if($topic['last_time'] <= $topic['time'] || $this->lat->user['forum_cutoff'] >= $topic['last_time'] || !$this->lat->user['id'])
				{
					$topic['i'] = "<img src=\"{$this->lat->image_url}{$pt}_read{$topic['hot_topic']}.png\" alt=\"\" />";
				}
				else
				{
					$topic['unread'] = "<a href=\"{$this->lat->url}pg=topic;do=unread;id={$topic['id']}\"><img src=\"{$this->lat->image_url}unread.png\" alt=\"{$this->lat->lang['view_first_unread']}\" /></a> ";
					$topic['i'] = "<img src=\"{$this->lat->image_url}{$pt}_unread{$topic['hot_topic']}.png\" alt=\"\" />";
				}

				// Announcement Topic
				if($topic['stick'] != 1 && $topic['stick'])
				{
					$topic['i'] = "<img src=\"{$this->lat->image_url}topic_announce.png\" alt=\"\" />";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['announcement']}</b></span> ";
				}
				// Sticky Topic
				elseif($topic['stick'] == 1)
				{
					$topic['i'] = "<img src=\"{$this->lat->image_url}topic_sticky.png\" alt=\"\" />";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['topic_sticky']}</b></span> ";
				}
				// Moved Topic
				elseif($topic['moved'])
				{
					$topic['i'] = "<img src=\"{$this->lat->image_url}topic_moved.png\" alt=\"\" />";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['moved']}</b></span> ";
				}
				// Locked Topic
				elseif($topic['locked'])
				{
					$topic['i'] = "<img src=\"{$this->lat->image_url}topic_locked.png\" alt=\"\" />";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['locked_prefix']}</b></span> ";
				}

				// Hidden Topic
				if($topic['hidden'] == 1)
				{
					$topic['title'] = "<span class=\"hidden\">{$topic['title']}</span>";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['hidden_prefix']}</b></span> ";
				}
				// Deleted Topic
				elseif($topic['hidden'] > 1)
				{
					$topic['title'] = "<span class=\"hidden\"><s>{$topic['title']}</s></span>";
					$topic['prefix'] = "<span class=\"tiny_text\"><b>{$this->lat->lang['delete_prefix']}</b></span> ";
				}

				// Topic Icon
				if(!$topic['icon'])
				{
					$topic['icon'] = "&nbsp;";
				}
				else
				{
					$topic['icon'] = "<img src=\"{$this->lat->config['STORAGE_PATH']}smilies/{$this->lat->cache['icon'][$topic['icon']]['image']}\" alt=\"\" />";
				}

				// Moved Topic
				if($topic['moved'])
				{
					$topic['posts'] = "-";
					$topic['views'] = "-";
					$topic['unread'] = "";
				}

				if(!$this->lat->user['num_posts'])
				{
					$this->lat->user['num_posts'] = $this->lat->cache['config']['num_posts'];
				}

				$arr_page = array("total"   => $topic['posts'],
				                  "cap"     => $this->lat->user['num_posts'],
				                  "links"   => 5,
				                  "url"     => $this->lat->url."topic={$topic['id']}",
				                  "content" => true);

				$topic['pages'] = " ".$this->lat->show->make_pages($arr_page);
				$topic['posts']--;

				if($this->lat->user['id'])
				{
					// Moderator checkbox
					if($this->lat->user['group']['supermod'] || !empty($pre_mod))
					{
						if($this->lat->input['id'] == $topic['fid'])
						{
							$topic['mod'] = "<div style=\"float: right\"><input type=\"checkbox\" name=\"item[]\" value=\"{$topic['id']}\" /></div>";
						}
						else
						{
							$topic['mod'] = "";
						}
					}
				}

				eval("\$topichtml .=".$this->lat->skin['topic']);
			}

			// There were no topics!
			if(!$topichtml)
			{
				eval("\$topichtml =".$this->lat->skin['no_topics']);
			}
			// There are some topics!
			elseif(!$ids)
			{
				// Are we a moderator?
				if($this->lat->user['id'])
				{
					// Move Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['move_topics'])
					{
						$moderate .= "<option value=\"tmove\" class=\"form_select_blue\">{$this->lat->lang['move']}</option>";
					}

					// Lock Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['lock_topics'])
					{
						$moderate .= "<option value=\"tlock\" class=\"form_select_blue\">{$this->lat->lang['lock']}</option>";
						$moderate .= "<option value=\"tunlock\" class=\"form_select_blue\">{$this->lat->lang['open']}</option>";
					}

					// Delete Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['hide_topics'])
					{
						$moderate .= "<option value=\"thide\" class=\"form_select_blue\">{$this->lat->lang['hide']}</option>";
						$moderate .= "<option value=\"tunhide\" class=\"form_select_blue\">{$this->lat->lang['unhide']}</option>";
					}

					// Move Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['sticky_topics'])
					{
						$moderate .= "<option value=\"tstick\" class=\"form_select_green\">{$this->lat->lang['sticky']}</option>";
						$moderate .= "<option value=\"tunstick\" class=\"form_select_green\">{$this->lat->lang['unsticky']}</option>";
					}

					// Move Topic permission
					if($this->lat->user['group']['supermod'])
					{
						$moderate .= "<option value=\"tannounce\" class=\"form_select_green\">{$this->lat->lang['announce']}</option>";
						$moderate .= "<option value=\"tunannounce\" class=\"form_select_green\">{$this->lat->lang['unannounce']}</option>";
					}

					// Delete Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['delete_topics'])
					{
						$moderate .= "<option value=\"tdelete_confirm\" class=\"form_select_red\">{$this->lat->lang['delete']}</option>";
					}

					// Delete Topic permission
					if($this->lat->user['group']['supermod'] || ($pre_mod['undelete_topics'] && $pre_mod['see_delete_topics']))
					{
						$moderate .= "<option value=\"tundelete\" class=\"form_select_red\">{$this->lat->lang['undelete']}</option>";
					}

					// Delete Topic permission
					if($this->lat->user['group']['supermod'] || $pre_mod['purge_topics'])
					{
						$moderate .= "<option value=\"tpurge_confirm\" class=\"form_select_red\">{$this->lat->lang['purge']}</option>";
					}

					// Output selection box
					if($moderate)
					{
						eval("\$moderate =".$this->lat->skin['forum_moderate']);
					}
				}
			}

			if(!$ids)
			{
				// Exclude sticky topics
				$where[] = "stick=0";

				if($pre_mod['see_delete_topics'])
				{
					$where[] = "t.hidden != 1";
				}
				elseif($pre_mod['see_hidden_topics'])
				{
					$where[] = "t.hidden < 2";
				}
				elseif(!$this->lat->user['group']['supermod'] && !($pre_mod['see_hidden_topics'] && $pre_mod['see_delete_topics']))
				{
					$where[] = "t.hidden = 0";
				}

				// Query: Grab number of topics to calcuate number of pages of topics in the forum
				$query = array("select" => "count(t.id) as num",
				               "from"   => "topic t",
				               "where"  => "(".implode(" AND ", $where).") OR (t.stick LIKE ',%{$this->lat->input['id']},%' OR (t.stick=1 AND t.fid={$this->lat->input['id']}))");

				$pg = $this->lat->sql->query($query);

				$arr_page = array("total"   => $pg['num'],
				                  "cap"     => $this->lat->user['num_topics'],
				                  "links"   => 4,
				                  "url"     => $this->lat->url."forum=".$this->lat->input['id'].$order_url);
			}
			else
			{
				$arr_page = array("total"   => count(explode(",", $ids)),
				                  "cap"     => $this->lat->user['num_topics'],
				                  "links"   => 4,
				                  "url"     => $this->lat->url.$this->lat->search_url);
			}

			$pages = $this->lat->show->make_pages($arr_page);
		}

		if(!$ids)
		{
			$this->user_id = array();
			$group_mod_id = array();
			$user_mod_id = array();

			if(!empty($this->lat->cache['forum_mod']))
			{
				foreach($this->lat->cache['forum_mod'] as $forum_mod)
				{
					if(in_array($this->lat->input['id'], explode(",", $forum_mod['forums'])))
					{
						if($forum_mod['uid'])
						{
							$this->user_id = array_merge($this->user_id,  explode(",", $forum_mod['uid']));
							$user_mod_id = array_merge($user_mod_id,  explode(",", $forum_mod['uid']));
						}

						if($forum_mod['gid'])
						{
							$group_mod_id = array_merge($group_mod_id,  explode(",", $forum_mod['gid']));
						}
					}
				}
			}

			$this->make_permission();
			$forum_html = $this->make_forum($this->lat->input['id']);

			if(!empty($this->user_id) && empty($this->user))
			{
				$this->user = $this->lat->show->get_name_array($this->user_id, "user_");
			}

			if(!empty($user_mod_id))
			{
				foreach($user_mod_id as $uid)
				{
					$user_mod[] = $this->lat->show->make_username($this->user[$uid], "user_");
				}
			}

			if(!empty($group_mod_id))
			{
				foreach($group_mod_id as $gid)
				{
					$group_mod[] = $this->lat->show->make_groupname($gid);
				}
				$lang = $this->lat->lang['groups'];
				$list = implode(", ", $group_mod);
				eval("\$group_mod =".$this->lat->skin['moderator_item']);
			}

			if($user_mod_id)
			{
				$lang = $this->lat->lang['users'];
				$list = implode(", ", $user_mod);
				eval("\$user_mod =".$this->lat->skin['moderator_item']);
			}

			if($user_mod || $group_mod)
			{
				eval("\$moderate_html =".$this->lat->skin['moderator_list']);
			}

			if(!empty($forum_html))
			{
				// Is the display minimized?
				$div = $this->lat->show->div_state("f".$this->lat->input['id']);
				$category = $this->lat->input['id'];
				$ftype = "f";
				$category_name = $this->lat->lang['forums'];

				eval("\$this->lat->output .=".$this->lat->skin['category_forums']);
			}

			if($this->lat->user['id'])
			{
				$uid_get = " AND s.uid!={$this->lat->user['id']}";
			}

			$online = array("pg"  => array("topic", "post", "forum"),
							"on"  => $this->lat->lang['online_forum'],
							"off" => $this->lat->lang['members_off_forum']);

			$active_users .= $this->lat->show->who_online($online);
		}

		if($this->lat->cache['forum'][$this->lat->input['id']]['parent'] || $ids)
		{
			// Do we have permission to create topics?
			if($pre_forum['post_topics'])
			{
				$buttons .= "<a href=\"{$this->lat->url}pg=post;do=topic;id={$this->lat->input['id']};key={$this->lat->user['key']}\"><big><img src=\"{$this->lat->image_url}button_new.png\" alt=\"\" />{$this->lat->lang['make_new_topic']}</big></a>";
			}

			eval("\$this->lat->output .=".$this->lat->skin['topic_list']);

			if(!$ids)
			{
				eval("\$this->lat->output .=".$this->lat->skin['topic_list_foot']);
			}
		}
	}


	// +-------------------------+
	//   Forum List
	// +-------------------------+
	// Outputs all the forums, and their subforums

	function forum_list()
	{
		$this->lat->title = $this->lat->lang['forum_list'];

		// Set array to parents as key
		$this->lat->inc->forum->sort_parent();
		$this->make_permission();

		// Every category...
		if(!empty($this->lat->inc->forum->pforums))
		{
			foreach($this->lat->inc->forum->pforums['0'] as $category)
			{
				if($this->lat->inc->forum->pre_forum[$category]['view_index'])
				{
					$forum_html = $this->make_forum($category);
					$ftype = "fc";
					$category_name = "<a href=\"{$this->lat->url}forum={$category}\">{$this->lat->cache['forum'][$category]['name']}</a>";

					$div = $this->lat->show->div_state("fc".$category);

					eval("\$this->lat->output .=".$this->lat->skin['category_forums']);
				}
			}
		}
		else
		{
			$this->lat->core->error("err_no_permission_forum_list");
		}

		eval("\$this->lat->output .=".$this->lat->skin['category_footer']);
	}


	// +-------------------------+
	//   Generate permissions
	// +-------------------------+
	// Makes permissions up for our forum profiles

	function make_permission()
	{
		// Organize Forum Profile permissions
		if(!empty($this->lat->cache['forum_profile']))
		{
			foreach($this->lat->cache['forum_profile'] as $profile)
			{
				$profile['groups'] = explode(",", $profile['groups']);

				// This is our permission profile!
				if(in_array($this->lat->user['group']['id'], $profile['groups']))
				{
					$profile['forums'] = explode(",", $profile['forums']);

					foreach($profile['forums'] as $fid)
					{
						$this->lat->inc->forum->pre_forum[$fid] = $profile;
					}
				}
			}
		}
	}


	// +-------------------------+
	//   Generate Forums
	// +-------------------------+
	// Makes permissions up for our forum profiles

	function make_forum($id)
	{
		$this->lat->inc->forum->sort_parent();

		if(!empty($this->lat->inc->forum->pforums[$id]))
		{
			if(empty($this->user))
			{
				foreach($this->lat->inc->forum->pforums[$this->lat->cache['forum'][$id]['parent']] as $root)
				{
					if(!empty($this->lat->inc->forum->pforums[$root]))
					{
						foreach($this->lat->inc->forum->pforums[$root] as $mainforum)
						{
							if($this->lat->cache['forum'][$mainforum]['last_id'])
							{
								$this->user_id[] = $this->lat->cache['forum'][$mainforum]['last_id'];
							}
						}
					}
				}

				if(!empty($this->user_id))
				{
					$this->user = $this->lat->show->get_name_array($this->user_id, "user_");
				}
			}

			// Every Forum...
			foreach($this->lat->inc->forum->pforums[$id] as $mainforum)
			{
				if($this->lat->inc->forum->pre_forum[$mainforum]['view_index'] && $this->lat->inc->forum->pre_forum[$mainforum]['view_index'])
				{
					$subforums = "";

					// Every subforum...
					if(!empty($this->lat->inc->forum->pforums[$mainforum]))
					{
						foreach($this->lat->inc->forum->pforums[$mainforum] as $subforum)
						{
							if($this->lat->inc->forum->pre_forum[$subforum]['view_index'])
							{
								eval("\$subforums[] =".$this->lat->skin['subforum']);
							}
						}
					}

					// Validate last poster information
					if(!$this->lat->inc->forum->pre_forum[$mainforum]['view_topics'])
					{
						$postinfo = "<i>{$this->lat->lang['insufficient_permissions']}</i>";
					}
					elseif(!$this->lat->cache['forum'][$mainforum]['last_topic'] || $this->lat->cache['forum'][$mainforum]['last_topic_name'] == "")
					{
						$postinfo = $this->lat->lang['never_posts'];
					}
					else
					{
						$postinfo = $this->lat->lang['last_post_info'];

						$forum_topic = $this->lat->parse->unsafe_text($this->lat->cache['forum'][$mainforum]['last_topic_name']);
						if($this->lat->parse->get_length($forum_topic) > 22)
						{
							$forum_topic = substr($forum_topic, 0, 22)."...";
						}
						$forum_topic = $this->lat->parse->no_text($forum_topic);

						$postinfo = str_replace("<!-- TOPIC -->", "<a href=\"{$this->lat->url}pg=topic;do=unread;id={$this->lat->cache['forum'][$mainforum]['last_topic']}\" title=\"{$this->lat->cache['forum'][$mainforum]['last_topic_name']}\">{$forum_topic}</a>", $postinfo);
						$postinfo = str_replace("<!-- USER -->", $this->lat->show->make_username($this->user[$this->lat->cache['forum'][$mainforum]['last_id']], "user_"), $postinfo);
						$postinfo = str_replace("<!-- TIME -->", $this->lat->show->make_time($this->lat->user['long_date'], $this->lat->cache['forum'][$mainforum]['last_time']), $postinfo);
					}

					// Output subforum list
					if(!empty($subforums))
					{
						$subforums = "<br />".$this->lat->lang['subforums'].implode(", ", $subforums);
					}

					if($this->lat->cache['forum'][$mainforum]['link'] != "")
					{
						$icon = "<img src=\"{$this->lat->image_url}forum_link.png\" alt=\"\" />";
						$this->lat->cache['forum'][$mainforum]['link_clicks'] = intval($this->lat->cache['forum'][$mainforum]['link_clicks']);
						eval("\$forum_html .=".$this->lat->skin['forum_link']);
					}
					else
					{
						// Is the forum unread?
						if(!$this->lat->inc->forum->get_unread($mainforum))
						{
							$icon = "<img src=\"{$this->lat->image_url}forum_read.png\" alt=\"\" />";
						}
						else
						{
							$icon = "<img src=\"{$this->lat->image_url}forum_unread.png\" alt=\"\" />";
						}

						$this->lat->cache['forum'][$mainforum]['replies'] = $this->lat->cache['forum'][$mainforum]['posts'] - $this->lat->cache['forum'][$mainforum]['topics'];

						eval("\$forum_html .=".$this->lat->skin['forum']);
					}
				}
			}
			return $forum_html;
		}
	}
}
?>
