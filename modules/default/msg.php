<?php if(!defined('LAT')) die("Access Denied.");

class module_msg
{
	function initialize()
	{
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		// User can't PM
		if(!$this->lat->user['group']['maxpm'])
		{
			$this->lat->core->error("err_cant_pm");
		}

		$this->lat->core->load_cache("setting");
		$this->lat->user['pm_folders'] = unserialize($this->lat->user['pm_folders']);
		$this->lat->nav[] = array($this->lat->lang['inbox'], "pg=msg");
		$folder .= "<option value='inbox'>{$this->lat->lang['inbox']} [{$this->lat->user['pm_folders']['inbox']}]</option>";
		$folder .= "<option value='drafts'>{$this->lat->lang['drafts']} [{$this->lat->user['pm_folders']['drafts']}]</option>";
		$folder .= "<option value='sent'>{$this->lat->lang['sent']} [{$this->lat->user['pm_folders']['sent']}]</option>";

		foreach($this->lat->user['pm_folders'] as $fname => $fnum)
		{
			$flink = rawurlencode($fname);
			if(!in_array($fname, array("drafts", "sent", "inbox")))
			{
				$folder .= "<option value='{$flink}'>{$fname} [{$fnum}]</option>";
			}
		}

		$this->lat->output .= <<<LOL
<form action="{$this->lat->url}pg=msg;do=url" method="post" enctype="multipart/form-data">
<div class="bdr_invisible">
	<a href="{$this->lat->url}pg=msg;do=new"><big><img src="{$this->lat->image_url}button_new.png" alt="" />{$this->lat->lang['make_new_pm']}</big></a>
	<a href="{$this->lat->url}pg=msg;do=edit"><big><img src="{$this->lat->image_url}button_talk.png" alt="" />{$this->lat->lang['edit_folders']}</big></a>
	{$this->lat->lang['go_to_folder']} <select name="url" class="form_select" onchange="redirect(this.options[this.selectedIndex].value, '{$this->lat->url}pg=msg;fd=')"><option value="" selected="selected"></option>{$folder}</select> <input type="submit" class="form_button" name="go" value="Go" />
</div>
</form>
<div class="clear"></div>
LOL;
		switch($this->lat->input['do'])
		{
			case "pm_action":
				$this->pm_action();
				break;
			// Action Folder
			case "action_folder":
				$this->action_folder();
				break;
			// Empty Folder
			case "empty_folder":
				$this->empty_folder();
				break;
			// Delete Folder
			case "delete_folder":
				$this->delete_folder();
				break;
			// View PM
			case "view":
				$this->view_pm();
				break;
			// New PM
			case "new":
				$this->new_pm();
				break;
			// Submit PM
			case "submit_pm":
				$this->submit_pm();
				break;
			// Edit Folders
			case "edit":
				$this->edit_folder();
				break;
			// Redirect to URL
			case "url":
				$this->lat->get_input->no_text("url");
				if(!is_null($this->lat->user['pm_folders'][$this->lat->input['url']]))
				{
					$this->lat->core->redirect($this->lat->url."pg=msg;fd=".$this->lat->input['url']);
				}
				$this->lat->core->error("err_input");
				break;
			// Else...
			default:
				$this->folder();
				break;
		}
	}


	// +-------------------------+
	//	 View PM
	// +-------------------------+
	// Shows us a private message

