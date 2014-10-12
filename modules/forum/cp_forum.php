<?php if(!defined('LAT')) die("Access Denied.");

class module_cp_forum
{
    function initialize()
    {
        $this->lat->core->load_class("forum", "cp_forum");
        switch($this->lat->input['do'])
        {
            // Submit Edit forums
            case "submit_edit":
                $this->submit_edit();
                break;
            // Edit forums
            case "edit":
                $this->edit();
                break;
            // Delete or Empty forums
            case "delete":
                $this->delete_empty();
                break;
            // Manage Profiles
            case "profile":
                $this->profile();
                break;
            // Edit Profiles
            case "profile_edit":
                $this->profile_edit();
                break;
            // Submit Edit Profiles
            case "submit_profile_edit":
                $this->submit_profile_edit();
                break;
            // Delete Profiles
            case "profile_delete":
                $this->profile_delete();
                break;
            // Main overview
            default:
                $this->manage();
                break;
        }
    }


    // +-------------------------+
    //   Delete forum profiles
    // +-------------------------+
    // Will empty or delete a forum based upon the situation

    function profile_delete()
    {
        $this->lat->nav[] = array("Forum Profiles", "pg=cp_forum;do=profile");
        $this->lat->nav[] = $this->lat->title = "Delete Forum Profile";

        if(!$this->lat->cache['forum'][$this->lat->input['id']]['posts'] && !$this->lat->cache['forum'][$this->lat->input['id']]['topics'] && !empty($this->lat->inc->forum->pforums[$this->lat->input['id']]))
        {
            $this->lat->core->error("You cannot delete this forum, it currently has subforums.");
        }

        if($this->lat->raw_input['submit'])
        {
            $this->lat->core->check_key();

            $query = array("delete" => "forum_profile",
                           "where"  => "id=".$this->lat->input['id']);

            $this->lat->sql->query($query);
            $this->lat->sql->cache("forum_profile");

            $this->lat->core->redirect($this->lat->url."pg=cp_forum;do=profile".$url, "Forum profile deleted.");
        }
        else
        {
            $this->lat->inc->cp->form = "pg=cp_forum;do=profile_delete";

            $this->lat->output = <<<LAT
        <div class="bdr2" style="text-align: center">
            You are about to permanently remove all permissions associated with the following forum profile:<br /><b>{$this->lat->cache['forum_profile'][$this->lat->input['id']]['name']}</b>
            <br /><br />
            <span class="fail">This action CANNOT be undone. Make <u>absolutely</u> sure this is what you want to do!</span>
        </div>
        <h3><input type="submit" name="submit" class="form_button" value="Delete Profile" /></h3>
LAT;
        }
    }


    // +-------------------------+
    //   Submit Profile Edit
    // +-------------------------+
    // Submits forum profile either for edit or completely new

