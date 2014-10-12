<?php if(!defined('LAT')) die("Access Denied.");

class module_cp
{
	function initialize()
	{
		switch($this->lat->input['do'])
		{
			// Settings
			case "settings":
				$this->settings();
				break;
			case "submit_settings":
				$this->submit_settings();
				break;
			// Check version (ajax)
			case "version_check":
				$this->version_check();
				break;
			case "cache":
				$this->reload_cache();
				break;
			case "reparse":
				$this->reparse_content();
				break;
			// Main overview
			default:
				$this->main();
				break;
		}
	}

	//   Reload Cache
	// +-------------------------+
	// Forces reload of the entire cache

	function reload_cache()
	{
		$this->lat->inc->cp->form = "pg=cp;do=cache";
		$this->lat->nav[] = $this->lat->title = "Reload Cache";

		if($this->lat->raw_input['submit'])
		{
			$this->lat->core->check_key();
			$date = $this->lat->show->make_time(str_replace(array("[", "]"), "", $this->lat->user['long_date']), time());
			$this->lat->sql->cache("", true);
			$debug = implode("<br />", $this->lat->sql->cache_debug);
			$this->lat->sql->cache_debug_time = $this->lat->parse->number($this->lat->sql->cache_debug_time, 5);
			$body = "{$date}<br />Syncing Latova cache...<br /><br />{$debug}<br /><br /><i>Total sync execution time: {$this->lat->sql->cache_debug_time}s</i>";
		}
		else
		{
			$body = "Latova stores temporarily stores data in small tables to be loaded quickly all at once upon visiting any page. Latova automatically updates the cache as it sees needed, with the exception of manual database editing.<br /><br /><b>This tool is meant for developers and advanced users. You will only need to use this page if you are manually editing the database.</b>";
		}

		$this->lat->output .= <<<LAT
		<div class="bdr2">
			{$body}
		</div>
		<h3><input type="submit" name="submit" class="form_button" value="Reload Cache" /></h3>
LAT;
	}

	//   Reparse Content
	// +-------------------------+
	// Forces reparse of all content

	function reparse_content()
	{
		$this->lat->inc->cp->form = "pg=cp;do=reparse";
		$this->lat->nav[] = $this->lat->title = "Reparse Content";

		if($this->lat->raw_input['submit'])
		{
			$this->lat->core->check_key();
			foreach($this->lat->cache['page'] as $page)
			{
				if($page['system'])
				{
					$this->lat->core->load_module($page['name']);
					$this->lat->module->$page['name']->latova_system(array("type" => "reparse"));
				}
			}

			$date = $this->lat->show->make_time(str_replace(array("[", "]"), "", $this->lat->user['long_date']), time());
			$body = "{$date}<br />All content has been flagged for reparsing.";
		}
		else
		{
			$body = "Latova stores a both an unparsed version (original content user put in with desired bbtags and such intact) cached version of content (parsed with HTML). The cached version loads pages faster, however with Latova's many real-time content related features enable administrators to make changes to bbtags and such that will immediately be visible on all past, present and future variations of content.<br /><br />Instead of reparsing all effected content upon changing a parser element (like deleting a bbtag for example), which would be undoubtedly slow since possibly millions of pieces of content may exist, the content will only be reparsed when a user requests it, and only for the pieces of content they wish to view. To the user and administrator, this process is transparent and appears to be real-time. This process only happens once for everytime a piece of content has the reparse flag set and will remove the flag so the next user who views a piece of content will view the new cached version and not reparse it for the second time.<br /><br />Usually Latova automatically flags content that it knows will need to be reparsed. If for whatever reason (due to some error or manual changes) you need to flag ALL content to be reparsed, this page is intended to do exact that.<br /><br /><b>This tool is meant for developers and advanced users. You will only need to use this page if you are manually editing the database.</b>";
		}

		$this->lat->output .= <<<LAT
		<div class="bdr2">
			{$body}
		</div>
		<h3><input type="submit" name="submit" class="form_button" value="Reparse Content" /></h3>
LAT;
	}

	//   Version check
	// +-------------------------+
	// Ajax function to contact server via php and check for new version

