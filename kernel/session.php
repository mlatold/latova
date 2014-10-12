<?php if(!defined('LAT')) die("Access Denied.");

class kernel_session
{
    // +-------------------------+
    //   Session Parsing
    // +-------------------------+
    // Automatically executed upon creation of this class. It tries to find the user's session either passed within
    // a cookie (yum!) or a GET input. It will then create or update the session, and create the base URL for links
    // & scripts within our website to use.

    function initialize()
    {
        if($this->get_cookie("sid")!= "")
        {
            $cookie_sid = $sid = $this->lat->parse->preg_whitelist($this->get_cookie("sid"), "a-z0-9");
        }
        else
        {
            $sid = $this->lat->get_input->preg_whitelist("sid", "a-z0-9");
        }

        $ip = preg_replace("{[^0-9/.]}", "", $_SERVER['REMOTE_ADDR']);

        if($this->lat->cache['config']['bots_on'])
        {
            $bot_list = explode("\n", $this->lat->cache['config']['bots_list']);

            // Compare known bots with our current user agent
            foreach($bot_list as $bot)
            {
                $bot = explode("|", $bot);

                if(preg_match("{{$bot[0]}}i", $_SERVER['HTTP_USER_AGENT']) && $bot != "")
                {
                    $this->lat->user['spider'] = $bot[1]."|".$bot[2];
                    $cookie_sid = $sid = substr($bot[1], 0, 10);
                }
            }
        }

        // Let's hope that we can save one extra query!
        if($sid)
        {
            // Query: Fetch the session with our user too (we hope) :3
            $query = array("select" => "s.ip, s.sid, s.key, s.act, s.spider, s.captcha, s.escalated, u.*",
                           "from"   => "kernel_session s",
                           "left"   => array("user u ON (s.uid=u.id)"),
                           "where"  => "s.ip='{$ip}' AND s.sid='{$sid}'");

            $session_query = $this->lat->sql->query($query);
        }

        // Old session
        if($session_query['sid'])
        {
            $this->lat->user = $session_query;
        }
        // New session
        else
        {
            // Check password cookie
            $c_user = $this->lat->parse->unsigned_int($this->get_cookie("user"));
            $c_pass = $this->get_cookie("pass");

            if($c_user && $c_pass)
            {
                // Query: Get the details of the user we want to be
                $query = array("select" => "u.*",
                               "from"   => "user u",
                               "where"  => "u.id=".$c_user);

                $user_fetch = $this->lat->sql->query($query);

                // Check for password attempt abuse
                $anum = $this->check_abuse($ip);

                // User details are a match!
                if($user_fetch['pass'] == $c_pass && !$user_fetch['validate'] && $anum != -1)
                {
                    $this->lat->user = $user_fetch;

                    // Query: Delete previous sessions logged in as that user, ip, or old
                    $query = array("delete"   => "kernel_session",
                                   "where"    => "(ip='{$ip}' AND uid=0) OR uid={$c_user} OR last_time < ".(time() - ($this->lat->cache['config']['session_length'] * 60)),
                                   "shutdown" => 1);

                    $this->lat->sql->query($query);

                    // Query: Update our last login (cookies count as a login)
                    $query = array("update"   => "user",
                                   "set"      => array("last_login" => time()),
                                   "where"    => "id=".$c_user,
                                   "shutdown" => 1);

                    $this->lat->sql->query($query);
                }
                // Incorrect details
                else
                {
                    $c_user = 0;
                    $c_pass = "";
                    $this->out_cookie("user", "", true);
                    $this->out_cookie("pass", "", true);
                    $this->add_abuse();
                }
            }

            if(!$this->lat->user['id'])
            {
                // Query: Delete other session guests with our IP, or old sessions
                $query = array("delete"   => "kernel_session",
                               "where"    => "(ip='{$ip}' AND uid=0) OR last_time < ".(time() - ($this->lat->cache['config']['session_length'] * 60)));

                $this->lat->sql->query($query);
            }

            // Generate Standard Key - One that doesn't refresh upon pageloads
            $this->lat->user['key'] = substr(md5(uniqid(microtime())), 0, 10);

            // Generate session ID
            if($this->lat->user['spider'])
            {
                $this->lat->user['sid'] = $sid;
            }
            else
            {
                do {
                    $this->lat->user['sid'] = substr(md5(uniqid(microtime())), 0, 10);

                    // Query: Check if the session ID already exists
                    $query = array("select" => "count(uid) as num",
                                   "from"   => "kernel_session",
                                   "where"  => "sid='{$this->lat->user['sid']}'");

                    $exec = $this->lat->sql->query($query);

                } while ($exec['num']);
            }

            $this->create = true;

            // Attempt to send the sid cookie
            $cookie_sid = "";
            $this->out_cookie("sid", $this->lat->user['sid']);
        }

        // Cookies are disabled, use url sid then :(
        if($cookie_sid == "")
        {
            $this->lat->url = $this->lat->cache['config']['script_url']."index.php?sid={$this->lat->user['sid']};";
        }
        else
        {
            $this->lat->url = $this->lat->cache['config']['script_url']."index.php?";
        }

        // Turn null into zero
        if(!$this->lat->user['id'])
        {
            $this->lat->user['id'] = 0;
        }

        // This user is using default long date format
        if($this->lat->user['long_date'] == "")
        {
            $this->lat->user['long_date'] = $this->lat->cache['config']['long_date'];
        }

        // This user is using default short date format
        if($this->lat->user['short_date'] == "")
        {
            $this->lat->user['short_date'] = $this->lat->cache['config']['short_date'];
        }

        $this->lat->user['ip'] = $ip;

        // If there isn't a group, you're a guest!
        if(!$this->lat->user['gid'])
        {
            $this->lat->user['gid'] = 3;
        }

        // Throw in group permissions in here
        $this->lat->user['group'] = $this->lat->cache['group'][$this->lat->user['gid']];
    }


