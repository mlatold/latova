<?php if(!defined('LAT')) die("Access Denied.");

class module_login
{
	function initialize()
	{
		if($this->lat->user['id'] && $this->lat->input['do'] != "logout")
		{
			$this->lat->core->error("err_logged_in");
		}

		$this->lat->nav[] = array($this->lat->lang['login'], "pg=login");

		switch($this->lat->input['do'])
		{
			// Register form & Terms of Agreement
			case "register":
				$this->register();
				break;
			// Create the account
			case "submit_register":
				$this->submit_register();
				break;
			// Manual Activation
			case "activate":
				$this->activate();
				break;
			// Submit Activation
			case "submit_activate":
				$this->submit_activate();
				break;
			// Resend Validation Email
			case "recover":
				$this->recover();
				break;
			// Resend Validation Email
			case "submit_recover":
				$this->submit_recover();
				break;
			// Submit password reset
			case "logout":
				$this->logout();
				break;
			// Submit Login
			case "submit":
				$this->submit();
				break;
			// Login page
			default:
				$this->login();
				break;
		}
	}


	// +-------------------------+
	//   Logout
	// +-------------------------+
	// Kills cookies and logs out our user.

	function logout()
	{
		// You need to be logged in to logout!
		if(!$this->lat->user['id'])
		{
			$this->lat->core->error("err_logged_out");
		}

		$this->lat->core->check_key();

		// Query: Set session to guest
		$query = array("update" => "kernel_session",
					   "set"    => array("uid" => 0),
					   "where"  => "uid=".$this->lat->user['id']);

		$this->lat->sql->query($query);

		$this->lat->session->out_cookie("user", "", true);
		$this->lat->session->out_cookie("pass", "", true);

		$this->lat->core->redirect($this->lat->url, "logged_out");
	}


	// +-------------------------+
	//   Reset Password
	// +-------------------------+
	// Shows us the reset password form... but we don't have to fill in the code or id :)

	function activate($validation=0)
	{
		$this->lat->get_input->preg_whitelist("code", "a-z0-9");

		if($this->lat->input['id'] == 0)
		{
			$this->lat->input['id'] = "";
		}

		if($validation)
		{
			$this->lat->title = $this->lat->lang['activate_account'];
			$this->lat->nav[] = $this->lat->lang['activate_account'];

			eval("\$this->lat->output =".$this->lat->skin['activate']);
		}
		else
		{
			$this->lat->title = $this->lat->lang['reset_password'];
			$this->lat->nav[] = $this->lat->lang['reset_password'];

			eval("\$this->lat->output =".$this->lat->skin['reset_password']);
		}
	}


	// +-------------------------+
	//   Submit Reset
	// +-------------------------+
	// Resets the password n' all

	function submit_activate()
	{
		$this->lat->get_input->unsigned_int("id");
		$this->lat->get_input->preg_whitelist("code", "a-z0-9");

		// Query: Get the user based upon input name and email address
		$query = array("select" => "u.id, u.validate",
					   "from"   => "user u",
					   "where"  => "u.id='{$this->lat->input['id']}' AND u.validate_code='{$this->lat->input['code']}'");

		$user = $this->lat->sql->query($query);

		// Their details don't match... just send them to a form
		if(!$user['id'] || !$this->lat->input['id'] || !$this->lat->input['code'])
		{
			$this->lat->form_error[] = "err_validate_data";
			return $this->activate(1);
		}
		// We're getting a lost password
		elseif(!$user['validate'])
		{
			// No Password!!!
			if(!$this->lat->get_input->no_text("pass"))
			{
				$this->lat->form_error[] = "err_no_password";
			}
			// No Verified Password!!!
			elseif(!$this->lat->get_input->no_text("vpass"))
			{
				$this->lat->form_error[] = "err_no_vpassword";
			}
			// Too big of a password
			elseif($this->lat->parse->get_length($this->lat->raw_input['pass']) > 32)
			{
				$this->lat->form_error[] = "err_long_password";
			}
			// Too short of a password
			elseif($this->lat->parse->get_length($this->lat->raw_input['pass']) < 5)
			{
				$this->lat->form_error[] = "err_short_password";
			}
			// Passwords don't match
			elseif($this->lat->input['pass'] != $this->lat->input['vpass'])
			{
				$this->lat->form_error[] = "err_match_password";
			}
			elseif(!$this->lat->session->check_password($this->lat->input['pass']))
			{
				$this->lat->form_error[] = "err_password_too_easy";
			}
		}

		if(!empty($this->lat->form_error))
		{
			return $this->activate();
		}

		if($user['validate'])
		{
			$set = array("validate"      => 0,
						 "validate_code" => "");
		}
		else
		{
			$salt = $this->lat->session->salt();

			$set = array("salt"          => $salt,
						 "pass"          => md5($salt.$this->lat->input['pass']),
						 "validate_code" => "");

			$c_pass = $this->lat->session->get_cookie("pass");
			if($this->lat->parse->unsigned_int($this->lat->session->get_cookie("user")) == $this->lat->input['id'] && $c_pass)
			{
				$this->lat->session->out_cookie("pass", md5($salt.$this->lat->input['pass']), true);
			}
		}

		// Query: Update password and remove validating details
		$query = array("update" => "user",
					   "set"    => $set,
					   "where"  => "id=".$this->lat->input['id']);

		$this->lat->sql->query($query);

		if($user['validate'])
		{
			$this->lat->core->redirect($this->lat->url."pg=login", "validate_done");
		}
		else
		{
			$this->lat->core->redirect($this->lat->url."pg=login", "new_password_done");
		}
	}


