<?php if(!defined('LAT')) die("Access Denied.");

class inc_msg
{
	function initialize() { }

	// +-------------------------+
	//   Send Email
	// +-------------------------+
	// Sends out an email using SMTP or the PHP function, using incoming data from an array.

	function email($data)
	{
		$this->clrf = chr(13).chr(10);

		if(!$data['from'])
		{
			$data['from'] = $this->lat->cache['config']['mail_email'];
		}

		if(!$data['from_name'])
		{
			$data['from_name'] = $this->lat->cache['config']['script_name'];
		}

		$data['from_name'] .= " ";

		// Replace badness
		$data['text'] = str_replace("\r\n", "\n", $data['text']);
		$data['text'] = str_replace("\r", "\n", $data['text']);
		$data['text'] = str_replace($this->clrf, "\n", $data['text']);
		$data['text'] = str_replace(chr(13), "\n", $data['text']);

		if(!$this->lat->cache['config']['mail_smtp'])
		{
			mail($data['to'], $data['subject'], $data['text']);

			return "The SMTP mailer setting isn't on. The script sent the mail through the php mail method instead.";
		}
		else
		{
			// First things first. Headers!
			$data['headers']  = "MIME-Version: 1.0\n";
			$data['headers'] .= "Content-type: text/plain; charset={$this->lat->lang['charset']}\n";
			$data['headers'] .= "X-Mailer: Latova Mailer\n";
			$data['headers'] .= "X-Priority: 3\n";
			$data['headers'] .= "X-MSMail-Priority: Normal\n";
			$data['headers'] .= "From: {$data['from_name']}<{$data['from']}>\n";
			//$data['headers'] .= "To: {$data['to']}\n";
			$data['headers'] .= "Subject: {$data['subject']}\n";

			$data['smtptext'] = $data['headers']."\n".$data['text'];

			$this->connection = @fsockopen($this->lat->cache['config']['mail_host'], $this->lat->cache['config']['mail_port'], $errno, $errstr, 30);

			// All our connection are belong to us
			if(!$this->connection)
			{
				return $this->smtp_fail($data, "Could not connect to SMTP server. Check port, and mail host settings.");
			}

			$reply = substr($this->smtp_get(), 0, 3);

			if($reply == 220)
			{
				// Helo world!
				if($this->smtp_send("HELO ".$this->lat->cache['config']['mail_host']) != 250)
				{
					return $this->smtp_fail($data, "STMP Server appears to not be in a known state (it did not respond correctly after the 'HELO {$this->lat->cache['config']['mail_host']}' command). This SMTP server is likely incompatible... use the php mailer method instead.");
				}

				if($this->lat->cache['config']['mail_user'] && $this->lat->cache['config']['mail_pass'])
			   	{
				   	// Is our authentication okay?
				   	if($this->smtp_send("AUTH LOGIN") == 334)
				   	{
				   	   	// Is our username okay?
					   	if($this->smtp_send(base64_encode($this->lat->cache['config']['mail_user'])) != 334)
					   	{
							return $this->smtp_fail($data, "STMP Server rejected the username.");
					   	}

					   	// Is our password okay?
					   	if($this->smtp_send(base64_encode($this->lat->cache['config']['mail_pass'])) != 235)
					   	{
							return $this->smtp_fail($data, "STMP Server rejected the password.");
					   	}
				   	}
				   	// Is our... oh wait we're done :)
			   		else
			   		{
				   		return $this->smtp_fail($data, "SMTP server does not appear to support a compatible method of authentication, or it isn't required. Please remove the username and password for SMTP authentication to blank. If it still doesn't work, use the PHP mailer option instead.");
			   		}
		   		}
	   		}

		   	// We just got some mail, I wonder who its from?
			if($this->smtp_send("MAIL FROM:<{$data['from']}>") != 250)
			{
				return $this->smtp_fail($data, "STMP Server rejected the 'from' email address. Check that setting to see if it is correct and valid.");
			}

			// Who's we sending to?
			if($this->smtp_send("RCPT TO:<{$data['to']}>") != 250)
			{
				return $this->smtp_fail($data, "STMP Server rejected the 'to' email address. Check that setting to see if it is correct and valid.");
			}

			// Can we send our message?
			if($this->smtp_send("DATA") != 354)
			{
				return $this->smtp_fail($data, "STMP Server rejected the data retrival command");
			}

			// Thanks dr. smtp! Here comes the message!
			fputs($this->connection, $data['smtptext'].$this->clrf);

			// Finish him!
			if($this->smtp_send(".") != 250)
			{
				return $this->smtp_fail($data, "STMP Server rejected the data end command");
			}

			if($this->smtp_send("quit") != 221)
			{
				return $this->smtp_fail($data, "STMP Server rejected the quit command");
			}

			return true;
		}
	}


	// +-------------------------+
	//   SMTP Get Last Line
	// +-------------------------+
	// Get's the replies from the server.

	function smtp_get()
	{
		// Let's get our lines...
		while($line = fgets($this->connection, 515))
		{
			$return .= $line;

			// We're done?
			if(substr($line,3,1) == " ")
			{
				break;
			}
		}

		return $return;
	}