	function view_pm()
	{
		// Make sure we aren't getting a strange input
		if(!$this->lat->get_input->unsigned_int("id"))
		{
			$this->lat->core->error("err_input");
		}

		// Query: Get the private message from the database
		$query = array("select" => "pm.*, p.*, u.avatar_width, u.avatar_height, u.avatar_type, u.registered, u.posts, u.user_title, u.avatar_url, u.avatar_width, u.avatar_height, u.avatar_type, u.gid",
					   "user"   => array("u user_"),
					   "from"   => "kernel_msg pm",
					   "left"   => array("user u on(pm.sent_from=u.id)",
					  					 "user_profile p on(pm.sent_from=p.uid)"),
					   "where"  => "pm.id={$this->lat->input['id']} AND ((pm.sent_from={$this->lat->user['id']} AND pm.folder='sent') OR (pm.sent_to={$this->lat->user['id']} AND pm.folder!='sent'))");

		$post = $this->lat->sql->query($query);

		if(!$this->lat->sql->num())
		{
			$this->lat->core->error("err_no_pm_find");
		}

		if($post['unread'])
		{
			// Query: PM isn't read anymore
			$query = array("update" => "kernel_msg",
						   "set"    => array("unread" => 0),
						   "where"  => "id=".$this->lat->input['id']);

			$this->lat->sql->query($query);

			$this->lat->core->load_class("default", "msg");
			$this->lat->inc->msg->sync_pm();
		}

		if($post['folder'] == "sent")
		{
			$to_pm = $post['sent_to'];
		}
		else
		{
			$to_pm = $post['sent_from'];
		}


		$this->lat->title = $post['title'];

		// Default folder
		if(!in_array($post['folder'], array("inbox", "drafts", "sent")))
		{
			$this->lat->nav[] = $this->lat->input['fd'];
		}
		// Custom folder
		elseif($post['folder'] != "inbox")
		{
			$this->lat->nav[] = $this->lat->lang[$this->lat->input['fd']];
		}

		$this->lat->nav[] = $post['title'];
		$this->lat->title = $post['title'];

		$this->lat->parse->recache(array("fetch" => &$post,
										 "item"  => "signature",
										 "table" => "user_profile",
										 "where" => "uid=".$post['user_id'],
										 "gid"   => $post['user_gid']));

		$this->lat->parse->recache(array("fetch" => &$post,
										 "table" => "kernel_msg",
										 "where" => "id=".$post['id'],
										 "gid"   => $post['user_gid'],
										 "type"  => 2));

		$post['signature_cached'] = $this->lat->show->make_signature($post['signature_cached']);
		$post['profile_buttons'] .= "<a href=\"{$this->lat->url}pg=msg;do=new;id={$post['user_id']}\" class=\"small_button\"><img src=\"{$this->lat->image_url}send_pm.png\" title=\"{$this->lat->lang['send_pm_to_user']}\" alt=\"\" /></a>";

		// Get IM information
		foreach($post as $pname => $pval)
		{
			if(substr($pname, 0, 8) == "profile_")
			{
				if($this->lat->cache['setting'][$pname]['im'] && $pval)
				{
					$im_id = $post['user_id'];
					$im = substr($pname, 8);
					eval("\$post['profile_buttons'] .=".$this->lat->skin['post_im']);
				}
			}
		}

		$post['name'] = $this->lat->show->make_username($post, "user_");
		$profile = $this->lat->show->user_profile($post);

		$post['sent_date'] = $this->lat->show->make_time($this->lat->user['long_date'], $post['sent_date']);

		eval("\$this->lat->output .=".$this->lat->skin['show_pm']);
	}


	// +-------------------------+
	//	 PM action
	// +-------------------------+
	// Deletes, moves, etc to a PM