	// +-------------------------+
	//   Recover Account
	// +-------------------------+
	// Shows us the account recovery form

	function recover()
	{
		$this->lat->title = $this->lat->lang['recover'];
		$this->lat->nav[] = $this->lat->lang['recover'];

		$captcha_html = $this->lat->core->captcha();

		eval("\$this->lat->output =".$this->lat->skin['recover']);
	}


	// +-------------------------+
	//   Submit Lost Password Request
	// +-------------------------+
	// Helps to resend activation emails or reset a password

	function submit_recover()
	{
		$this->lat->core->check_key_form();
		$this->lat->core->check_captcha();

		// No Name
		if($this->lat->get_input->no_text("name") == "")
		{
			$this->lat->form_error[] = "err_no_name";
		}

		// No Email
		if(!$this->lat->raw_input['email'])
		{
			$this->lat->form_error[] = "err_no_mail";
		}
		// Somethings fishy about this email...
		elseif($this->lat->get_input->is_email("email") == "" || strlen($this->lat->raw_input['email']) > 255)
		{
			$this->lat->form_error[] = "err_bad_mail";
		}

		if(empty($this->lat->form_error))
		{
			// Query: Get the user based upon input name and email address
			$query = array("select" => "u.id, u.validate",
						   "from"   => "user u",
						   "where"  => "u.name='{$this->lat->input['name']}' AND u.email='{$this->lat->input['email']}'");

			$user = $this->lat->sql->query($query);

			if(!$user['id'])
			{
				$this->lat->form_error[] = "err_no_name_email";
			}
		}

		// Uh oh. Our user did a nono.
		if(!empty($this->lat->form_error))
		{
			return $this->recover();
		}

		// Generate an activation hash
		$activation_code = md5(uniqid(microtime()));

		// Query: Update password and remove validating details
		$query = array("update" => "user",
					   "set"    => array("validate_code" => $activation_code),
					   "where"  => "id=".$user['id']);

		$this->lat->sql->query($query);

		// Send the email
		$this->lat->core->load_class("default", "msg");

		if($user['validate'] == 1)
		{
			$subject = $this->lat->lang['email_activate'];
			$do = "submit_activate";
		}
		else
		{
			$subject = $this->lat->lang['email_reset_password'];
			$do = "activate";
		}

		$this->lat->lang['email_recover'] = str_replace(array("<!-- DO -->", "<!-- URL -->", "<!-- RAW URL -->", "<!-- IP -->", "<!-- CODE -->", "<!-- ID -->", "<!-- NAME -->"), array($do, $this->lat->url, $this->lat->cache['config']['script_url'], $this->lat->user['ip'], $activation_code, $user['id'], $this->lat->input['name']), $this->lat->lang['email_recover']);

		$this->lat->inc->msg->email(array("to"      => $this->lat->input['email'],
										  "subject" => $subject,
										  "text"    => $this->lat->lang['email_recover']));

		if($user['validate'] == 1)
		{
			$this->lat->core->redirect($this->lat->url."pg=login", "validate_sent");
		}
		else
		{
			$this->lat->core->redirect($this->lat->url."pg=login", "password_sent");
		}
	}