	function version_check()
	{
		$this->lat->core->load_cache("module");
		$this->lat->input['ids'] = explode(",", $this->lat->get_input->preg_whitelist("ids", "0-9,"));
		$this->lat->show->ajax = true;

		// Load up the module cache information
		foreach($this->lat->input['ids'] as $ids)
		{
			if($data)
			{
				$data .= "\n";
			}
			$id = $ids;
			$mod_id[] = $this->lat->cache['module'][$id]['mod_id'];
			$data .= $id."=".$this->lat->cache['module'][$id]['version'];
		}

		// To reduce loads on version check servers, we check only every 15 minutes for new versions
		$version_cache = unserialize($this->lat->cache['storage']['version_cache']);
		$md5_url = md5($this->lat->cache['module'][$id]['url_version']);

		if(!empty($version_cache[$md5_url]) && $version_cache[$md5_url]['time'] + 300 > time())
		{
			foreach($version_cache[$md5_url]['mod'] as $mid => $mv)
			{
	    		if($nl)
	    		{
	    			$this->lat->output .= "\n";
	    		}
	    		$nl = true;

				if(version_compare($this->lat->cache['module'][$mid]['version'], $mv) == -1)
	    		{
	    			$this->lat->output .= $mid.":<span class=\"fail\">{$mv}</span>";
	    		}
	    		else
	    		{
	    			$this->lat->output .= $mid.":<span class=\"pass\">{$mv}</span>";
	    		}
			}
		    return;
		}

		$url = parse_url($this->lat->cache['module'][$id]['url_version']);

		$fp = fsockopen($url['host'], 80, $errno, $errstr, 5);
		if (!$fp)
		{
			foreach($mod_id as $id)
			{
				$this->lat->output .= $id.":<span class=\"fail\">Server Unreachable</span>";
			}
		    return;
		}
		else
		{
		    fputs($fp, "POST {$this->lat->cache['module'][$id]['url_version']}  HTTP/1.1\r\n");
			$header = "Host: {$url['host']}\r\n";
			$header .= "User-Agent: Latova\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: ".strlen($data)."\r\n";
			$header .= "Connection: close\r\n\r\n";
		    fputs($fp, $header.$data);
		    fwrite($fp, $out);
		    $grab = false;
		    while (!feof($fp))
		    {
		    	$line = fgets($fp, 128);
		    	if($grab)
		    	{
		    		$line = $this->lat->parse->preg_whitelist($line, "0-9=.");
		    		$line = trim($line);
		    		$line_ex = explode("=", $line);
		    		$version_cache[$md5_url]['mod'][$line_ex[0]] = $line_ex[1];

		    		if($nl)
		    		{
		    			$this->lat->output .= "\n";
		    		}
		    		$nl = true;

		    		if(version_compare($this->lat->cache['module'][$line_ex[0]]['version'], $line_ex[1]) == -1)
		    		{
		    			$this->lat->output .= $line_ex[0].":<span class=\"fail\">{$line_ex[1]}</span>";
		    		}
		    		else
		    		{
		    			$this->lat->output .= $line_ex[0].":<span class=\"pass\">{$line_ex[1]}</span>";
		    		}
		    	}
		    	elseif(trim($line) == "")
		    	{
		    		$grab = true;
		    	}
		    }
		    fclose($fp);

		    // Update version cache
		    $version_cache[$md5_url]['time'] = time();

		    $query = array("update" => "kernel_storage",
						   "set"    => array("data" => serialize($version_cache)),
						   "where"  => "label='version_cache'");

			$this->lat->sql->query($query);
		    $this->lat->sql->cache("storage");
		}
	}


	//   Main
	// +-------------------------+
	// Default function, checks versions, basic info, etc

