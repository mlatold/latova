function version_ajax(hash, ids)
{
	var idarray = ids.split(','); 
	var ajax = this.open_ajax();

	ajax.onreadystatechange = function()
	{
		if(ajax.readyState == 4)
		{
			if (ajax.status == 200) 
			{
				var resp = ajax.responseText;
				var resparray = resp.split('\n');
				for(i=0; i < resparray.length; i++) 
				{
					var respstr = resparray[i].split(':');
					get_element("url_" + hash + "_" + respstr[0]).innerHTML = respstr[1];
				}
			}
			else
			{
				for(i=0; i < idarray.length; i++) 
				{
					get_element("url_" + hash + "_" + idarray[i]).innerHTML = "<span class=\"fail\">Error</span>";
				}
			}
		}
	}

	ajax.open('GET', url + 'pg=cp;do=version_check;ids=' + ids, true);
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.setRequestHeader("Connection", "close");
	    
	ajax.send(null);



}