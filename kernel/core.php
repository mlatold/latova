<?php if(!defined('LAT')) die("Access Denied.");

class kernel_core
{
    //   System Initalize
    // +-------------------------+
    // All the things that latova needs to do to start up

    function initialize()
    {
        global $lat;
        $this->lat = &$lat;

        // Load Critical Kernel Files
        $this->load_kernel("show");
        $this->load_kernel("parse");
        $this->load_kernel("sql");
        $this->load_kernel("get_input");
        $this->load_kernel("session");

        if(!DEBUG && SECURITY_IP == 0 && $this->lat->user['group']['superadmin'])
        {
            error_reporting(E_ALL & ~E_NOTICE);
        }

        // What skin are we getting?
        if(!$this->lat->user['set_skin'])
        {
            $this->lat->show->skin_id = $this->lat->cache['config']['default_skin'];
        }
        else
        {
            $this->lat->show->skin_id = $this->lat->user['set_skin'];
        }

        if(!$this->lat->user['set_lang'])
        {
            $this->lat->show->lang_id = $this->lat->cache['config']['default_lang'];
        }
        else
        {
            $this->lat->show->lang_id = $this->lat->user['set_lang'];
        }

        // Image urls
        $this->lat->image_url = $this->lat->cache['config']['script_url'].$this->lat->config['STORAGE_PATH']."images/".$this->lat->show->skin_id."/";


        // Load the requested page
        if($this->lat->cache['page'][$this->lat->input['pg']]['file'] && !$this->lat->cache['page'][$this->lat->input['pg']]['type'])
        {
            $pg = $this->lat->input['pg'];
            $this->load_module();
            $this->load_skin();
            $this->load_lang();

            if($this->lat->cache['page'][$this->lat->input['pg']]['cp'])
            {
                $this->load_class("cp", "cp");
            }

            $this->lat->module->$pg->initialize();

            if($this->lat->cache['page'][$this->lat->input['pg']]['cp'] && !$this->lat->show->ajax)
            {
                $this->lat->inc->cp->render();
            }

            $this->lat->show->render();
        }
        else
        {
            $this->error("err_no_page");
        }
    }


    //   Load Kernel File
    // +-------------------------+
    // Loads a main kernel file into the latova class and initalizes it

    function load_kernel($file)
    {
        if($file == "sql")
        {
            $class = "kernel_".strtolower($this->lat->config['SQL_DRIV']);
            require_once($this->lat->config['KERNEL_PATH'].$this->lat->config['SQL_DRIV'].".php");
        }
        else
        {
            $class = "kernel_".$file;
            require_once($this->lat->config['KERNEL_PATH'].$file.".php");
        }

        $this->lat->$file = new $class;
        $this->lat->$file->lat = &$this->lat;
        $this->lat->$file->initialize();
    }


    //   Load Module File
    // +-------------------------+
    // Loads a module file into the latova class and initalizes it

    function load_module($name="")
    {
        if($name== "")
        {
            $name = $this->lat->input['pg'];
        }

        if(!isset($this->lat->module->$name))
        {
            require_once($this->lat->config['MODULES_PATH'].$this->lat->cache['page'][$name]['file'].".php");
            $class = "module_".$name;
            $this->lat->module->$name = new $class;
            $this->lat->module->$name->lat = &$this->lat;
        }
    }


    //   Skin Loader
    // +-------------------------+
    // This will load a skin and return it in the form of a class.

    function load_skin($name="")
    {
        if($name == "")
        {
            $name = $this->lat->input['pg'];
        }

        // Query: Fetch templates from database
        $query = array("select" => "s.label, s.skin",
                       "from"   => "local_skin s",
                       "where"  => "s.sid={$this->lat->show->skin_id} AND (s.pg='{$name}' OR s.pg='')");


        // Fetch skins and ready them for usage
        while($skin = $this->lat->sql->query($query))
        {
            $this->lat->skin[$skin['label']] = " <<<TEMP\n".str_replace("TEMP;", "TEMP&#59;", $skin['skin'])."\nTEMP;\n";
        }
    }


    //   Language Loader
    // +-------------------------+
    // Will load a skin, but will either merge it with our language super array, or it will return it in the form of an array.