	function main()
	{
		$this->lat->core->load_cache("module");
		$this->lat->inc->cp->form = "pg=cp";

		// Force version rechecks
		if($this->lat->raw_input['submit'] && function_exists("fsockopen"))
		{
			$version_cache = unserialize($this->lat->cache['storage']['version_cache']);

			foreach($version_cache as $n => $v)
			{
				$version_cache[$n]['time'] = 0;
			}

		    $query = array("update" => "kernel_storage",
						   "set"    => array("data" => serialize($version_cache)),
						   "where"  => "label='version_cache'");

			$this->lat->sql->query($query);
		    $this->lat->sql->cache("storage");
		}

		foreach($this->lat->cache['module'] as $mod)
		{
			if($mod['id'] == 1)
			{
				$class = "kernel";
				$prefix = "";
			}
			else
			{
				$class = "module";
				$prefix = "&nbsp; &gt; ";
			}

			$mod_html .= <<<LAT

				<tr>
					<td class="cell_1_first">
						<span class="{$class}">{$prefix}{$mod['name']}</span>
					</td>
					<td align="center" class="cell_2">
						<span class="{$class}">{$mod['version']}</span>
					</td>
LAT;

			if(!function_exists("fsockopen"))
			{
				$mod_html .= <<<LAT
				</tr>
LAT;
			}
			elseif($mod['url_version'])
			{
				$md5_mod = md5($mod['url_version']);
				$check_mod[$md5_mod][] = $mod['mod_id'];

				$mod_html .= <<<LAT

					<td class="cell_1">
						<div id="url_{$md5_mod}_{$mod['mod_id']}" style="text-align: center" class="{$class}">
							<span class="tiny_text" style="font-weight: normal"><img src="{$this->lat->image_url}ajax_small.gif" alt="" /> Checking Version...</span>
						</div>
					</td>
				</tr>
LAT;
			}
			else
			{
				$mod_html .= <<<LAT

					<td class="cell_1">
						Unknown
					</td>
				</tr>
LAT;
			}
		}

		if(!empty($check_mod))
		{
			foreach($check_mod as $hmod => $cmod)
			{
				$this->lat->show->js_end[] = "version_ajax(\"{$hmod}\", \"".implode(",", $cmod)."\");";
			}
		}

		if(function_exists("fsockopen"))
		{
			$mod_header = <<<LAT
				<th width="50%">
					Module
				</th>
				<th width="20%">
					Your Version
				</th>
				<th width="30%">
					Latest Version
				</th>
LAT;

			$mod_button = "<input type=\"submit\" name=\"submit\" class=\"form_button\" value=\"Force version recheck\" />";
		}
		else
		{
			$mod_header = <<<LAT
				<th width="70%">
					Module
				</th>
				<th width="30%">
					Version
				</th>
LAT;

			$mod_button = "Note: Latova can't check module versions due to server configuration.";
		}

		$this->lat->title = "Overview";
		$gd = gd_info();
		$sql = $this->lat->sql->version();
		if(ini_get("safe_mode"))
		{
			$safemode = "Enabled";
		}
		else
		{
			$safemode = "Disabled";
		}
		$phpver = phpversion();

		$this->lat->output = <<<LAT
		<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
			<tr>
{$mod_header}
			</tr>{$mod_html}
		</table>
		<h3>{$mod_button}</h3>
	</div>
	<div class="clear"></div>
	<div class="bdr">
		<h2>Information</h2>
		<div class="bdr2">
			<b>Server:</b> {$_SERVER['SERVER_SOFTWARE']}<br />
			<b>PHP Version:</b> {$phpver}<br />
			<b>PHP Safe Mode:</b> {$safemode}<br />
			<b>Mysql:</b> {$sql}<br />
			<b>GD Library:</b> {$gd['GD Version']}
		</div>
		<h2>Credits</h2>
		<div class="bdr2">
			<b>Developed By:</b> Michael Lat<br />
			<b>Skin Designer:</b> Hubert Yip
		</div>
LAT;

$this->lat->inc->cp->header = <<<LAT
<noscript>
<div class="bdr">
	<h1>Javascript Disabled</h1>
	<div class="bdr2_error">
		<div style="margin-left: 44px">
			It looks like javascript is disabled. Latova Control Panel uses lots of javascript so it'd be a good idea if you enable it. Plus you'll remove this super huge annoying message, which is designed to motivate you.
			<i><ol><li>If you're using some browser addons such as noscript for Firefox, add this website to the whitelist, and know that I as a developer complement your good taste in browsers and extensions.
			<li>If you have a browser with javascript disabled, it's a good idea to turn it on. I promise there's no funny business or scripts in here.</li>
			<li>If you're using an old Windows 3.1 machine with a super old netscape browser or something, for god's sake, upgrade!</li>
			<li>You can keep trying to use the Control Panel without javascript I guess... but don't come crying to me when nothing works.</ol></i>
		</div>
	</div>
</div>
<div class="clear"></div>
</noscript>
LAT;
	}


	//   Settings
	// +-------------------------+
	// Loads up a settings page with all the fields

