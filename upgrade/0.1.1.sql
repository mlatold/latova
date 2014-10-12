UPDATE `lat_topic` SET stick='' WHERE stick=0;

UPDATE `lat_local_skin` SET `skin` = '<div class="bdr">
	<h1>{$div[0]}{$category_name}</h1>
	<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr" id="{$ftype}{$category}" style="{$div[1]}">{$forum_html}
	</table>
</div>
<div class="clear"></div>
' WHERE `label` = 'category_forums' AND `pg` = 'forum' AND `sid` = 1;