	// +-------------------------+
	//   Login
	// +-------------------------+
	// Shows us the login form.

	function login()
	{
		$this->lat->title = $this->lat->lang['login'];
		$this->lat->get_input->form_checkbox("remember");

		if(!$this->lat->input['do'])
		{
			$this->lat->input['refer'] = $this->lat->parse->no_text($_SERVER['HTTP_REFERER']);
		}

		eval("\$this->lat->output =".$this->lat->skin['login']);
	}


	// +-------------------------+
	//   Submit Login
	// +-------------------------+
	// Processes a login

	function submit()
	{
		// No Name
		if(!$this->lat->get_input->no_text("name"))
		{
			$this->lat->form_error[] = "err_no_name";
		}

		// No Password
		if(!$this->lat->get_input->no_text("pass"))
		{
			$this->lat->form_error[] = "err_no_password";
		}

		$abuse = $this->lat->session->check_abuse();

		if($abuse == -1)
		{
			$this->lat->form_error[] = "err_attempts_none";
		}

		if(empty($this->lat->form_error))
		{
			// Query: Check to see if our code and id match
			$query = array("select" => "u.id, u.pass, u.validate, u.salt",
						   "from"   => "user u",
						   "where"  => "u.name='{$this->lat->input['name']}'");

			if($this->lat->sql->num($query))
			{
				$user = $this->lat->sql->query($query);

				// No validating users are permitted to login
				if($user['validate'])
				{
					$this->lat->form_error[] = "err_validating";
				}

				if(md5($user['salt'].$this->lat->input['pass']) != $user['pass'] && empty($this->lat->form_error))
				{
					$this->lat->form_error[] = "err_password_incorrect";
					$this->lat->form_error[] = str_replace("<!-- NUM -->", 4 - $abuse, $this->lat->lang['err_attempts_left']);
					$this->lat->session->add_abuse();
				}
			}
			else
			{
				$this->lat->input['name'] = "";
				$this->lat->form_error[] = "err_name_not_exists";
			}
		}

		$this->lat->get_input->unsigned_int("remember");
		$this->lat->get_input->no_text("refer");

		// Uh oh. Our user did a nono.
		if(!empty($this->lat->form_error))
		{
			return $this->login();
		}
		if(strstr($this->lat->input['refer'], $this->lat->cache['config']['script_url']) == false || strpos(strtolower($this->lat->input['refer']), "pg=login") != false)
		{
			$this->lat->input['refer'] = $this->lat->url;
		}

		// Query: Only one login at a time
		$query = array("delete" => "kernel_session",
					   "where"  => "(uid={$user['id']} OR last_time < ".(time() - ($this->lat->cache['config']['session_length'] * 60)).") AND last_pg!='cp'");

		$this->lat->sql->query($query);

		// Query: Update session to log us in
		$query = array("update" => "kernel_session",
					   "set"    => array("uid"       => $user['id'],
										 "escalated" => 1),
					   "where"  => "ip='{$this->lat->user['ip']}' AND sid='{$this->lat->user['sid']}'",
					   "limit"  => 1);

		$this->lat->sql->query($query);

		// Query: Update users to a new last login time
		$query = array("update" => "user",
					   "set"    => array("last_login" => time()),
					   "where"  => "id=".$user['id']);

		$this->lat->sql->query($query);

		// Give the user a cookie :3
		if($this->lat->get_input->unsigned_int("remember"))
		{
			$this->lat->session->out_cookie("user", $user['id'], true);
			$this->lat->session->out_cookie("pass", md5($user['salt'].$this->lat->input['pass']), true);
		}

		$this->lat->core->redirect($this->lat->input['refer'], "logged_in");
	}


	// +-------------------------+
	//   Registration Form
	// +-------------------------+
	// This will output the form, and also output it again with errors incase our user made a mistake.