    // +-------------------------+
    //   Output Cookie
    // +-------------------------+
    // A simple function that will output a cookie to our user's computer.

    function out_cookie($name, $content="", $exp=false)
    {
        if($exp)
        {
            $expire = time() + 31536000;
        }

        // Set the cookie!
        @setcookie($this->lat->cache['config']['cookie_prefix'].$name, $content, $expire, "/", $this->lat->cache['config']['cookie_domain']);
    }


    // +-------------------------+
    //   Get Cookie
    // +-------------------------+
    // An even simpler function that will grab a cookie value from our user's computer.

    function get_cookie($name)
    {
        return $_COOKIE[$this->lat->cache['config']['cookie_prefix'].$name];
    }


    // +-------------------------+
    //   Generate Salt
    // +-------------------------+
    // Creates a salt for additional password protection.

    function salt()
    {
        // Latova salts are 10 characters
        while(strlen($string) < 9)
        {
            $random = mt_rand(32, 126);

            // Leave the no no characters out
            if(in_array($random, array(34, 39, 92, 96)))
            {
                $random--;
            }

            $string .= chr($random);
        }

        return $string;
    }


    // +-------------------------+
    //   Update Session
    // +-------------------------+
    // Parses the session to update to the database

    function update_session($act="")
    {
        $location = array();

        if(!$this->no_location)
        {
           $location = array("last_pg" => $this->lat->input['pg'],
                             "last_do" => $this->lat->input['do'],
                             "last_id" => $this->lat->input['id'],
                             "last_cn" => $this->lat->content);
        }

        // You've got error!
        if($this->lat->show->error_occured)
        {
            $this->lat->input['pg'] = "error";
            $this->lat->input['do'] = "";
            $this->lat->input['id'] = 0;
        }

        if($this->create)
        {
            // Query: Generate new session
            $query = array("insert"   => "kernel_session",
                           "data"     => array_merge(array("sid"       => $this->lat->user['sid'],
                                                           "ip"        => $this->lat->user['ip'],
                                                           "uid"       => $this->lat->user['id'],
                                                           "act"       => $act,
                                                           "last_time" => time(),
                                                           "key"       => $this->lat->user['key'],
                                                           "spider"    => $this->lat->user['spider'],
                                                           "uagent"    => substr(addslashes($_SERVER['HTTP_USER_AGENT']), 0, 255),
                                                           "captcha"   => $this->captcha), $location),
                            "shutdown" => 1);

            $this->lat->sql->query($query);
        }
        elseif($this->lat->user['sid'] != "" && !$this->no_update)
        {
            // Query: Update session with tons with information about current page, key, and last clicky
            $query = array("update"   => "kernel_session",
                           "set"      => array_merge(array("captcha"   => $this->captcha,
                                                           "act"       => $act,
                                                           "last_time" => time()), $location),
                           "where"    => "ip='{$this->lat->user['ip']}' AND sid='{$this->lat->user['sid']}'",
                           "shutdown" => 1);

            $this->lat->sql->query($query);
        }
    }