	function pm_action()
	{
		$this->lat->core->check_key();

		// This isn't a PM folder
		if($this->lat->get_input->no_text("fd") == "" || is_null($this->lat->user['pm_folders'][$this->lat->input['fd']]))
		{
			$this->lat->core->error("err_folder");
		}

		// What PMs are we messing with?
		foreach($this->lat->raw_input as $pm_id => $is_selected)
		{
			if($is_selected && intval(substr($pm_id, 8)) > 0)
			{
				$selected_pms[] = intval(substr($pm_id, 8));
			}
		}

		// No PMs were selected!
		if(empty($selected_pms))
		{
			$this->lat->core->error("err_pm_select");
		}

		$this->lat->get_input->no_text("pm_action");

		// We are moving the PM(s) somewhere!
		if(substr($this->lat->raw_input['pm_action'], 0, 1) == "'")
		{
			$this->lat->raw_input['pm_action'] = substr($this->lat->raw_input['pm_action'], 1);
			$this->lat->get_input->no_text("pm_action");

			if(is_null($this->lat->user['pm_folders'][$this->lat->input['pm_action']]) || $this->lat->input['pm_action'] == "")
			{
				$this->lat->core->error("err_folder");
			}

			// We aren't allowed to send anything to the sent folder!
			if($this->lat->input['pm_action'] == "sent" || $this->lat->input['pm_action'] == "drafts")
			{
				$this->lat->core->error("err_input");
			}

			// Query: Update the PM area
			$query = array("update" => "kernel_msg",
						   "set"    => array("folder" => $this->lat->input['pm_action']),
						   "where"	=> "folder != 'sent' AND folder != 'drafts' AND id IN(".implode(",", $selected_pms).") AND sent_to=".$this->lat->user['id']);

			$this->lat->sql->query($query);
		}
		// Time to balete some pms!
		elseif($this->lat->input['pm_action'] == "delete")
		{
			// Query: Delete the selected PMs
			$query = array("delete" => "kernel_msg",
						   "where"  => "id IN(".implode(",", $selected_pms).") AND ((sent_from={$this->lat->user['id']} AND folder='sent') OR (sent_to={$this->lat->user['id']} AND folder!='sent'))");

			$this->lat->sql->query($query);
		}
		else
		{
			$this->lat->core->error("err_no_select");
		}

		$this->lat->core->load_class("default", "msg");
		$this->lat->inc->msg->sync_pm();

		$this->lat->core->redirect($this->lat->url."pg=msg;fd=".rawurlencode($this->lat->input['fd']), "pms");
	}


	// +-------------------------+
	//	 Submit PM
	// +-------------------------+
	// Inport a PM to the database

	function submit_pm()
	{
		$this->lat->core->check_key_form();
		$save_pm = $this->lat->get_input->whitelist("save_pm", array(0, 1));

		// User has too many PMs
		if($this->lat->user['pm_total'] > ($this->lat->user['group']['maxpm'] - 1))
		{
			$this->lat->core->error("err_max_pms");
		}

		$post_profile = $this->lat->parse->load_profile(2);

		// Data not filled in
		if($this->lat->get_input->ln_text("data") == "")
		{
			$this->lat->form_error[] = "err_message_none";
		}
		// Data too long
		elseif($this->lat->parse->get_length($this->lat->input['data']) > $post_profile['chr'] || strlen($this->lat->input['data']) > 65535)
		{
			$this->lat->form_error[] = "err_message_long";
		}

		// Title not filled in
		if($this->lat->get_input->no_text("subject") == "")
		{
			$this->lat->form_error[] = "err_subject_none";
		}
		// Title too long
		elseif($this->lat->parse->get_length($this->lat->input['subject']) > 50)
		{
			$this->lat->form_error[] = "err_subject_long";
		}

		if($this->lat->get_input->no_text("to") != "")
		{
			// Query: Get user details
			$query = array("select" => "id, gid, pm_total, pm_folders",
						   "from"   => "user",
						   "where"  => "name='{$this->lat->input['to']}'");

			$pm_user = $this->lat->sql->query($query);

			if(!$this->lat->sql->num())
			{
				$this->lat->form_error[] = "err_pm_to_found";
			}
			else
			{
				// User can't PM
				if(!$this->lat->cache['group'][$pm_user['gid']]['maxpm'])
				{
					$this->lat->form_error[] = "err_to_cant_pm";
				}

				// Recipient has too many PMs
				if($pm_user['pm_total'] >= $this->lat->cache['group'][$pm_user['gid']]['maxpm'])
				{
					$this->lat->form_error[] = "err_to_max_pms";
				}

				// Sending to self, has too many PMs
				if($save_pm && $pm_user['id'] == $this->lat->user['id'] && $this->lat->user['pm_total'] >= ($this->lat->user['group']['maxpm'] - 1))
				{
					$this->lat->core->error("err_to_max_pms");
				}
			}
		}
		else
		{
			$this->lat->form_error[] = "err_pm_to_none";
		}

		if($this->lat->user['group']['pm_smi'])
		{
			$this->lat->get_input->whitelist("show_smi", array(0, 1));
		}

		if($this->lat->user['group']['flood_pm'])
		{
			$query = array("select" => "count(m.id) as num",
						   "from"   => "kernel_msg m",
						   "where"  => "(m.sent_from={$this->lat->user['id']} OR m.from_ip='{$this->lat->user['ip']}') AND m.sent_date > ".(time() - $this->lat->user['group']['flood_pm']));

			$msg_check = $this->lat->sql->query($query);

			if($msg_check['num'])
			{
				$this->lat->form_error[] = str_replace("<!-- NUM -->", $this->lat->user['group']['flood_pm'], $this->lat->lang['err_pm_flood']);
			}
		}

		if(!empty($this->lat->form_error) || $this->lat->raw_input['preview'])
		{
			return $this->new_pm();
		}

		$this->lat->core->load_class("default", "msg");

		$this->lat->inc->msg->send_pm(array("to"        => $pm_user['id'],
							 				"title"     => $this->lat->input['subject'],
							 				"data"      => $this->lat->input['data'],
							 				"query"     => $pm_user,
							 				"save_sent" => $save_pm,
							 				"smi"       => $this->lat->input['show_smi'],
											"bb"        => $this->lat->user['group']['pm_bb']));

		$this->lat->core->redirect($this->lat->url."pg=msg", "pm_sent");
	}