	function register($error="")
	{
		$this->lat->title = $this->lat->lang['register'];
		$this->lat->nav[] = $this->lat->lang['register'];

		if($this->lat->input['do'] == "register")
		{
			$time = gettimeofday();
			$this->lat->raw_input['dst'] = $time['dsttime'];
			$this->lat->raw_input['mmail'] = 1;
			$this->lat->raw_input['amail'] = 1;
			$this->lat->raw_input['timezone'] = intval($this->lat->cache['config']['timezone']);
		}

		$this->lat->get_input->form_checkbox("dst");
		$this->lat->get_input->form_checkbox("mmail");
		$this->lat->get_input->form_checkbox("amail");
		$this->lat->get_input->form_checkbox("agree");
		$this->lat->get_input->form_select("bmonth");
		$this->lat->get_input->form_select("bday");
		$this->lat->get_input->form_select("byear");
		$this->lat->get_input->form_select("timezone");

		$year_start = date('Y') - 13;
		$year_end = $year_start - 61;

		for($i=$year_start; $i>$year_end; $i--)
		{
			$year .= "<option value=\"{$i}\"{$this->lat->input['byear'][$i]}>{$i}</option>";
		}

		$captcha_html = $this->lat->core->captcha();

		eval("\$this->lat->output =".$this->lat->skin['register']);
	}


	// +-------------------------+
	//   Registration Submittion
	// +-------------------------+
	// Parses a incoming registration for errors or such