	function settings()
	{
		$this->lat->get_input->no_text("act");

		foreach($this->lat->cache['page_cp'] as $pg)
		{
			if($pg['config'] && $this->lat->input['act'] == $pg['name'] )
			{
				$pg_id = $pg['id'];
				break;
			}
		}

		if(!$pg_id)
		{
			$this->lat->core->error("Settings page does not exist.");
		}

		$section = strtolower($this->lat->input['act']);
		$pg_name = "Settings: ".ucfirst($this->lat->cache['page_cp'][$pg_id]['title']);

		if(substr(strtolower($pg_name), -9, 9) == " settings")
		{
			$pg_name = ucfirst(substr($pg_name, 0, strlen($pg_name) - 9));
		}

		$this->lat->title = $pg_name;
		$this->lat->nav[] = array($pg_name);
		$this->lat->inc->cp->form = "pg=cp;do=submit_settings;act=".$this->lat->cache['page_cp'][$pg_id]['name'];

		$query = array("select" => "*",
		               "from"   => "config",
					   "where"  => "section='{$section}'",
					   "order"  => "o ASC");

		while($config = $this->lat->sql->query($query))
		{
			if($config['title'] == "")
			{
				$config['title'] = $config['name'];
			}

			switch($config['type'])
			{
				// Header
				case 0:
					$this->lat->inc->cp->construct(array("type"  => "header",
														 "help"  => $config['help'],
														 "title" => $config['title']));
					break;
				// Textbox
				case 1:
					$this->lat->inc->cp->construct(array("type"  => "textbox",
														 "name"  => "cfg_".$config['name'],
														 "help"  => $config['help'],
														 "title" => $config['title'],
														 "value" => $config['value'],
														 "field" => array("maxlength" => $config['extra'])));
					break;
				// Textarea
				case 2:
					$this->lat->inc->cp->construct(array("type"  => "textarea",
														 "name"  => "cfg_".$config['name'],
														 "help"  => $config['help'],
														 "title" => $config['title'],
														 "value" => $config['value']));
					break;
				// Enabled/Disabled
				case 3:
					$this->lat->inc->cp->construct(array("type"  => "state",
														 "name"  => "cfg_".$config['name'],
														 "help"  => $config['help'],
														 "title" => $config['title'],
														 "value" => $config['value']));
					break;
				// Dropdown
				case 4:
					$config['extra'] = explode("\n", $config['extra']);
					foreach($config['extra'] as $opt)
					{
						$options[] = explode("|", $opt);
					}

					$this->lat->inc->cp->construct(array("type"    => "dropdown",
														 "name"    => "cfg_".$config['name'],
														 "help"    => $config['help'],
														 "title"   => $config['title'],
														 "options" => $options,
														 "default" => $config['value']));
					break;
			}
		}

		if($this->lat->output == "")
		{
			$this->lat->core->error("No settings exist on this page!");
		}

		$this->lat->inc->cp->construct(array("type"   => "finish",
											 "button" => array("value" => "Submit")));
	}


	//   Submit Settings
	// +-------------------------+
	// Submits settings page by loading what fields should be there

	function submit_settings()
	{
		$this->lat->get_input->no_text("act");
		$this->lat->core->check_key();

		foreach($this->lat->cache['page_cp'] as $pg)
		{
			if($pg['config'] && $this->lat->input['act'] == $pg['name'])
			{
				$pg_id = $pg['id'];
				break;
			}
		}

		if(!$pg_id)
		{
			$this->lat->core->error("Settings page does not exist.");
		}

		$section = strtolower($this->lat->input['act']);

		$query = array("select" => "*",
		               "from"   => "config",
					   "where"  => "section='{$section}' AND type!=0");

		while($config = $this->lat->sql->query($query))
		{
			switch($config['type'])
			{
				case 3:
					$update = $this->lat->get_input->whitelist("cfg_".$config['name'], array(0, 1));
					break;
				case 4:
					$options = explode("\n", $config['extra']);
					$opt_arr = "";

					// Give us each recorded option
					foreach($options as $opt)
					{
						$opt_parsed = explode("|", $opt);
						$opt_arr[] = $opt_parsed[0];
					}

					$update = $this->lat->get_input->whitelist("cfg_".$config['name'], $opt_arr);
					break;
				default:
					$update = $this->lat->get_input->sql_text("cfg_".$config['name']);
					break;
			}

			$update_query = array("update" => "config",
								  "set"    => array("value" => $update),
								  "where"  => "name='{$config['name']}'");

			$this->lat->sql->query($update_query);
		}

		$this->lat->sql->cache("config");
		$this->lat->core->redirect($this->lat->url."pg=cp;do=settings;act=".$section, "Configuration updated successfully.");
	}
}
?>