    function submit_profile_edit()
    {
        $this->lat->core->check_key_form();

        $this->lat->get_input->whitelist("view_index", array(0, 1));
        $this->lat->get_input->whitelist("view_topics", array(0, 1));
        $this->lat->get_input->whitelist("view_posts", array(0, 1));
        $this->lat->get_input->whitelist("post_replies_own", array(0, 1));
        $this->lat->get_input->whitelist("post_replies_other", array(0, 1));
        $this->lat->get_input->whitelist("post_topics", array(0, 1));
        $this->lat->get_input->whitelist("quick_reply", array(0, 1));
        $this->lat->get_input->whitelist("own_lock", array(0, 1));
        $this->lat->get_input->whitelist("own_delete", array(0, 1));
        $this->lat->get_input->whitelist("own_move", array(0, 1));
        $this->lat->get_input->whitelist("own_edit", array(0, 1));
        $this->lat->get_input->whitelist("own_edit_title", array(0, 1));
        $this->lat->get_input->whitelist("own_delete_posts", array(0, 1));
        $this->lat->get_input->whitelist("use_bb", array(0, 1));
        $this->lat->get_input->whitelist("use_smi", array(0, 1));
        $this->lat->get_input->preg_whitelist("forums", "0-9,");

        if($this->lat->get_input->sql_text("name") == "")
        {
            $this->lat->form_error[] = "The name box was left empty.";
            return $this->profile_edit();
        }

        foreach($this->lat->cache['group'] as $group)
        {
            if($this->lat->get_input->whitelist("g_".$group['id'], array(0, 1)))
            {
                $gid[] = $group['id'];
            }
        }

        if(!empty($gid))
        {
            $gid_profile = implode(",", $gid);
        }

        if($this->lat->input['id'])
        {
            $query = array("update" => "forum_profile",
                           "set"    => array("name"               => $this->lat->input['name'],
                                             "groups"             => $gid_profile,
            								 "forums"			  => implode(",", $this->lat->input['forums']),
                                             "view_index"         => $this->lat->input['view_index'],
                                             "view_topics"        => $this->lat->input['view_topics'],
                                             "view_posts"         => $this->lat->input['view_posts'],
                                             "view_topics"        => $this->lat->input['view_topics'],
                                             "post_replies_own"   => $this->lat->input['post_replies_own'],
                                             "post_replies_other" => $this->lat->input['post_replies_other'],
                                             "post_topics"        => $this->lat->input['post_topics'],
                                             "quick_reply"        => $this->lat->input['quick_reply'],
                                             "own_delete"         => $this->lat->input['own_delete'],
                                             "own_move"           => $this->lat->input['own_move'],
                                             "own_edit"           => $this->lat->input['own_edit'],
                                             "own_edit_title"     => $this->lat->input['own_edit_title'],
                                             "own_delete_posts"   => $this->lat->input['own_delete_posts'],
                                             "use_bb"             => $this->lat->input['use_bb'],
                                             "use_smi"            => $this->lat->input['use_smi']),
                           "where"  => "id=".$this->lat->input['id']);
            $msg = "Forum profile updated.";
        }
        else
        {
            $query = array("insert"   => "forum_profile",
                           "data"     => array("name"               => $this->lat->input['name'],
                                               "groups"             => $gid_profile,
            								   "forums"				=> implode(",", $this->lat->input['forums']),
                                               "view_index"         => $this->lat->input['view_index'],
                                               "view_topics"        => $this->lat->input['view_topics'],
                                               "view_posts"         => $this->lat->input['view_posts'],
                                               "view_topics"        => $this->lat->input['view_topics'],
                                               "post_replies_own"   => $this->lat->input['post_replies_own'],
                                               "post_replies_other" => $this->lat->input['post_replies_other'],
                                               "post_topics"        => $this->lat->input['post_topics'],
                                               "quick_reply"        => $this->lat->input['quick_reply'],
                                               "own_delete"         => $this->lat->input['own_delete'],
                                               "own_move"           => $this->lat->input['own_move'],
                                               "own_edit"           => $this->lat->input['own_edit'],
                                               "own_edit_title"     => $this->lat->input['own_edit_title'],
                                               "own_delete_posts"   => $this->lat->input['own_delete_posts'],
                                               "use_bb"             => $this->lat->input['use_bb'],
                                               "use_smi"            => $this->lat->input['use_smi']));

            $msg = "Forum profile created.";
        }

        $this->lat->sql->query($query);
        $this->lat->sql->cache("forum_profile");
        $this->lat->core->redirect($this->lat->url."pg=cp_forum;do=profile", $msg);
    }


    // +-------------------------+
    //   Edit forum profiles
    // +-------------------------+
    // Edit the forum profiles

