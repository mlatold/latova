var quick_reply = 0;

function qr_toggle()
{
	var obj_qr = get_element("qr");

	if(quick_reply)
	{
		quick_reply = 0;
		obj_qr.style.display = "none";
	}
	else
	{
		obj_qr.style.display = "";
		quick_reply = 1;
	}
}

function sel_post(pid, obj)
{
	var data = new Array();

	if(obj == 0)
	{
		var posts_cookie = get_cookie("multiquote")
	}
	else
	{
		var posts_cookie = get_cookie("selposts")
	}

	if(posts_cookie)
	{
		posts = posts_cookie.split("|");

		for (i in posts)
		{
			if (posts[i] != pid && posts[i] != "")
			{
				data[data.length] = posts[i];
			}
		}
	}

	if(obj == 0)
	{
		var img_obj = get_element("img_" + pid);

		if(img_obj.alt == "-")
		{
			img_obj.src = image_url + "multi_off.png";
			img_obj.alt = "+";
		}
		else
		{
			img_obj.alt = "-";
			data[data.length] = pid;
			img_obj.src = image_url + "multi_on.png";
		}

		out_cookie("multiquote", data.join("|"), 1);
	}
	else
	{
		if(obj.checked == true)
		{
			data[data.length] = pid;
		}

		out_cookie("selposts", data.join("|"), 1);
	}
}