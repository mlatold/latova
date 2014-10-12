<?php if(!defined('LAT')) die("Access Denied.");

class module_ucp
{
	function initialize()
	{
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		$this->lat->core->load_cache("setting");
		$this->lat->core->load_cache("setting_page");
		$this->lat->nav[] = array($this->lat->lang['settings'], "pg=ucp");

		switch($this->lat->input['do'])
		{
			// Submit Avatar
			case "submit_avatar":
				$this->submit_avatar();
				break;
			// Show avatar form
			case "avatar":
				$this->avatar();
				break;
			// Show Signature Form
			case "signature":
				$this->signature();
				break;
			// Submit Signature
			case "submit_signature":
				$this->submit_signature();
				break;
			// Show photo form
			case "photo":
				$this->photo();
				break;
			// Submit Avatar
			case "submit_photo":
				$this->submit_photo();
				break;
			// Password
			case "passwd":
				$this->passwd();
				break;
			// Submit Password
			case "submit_passwd":
				$this->submit_passwd();
				break;
			// Email
			case "email":
				$this->email();
				break;
			// Submit Email
			case "submit_email":
				$this->submit_email();
				break;
			// Config
			case "config":
				$this->config();
				break;
			// Submit Config
			case "submit_config":
				$this->submit_config();
				break;
			// Modify Name
			case "name":
				$this->name();
				break;
			// Submit Modify Name
			case "submit_name":
				$this->submit_name();
				break;
			// Just show the default page
			default:
				$this->default_page();
				break;
		}
	}


	// +-------------------------+
	//	 Name Change
	// +-------------------------+
	// Outputs form and info for changing name

	function name()
	{
		$this->lat->nav[] = $this->lat->lang['change_name'];
		$this->lat->title = $this->lat->lang['change_name'];

		if($this->lat->input['do'] == "name")
		{
			$this->lat->input['name'] = $this->lat->user['name'];
		}

		if($this->lat->cache['config']['name_change_days'] > 0 && $this->lat->cache['config']['name_change_num'] > 0)
		{
			$query = array("select" => "count(h.uid) as num",
						   "from"   => "user_history h",
						   "where"  => "h.uid={$this->lat->user['id']} AND h.time > ".(time() - ($this->lat->cache['config']['name_change_days'] * 86400)));

			$name = $this->lat->sql->query($query);

			if($name['num'] >= $this->lat->cache['config']['name_change_num'])
			{
				$this->lat->core->error("err_name_limit");
			}

			$name_change = str_replace(array("<!-- NUM -->", "<!-- TOTAL -->", "<!-- DAYS -->"), array($name['num'], $this->lat->cache['config']['name_change_num'], $this->lat->cache['config']['name_change_days']), $this->lat->lang['change_name_info']);
		}
		else
		{
			$this->lat->core->error("err_disabled");
		}

		eval("\$this->lat->output =".$this->lat->skin['name']);
	}


	function submit_name()
	{
		$this->lat->get_input->no_text("name");
		$this->lat->core->check_key_form();

		// No Password
		if(!$this->lat->get_input->no_text("password"))
		{
			$this->lat->form_error[] = "err_no_old_password";
		}

		// Older password
		$old_password = md5($this->lat->user['salt'].$this->lat->input['password']);

		if($old_password != $this->lat->user['pass'] && empty($this->lat->form_error))
		{
			$this->lat->form_error[] = "err_old_password_mismatch";
		}

		if(empty($this->lat->form_error))
		{
			// Our name is too long!
			if($this->lat->parse->get_length($this->lat->raw_input['name']) > 25)
			{
				$this->lat->form_error[] = "err_long_name";
			}
			// With user: prefix
			elseif(substr(html_entity_decode($this->lat->raw_input['name']), 0, 5) == "user:")
			{
				$this->lat->form_error[] = "err_name_invalid";
			}
			// With brackets
			elseif(strstr($this->lat->raw_input['name'], "[") != false || strstr($this->lat->raw_input['name'], "]") != false || strstr($this->lat->raw_input['name'], "&#91;") != false || strstr($this->lat->raw_input['name'], "&#93;") != false)
			{
				$this->lat->form_error[] = "err_name_invalid";
			}
			// Mr. No Name?
			elseif($this->lat->input['name'] == "")
			{
				$this->lat->form_error[] = "err_no_name";
			}
			// The names are the same
			elseif($this->lat->input['name'] == $this->lat->user['name'])
			{
				$this->lat->form_error[] = "err_same_name";
			}
			// Check if our name was taken...
			else
			{
				$query = array("select" => "count(u.id) as taken",
							   "from"   => "user u",
							   "where"  => "u.name='{$this->lat->input['name']}'");

				$check_name = $this->lat->sql->query($query);

				if($check_name['taken'])
				{
					$this->lat->form_error[] = "err_taken_name";
				}
			}
		}

		$query = array("select" => "count(h.uid) as num",
					   "from"   => "user_history h",
					   "where"  => "h.uid={$this->lat->user['id']} AND h.time > ".(time() - ($this->lat->cache['config']['name_change_days'] * 86400)));

		$name = $this->lat->sql->query($query);

		if($name['num'] >= $this->lat->cache['config']['name_change_num'])
		{
			$this->lat->core->error("err_name_limit");
		}

		if(!empty($this->lat->form_error))
		{
			$this->name();
		}
		else
		{
			$query = array("select" => "count(h.uid) as num",
						   "from"   => "user_history h",
						   "where"  => "h.uid={$this->lat->user['id']}");

			$used = $this->lat->sql->query($query);

			if(!$used['num'])
			{
				// Query: Insert the first user history record
				$query = array("insert" => "user_history",
						       "data"   => array("uid"  => $this->lat->user['id'],
												 "name" => $this->lat->user['name'],
												 "time" => 0));
				$this->lat->sql->query($query);
			}

			// Query: Insert this user history record
			$query = array("insert" => "user_history",
					       "data"   => array("uid"  => $this->lat->user['id'],
											 "name" => $this->lat->input['name'],
											 "time" => time()));
			$this->lat->sql->query($query);

			$query = array("update" => "user",
						   "set"    => "name='{$this->lat->input['name']}'",
						   "where"  => "id=".$this->lat->user['id']);

			$this->lat->sql->query($query);

			// Run all reparsing system functions
			foreach($this->lat->cache['page'] as $page)
			{
				if($page['system'])
				{
					$this->lat->core->load_module($page['name']);
					$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "user" => $this->lat->user['id'], "text" => "user:".$this->lat->user['id']));
				}
			}

