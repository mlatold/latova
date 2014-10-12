<?php if(!defined('LAT')) die("Access Denied.");

class module_cp_global
{
    function initialize()
    {
        switch($this->lat->input['do'])
        {
            // Word Filters
            case "filter":
                $this->filter();
                break;
            case "submit_filter":
                $this->submit_filter();
                break;
            case "edit_filter":
                $this->edit_filter();
                break;
            case "submit_edit_filter":
                $this->submit_edit_filter();
                break;
            case "delete_filter":
            	$this->delete_filter();
            	break;
            case "bbtag":
            	$this->bbtag();
            	break;
            case "edit_bbtag":
            	$this->edit_bbtag();
            	break;
            case "submit_edit_bbtag":
            	$this->submit_edit_bbtag();
            	break;
            case "delete_bbtag":
			    $this->delete_bbtag();
			    break;
            case "autoparse":
			    $this->autoparse();
			    break;
            case "edit_autoparse":
			    $this->edit_autoparse();
			    break;
            case "submit_edit_autoparse":
			    $this->submit_edit_autoparse();
			    break;
            case "delete_autoparse":
			    $this->delete_autoparse();
			    break;
            default:
            	$this->lat->core->error("No page exists here.");
            	break;
        }
    }


    // +-------------------------+
    //   View filters
    // +-------------------------+

    function filter()
    {
        $this->lat->nav[] = $this->lat->title = "Word Filter";
        $this->lat->core->load_cache("filter");

        if(!empty($this->lat->cache['filter']))
        {
            foreach($this->lat->cache['filter'] as $filter)
            {

                if($filter['type'] == 0)
                {
                    $ftype = "Exact";
                }
                else
                {
                    $ftype = "Loose";
                }

                if($filter['replace_with'] == "")
                {
                    $filter['replace_with'] = "<i>Default</i>";
                }

                $out .= <<<LAT

        <tr>
            <td class="cell_1_first">
                <div style="overflow: hidden; float: left;">
                    {$filter['word']}
                </div>
            </td>
            <td class="cell_1">
                <div style="overflow: hidden; float: left;">
                    {$filter['replace_with']}
                </div>
            </td>
            <td class="cell_2" align="center">
                {$ftype}
            </td>
            <td class="cell_1" align="center">
                <b><a onclick="return confirm('Are you sure you want to delete this filter?')" href="{$this->lat->url}pg=cp_global;do=delete_filter;id={$filter['id']}">Delete</a> / <a href="{$this->lat->url}pg=cp_global;do=edit_filter;id={$filter['id']}">Edit</a></b>
            </td>
        </tr>
LAT;
            }

            $this->lat->output .= <<<LAT
    <table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
        <th>
            Word
        </th>
        <th>
            Replace with
        </th>
        <th width="150">
            Match Type
        </th>
        <th width="150">
            &nbsp;
        </th>
{$out}
    </table>
LAT;
        }
        else
        {
            $this->lat->output .= <<<LAT
        <div class="bdr2">
            No filters exist.
        </div>
LAT;
        }

        $this->lat->inc->cp->footer .= <<<LAT

    <div class="clear"></div>
    <a href="{$this->lat->url}pg=cp_global;do=edit_filter"><big><img src="{$this->lat->image_url}button_new.png" alt="" />New filter</big></a>

LAT;
    }


    // +-------------------------+
    //   Edit filters
    // +-------------------------+

