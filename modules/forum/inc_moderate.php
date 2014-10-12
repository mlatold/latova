<?php if(!defined('LAT')) die("Access Denied.");

class inc_moderate
{
	function initialize()
	{
		$this->lat->core->load_class("forum", "forum");
	}

	// +-------------------------+
	//	 Moderate Topic
	// +-------------------------+
	// Moderate one or more topics

	function topic($data)
	{
		if(!$data['no_clean'])
		{
			$data['id'] = $this->lat->parse->as_array("unsigned_int", $data['id']);
		}

		if(empty($data['id']))
		{
			return false;
		}

		$id = implode(",", $data['id']);

		switch($data['type'])
		{
			// Announce
			case "announce":
				if(!empty($data['forum']))
				{
					$set['stick'] = ",".implode(",", $this->lat->inc->forum->check_forums($data['forum'])).",";
				}
				$set['stick'] = "2".$set['stick'];
				break;
			// UnAnnounce
			case "unannounce":
				$set= array("stick" => "");
				break;
			// Announce
			case "stick":
				$set = array("stick" => 1);
				break;
			// Unstick
			case "unstick":
				$set= array("stick" => "");
				break;
			// Lock
			case "lock":
				$set = array("locked" => $this->lat->parse->whitelist($data['lock'], array(2, 1)));
				break;
			// Unlock
			case "unlock":
				$set = array("locked" => 0);
				break;
			// Delete
			case "delete":
				$set = array("hidden" => time());
				break;
			// Undelete
			case "undelete":
				$set = array("hidden" => 0);
				$data['extra'] = " AND hidden > 1";
				break;
			// Hide
			case "hide":
				$set = array("hidden" => 1);
				$data['extra'] = " AND hidden = 0";
				break;
			// Undelete
			case "unhide":
				$set = array("hidden" => 0);
				$data['extra'] = " AND hidden = 1";
				break;
		}

		if(!empty($set))
		{
			$query = array("update" => "topic",
						   "set"    => $set,
						   "where"  => "id in ({$id})".$data['extra']);

			$this->lat->sql->query($query);

			if(in_array($data['type'], array("hide", "unhide", "delete", "undelete")))
			{
				$query = array("select" => "t.fid",
							   "from"	=> "topic t",
							   "where"  => "t.id in ({$id})");

				while($tcheck = $this->lat->sql->query($query))
				{
					$forums[] = $tcheck['fid'];
				}

				$forums = $this->lat->inc->forum->check_forums($forums);
				$this->lat->inc->forum->sync_forum($forums);
			}

			return true;
		}

		if($data['type'] == "purge")
		{
			// Query: Check forum id
			$query = array("select" => "t.fid",
						   "from"	=> "topic t",
						   "where"  => "t.id in ({$id})");

			while($tcheck = $this->lat->sql->query($query))
			{
				$forums[] = $tcheck['fid'];
			}

			$forums = $this->lat->inc->forum->check_forums($forums);
			if(empty($forums))
			{
				return false;
			}

			// Query: Delete all the replies in the topics
			$query = array("delete" => "topic_reply",
						   "where"  => "tid in({$id})");

			$this->lat->sql->query($query);

			// Query: Delete the topics
			$query = array("delete" => "topic",
						   "where"  => "id in({$id})");

			$this->lat->sql->query($query);

			// Query: Delete moved topics
			$query = array("delete" => "topic",
						   "where"  => "moved in({$id})");

			$this->lat->sql->query($query);

			// Query: Delete polls
			$query = array("delete" => "poll",
						   "where"  => "tid in({$id})");

			$this->lat->sql->query($query);

			// Query: Delete poll voted entries
			$query = array("delete" => "poll_vote",
						   "where"  => "tid in({$id})");

			$this->lat->sql->query($query);

			// Query: Delete topic read entries
			$query = array("delete" => "topic_read",
						   "where"  => "tid in({$id})");

			$this->lat->sql->query($query);

			$this->lat->inc->forum->sync_forum($forums);
			return true;
		}
		elseif($data['type'] == "move")
		{
			$data['forum'] = $this->lat->inc->forum->check_forums($data['forum']);

			if(empty($data['forum']))
			{
				return false;
			}

			$sync_fid[] = $data['forum'][0];

			$query = array("select" => "t.*",
						   "from"	=> "topic t",
						   "where"  => "t.id in({$id})");

			while($topic = $this->lat->sql->query($query))
			{
				$sync_fid[] = $topic['fid'];

				if(!$topic['moved'] && $data['link'])
				{
					$pdata = array("fid"         => $topic['fid'],
								   "title"       => $topic['title'],
								   "icon"        => $topic['icon'],
								   "start_id"    => $topic['start_id'],
								   "start_name"  => $topic['start_name'],
								   "start_time"  => $topic['start_time'],
								   "last_id"     => $topic['last_id'],
								   "last_name"   => $topic['last_name'],
								   "last_time"   => $topic['last_time'],
								   "moved"       => $topic['id']);


					$insert = $this->lat->sql->parse_insert($pdata);
					$insert_data[] = $insert['data'];
				}
			}

			if(!empty($insert_data))
			{
				// Query: Insert link topics into the database
				$this->lat->sql->query(array("pinsert" => "topic",
											 "name"    => $insert['name'],
											 "data"    => $insert_data));
			}

			// Query: Update the fourm IDs to move the topics
			$query = array("update" => "topic",
						   "set"	=> "fid=".$data['forum'][0],
						   "where"  => "id in ({$id})");

			$this->lat->sql->query($query);

			$this->lat->inc->forum->sync_forum($sync_fid);
			return true;
		}

		return false;
	}