	// +-------------------------+
	//	 New Private Message
	// +-------------------------+
	// Opens the composer for a new private message!

	function new_pm()
	{
		// User has too many PMs
		if($this->lat->user['pm_total'] > ($this->lat->user['group']['maxpm'] - 1))
		{
			$this->lat->core->error("err_max_pms");
		}

		$this->lat->core->load_class("default", "content");
		$this->lat->show->js_files[] = $this->lat->config['MODULES_PATH']."default/js_post";
		$this->lat->input['user'] = intval($this->lat->input['user']);

		if($this->lat->get_input->unsigned_int("pm"))
		{
			// Query: Get the PM sender name
			$query = array("select" => "p.*, u.name",
						   "from"   => "kernel_msg p",
						   "left"   => array("user u on(p.sent_from=u.id)"),
						   "where"  => "p.id={$this->lat->input['pm']}  AND ((p.sent_from={$this->lat->user['id']} AND p.folder='sent') OR (p.sent_to={$this->lat->user['id']} AND p.folder!='sent'))");

			$pm = $this->lat->sql->query($query);

			if(!$this->lat->sql->num())
			{
				$this->lat->core->error("err_no_pm_find");
			}

			// Check which bbtags we're going to strip from being quoted
			foreach($this->lat->cache['bbtag'] as $bbtag)
			{
				if($bbtag['no_quote'])
				{
					if($bbtag['opt'])
					{
						while(preg_match("{\[{$bbtag['tag']}=.+?\].+?\[/{$bbtag['tag']}\]}si", $pm['data']))
						{
							$pm['data'] = preg_replace("{\[{$bbtag['tag']}=.+?\].+?\[/{$bbtag['tag']}\]}si", "", $pm['data']);
						}
					}
					else
					{
						while(preg_match("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}", $pm['data']))
						{
							$pm['data'] = preg_replace("{\[{$bbtag['tag']}\](.+?)\[/{$bbtag['tag']}\]}", "", $pm['data']);
						}
					}
				}
			}

			$pm['data'] = preg_replace("{\[/quote\]}", "", $pm['data']);
			$pm['data'] = trim($pm['data']);

			// Is there anything to quote?
			if($pm['data'] != "")
			{
				$this->lat->input['data'] = "[quote={$pm['name']}]{$pm['data']}[/quote]\n";
			}

			// Put in the replying subject
			if(substr($pm['title'], 0, strlen($this->lat->lang['pm_reply_prefix'])) != $this->lat->lang['pm_reply_prefix'])
			{
				$this->lat->input['subject'] = $this->lat->lang['pm_reply_prefix'].substr($pm['title'], 0, (50 - strlen($this->lat->lang['pm_reply_prefix'])));
			}
			else
			{
				$this->lat->input['subject'] = $pm['title'];
			}
			$this->lat->input['to'] = $pm['name'];
		}
		elseif(empty($error) && $this->lat->get_input->unsigned_int("user"))
		{
			// Query: Just a name of a user for the PM
			$query = array("select" => "name",
						   "from"   => "user",
						   "where"  => "id=".$this->lat->input['user']);

			$pm = $this->lat->sql->query($query);
			$this->lat->input['to'] = $pm['name'];
		}

		if(substr($this->lat->input['do'], 0, 7) != "submit_")
		{
			$this->lat->raw_input['show_sig'] = 1;
			$this->lat->raw_input['show_smi'] = 1;
			$this->lat->raw_input['save_pm'] = 1;
		}

		$this->lat->nav[] = $this->lat->lang['making_new_pm'];
		$this->lat->title = $this->lat->lang['making_new_pm'];
		$this->lat->content = $topic['title'];

		if($this->lat->user['group']['pm_bb'])
		{
			$buttons = $this->lat->inc->content->bbtag_buttons();
		}

		$this->lat->get_input->form_checkbox("save_pm");
		$post_settings .= "<label><input type=\"checkbox\" name=\"save_pm\" value=\"1\" class=\"form_check\"{$this->lat->input['save_pm']} /> {$this->lat->lang['save_pm']}</label>";

		if($this->lat->user['group']['pm_smi'])
		{
			$smilies = $this->lat->inc->content->emoticon_table();
			$this->lat->get_input->form_checkbox("show_smi");

			$post_settings .= "<br /><label><input type=\"checkbox\" name=\"show_smi\" value=\"1\" class=\"form_check\"{$this->lat->input['show_smi']} /> {$this->lat->lang['show_smilies']}</label>";
		}


		// Footer
		$post_footer = $this->lat->inc->content->post_footer(2);

		if($this->lat->raw_input['preview'])
		{
			$preview_post = $this->lat->parse->cache($this->lat->input['data'], array("bb" => $this->lat->user['group']['pm_bb'], "smi" => $this->lat->input['show_smi'], "type" => 2));

			$lang_post = $this->lat->lang['preview_pm'];
			eval("\$form_html .=".$this->lat->skin['preview_post']);
		}

		// Output post box and post settings
		$this->lat->get_input->form_checkbox("lock");
		$lang_settings = $this->lat->lang['post_settings'];
		eval("\$form_html .=".$this->lat->skin['post_box']);
		eval("\$form_html .=".$this->lat->skin['posting_buttons']);
		eval("\$this->lat->output .=".$this->lat->skin['pm_table']);
	}