    function edit_filter()
    {
        $this->lat->core->load_cache("filter");
        $this->lat->inc->cp->form = "pg=cp_global;do=submit_edit_filter";
        $this->lat->nav[] = array("Word Filter", "pg=cp_global;do=filter");
        if($this->lat->input['do'] == "submit_edit_filter")
        {
            $this->lat->nav[] = $this->lat->title = "Editing Filter";
			$this->lat->get_input->no_text("replace_with");
			$this->lat->get_input->no_text("type");
        }
        elseif($this->lat->input['id'])
        {
            $this->lat->nav[] = $this->lat->title = "Editing Filter";
            $this->lat->input['word'] = $this->lat->parse->no_text($this->lat->cache['filter'][$this->lat->input['id']]['word']);
            $this->lat->input['replace_with'] = $this->lat->parse->no_text($this->lat->cache['filter'][$this->lat->input['id']]['replace_with']);
	        $this->lat->input['type'] = $this->lat->cache['filter'][$this->lat->input['id']]['type'];
        }
        else
        {
            $this->lat->nav[] = $this->lat->title = "New Filter";
        }

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Filter settings"));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "word",
                                             "title" => "Word",
                                             "value" => $this->lat->input['word'],
                                             "field" => array("maxlength" => 255),
        									 "help"  => "Any new filters or changes to existing filters will automatically apply to all existing content and future content."));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "replace_with",
                                             "title" => "Replace with",
        									 "help"  => "If left blank, will replace each character with the contents of filter_character configuration option. Whatever you enter will be parsed by the user in their content.",
                                             "value" => $this->lat->input['replace_with'],
                                             "field" => array("maxlength" => 255)));

        $this->lat->inc->cp->construct(array("type"      => "state",
                                             "name"      => "type",
                                             "opt_title" => array("Exact Match", "Loose Match"),
                                             "value"     => $this->lat->input['type'],
        									 "width"     => 120,
                                             "help"      => "Loose match will find words even if they are a part of another word. Exact match needs the word to be on its own."));

        $this->lat->inc->cp->construct(array("type"   => "finish",
                                             "button" => array("value" => "Submit")));
    }


    // +-------------------------+
    //   Submit Filter
    // +-------------------------+

    function submit_edit_filter()
    {
        $this->lat->core->load_cache("filter");
        $this->lat->core->check_key_form();

        $this->lat->get_input->whitelist("type", array(0, 1));
        $this->lat->get_input->sql_text("replace_with");

        if($this->lat->get_input->sql_text("word") == "")
        {
            $this->lat->form_error[] = "The word box was left empty.";
            return $this->edit_filter();
        }

		foreach($this->lat->cache['filter'] as $filter)
		{
			if(strtolower($filter['word']) == strtolower($this->lat->raw_input['word']) && $this->lat->input['id'] != $filter['id'])
			{
	            $this->lat->form_error[] = "This word is already in the filter list.";
	            return $this->edit_filter();
			}
		}

        if($this->lat->input['id'])
        {
            $query = array("update" => "kernel_filter",
                           "set"    => array("type"         => $this->lat->input['type'],
                                             "word"         => $this->lat->input['word'],
            								 "replace_with" => $this->lat->input['replace_with']),
                           "where"  => "id=".$this->lat->input['id']);
            $msg = "Word filter updated.";
        }
        else
        {
            $query = array("insert" => "kernel_filter",
                           "data"   => array("type"         => $this->lat->input['type'],
                                             "word"         => $this->lat->input['word'],
            								 "replace_with" => $this->lat->input['replace_with']));
            $msg = "Word filter created.";
        }

        $this->lat->sql->query($query);
        $this->lat->sql->cache("filter");

		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => $this->lat->input['word']));
			}
		}

        $this->lat->core->redirect($this->lat->url."pg=cp_global;do=filter", $msg);
    }


    // +-------------------------+
    //   Delete Filter
    // +-------------------------+

	function delete_filter()
    {
        $this->lat->core->load_cache("filter");
		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => $this->lat->parse->sql_text($this->lat->cache['filter'][$this->lat->input['id']]['word'])));
			}
		}

		$query = array("delete" => "kernel_filter",
					   "where"  => "id=".$this->lat->input['id']);

		$this->lat->sql->query($query);
		$this->lat->sql->cache("filter");
		$this->lat->core->redirect($this->lat->url."pg=cp_global;do=filter", "Filter deleted.");
    }


    // +-------------------------+
    //   Generate BBtag Placement Order
    // +-------------------------+

    function generate_placement($row, $order)
    {
    	if(strlen($order) == 1)
    	{
    		return $row."0".$order;
    	}
    	else
    	{
    		return $row.$order;
    	}
    }


    // +-------------------------+
    //   View BBtags
    // +-------------------------+

    function bbtag()
    {
        $this->lat->nav[] = $this->lat->title = "BBtags";
        $this->lat->inc->cp->form = "pg=cp_global;do=bbtag";
        $this->lat->core->load_cache("bbtag");
        $placement = array();

        foreach($this->lat->cache['bbtag'] as $bb)
        {
            if($bb['placement'] > 0)
            {
            	$c_p = substr($bb['placement'], 0, 1) + 1;
				$count[$c_p]++;
            }
        }

        if($this->lat->raw_input['submit'])
        {
            $this->lat->core->check_key();
            foreach($this->lat->cache['bbtag'] as $bbtag)
            {
            	$this->lat->get_input->unsigned_int("odr_".$bbtag['id']);
            	$c_p = substr($bb['placement'], 0, 1);
                if($this->lat->input['odr_'.$bbtag['id']] > 0 && $bbtag['placement'] != $this->generate_placement($c_p, $this->lat->input['odr_'.$bbtag['id']]))
                {
                    if($count[$c_p + 1] + 1 < $this->lat->input['odr_'.$bbtag['id']])
                    {
                        $this->lat->input['odr_'.$bbtag['id']] = $count[$c_p + 1];
                    }

                    $query = array("update" => "kernel_bbtag",
                                   "set"    => array("placement" => $this->generate_placement(substr($bbtag['placement'], 0, 1), $this->lat->input['odr_'.$bbtag['id']])),
                                   "where"  => "id=".$bbtag['id']);

                    $this->lat->sql->query($query);
                }
            }

            $this->lat->sql->cache("bbtag");
        }

        if(!empty($this->lat->cache['bbtag']))
        {
            foreach($this->lat->cache['bbtag'] as $bbtag)
            {
            	if($bbtag['placement'] > 0)
            	{
            		$c_p = substr($bbtag['placement'], 0, 1) + 1;
            	}
            	else
            	{
            		$c_p = 0;
            	}

            	if(!in_array($c_p, $placement))
            	{
					$placement[] = $c_p;

		            if($c_p)
		            {
		            	$name = "Row {$c_p}";
		            }
		            else
		            {
		            	$name = "Not Displayed";
		            }

                	$out .= <<<LAT

        <tr>
            <td class="sub_header" colspan="2">
				{$name}
            </td>
        </tr>
LAT;
	    	    }

    	        $order = substr($bbtag['placement'], 1);

                $drop = $this->lat->inc->cp->make_order_dropdown($bbtag['id'], $count[$c_p], substr($bbtag['placement'], 1) - 1);
                $out .= <<<LAT

        <tr>
            <td class="cell_1_first">
                <div style="float: right">{$drop}</div>
                <div style="overflow: hidden; float: left;">
                    {$bbtag['tag']}
                </div>
            </td>
            <td class="cell_1" width="200" align="center">
                <b><a onclick="return confirm('Are you sure you want to delete this bbtag?')" href="{$this->lat->url}pg=cp_global;do=delete_bbtag;id={$bbtag['id']}">Delete</a> / <a href="{$this->lat->url}pg=cp_global;do=edit_bbtag;id={$bbtag['id']}">Edit</a></b>
            </td>
        </tr>
LAT;
            }

           	$this->lat->output .= <<<LAT
    <table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
{$out}
    </table>
LAT;
            if($this->lat->inc->cp->drop_exists)
            {
	                $this->lat->output .= <<<LAT

    <h3><input type="submit" name="submit" class="form_button" value="Resort BBtags" /></h3>
LAT;
            }
		}
        else
        {
            if($this->lat->input['id'])
            {
                $this->lat->core->error("No bbtags exist here.");
            }
            else
            {
                $this->lat->output .= <<<LAT
        <div class="bdr2">
            No bbtags exist.
        </div>
LAT;
            }
        }

        $this->lat->inc->cp->footer .= <<<LAT

    <div class="clear"></div>
    <a href="{$this->lat->url}pg=cp_global;do=edit_bbtag"><big><img src="{$this->lat->image_url}button_new.png" alt="" />New BBtag</big></a>

LAT;
    }


    // +-------------------------+
    //   Edit bbtag
    // +-------------------------+

    function edit_bbtag()
    {
        $this->lat->core->load_cache("bbtag");
        $this->lat->inc->cp->form = "pg=cp_global;do=submit_edit_bbtag";
        $this->lat->nav[] = array("BBtags", "pg=cp_global;do=bbtag");
        if($this->lat->input['do'] == "submit_edit_filter")
        {
            $this->lat->nav[] = $this->lat->title = "Editing Filter";
			$this->lat->get_input->no_text("replace_with");
			$this->lat->get_input->no_text("type");
        }
        elseif($this->lat->input['id'])
        {
            $this->lat->nav[] = $this->lat->title = "Editing Filter";
            $this->lat->input['tag'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['tag'];
            $this->lat->input['hotkey'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['hotkey'];
            $this->lat->input['opt'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['option'];
            $this->lat->input['replace_with'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['replace_with'];
            $this->lat->input['no_embed'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['no_embed'];
            $this->lat->input['no_quote'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['no_quote'];
            $this->lat->input['clean'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['clean'];
            $this->lat->input['inherit_img'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['inherit_img'];
            $this->lat->input['inherit_mda'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['inherit_mda'];
            $this->lat->input['display'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['display'];
            $this->lat->input['example_opt'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['example_opt'];
            $this->lat->input['example_opt2'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['example_opt2'];
            $this->lat->input['example'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['example'];
            $this->lat->input['file'] = $this->lat->cache['bbtag'][$this->lat->input['id']]['file'];

            if($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'] == 0)
            {
            	$this->lat->input['row'] = 0;
            }
            else
            {
            	$this->lat->input['row'] = substr($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'], 0, 1) + 1;
            }
        }
        else
        {
            $this->lat->nav[] = $this->lat->title = "New BBtag";
        }

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "BBtag"));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "tag",
                                             "title" => "Tag",
                                             "value" => $this->lat->input['tag'],
                                             "field" => array("maxlength" => 10),
        									 "help"  => "BBtags are encased in square brackets [ ] and then parsed by latova. Any new bbtags or changes to existing filters will automatically apply to all existing content and future content.<br /><br /><i>Characters not allowed: [ ] /</i>"));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "hotkey",
                                             "title" => "Hotkey",
        									 "help"  => "This hotkey will be used for whenever the bbtag is displayed on posting screens.<br /><br /><i>Optional.</i>",
                                             "value" => $this->lat->input['hotkey'],
                                             "field" => array("maxlength" => 1)));

        $this->lat->inc->cp->construct(array("type"  => "textarea",
                                             "name"  => "replace_with",
                                             "title" => "Replacement",
        									 "help"  => "The HTML that replaces the BBtag. Using the following example: [bbtag=OPTION]DATA[/bbtag]<br /><br />&amp;lt;!-- data --&amp;gt; = Gets replaced with anything in DATA<br />&amp;lt;!-- optn --&amp;gt; = Gets replaced with anything in OPTION",
                                             "value" => $this->lat->input['replace_with']));

        if(is_dir($this->lat->config['PLUGINS_PATH']."bbtags"))
        {
			$dir = opendir($this->lat->config['PLUGINS_PATH']."bbtags/");
			$file_options[] = array("", "");
			while (($file = readdir($dir)) !== FALSE)
			{
				if(preg_match("{[A-Za-z0-9_].php}", $file))
				{
					$file_options[] = array($file, substr($file, 0, strlen($file) - 4));
				}
			}

			if(!empty($file_options))
			{
	        	$this->lat->inc->cp->construct(array("type"    => "dropdown",
		                                             "name"    => "file",
		                                             "title"   => "PHP Parsing File",
		        									 "default" => $this->lat->input['file'],
		        									 "options" => $file_options,
		        									 "help"    => "This will run the BBtag through the PHP file specified. Files are usually tailored for special bbtags. For advanced users. Files listed are located in {$this->lat->config['PLUGINS_PATH']}bbtags/<br /><br /><i>Optional.</i>"));
			}
        }

        $this->lat->inc->cp->construct(array("type"    => "dropdown",
                                             "name"    => "opt",
                                             "title"   => "Type",
        									 "default" => $this->lat->input['opt'],
        									 "options" => array(array("No Option", 0), array("Option Required", 1), array("Either Permitted", 2)),
        									 "help"    => "No Option = [b]Example[/b].<br />Option Required = [b=opt]Example[/b].<br />Either Permitted = Works with and without option."));

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Options"));

        $this->lat->inc->cp->construct(array("type"  => "state",
                                             "name"  => "no_embed",
                                             "title" => "Embed this tag as one",
                                             "opt_title" => array("No", "Yes"),
                                             "value" => $this->lat->input['no_embed'],
                                             "help"  => "When set to 'yes', it will treat embeded bbtags like one set, so [b][b]Example[/b][/b] would be treated like [b]Example[/b]."));

        $this->lat->inc->cp->construct(array("type"  => "state",
                                             "name"  => "no_quote",
                                             "title" => "Remove when quoting",
                                             "opt_title" => array("No", "Yes"),
                                             "value" => $this->lat->input['no_quote'],
                                             "help"  => "This tag, and anything in between, will be removed when quoting."));

        $this->lat->inc->cp->construct(array("type"  => "state",
                                             "name"  => "clean",
                                             "title" => "Remove embedded BBtags",
                                             "opt_title" => array("No", "Yes"),
                                             "value" => $this->lat->input['clean'],
                                             "help"  => "Any tags in between this tag will be not be parsed."));

        $this->lat->inc->cp->construct(array("type"  => "state",
                                             "name"  => "inherit_img",
                                             "title" => "Inherit image limit",
                                             "opt_title" => array("No", "Yes"),
                                             "value" => $this->lat->input['inherit_img'],
                                             "help"  => "Inherits image limits from the bbtag profile."));

        $this->lat->inc->cp->construct(array("type"  => "state",
                                             "name"  => "inherit_mda",
                                             "title" => "Inherit media limit",
                                             "opt_title" => array("No", "Yes"),
                                             "value" => $this->lat->input['inherit_mda'],
                                             "help"  => "Inherits embedded media limits from the bbtag profile."));

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Display"));

        $this->lat->inc->cp->construct(array("type"    => "dropdown",
                                             "name"    => "row",
                                             "title"   => "Display",
        									 "default" => $this->lat->input['row'],
        									 "options" => array(array("Do not display", 0), array("Row 1", 1), array("Row 2", 2), array("Row 3", 3)),
        									 "help"    => "To show it on post forms. If set to 'do not display', the tag will be hidden but still usable and viewable from bbtag help, which is good for unpopular tags.<br /><br />You can change the order in which bbtags are displayed from the main bbtag list."));

        $this->lat->inc->cp->construct(array("type"  => "textarea",
                                             "name"  => "display",
                                             "title" => "Dropdown Options",
        									 "help"  => "To have the BBtag show as a dropdown instead of a button, one option per line. Each line has 3 vars separated by pipe characters, which are as following (left to right):<br />1. Displayed option name.<br />2. Option value that is placed in the tag.<br />3. CSS on the dropdown option (optional)<br /><br /><i>Optional.</i>",
                                             "value" => $this->lat->input['display']));

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Help"));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "example",
                                             "title" => "[No Option] Data Example",
        									 "help"  => "If your bbtag needs a special text in between its tags when there is no option, when displaying on the BBtags help page. If nothing is entered it'll just use Example text, which should work in most cases anyway.<br /><br /><i>Optional.</i>",
                                             "value" => $this->lat->input['example'],
                                             "field" => array("maxlength" => 255)));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "example_opt",
                                             "title" => "[Option] Data Example",
        									 "help"  => "If your bbtag needs a special text in between its tags when there is an option, when displaying on the BBtags help page. If nothing is entered it'll just use Example text, which should work in most cases anyway.<br /><br /><i>Optional.</i>",
                                             "value" => $this->lat->input['example_opt'],
                                             "field" => array("maxlength" => 255)));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "example_opt2",
                                             "title" => "[Option] Option Example",
        									 "help"  => "If your bbtag needs a special text in its option when there is an option, when displaying on the BBtags help page. If nothing is entered it'll just use Example text, which should work in most cases anyway.<br /><br /><i>Optional.</i>",
                                             "value" => $this->lat->input['example_opt2'],
                                             "field" => array("maxlength" => 255)));

        $this->lat->inc->cp->construct(array("type"   => "finish",
                                             "button" => array("value" => "Submit")));
    }


    // +-------------------------+
    //   Submit BBtag
    // +-------------------------+

    function submit_edit_bbtag()
    {
        $this->lat->core->load_cache("bbtag");
        $this->lat->core->check_key_form();

        if($this->lat->input['id'])
        {
        	$oldtag = $this->lat->cache['bbtag'][$this->lat->input['id']]['tag'];
        }

		$this->lat->get_input->sql_text("tag");
		$this->lat->input['tag'] = strtolower($this->lat->input['tag']);
		$this->lat->input['hotkey'] = strtolower(substr($this->lat->get_input->no_text("hotkey"), 0, 1));
        $this->lat->get_input->whitelist("opt", array(0, 1, 2));
        $this->lat->get_input->whitelist("row", array(0, 1, 2, 3));
        $this->lat->get_input->sql_text("replace_with");
        $this->lat->get_input->whitelist("no_embed", array(0, 1));
        $this->lat->get_input->whitelist("no_quote", array(0, 1));
        $this->lat->get_input->whitelist("clean", array(0, 1));
        $this->lat->get_input->whitelist("inherit_img", array(0, 1));
        $this->lat->get_input->whitelist("inherit_mda", array(0, 1));
		$this->lat->get_input->sql_text("display");
		$this->lat->get_input->sql_text("example_opt");
		$this->lat->get_input->sql_text("example_opt2");
		$this->lat->get_input->sql_text("example");
		$this->lat->get_input->preg_whitelist("file", "A-Za-z0-9_");

        if($this->lat->input['tag'] == "")
        {
            $this->lat->form_error[] = "The tag box was left empty.";
            return $this->edit_bbtag();
        }

        if(str_replace(array("[", "]", "&#91;", "&#93;", "/", "&#47;"), "", $this->lat->input['tag']) != $this->lat->input['tag'])
        {
            $this->lat->form_error[] = "Entering square brackets [ ] or / is not allowed. They're used for parsing bbtags.";
            return $this->edit_bbtag();
        }

		foreach($this->lat->cache['bbtag'] as $bbtag)
		{
			if(strtolower($bbtag['tag']) == strtolower($this->lat->raw_input['tag']) && $this->lat->input['id'] != $bbtag['id'])
			{
	            $this->lat->form_error[] = "This bbtag already exists.";
	            return $this->edit_bbtag();
			}

			if($this->lat->input['hotkey'] != "" && $this->lat->input['hotkey'] == $bbtag['hotkey'] && $this->lat->input['id'] != $bbtag['id'])
			{
	            $this->lat->form_error[] = "This hotkey is already being used by the tag [{$bbtag['tag']}]";
	            return $this->edit_bbtag();
			}
		}

		if($this->lat->input['file'] !="" && !file_exists($this->lat->config['PLUGINS_PATH']."bbtags/".$this->lat->input['file'].".php"))
		{
            $this->lat->form_error[] = "This specified php parsing file is invalid.";
            return $this->edit_bbtag();
		}

        foreach($this->lat->cache['bbtag'] as $bb)
        {
            if($bb['placement'] > 0)
            {
            	$c_p = substr($bb['placement'], 0, 1) + 1;
				$count[$c_p]++;
            }
        }

        if($this->lat->input['id'])
        {
        	if(!$this->lat->input['row'])
        	{
        		$placement = "000";
        	}
			else
			{
				$placement = $this->generate_placement($this->lat->input['row'] - 1, substr($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'], 1));
			}

        	$oldrow = substr($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'], 0, 1);

			if($oldrow + 1 != $this->lat->input['row'])
			{
				if($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'] > 0)
				{
		            $query = array("update" => "kernel_bbtag",
		                           "set"    => "placement=placement-1",
		                           "where"  => "placement > {$this->lat->cache['bbtag'][$this->lat->input['id']]['placement']} AND placement != 000 AND placement > {$oldrow}00 AND placement < {$oldrow}99");

		            $this->lat->sql->query($query);
				}

	            $placement = $this->generate_placement($this->lat->input['row'] - 1, $count[$this->lat->input['row']] + 1);
			}

            $query = array("update" => "kernel_bbtag",
                           "set"    => array("tag"          => $this->lat->input['tag'],
            								 "placement"    => $placement,
                                             "hotkey"       => $this->lat->input['hotkey'],
                                             "opt"          => $this->lat->input['opt'],
                                             "replace_with" => $this->lat->input['replace_with'],
                                             "no_embed"     => $this->lat->input['no_embed'],
                                             "no_quote"     => $this->lat->input['no_quote'],
                                             "clean"        => $this->lat->input['clean'],
                                             "inherit_img"  => $this->lat->input['inherit_img'],
                                             "inherit_mda"  => $this->lat->input['inherit_mda'],
                                             "display"      => $this->lat->input['display'],
                                             "example_opt"  => $this->lat->input['example_opt'],
                                             "example_opt2" => $this->lat->input['example_opt2'],
                                             "example"      => $this->lat->input['example'],
                                             "example"      => $this->lat->input['example']),
                           "where"  => "id=".$this->lat->input['id']);
            $msg = "BBtag updated.";
        }
        else
        {
        	if(!$this->lat->input['row'])
        	{
        		$placement = "000";
        	}
			else
			{
				$placement = $this->generate_placement($this->lat->input['row'] - 1, $count[$this->lat->input['row']] + 1);
			}

            $query = array("insert" => "kernel_bbtag",
                           "data"   => array("tag"          => $this->lat->input['tag'],
            								 "placement"    => $placement,
                                             "hotkey"       => $this->lat->input['hotkey'],
                                             "opt"          => $this->lat->input['opt'],
                                             "replace_with" => $this->lat->input['replace_with'],
                                             "no_embed"     => $this->lat->input['no_embed'],
                                             "no_quote"     => $this->lat->input['no_quote'],
                                             "clean"        => $this->lat->input['clean'],
                                             "inherit_img"  => $this->lat->input['inherit_img'],
                                             "inherit_mda"  => $this->lat->input['inherit_mda'],
                                             "display"      => $this->lat->input['display'],
                                             "example_opt"  => $this->lat->input['example_opt'],
                                             "example_opt2" => $this->lat->input['example_opt2'],
                                             "example"      => $this->lat->input['example'],
                                             "example"      => $this->lat->input['example']));
            $msg = "BBtag created.";
        }

        $this->lat->sql->query($query);
        $this->lat->sql->cache("bbtag");

        if($oldtag != "" && $this->lat->input['tag'] != $oldtag)
        {
			foreach($this->lat->cache['page'] as $page)
			{
				if($page['system'])
				{
					$this->lat->core->load_module($page['name']);
					$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => "[".$this->lat->parse->sql_text($oldtag)."]"));
				}
			}
        }

		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => "[".$this->lat->input['tag']."]"));
			}
		}

        $this->lat->core->redirect($this->lat->url."pg=cp_global;do=bbtag", $msg);
    }


    // +-------------------------+
    //   Delete BBtag
    // +-------------------------+

	function delete_bbtag()
    {
        $this->lat->core->load_cache("bbtag");
		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => "[".$this->lat->parse->sql_text($this->lat->cache['bbtag'][$this->lat->input['id']]['tag'])."]"));
			}
		}

		if($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'] > 0)
		{
			$row = substr($this->lat->cache['bbtag'][$this->lat->input['id']]['placement'], 0, 1);

	        $query = array("update" => "kernel_bbtag",
	                       "set"    => "placement=placement-1",
	                       "where"  => "placement > {$this->lat->cache['bbtag'][$this->lat->input['id']]['placement']} AND placement != 000 AND placement > {$row}00 AND placement < {$row}99");

	        $this->lat->sql->query($query);
		}

		$query = array("delete" => "kernel_bbtag",
					   "where"  => "id=".$this->lat->input['id']);

		$this->lat->sql->query($query);
		$this->lat->sql->cache("bbtag");
		$this->lat->core->redirect($this->lat->url."pg=cp_global;do=bbtag", "BBtag deleted.");
    }


    // +-------------------------+
    //   View autoparse
    // +-------------------------+

    function autoparse()
    {
        $this->lat->nav[] = $this->lat->title = "Autoparse";
        $this->lat->core->load_cache("autoparse");

        if(!empty($this->lat->cache['autoparse']))
        {
            foreach($this->lat->cache['autoparse'] as $ap)
            {
            	if($ap['type'] == 0)
            	{
            		$type = "Image";
            		$name = $ap['data'];
            	}
            	else
            	{
            		$type = "Media";
            		$name = $ap['site'];
            	}

                $out .= <<<LAT

        <tr>
            <td class="cell_1_first">
                {$type}
            </td>
            <td class="cell_1">
                <div style="overflow: hidden; float: left;">
                    {$name}
                </div>
            </td>
            <td class="cell_1" align="center">
                <b><a onclick="return confirm('Are you sure you want to delete this autoparse entry?')" href="{$this->lat->url}pg=cp_global;do=delete_autoparse;id={$ap['id']}">Delete</a> / <a href="{$this->lat->url}pg=cp_global;do=edit_autoparse;id={$ap['id']}">Edit</a></b>
            </td>
        </tr>
LAT;
            }

            $this->lat->output .= <<<LAT
    <table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
        <th width="100">
            Type
        </th>
        <th>
            Entry
        </th>
        <th width="150">
            &nbsp;
        </th>
{$out}
    </table>
LAT;
        }
        else
        {
            $this->lat->output .= <<<LAT
        <div class="bdr2">
            No autoparse entries exist.
        </div>
LAT;
        }

        $this->lat->inc->cp->footer .= <<<LAT

    <div class="clear"></div>
    <a href="{$this->lat->url}pg=cp_global;do=edit_autoparse"><big><img src="{$this->lat->image_url}button_new.png" alt="" />New AP entry</big></a>

LAT;
    }


    // +-------------------------+
    //   Edit filters
    // +-------------------------+

    function edit_autoparse()
    {
        $this->lat->core->load_cache("autoparse");
        $this->lat->inc->cp->form = "pg=cp_global;do=submit_edit_autoparse";
        $this->lat->nav[] = array("Autoparse", "pg=cp_global;do=autoparse");
        if($this->lat->input['do'] == "submit_edit_autoparse")
        {
            $this->lat->nav[] = $this->lat->title = "Editing Entry";
			$this->lat->get_input->no_html("content");
            $select = array($this->lat->input['type'] => " checked=\"checked\"");
        }
        elseif($this->lat->input['id'])
        {
            $this->lat->nav[] = $this->lat->title = "Editing Entry";
            $this->lat->input['content'] = $this->lat->parse->no_html($this->lat->cache['autoparse'][$this->lat->input['id']]['content']);
            $this->lat->input['site'] = $this->lat->parse->no_text($this->lat->cache['autoparse'][$this->lat->input['id']]['site']);
            $this->lat->input['data'] = $this->lat->parse->no_text($this->lat->cache['autoparse'][$this->lat->input['id']]['data']);
	        $this->lat->input['type'] = $this->lat->cache['autoparse'][$this->lat->input['id']]['type'];
            $select = array($this->lat->input['type'] => " checked=\"checked\"");
        }
        else
        {
            $this->lat->nav[] = $this->lat->title = "New Entry";
            $select = array(0 => " checked=\"checked\"");
        }

		switch($this->lat->input['type'])
		{
			case 0:
				$hide1 = "display: none";
				$this->lat->input['ext'] = $this->lat->input['data'];
				break;
			case 1:
				$hide3 = "display: none";
				$hide4 = "display: none";
				$this->lat->input['var'] = $this->lat->input['data'];
				break;
			case 2:
				$hide2 = "display: none";
				$hide4 = "display: none";
				$this->lat->input['preg'] = $this->lat->input['data'];
				break;
		}

        $this->lat->output .= <<<LAT
    <div class="bdr2">
        <div class="left">
        	<a onmouseover="help('Any new autoparse entries or changes to existing autoparse entries will automatically apply to all existing content and future content.', this)" onmouseout="unhelp()" class="help"><img src="{$this->lat->image_url}help.png" alt="" /></a>
            Type:
        </div>
        <div class="right">
            <div class="left" style="width: 100px"><label><input type="radio" name="type" value="0" onclick="get_element('media1').style.display='none'; get_element('media4').style.display='';"{$select[0]} /> Image</label></div>
            <div class="left" style="width: 120px"><label><input type="radio" name="type" value="1" onclick="get_element('media4').style.display='none'; get_element('media1').style.display=''; get_element('media2').style.display=''; get_element('media3').style.display='none';"{$select[1]} /> Media [var]</label></div>
            <div class="left" style="width: 120px"><label><input type="radio" name="type" value="2" onclick="get_element('media4').style.display='none'; get_element('media1').style.display=''; get_element('media3').style.display=''; get_element('media2').style.display='none';"{$select[2]} /> Media [preg]</label></div>
        </div>
        <div class="clear"></div>
        <div id="media1" style="{$hide1}">
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "site",
                                             "title" => "Website",
                                             "value" => $this->lat->input['site'],
                                             "field" => array("maxlength" => 50),
        									 "help"  => "The domain where the content comes from. Example: <i>mikelat.com</i>"));

        $this->lat->output .= <<<LAT
       		<div id="media2" style="{$hide2}">
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "var",
                                             "title" => "Variable",
                                             "value" => $this->lat->input['var'],
                                             "field" => array("maxlength" => 255),
        									 "help"  => "Variable in the URL to grab data from. Example: <i>v</i> would take the data out of mikelat.com?v=DATA"));

        $this->lat->output .= <<<LAT
       		</div>
       		<div id="media3" style="{$hide3}">
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "preg",
                                             "title" => "Regular Expression",
                                             "value" => $this->lat->input['preg'],
                                             "field" => array("maxlength" => 255),
        									 "help"  => "Regular Expression used to grab data from a url. For advanced users."));

        $this->lat->output .= <<<LAT
       		</div>
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textarea",
                                             "name"  => "content",
                                             "title" => "Embed HTML",
        									 "help"  => "HTML which holds the embedded content.<br /><br />&amp;lt;!-- DATA --&amp;gt; = The data which is grabbed from the variable or regular expression.",
                                             "value" => $this->lat->input['content']));

        $this->lat->output .= <<<LAT
       	</div>
       	<div id="media4" style="{$hide4}">
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "ext",
                                             "title" => "Image Extention",
                                             "value" => $this->lat->input['ext'],
                                             "field" => array("maxlength" => 4),
        									 "help"  => "Extention on the image to search for. Example: <i>png</i>"));

        $this->lat->output .= <<<LAT
       	</div>