    function profile_edit()
    {
        $this->lat->inc->cp->form = "pg=cp_forum;do=submit_profile_edit";
        $this->lat->nav[] = array("Forum Profiles", "pg=cp_forum;do=profile");

        if($this->lat->input['id'])
        {
            if($this->lat->input['do'] != "submit_profile_edit")
            {
                $this->lat->input['name'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['name'];
                $this->lat->input['view_index'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['view_index'];
                $this->lat->input['view_topics'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['view_topics'];
                $this->lat->input['view_posts'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['view_posts'];
                $this->lat->input['post_replies_own'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['post_replies_own'];
                $this->lat->input['post_replies_other'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['post_replies_other'];
                $this->lat->input['post_topics'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['post_topics'];
                $this->lat->input['quick_reply'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['quick_reply'];
                $this->lat->input['own_lock'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_lock'];
                $this->lat->input['own_delete'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_delete'];
                $this->lat->input['own_move'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_move'];
                $this->lat->input['own_edit'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_edit'];
                $this->lat->input['own_edit_title'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_edit_title'];
                $this->lat->input['own_delete_posts'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['own_delete_posts'];
                $this->lat->input['use_bb'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['use_bb'];
                $this->lat->input['use_smi'] = $this->lat->cache['forum_profile'][$this->lat->input['id']]['use_smi'];

                $selected = explode(",", $this->lat->cache['forum_profile'][$this->lat->input['id']]['groups']);
                $this->lat->input['forums'] = explode(",", $this->lat->cache['forum_profile'][$this->lat->input['id']]['forums']);
            }
            else
            {
                foreach($this->lat->cache['group'] as $group)
                {
                    if($this->lat->get_input->whitelist("g_".$group['id'], array(0, 1)))
                    {
                        $selected[] = $group['id'];
                    }
                }

                $this->lat->get_input->preg_whitelist("forums", "0-9,");
            }
            $this->lat->nav[] = $this->lat->title = "Editing Profile: ".$this->lat->cache['forum_profile'][$this->lat->input['id']]['name'];
        }
        else
        {
            $this->lat->nav[] = $this->lat->title = "New Forum Profile";
        }

        // General information stuff
        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "General Information"));

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "name",
                                             "title" => "Name",
                                             "value" => $this->lat->input['name'],
                                             "field" => array("maxlength" => 45)));

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Groups"));

        $this->lat->inc->cp->construct(array("type"     => "group",
                                             "name"     => "g",
                                             "selected" => $selected));

        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Forums"));

        $dropdown = $this->lat->inc->forum->generate_list(array("type" => "checkbox", "select_category" => true, "selected" => $this->lat->input['forums'], "prefix" => "<div><b>", "suffix" => "</b></div>", "skip_permissions" => true, "show_links" => true));

        $this->lat->output .= <<<LAT
            <div class="left">
                &nbsp;
            </div>
            <div class="right">
                {$dropdown}
            </div>
            <div class="clear"></div>

LAT;

        // Massive Permission Checkbox lisssttttt
        $this->lat->inc->cp->construct(array("type"  => "header",
                                             "title" => "Permissions"));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "view_index",
                                             "title" => "View forum on index",
                                             "value" => $this->lat->input['view_index']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "view_topics",
                                             "title" => "View topics in forum",
                                             "value" => $this->lat->input['view_topics']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "view_posts",
                                             "title" => "View posts in forum",
                                             "value" => $this->lat->input['view_posts']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "post_replies_own",
                                             "title" => "Post replies in own topic",
                                             "value" => $this->lat->input['post_replies_own']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "post_replies_other",
                                             "title" => "Post replies in other peoples topics",
                                             "value" => $this->lat->input['post_replies_other']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "post_topics",
                                             "title" => "Post topics",
                                             "value" => $this->lat->input['post_topics']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "quick_reply",
                                             "title" => "Use quick reply",
                                             "value" => $this->lat->input['quick_reply']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_lock",
                                             "title" => "Lock own topics",
                                             "value" => $this->lat->input['own_lock']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_delete",
                                             "title" => "Delete own topics",
                                             "value" => $this->lat->input['own_delete']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_move",
                                             "title" => "Move own topics",
                                             "value" => $this->lat->input['own_move']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_edit_title",
                                             "title" => "Edit own topic titles",
                                             "value" => $this->lat->input['own_edit_title']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_edit",
                                             "title" => "Edit own posts",
                                             "value" => $this->lat->input['own_edit']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "own_delete_posts",
                                             "title" => "Delete own posts",
                                             "value" => $this->lat->input['own_delete_posts']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "use_bb",
                                             "title" => "Use BBtags in posts",
                                             "value" => $this->lat->input['use_bb']));

        $this->lat->inc->cp->construct(array("type"  => "checkbox",
                                             "name"  => "use_smi",
                                             "title" => "Use smilies in posts",
                                             "value" => $this->lat->input['use_smi']));

        $this->lat->inc->cp->construct(array("type"   => "finish",
                                             "button" => array("value" => "Submit")));
    }


    // +-------------------------+
    //   Manage forum profiles
    // +-------------------------+
    // Manage the forum profiles

    function profile()
    {
        $this->lat->nav[] = $this->lat->title = "Forum Profiles";
        $this->lat->inc->cp->form = "pg=cp_forum;do=profile";

        if(!empty($this->lat->cache['forum_profile']))
        {
            foreach($this->lat->cache['forum_profile'] as $profile)
            {
                $out .= <<<LAT

        <tr>
            <td class="cell_1_first">
                <div style="float: right">{$drop}</div>
                <div style="overflow: hidden; float: left;" class="category_title">
                    {$profile['name']}
                </div>
            </td>
            <td class="cell_1" width="200" align="center">
                <b><a href="{$this->lat->url}pg=cp_forum;do=profile_delete;id={$profile['id']}">Delete</a> / <a href="{$this->lat->url}pg=cp_forum;do=profile_edit;id={$profile['id']}">Edit</a></b>
            </td>
        </tr>
LAT;
            }

            $this->lat->output .= <<<LAT
    <table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
{$out}
    </table>
LAT;
            if($this->lat->inc->cp_forum->drop_exists)
            {
                $this->lat->output .= <<<LAT

    <h3><input type="submit" name="submit" class="form_button" value="Resort Forums" /></h3>
LAT;
            }
        }
        else
        {
            $this->lat->output .= <<<LAT
        <div class="bdr2">
            No forums exist.
        </div>
LAT;
        }

        $this->lat->inc->cp->footer .= <<<LAT

    <div class="clear"></div>
    <a href="{$this->lat->url}pg=cp_forum;do=profile_edit"><big><img src="{$this->lat->image_url}button_new.png" alt="" />New profile</big></a>

LAT;
    }


    // +-------------------------+
    //   Delete & Empty forums
    // +-------------------------+
    // Will empty or delete a forum based upon the situation

    function delete_empty()
    {
        $this->lat->inc->forum->sort_parent();
        $this->lat->nav[] = array("Forum Management", "pg=cp_forum");

        if(!$this->lat->cache['forum'][$this->lat->input['id']]['posts'] && !$this->lat->cache['forum'][$this->lat->input['id']]['topics'] && !empty($this->lat->inc->forum->pforums[$this->lat->input['id']]))
        {
            $this->lat->core->error("You cannot delete this forum, it currently has subforums.");
        }

        if($this->lat->raw_input['submit'])
        {
            $this->lat->core->check_key();

            $parent1 = $this->lat->cache['forum'][$this->lat->input['id']]['parent'];
            $parent2 = $this->lat->cache['forum'][$parent1]['parent'];

            // Empty
            if($this->lat->cache['forum'][$this->lat->input['id']]['posts'] || $this->lat->cache['forum'][$this->lat->input['id']]['topics'])
            {
                if($parent1 && $parent2)
                {
                    $url = ";id=".$parent1;
                }

                $query = array("select" => "id",
                               "from"   => "topic",
                               "where"  => "fid=".$this->lat->input['id']);

                while($t = $this->lat->sql->query($query))
                {
                    $id[] = $t['id'];
                }

                $this->lat->core->load_class("forum", "moderate");
                $this->lat->inc->moderate->topic(array("type" => "purge",
                                                       "id"   => $id));

                $this->lat->core->redirect($this->lat->url."pg=cp_forum".$url, "Forum emptied.");
            }
            // Delete
            else
            {
                if($parent1 && $parent2)
                {
                    $url = ";id=".$parent2;
                }

                $query = array("update" => "forum",
                               "set"    => "o=o-1",
                               "where"  => "o > {$this->lat->cache['forum'][$this->lat->input['id']]['o']} AND parent={$this->lat->cache['forum'][$this->lat->input['id']]['parent']}");

                $this->lat->sql->query($query);

                $query = array("delete" => "forum",
                               "where"  => "id=".$this->lat->input['id']);

                $this->lat->sql->query($query);
                $this->lat->sql->cache("forum");
                $this->lat->inc->cp_forum->sync_profiles();

                $this->lat->core->redirect($this->lat->url."pg=cp_forum".$url, "Forum deleted.");
            }
        }
        else
        {
            $this->lat->inc->cp->form = "pg=cp_forum;do=delete";

            if($this->lat->cache['forum'][$this->lat->input['id']]['posts'] || $this->lat->cache['forum'][$this->lat->input['id']]['topics'])
            {
                $this->lat->nav[] = $this->lat->title = "Empty Forum";
                $body = "You are about to permanently remove all topics, posts, polls from the following forum:<br /><b>{$this->lat->cache['forum'][$this->lat->input['id']]['name']}</b>";
                $button = "Empty Forum";
            }
            else
            {
                $this->lat->nav[] = $this->lat->title = "Delete Forum";
                $body = "You are about to permanently delete the following forum:<br /><b>{$this->lat->cache['forum'][$this->lat->input['id']]['name']}</b>";
                $button = "Delete Forum";
            }

            $this->lat->output = <<<LAT
        <div class="bdr2" style="text-align: center">
            {$body}
            <br /><br />
            <span class="fail">This action CANNOT be undone. Make <u>absolutely</u> sure this is what you want to do!</span>
        </div>
        <h3><input type="submit" name="submit" class="form_button" value="{$button}" /></h3>
LAT;
        }
    }


    // +-------------------------+
    //   Submit Forum
    // +-------------------------+
    // Submits forum either for edit or completely new

    function submit_edit()
    {
        $this->lat->core->check_key_form();

        if($this->lat->get_input->sql_text("name") == "")
        {
            $this->lat->form_error[] = "The name box was left empty.";
        }

        if($this->lat->get_input->whitelist("is_forum", array(0, 1)))
        {
            $this->lat->get_input->whitelist("ort", array(0, 1));
            $this->lat->get_input->ranged_int("odr", array(0, 5));
            if(!$this->lat->get_input->unsigned_int("parent_forum"))
            {
                $this->lat->form_error[] = "You didn't select a parent forum.";
            }
            elseif(!$this->lat->cache['forum'][$this->lat->input['parent_forum']]['id'])
            {
                $this->lat->form_error[] = "Invalid parent forum selected.";
            }

            $this->lat->get_input->sql_text("link");
            $this->lat->get_input->br_sql_text("desc");

            if($this->lat->cache['forum'][$this->lat->input['id']]['link'] != $this->lat->get_input->sql_text("link"))
            {
                $click = 0;
            }
            else
            {
                $click = $this->lat->cache['forum'][$this->lat->input['id']]['link_clicks'];
            }
        }
        else
        {
            $this->lat->input['parent_forum'] = 0;
        }

        if(!empty($this->lat->form_error))
        {
            return $this->edit();
        }

        $this->lat->inc->forum->sort_parent();

        if($this->lat->input['id'])
        {
            $id = $this->lat->input['id'];
            if(count($this->lat->inc->forum->pforums[$this->lat->input['parent_forum']]) - 1 < $this->lat->cache['forum'][$id]['o'])
            {
                $this->lat->cache['forum'][$id]['o'] = count($this->lat->inc->forum->pforums[$this->lat->input['parent_forum']]) - 1;
            }

            if(!$this->lat->cache['forum'][$this->lat->input['id']]['id'])
            {
                $this->lat->core->error("This forum doesn't exist.");
            }

            if(!$this->lat->input['is_forum'] && ($this->lat->cache['forum'][$this->lat->input['id']]['posts'] || $this->lat->cache['forum'][$this->lat->input['id']]['topics']))
            {
                $this->lat->core->error("You can't make this forum into a category, it currently has topics in it.");
            }

            $query = array("update" => "forum",
                           "set"    => array("name"        => $this->lat->input['name'],
                                             "description" => $this->lat->input['desc'],
                                             "link"        => $this->lat->input['link'],
                                             "link_clicks" => $click,
                                             "parent"      => $this->lat->input['parent_forum'],
                                             "topic_or"    => $this->lat->input['ort'],
                                             "topic_order" => $this->lat->input['odr'],
                                             "o"           => $this->lat->cache['forum'][$id]['o']),
                           "where"  => "id=".$this->lat->input['id']);

            $this->lat->sql->query($query);
            $this->lat->sql->cache("forum");
            $this->lat->inc->forum->sync_forum($this->lat->input['id']);
            $msg = "Forum updated.";
        }
        else
        {
            $query = array("insert"   => "forum",
                           "data"     => array("name"        => $this->lat->input['name'],
                                               "description" => $this->lat->input['desc'],
                                               "link"        => $this->lat->input['link'],
                                               "parent"      => $this->lat->input['parent_forum'],
                                               "topic_or"    => $this->lat->input['ort'],
                                               "topic_order" => $this->lat->input['odr'],
                                               "o"           => count($this->lat->inc->forum->pforums[$this->lat->input['parent_forum']])));

            $id = $this->lat->sql->query($query);
            $this->lat->sql->cache("forum");
            $msg = "Forum created.";
        }

        if(!empty($this->lat->cache['forum_profile']))
        {
            foreach($this->lat->cache['forum_profile'] as $profile)
            {
                if($this->lat->get_input->whitelist("fp_".$profile['id'], array(0, 1)))
                {
                    $add[$profile['id']]['add_forums'][] = $id;
                }
                else
                {
                    $add[$profile['id']]['rem_forums'][] = $id;
                }
            }
            $this->lat->inc->cp_forum->sync_profiles($add);
        }

        $this->lat->core->redirect($this->lat->url."pg=cp_forum", $msg);
    }


    // +-------------------------+
    //   Edit Forum
    // +-------------------------+
    // Makes new forums and also edits old ones

    function edit()
    {
        $this->lat->inc->cp->form = "pg=cp_forum;do=submit_edit";
        $this->lat->nav[] = array("Forum Management", "pg=cp_forum");

        if($this->lat->input['id'])
        {
            $this->lat->title = $this->lat->nav[] = "Editing: ".$this->lat->cache['forum'][$this->lat->input['id']]['name'];
        }
        else
        {
            $this->lat->title = $this->lat->nav[] = "New Forum";
        }

        if($this->lat->input['do'] == "submit_edit")
        {
            $this->lat->get_input->br_text("name");
            $this->lat->get_input->br_text("link");
            $this->lat->get_input->ln_text("desc");
            $flist = $this->lat->get_input->form_select("forums");

            if($this->lat->input['id'])
            {
                if(!$this->lat->cache['forum'][$this->lat->input['id']]['id'])
                {
                    $this->lat->core->error("This forum doesn't exist.");
                }
            }

            if(!$this->lat->get_input->whitelist("is_forum", array(0, 1)))
            {
                $select = array(0=>" checked=\"checked\"");
                $hide = "display: none";
            }
            else
            {
                $select = array(1=>" checked=\"checked\"");
            }
        }
        elseif($this->lat->input['id'])
        {
            if(!$this->lat->cache['forum'][$this->lat->input['id']]['id'])
            {
                $this->lat->core->error("This forum doesn't exist.");
            }

            if(!$this->lat->cache['forum'][$this->lat->input['id']]['parent'])
            {
                $select = array(0=>" checked=\"checked\"");
                $hide = "display: none";
            }
            else
            {
                $select = array(1=>" checked=\"checked\"");
            }

            $this->lat->raw_input['name'] = $this->lat->cache['forum'][$this->lat->input['id']]['name'];
            $this->lat->raw_input['desc'] = $this->lat->cache['forum'][$this->lat->input['id']]['description'];
            $this->lat->raw_input['link'] = $this->lat->cache['forum'][$this->lat->input['id']]['link'];
            $flist = array($this->lat->cache['forum'][$this->lat->input['id']]['parent'] => " selected=\"selected\"");
            $this->lat->input['odr'] = $this->lat->cache['forum'][$this->lat->input['id']]['topic_order'];
            $this->lat->input['ort'] = $this->lat->cache['forum'][$this->lat->input['id']]['topic_or'];
        }
        else
        {
            $select = array(1=>" checked=\"checked\"");
            $flist = array(0 => true);
            $this->lat->input['ort'] = 0;
            $this->lat->input['odr'] = 0;
        }

        $odr[$this->lat->input['odr']] = " selected=\"selected\"";
        $ort[$this->lat->input['ort']] = " checked=\"checked\"";

        if(!empty($this->lat->cache['forum_profile']))
        {
            foreach($this->lat->cache['forum_profile'] as $forum_profile)
            {
                if($this->lat->input['do'] == "submit_edit")
                {
                    $checked = $this->lat->get_input->form_checkbox("fp_".$forum_profile['id']);
                }
                elseif($this->lat->input['id'])
                {
                    $fp = explode(",", $forum_profile['forums']);
                    if(in_array($this->lat->input['id'], $fp))
                    {
                        $checked = " checked=\"checked\"";
                    }
                    else
                    {
                        $checked = "";
                    }
                }

                if(count($profile1) == count($profile2))
                {
                    $profile1[] = "<label><input type=\"checkbox\" class=\"form_check\" name=\"fp_{$forum_profile['id']}\" value=\"1\"{$checked} /> ".ucfirst($forum_profile['name'])."</label>";
                }
                else
                {
                    $profile2[] = "<label><input type=\"checkbox\" class=\"form_check\" name=\"fp_{$forum_profile['id']}\" value=\"1\"{$checked} /> ".ucfirst($forum_profile['name'])."</label>";
                }
            }

            $forum_profile1 = implode("<br />", $profile1);
            if(!empty($profile2))
            {
                $forum_profile2 = implode("<br />", $profile2);
            }
        }
        else
        {
            $forum_profile1 = "<b>No forum profiles exist.</b>";
        }

        $this->lat->inc->forum->sort_parent();
        if(!empty($this->lat->inc->forum->pforums['0']))
        {
            foreach($this->lat->inc->forum->pforums['0'] as $cat)
            {
                if($cat != $this->lat->input['id'])
                {
                    $option_html .= "<option value=\"{$cat}\" style=\"font-weight: bold\"{$flist[$cat]}>{$this->lat->cache['forum'][$cat]['name']}</option>";

                    // Start makin' options!
                    if(!empty($this->lat->inc->forum->pforums[$cat]))
                    {
                        foreach($this->lat->inc->forum->pforums[$cat] as $pfid)
                        {
                            $option_html .= $this->lat->inc->cp_forum->option_parse($pfid, 0, $flist);
                        }
                    }
                }
            }

            $parent = "<select name=\"parent_forum\" class=\"form_select\">{$option_html}</select>";
        }

        if($option_html == "")
        {
            $parent = "No categories exist. You will need to make a new one.";
        }

        $this->lat->output .= <<<LAT
    <div class="bdr2">
        <div class="left">
            Type:
        </div>
        <div class="right">
            <div class="left" style="width: 90px"><label><input type="radio" name="is_forum" value="0" onclick="get_element('forum').style.display='none'"{$select[0]} /> Category</label></div>
            <div class="left" style="width: 90px"><label><input type="radio" name="is_forum" value="1" onclick="get_element('forum').style.display=''"{$select[1]} /> Forum</label></div>
        </div>
        <div class="clear"></div>
LAT;

        $this->lat->inc->cp->construct(array("type"  => "textbox",
                                             "name"  => "name",
                                             "title" => "Name",
                                             "value" => $this->lat->raw_input['name'],
                                             "field" => array("maxlength" => 45)));

        $this->lat->output .= <<<LAT
        <div id="forum" style="{$hide}">

LAT;

        $this->lat->inc->cp->construct(array("type"  => "textarea",
                                             "name"  => "desc",
                                             "title" => "Description",
                                             "value" => $this->lat->raw_input['desc']));

        $this->lat->output .= <<<LAT
            <div class="left">
                Parent Forum:
            </div>
            <div class="right">
                {$parent}
            </div>
            <div class="clear"></div>
LAT;

            $this->lat->inc->cp->construct(array("type"    => "dropdown",
                                                 "name"    => "odr",
                                                 "title"   => "Topic Order",
                                                 "options" => array(array("Last Post Date", 0),
                                                                    array("Topic Creation Date", 1),
                                                                    array("Topic Title", 2),
                                                                    array("Views", 3),
                                                                    array("Replies", 4),
                                                                    array("Topic Creator Name", 5)),
                                                 "default" => $this->lat->input['odr']));

        $this->lat->output .= <<<LAT
            <div class="left">
                Topic Orientation:
            </div>
            <div class="right">
                <div class="left" style="width: 120px"><label><input type="radio" name="ort" value="0"{$ort[0]} />Descending</label></div>
                <div class="left" style="width: 120px"><label><input type="radio" name="ort" value="1"{$ort[1]} />Ascending</label></div>
            </div>
            <div class="clear"></div>
LAT;

            $this->lat->inc->cp->construct(array("type"  => "textbox",
                                                 "name"  => "link",
                                                 "title" => "Link",
                                                 "help"  => "This will use the forum as a forwarder to link to another place. Leave it empty to disable it.",
                                                 "value" => $this->lat->input['link'],
                                                 "field" => array("maxlength" => 255)));

        $this->lat->output .= <<<LAT
        </div>
        <div class="left">
            <img src="{$this->lat->image_url}help.png" alt="" onmouseover="help('Check which permission profiles the forum should be a part of. The forum will inherit the best permissions possible from each one.', this)" onmouseout="unhelp()" class="help" />
            Permission Profiles:
        </div>
        <div class="right">
            <div class="left" style="width: 200px">
                {$forum_profile1}
            </div>
            <div class="left" style="width: 200px">
                {$forum_profile2}
            </div>
        </div>
        <div class="clear"></div>
LAT;

        $this->lat->inc->cp->construct(array("type"   => "finish",
                                             "button" => array("value" => "Submit")));
    }


    // +-------------------------+
    //   Manage Forums
    // +-------------------------+
    // The big forum manager thingy

    function manage()
    {
        $this->lat->inc->forum->sort_parent();
        if($this->lat->raw_input['submit'])
        {
            $this->lat->core->check_key();
            foreach($this->lat->cache['forum'] as $forum)
            {
                if($this->lat->get_input->unsigned_int("odr_".$forum['id']) > 0 && $forum['o'] != $this->lat->input['odr_'.$forum['id']] - 1)
                {
                    $this->lat->input['odr_'.$forum['id']]--;
                    if(count($this->lat->inc->forum->pforums[$forum['parent']]) - 1 < $this->lat->input['odr_'.$forum['id']])
                    {
                        $this->lat->input['odr_'.$forum['id']] = count($this->lat->inc->forum->pforums[$forum['parent']]) - 1;
                    }

                    $query = array("update" => "forum",
                                   "set"    => array("o" => $this->lat->input['odr_'.$forum['id']]),
                                   "where"  => "id=".$forum['id']);

                    $this->lat->sql->query($query);
                }
            }

            $this->lat->sql->cache("forum");
            unset($this->lat->inc->forum->pforums);
            $this->lat->inc->forum->sort_parent();
        }

        $this->lat->nav[] = array("Forum Management", "pg=cp_forum");
        $this->lat->title = "Forum Management";
        $this->lat->inc->cp_forum->nav_forums($this->lat->input['id']);
        $this->lat->inc->cp->form = "pg=cp_forum";

        if($this->lat->input['id'])
        {
            $cat = $this->lat->cache['forum'][$this->lat->input['id']]['parent'];
        }
        else
        {
            $cat = 0;
        }

        // Every category...
        if(!empty($this->lat->inc->forum->pforums[$cat]))
        {
            foreach($this->lat->inc->forum->pforums[$cat] as $category)
            {
                $manage = "&nbsp;";
                if(!$this->lat->input['id'])
                {
                    if(empty($this->lat->inc->forum->pforums[$category]))
                    {
                        $manage = "<a href=\"{$this->lat->url}pg=cp_forum;do=delete;id={$category}\">Delete</a> / <a href=\"{$this->lat->url}pg=cp_forum;do=edit;id={$category}\">Edit</a>";
                    }
                    else
                    {
                        $manage = "<a href=\"{$this->lat->url}pg=cp_forum;do=edit;id={$category}\">Edit</a>";
                    }
                }

                $drop = $this->lat->inc->cp->make_order_dropdown($category, count($this->lat->inc->forum->pforums[0]), $this->lat->cache['forum'][$category]['o']);
                $out .= <<<LAT

        <tr>
            <td class="sub_header">
                <div style="float: right">{$drop}</div>
                <div style="overflow: hidden; float: left;">
                    {$this->lat->cache['forum'][$category]['name']}
                </div>
            </td>
            <td class="sub_header" width="200" align="center">
                {$manage}
            </td>
        </tr>
LAT;
                if(!empty($this->lat->inc->forum->pforums[$category]))
                {
                    foreach($this->lat->inc->forum->pforums[$category] as $forum)
                    {
                        if(!empty($this->lat->inc->forum->pforums[$forum]))
                        {
                            $sub = "";
                            if(count($this->lat->inc->forum->pforums[$forum]) != 1)
                            {
                                $sub = "s";
                            }
                            $name = "<u><a href=\"{$this->lat->url}pg=cp_forum;id={$forum}\">{$this->lat->cache['forum'][$forum]['name']}</a></u> (".count($this->lat->inc->forum->pforums[$forum])." subforum{$sub})";
                        }
                        else
                        {
                            $name = $this->lat->cache['forum'][$forum]['name'];
                        }

                        if($this->lat->cache['forum'][$forum]['posts'])
                        {
                            $delete = "<a href=\"{$this->lat->url}pg=cp_forum;do=delete;id={$forum}\">Empty</a> / ";
                        }
                        elseif(empty($this->lat->inc->forum->pforums[$forum]))
                        {
                            $delete = "<a href=\"{$this->lat->url}pg=cp_forum;do=delete;id={$forum}\">Delete</a> / ";
                        }
                        else
                        {
                            $delete = "";
                        }

                        $drop = $this->lat->inc->cp->make_order_dropdown($forum, count($this->lat->inc->forum->pforums[$this->lat->cache['forum'][$forum]['parent']]), $this->lat->cache['forum'][$forum]['o']);
                        $out .= <<<LAT

        <tr>
            <td class="cell_1_first">
                <div style="float: right">{$drop}</div>
                <div style="overflow: hidden; float: left;" class="category_title">
                    {$name}
                </div>
            </td>
            <td class="cell_1" width="200" align="center">
                <b>{$delete}<a href="{$this->lat->url}pg=cp_forum;do=edit;id={$forum}">Edit</a></b>
            </td>
        </tr>
LAT;
                    }
                }
            }
            $this->lat->output .= <<<LAT
    <table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">
{$out}
    </table>
LAT;
            if($this->lat->inc->cp->drop_exists)
            {
                $this->lat->output .= <<<LAT

    <h3><input type="submit" name="submit" class="form_button" value="Resort Forums" /></h3>
LAT;
            }
        }
        else
        {
            if($this->lat->input['id'])
            {
                $this->lat->core->error("No subforums exist here.");
            }
            else
            {
                $this->lat->output .= <<<LAT
        <div class="bdr2">
            No forums exist.
        </div>
LAT;
            }
        }

        $this->lat->inc->cp->footer .= <<<LAT

    <div class="clear"></div>
    <a href="{$this->lat->url}pg=cp_forum;do=edit"><big><img src="{$this->lat->image_url}button_new.png" alt="" />New forum</big></a>

LAT;
    }
}
?>