	// +-------------------------+
	//	 Delete Folder
	// +-------------------------+
	// Deletes a folder

	function delete_folder()
	{
		$this->lat->core->check_key();

		// Folder is incorrect
		if($this->lat->get_input->no_text("fd") == "" || is_null($this->lat->user['pm_folders'][$this->lat->input['fd']]))
		{
			$this->lat->core->error("err_folder");
		}

		// Folder exists or still has PMs
		if(in_array($this->lat->input['fd'], array("inbox", "drafts", "sent")) || $this->lat->user['pm_folders'][$this->lat->input['fd']] > 0)
		{
			$this->lat->core->error("err_folder_delete");
		}

		unset($this->lat->user['pm_folders'][$this->lat->input['fd']]);
		ksort($this->lat->user['pm_folders']);
		$pm_folders = serialize($this->lat->user['pm_folders']);

		// Query: Update the folders
		$query = array("update"	=> "user",
					   "set"    => array("pm_folders" => $pm_folders),
					   "where"  => "id=".$this->lat->user['id']);

		$this->lat->sql->query($query);

		$this->lat->core->redirect($this->lat->url."pg=msg;do=edit", "folders");
	}


	// +-------------------------+
	//	 Empty Folder
	// +-------------------------+
	// Deletes all messages within a folder

