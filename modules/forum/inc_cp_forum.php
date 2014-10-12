<?php if(!defined('LAT')) die("Access Denied.");

class inc_cp_forum
{
	function initialize()
	{
		$this->lat->core->load_class("forum", "forum");
	}


	// +-------------------------+
	//	 Nav Forums
	// +-------------------------+
	// Generate forum navigation... in cp!

	function nav_forums($last_forum)
	{
		if(!$this->lat->cache['forum'][$last_forum]['parent'])
		{
			return;
		}

		// Setup navigation format
		while($this->lat->cache['forum'][$last_forum]['parent'])
		{
			$this->lat->nav_forum[] = array($this->lat->cache['forum'][$last_forum]['name'], "pg=cp_forum;id=".$last_forum);
			$last_forum = $this->lat->cache['forum'][$last_forum]['parent'];
		}

		$this->lat->nav = array_merge((array)$this->lat->nav, (array)array_reverse($this->lat->nav_forum));
	}


	// +-------------------------+
	//	 Option Parse
	// +-------------------------+
	// Parse options from the cp forum dropdown

	function option_parse($fid, $level, $flist="")
	{
		if($fid == $this->lat->input['id'])
		{
			return;
		}

		$option .= "<option value=\"{$fid}\"{$flist[$fid]}>".str_repeat("&nbsp; &nbsp; ", $level)."&raquo; {$this->lat->cache['forum'][$fid]['name']}</option>";

		if(!empty($this->lat->inc->forum->pforums[$fid]))
		{
			foreach($this->lat->inc->forum->pforums[$fid] as $subf)
			{
				$option .= $this->option_parse($subf, $level + 1, $checkboxes);
			}
		}

		return $option;
	}


	// +-------------------------+
	//   Sync Profiles
	// +-------------------------+
	// Remakes forum profiles

	function sync_profiles($add="")
	{
		if(!empty($this->lat->cache['forum_profile']))
		{
			foreach($this->lat->cache['forum_profile'] as $profile)
			{
				$sync_forums = array();
				$sync_groups = array();
				$forums = explode(",", $profile['forums']);
				$groups = explode(",", $profile['groups']);

				if(!empty($add[$profile['id']]['add_forums']))
				{
					$forums = array_merge($forums, $add[$profile['id']]['add_forums']);
				}
				if(!empty($add[$profile['id']]['add_groups']))
				{
					$groups = array_merge($groups, $add[$profile['id']]['add_groups']);
				}

				if(!empty($forums))
				{
					foreach($forums as $fid)
					{
						if($this->lat->cache['forum'][$fid]['id'] && (empty($add[$profile['id']]['rem_forums']) || !in_array($fid, $add[$profile['id']]['rem_forums'])))
						{
							$sync_forums[] = $fid;
						}
					}
				}

				if(!empty($groups))
				{
					foreach($groups as $gid)
					{
						if($this->lat->cache['group'][$gid]['id'] && (empty($add[$profile['id']]['rem_groups']) || !in_array($gid, $add[$profile['id']]['rem_groups'])))
						{
							$sync_groups[] = $gid;
						}
					}
				}
				$update_query = array("update" => "forum_profile",
							  		  "set"    => array("groups" => implode(",", array_unique($sync_groups)),
							   						    "forums" => implode(",", array_unique($sync_forums))),
							   		  "where"  => "id=".$profile['id']);

				$this->lat->sql->query($update_query);
			}
		}

		$this->lat->sql->cache("forum_profile");
	}
}
?>