			$this->lat->core->redirect($this->lat->url."pg=ucp", "name_changed");
		}
	}


	// +-------------------------+
	//	 Submit Config
	// +-------------------------+
	// Generic Submittion method to the database

	function default_page()
	{
		$this->lat->title = $this->lat->lang['settings'];

		$this->default_page_link(0, $this->lat->lang['profile'], "pg=ucp;do=config;sc=profile", $this->lat->lang['about_profile']);
		$this->default_page_link(0, $this->lat->lang['signature'], "pg=ucp;do=signature", $this->lat->lang['about_signature']);
		$this->default_page_link(0, $this->lat->lang['avatar'], "pg=ucp;do=avatar", $this->lat->lang['about_avatar']);
		$this->default_page_link(0, $this->lat->lang['photo'], "pg=ucp;do=photo", $this->lat->lang['about_photo']);

		$this->default_page_link(2, $this->lat->lang['change_email'], "pg=ucp;do=email", $this->lat->lang['about_change_email']);
		$this->default_page_link(2, $this->lat->lang['change_password'], "pg=ucp;do=passwd", $this->lat->lang['about_change_password']);

		if($this->lat->cache['config']['name_change_days'] > 0 && $this->lat->cache['config']['name_change_num'] > 0)
		{
			$this->default_page_link(2, $this->lat->lang['change_name'], "pg=ucp;do=name", $this->lat->lang['about_change_name']);
		}


		foreach($this->lat->cache['setting_page'] as $page)
		{
			$this->default_page_link(1, $page['title'], "pg=ucp;do=config;sc=".$page['name'], $page['description']);
		}

		eval("\$this->lat->output =".$this->lat->skin['main_page']);
	}


	// +-------------------------+
	//	 Default Page Link
	// +-------------------------+
	// Makes each non-standard page link

	function default_page_link($area, $name, $link, $desc)
	{
		$this->menu_num[$area]++;
		if($this->menu_num[$area] % 2 == 1)
		{
			if($this->area_num[1] > 1 || $area != 1)
			{
				$this->menu[$area] .= "<div class=\"clear\" style=\"clear: both\"></div>";
			}
			$float = "left";
		}
		else
		{
			$float = "right";
		}

		eval("\$this->menu[\$area] .= ".$this->lat->skin['main_page_link']);
	}


	// +-------------------------+
	//	 Submit Config
	// +-------------------------+
	// Generic Submittion method to the database

	function submit_config()
	{
		$this->lat->get_input->preg_whitelist("sc", "a-z_");
		$this->lat->core->check_key_form();
		foreach($_POST as $name => $input)
		{
			// If the type matches our supposedly existant section
			if($this->lat->input['sc'] == $this->lat->cache['setting'][$name]['section'])
			{
				// This is an dropdown, check if our option is a valid option
				if($this->lat->cache['setting'][$name]['type'] == 1)
				{
					$option = explode("\n", $this->lat->cache['setting'][$name]['content']);
					$optvalid = array();

					foreach($option as $optval)
					{
						$optval = explode("|", $optval);
						$optvalid[] = $optval[0];
					}

					if(!in_array($input, $optvalid) && !$this->lat->cache['setting'][$name]['required'])
					{
						$input = "";
					}
					elseif(!in_array($input, $optvalid) && $this->lat->cache['setting'][$name]['required'])
					{
						$this->lat->core->error("err_input");
					}
				}

				if($this->lat->cache['setting'][$name]['im'] || $this->lat->cache['setting'][$name]['charlimit'] == 255)
				{
					$strlen = strlen($input);
				}
				else
				{
					$strlen = $this->lat->parse->get_length($input);
				}

				// Does our value exceed maximum length
				if($strlen > $this->lat->cache['setting'][$name]['charlimit'] && $this->lat->cache['setting'][$name]['charlimit'])
				{
					$input = substr($input, 0, $this->lat->cache['setting'][$name]['charlimit']);
				}

				// Validation types
				if($this->lat->cache['setting'][$name]['check'] && $this->lat->cache['setting'][$name]['type'] != 1)
				{
					switch($this->lat->cache['setting'][$name]['check'])
					{
						// Just HTML encode it
						case 1:
							$input = $this->lat->parse->no_text($input);
							break;
						// Email Address
						case 2:
							$input = $this->lat->parse->is_email($input);
							break;
						// This is a website url URL
						case 3:
							$input = $this->lat->parse->no_text($input);

							$lower_url = strtolower($input);
							if(substr($lower_url, 0, 4) == "www." || (substr($lower_url, 0, 7) != "http://" && substr($lower_url, 0, 8) != "https://" && substr($lower_url, 0, 6) != "ftp://" && substr($lower_url, 0, 7) != "news://"))
							{
								$input = "http://".$input;
							}

							if(!$this->lat->parse->is_url($input))
							{
								$input = "";
							}
							break;
						// Numeric Value
						case 4:
							$input = intval($input);
							break;
						// (Advanced) use a regular expression
						case 5:
							$input = preg_replace($this->lat->cache['setting'][$name]['content'], "", $input);
							break;
					}
				}

				// Remove newlines
				if(!$this->lat->cache['setting'][$name]['newline'])
				{
					$input = preg_replace("{\n}s", "", $input);
				}
				// Make newlines into well... HTML newlines for our post
				else
				{
					$input = preg_replace("{\n}s", "<br />", $input);
				}

				// This is a profile input
				if(!$this->lat->cache['setting'][$name]['in_use'])
				{
					$profile_update[$this->lat->cache['setting'][$name]['name']] = $input;
				}
				// This is a user input
				else
				{
					if($name == "long_date" && $input == $this->lat->cache['config']['long_date'])
					{
						$input = "";
					}

					if($name == "short_date" && $input == $this->lat->cache['config']['short_date'])
					{
						$input = "";
					}

					$user_update[$this->lat->cache['setting'][$name]['name']] = $input;
				}
			}
		}

		if(count($profile_update))
		{
			// Query: Update the profile
			$query = array("update" => "user_profile",
						   "set"    => $profile_update,
						   "where"  => "uid=".$this->lat->user['id']);

			$this->lat->sql->query($query);
		}

		if(count($user_update))
		{
			// Query: Update the user record
			$query = array("update"	=> "user",
						   "set"    => $user_update,
						   "where"  => "id=".$this->lat->user['id']);

			$this->lat->sql->query($query);
		}

		$this->lat->core->redirect($this->lat->url."pg=ucp", "config_update");
	}

	// +-------------------------+
	//	 Config
	// +-------------------------+
	// Settings pages for different modules

	function config()
	{
		if($this->lat->get_input->preg_whitelist("sc", "a-z_") == "profile")
		{
			$this->lat->nav[] = $this->lat->lang['profile'];
			$this->lat->title = $this->lat->lang['profile'];
		}
		else
		{
			$this->lat->nav[] = $this->lat->cache['setting_page'][$this->lat->input['sc']]['title'];
			$this->lat->title = $this->lat->lang['settings_prefix'].$this->lat->cache['setting_page'][$this->lat->input['sc']]['title'];
		}

		// Query: Get all profile data
		$query = array("select" => "p.*",
					   "from"   => "user_profile p",
					   "where"  => "p.uid=".$this->lat->user['id']);

		$profiles_fetch = $this->lat->sql->query($query);

		// Merge all user data
		$user_data = array_merge($this->lat->user, $profiles_fetch);
		$this->lat->lang['about_timezone'] = str_replace("<!-- TIME -->", $this->lat->show->make_time("g:i a", time()), $this->lat->lang['about_timezone']);

		// Check each settings for output
		foreach($this->lat->cache['setting'] as $settings)
		{
			if($this->lat->input['sc'] == $settings['section'])
			{
				// Add a star for a required feild, except for dropdowns
				if($settings['required'] && $settings['type'] != 1)
				{
					$settings['title'] .= "*";
				}

				// A URL based input... remove the url!
				$user_data[$settings['name']] = preg_replace("{<br />}s", "\n", $user_data[$settings['name']]);

				if($settings['about'])
				{
					$settings['about'] = "<img onmouseover=\"help('{$settings['about']}', this)\" onmouseout=\"unhelp()\" class=\"help\" src=\"{$this->lat->image_url}help.png\" alt=\"\" />";
				}

				switch($settings['type'])
				{
					// Dropdown
					case 1:
						$options = explode("\n", $settings['content']);

						// If its not required, allow for a null entry
						if(!$settings['required'])
						{
							$settings['content'] = "<option value=\"\"> </option>";
						}

						// Give us each recorded option
						foreach($options as $opt)
						{
							$opt_parsed = explode("|", $opt);

							$selected = "";
							if($opt_parsed[0] == $user_data[$settings['name']])
							{
								$selected = " selected=\"selected\"";
							}

							$settings['content'] .= "<option value=\"{$opt_parsed[0]}\"{$selected}>{$opt_parsed[1]}</option>";
						}

						$settings['content'] = "<select class=\"form_select\" name=\"{$settings['name']}\">{$settings['content']}</select>";

						eval("\$config_html .=".$this->lat->skin['config_field']);

						break;
					// Textbox
					case 2:
						$extra = "";

						// If its zero, then its null
						if(!$user_data[$settings['name']])
						{
							$user_data[$settings['name']] = "";
						}

						// Charlimit
						if($settings['charlimit'])
						{
							$extra .= " maxlength=\"{$settings['charlimit']}\"";
						}

						$settings['content'] = "<input type=\"text\" class=\"form_text\" size=\"45\" name=\"{$settings['name']}\"  value=\"{$user_data[$settings['name']]}\"{$extra} />";

						// Messenger or normal data
						if($settings['im'])
						{
							eval("\$msgr_html .=".$this->lat->skin['config_field']);
						}
						else
						{
							eval("\$config_html .=".$this->lat->skin['config_field']);
						}

						break;
					// Textarea
					case 3:
						$settings['content'] = "<textarea class=\"form_text\" rows=\"5\" cols=\"50\" name=\"{$settings['name']}\">{$user_data[$settings['name']]}</textarea>";
						eval("\$config_html .=".$this->lat->skin['config_field']);

						break;
				}
			}
		}

		$config_html = $msgr_html.$config_html;

		if(!$config_html)
		{
			$this->lat->core->error("err_no_config");
		}

		eval("\$this->lat->output .=".$this->lat->skin['config']);
	}


	// +-------------------------+
	//	 Password Change
	// +-------------------------+
	// Outputs form for changing the password

	function passwd()
	{
		$this->lat->nav[] = $this->lat->lang['change_password'];
		$this->lat->title = $this->lat->lang['change_password'];

		eval("\$this->lat->output =".$this->lat->skin['change_password']);
	}


	// +-------------------------+
	//	 Submit Password Change
	// +-------------------------+
	// Send password change to the database

	function submit_passwd()
	{
		$this->lat->core->check_key_form();

		// No Password
		if(!$this->lat->get_input->no_text("old_password"))
		{
			$this->lat->form_error[] = "err_no_old_password";
		}

		// Older password
		$old_password = md5($this->lat->user['salt'].$this->lat->input['old_password']);

		if($old_password != $this->lat->user['pass'] && empty($this->lat->form_error))
		{
			$this->lat->form_error[] = "err_old_password_mismatch";
		}

		if(empty($this->lat->form_error))
		{
			// No Password!!!
			if(!$this->lat->get_input->no_text("new_password"))
			{
				$this->lat->form_error[] = "err_no_new_password";
			}
			// No Verified Password!!!
			elseif(!$this->lat->get_input->no_text("new_vpassword"))
			{
				$this->lat->form_error[] = "err_no_new_vpass";
			}
			// Too short of a password
			elseif($this->lat->parse->get_length($this->lat->raw_input['new_vpassword']) < 5)
			{
				$this->lat->form_error[] = "err_short_password";
			}
			// Passwords don't match
			elseif($this->lat->input['new_password'] != $this->lat->input['new_vpassword'])
			{
				$this->lat->form_error[] = "err_match_new_password";
			}
			// Passwords are the same!
			elseif($this->lat->input['new_password'] == $this->lat->input['old_password'])
			{
				$this->lat->form_error[] = "err_same_pass";
			}
			elseif(!$this->lat->session->check_password($this->lat->input['new_password']))
			{
				$this->lat->form_error[] = "err_password_too_easy";
			}
		}

		if(!empty($this->lat->form_error))
		{
			return $this->passwd();
		}

		// This password is too salty
		$salt = $this->lat->session->salt();

		// Query: Updating user password
		$query = array("update" => "user",
					   "set"    => array("salt" => $salt,
										 "pass" => md5($salt.$this->lat->input['new_password'])),
					   "where"  => "id=".$this->lat->user['id']);

		$this->lat->sql->query($query);

		$c_pass = $this->lat->session->get_cookie("pass");
		if($this->lat->parse->unsigned_int($this->get_cookie("user")) == $this->lat->user['id'] && $c_pass)
		{
			$this->lat->session->out_cookie("pass", md5($salt.$this->lat->input['new_password']), true);
		}

		$this->lat->core->redirect($this->lat->url."pg=ucp", "password");
	}


	// +-------------------------+
	//	 Email Change
	// +-------------------------+
	// Outputs form for changing the email

	function email()
	{
		$this->lat->nav[] = $this->lat->lang['change_email'];
		$this->lat->title = $this->lat->lang['change_email'];

		eval("\$this->lat->output =".$this->lat->skin['change_email']);
	}


	// +-------------------------+
	//	 Submit Email Change
	// +-------------------------+
	// Send email change to the database

	function submit_email()
	{
		$this->lat->core->check_key_form();

		// No Password
		if(!$this->lat->get_input->no_text("password"))
		{
			$this->lat->form_error[] = "err_no_old_password";
		}

		// Older password
		$old_password = md5($this->lat->user['salt'].$this->lat->input['password']);

		if($old_password != $this->lat->user['pass'] && empty($this->lat->form_error))
		{
			$this->lat->form_error[] = "err_old_password_mismatch";
		}

		if(empty($this->lat->form_error))
		{
			// Emails don't match
			if(!$this->lat->raw_input['new_email'])
			{
				$this->lat->form_error[] = "err_no_new_mail";
			}
			// Everyone makes mistakes...
			elseif(!$this->lat->raw_input['new_vemail'])
			{
				$this->lat->form_error[] = "err_no_vmail";
			}
			// Bad-Mail
			elseif(!$this->lat->get_input->is_email("new_email") || !$this->lat->get_input->is_email("new_vemail") || strlen($this->lat->raw_input['new_email']) > 255)
			{
				$this->lat->form_error[] = "err_bad_mail";
			}
			// Emails don't match
			elseif($this->lat->raw_input['new_email'] != $this->lat->raw_input['new_vemail'])
			{
				$this->lat->form_error[] = "err_match_mail";
			}
			// Everyone makes mistakes...
			elseif($this->lat->input['new_email'] == $this->lat->user['email'])
			{
				$this->lat->form_error[] = "err_same_mail";
			}
			elseif($this->lat->cache['config']['one_email'])
			{
				// Query: Get the email from the database
				$query = array("select" => "count(u.id) as used",
							   "from"   => "user u",
							   "where"  => "u.email='{$this->lat->input['mail']}'");

				$email = $this->lat->sql->query($query);

				if($email['used'])
				{
					$this->lat->show->form_error[] = "err_used_mail";
				}
			}
		}

		if(!empty($this->lat->form_error))
		{
			return $this->email();
		}

		if($this->lat->cache['config']['email_activate'])
		{
			// Query: Update session to log us out
			$query = array("update" => "kernel_session",
						   "set"    => array("uid" => 0),
						   "where"  => "ip='{$this->lat->user['ip']}' AND sid='{$this->lat->user['sid']}'",
						   "limit"  => 1);

			$this->lat->sql->query($query);
			$activation_code = md5(uniqid(microtime()));

			// Query: Set new email
			$query = array("update" => "user",
						   "set"    => array("email"         => $this->lat->input['new_email'],
											 "validate_code" => $activation_code,
											 "validate"      => 1),
						   "where"  => "id=".$this->lat->user['id']);

			$this->lat->sql->query($query);

			// Send the email
			$this->lat->core->load_class("default", "msg");
			$this->lat->lang['email_register'] = str_replace(array("<!-- DO -->", "<!-- URL -->", "<!-- RAW URL -->", "<!-- IP -->", "<!-- CODE -->", "<!-- ID -->", "<!-- NAME -->"), array("submit_activate", $this->lat->url, $this->lat->cache['config']['script_url'], $this->lat->user['ip'], $activation_code, $this->lat->user['id'], $this->lat->user['name']), $this->lat->lang['email_register']);

			$this->lat->inc->msg->email(array("to"      => $this->lat->input['new_email'],
											  "subject" => $this->lat->lang['email_activate'],
											  "text"    => $this->lat->lang['email_register']));

			$this->lat->core->redirect($this->lat->url, "email_validate");
		}
		else
		{
			$query = array("update" => "user",
						   "set"    => array("email" => $this->lat->input['new_email']),
						   "where"  => "id=".$this->lat->user['id']);

			$this->lat->sql->query($query);

			$this->lat->core->redirect($this->lat->url."pg=ucp", "email");
		}
	}


	// +-------------------------+
	//	 Signature
	// +-------------------------+
	// Outputs form for editing a signature

	function signature()
	{
		$content = $this->lat->core->load_class("default", "content");
		$this->lat->nav[] = $this->lat->lang['editing_sig'];
		$this->lat->title = $this->lat->lang['editing_sig'];
		$this->lat->get_input->ln_text("data");

		// Preview the signature
		if($this->lat->raw_input['preview'])
		{
			$sig['signature_cached'] = $this->lat->parse->cache($this->lat->input['data']);
			$sig_title = $this->lat->lang['signature_preview'];
			eval("\$current =".$this->lat->skin['sig_preview']);
		}
		// Just show the signature
		else
		{
			// Query: Get the signature
			$query = array("select" => "p.signature, p.signature_cached, p.signature_reparse",
						   "from"   => "user_profile p",
						   "where"  => "p.uid=".$this->lat->user['id']);

			$sig = $this->lat->sql->query($query);

			$this->lat->parse->recache(array("fetch" => &$sig,
											 "item"  => "signature",
											 "table" => "user_profile",
											 "where" => "uid=".$this->lat->user['id'],
											 "gid"   => $this->lat->user['gid']));

			if($sig['signature'] != "")
			{
				$this->lat->input['data'] = $sig['signature'];

				if($this->lat->cache['config']['sig_height'])
				{
					$sig['signature'] = "<div style='width: 100%; max-height: {$this->lat->cache['config']['sig_height']}px; overflow: auto;'>{$sig['signature']}</div>";
				}

				$sig_title = $this->lat->lang['current_sig'];

				eval("\$current =".$this->lat->skin['sig_preview']);
			}
		}

		// Notify user of maximum signtaure height
		if($this->lat->lang['sig_height'])
		{
			$this->lat->lang['sig_height'] = str_replace("<!-- HEIGHT -->", $this->lat->cache['config']['sig_height'], $this->lat->lang['sig_height']);
		}
		// No signature height
		else
		{
			$this->lat->lang['sig_height'] = "";
		}

		$this->lat->show->js_files[] = $this->lat->config['MODULES_PATH']."default/js_post";
		$buttons = $this->lat->inc->content->bbtag_buttons();
		$smilies = $this->lat->inc->content->emoticon_table();
		$this->lat->get_input->form_checkbox("show_smi");
		$post_footer = $this->lat->inc->content->post_footer(0);
		$lang_settings = $this->lat->lang['enter_sig'];
		$post_extra = "<br /><span class='tiny_text'>{$this->lat->lang['max_sig']}{$this->lat->cache['config']['sig_length']}{$this->lat->lang['chrs_sig']}{$this->lat->lang['sig_height']}</span>";
		eval("\$form_html .=".$this->lat->skin['post_box']);
		eval("\$buttons_submit .=".$this->lat->skin['posting_buttons']);
		eval("\$this->lat->output .=".$this->lat->skin['sig_box']);
	}


	// +-------------------------+
	//	 Submit Signature
	// +-------------------------+
	// Submits a new signature to be posted

	function submit_signature()
	{
		$this->lat->core->check_key_form();
		$this->lat->get_input->ln_text("data");
		$sig_cached = $this->lat->parse->cache($this->lat->input['data']);

		$post_profile = $this->lat->parse->load_profile(0);

		// Data too long for database
		if($this->lat->parse->get_length($this->lat->input['data']) > $post_profile['chr'] || strlen($sig_cached) > 65535)
		{
			$this->lat->form_error[] = "err_signature_long";
		}

		if($this->lat->form_error || $this->lat->raw_input['preview'])
		{
			return $this->signature();
		}

		// Query: Update the signature
		$query = array("update" => "user_profile",
					"set"    => array("signature" =>        $this->lat->parse->sql_text($this->lat->input['data']),
									  "signature_cached" => $this->lat->parse->sql_text($sig_cached)),
					"where"  => "uid={$this->lat->user['id']}");

		$this->lat->sql->query($query);

		$this->lat->core->redirect($this->lat->url."pg=ucp", "signature");
	}


	// +-------------------------+
	//	 Photo
	// +-------------------------+
	// Displays the main page for either uploading or linking a photo

	function photo()
	{
		$this->lat->nav[] = $this->lat->lang['photo'];
		$this->lat->title = $this->lat->lang['photo'];

		// Query: Get the profile photo
		$query = array("select" => "p.photo_url, p.photo_width, p.photo_height, p.photo_type",
					   "from"   => "user_profile p",
					   "where"  => "p.uid=".$this->lat->user['id']);

		$photo = $this->lat->sql->query($query);

		// Get the statistics of the photo
		$this->lat->lang['pic_size'] = str_replace("<!-- SIZE -->", $this->lat->show->make_size($this->lat->cache['config']['photo_size'] * 1024, 1), $this->lat->lang['pic_size']);
		$this->lat->lang['pic_exts'] = str_replace("<!-- EXTS -->", $this->lat->cache['config']['photo_ext'], $this->lat->lang['pic_exts']);
		$this->lat->lang['pic_dim'] = str_replace("<!-- WIDTH -->", $this->lat->cache['config']['photo_width'], $this->lat->lang['pic_dim']);
		$this->lat->lang['pic_dim'] = str_replace("<!-- HEIGHT -->", $this->lat->cache['config']['photo_height'], $this->lat->lang['pic_dim']);

		$make_photo = $this->lat->show->make_photo(array("url"    => $photo['photo_url'],
														 "width"  => $photo['photo_width'],
														 "height" => $photo['photo_height'],
														 "type"   => $photo['photo_type']));

		// Uploaded photo
		if($photo['photo_type'] == 1)
		{
			$photo_display = $this->lat->lang['upload_photo']."({$photo['photo_width']}x{$photo['photo_height']})";
		}
		// Linked Photo
		elseif($photo['photo_type'] == 2)
		{
			$photo_display = $this->lat->lang['link_photo']."({$photo['photo_width']}x{$photo['photo_height']})";
			$photo_link = $photo['photo_url'];
		}
		// No Photo
		else
		{
			$photo_display = $this->lat->lang['no_photo'];
		}

		$photo_display = "<div class=\"tiny_text\"><i>{$photo_display}</i></div>".$make_photo;

		if($this->lat->cache['config']['photo_upload'])
		{
			$change_html = "<div class=\"left\"><img onmouseover=\"help('{$this->lat->lang['help_upload']}', this)\" onmouseout=\"unhelp()\" class=\"help\" src=\"{$this->lat->image_url}help.png\" alt=\"\" />{$this->lat->lang['upload_pick']}</div><div class=\"right\"><input type=\"file\" class=\"form_file\" size=\"30\" name=\"upload_photo\" /></div><div class=\"clear\"></div>";
		}
		if($this->lat->cache['config']['photo_link'])
		{
			$change_html .= "<div class=\"left\"><img src=\"{$this->lat->image_url}help.png\" alt=\"\" onmouseover=\"help('{$this->lat->lang['help_link']}', this)\" onmouseout=\"unhelp()\" class=\"help\" />{$this->lat->lang['link_pick']}</div><div class=\"right\"><input type=\"text\" class=\"form_text\" name=\"link_photo\" maxlength=\"255\" value=\"{$photo_link}\" /></div><div class=\"clear\"></div>";
		}

		if(empty($change_html))
		{
			$this->lat->core->error("err_input");
		}

		eval("\$this->lat->output .=".$this->lat->skin['photo']);
	}


	// +-------------------------+
	//	 Submit Photo
	// +-------------------------+
	// Submits a photo to the database

	function submit_photo()
	{
		$this->lat->core->check_key_form();

		/// Query: Get photo details
		$query = array("select" => "p.photo_url, p.photo_width, p.photo_height, p.photo_type",
					   "from"   => "user_profile p",
					   "where"  => "p.uid=".$this->lat->user['id']);

		$photo = $this->lat->sql->query($query);

		// Uploaded Photo
		if(is_uploaded_file($_FILES['upload_photo']['tmp_name']) && $this->lat->cache['config']['photo_upload'])
		{
			// This is REALLY big
			if(filesize($_FILES['upload_photo']['tmp_name']) > ($this->lat->cache['config']['photo_size'] * 1024 * 5))
			{
				$this->lat->core->error("err_size_photo");
			}

			// The directory isn't writable :(
			if(!is_writeable($this->lat->config['STORAGE_PATH']."photos/"))
			{
				$this->lat->core->error("err_write_photo");
			}

			// Get the extention
			$ext = explode(",", $this->lat->cache['config']['photo_ext']);
			$fileext = strtolower(substr($_FILES['upload_photo']['name'], strrpos($_FILES['upload_photo']['name'], ".") + 1));

			if(!in_array($fileext, $ext))
			{
				$this->lat->core->error("err_extension_photo");
			}

			// Get dimentions of the image
			list($new_photo['width'], $new_photo['height']) = @getimagesize($_FILES['upload_photo']['tmp_name']);
			$new_photo['width'] = $this->lat->parse->unsigned_int($new_photo['width']);
			$new_photo['height'] = $this->lat->parse->unsigned_int($new_photo['height']);

			if(!$new_photo['width'] || !$new_photo['height'])
			{
				$this->lat->core->error("err_corrupt_photo");
			}

			$photo_hash = substr(md5(microtime()), 0, 5);

			$outext = $fileext;
			if($outext == "jpeg")
			{
				$outext = "jpg";
			}

			// Move the image
			$result = $this->lat->show->make_thumb(array("width"      => $new_photo['width'],
														 "height"     => $new_photo['height'],
													     "max_width"  => $this->lat->cache['config']['photo_width'],
													     "max_height" => $this->lat->cache['config']['photo_height'],
													     "quality"    => 7,
													     "input"      => $_FILES['upload_photo']['tmp_name'],
													     "output"     => $this->lat->config['STORAGE_PATH']."photos/".$this->lat->user['id']."-".$photo_hash.".".$outext,
													     "input_ext"  => $fileext));

			// Move current file
			if(!$result)
			{
				if(!@move_uploaded_file($_FILES['upload_photo']['tmp_name'], $this->lat->config['STORAGE_PATH']."photos/".$this->lat->user['id']."-".$photo_hash.".".$outext))
				{
					$this->lat->core->error("err_move_photo");
				}
			}

			$photo_size = filesize($this->lat->config['STORAGE_PATH']."photos/".$this->lat->user['id']."-".$photo_hash.".".$outext);

			if($photo_size > $this->lat->cache['config']['photo_size'] * 1024 || !$photo_size)
			{
				@unlink($this->lat->config['STORAGE_PATH']."photos/".$this->lat->user['id']."-".$photo_hash.".".$outext);
				$this->lat->core->error("err_size_photo");
			}

			// Get the new image size
			list($new_photo['width'], $new_photo['height']) = $this->lat->parse->size_image(array($new_photo['width'], $new_photo['height']), array($this->lat->cache['config']['photo_width'], $this->lat->cache['config']['photo_height']));
			$new_photo['url'] = "{$this->lat->user['id']}-{$photo_hash}.{$outext}";
			$new_photo['type'] = 1;

			$this->lat->sql->query($query);
		}
		// Linked photo
		elseif($this->lat->get_input->no_text("link_photo") && $this->lat->cache['config']['photo_link'])
		{
			// Link is too long for the database
			if(strlen($this->lat->input['link_photo']) > 255)
			{
				$this->lat->core->error("err_long_link");
			}

			list($new_photo['width'], $new_photo['height']) = @getimagesize($this->lat->input['link_photo']);
			$new_photo['width'] = $this->lat->parse->unsigned_int($new_photo['width']);
			$new_photo['height'] = $this->lat->parse->unsigned_int($new_photo['height']);

			// Seems like a valid image...
			if($new_photo['width'] && $new_photo['height'])
			{
				// Autosize the image again
				list($new_photo['width'], $new_photo['height']) = $this->lat->parse->size_image(array($new_photo['width'], $new_photo['height']), array($this->lat->cache['config']['photo_width'], $this->lat->cache['config']['photo_height']));
				$new_photo['url'] = $this->lat->input['link_photo'];
				$new_photo['type'] = 2;
			}
			else
			{
				$this->lat->core->error("err_corrupt_photo");
			}
		}
		elseif(!$this->lat->raw_input['no_photo'])
		{
			$this->lat->core->error("err_no_photo");
		}

		if($photo['photo_type'] == 1)
		{
			@unlink($this->lat->config['STORAGE_PATH']."photos/".$photo['photo_url']);
		}

		// Query: Delete any photo
		$query = array("update" => "user_profile",
					   "set"    => array("photo_url"    => $new_photo['url'],
										 "photo_width"  => $new_photo['width'],
										 "photo_height" => $new_photo['height'],
										 "photo_type"   => $new_photo['type']),
					   "where"  => "uid={$this->lat->user['id']}");

		$this->lat->sql->query($query);

		if(!$new_photo['type'])
		{
			$act = "delete_photo";
		}
		else
		{
			$act = "photo";
		}

		$this->lat->core->redirect($this->lat->url."pg=ucp", $act);
	}


	// +-------------------------+
	//	 Avatar
	// +-------------------------+
	// Displays the main page for avatar selection

	function avatar()
	{
		// Avatar page mode
		if(!$this->lat->get_input->no_text("gallery"))
		{
			$this->lat->title = $this->lat->lang['avatar'];
			$this->lat->nav[] = $this->lat->lang['avatar'];

			// Avatar size details
			$this->lat->lang['pic_size'] = str_replace("<!-- SIZE -->", $this->lat->show->make_size($this->lat->cache['config']['avatar_size'] * 1024, 1), $this->lat->lang['pic_size']);
			$this->lat->lang['pic_exts'] = str_replace("<!-- EXTS -->", $this->lat->cache['config']['avatar_ext'], $this->lat->lang['pic_exts']);
			$this->lat->lang['pic_dim'] = str_replace("<!-- WIDTH -->", $this->lat->cache['config']['avatar_width'], $this->lat->lang['pic_dim']);
			$this->lat->lang['pic_dim'] = str_replace("<!-- HEIGHT -->", $this->lat->cache['config']['avatar_height'], $this->lat->lang['pic_dim']);

			// Make an avatar on the page
			$avatar_display = $this->lat->show->make_avatar(array("url"    => $this->lat->user['avatar_url'],
																  "width"  => $this->lat->user['avatar_width'],
																  "height" => $this->lat->user['avatar_height'],
																  "type"   => $this->lat->user['avatar_type'],
																  "force"  => true));

			// Uploaded avatar
			if($this->lat->user['avatar_type'] == 1)
			{
				$avatar_display = $this->lat->lang['upload_avatar']."({$this->lat->user['avatar_width']}x{$this->lat->user['avatar_height']})<br />".$avatar_display;
			}
			// Linked avatar
			elseif($this->lat->user['avatar_type'] == 2)
			{
				$avatar_display = $this->lat->lang['link_avatar']."({$this->lat->user['avatar_width']}x{$this->lat->user['avatar_height']})<br />".$avatar_display;
				$avatar_link = stripslashes($this->lat->user['avatar_url']);
			}
			// Gallery avatar
			elseif($this->lat->user['avatar_type'] == 3)
			{
				$avatar_display = $this->lat->lang['gallery_avatar']."({$this->lat->user['avatar_width']}x{$this->lat->user['avatar_height']})<br />".$avatar_display;
			}
			// No avatar
			else
			{
				$avatar_display = "<i>{$this->lat->lang['no_avatar']}</i>";
			}

			$avatar_display = "<div class=\"tiny_text\"><i>{$avatar_display}</i></div>";

			// Get gallery directories
			$opendir = opendir($this->lat->config['STORAGE_PATH']."gallery/");
			while ($dir = readdir($opendir))
			{
				if(is_dir($this->lat->config['STORAGE_PATH']."gallery/".$dir) && $dir != "." && $dir != "..")
				{
					$dir = htmlspecialchars($dir);
					$galleries .= "<option value=\"{$dir}\">{$dir}</option>";
				}
			}

			if($this->lat->cache['config']['avatar_upload'])
			{
				$change_html = "<div class=\"left\"><img onmouseover=\"help('{$this->lat->lang['help_upload']}', this)\" onmouseout=\"unhelp()\" class=\"help\" src=\"{$this->lat->image_url}help.png\" alt=\"\" />{$this->lat->lang['upload_pick']}</div><div class=\"right\"><input type=\"file\" class=\"form_file\" size=\"30\" name=\"upload_avatar\" /></div><div class=\"clear\"></div>";
			}
			if($this->lat->cache['config']['avatar_link'])
			{
				$change_html .= "<div class=\"left\"><img onmouseover=\"help('{$this->lat->lang['help_link']}', this)\" onmouseout=\"unhelp()\" class=\"help\" src=\"{$this->lat->image_url}help.png\" alt=\"\" />{$this->lat->lang['link_pick']}</div><div class=\"right\"><input type=\"text\" class=\"form_text\" name=\"link_avatar\" maxlength=\"255\" value=\"{$avatar_link}\" /></div><div class=\"clear\"></div>";
			}
			if($galleries)
			{
				$change_html .= "<div class=\"left\">{$this->lat->lang['gallery_pick']}</div><div class=\"right\"><select name=\"gallery\" class=\"form_select\" onchange=\"redirect(this.options[this.selectedIndex].value, '{$this->lat->url}pg=ucp;do=avatar;gallery=')\"><option value=\"\" selected=\"selected\"></option>{$galleries}</select> <input type=\"submit\" class=\"form_button\" name=\"go\" value=\"{$this->lat->lang['go']}\" /></div><div class=\"clear\"></div>";
			}

			if(empty($change_html))
			{
				$this->lat->core->error("err_input");
			}

			eval("\$this->lat->output .=".$this->lat->skin['avatar']);
		}
		// Gallery page
		else
		{
			// Clean the gallery...
			$this->lat->input['gallery'] = preg_replace("{[\./\\\]}", "", $this->lat->input['gallery']);

			// Check if its a directory
			if(!is_dir($this->lat->config['STORAGE_PATH']."gallery/".$this->lat->input['gallery']) || $this->lat->input['gallery'] == "")
			{
				$this->lat->core->error("err_not_gallery");
			}

			$this->lat->title = $this->lat->lang['viewing_gallery'].$this->lat->input['gallery'];
			$this->lat->nav[] = $this->lat->lang['viewing_gallery'].$this->lat->input['gallery'];

			$gdir = rawurlencode($this->lat->input['gallery']);

			// Open the directory
			$opendir = opendir($this->lat->config['STORAGE_PATH']."gallery/".$this->lat->input['gallery']);
			$ext = explode(",", strtolower($this->lat->cache['config']['gallery_ext']));

			while ($gavatar = readdir($opendir))
			{
				if(in_array(strtolower(substr($gavatar, strrpos($gavatar, ".") + 1)), $ext))
				{
					if($num == $this->lat->cache['config']['gallery_col'])
					{
						$num = 0;
						$row++;
					}
					$num++;

					// The avatar is selected already!
					$selected = "";
					if($this->lat->user['avatar_url'] == $gdir."/".$gavatar)
					{
						$selected = " checked=\"checked\"";
					}

					$td[$row][] = "<label><img src=\"{$this->lat->cache['config']['gallery_url']}{$gdir}/{$gavatar}\" border=\"0\" alt=\"{$gavatar}\" /><br /><input type=\"radio\" class=\"form_check\" name=\"gavatar\" value=\"{$gavatar}\"{$selected} /> <b>".substr($gavatar, 0, strrpos($gavatar, "."))."</b></label><br /><br />";
				}
			}

			if(empty($td))
			{
				$this->lat->core->error("err_no_avatars");
			}

			foreach($td as $val)
			{
				$tr[] = "<td align='center'>".implode("</td><td align='center'>", $val)."</td>";
			}

			$gallery = "<tr>".implode("</tr><tr>", $tr)."</tr>";
			$title = $this->lat->lang['viewing_gallery'].$this->lat->input['gallery'];

			eval("\$this->lat->output .=".$this->lat->skin['avatar_gallery']);
		}
	}


	// +-------------------------+
	//	 Submit Avatar
	// +-------------------------+
	// Sends our avatar to the database.

	function submit_avatar()
	{
		$this->lat->core->check_key_form();

		if($this->lat->raw_input['no_avatar']) { }
		elseif($this->lat->raw_input['go'])
		{
			$this->lat->get_input->no_text("gallery");
			$this->lat->input['gallery'] = preg_replace("{[\./\\\]}", "", $this->lat->input['gallery']);
			if(!is_dir($this->lat->config['STORAGE_PATH']."gallery/".$this->lat->input['gallery']) || $this->lat->input['gallery'] == "")
			{
				$this->lat->core->error("err_not_gallery");
			}

			$this->lat->core->redirect($this->lat->url."pg=ucp;do=avatar;gallery=".$this->lat->input['gallery']);
		}
		// Uploaded Avatar
		elseif(is_uploaded_file($_FILES['upload_avatar']['tmp_name']) && $this->lat->cache['config']['avatar_upload'])
		{
			if($fileext == "gif")
			{
				$size = $this->lat->cache['config']['avatar_size'] * 1024;
			}
			else
			{
				$size = $this->lat->cache['config']['avatar_size'] * 1024 * 5;
			}

			// This is REALLY big
			if(filesize($_FILES['upload_avatar']['tmp_name']) > $size)
			{
				$this->lat->core->error("err_size_avatar");
			}

			// The directory isn't writable :(
			if(!is_writeable($this->lat->config['STORAGE_PATH']."avatars/"))
			{
				$this->lat->core->error("err_write_avatar");
			}

			// Get the extention
			$ext = explode(",", $this->lat->cache['config']['avatar_ext']);
			$fileext = strtolower(substr($_FILES['upload_avatar']['name'], strrpos($_FILES['upload_avatar']['name'], ".") + 1));

			if(!in_array($fileext, $ext))
			{
				$this->lat->core->error("err_extension_avatar");
			}

			// Get dimentions of the image
			list($new_avatar['width'], $new_avatar['height']) = @getimagesize($_FILES['upload_avatar']['tmp_name']);
			$new_avatar['width'] = $this->lat->parse->unsigned_int($new_avatar['width']);
			$new_avatar['height'] = $this->lat->parse->unsigned_int($new_avatar['height']);

			if(!$new_avatar['width'] || !$new_avatar['height'])
			{
				$this->lat->core->error("err_corrupt_avatar");
			}

			$outext = $fileext;
			if($outext == "jpeg")
			{
				$outext = "jpg";
			}

			$avatar_hash = substr(md5(microtime()), 0, 5);

			if($fileext == "gif")
			{
				$result = move_uploaded_file($_FILES['upload_avatar']['tmp_name'], $this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['id']."-".$avatar_hash.".".$outext);

				if(!$result)
				{
					$this->lat->core->error("err_move_avatar");
				}

				list($new_avatar['width'], $new_avatar['height']) = $this->lat->parse->size_image(array($new_avatar['width'], $new_avatar['height']), array($this->lat->cache['config']['avatar_width'], $this->lat->cache['config']['avatar_height']));
				$new_avatar['url'] = "{$this->lat->user['id']}-{$avatar_hash}.{$outext}";
				$new_avatar['type'] = 1;
			}
			else
			{
				$result = $this->lat->show->make_thumb(array("width"      => $new_avatar['width'],
															 "height"     => $new_avatar['height'],
															 "max_width"  => $this->lat->cache['config']['avatar_width'],
															 "max_height" => $this->lat->cache['config']['avatar_height'],
													     	 "quality"    => 7,
															 "input"      => $_FILES['upload_avatar']['tmp_name'],
															 "output"     => $this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['id']."-".$avatar_hash.".".$outext,
															 "input_ext"  => $fileext));

				// Move current file
				if(!$result)
				{
					if(!@move_uploaded_file($_FILES['upload_avatar']['tmp_name'], $this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['id']."-".$avatar_hash.".".$outext))
					{
						$this->lat->core->error("err_move_avatar");
					}
				}

				$avatar_size = filesize($this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['id']."-".$avatar_hash.".".$outext);

				if($avatar_size > $this->lat->cache['config']['avatar_size'] * 1024 || !$avatar_size)
				{
					@unlink($this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['id']."-".$avatar_hash.".".$outext);
					$this->lat->core->error("err_size_avatar");
				}

				// Get the new image size
				list($new_avatar['width'], $new_avatar['height']) = $this->lat->parse->size_image(array($new_avatar['width'], $new_avatar['height']), array($this->lat->cache['config']['avatar_width'], $this->lat->cache['config']['avatar_height']));
				$new_avatar['url'] = "{$this->lat->user['id']}-{$avatar_hash}.{$outext}";
				$new_avatar['type'] = 1;
			}
		}
		elseif($this->lat->get_input->no_text("gallery") && $this->lat->get_input->no_text("gavatar"))
		{
			// Clean the gallery
			$this->lat->input['gallery'] = preg_replace("{[\./\\\]}", "", $this->lat->input['gallery']);

			// Check the directory...
			if(!is_dir($this->lat->config['STORAGE_PATH']."gallery/".$this->lat->input['gallery']) || $this->lat->input['gallery'] == "")
			{
				$this->lat->core->error("err_not_gallery");
			}

			list($new_avatar['width'], $new_avatar['height']) = @getimagesize($this->lat->cache['config']['gallery_url'].$this->lat->input['gallery']."/".$this->lat->input['gavatar']);
			$new_avatar['width'] = $this->lat->parse->unsigned_int($new_avatar['width']);
			$new_avatar['height'] = $this->lat->parse->unsigned_int($new_avatar['height']);

			if(!$new_avatar['width'] || !$new_avatar['height'] || !file_exists($this->lat->config['STORAGE_PATH']."gallery/".$this->lat->input['gallery']."/".$this->lat->input['gavatar']))
			{
				$this->lat->core->error("err_corrupt_avatar");
			}

			$new_avatar['url'] = rawurlencode($this->lat->input['gallery'])."/".$this->lat->input['gavatar'];
			$new_avatar['type'] = 3;
		}
		// Linked Avatar
		elseif($this->lat->get_input->no_text("link_avatar") && $this->lat->cache['config']['avatar_link'])
		{
			// Link is too long for the database
			if(strlen($this->lat->input['link_avatar']) > 255)
			{
				$this->lat->core->error("err_long_link");
			}

			list($new_avatar['width'], $new_avatar['height']) = @getimagesize($this->lat->input['link_avatar']);
			$new_avatar['width'] = $this->lat->parse->unsigned_int($new_avatar['width']);
			$new_avatar['height'] = $this->lat->parse->unsigned_int($new_avatar['height']);

			// Seems like a valid image...
			if($new_avatar['width'] && $new_avatar['height'])
			{
				// Autosize the image again
				list($new_avatar['width'], $new_avatar['height']) = $this->lat->parse->size_image(array($new_avatar['width'], $new_avatar['height']), array($this->lat->cache['config']['avatar_width'], $this->lat->cache['config']['avatar_height']));
				$new_avatar['url'] = $this->lat->input['link_avatar'];
				$new_avatar['type'] = 2;
			}
			else
			{
				$this->lat->core->error("err_corrupt_avatar");
			}
		}
		elseif(!$this->lat->raw_input['no_avatar'])
		{
			$this->lat->core->error("err_no_avatar");
		}

		if($this->lat->user['avatar_type'] == 1)
		{
			@unlink($this->lat->config['STORAGE_PATH']."avatars/".$this->lat->user['avatar_url']);
		}

		// Query: Delete any photo
		$query = array("update" => "user",
					   "set"    => array("avatar_url"    => $new_avatar['url'],
										 "avatar_width"  => $new_avatar['width'],
										 "avatar_height" => $new_avatar['height'],
										 "avatar_type"   => $new_avatar['type']),
					   "where"  => "id={$this->lat->user['id']}");

		$this->lat->sql->query($query);

		if(!$new_avatar['type'])
		{
			$act = "delete_avatar";
		}
		else
		{
			$act = "avatar";
		}

		$this->lat->core->redirect($this->lat->url."pg=ucp", $act);
	}
}
?>