	function empty_folder()
	{
		$this->lat->core->check_key();

		// Folder is incorrect
		if($this->lat->get_input->no_text("fd") == "" || is_null($this->lat->user['pm_folders'][$this->lat->input['fd']]))
		{
			$this->lat->core->error("err_folder");
		}

		$query = array("delete" => "kernel_msg",
					   "where"  => "folder='{$this->lat->input['fd']}' AND ((sent_from={$this->lat->user['id']} AND folder='sent') OR (sent_to={$this->lat->user['id']} AND folder!='sent'))");

		$this->lat->sql->query($query);

		$this->lat->core->load_class("default", "msg");
		$this->lat->inc->msg->sync_pm();

		$this->lat->core->redirect($this->lat->url."pg=msg;do=edit", "folders");
	}


	// +-------------------------+
	//	 Action Folders
	// +-------------------------+
	// Submits updated information on our folders

	function action_folder()
	{
		$this->lat->core->check_key();

		// Parse the new folder
		if($this->lat->get_input->no_text("new_folder") != "")
		{
			// New folder is too long
			if($this->lat->parse->get_length($this->lat->input['new_folder']) > 50)
			{
				$this->lat->core->error("err_folder_long");
			}

			if(!is_null($this->lat->user['pm_folders'][$this->lat->input['new_folder']]))
			{
				$this->lat->core->error("err_folder_name");
			}

			// Make the folder
			$this->lat->user['pm_folders'][$this->lat->input['new_folder']] = 0;
		}

		for($i=1; $i <= $this->lat->get_input->unsigned_int("total_folders"); $i++)
		{
			$this->lat->get_input->no_text("name_".$i);
			$this->lat->get_input->no_text("change_".$i);

			// New folder is too long
			if($this->lat->parse->get_length($this->lat->input['change_'.$i]) > 50)
			{
				$this->lat->core->error("err_folder_long");
			}

			// One older isn't a folder
			if($this->lat->input['name_'.$i] == "" || $this->lat->input['change_'.$i] == "")
			{
				$this->lat->core->error("err_folder_null");
			}

			if($this->lat->input['name_'.$i] != $this->lat->input['change_'.$i])
			{
				// The folder exists! :(
				if(!is_null($this->lat->user['pm_folders'][$this->lat->input['change_'.$i]]))
				{
					$this->lat->core->error("err_folder_name");
				}

				if(is_null($this->lat->user['pm_folders'][$this->lat->input['name_'.$i]]))
				{
					$this->lat->core->error("err_input");
				}

				// Transfer the pms and delete old folder
				$this->lat->user['pm_folders'][$this->lat->input['change_'.$i]] = $this->lat->user['pm_folders'][$this->lat->input['name_'.$i]];
				unset($this->lat->user['pm_folders'][$this->lat->input['name_'.$i]]);

				// Query: Update PM folders
				$query = array("update"	=> "kernel_msg",
							   "set"    => array("folder" => $this->lat->input['change_'.$i]),
							   "where"  => "folder='{$this->lat->input['name_'.$i]}'");

				$this->lat->sql->query($query);
			}
		}

		ksort($this->lat->user['pm_folders']);

		// Just to set a limit against possible abusers and such. There is NO reason they should have over 50... nobody needs THAT much organization.
		if(count($this->lat->user['pm_folders']) > 50)
		{
			$this->lat->core->error("err_folder_length");
		}

		$pm_folders = serialize($this->lat->user['pm_folders']);

		// Query: Update user pm folders
		$query = array("update" => "user",
					   "set"    => array("pm_folders" => $pm_folders),
					   "where"  => "id=".$this->lat->user['id']);

		$this->lat->sql->query($query);
		$this->lat->core->redirect($this->lat->url."pg=msg;do=edit", "folders");
	}