	// +-------------------------+
	//	 Delete posts
	// +-------------------------+
	// Delete one or more posts

	function post($data)
	{
		$data['id'] = $this->lat->parse->as_array("unsigned_int", $data['id']);
		if(empty($data['id']))
		{
			return false;
		}

		$id = implode(",", $data['id']);

		switch($data['type'])
		{
			case "purge":
				$tid = $this->get_topic_id($id);

				// Query: Delete the replies from the database
				$query = array("delete" => "topic_reply",
							   "where"  => "id in ({$id})");

				$this->lat->sql->query($query);

				$this->lat->inc->forum->sync_topic($tid);
				return true;
			case "hide":
				$hidden[0] = 1;
				$hidden[1] = 0;
				break;
			case "unhide":
				$hidden[0] = 0;
				$hidden[1] = 1;
				break;
			case "delete":
				$hidden[0] = 2;
				$hidden[1] = 0;
				break;
			case "undelete":
				$hidden[0] = 0;
				$hidden[1] = 2;
				break;
		}

		if(!empty($hidden))
		{
			// Query: Update the fourm IDs to move the topics
			$query = array("update" => "topic_reply",
						   "set"	=> "hidden={$hidden[0]}, hidden_id={$this->lat->user['id']}, hidden_time=".time(),
						   "where"  => "hidden={$hidden[1]} AND id in({$id})");

			$this->lat->sql->query($query);

			$this->lat->inc->forum->sync_topic($this->get_topic_id($id));

			return true;
		}

		return false;
	}

	// +-------------------------+
	//	 Sync Topic from post
	// +-------------------------+
	// Resync topic from one or more post ids

	function get_topic_id($id)
	{
		$query = array("select" => "r.tid",
					   "from"   => "topic_reply r",
					   "where"  => "r.id in ({$id})");

		while($post = $this->lat->sql->query($query))
		{
			$tid[] = $post['tid'];
		}

		return $tid;
	}


	// +-------------------------+
	//	 Check Moderator Permissions
	// +-------------------------+
	// Check to see if they're allowed to perform a moderator task

	function can_mod($forum="", $mod_level="")
	{
		if(!$this->lat->user['id'])
		{
			return false;
		}

		// System moderator? We can do anything!
		if($this->lat->user['group']['supermod'])
		{
			return true;
		}

		// Normal moderator are we? Now we must check.
		if($mod_level && $forum && !empty($this->lat->cache['forums_moderators']))
		{
			foreach($this->lat->cache['forums_moderators'] as $mod_profile)
			{
				if($mod_profile[$mod_level] && in_array($forum, explode(",", $mod_profile['forums'])) && (in_array($this->lat->user['id'], explode(",", $mod_profile['uids'])) || in_array($this->lat->user['group']['id'], explode(",", $mod_profile['groups']))))
				{
					return true;
				}
			}
		}

		return false;
	}
}
?>