LAT;

        $this->lat->inc->cp->construct(array("type"   => "finish",
                                             "button" => array("value" => "Submit")));
    }


    // +-------------------------+
    //   Submit Filter
    // +-------------------------+

    function submit_edit_autoparse()
    {
        $this->lat->core->load_cache("autoparse");
        $this->lat->core->check_key_form();

        $this->lat->get_input->whitelist("type", array(0, 1));
        $this->lat->get_input->sql_text("content");
        $this->lat->get_input->sql_text("ext");
        $this->lat->get_input->sql_text("preg");
        $this->lat->get_input->sql_text("var");

        switch($this->lat->input['type'])
        {
        	case 0:
				$this->lat->input['data'] = $this->lat->input['ext'];
				$search = $this->lat->input['ext'];
				break;
        	case 1:
				$this->lat->input['data'] = $this->lat->input['var'];
				$search = $this->lat->input['site'];
				break;
        	case 2:
				$this->lat->input['data'] = $this->lat->input['preg'];
				$search = $this->lat->input['site'];
				break;

        }

        if($this->lat->input['type'] > 0 && $this->lat->get_input->sql_text("site") == "")
        {
            $this->lat->form_error[] = "The site box was left empty.";
            return $this->edit_autoparse();
        }

        if($this->lat->input['type'] == 0 && $this->lat->get_input->sql_text("ext") == "")
        {
            $this->lat->form_error[] = "The extention box was left empty.";
            return $this->edit_autoparse();
        }

        if($this->lat->cache['autoparse'][$this->lat->input['id']]['type'] == 0)
        {
        	$search_org = $this->lat->cache['autoparse'][$this->lat->input['id']]['data'];
        }
        else
        {
        	$search_org = $this->lat->cache['autoparse'][$this->lat->input['id']]['site'];
        }

        $search_org = $this->lat->parse->sql_text($search_org);
        if($search_org != $search)
        {
			foreach($this->lat->cache['page'] as $page)
			{
				if($page['system'])
				{
					$this->lat->core->load_module($page['name']);
					$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => $search_org));
				}
			}
        }

        if($this->lat->input['id'])
        {
            $query = array("update" => "kernel_autoparse",
                           "set"    => array("type"    => $this->lat->input['type'],
                                             "data"    => $this->lat->input['data'],
                                             "site"    => $this->lat->input['site'],
            								 "content" => $this->lat->input['content']),
                           "where"  => "id=".$this->lat->input['id']);
            $msg = "Autoparse entry updated.";
        }
        else
        {
            $query = array("insert" => "kernel_autoparse",
                           "data"   => array("type"    => $this->lat->input['type'],
                                             "data"    => $this->lat->input['data'],
                                             "site"    => $this->lat->input['site'],
            								 "content" => $this->lat->input['content']));
            $msg = "Autoparse entry created.";
        }

        $this->lat->sql->query($query);
        $this->lat->sql->cache("autoparse");

		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => $search));
			}
		}

        $this->lat->core->redirect($this->lat->url."pg=cp_global;do=autoparse", $msg);
    }


    // +-------------------------+
    //   Delete Filter
    // +-------------------------+

	function delete_autoparse()
    {
        $this->lat->core->load_cache("autoparse");

        if($this->lat->cache['autoparse'][$this->lat->input['id']]['type'] == 0)
        {
        	$search = $this->lat->cache['autoparse'][$this->lat->input['id']]['data'];
        }
        else
        {
        	$search = $this->lat->cache['autoparse'][$this->lat->input['id']]['site'];
        }

		foreach($this->lat->cache['page'] as $page)
		{
			if($page['system'])
			{
				$this->lat->core->load_module($page['name']);
				$this->lat->module->$page['name']->latova_system(array("type" => "reparse", "text" => $this->lat->parse->sql_text($search)));
			}
		}

		$query = array("delete" => "kernel_autoparse",
					   "where"  => "id=".$this->lat->input['id']);

		$this->lat->sql->query($query);
		$this->lat->sql->cache("autoparse");
		$this->lat->core->redirect($this->lat->url."pg=cp_global;do=autoparse", "Autoparse entry deleted.");
    }


}