	// +-------------------------+
	//	 Manage Folders
	// +-------------------------+
	// Displays all of our folders and how we can manage them.

	function edit_folder()
	{
		$this->lat->nav[] = $this->lat->lang['edit_folders'];
		$this->lat->title = $this->lat->lang['edit_folders'];

		foreach($this->lat->user['pm_folders'] as $fname => $fnum)
		{
			if($fnum == 1)
			{
				$action = $this->lat->lang['contain_pm'];
			}
			else
			{
				$action = str_replace("<!-- NUM -->", $fnum, $this->lat->lang['contain_pms']);
			}

			if(!in_array($fname, array("inbox", "drafts", "sent")))
			{
				$folder_id++;

				// Delete the folder
				if(!$fnum)
				{
					$action .= "<a onclick=\"return confirm('{$this->lat->lang['confirm_delete']}')\" href=\"{$this->lat->url}pg=msg;do=delete_folder;key={$this->lat->user['key']};fd=".rawurlencode($fname)."\">[{$this->lat->lang['delete']}]</a>";
				}
				// Empty the folder
				else
				{
					$action .= "<a onclick=\"return confirm('{$this->lat->lang['confirm_empty']}')\" href=\"{$this->lat->url}pg=msg;do=empty_folder;key={$this->lat->user['key']};fd=".rawurlencode($fname)."\">[{$this->lat->lang['empty']}]</a>";
				}

				eval("\$pm_folders .=".$this->lat->skin['folder_row']);
			}
			else
			{
				// Can't delete this!
				if(!$fnum)
				{
					$action .= "[<i>{$this->lat->lang['no_delete']}</i>]";
				}
				// Empty the folder
				else
				{
					$action .= "<a onclick=\"return confirm('{$this->lat->lang['confirm_empty']}')\" href=\"{$this->lat->url}pg=msg;do=empty_folder;key={$this->lat->user['key']};fd=".rawurlencode($fname)."\">[{$this->lat->lang['empty']}]</a>";
				}

				eval("\$main_pm_folders .=".$this->lat->skin['folder_row_def']);
			}
		}

		eval("\$this->lat->output .=".$this->lat->skin['manage_folder']);
	}


	// +-------------------------+
	//	 Folder
	// +-------------------------+
	// Views a folder and all the PMs inside

