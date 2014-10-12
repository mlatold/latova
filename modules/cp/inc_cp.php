<?php if(!defined('LAT')) die("Access Denied.");

class inc_cp
{
	function initialize()
	{
		if(!$this->lat->user['id'])
		{
			$this->noncp_error = true;
			$this->lat->core->error("err_logged_out");
		}

		if(!$this->lat->user['group']['superadmin'])
		{
			$this->noncp_error = true;
			$this->lat->core->error("insufficient_permissions");
		}

		$this->lat->core->check_password("pg=cp");
		$this->lat->core->load_cache("page_cp");
		$this->lat->nav[] = array("Control Panel", "pg=cp");
	}


	// +-------------------------+
	//	 Make order dropdown
	// +-------------------------+
	// Dropdown used to reorder forums

	function make_order_dropdown($id, $num, $order=0)
	{
		if($num < 2)
		{
			return "&nbsp;";
		}

		for($i=0;$i < $num;$i++)
		{
			$selected = "";
			if($order == $i)
			{
				$selected = " selected=\"selected\"";
			}
			$drop .= "<option value=\"".($i+1)."\"{$selected}>".($i+1)."</option>";
		}
		$this->drop_exists = true;
		return "<select class=\"quick\" name=\"odr_{$id}\">{$drop}</select>";
	}

	function construct($data)
	{
		switch($data['type'])
		{
			// Header
			case "header":
				if($this->header_open)
				{
					$out .= <<<LAT
		</div>

LAT;
				}

				$out .= <<<LAT
		<h2>{$data['title']}</h2>
		<div class="bdr2">

LAT;

				if($data['help'])
				{
					$out .= <<<LAT
			<div class="tiny_text" style="margin-left: 30px">{$data['help']}</div>

LAT;
				}
				$this->header_open = true;
				break;
			// Footer
			case "footer":
				if($this->header_open)
				{
					$out .= <<<LAT
		</div>

LAT;
					$this->header_open = false;
				}
				break;
			case "button":
				if($data['class'] == "")
				{
					$data['class'] = "form_button";
				}

				if($data['button_type'] == "")
				{
					$data['button_type'] = "submit";
				}

				$data['field']['name'] = $data['name'];
				$data['field']['type'] = $data['button_type'];
				$data['field']['class'] = $data['class'];
				$data['field']['value'] = $this->lat->parse->no_text($data['value']);

				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<input{$field} />";
				break;
			// Finisher
			case "finish":
				$this->construct("type" == "footer");

				if($data['button'] !== "")
				{
					if(is_array($data['button'][0]))
					{
						foreach($data['button'] as $b)
						{

							$out .= $this->construct(array_merge($b, array("type" => "button", "return" => 1)));
						}
					}
					else
					{
						$out .= $this->construct(array_merge($data['button'], array("type" => "button", "return" => 1)));
					}



					$out = <<<LAT
		</div>
	<h3>{$out}</h3>
LAT;
				}
				break;
			// Small textbox
			case "textbox":
				$input = true;
				if(!$data['size'])
				{
					$data['size'] = "45";
				}

				if($data['class'] == "")
				{
					$data['class'] = "form_text";
				}

				$data['field']['name'] = $data['name'];
				$data['field']['type'] = $data['type'];
				$data['field']['size'] = $data['size'];
				$data['field']['class'] = $data['class'];
				$data['field']['value'] = $this->lat->parse->no_text($data['value']);
				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<input{$field} />";
				break;
			// Large text area
			case "textarea":
				$input = true;
				if(!$data['rows'])
				{
					$data['rows'] = "5";
				}

				if(!$data['cols'])
				{
					$data['cols'] = "50";
				}

				if($data['class'] == "")
				{
					$data['class'] = "form_text";
				}

				$data['field']['name'] = $data['name'];
				$data['field']['cols'] = $data['cols'];
				$data['field']['rows'] = $data['rows'];
				$data['field']['class'] = $data['class'];
				$data['value'] = $this->lat->parse->no_html($data['value']);
				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<textarea{$field}>{$data['value']}</textarea>";
				break;
			// Enabled/Disabled
			case "state":
				$input = true;
				if($data['class'] == "")
				{
					$data['class'] = "form_select";
				}

				$data['field']['type'] = "radio";
				$data['field']['name'] = $data['name'];
				$data['field']['class'] = $data['class'];
				$data['value'] = $this->lat->parse->whitelist($data['value'], array(0, 1));
				$ch[$data['value']] = "checked=\"checked\" ";
				ksort($data['field']);

				if($data['opt_title'] == "")
				{
					$data['opt_title'] = array("Disabled", "Enabled");
				}

				if(!$data['width'])
				{
					$data['width'] = 90;
				}

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<div class=\"left\" style=\"width: {$data['width']}px\"><label><input{$field} value=\"0\" {$ch[0]}/>{$data['opt_title'][0]}</label></div><div class=\"left\" style=\"width: {$data['width']}px\"><label><input{$field} value=\"1\" {$ch[1]}/>{$data['opt_title'][1]}</label></div>";
				break;
			// Dropdown
			case "dropdown":
				$input = true;
				if($data['class'] == "")
				{
					$data['class'] = "form_select";
				}

				$options = explode("\n", $config['extra']);

				foreach($data['options'] as $opt)
				{
					$opt[1] = trim($opt[1]);
					$selected = "";
					if($opt[1] == $data['default'])
					{
						$selected = " selected=\"selected\"";
					}

					$value .= "<option value=\"{$opt[1]}\"{$selected}>{$opt[0]}</option>";
				}

				$data['field']['name'] = $data['name'];
				$data['field']['class'] = $data['class'];
				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<select{$field}\">{$value}</select>";
				break;
			// Checkbox
			case "checkbox":
				$input = true;
				if($data['class'] == "")
				{
					$data['class'] = "form_check";
				}

				if($data['value'])
				{
					$data['field']['checked'] = "checked";
				}

				$data['field']['name'] = $data['name'];
				$data['field']['type'] = $data['type'];
				$data['field']['value'] = 1;
				$data['field']['class'] = $data['class'];
				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				$out = "<label><input{$field} />  {$data['title']}</label>";
				$data['title'] = "";
				break;
			case "group":
				$input = true;
				if($data['class'] == "")
				{
					$data['class'] = "form_check";
				}

				$data['field']['type'] = "checkbox";
				$data['field']['value'] = 1;
				$data['field']['class'] = $data['class'];
				ksort($data['field']);

				foreach($data['field'] as $n => $v)
				{
					if($v != "")
					{
						$field .= " {$n}=\"{$v}\"";
					}
				}

				foreach($this->lat->cache['group'] as $group)
				{
					if(!empty($data['selected']))
					{
						if(in_array($group['id'], $data['selected']))
						{
							$checked = " checked=\"checked\"";
						}
						else
						{
							$checked = "";
						}
					}

					if(count($group1) == count($group2))
					{
						$group1[] = "<label><input{$field} name=\"{$data['name']}_{$group['id']}\"{$checked} /> {$group['name']}</label>";
					}
					else
					{
						$group2[] = "<label><input{$field} name=\"{$data['name']}_{$group['id']}\"{$checked} /> {$group['name']}</label>";
					}
				}

				$group_list1 = implode("<br />", $group1);
				$group_list2 = implode("<br />", $group2);

				$out = "<div class=\"left\" style=\"width: 200px; overflow: auto;\">{$group_list1}</div><div class=\"left\" style=\"width: 200px; overflow: auto;\">{$group_list2}</div>";
				break;
		}

		if($data['help'])
		{
			$data['help'] = str_replace(array("<", ">", "'"), array("{", "}", "&amp;#39;"), $data['help']);
			$data['help'] = "<img class=\"help\" src=\"{$this->lat->image_url}help.png\" onmouseover=\"help('{$data['help']}', this)\" onmouseout=\"unhelp()\" alt=\"\" />";
		}

		if($input)
		{
			if($data['title'] == "")
			{
				$data['title'] = "&nbsp;";
			}
			else
			{
				$data['title'] .= ":";
			}

			$out = <<<LAT
			<div class="left">
				{$data['help']}{$data['title']}
			</div>
			<div class="right">
				{$out}
			</div>
			<div class="clear"></div>

LAT;
		}

		if($data['return'])
		{
			return $out;
		}
		else
		{
			$this->lat->output .= $out;
		}
	}