    // +-------------------------+
    //   Add Abuse
    // +-------------------------+
    // This will increment the abuse counter!

    function add_abuse()
    {
        // Query: Delete older records
        $query = array("delete" => "user_lock",
                       "where"  => "ip='{$this->lat->user['ip']}' AND time < ".(time() - (15 * 60)));

        $this->lat->sql->query($query);

        if($this->attempts == "")
        {
            // Query: Do we have an existing record?
            $query = array("select" => "l.ip",
                           "from"   => "user_lock l",
                           "where"  => "l.ip='{$this->lat->user['ip']}' AND l.time >= ".(time() - (15 * 60)));

            $this->lat->sql->query($query);

            if($this->lat->sql->num())
            {
                $this->attempts = true;
            }
        }

        if(!$this->attempts)
        {
            // Query: Insert a new login ban record
            $query = array("insert"   => "user_lock",
                           "data"     => array("ip"       => $this->lat->user['ip'],
                                               "attempts" => 1,
                                               "time"     => time()),
                           "shutdown" => 1);
        }
        else
        {
            // Query: Increment the previous ban record
            $query = array("update"   => "user_lock",
                           "set"      => array("attempts=" => "attempts+1"),
                           "where"    => "ip='{$this->lat->user['ip']}'",
                           "shutdown" => 1);
        }

        $this->lat->sql->query($query);
    }


    // +-------------------------+
    //   Check Abuse
    // +-------------------------+
    // This will lock a user out if there is suspected abuse if one of the systems

    function check_abuse($ip="")
    {
        if($ip == "")
        {
            $ip = $this->lat->user['ip'];
        }

        // Query: Get the number of attempts
        $query = array("select" => "l.attempts",
                       "from"   => "user_lock l",
                       "where"  => "l.ip='{$ip}' AND l.time >= ".(time() - (15 * 60)));

        $blogin = $this->lat->sql->query($query);

        $this->attempts = $blogin['attempts'];

        if(!$blogin['attempts'])
        {
            return 0;
        }
        elseif($blogin['attempts'] > 3)
        {
            return -1;
        }
        else
        {
            return $blogin['attempts'];
        }
    }


    // +-------------------------+
    //   Check Abuse
    // +-------------------------+
    // This will lock a user out if there is suspected abuse if one of the systems

    function check_captcha()
    {
        if(!$this->lat->user['captcha'] && $this->lat->user['captcha'] != $this->lat->input['captcha'])
        {
            $query = array("update" => "kernel_session",
                           "set"    => array("captcha" => ""),
                           "where"  => "sid='{$this->lat->user['sid']}'");

            $this->lat->sql->query($query);

            $this->lat->form_error[] = $this->lat->lang['err_security_code'];
        }
    }


    // +-------------------------+
    //   Check Password
    // +-------------------------+
    // Does a quick check for common passwords

    function check_password($password)
    {
        $password_keyboard = "qwertyuiop[]\\asdfghjkl;'zxcvbnm,./`1234567890-=";
        $password_common = array("password", "password1", "liverpool", "letmein", "charlie", "monkey", "arsenal", "thomas", "abc123");

        if(strpos($password_keyboard, strtolower($password)) === false && strpos(strrev($password_keyboard), strtolower($password)) === false && !in_array(strtolower($password), $password_common))
        {
            return true;
        }
    }
}
?>