	function folder()
	{
		if($this->lat->get_input->no_text("fd") == "")
		{
			$this->lat->input['fd'] = "inbox";
		}

		// Folder doesn't exist
		if(is_null($this->lat->user['pm_folders'][$this->lat->input['fd']]))
		{
			$this->lat->core->error("err_folder");
		}

		$this->lat->title = $this->lat->lang['inbox'];

		// Default folder
		if(!in_array($this->lat->input['fd'], array("inbox", "drafts", "sent")))
		{
			$this->lat->nav[] = $this->lat->input['fd'];
			$this->lat->title = $this->lat->input['fd'];
		}
		// Custom folder
		elseif($this->lat->input['fd'] != "inbox")
		{
			$this->lat->nav[] = $this->lat->lang[$this->lat->input['fd']];
			$this->lat->title = $this->lat->lang[$this->lat->input['fd']];
		}

		$this->lat->get_input->unsigned_int("st");
		$content = 20;

		$arr_page = array("total"   => $this->lat->user['pm_folders'][$this->lat->input['fd']],
		                  "cap"     => $content,
		                  "links"   => 4,
		                  "url"     => $this->lat->url."pg=msg;fd=".$this->lat->input['fd']);

		$pages = $this->lat->show->make_pages($arr_page);

		// No PMs found
		if(!$this->lat->user['pm_folders'][$this->lat->input['fd']])
		{
			eval("\$pm_html .=".$this->lat->skin['no_pms']);
		}
		else
		{
			// Query: Get PMs from the database to list
			$query = array("select" => "p.id, p.title, p.data, p.unread, p.sent_date",
						   "user"   => array("u from_", "uu sent_"),
						   "from"   => "kernel_msg p",
						   "left"   => array("user u on(p.sent_from=u.id)",
						  					 "user uu on(p.sent_to=uu.id)"),
						   "where"  => "p.folder='{$this->lat->input['fd']}' AND ((p.sent_from={$this->lat->user['id']} AND p.folder='sent') OR (p.sent_to={$this->lat->user['id']} AND p.folder!='sent'))",
						   "limit"  => $this->lat->input['st'].",".$content,
						   "order"  => "unread DESC, sent_date DESC");

			while($pm = $this->lat->sql->query($query))
			{
				// Unread PM
				if($pm['unread'])
				{
					$pm['title'] = "<b>{$pm['title']}</b>";
				}

				$pm['unread']++;

				$pm['sent_date'] = $this->lat->show->make_time($this->lat->user['short_date'], $pm['sent_date']);

				// Determine the name of the sender
				if($this->lat->input['fd'] == "sent")
				{
					$pm['name'] = $this->lat->show->make_username($pm, "sent_");
				}
				else
				{
					$pm['name'] = $this->lat->show->make_username($pm, "from_");
				}

				eval("\$pm_html .=".$this->lat->skin['pm_row']);
			}
		}

		if(!in_array($this->lat->input['fd'], array("drafts", "sent")))
		{
			// Generate a dropdown
			if($this->lat->input['fd'] != "inbox")
			{
				$folders .= "<option value=\"'inbox\">{$this->lat->lang['inbox']} [{$this->lat->user['pm_folders']['inbox']}]</option>";
			}

			foreach($this->lat->user['pm_folders'] as $fname => $fnum)
			{
				if(!in_array($fname, array("drafts", "sent", "inbox", $this->lat->input['fd'])))
				{
					$folders .= "<option value=\"'{$fname}\">{$fname} [{$fnum}]</option>";
				}
			}

			if($folders != "")
			{
				$folders = "<optgroup label=\"{$this->lat->lang['move_to_folder']}\">{$folders}</optgroup>";
			}
		}

		$encoded_folder = rawurlencode($this->lat->input['name']);

		if($this->lat->input['fd'] == "sent")
		{
			$this->lat->lang['sent_by'] = $this->lat->lang['sent_to'];
		}

		// Infinate space, technically has a limit, but its just so high, most people will never hit it
		if($this->lat->user['group']['maxpm'] == 65535)
		{
			$pm_space = str_replace(array("<!-- NUM -->", "<!-- MAX -->"), array($this->lat->user['pm_total'], "&infin;"), "<div style=\"float: right\" class=\"tiny_text\">{$this->lat->lang['pm_amount']}</div>");
		}
		else
		{
			$bar = $this->lat->show->make_bar(array("value" => $this->lat->user['pm_total'],
													"total" => $this->lat->user['group']['maxpm']));

			$pm_space = "<div style=\"float: right\" class=\"tiny_text\"> {$this->lat->lang['hundred']}</div><div style=\"float: right; width: 100px;\">".$bar."</div><div style=\"float: right\" class=\"tiny_text\"> &nbsp; {$this->lat->lang['zero']} </div>";
			$pm_space .= str_replace(array("<!-- NUM -->", "<!-- MAX -->"), array($this->lat->user['pm_total'], $this->lat->user['group']['maxpm']), "<div style=\"float: right\" class=\"tiny_text\">{$this->lat->lang['pm_amount']}</div>");
		}

		$encoded_folder = rawurlencode($this->lat->input['fd']);
		eval("\$this->lat->output .=".$this->lat->skin['pm_folder_list']);
	}
}
?>