	function submit_register()
	{
		$this->lat->core->check_key_form();
		$this->lat->core->check_captcha();

		// Did we agree to the terms?
		if(!$this->lat->get_input->whitelist("agree", array(0, 1)))
		{
			$this->lat->form_error[] = "err_terms";
		}

		// Our name is too long!
		if($this->lat->parse->get_length($this->lat->raw_input['name']) > 25)
		{
			$this->lat->form_error[] = "err_long_name";
		}
		// With user: prefix
		elseif(substr(html_entity_decode($this->lat->raw_input['name']), 0, 5) == "user:" && substr(html_entity_decode(html_entity_decode($this->lat->raw_input['name'])), 0, 5) == "user:")
		{
			$this->lat->form_error[] = "err_name_invalid";
		}
		// With brackets
		elseif(strstr($this->lat->raw_input['name'], "[") != false || strstr($this->lat->raw_input['name'], "]") != false || strstr($this->lat->raw_input['name'], "&#91;") != false || strstr($this->lat->raw_input['name'], "&#93;") != false)
		{
			$this->lat->form_error[] = "err_name_invalid";
		}
		// Mr. No Name?
		elseif($this->lat->get_input->no_text("name") == "")
		{
			$this->lat->form_error[] = "err_no_name";
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

		// No Password!!!
		if(!$this->lat->get_input->no_text("pass"))
		{
			$this->lat->form_error[] = "err_no_password";
		}
		// No Verified Password!!!
		elseif(!$this->lat->get_input->no_text("vpass"))
		{
			$this->lat->form_error[] = "err_no_vpassword";
		}
		// Too short of a password
		elseif($this->lat->parse->get_length($this->lat->raw_input['pass']) < 5)
		{
			$this->lat->form_error[] = "err_short_password";
		}
		// Passwords don't match
		elseif($this->lat->input['pass'] != $this->lat->input['vpass'])
		{
			$this->lat->form_error[] = "err_password_no_match";
		}
		elseif(!$this->lat->session->check_password($this->lat->input['pass']))
		{
			$this->lat->form_error[] = "err_password_too_easy";
		}

		// No-Mail
		if(!$this->lat->raw_input['email'])
		{
			$this->lat->form_error[] = "err_no_mail";
		}
		// Bad-Mail
		elseif(!$this->lat->get_input->is_email("email") || strlen($this->lat->raw_input['email']) > 255)
		{
			$this->lat->form_error[] = "err_bad_mail";
		}
		elseif($this->lat->cache['config']['one_email'])
		{
			// Query: Get the email from the database
			$query = array("select" => "count(u.id) as used",
						   "from"   => "user u",
						   "where"  => "u.email='{$this->lat->input['email']}'");

			$email = $this->lat->sql->query($query);

			if($email['used'])
			{
				$this->lat->show->form_error[] = $this->lat->lang['err_email_used'];
			}
		}

		$this->lat->get_input->whitelist("timezone", array(-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 6, 7, 8, 9, 9.5, 10, 11, 12));
		$this->lat->get_input->whitelist("mmail", array(1, 0));
		$this->lat->get_input->whitelist("amail", array(1, 0));
		$this->lat->get_input->whitelist("dst", array(0, 1));
		$this->lat->get_input->ranged_int("byear", array((date('Y') - 73), (date('Y') - 13)));
		$this->lat->get_input->ranged_int("bmonth", array(0, 12));
		$this->lat->get_input->ranged_int("bday", array(0, 31));

		if($this->lat->input['byear'] < (date('Y') - 74))
		{
			$this->lat->input['byear'] = 0;
		}

		if(!$this->lat->input['bmonth'] || !$this->lat->input['bday'] || !$this->lat->input['byear'])
		{
			$this->lat->form_error[] = "err_no_birthday";
		}
		elseif(!checkdate($this->lat->input['bmonth'], $this->lat->input['bday'], $this->lat->input['byear']))
		{
			$this->lat->form_error[] = "err_birthday_valid";
		}

		// An error happened. Oh noes!
		if(!empty($this->lat->form_error))
		{
			$this->register();
		}
		else
		{
			$salt = $this->lat->session->salt();
			$activation_code = md5(uniqid(microtime()));

			if(!$this->lat->cache['config']['email_activate'])
			{
				$last_time = time();
			}

			// Import user into the database
			$query = array("insert" => "user",
						   "data"   => array("name"          => $this->lat->input['name'],
											 "user_ip"       => $this->lat->user['ip'],
											 "pass"          => md5($salt.$this->lat->input['pass']),
											 "salt"          => $salt,
											 "email"         => $this->lat->input['email'],
											 "registered"    => time(),
											 "validate"      => $this->lat->cache['config']['email_activate'],
											 "validate_code" => $activation_code,
											 "gid"           => 2,
											 "timezone"      => $this->lat->input['timezone'],
											 "last_login"    => $last_time,
											 "member_mail"   => $this->lat->input['mmail'],
											 "admin_mail"    => $this->lat->input['amail'],
											 "pm_folders"    => "a:3:{s:5:\"inbox\";i:0;s:4:\"sent\";i:0;s:6:\"drafts\";i:0;}",
											 "dst"           => $this->lat->input['dst'],
											 "birthday"      => $this->lat->input['bmonth'].",".$this->lat->input['bday'].",".$this->lat->input['byear']));

			$member_id = $this->lat->sql->query($query);

			// Query: Insert a blank users_profile record
			$query = array("insert" => "user_profile",
						   "data"   => array("uid" => $member_id));

			$this->lat->sql->query($query);

			// Query: Update storage with last user
			$query = array("update" => "kernel_storage",
						   "set"    => array("data" => $this->lat->input['name']),
						   "where"  => "label='stats_last_user'");

			$this->lat->sql->query($query);

			// Query: Update storage with last user id
			$query = array("update" => "kernel_storage",
						   "set"    => array("data" => $member_id),
						   "where"  => "label='stats_last_userid'");

			$this->lat->sql->query($query);

			// Query: Recount total users
			$query = array("select" => "count(u.id) as num",
						   "from"   => "user u");

			$user = $this->lat->sql->query($query);

			// Query: Update storage with total users
			$query = array("update" => "kernel_storage",
						   "set"    => array("data" => $user['num']),
						   "where"  => "label='stats_users'");

			$this->lat->sql->query($query);
			$this->lat->sql->cache("storage");

			if($this->lat->cache['config']['email_activate'])
			{
				$this->lat->core->load_class("default", "msg");

				$this->lat->lang['email_register'] = str_replace(array("<!-- URL -->", "<!-- RAW URL -->", "<!-- IP -->", "<!-- CODE -->", "<!-- ID -->", "<!-- NAME -->"), array($this->lat->url, $this->lat->cache['config']['script_url'], $this->lat->user['ip'], $activation_code, $member_id, $this->lat->input['name']), $this->lat->lang['email_register']);

				$this->lat->inc->msg->email(array("to"	     => $this->lat->input['email'],
								   "subject" => $this->lat->lang['email_activate'],
								   "text"	 => $this->lat->lang['email_register']));

				$this->lat->sql->query($query);

				$this->lat->core->redirect($this->lat->url, "activate");
			}
			else
			{
				// Query: Update session to log us in
				$query = array("update" => "kernel_session",
							   "set"    => array("uid" => $member_id, "escalated" => 1),
							   "where"  => "ip='{$this->lat->user['ip']}' AND sid='{$this->lat->user['sid']}'",
							   "limit"  => 1);

				$this->lat->sql->query($query);

				$this->lat->core->redirect($this->lat->url, "registered");
			}
		}
	}
}
?>