    function load_lang($name="")
    {
        if($name== "")
        {
            $name = $this->lat->input['pg'];
        }

        // Query: Fetch language entries from database
        $query = array("select" => "l.label, l.word",
                       "from"   => "local_lang l",
                       "where"  => "l.lid={$this->lat->show->lang_id} AND (l.pg='{$name}' OR l.pg='')");

        // Import words into array
        while($lang = $this->lat->sql->query($query))
        {
            if(substr($lang['name'], 0, 5) == "help_")
            {
                $lang['word'] = str_replace(array("<", ">"), array("{", "}"), $lang['word']);
            }

            $this->lat->lang[$lang['label']] = $lang['word'];
        }
    }


    //   Class Loader
    // +-------------------------+
    // Will load a class that preforms functions that do not need to be called as often. Returns a class.

    function load_class($dir, $file)
    {
        global $lat;

        if(isset($this->lat->inc->$file))
        {
            return;
        }

        require_once($this->lat->config['MODULES_PATH'].$dir."/inc_{$file}.php");
        $class = "inc_".$file;
        $this->lat->inc->$file = new $class;
        $this->lat->inc->$file->lat = &$lat;
        $this->lat->inc->$file->initialize();
    }


    //   Cache Loader
    // +-------------------------+
    // Loads a cache

    function load_cache($label)
    {
        if(empty($this->lat->cache[$label]))
        {
            if(!empty($this->lat->sql->raw_cache[$label]))
            {
                $this->lat->cache[$label] = unserialize($this->lat->sql->raw_cache[$label]['data']);
            }
            else
            {
                return false;
            }
        }

        return true;
    }



    //   Timer
    // +-------------------------+
    // Returns timer value, credits to php.net

