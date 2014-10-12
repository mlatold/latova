var qs_used = 0;

function unhide(object)
{
	var data = new Array();

	var obj = get_element(object);
	var obj_hidden = get_element(object + "_hidden");

	$("#" + object).slideUp("slow");
	obj_hidden.style.display = "";
}

function open_ajax()
{
	var ajax;
	
	// Works for modern browsers
	try
	{
		ajax = new XMLHttpRequest();
	}
	// Works for IE browsers
	catch (e)
	{
		try
		{
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				return;
			}
		}
	}
	
	return ajax;
}

function do_image(num)
{
	var check_again = false;
	for (var i=1; i <= num; i++) 
	{
		var img = "img_" + i;
		var new_img = new Image();
		new_img.src = document.images[img].src;
		var h = new_img.height;
		var w = new_img.width;
		var img_max_h = img_h[img_type[i]];
		var img_max_w = img_w[img_type[i]];
		var obj_div = get_element("div_" + i);
		
		if(h > 0 && w > 0 && obj_div.style.display !="")
		{
			if((h > img_max_h || w > img_max_w) && img_max_h > 0 && img_max_w > 0)
			{
				var ratio = h / w;
					
				if ((img_max_h / img_max_w) > ratio) 
				{
					document.images[img].width = img_max_w;
					document.images[img].height = img_max_w * ratio;
					obj_div.style.width = img_max_w + "px";
				}
				else 
				{
					document.images[img].height = img_max_h;
					document.images[img].width = img_max_h / ratio;
					obj_div.style.width = (img_max_h / ratio) + "px";
				}
				obj_div.style.display = "";
			}
			else
			{
				var obj_zoom = get_element("zoom_" + i);
				obj_zoom.style.display = "none";
			
				obj_div.className = "";
				obj_div.style.display = "inline";
			}
		}
		else
		{
			check_again = true;
		}
	}
	
	if(check_again == true)
	{
		setTimeout("do_image(" + num + ")", 100);
	}
}

function clear_qs(txt)
{
	if (!qs_used)
	{
		qs_used = 1;
		txt.value = "";
	}
}

function help(msg, obj)
{
	var obj_help = get_element("help");
	msg = msg.replace(/{/g, "<");
	msg = msg.replace(/}/g, ">");
	obj_help.innerHTML = msg;

	var offtop = obj.offsetTop;
	var offleft = obj.offsetLeft;
	var pelement = obj.offsetParent;

	while (pelement!=null)
	{
		offtop += pelement.offsetTop;
		pelement = pelement.offsetParent;
	}

	pelement = obj.offsetParent;
	while (pelement!=null)
	{
		offleft += pelement.offsetLeft;
		pelement = pelement.offsetParent;
	}

	obj_help.style.left = offleft + "px";
	obj_help.style.top = (offtop + 20) + "px";
	obj_help.style.display = "none";
	$("#help").fadeIn("fast");
}

function unhelp()
{
	var obj_help = get_element("help");
	$("#help").fadeOut("fast");
}

function check_on(id)
{
	var obj_form = get_element(id);

	for (i = 0; i < obj_form.length; i++)
	{
		if(obj_form[i].disabled == false && obj_form[i].type == "checkbox")
		{
			obj_form[i].checked = true;
		}
	}
}

function check_off(id)
{
	var obj_form = get_element(id);

	for (i = 0; i < obj_form.length; i++)
	{
		if(obj_form[i].disabled == false && obj_form[i].type == "checkbox")
		{
			obj_form[i].checked = false;
		}
	}
}

function check_invert(id)
{
	var obj_form = get_element(id);

	for (i = 0; i < obj_form.length; i++)
	{
		if(obj_form[i].disabled == false && obj_form[i].type == "checkbox")
		{
			if(obj_form[i].checked == false)
			{
				obj_form[i].checked = true;
			}
			else
			{
				obj_form[i].checked = false;
			}
		}
	}
}

function check_selective(id, val)
{
	var obj_form = get_element(id);

	for (i = 0; i < obj_form.length; i++)
	{
		if(obj_form[i].value == val)
		{
			obj_form[i].checked = true;
		}
		else
		{
			obj_form[i].checked = false;
		}
	}
}