	// +-------------------------+
	//   SMTP Send Command
	// +-------------------------+
	// Sends a command to our SMTP server, returns the response code.

	function smtp_send($message)
	{
		// Send the message to the smtp sever
		fputs($this->connection, $message.$this->clrf);

		// Return what our server said
		$lines = $this->smtp_get();

		$j = substr($lines, 0, 3);

		return $j;
	}


	// +-------------------------+
	//   SMTP Fail
	// +-------------------------+
	// There would be a problem if we didn't send out an email during this... some data could be lost.
	// The php mail function acts like a backup whenever the stmp encounters a problem...

	function smtp_fail($data, $msg)
	{
		if(!$data['debug'])
		{
			mail($data['to'], $data['subject'], $data['text'], $data['headers']);
		}
		else
		{
			return $msg;
		}
	}


	// +-------------------------+
	//   Send Private Message
	// +-------------------------+
	// Send a private message to someone else

	function send_pm($data)
	{
		if(!$data['folder'])
		{
			$data['folder'] = "inbox";
		}

		if(!$data['from'])
		{
			$data['from'] = $this->lat->user['id'];
		}

		if(!$data['date'])
		{
			$data['date'] = time();
		}


		$pm_cached = $this->lat->parse->cache($data['data'], array("bb" => $data['bb'], "smi" => $data['smi'], "type" => 2));

		// Query: Insert the PM into the database
		$query = array("insert"	 => "kernel_msg",
					   "data"	 => array("sent_to"     => $data['to'],
					  					  "sent_from"   => $data['from'],
						  				  "sent_date"   => $data['date'],
										  "from_ip"     => $this->lat->user['ip'],
										  "folder"      => $data['folder'],
										  "title"       => $data['title'],
										  "data"        => $this->lat->parse->sql_text($data['data']),
										  "data_cached" => $this->lat->parse->sql_text($pm_cached),
										  "smi"         => $data['smi'],
										  "unread"      => 1));

		$this->lat->sql->query($query);

		if($data['save_sent'])
		{
			// Query: Insert the PM into the database
			$query = array("insert"	 => "kernel_msg",
						   "data"	 => array("sent_to"     => $data['to'],
						  					  "sent_from"   => $data['from'],
											  "from_ip"     => $this->lat->user['ip'],
							  				  "sent_date"   => $data['date'],
											  "folder"      => "sent",
											  "title"       => $data['title'],
											  "data"        => $this->lat->parse->sql_text($data['data']),
											  "data_cached" => $this->lat->parse->sql_text($pm_cached),
											  "smi"         => $data['smi']));

			$this->lat->sql->query($query);

			if($data['to'] != $data['from'])
			{
				$this->sync_pm($data['from']);
			}
		}

		$this->sync_pm($data['to'], 1);
	}

	function sync_pm($id=0, $notify=0)
	{
		if($id == $this->lat->user['id'] || !$id)
		{
			$q['pm_folders'] = $this->lat->user['pm_folders'];
			$q['pm_total'] = $this->lat->user['pm_total'];
			$q['id'] = $this->lat->user['id'];
		}
		else
		{
			// Query: Get user details
			$query = array("select" => "pm_total, pm_folders",
						   "from"   => "user",
						   "where"  => "id=".$id);

			$q = $this->lat->sql->query($query);
			$q['id'] = $id;
		}

		if(!is_array($q['pm_folders']))
		{
			$q['pm_folders'] = unserialize($q['pm_folders']);
		}

		$unread = 0;

		// Query: Recount total users
		$query = array("select" => "m.folder, m.unread, m.id",
					   "from"   => "kernel_msg m",
					   "where"  => "(m.sent_from={$q['id']} AND m.folder='sent') OR (m.sent_to={$q['id']} AND m.folder!='sent')");

		while($msg = $this->lat->sql->query($query))
		{
			$msg_count[$msg['folder']]++;
			$total_pm++;
			if($msg['unread'])
			{
				$unread++;
			}
		}

		foreach($q['pm_folders'] as $pmf => $pmv)
		{
			if($msg_count[$pmf])
			{
				$new_folders[$pmf] = $msg_count[$pmf];
			}
			else
			{
				$new_folders[$pmf] = 0;
			}
		}

		if($id == $this->lat->user['id'] || !$id)
		{
			$this->lat->user['pm_folders'] = $new_folders;
			$this->lat->user['pm_total'] = $total_pm;
			$this->lat->user['pm_unread'] = $unread;
		}

		$new_folders = serialize($new_folders);
		$qnotify = array();

		if($notify)
		{
			$qnotify['pm_notify'] = 1;
		}

		// Query: Update the user PM folders and stats
		$query = array("update" => "user",
					   "set"    => array_merge(array("pm_folders" => $new_folders,
													 "pm_total"   => $total_pm,
													 "pm_unread"  => $unread), $qnotify),
					   "where"  => "id=".$q['id']);

		$this->lat->sql->query($query);
	}
}
?>