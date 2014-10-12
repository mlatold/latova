function bbInsert(prefix, suffix, bname)
{
	if (document.post.data.createTextRange)
	{
		document.post.data.focus();
		var sel = document.selection;
		var range = sel.createRange();
		range.colapse;
		if((sel.type == "None" || sel.type == "Text") && range)
		{
			if(!range.text.length)
			{
				range.text = prefix + suffix;
				range.collapse(true);
				range.moveEnd('character', -suffix.length);
				range.moveStart('character', 0);
				range.select();
			}
			else
			{
				var newtext = prefix + range.text + suffix;
				range.text = newtext;
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', -newtext.length);
				range.select();
			}
		}
	}
	else if (document.post.data.selectionStart >= 0)
	{
		var scrollp = document.post.data.scrollTop;
		var sel = document.post.data.value.substr(document.post.data.selectionStart, document.post.data.selectionEnd - document.post.data.selectionStart);
		var selstart = document.post.data.selectionStart;

		document.post.data.value = document.post.data.value.substr(0, selstart) + prefix + sel + suffix + document.post.data.value.substr(document.post.data.selectionEnd);

		if (document.post.data.setSelectionRange)
		{
			if (sel.length)
			{
				document.post.data.setSelectionRange(selstart, selstart + prefix.length + sel.length + suffix.length);
			}
			else
			{
				document.post.data.setSelectionRange(selstart + prefix.length, selstart + prefix.length);
			}

			document.post.data.focus();
		}
		document.post.data.scrollTop = scrollp;
	}
	else
	{
		document.post.data.value += prefix + suffix;
		document.post.data.focus(document.post.data.value.length - 1);
	}
}


function optionInsert(val, tag)
{
    eval("document.post." + tag + ".selectedIndex = 0");

    if(!val)
    {
    	return;
	}
	else
	{
		bbInsert("[" + tag + "=" + val + "]", "[/" + tag + "]", tag);
	}
}

function smilieInsert(smilie)
{
	smilie = " " + smilie + " ";

	if (document.post.data.createTextRange)
	{
		document.post.data.focus();
		var sel = document.selection;
		var range = sel.createRange();
		range.colapse;

		range.text = smilie;
		range.collapse(true);
		range.moveEnd('character', -smilie.length);
		range.moveStart('character', smilie.length);
		range.select();
	}
	else if (document.post.data.selectionStart >= 0)
	{
		var sel = document.post.data.value.substr(document.post.data.selectionStart, document.post.data.selectionEnd - document.post.data.selectionStart);
		var selstart = document.post.data.selectionStart;
		var scrollp = document.post.data.scrollTop;

		document.post.data.value = document.post.data.value.substr(0, selstart) + sel + smilie + document.post.data.value.substr(document.post.data.selectionEnd);

		if (document.post.data.setSelectionRange)
		{
			document.post.data.setSelectionRange(selstart + smilie.length, selstart + smilie.length);

			document.post.data.focus();
		}

		document.post.data.scrollTop = scrollp;
	}
	else
	{
		document.post.data.value += smilie;
		document.post.data.focus(document.post.data.value.length - 1);
	}
}