    function timer()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }


    //   Go to URL
    // +-------------------------+
    // Redirects the browser instantly to another URL

    function redirect($http, $act="")
    {
        if(!DEBUG)
        {
            // Lets run shutdown queries before killing the script...
            $this->lat->session->update_session($act);
            $this->lat->sql->exec_shutdown();

            // Redirect and exit
            header("Location: ".$http);
            exit();
        }
        else
        {
        	$this->lat->show->head[] = "<meta http-equiv=\"refresh\" content=\"5;URL={$http}\">";
        	$this->lat->title = $this->lat->nav = "Redirecting... [DEBUG MODE]";
            $this->lat->output = "<div class=\"bdr\"><h1>Redirecting you there in a sec!</h1><div class=\"bdr2\">Latova is set to debug mode. This normally would be a redirection page.<br /><br /><a href=\"{$http}\">Click here to continue</a>.<br /><br /><span class=\"tiny_text\">It'll automatically redirect in like 5 seconds, so hit escape if you're reading queries.<br />Also, if you're an admin whos not debugging anything you should probably turn DEBUG mode off in index.php so you don't annoy yourself and your users.</div></div>";
        }
    }


    //   Captcha
    // +-------------------------+
    // Returns captcha image

    function captcha($without_layout=false)
    {
        if($this->lat->cache['config']['recaptcha_public'] && $this->lat->cache['config']['recaptcha_private'])
        {
            $this->lat->show->js_vars['RecaptchaOptions'] = "{ theme : 'clean' };";
            require_once($this->lat->config['PLUGINS_PATH']."other/recaptchalib.php");
            $captcha = recaptcha_get_html($this->lat->cache['config']['recaptcha_public']);
        }
        elseif($this->lat->show->find_gd() == 0)
        {
            $this->lat->session->captcha = rand(10000, 99999);
            eval("\$captcha =".$this->lat->skin['captcha_nogd']);
            eval("\$captcha_help =".$this->lat->skin['captcha_help']);
        }
        else
        {
            eval("\$captcha =".$this->lat->skin['captcha_latova']);
            eval("\$captcha_help =".$this->lat->skin['captcha_help']);
        }

        if(!$without_layout)
        {
            eval("\$captcha =".$this->lat->skin['captcha']);
        }

        return $captcha;
    }


    //   Check Key
    // +-------------------------+
    // Check authorization key.

    function check_key()
    {
        if($this->lat->get_input->preg_whitelist("key", "A-Za-z0-9") != $this->lat->user['key'])
        {
            $this->error("err_key");
        }
    }


    //   Check Key Form
    // +-------------------------+
    // Check authorization key.

    function check_key_form()
    {
        if($this->lat->get_input->preg_whitelist("key", "A-Za-z0-9") != $this->lat->user['key'])
        {
            $this->lat->form_error[] = "err_key_form";
        }
    }


    //   Check Key
    // +-------------------------+
    // Check authorization key.

    function check_captcha()
    {
        if($this->lat->cache['config']['recaptcha_public'] && $this->lat->cache['config']['recaptcha_private'])
        {
            if ($_POST["recaptcha_response_field"])
            {
                require_once($this->lat->config['PLUGINS_PATH']."other/recaptchalib.php");
                $resp = recaptcha_check_answer ($this->lat->cache['config']['recaptcha_private'],
                                                $_SERVER["REMOTE_ADDR"],
                                                $_POST["recaptcha_challenge_field"],
                                                $_POST["recaptcha_response_field"]);

                if (!$resp->is_valid)
                {
                    $this->lat->form_error[] = "err_captcha";
                }
            }
            else
            {
                $this->lat->form_error[] = "err_captcha";
            }
        }
        elseif(strtoupper($this->lat->get_input->preg_whitelist("captcha", "A-Za-z0-9")) != $this->lat->user['captcha'] || !$this->lat->user['captcha'])
        {
            $this->lat->session->captcha = 0;
            $this->lat->form_error[] = "err_captcha";
        }
    }


    //   Check Password
    // +-------------------------+
    // Requires password for escalation

    function check_password($page)
    {
        if(!$this->lat->user['id'])
        {
            $this->lat->core->error("err_logged_out");
        }

        if(!$this->lat->user['escalated'])
        {
            if($this->lat->get_input->no_text("pass"))
            {
                if(md5($this->lat->user['salt'].$this->lat->input['pass']) != $this->lat->user['pass'])
                {
                    $this->lat->form_error[] = "err_password_incorrect";
                }
                else
                {
                    // Query: Update session to log us in
                    $query = array("update" => "kernel_session",
                                   "set"    => array("escalated" => 1),
                                   "where"  => "uid='{$this->lat->user['id']}' AND sid='{$this->lat->user['sid']}'",
                                   "limit"  => 1);

                    $this->lat->sql->query($query);
                    return;
                }
            }

            eval("\$this->lat->output =".$this->lat->skin['escalation']);
            $this->lat->title = $this->lat->lang['escalation'];
            $this->lat->nav = $this->lat->lang['escalation'];
            $this->lat->session->update_session();
            $this->lat->sql->exec_shutdown();
            $this->lat->show->render();
            exit();
        }
    }


    //   Error
    // +-------------------------+
    // A problem occured, halt the script and display the error.

    function error($error)
    {
        if(empty($this->lat->lang))
        {
            $this->load_lang("global");
        }

        if(empty($this->lat->skin))
        {
            $this->load_skin("global");
        }

        // Kill form errors and shutdown queries. We don't want to run/display anything that was involved in an error...
        $this->lat->session->error_occured = true;
        unset($this->lat->form_error);

        if($this->lat->lang[$error] != "")
        {
            $error = $this->lat->lang[$error];
        }

        eval("\$this->lat->output =".$this->lat->skin['error']);

        if($this->lat->cache['page'][$this->lat->input['pg']]['cp'])
        {
            $this->lat->inc->cp->render();
        }

        // Rest title, navigation, and output
        $this->lat->title = $this->lat->lang['err_critical_title'];
        $this->lat->nav = $this->lat->lang['err_critical_title'];

        // Output & exit...
        $this->lat->show->render();
        exit();
    }


    // +-------------------------+
    //   Core Error
    // +-------------------------+
    // If its not possible for language and skins to be loaded on error, this error is used

    function panic($error, $solutions)
    {
        echo <<<ERROR
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Latova Error</title>
        <link rel="shortcut icon" href="./favicon.ico" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1;" />
    </head>
    <body>
        <span style="font-family: Verdana, Helvetica, sans-serif;color: #d68c31;font-size: 35px;font-weight: bold;">Latova Error</span><br />
        <blockquote>
            <span style="font-family: Verdana, Helvetica, sans-serif;font-size: 15px;color: black;">
            Oops! The script encountered a kernel error.<br />
            If the problem presists, contact the administration so they may rectify the problem.<br /><br />
            Error returned:<br/>
            <textarea rows="8" cols="80" style="border: 1px #d68c31 solid; padding: 2px;">{$error}</textarea><br /><br />
            Suggested solutions:<br/>
            <textarea rows="10" cols="80" style="border: 1px #d68c31 solid; padding: 2px;">{$solutions}</textarea><br /><br />
            You can reload the page by clicking <a href="javascript:history.go();">here</a>.
            </span>
        </blockquote>
    </body>
</html>
ERROR;
        exit();
    }
}
?>