function get_element(object)
{
	if(document.getElementById)
	{
		return document.getElementById(object);
	}
	else if(document.all)
	{
		return document.all[object];
	}
	else if(document.layers)
	{
		return document.layers[object];
	}
}

function toggle(object, fade)
{
	var data = new Array();
	if (typeof fade == 'undefined' ) fade = false;

	if(minimized_cookie = get_cookie("minimized"))
	{
		minimized = minimized_cookie.split("|");

		for (i in minimized)
		{
			if (minimized[i] != object && minimized[i] != "")
			{
				data[data.length] = minimized[i];
			}
		}
	}

	var obj = get_element(object);
	var obj_img = get_element("img_" + object);

	if(object == "pm_popup")
	{
		obj.style.display = "none";
	}
	else if(obj_img.alt == "+")
	{
		out_cookie("minimized", data.join("|"), 0);
		obj_img.src = image_url + "collapse.png";
		obj_img.alt = "-";
		if(fade)
		{
			$("#" + object).fadeIn("slow");
		}
		else
		{
			$("#" + object).slideDown("slow");
		}
	}
	else if(obj_img.alt == "-")
	{
		data[data.length] = object;
		out_cookie("minimized", data.join("|"), 0);
		obj_img.src = image_url + "expand.png";
		obj_img.alt = "+";
		if(fade)
		{
			$("#" + object).fadeOut("slow");
		}
		else
		{
			$("#" + object).slideUp("slow");
		}
	}
}

function out_cookie(name, content, session)
{
	data = "";

	if(!session)
	{
		date = new Date();
		date.setTime(date.getTime() + (1000 * 86400 * 365));

		data = data + " expires=Mon, 10 Jun 2020 10:00:00 GMT;";
	}

	if(cookie_domain)
	{
		data = data + " domain=" + cookie_domain + ";";
	}

	if(cookie_path)
	{
		data = data + " path=" + cookie_path + ";";
	}

	document.cookie = cookie_prefix + name + "=" + content + ";" + data;
}

function get_cookie(name)
{
	var cookie_array = document.cookie.split(';');
	var cookie_name = cookie_prefix + name + "=";

	for(var i = 0; i < cookie_array.length;i++)
	{
		while(cookie_array[i].charAt(0) == " ")
		{
			cookie_array[i] = cookie_array[i].substring(1, cookie_array[i].length);
		}

		if (cookie_array[i].indexOf(cookie_name) == 0)
		{
			return cookie_array[i].substring(cookie_name.length, cookie_array[i].length);
		}
	}
	return null;
}

function redirect(url, prefix)
{
	if(!url || !prefix)
	{
		return;
	}
	else
	{
		window.location = prefix + url;
	}
}

function show_pm(num)
{
	var obj = get_element("pm_popup");
	var obj_width = 0;
	var obj_height = 0;

	if(window.innerHeight + window.scrollY - 50 > 0)
	{
		obj_height = window.innerHeight + window.scrollY - 50;
	}
	else if(self.innerHeight + window.pageYOffset - 50 > 0)
	{
		obj_height = self.innerHeight + window.pageYOffset - 50;
	}
	else if(document.documentElement.offsetHeight + document.documentElement.scrollTop - 50 > 0)
	{
		obj_height = document.documentElement.offsetHeight + document.documentElement.scrollTop - 50;
	}

	if(window.innerWidth + window.scrollX - 155 > 0)
	{
		obj_width = window.innerWidth + window.scrollX - 155;
	}
	else if(self.innerWidth + window.pageXOffset - 155 > 0)
	{
		obj_width = self.innerWidth + window.pageXOffset - 155;
	}
	else if(document.documentElement.offsetWidth + document.documentElement.scrollLeft - 155 > 0)
	{
		obj_width = document.documentElement.offsetWidth + document.documentElement.scrollLeft - 155;
	}

	var off_height = obj.offsetHeight;
	var off_width = obj.offsetWidth;

	obj.style.left = obj_width - off_width / 2 + "px";
	obj.style.top = obj_height - off_height / 2 + "px";

	if(num > 0)
	{
		obj.style.display = "";
	}
	else
	{
		num++;
	}
	
	setTimeout("show_pm(" + num + ")", 100);
}