	function render()
	{
		if($this->noncp_error)
		{
			return;
		}

		$this->lat->show->js_files[] = $this->lat->config['MODULES_PATH']."cp/cp";
		$section_done = array();

		foreach($this->lat->cache['page_cp'] as $pg)
		{
			if(!in_array($pg['section'], $section_done))
			{
				$section_done[] = $pg['section'];
				$first = " class=\"first\"";
				$links = "";
				if(substr($pg['section'], 0, 1) == ".")
				{
					$pg_name = ucfirst(substr($pg['section'], 1));
				}
				else
				{
					$pg_name = ucfirst($pg['section']);
				}

				foreach($this->lat->cache['page_cp'] as $pg_cp)
				{
					if($pg_cp['section'] == $pg['section'])
					{
						$pg_cp_name = ucfirst($pg_cp['title']);
						$links .= "<li{$first}><a href=\"{$this->lat->url}{$pg_cp['link']}\">{$pg_cp_name}</a></li>";
						$first = "";
					}
				}

				$temp = <<<LAT
		<h2>{$pg_name}</h2>
		<div class="bdr2">
			<ul class="admin_list">
				{$links}
			</ul>
		</div>

LAT;
				if($pg['section'] == "settings")
				{
					$settings = $temp;
				}
				else
				{
					$sections .= $temp;
				}
			}
		}

		if($this->form)
		{
			if($this->lat->input['id'])
			{
				$this->form .= ";id=".$this->lat->input['id'];
			}

			$form = <<<LAT

<form action="{$this->lat->url}{$this->form}" method="post">
<input type="hidden" name="key" value="{$this->lat->user['key']}" />
LAT;

			$form_end = <<<LAT

</form>
LAT;
		}


		if(!$this->lat->session->error_occured)
		{
			$this->lat->output = <<<LAT
	<div class="bdr">
		<h1>{$this->lat->title}</h1>
{$this->lat->output}
	</div>
LAT;
		}
		$this->lat->output = <<<LAT
<div style="float: left; width: 200px;">
	<div class="bdr">
{$settings}
{$sections}
	</div>
</div>
<div style="margin-left: 205px;">
<!-- CP HEADER -->
{$this->header}{$form}
{$this->lat->output}
</div>{$form_end}
{$this->footer}
<div style="clear: both"></div>
LAT;
	}
}
?>