SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `lat_config`
--

CREATE TABLE IF NOT EXISTS `lat_config` (
  `name` varchar(20) NOT NULL,
  `value` text,
  `title` varchar(50) NOT NULL DEFAULT '',
  `help` text,
  `o` smallint(4) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `def` text,
  `section` varchar(50) NOT NULL DEFAULT '',
  `extra` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_config`
--

INSERT INTO `lat_config` (`name`, `value`, `title`, `help`, `o`, `type`, `def`, `section`, `extra`) VALUES
('advanced_header', '', 'Advanced', 'It''s probably not a good idea to change any of this unless you know what you''re doing.', 9900, 0, '', 'system', ''),
('autoimage_header', '', 'Auto Image Resize', '', 9900, 0, '', 'general', ''),
('avatar_ext', 'jpg,jpeg,gif,png', 'Avatar Extensions', 'File types separated by commas.', 1005, 1, 'jpg,jpeg,gif,png', 'member', ''),
('avatar_header', '', 'Avatar', '', 1000, 0, '', 'member', ''),
('avatar_height', '125', 'Max Avatar Height (px)', '', 1003, 1, '125', 'member', ''),
('avatar_link', '0', 'Avatar Linking', 'Latova will have very limited control of the image if you enable this.', 1002, 3, '0', 'member', ''),
('avatar_size', '25', 'Max Avatar Filesize (kb)', 'Only effects avatars uploaded to your server. Latova cant check linked avatars.', 1006, 1, '25', 'member', ''),
('avatar_upload', '1', 'Avatar Uploading', '', 1001, 3, '1', 'member', ''),
('avatar_url', 'storage/avatars/', '', '', 0, 255, '', '', ''),
('avatar_width', '125', 'Max Avatar Width (px)', '', 1004, 1, '125', 'member', ''),
('bots_header', '', 'Search Engine Spiders', '', 2000, 0, '', 'system', ''),
('bots_list', 'googlebot|Google|http://www.google.com\r\nslurp@inktomi|HotBot|http://www.hotbot.com\r\nlycos|Lycos|http://www.lycos.com\r\nia_archiver|Archive|http://www.archive.org\r\nAsk Jeeves|Ask Jeeves|http://www.ask.com\r\nmsnbot|MSN|http://www.msn.com\r\nYahoo!|Yahoo|http://www.yahoo.com', 'Spiders List', 'Spiders for latova to identify, one per line. Each line has 3 vars separated by pipe characters, which are as following (left to right):{br /}1. User agent string to search for.{br /}2. Name for latova to give it.{br /}3. Link to the spiders website.', 2002, 2, 'googlebot|Google|http://www.google.com\r\nslurp@inktomi|HotBot|http://www.hotbot.com\r\nlycos|Lycos|http://www.lycos.com\r\nia_archiver|Archive|http://www.archive.org\r\nAsk Jeeves|Ask Jeeves|http://www.ask.com\r\nmsnbot|MSN|http://www.msn.com\r\nYahoo!|Yahoo|http://www.yahoo.com', 'system', ''),
('bots_on', '1', 'Identify Spiders', 'Latova will separate search engine spiders from other users in the online list and show them. When turned off, bots will simply be identified as guests.', 2001, 3, '1', 'system', ''),
('content_header', '', 'Content Posting', '', 2000, 0, '', 'general', ''),
('cookie_domain', '127.0.0.1', 'Cookie Domain', 'Usually should be something like {b}.yoursite.com{/b}', 3001, 1, '', 'system', ''),
('cookie_header', '', 'Cookie', '', 3000, 0, '', 'system', ''),
('cookie_path', '/', 'Cookie Path', '', 3002, 1, '/', 'system', ''),
('decimal_format', '.', 'Decimal Separator', '', 1004, 1, '.', 'general', ''),
('default_lang', '1', '', '', 0, 1, '', '', ''),
('default_page', 'forum', 'Default Page', 'If no page is specified, this is the module that Latova accesses first.', 1006, 1, 'forum', 'system', ''),
('default_search', 'topic', 'Default search item', 'The thing that Latova searches for when no other guess can be made what they might want to be looking for first.', 3003, 1, 'topic', 'general', ''),
('default_skin', '1', '', '', 0, 1, '', '', ''),
('email_activate', '0', 'Require Email Activation', 'When enabled, registering members will get sent an email and have to click a link before they can login. Good way to prevent spam and make sure everybody is signing up with a real email address.', 3001, 3, '0', 'member', ''),
('email_header', '', 'Email', '', 1000, 0, '', 'comm', ''),
('forum_header', '', 'General', '', 1000, 0, '', 'forum', ''),
('gallery_ext', 'jpg,jpeg,gif,png', 'Avatar Gallery Extensions', 'File types separated by commas.', 1007, 1, '', 'member', ''),
('gallery_url', 'storage/gallery/', '', '', 0, 1, '', '', ''),
('gd_version', '-1', 'GD Version', 'Latova uses GD Library to manipulate uploaded images and create captchas. It is highly recommended you have this enabled.', 1004, 4, '-1', 'system', 'Auto Detect|-1\r\nDisableGD|0\r\nGD 2.0 or greater|1'),
('header_global', '', 'Global', '', 1000, 0, '', 'general', ''),
('hot_topic', '20', 'Posts to make Hot Topic', '', 2001, 1, '', 'forum', ''),
('img_0_h', '200', 'Max Signature Image Height (px)', '', 9901, 1, '', 'general', ''),
('img_0_w', '500', 'Max Signature Image Width (px)', '', 9902, 1, '', 'general', ''),
('img_1_h', '0', 'Max Small Image Height (px)', '', 9903, 1, '0', 'general', ''),
('img_1_w', '0', 'Max Small Image Width (px)', '', 9904, 1, '0', 'general', ''),
('img_2_h', '500', 'Max Standard Image Height (px)', '', 9905, 1, '500', 'general', ''),
('img_2_w', '500', 'Max Standard Image Width (px)', '', 9906, 1, '500', 'general', ''),
('img_3_h', '800', 'Max Big Image Height (px)', '', 9907, 1, '800', 'general', ''),
('img_3_w', '500', 'Max Big Image Width (px)', '', 9908, 1, '500', 'general', ''),
('logo_des', 'Generic description', 'Script Description', 'Usually appears on your page header, but may appear elsewhere.', 1002, 1, '', 'system', ''),
('long_date', '[F jS Y,] g:i a', 'Long Date', 'Date in PHP Date format which is the default put into places where theres a lot of space for a date.', 1001, 1, '', 'general', ''),
('mail_email', 'noreply@yoursite.com', 'General Email Address', 'When Latova sends out emails, it will appear to be coming from this address.', 1001, 1, '', 'comm', ''),
('mail_host', 'yoursite.com', 'SMTP Host', '', 2002, 1, '', 'comm', ''),
('mail_pass', '', 'SMTP Password', '', 2005, 1, '', 'comm', ''),
('mail_port', '25', 'SMTP Port', '', 2003, 1, '25', 'comm', ''),
('mail_smtp', '0', 'Use SMTP Mailer', '', 2001, 3, '0', 'comm', ''),
('mail_user', 'noreply@yoursite.com', 'SMTP Username', '', 2004, 1, '', 'comm', ''),
('max_polls', '5', 'Max Polls per Topic', '', 4001, 1, '5', 'forum', ''),
('max_polls_opt', '10', 'Max Options per Poll', '', 4002, 1, '10', 'forum', ''),
('name_change_days', '30', 'Name Chances Reset (days)', 'When someone changes their name once, a timer starts which will reset their number of chances after this amount of time.', 5001, 1, '30', 'member', ''),
('name_change_num', '3', 'Name Change Chances', 'Amount of times people can give themselves a new username before they''ll have to wait for the chance reset period.', 5002, 1, '3', 'member', ''),
('name_header', '', 'Name Changes', '', 5000, 0, '', 'member', ''),
('number_format', ',', 'Thousands Separator', '', 1003, 1, ',', 'general', ''),
('num_posts', '10', 'Default Posts per Page', '', 1002, 1, '10', 'forum', ''),
('num_topics', '15', 'Default Topics per Page', '', 1001, 1, '15', 'forum', ''),
('one_email', '1', 'One Email per Account', 'When enabled, Latova will prevent people from registering for multiple accounts with the same email.', 3002, 3, '1', 'member', ''),
('photo_ext', 'jpg,jpeg,gif,png', 'Photo Extensions', 'File types separated by commas.', 2005, 1, 'jpg,jpeg,gif,png', 'member', ''),
('photo_header', '', 'Photo', '', 2000, 0, '', 'member', ''),
('photo_height', '200', 'Max Photo Height (px)', '', 2003, 1, '200', 'member', ''),
('photo_link', '0', 'Photo Linking', 'Latova will have very limited control of the image if you enable this.', 2002, 3, '0', 'member', ''),
('photo_size', '35', 'Max Photo Filesize (kb)', 'Only effects photos uploaded to your server. Latova cant check linked avatars.', 2006, 1, '35', 'member', ''),
('photo_upload', '1', 'Photo Uploading', '', 2001, 3, '1', 'member', ''),
('photo_url', 'storage/photos/', '', '', 0, 1, '', '', ''),
('photo_width', '200', 'Max Photo Width (px)', '', 2004, 1, '200', 'member', ''),
('picon_table', '6', 'Max Post Icon Columns', '', 2001, 1, '6', 'general', ''),
('poll_header', '', 'Polls', '', 4000, 0, '', 'forum', ''),
('gallery_col', '5', 'Number of Gallery Columns', '', 1008, 1, '5', 'member', ''),
('recaptcha_header', '', 'Recaptcha', 'If you wish to use recaptcha instead of Latovas'' default captchas, you will need to sign up at <a href="http://www.recaptcha.net" target="_blank">recaptcha.net</a> and enter in the keys that you get from there.', 4000, 0, '', 'member', ''),
('recaptcha_private', '', 'Recaptcha Private Key', '', 4002, 1, '', 'member', ''),
('recaptcha_public', '', 'Recaptcha Public Key', '', 4001, 1, '', 'member', ''),
('register_header', '', 'Registration', '', 3000, 0, '', 'member', ''),
('script_name', 'Latova', 'Script Title', '', 1001, 1, '', 'system', ''),
('script_url', 'http://127.0.0.1/latova/upload/', 'Script URL', 'URL to the script, end with a /', 9902, 1, '', 'system', ''),
('search_header', '', 'Search', '', 3000, 0, '', 'general', ''),
('search_num', '3', 'Search Attempts', 'Number of searches you are allowed within the search period.', 3002, 1, '3', 'general', ''),
('search_time', '60', 'Search Period (minutes)', '', 3001, 1, '60', 'general', ''),
('session_length', '60', 'Session Length (minutes)', 'Amount of time before Latova deletes a session. When a session ends, members will have to login again, and if theyre submitting forms they will get a key error and have to submit the form again.', 1005, 1, '60', 'system', ''),
('short_date', '[M jS,] g:i a', 'Short Date', 'Date in PHP Date format which is the default put into places where theres not a lot of space.', 1002, 1, '[M jS,] g:i a', 'general', ''),
('short_page', 'topic|topic|\r\nforum|forum|view\r\npost|topic|find\r\nmember|member|profile\r\npm|msg|view\r\nsearch|global|view_search\r\nsearch_user|global|view_search', 'Short Pages', 'One per line. Each line has 3 vars separated by pipe characters, which are as following (left to right):{br /}1. Short page variable to look for.{br /}2. pg var to place.{br /}3. do var to place.{br /}{br /}Any variable captured in the short page format gets put into the id var.', 9901, 2, 'topic|topic|\r\nforum|forum|view\r\npost|topic|find\r\nmember|member|profile\r\npm|msg|view\r\nsearch|global|view_search\r\nsearch_user|global|view_search', 'system', ''),
('signature_header', '', 'Signature', '', 6000, 0, '', 'member', ''),
('sig_height', '200', 'Signature Height (px)', 'Prevent people from putting long content into signatures and cuts off anything after that point.{br /}{br /}Set to 0 to disable.', 6001, 1, '200', 'member', ''),
('smilies_table', '4', 'Max Smilie Columns', '', 2003, 1, '4', 'general', ''),
('smtp_header', '', 'SMTP Mailer', 'It is highly recommend you turn this on. When disabled, Latova falls back to a PHP Mailer which tends to go into spamboxes. You will need to fill out the fields below based upon what your servers SMTP details are.', 2000, 0, '', 'comm', ''),
('systembasic_header', '', 'Basic', '', 1000, 0, '', 'system', ''),
('timezone', '-5', 'Default Timezone', '', 1005, 4, '', 'general', '(GMT -12:00) Eniwetok, Kwajalein|-12\r\n(GMT -11:00) Midway Island, Samoa|-11\r\n(GMT -10:00) Hawaii|-10\r\n(GMT -9:00) Alaska|-9\r\n(GMT -8:00) Pacific Time (US &amp; Canada)|-8\r\n(GMT -7:00) Mountain Time (US &amp; Canada)|-7\r\n(GMT -6:00) Central Time (US &amp; Canada), Mexico City|-6\r\n(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima|-5\r\n(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz|-4\r\n(GMT -3:30) Newfoundland|-3.5\r\n(GMT -3:00) Brazil, Buenos Aires, Georgetown|-3\r\n(GMT -2:00) Mid-Atlantic|-2\r\n(GMT -1:00 hour) Azores, Cape Verde Islands|-1\r\n(GMT) Western Europe Time, London, Lisbon, Casablanca|0\r\n(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris|1\r\n(GMT +2:00) Kaliningrad, South Africa|2\r\n(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg|3\r\n(GMT +3:30) Tehran|3.5\r\n(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi|4\r\n(GMT +4:30) Kabu|4.5\r\n(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent|5\r\n(GMT +5:30) Bombay, Calcutta, Madras, New Delhi|5.5\r\n(GMT +6:00) Almaty, Dhaka, Colombo|6\r\n(GMT +7:00) Bangkok, Hanoi, Jakarta|7\r\n(GMT +8:00) Beijing, Perth, Singapore, Hong Kong|8\r\n(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk|9\r\n(GMT +9:30) Adelaide, Darwin|9.5\r\n(GMT +10:00) Eastern Australia, Guam, Vladivostok|10\r\n(GMT +11:00) Magadan, Solomon Islands, New Caledonia|11\r\n(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka|12'),
('topic', '', 'Topics', '', 2000, 0, '', 'forum', '');

-- --------------------------------------------------------

--
-- Table structure for table `lat_forum`
--

CREATE TABLE IF NOT EXISTS `lat_forum` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `o` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `link_clicks` int(10) unsigned NOT NULL DEFAULT '0',
  `parent` smallint(5) unsigned NOT NULL DEFAULT '0',
  `topics` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pcount_off` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `topic_or` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_topic` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_topic_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(125) NOT NULL DEFAULT '',
  `last_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_topic_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `lat_forum`
--

INSERT INTO `lat_forum` (`id`, `name`, `description`, `o`, `link`, `link_clicks`, `parent`, `topics`, `posts`, `pcount_off`, `topic_num`, `topic_or`, `topic_order`, `last_topic`, `last_topic_name`, `last_name`, `last_id`, `last_time`, `last_topic_time`) VALUES
(1, 'Test Category', '', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', 0, 0, 0),
(2, 'Test Forum', 'Welcome to Latova!', 0, '', 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lat_forum_mod`
--

CREATE TABLE IF NOT EXISTS `lat_forum_mod` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(225) NOT NULL DEFAULT '',
  `gid` varchar(150) NOT NULL DEFAULT '',
  `forums` text,
  `delete_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `delete_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `undelete_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `undelete_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `purge_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `purge_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hide_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hide_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `edit_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `edit_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lock_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `move_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sticky_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `announce_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `see_ip` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `see_delete_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `see_hidden_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `see_delete_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `see_hidden_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_forum_mod`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_forum_profile`
--

CREATE TABLE IF NOT EXISTS `lat_forum_profile` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `groups` text,
  `forums` text,
  `view_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `view_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `view_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_replies_own` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_replies_other` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_topics` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_polls` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vote_polls` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `quick_reply` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `attach_make` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `attach_download` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_lock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_move` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_edit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_edit_title` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `own_delete_posts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_bb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_smi` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lat_forum_profile`
--

INSERT INTO `lat_forum_profile` (`id`, `name`, `groups`, `forums`, `view_index`, `view_topics`, `view_posts`, `post_replies_own`, `post_replies_other`, `post_topics`, `post_polls`, `vote_polls`, `quick_reply`, `attach_make`, `attach_download`, `own_lock`, `own_delete`, `own_move`, `own_edit`, `own_edit_title`, `own_delete_posts`, `use_bb`, `use_smi`) VALUES
(1, 'Default', '1,2,3,4', '1,2', 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_autoparse`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_autoparse` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `site` varchar(50) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `lat_kernel_autoparse`
--

INSERT INTO `lat_kernel_autoparse` (`id`, `type`, `site`, `data`, `content`) VALUES
(1, 0, '', 'png', ''),
(2, 0, '', 'jpg', ''),
(3, 0, '', 'jpeg', ''),
(4, 0, '', 'gif', ''),
(5, 1, 'youtube.com', 'v', '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/<!-- VIDEO -->&rel=1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/<!-- VIDEO -->&rel=1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>'),
(6, 2, 'metacafe.com', '([0-9]+/[a-z_]+)', '<embed src="http://www.metacafe.com/fplayer/<!-- VIDEO -->.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>'),
(7, 2, '5min.com', '([0-9]+$)', '<object width=''400'' height=''325'' id=''FiveminPlayer''><param name=''allowfullscreen'' value=''true''/><param name=''allowScriptAccess'' value=''always''/><param name=''movie'' value=''http://www.5min.com/Embeded/<!-- VIDEO -->/''/><embed src=''http://www.5min.com/Embeded/<!-- VIDEO -->/'' type=''application/x-shockwave-flash'' width=''400'' height=''325'' allowfullscreen=''true'' allowScriptAccess=''always''></embed></object>');

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_bbtag`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_bbtag` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL DEFAULT '',
  `file` varchar(50) NOT NULL DEFAULT '',
  `hotkey` char(1) NOT NULL DEFAULT '',
  `opt` tinyint(1) NOT NULL DEFAULT '0',
  `no_embed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `no_quote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `placement` tinyint(3) unsigned zerofill NOT NULL DEFAULT '000',
  `replace_with` text,
  `display` text,
  `clean` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `example` varchar(255) NOT NULL DEFAULT '',
  `example_opt` varchar(255) NOT NULL DEFAULT '',
  `example_opt2` varchar(255) NOT NULL DEFAULT '',
  `inherit_img` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `inherit_mda` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `lat_kernel_bbtag`
--

INSERT INTO `lat_kernel_bbtag` (`id`, `tag`, `file`, `hotkey`, `opt`, `no_embed`, `no_quote`, `placement`, `replace_with`, `display`, `clean`, `example`, `example_opt`, `example_opt2`, `inherit_img`, `inherit_mda`) VALUES
(1, 'b', '', 'b', 0, 0, 0, 100, '<b><!-- data --></b>', '', 0, '', '', '', 0, 0),
(2, 'u', '', 'u', 0, 0, 0, 101, '<u><!-- data --></u>', '', 0, '', '', '', 0, 0),
(3, 'i', '', 'i', 0, 0, 0, 102, '<i><!-- data --></i>', '', 0, '', '', '', 0, 0),
(4, 'color', 'color', '', 1, 0, 0, 103, '<span <!-- optn -->><!-- data --></span>', 'Blue|blue|color:blue\r\nRed|red|color:red\r\nPurple|purple|color:purple\r\nGray|gray|color:gray\r\nGreen|green|color:green\r\nOrange|orange|color:orange', 0, '', '', 'red', 0, 0),
(5, 'font', 'letters_numbers_spaces', '', 1, 0, 0, 104, '<span style="font-family: <!-- optn -->;"><!-- data --></span>', 'Arial|arial|font-family: arial; font-size: 10px;\r\nComic Sans|Comic Sans MS|font-family: Comic Sans MS; font-size: 10px;\r\nCourier|Courier New|font-family: Courier New; font-size: 10px;\r\nLucida|Lucida Console|font-family: Lucida Console; font-size: 10px;\r\nTahoma|Tahoma|font-family: Tahoma; font-size: 10px;\r\nTimes Roman|Times New Roman|font-family: Times New Roman; font-size: 10px;', 0, '', '', 'Lucida Console', 0, 0),
(6, 'size', 'size', '', 1, 0, 0, 105, '<span style="font-size: <!-- optn -->px;"><!-- data --></span>', 'Small|8\r\nLarge|15\r\nLarger|19\r\nLargest|23', 0, '', '', '15', 0, 0),
(7, 'img', 'image', 'm', 0, 0, 0, 200, '<div style="display: none" id="div_<!-- NEW INT -->" class="big_img"><script type="text/javascript">img_type[<!-- INT -->]=<!-- type -->;</script><img id="img_<!-- INT -->" src="<!-- data -->" alt="img" /><div class="img_zoom" id="zoom_<!-- INT -->"><a href="<!-- data -->" target="_blank"><img src="<!-- IMAGE -->zoom.png" alt="+" />&nbsp;<lang:enlarge></a></div></div><noscript><img src="<!-- data -->" alt="img" /></noscript>', '', 1, 'http://www.latova.com/image.png', '', '', 1, 0),
(8, 'url', 'url', 'l', 2, 0, 0, 201, '', '', 0, 'http://www.latova.com', '', 'http://www.latova.com', 0, 0),
(9, 'quote', 'quote', 'q', 2, 1, 1, 203, '<div class="bdr_bb">\r\n	<div class="quote">\r\n		<!-- optn -->\r\n	</div>\r\n	<div class="quote_text">\r\n		<!-- data -->\r\n	</div>\r\n</div>', '', 0, '', '', '', 0, 0),
(10, 'code', 'code', 'c', 0, 1, 0, 204, '<div class="bdr_bb">\r\n	<div class="code">\r\n		<lang:code>\r\n	</div>\r\n	<div class="code_text">\r\n		<!-- data -->\r\n	</div>\r\n</div>', '', 1, '', '', '', 0, 0),
(11, 'codebox', 'codebox', '', 0, 1, 0, 205, '<div class="bdr_bb">\r\n	<div class="code">\r\n		<lang:codebox>\r\n	</div>\r\n	<textarea class="codebox_text" rows="10" cols="50"><!-- data --></textarea>\r\n</div>', '', 1, '', '', '', 0, 0),
(12, 's', '', '', 0, 0, 0, 000, '<del><!-- data --></del>', '', 0, '', '', '', 0, 0),
(13, 'sub', '', '', 0, 1, 0, 000, '<sub><!-- data --></sub>', '', 1, '', '', '', 0, 0),
(14, 'sup', '', '', 0, 1, 0, 000, '<sup><!-- data --></sup>', '', 1, '', '', '', 0, 0),
(15, 'list', 'list', '', 2, 0, 0, 000, '', '', 0, '<br />[*]item1<br />[*]item2<br />', '<br />[*]item1<br />[*]item2<br />', 'o', 0, 0),
(16, 'center', '', '', 0, 0, 0, 206, '<!-- center --><div style="text-align: center"><!-- data --></div><!-- end center -->', '', 0, '', '', '', 0, 0),
(17, 'right', '', '', 0, 0, 0, 000, '<!-- right --><div style="text-align: right"><!-- data --></div><!-- end right -->', '', 0, '', '', '', 0, 0),
(18, 'post', '', '', 0, 1, 0, 000, '<a href="index.php?post=<!-- data -->"><img src="<!-- IMAGE -->link.png" alt="" /></a>', '', 0, '1', '', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_bbtag_profile`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_bbtag_profile` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `groups` text,
  `bbtags` text,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `max_img` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_mda` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_smi` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_chr` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `lat_kernel_bbtag_profile`
--

INSERT INTO `lat_kernel_bbtag_profile` (`id`, `groups`, `bbtags`, `type`, `max_img`, `max_mda`, `max_smi`, `max_chr`) VALUES
(1, '1,2,3,4', '', 0, 3, 0, 3, 500),
(2, '1,2,3,4', '', 1, 0, 0, 3, 500),
(3, '1,2,3,4', '', 2, 10, 3, 10, 15000),
(4, '1,2,3,4', '', 3, 50, 10, 20, 100000);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_cache`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_cache` (
  `label` varchar(25) NOT NULL DEFAULT '',
  `autoload` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data` mediumtext,
  `cache` text,
  PRIMARY KEY (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_kernel_cache`
--

INSERT INTO `lat_kernel_cache` (`label`, `autoload`, `data`, `cache`) VALUES
('autoparse', 0, 'a:7:{i:1;a:5:{s:2:"id";s:1:"1";s:4:"type";s:1:"0";s:4:"site";s:0:"";s:4:"data";s:3:"png";s:7:"content";s:0:"";}i:2;a:5:{s:2:"id";s:1:"2";s:4:"type";s:1:"0";s:4:"site";s:0:"";s:4:"data";s:3:"jpg";s:7:"content";s:0:"";}i:3;a:5:{s:2:"id";s:1:"3";s:4:"type";s:1:"0";s:4:"site";s:0:"";s:4:"data";s:4:"jpeg";s:7:"content";s:0:"";}i:4;a:5:{s:2:"id";s:1:"4";s:4:"type";s:1:"0";s:4:"site";s:0:"";s:4:"data";s:3:"gif";s:7:"content";s:0:"";}i:5;a:5:{s:2:"id";s:1:"5";s:4:"type";s:1:"1";s:4:"site";s:11:"youtube.com";s:4:"data";s:1:"v";s:7:"content";s:321:"<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/<!-- VIDEO -->&rel=1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/<!-- VIDEO -->&rel=1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>";}i:6;a:5:{s:2:"id";s:1:"6";s:4:"type";s:1:"2";s:4:"site";s:12:"metacafe.com";s:4:"data";s:16:"([0-9]+/[a-z_]+)";s:7:"content";s:212:"<embed src="http://www.metacafe.com/fplayer/<!-- VIDEO -->.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>";}i:7;a:5:{s:2:"id";s:1:"7";s:4:"type";s:1:"2";s:4:"site";s:8:"5min.com";s:4:"data";s:9:"([0-9]+$)";s:7:"content";s:403:"<object width=''400'' height=''325'' id=''FiveminPlayer''><param name=''allowfullscreen'' value=''true''/><param name=''allowScriptAccess'' value=''always''/><param name=''movie'' value=''http://www.5min.com/Embeded/<!-- VIDEO -->/''/><embed src=''http://www.5min.com/Embeded/<!-- VIDEO -->/'' type=''application/x-shockwave-flash'' width=''400'' height=''325'' allowfullscreen=''true'' allowScriptAccess=''always''></embed></object>";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:16:"kernel_autoparse";}s:3:"key";s:2:"id";}'),
('bbtag', 0, 'a:18:{i:1;a:16:{s:2:"id";s:1:"1";s:3:"tag";s:1:"b";s:4:"file";s:0:"";s:6:"hotkey";s:1:"b";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"100";s:7:"replace";s:20:"<b><!-- data --></b>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:2;a:16:{s:2:"id";s:1:"2";s:3:"tag";s:1:"u";s:4:"file";s:0:"";s:6:"hotkey";s:1:"u";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"101";s:7:"replace";s:20:"<u><!-- data --></u>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:3;a:16:{s:2:"id";s:1:"3";s:3:"tag";s:1:"i";s:4:"file";s:0:"";s:6:"hotkey";s:1:"i";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"102";s:7:"replace";s:20:"<i><!-- data --></i>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:4;a:16:{s:2:"id";s:1:"4";s:3:"tag";s:5:"color";s:4:"file";s:5:"color";s:6:"hotkey";s:0:"";s:6:"option";s:1:"1";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"103";s:7:"replace";s:40:"<span <!-- optn -->><!-- data --></span>";s:7:"display";s:142:"Blue|blue|color:blue\r\nRed|red|color:red\r\nPurple|purple|color:purple\r\nGray|gray|color:gray\r\nGreen|green|color:green\r\nOrange|orange|color:orange";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:3:"red";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:5;a:16:{s:2:"id";s:1:"5";s:3:"tag";s:4:"font";s:4:"file";s:22:"letters_numbers_spaces";s:6:"hotkey";s:0:"";s:6:"option";s:1:"1";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"104";s:7:"replace";s:62:"<span style="font-family: <!-- optn -->;"><!-- data --></span>";s:7:"display";s:381:"Arial|arial|font-family: arial; font-size: 10px;\r\nComic Sans|Comic Sans MS|font-family: Comic Sans MS; font-size: 10px;\r\nCourier|Courier New|font-family: Courier New; font-size: 10px;\r\nLucida|Lucida Console|font-family: Lucida Console; font-size: 10px;\r\nTahoma|Tahoma|font-family: Tahoma; font-size: 10px;\r\nTimes Roman|Times New Roman|font-family: Times New Roman; font-size: 10px;";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:14:"Lucida Console";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:6;a:16:{s:2:"id";s:1:"6";s:3:"tag";s:4:"size";s:4:"file";s:4:"size";s:6:"hotkey";s:0:"";s:6:"option";s:1:"1";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"105";s:7:"replace";s:62:"<span style="font-size: <!-- optn -->px;"><!-- data --></span>";s:7:"display";s:40:"Small|8\r\nLarge|15\r\nLarger|19\r\nLargest|23";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:2:"15";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:7;a:16:{s:2:"id";s:1:"7";s:3:"tag";s:3:"img";s:4:"file";s:5:"image";s:6:"hotkey";s:1:"m";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"200";s:7:"replace";s:428:"<div style="display: none" id="div_<!-- NEW INT -->" class="big_img"><script type="text/javascript">img_type[<!-- INT -->]=<!-- type -->;</script><img id="img_<!-- INT -->" src="<!-- data -->" alt="img" /><div class="img_zoom" id="zoom_<!-- INT -->"><a href="<!-- data -->" target="_blank"><img src="<!-- IMAGE -->zoom.png" alt="+" />&nbsp;<lang:enlarge></a></div></div><noscript><img src="<!-- data -->" alt="img" /></noscript>";s:7:"display";s:0:"";s:5:"clean";s:1:"1";s:7:"example";s:31:"http://www.latova.com/image.png";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"1";s:11:"inherit_mda";s:1:"0";}i:8;a:16:{s:2:"id";s:1:"8";s:3:"tag";s:3:"url";s:4:"file";s:3:"url";s:6:"hotkey";s:1:"l";s:6:"option";s:1:"2";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"201";s:7:"replace";s:0:"";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:21:"http://www.latova.com";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:21:"http://www.latova.com";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:9;a:16:{s:2:"id";s:1:"9";s:3:"tag";s:5:"quote";s:4:"file";s:5:"quote";s:6:"hotkey";s:1:"q";s:6:"option";s:1:"2";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"1";s:9:"placement";s:3:"203";s:7:"replace";s:129:"<div class="bdr_bb">\r\n	<div class="quote">\r\n		<!-- optn -->\r\n	</div>\r\n	<div class="quote_text">\r\n		<!-- data -->\r\n	</div>\r\n</div>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:10;a:16:{s:2:"id";s:2:"10";s:3:"tag";s:4:"code";s:4:"file";s:4:"code";s:6:"hotkey";s:1:"c";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"204";s:7:"replace";s:125:"<div class="bdr_bb">\r\n	<div class="code">\r\n		<lang:code>\r\n	</div>\r\n	<div class="code_text">\r\n		<!-- data -->\r\n	</div>\r\n</div>";s:7:"display";s:0:"";s:5:"clean";s:1:"2";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:11;a:16:{s:2:"id";s:2:"11";s:3:"tag";s:7:"codebox";s:4:"file";s:7:"codebox";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"205";s:7:"replace";s:154:"<div class="bdr_bb">\r\n	<div class="code">\r\n		<lang:codebox>\r\n	</div>\r\n	<textarea class="codebox_text" rows="10" cols="50"><!-- data --></textarea>\r\n</div>";s:7:"display";s:0:"";s:5:"clean";s:1:"1";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:12;a:16:{s:2:"id";s:2:"12";s:3:"tag";s:1:"s";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:24:"<del><!-- data --></del>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:13;a:16:{s:2:"id";s:2:"13";s:3:"tag";s:3:"sub";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:24:"<sub><!-- data --></sub>";s:7:"display";s:0:"";s:5:"clean";s:1:"1";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:14;a:16:{s:2:"id";s:2:"14";s:3:"tag";s:3:"sup";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:24:"<sup><!-- data --></sup>";s:7:"display";s:0:"";s:5:"clean";s:1:"1";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:15;a:16:{s:2:"id";s:2:"15";s:3:"tag";s:4:"list";s:4:"file";s:4:"list";s:6:"hotkey";s:0:"";s:6:"option";s:1:"2";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:0:"";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:34:"<br />[*]item1<br />[*]item2<br />";s:11:"example_opt";s:34:"<br />[*]item1<br />[*]item2<br />";s:12:"example_opt2";s:1:"o";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:16;a:16:{s:2:"id";s:2:"16";s:3:"tag";s:6:"center";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:3:"206";s:7:"replace";s:85:"<!-- center --><div style="text-align: center"><!-- data --></div><!-- end center -->";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:17;a:16:{s:2:"id";s:2:"17";s:3:"tag";s:5:"right";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"0";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:82:"<!-- right --><div style="text-align: right"><!-- data --></div><!-- end right -->";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:0:"";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}i:18;a:16:{s:2:"id";s:2:"18";s:3:"tag";s:4:"post";s:4:"file";s:0:"";s:6:"hotkey";s:0:"";s:6:"option";s:1:"0";s:8:"no_imbed";s:1:"1";s:8:"no_quote";s:1:"0";s:9:"placement";s:1:"0";s:7:"replace";s:86:"<a href="index.php?post=<!-- data -->"><img src="<!-- IMAGE -->link.png" alt="" /></a>";s:7:"display";s:0:"";s:5:"clean";s:1:"0";s:7:"example";s:1:"1";s:11:"example_opt";s:0:"";s:12:"example_opt2";s:0:"";s:11:"inherit_img";s:1:"0";s:11:"inherit_mda";s:1:"0";}}', 'a:2:{s:5:"query";a:3:{s:6:"select";s:1:"*";s:4:"from";s:12:"kernel_bbtag";s:5:"order";s:2:"id";}s:3:"key";s:2:"id";}'),
('bbtag_profile', 0, 'a:4:{i:1;a:8:{s:2:"id";s:1:"1";s:6:"groups";s:7:"1,2,3,4";s:6:"bbtags";s:0:"";s:4:"type";s:1:"0";s:7:"max_img";s:1:"3";s:7:"max_mda";s:1:"0";s:7:"max_smi";s:1:"3";s:7:"max_chr";s:3:"500";}i:2;a:8:{s:2:"id";s:1:"2";s:6:"groups";s:7:"1,2,3,4";s:6:"bbtags";s:0:"";s:4:"type";s:1:"1";s:7:"max_img";s:1:"0";s:7:"max_mda";s:1:"0";s:7:"max_smi";s:1:"3";s:7:"max_chr";s:3:"500";}i:3;a:8:{s:2:"id";s:1:"3";s:6:"groups";s:7:"1,2,3,4";s:6:"bbtags";s:0:"";s:4:"type";s:1:"2";s:7:"max_img";s:2:"10";s:7:"max_mda";s:1:"3";s:7:"max_smi";s:2:"10";s:7:"max_chr";s:5:"15000";}i:4;a:8:{s:2:"id";s:1:"4";s:6:"groups";s:7:"1,2,3,4";s:6:"bbtags";s:0:"";s:4:"type";s:1:"3";s:7:"max_img";s:2:"50";s:7:"max_mda";s:2:"10";s:7:"max_smi";s:2:"20";s:7:"max_chr";s:6:"100000";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:20:"kernel_bbtag_profile";}s:3:"key";s:2:"id";}'),
('config', 1, 'a:66:{s:10:"avatar_ext";s:16:"jpg,jpeg,gif,png";s:13:"avatar_height";s:3:"125";s:11:"avatar_link";s:1:"0";s:11:"avatar_size";s:2:"25";s:13:"avatar_upload";s:1:"1";s:10:"avatar_url";s:16:"storage/avatars/";s:12:"avatar_width";s:3:"125";s:9:"bots_list";s:268:"googlebot|Google|http://www.google.com\r\nslurp@inktomi|HotBot|http://www.hotbot.com\r\nlycos|Lycos|http://www.lycos.com\r\nia_archiver|Archive|http://www.archive.org\r\nAsk Jeeves|Ask Jeeves|http://www.ask.com\r\nmsnbot|MSN|http://www.msn.com\r\nYahoo!|Yahoo|http://www.yahoo.com";s:7:"bots_on";s:1:"1";s:13:"cookie_domain";s:9:"127.0.0.1";s:11:"cookie_path";s:1:"/";s:14:"decimal_format";s:1:".";s:12:"default_lang";s:1:"1";s:12:"default_page";s:5:"forum";s:14:"default_search";s:5:"topic";s:12:"default_skin";s:1:"1";s:14:"email_activate";s:1:"0";s:11:"gallery_ext";s:16:"jpg,jpeg,gif,png";s:11:"gallery_url";s:16:"storage/gallery/";s:10:"gd_version";s:2:"-1";s:9:"hot_topic";s:2:"20";s:7:"img_0_h";s:3:"200";s:7:"img_0_w";s:3:"500";s:7:"img_1_h";s:1:"0";s:7:"img_1_w";s:1:"0";s:7:"img_2_h";s:3:"500";s:7:"img_2_w";s:3:"500";s:7:"img_3_h";s:3:"800";s:7:"img_3_w";s:3:"500";s:8:"logo_des";s:19:"Generic description";s:9:"long_date";s:15:"[F jS Y,] g:i a";s:10:"mail_email";s:20:"noreply@yoursite.com";s:9:"mail_host";s:12:"yoursite.com";s:9:"mail_pass";s:0:"";s:9:"mail_port";s:2:"25";s:9:"mail_smtp";s:1:"0";s:9:"mail_user";s:20:"noreply@yoursite.com";s:9:"max_polls";s:1:"5";s:13:"max_polls_opt";s:2:"10";s:16:"name_change_days";s:2:"30";s:15:"name_change_num";s:1:"3";s:13:"number_format";s:1:",";s:9:"num_posts";s:2:"10";s:10:"num_topics";s:2:"15";s:9:"one_email";s:1:"1";s:9:"photo_ext";s:16:"jpg,jpeg,gif,png";s:12:"photo_height";s:3:"200";s:10:"photo_link";s:1:"0";s:10:"photo_size";s:2:"35";s:12:"photo_upload";s:1:"1";s:9:"photo_url";s:15:"storage/photos/";s:11:"photo_width";s:3:"200";s:11:"picon_table";s:1:"6";s:11:"gallery_col";s:1:"5";s:17:"recaptcha_private";s:0:"";s:16:"recaptcha_public";s:0:"";s:11:"script_name";s:6:"Latova";s:10:"script_url";s:31:"http://127.0.0.1/latova/upload/";s:10:"search_num";s:1:"3";s:11:"search_time";s:2:"60";s:14:"session_length";s:2:"60";s:10:"short_date";s:13:"[M jS,] g:i a";s:10:"short_page";s:142:"topic|topic|\r\nforum|forum|view\r\npost|topic|find\r\nmember|member|profile\r\npm|msg|view\r\nsearch|global|view_search\r\nsearch_user|global|view_search";s:10:"sig_height";s:3:"200";s:13:"smilies_table";s:1:"4";s:8:"timezone";s:2:"-5";}', 'a:3:{s:5:"query";a:3:{s:6:"select";s:11:"name, value";s:4:"from";s:6:"config";s:5:"where";s:7:"type!=0";}s:3:"key";s:4:"name";s:5:"value";s:5:"value";}'),
('filter', 0, 'N;', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:13:"kernel_filter";}s:3:"key";s:2:"id";}'),
('forum', 0, 'a:2:{i:1;a:19:{s:2:"id";s:1:"1";s:4:"name";s:13:"Test Category";s:11:"description";s:0:"";s:1:"o";s:1:"0";s:4:"link";s:0:"";s:11:"link_clicks";s:1:"0";s:6:"parent";s:1:"0";s:6:"topics";s:1:"0";s:5:"posts";s:1:"0";s:10:"pcount_off";s:1:"0";s:9:"topic_num";s:1:"0";s:8:"topic_or";s:1:"0";s:11:"topic_order";s:1:"0";s:10:"last_topic";s:1:"0";s:15:"last_topic_name";s:0:"";s:9:"last_name";s:0:"";s:7:"last_id";s:1:"0";s:9:"last_time";s:1:"0";s:15:"last_topic_time";s:1:"0";}i:2;a:19:{s:2:"id";s:1:"2";s:4:"name";s:10:"Test Forum";s:11:"description";s:18:"Welcome to Latova!";s:1:"o";s:1:"0";s:4:"link";s:0:"";s:11:"link_clicks";s:1:"0";s:6:"parent";s:1:"1";s:6:"topics";s:1:"0";s:5:"posts";s:1:"0";s:10:"pcount_off";s:1:"0";s:9:"topic_num";s:1:"0";s:8:"topic_or";s:1:"0";s:11:"topic_order";s:1:"0";s:10:"last_topic";s:1:"0";s:15:"last_topic_name";s:0:"";s:9:"last_name";s:0:"";s:7:"last_id";s:1:"0";s:9:"last_time";s:1:"0";s:15:"last_topic_time";s:1:"0";}}', 'a:2:{s:5:"query";a:3:{s:6:"select";s:1:"*";s:4:"from";s:5:"forum";s:5:"order";s:1:"o";}s:3:"key";s:2:"id";}'),
('forum_mod', 0, 'N;', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:9:"forum_mod";}s:3:"key";s:2:"id";}'),
('forum_profile', 0, 'a:1:{i:1;a:23:{s:2:"id";s:1:"1";s:4:"name";s:7:"Default";s:6:"groups";s:7:"1,2,3,4";s:6:"forums";s:3:"1,2";s:10:"view_index";s:1:"1";s:11:"view_topics";s:1:"1";s:10:"view_posts";s:1:"1";s:16:"post_replies_own";s:1:"1";s:18:"post_replies_other";s:1:"1";s:11:"post_topics";s:1:"1";s:10:"post_polls";s:1:"0";s:10:"vote_polls";s:1:"0";s:11:"quick_reply";s:1:"1";s:11:"attach_make";s:1:"0";s:15:"attach_download";s:1:"0";s:8:"own_lock";s:1:"0";s:10:"own_delete";s:1:"0";s:8:"own_move";s:1:"0";s:8:"own_edit";s:1:"1";s:14:"own_edit_title";s:1:"0";s:16:"own_delete_posts";s:1:"0";s:6:"use_bb";s:1:"1";s:7:"use_smi";s:1:"1";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:13:"forum_profile";}s:3:"key";s:2:"id";}'),
('group', 1, 'a:4:{i:1;a:10:{s:2:"id";s:1:"1";s:4:"name";s:14:"Administrators";s:8:"supermod";s:1:"1";s:10:"superadmin";s:1:"1";s:9:"cp_access";s:1:"*";s:5:"maxpm";s:5:"65535";s:6:"pm_smi";s:1:"1";s:5:"pm_bb";s:1:"1";s:10:"flood_post";s:2:"10";s:8:"flood_pm";s:2:"10";}i:2;a:10:{s:2:"id";s:1:"2";s:4:"name";s:5:"Users";s:8:"supermod";s:1:"0";s:10:"superadmin";s:1:"0";s:9:"cp_access";s:0:"";s:5:"maxpm";s:4:"1000";s:6:"pm_smi";s:1:"1";s:5:"pm_bb";s:1:"1";s:10:"flood_post";s:2:"20";s:8:"flood_pm";s:2:"30";}i:3;a:10:{s:2:"id";s:1:"3";s:4:"name";s:6:"Guests";s:8:"supermod";s:1:"0";s:10:"superadmin";s:1:"0";s:9:"cp_access";s:0:"";s:5:"maxpm";s:1:"0";s:6:"pm_smi";s:1:"0";s:5:"pm_bb";s:1:"0";s:10:"flood_post";s:1:"0";s:8:"flood_pm";s:1:"0";}i:4;a:10:{s:2:"id";s:1:"4";s:4:"name";s:16:"Super Moderators";s:8:"supermod";s:1:"1";s:10:"superadmin";s:1:"0";s:9:"cp_access";s:0:"";s:5:"maxpm";s:5:"65535";s:6:"pm_smi";s:1:"1";s:5:"pm_bb";s:1:"1";s:10:"flood_post";s:2:"10";s:8:"flood_pm";s:2:"10";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:12:"kernel_group";}s:3:"key";s:2:"id";}'),
('icon', 0, 'a:28:{i:29;a:6:{s:2:"id";s:2:"29";s:3:"txt";s:6:":&#92;";s:5:"image";s:15:"indifferent.png";s:7:"is_post";s:1:"0";s:7:"is_icon";s:1:"0";s:1:"o";s:1:"0";}i:22;a:6:{s:2:"id";s:2:"22";s:3:"txt";s:2:";p";s:5:"image";s:14:"winktounge.png";s:7:"is_post";s:1:"0";s:7:"is_icon";s:1:"0";s:1:"o";s:1:"0";}i:21;a:6:{s:2:"id";s:2:"21";s:3:"txt";s:2:":p";s:5:"image";s:10:"tounge.png";s:7:"is_post";s:1:"0";s:7:"is_icon";s:1:"0";s:1:"o";s:1:"0";}i:1;a:6:{s:2:"id";s:1:"1";s:3:"txt";s:2:":)";s:5:"image";s:9:"happy.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"1";}i:2;a:6:{s:2:"id";s:1:"2";s:3:"txt";s:2:":(";s:5:"image";s:7:"sad.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"2";}i:3;a:6:{s:2:"id";s:1:"3";s:3:"txt";s:2:";)";s:5:"image";s:8:"wink.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"3";}i:4;a:6:{s:2:"id";s:1:"4";s:3:"txt";s:2:":P";s:5:"image";s:10:"tounge.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"4";}i:5;a:6:{s:2:"id";s:1:"5";s:3:"txt";s:2:"^^";s:5:"image";s:13:"innocence.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"5";}i:6;a:6:{s:2:"id";s:1:"6";s:3:"txt";s:2:":D";s:5:"image";s:9:"smile.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"6";}i:24;a:6:{s:2:"id";s:2:"24";s:3:"txt";s:9:"&lt;_&lt;";s:5:"image";s:9:"glare.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"7";}i:8;a:6:{s:2:"id";s:1:"8";s:3:"txt";s:5:":mad:";s:5:"image";s:9:"angry.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"8";}i:9;a:6:{s:2:"id";s:1:"9";s:3:"txt";s:8:":&#039;(";s:5:"image";s:7:"cry.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:1:"9";}i:10;a:6:{s:2:"id";s:2:"10";s:3:"txt";s:2:"8)";s:5:"image";s:8:"cool.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"10";}i:11;a:6:{s:2:"id";s:2:"11";s:3:"txt";s:3:"0.o";s:5:"image";s:7:"huh.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"11";}i:12;a:6:{s:2:"id";s:2:"12";s:3:"txt";s:8:"&gt;&lt;";s:5:"image";s:10:"guilty.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"12";}i:13;a:6:{s:2:"id";s:2:"13";s:3:"txt";s:6:":oops:";s:5:"image";s:8:"oops.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"13";}i:14;a:6:{s:2:"id";s:2:"14";s:3:"txt";s:2:":o";s:5:"image";s:8:"ohmy.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"14";}i:15;a:6:{s:2:"id";s:2:"15";s:3:"txt";s:6:":evil:";s:5:"image";s:12:"plotting.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"15";}i:16;a:6:{s:2:"id";s:2:"16";s:3:"txt";s:2:":|";s:5:"image";s:12:"straight.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"16";}i:17;a:6:{s:2:"id";s:2:"17";s:3:"txt";s:3:":!:";s:5:"image";s:11:"exclaim.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"17";}i:18;a:6:{s:2:"id";s:2:"18";s:3:"txt";s:3:":?:";s:5:"image";s:12:"question.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"18";}i:19;a:6:{s:2:"id";s:2:"19";s:3:"txt";s:5:"&lt;3";s:5:"image";s:9:"heart.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"1";s:1:"o";s:2:"19";}i:7;a:6:{s:2:"id";s:1:"7";s:3:"txt";s:2:";P";s:5:"image";s:14:"winktounge.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"20";}i:23;a:6:{s:2:"id";s:2:"23";s:3:"txt";s:6:":cute:";s:5:"image";s:8:"cute.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"21";}i:25;a:6:{s:2:"id";s:2:"25";s:3:"txt";s:4:":XD:";s:5:"image";s:9:"laugh.png";s:7:"is_post";s:1:"2";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"22";}i:26;a:6:{s:2:"id";s:2:"26";s:3:"txt";s:6:":idea:";s:5:"image";s:8:"idea.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"23";}i:28;a:6:{s:2:"id";s:2:"28";s:3:"txt";s:2:":/";s:5:"image";s:15:"indifferent.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:2:"25";}i:27;a:6:{s:2:"id";s:2:"27";s:3:"txt";s:6:":sick:";s:5:"image";s:8:"sick.png";s:7:"is_post";s:1:"1";s:7:"is_icon";s:1:"0";s:1:"o";s:3:"242";}}', 'a:2:{s:5:"query";a:3:{s:6:"select";s:1:"*";s:4:"from";s:10:"local_icon";s:5:"order";s:1:"o";}s:3:"key";s:2:"id";}'),
('local', 1, 'a:3:{i:1;a:5:{s:2:"id";s:1:"1";s:4:"name";s:11:"Latova Skin";s:7:"is_lang";s:1:"0";s:6:"is_img";s:1:"0";s:7:"is_skin";s:1:"2";}i:2;a:5:{s:2:"id";s:1:"2";s:4:"name";s:13:"Latova Images";s:7:"is_lang";s:1:"0";s:6:"is_img";s:1:"1";s:7:"is_skin";s:1:"0";}i:3;a:5:{s:2:"id";s:1:"3";s:4:"name";s:7:"English";s:7:"is_lang";s:1:"1";s:6:"is_img";s:1:"0";s:7:"is_skin";s:1:"0";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:5:"local";}s:3:"key";s:2:"id";}'),
('module', 0, 'a:2:{i:1;a:10:{s:2:"id";s:1:"1";s:4:"name";s:13:"Latova Kernel";s:6:"author";s:11:"Michael Lat";s:12:"website_name";s:6:"Latova";s:11:"url_website";s:21:"http://www.latova.com";s:11:"url_contact";s:0:"";s:12:"url_download";s:0:"";s:11:"url_version";s:33:"http://www.latova.com/version.php";s:7:"version";s:5:"0.3.0";s:6:"mod_id";s:1:"1";}i:2;a:10:{s:2:"id";s:1:"2";s:4:"name";s:12:"Latova Forum";s:6:"author";s:11:"Michael Lat";s:12:"website_name";s:6:"Latova";s:11:"url_website";s:21:"http://www.latova.com";s:11:"url_contact";s:0:"";s:12:"url_download";s:0:"";s:11:"url_version";s:33:"http://www.latova.com/version.php";s:7:"version";s:5:"0.3.0";s:6:"mod_id";s:1:"2";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:13:"kernel_module";}s:3:"key";s:2:"id";}'),
('page', 1, 'a:11:{s:5:"forum";a:7:{s:4:"name";s:5:"forum";s:4:"file";s:11:"forum/forum";s:4:"menu";s:13:"<lang:forums>";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"2";s:6:"system";s:1:"1";s:2:"cp";s:1:"0";}s:6:"global";a:7:{s:4:"name";s:6:"global";s:4:"file";s:14:"default/global";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"1";s:2:"cp";s:1:"0";}s:5:"login";a:7:{s:4:"name";s:5:"login";s:4:"file";s:13:"default/login";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:6:"member";a:7:{s:4:"name";s:6:"member";s:4:"file";s:14:"default/member";s:4:"menu";s:14:"<lang:members>";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:3:"msg";a:7:{s:4:"name";s:3:"msg";s:4:"file";s:11:"default/msg";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:4:"post";a:7:{s:4:"name";s:4:"post";s:4:"file";s:10:"forum/post";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:6:"search";a:7:{s:4:"name";s:6:"search";s:4:"file";s:0:"";s:4:"menu";s:13:"<lang:search>";s:8:"menu_url";s:19:"pg=global;do=search";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:5:"topic";a:7:{s:4:"name";s:5:"topic";s:4:"file";s:11:"forum/topic";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:3:"ucp";a:7:{s:4:"name";s:3:"ucp";s:4:"file";s:11:"default/ucp";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"0";}s:2:"cp";a:7:{s:4:"name";s:2:"cp";s:4:"file";s:5:"cp/cp";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"1";}s:8:"cp_forum";a:7:{s:4:"name";s:8:"cp_forum";s:4:"file";s:14:"forum/cp_forum";s:4:"menu";s:0:"";s:8:"menu_url";s:0:"";s:10:"can_search";s:1:"0";s:6:"system";s:1:"0";s:2:"cp";s:1:"1";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:11:"kernel_page";}s:3:"key";s:4:"name";}'),
('page_cp', 0, 'a:9:{i:6;a:6:{s:2:"id";s:1:"6";s:4:"name";s:5:"cache";s:5:"title";s:12:"reload cache";s:4:"link";s:14:"pg=cp;do=cache";s:7:"section";s:8:"advanced";s:6:"config";s:1:"0";}i:7;a:6:{s:2:"id";s:1:"7";s:4:"name";s:7:"reparse";s:5:"title";s:15:"reparse content";s:4:"link";s:16:"pg=cp;do=reparse";s:7:"section";s:8:"advanced";s:6:"config";s:1:"0";}i:3;a:6:{s:2:"id";s:1:"3";s:4:"name";s:5:"forum";s:5:"title";s:14:"forum settings";s:4:"link";s:27:"pg=cp;do=settings;act=forum";s:7:"section";s:5:"forum";s:6:"config";s:1:"1";}i:8;a:6:{s:2:"id";s:1:"8";s:4:"name";s:12:"manage_forum";s:5:"title";s:13:"Manage forums";s:4:"link";s:11:"pg=cp_forum";s:7:"section";s:5:"forum";s:6:"config";s:1:"0";}i:9;a:6:{s:2:"id";s:1:"9";s:4:"name";s:14:"manage_profile";s:5:"title";s:15:"Manage profiles";s:4:"link";s:22:"pg=cp_forum;do=profile";s:7:"section";s:5:"forum";s:6:"config";s:1:"0";}i:4;a:6:{s:2:"id";s:1:"4";s:4:"name";s:6:"member";s:5:"title";s:15:"member settings";s:4:"link";s:28:"pg=cp;do=settings;act=member";s:7:"section";s:7:"members";s:6:"config";s:1:"1";}i:5;a:6:{s:2:"id";s:1:"5";s:4:"name";s:4:"comm";s:5:"title";s:14:"communications";s:4:"link";s:26:"pg=cp;do=settings;act=comm";s:7:"section";s:8:"settings";s:6:"config";s:1:"1";}i:1;a:6:{s:2:"id";s:1:"1";s:4:"name";s:7:"general";s:5:"title";s:7:"general";s:4:"link";s:29:"pg=cp;do=settings;act=general";s:7:"section";s:8:"settings";s:6:"config";s:1:"1";}i:2;a:6:{s:2:"id";s:1:"2";s:4:"name";s:6:"system";s:5:"title";s:6:"system";s:4:"link";s:28:"pg=cp;do=settings;act=system";s:7:"section";s:8:"settings";s:6:"config";s:1:"1";}}', 'a:2:{s:5:"query";a:3:{s:6:"select";s:1:"*";s:4:"from";s:14:"kernel_page_cp";s:5:"order";s:14:"section, title";}s:3:"key";s:2:"id";}'),
('setting', 0, 'a:21:{s:10:"user_title";a:16:{s:2:"id";s:1:"1";s:4:"name";s:10:"user_title";s:5:"title";s:17:"<lang:user_title>";s:7:"section";s:6:"global";s:5:"about";s:23:"<lang:about_user_title>";s:7:"content";s:0:"";s:9:"charlimit";s:2:"51";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"1";}s:8:"timezone";a:16:{s:2:"id";s:1:"2";s:4:"name";s:8:"timezone";s:5:"title";s:15:"<lang:timezone>";s:7:"section";s:6:"global";s:5:"about";s:21:"<lang:about_timezone>";s:7:"content";s:589:"-12|<lang:time_-12>\r\n-11|<lang:time_-11>\r\n-10|<lang:time_-10>\r\n-9|<lang:time_-09>\r\n-8|<lang:time_-08>\r\n-7|<lang:time_-07>\r\n-6|<lang:time_-06>\r\n-5|<lang:time_-05>\r\n-4|<lang:time_-04>\r\n-3.5|<lang:time_-03.5>\r\n-3|<lang:time_-03>\r\n-2|<lang:time_-02>\r\n-1|<lang:time_-01>\r\n0|<lang:time_0>\r\n1|<lang:time_01>\r\n2|<lang:time_02>\r\n3|<lang:time_03>\r\n3.5|<lang:time_03.5>\r\n4|<lang:time_04>\r\n4.5|<lang:time_04.5>\r\n5|<lang:time_05>\r\n5.5|<lang:time_05.5>\r\n6|<lang:time_06>\r\n7|<lang:time_07>\r\n8|<lang:time_08>\r\n9|<lang:time_09>\r\n9.5|<lang:time_09.5>\r\n10|<lang:time_10>\r\n11|<lang:time_11>\r\n12|<lang:time_12>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:1:"2";}s:3:"dst";a:16:{s:2:"id";s:1:"3";s:4:"name";s:3:"dst";s:5:"title";s:10:"<lang:dst>";s:7:"section";s:6:"global";s:5:"about";s:16:"<lang:about_dst>";s:7:"content";s:41:"0|<lang:enable_dst>\r\n1|<lang:disable_dst>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:1:"3";}s:9:"long_date";a:16:{s:2:"id";s:1:"4";s:4:"name";s:9:"long_date";s:5:"title";s:16:"<lang:long_date>";s:7:"section";s:6:"global";s:5:"about";s:22:"<lang:about_long_date>";s:7:"content";s:0:"";s:9:"charlimit";s:2:"51";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"4";}s:10:"short_date";a:16:{s:2:"id";s:1:"5";s:4:"name";s:10:"short_date";s:5:"title";s:17:"<lang:short_date>";s:7:"section";s:6:"global";s:5:"about";s:23:"<lang:about_short_date>";s:7:"content";s:0:"";s:9:"charlimit";s:2:"51";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"5";}s:11:"profile_aim";a:16:{s:2:"id";s:1:"6";s:4:"name";s:11:"profile_aim";s:5:"title";s:10:"<lang:aim>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:14:"{[^0-9A-Za-z]}";s:9:"charlimit";s:2:"16";s:4:"type";s:1:"2";s:5:"check";s:1:"5";s:2:"im";s:1:"1";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"6";}s:13:"profile_gtalk";a:16:{s:2:"id";s:1:"7";s:4:"name";s:13:"profile_gtalk";s:5:"title";s:12:"<lang:gtalk>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"255";s:4:"type";s:1:"2";s:5:"check";s:1:"2";s:2:"im";s:1:"2";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"7";}s:11:"profile_icq";a:16:{s:2:"id";s:1:"8";s:4:"name";s:11:"profile_icq";s:5:"title";s:10:"<lang:icq>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:2:"10";s:4:"type";s:1:"2";s:5:"check";s:1:"4";s:2:"im";s:1:"3";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"8";}s:11:"profile_msn";a:16:{s:2:"id";s:1:"9";s:4:"name";s:11:"profile_msn";s:5:"title";s:10:"<lang:msn>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"255";s:4:"type";s:1:"2";s:5:"check";s:1:"2";s:2:"im";s:1:"2";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:1:"9";}s:11:"profile_yim";a:16:{s:2:"id";s:2:"10";s:4:"name";s:11:"profile_yim";s:5:"title";s:10:"<lang:yim>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:21:"{[^0-9A-Za-z&#92;._]}";s:9:"charlimit";s:2:"22";s:4:"type";s:1:"2";s:5:"check";s:1:"5";s:2:"im";s:1:"1";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"10";}s:15:"profile_website";a:16:{s:2:"id";s:2:"11";s:4:"name";s:15:"profile_website";s:5:"title";s:14:"<lang:website>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"255";s:4:"type";s:1:"2";s:5:"check";s:1:"3";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"1";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"11";}s:13:"profile_skype";a:16:{s:2:"id";s:2:"20";s:4:"name";s:13:"profile_skype";s:5:"title";s:12:"<lang:skype>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:2:"25";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"1";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"12";}s:16:"profile_location";a:16:{s:2:"id";s:2:"12";s:4:"name";s:16:"profile_location";s:5:"title";s:15:"<lang:location>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"150";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"1";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"13";}s:11:"profile_job";a:16:{s:2:"id";s:2:"15";s:4:"name";s:11:"profile_job";s:5:"title";s:10:"<lang:job>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"255";s:4:"type";s:1:"2";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"1";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"14";}s:17:"profile_interests";a:16:{s:2:"id";s:2:"13";s:4:"name";s:17:"profile_interests";s:5:"title";s:16:"<lang:interests>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:0:"";s:9:"charlimit";s:3:"500";s:4:"type";s:1:"3";s:5:"check";s:1:"1";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"1";s:7:"newline";s:1:"1";s:8:"required";s:1:"0";s:1:"o";s:2:"15";}s:14:"profile_gender";a:16:{s:2:"id";s:2:"14";s:4:"name";s:14:"profile_gender";s:5:"title";s:13:"<lang:gender>";s:7:"section";s:7:"profile";s:5:"about";s:0:"";s:7:"content";s:30:"1|<lang:male>\r\n2|<lang:female>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"0";s:6:"in_pro";s:1:"1";s:7:"newline";s:1:"0";s:8:"required";s:1:"0";s:1:"o";s:2:"16";}s:9:"num_posts";a:16:{s:2:"id";s:2:"16";s:4:"name";s:9:"num_posts";s:5:"title";s:16:"<lang:num_posts>";s:7:"section";s:5:"forum";s:5:"about";s:22:"<lang:about_num_posts>";s:7:"content";s:56:"0|<lang:default>\r\n5|5\r\n10|10\r\n15|15\r\n20|20\r\n25|25\r\n30|30";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:2:"17";}s:10:"num_topics";a:16:{s:2:"id";s:2:"17";s:4:"name";s:10:"num_topics";s:5:"title";s:17:"<lang:num_topics>";s:7:"section";s:5:"forum";s:5:"about";s:23:"<lang:about_num_topics>";s:7:"content";s:84:"0|<lang:default>\r\n5|5\r\n10|10\r\n15|15\r\n20|20\r\n25|25\r\n30|30\r\n35|35\r\n40|40\r\n45|45\r\n50|50";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:2:"18";}s:8:"hide_sig";a:16:{s:2:"id";s:2:"18";s:4:"name";s:8:"hide_sig";s:5:"title";s:17:"<lang:signatures>";s:7:"section";s:6:"global";s:5:"about";s:23:"<lang:about_signatures>";s:7:"content";s:28:"0|<lang:show>\r\n1|<lang:hide>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:2:"19";}s:8:"hide_ava";a:16:{s:2:"id";s:2:"19";s:4:"name";s:8:"hide_ava";s:5:"title";s:14:"<lang:avatars>";s:7:"section";s:6:"global";s:5:"about";s:20:"<lang:about_avatars>";s:7:"content";s:28:"0|<lang:show>\r\n1|<lang:hide>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:2:"20";}s:16:"dont_resize_imgs";a:16:{s:2:"id";s:2:"21";s:4:"name";s:16:"dont_resize_imgs";s:5:"title";s:18:"<lang:resize_imgs>";s:7:"section";s:6:"global";s:5:"about";s:24:"<lang:about_resize_imgs>";s:7:"content";s:25:"0|<lang:yes>\r\n1|<lang:no>";s:9:"charlimit";s:1:"0";s:4:"type";s:1:"1";s:5:"check";s:1:"0";s:2:"im";s:1:"0";s:6:"in_reg";s:1:"0";s:6:"in_use";s:1:"1";s:6:"in_pro";s:1:"0";s:7:"newline";s:1:"0";s:8:"required";s:1:"1";s:1:"o";s:2:"21";}}', 'a:2:{s:5:"query";a:3:{s:6:"select";s:1:"*";s:4:"from";s:7:"setting";s:5:"order";s:1:"o";}s:3:"key";s:4:"name";}'),
('setting_page', 0, 'a:2:{s:6:"global";a:3:{s:4:"name";s:6:"global";s:5:"title";s:21:"<lang:setting_global>";s:11:"description";s:27:"<lang:about_setting_global>";}s:5:"forum";a:3:{s:4:"name";s:5:"forum";s:5:"title";s:20:"<lang:setting_forum>";s:11:"description";s:26:"<lang:about_setting_forum>";}}', 'a:2:{s:5:"query";a:2:{s:6:"select";s:1:"*";s:4:"from";s:12:"setting_page";}s:3:"key";s:4:"name";}'),
('storage', 1, 'a:10:{s:15:"stats_max_users";s:1:"2";s:14:"stats_max_time";s:10:"1213189297";s:17:"stats_last_userid";s:1:"4";s:11:"stats_users";s:1:"4";s:21:"profile_content_links";s:12:"posts,topics";s:21:"profile_content_stats";s:12:"posts,topics";s:14:"user_list_rows";s:86:"a:4:{s:8:"username";i:40;s:3:"gid";i:25;s:16:"profile_location";i:30;s:5:"posts";i:5;}";s:15:"user_list_order";s:5:"posts";s:10:"user_fetch";s:11:"id,name,gid";s:13:"version_cache";s:133:"a:1:{s:32:"60c518819f580d937f6888348bd739c1";a:2:{s:3:"mod";a:3:{i:1;s:5:"0.3.0";i:2;s:5:"0.3.0";s:0:"";N;}s:4:"time";i:1222369637;}}";}', 'a:3:{s:5:"query";a:2:{s:6:"select";s:11:"label, data";s:4:"from";s:14:"kernel_storage";}s:3:"key";s:5:"label";s:5:"value";s:4:"data";}');

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_filter`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_filter` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `word` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `replace_with` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_kernel_filter`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_group`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `supermod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `superadmin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cp_access` text,
  `maxpm` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pm_smi` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pm_bb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `flood_post` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `flood_pm` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `lat_kernel_group`
--

INSERT INTO `lat_kernel_group` (`id`, `name`, `supermod`, `superadmin`, `cp_access`, `maxpm`, `pm_smi`, `pm_bb`, `flood_post`, `flood_pm`) VALUES
(1, 'Administrators', 1, 1, '*', 65535, 1, 1, 10, 10),
(2, 'Users', 0, 0, '', 1000, 1, 1, 20, 30),
(3, 'Guests', 0, 0, '', 0, 0, 0, 0, 0),
(4, 'Super Moderators', 1, 0, '', 65535, 1, 1, 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_module`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_module` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `author` varchar(50) NOT NULL DEFAULT '',
  `website_name` varchar(50) NOT NULL DEFAULT '',
  `url_website` varchar(100) NOT NULL DEFAULT '',
  `url_contact` varchar(100) NOT NULL DEFAULT '',
  `url_download` varchar(100) NOT NULL DEFAULT '',
  `url_version` varchar(100) NOT NULL DEFAULT '',
  `version` varchar(8) NOT NULL DEFAULT '',
  `mod_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `lat_kernel_module`
--

INSERT INTO `lat_kernel_module` (`id`, `name`, `author`, `website_name`, `url_website`, `url_contact`, `url_download`, `url_version`, `version`, `mod_id`) VALUES
(1, 'Latova Kernel', 'Michael Lat', 'Latova', 'http://www.latova.com', '', '', 'http://www.latova.com/version.php', '0.3.1', 1),
(2, 'Latova Forum', 'Michael Lat', 'Latova', 'http://www.latova.com', '', '', 'http://www.latova.com/version.php', '0.3.1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_msg`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_msg` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `sent_to` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sent_from` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `folder` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL DEFAULT '',
  `from_ip` varchar(16) NOT NULL DEFAULT '',
  `data` text,
  `data_cached` text,
  `data_reparse` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sent_date` int(10) unsigned NOT NULL DEFAULT '0',
  `smi` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `track` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `unread` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_kernel_msg`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_page`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_page` (
  `name` varchar(16) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `menu` varchar(50) NOT NULL DEFAULT '',
  `menu_url` varchar(50) NOT NULL DEFAULT '',
  `can_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cp` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_kernel_page`
--

INSERT INTO `lat_kernel_page` (`name`, `file`, `menu`, `menu_url`, `can_search`, `system`, `cp`) VALUES
('forum', 'forum/forum', '<lang:forums>', '', 2, 1, 0),
('global', 'default/global', '', '', 0, 1, 0),
('login', 'default/login', '', '', 0, 0, 0),
('member', 'default/member', '<lang:members>', '', 0, 0, 0),
('msg', 'default/msg', '', '', 0, 0, 0),
('post', 'forum/post', '', '', 0, 0, 0),
('search', '', '<lang:search>', 'pg=global;do=search', 0, 0, 0),
('topic', 'forum/topic', '', '', 0, 0, 0),
('ucp', 'default/ucp', '', '', 0, 0, 0),
('cp', 'cp/cp', '', '', 0, 0, 1),
('cp_forum', 'forum/cp_forum', '', '', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_page_cp`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_page_cp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(50) NOT NULL DEFAULT '',
  `section` varchar(50) NOT NULL DEFAULT '',
  `config` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `lat_kernel_page_cp`
--

INSERT INTO `lat_kernel_page_cp` (`id`, `name`, `title`, `link`, `section`, `config`) VALUES
(1, 'general', 'general', 'pg=cp;do=settings;act=general', '.settings', 1),
(2, 'system', 'system', 'pg=cp;do=settings;act=system', '.settings', 1),
(3, 'forum', 'forum settings', 'pg=cp;do=settings;act=forum', 'forum', 1),
(4, 'member', 'member settings', 'pg=cp;do=settings;act=member', 'members', 1),
(5, 'comm', 'communication', 'pg=cp;do=settings;act=comm', '.settings', 1),
(6, 'cache', 'reload cache', 'pg=cp;do=cache', 'advanced', 0),
(7, 'reparse', 'reparse content', 'pg=cp;do=reparse', 'advanced', 0),
(8, 'manage_forum', 'Manage forums', 'pg=cp_forum', 'forum', 0),
(9, 'manage_profile', 'Manage forum profiles', 'pg=cp_forum;do=profile', 'forum', 0),
(10, 'filter', 'Word Filters', 'pg=cp_global;do=filter', '.general', 0),
(11, 'bbtag', 'BBtags', 'pg=cp_global;do=bbtag', '.general', 0),
(12, 'autoparse', 'Autoparsers', 'pg=cp_global;do=autoparse', '.general', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_search`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_search` (
  `shash` varchar(72) NOT NULL,
  `content` text,
  `gid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `pg` varchar(16) NOT NULL DEFAULT '',
  KEY `shash` (`shash`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_kernel_search`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_session`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_session` (
  `sid` varchar(32) NOT NULL DEFAULT '',
  `escalated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `key` varchar(10) NOT NULL DEFAULT '',
  `spider` varchar(255) NOT NULL DEFAULT '',
  `uagent` varchar(255) NOT NULL DEFAULT '',
  `act` varchar(255) NOT NULL DEFAULT '',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_pg` varchar(50) DEFAULT NULL,
  `last_do` varchar(50) DEFAULT NULL,
  `last_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_cn` varchar(255) NOT NULL DEFAULT '',
  `captcha` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_kernel_session`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_storage`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_storage` (
  `label` varchar(25) NOT NULL DEFAULT '',
  `data` text,
  PRIMARY KEY (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_kernel_storage`
--

INSERT INTO `lat_kernel_storage` (`label`, `data`) VALUES
('stats_max_users', '0'),
('stats_max_time', '0'),
('stats_last_userid', '0'),
('stats_users', '0'),
('profile_content_links', 'posts,topics'),
('profile_content_stats', 'posts,topics'),
('user_list_rows', 'a:4:{s:8:"username";i:40;s:3:"gid";i:25;s:16:"profile_location";i:30;s:5:"posts";i:5;}'),
('user_list_order', 'posts'),
('user_fetch', 'id,name,gid'),
('version_cache', 'a:1:{s:32:"60c518819f580d937f6888348bd739c1";a:2:{s:3:"mod";a:3:{i:1;s:5:"0.3.0";i:2;s:5:"0.3.0";s:0:"";N;}s:4:"time";i:1222369637;}}');

-- --------------------------------------------------------

--
-- Table structure for table `lat_kernel_text`
--

CREATE TABLE IF NOT EXISTS `lat_kernel_text` (
  `id` varchar(32) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lat_kernel_text`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_local`
--

CREATE TABLE IF NOT EXISTS `lat_local` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `is_lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_img` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_skin` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `lat_local`
--

INSERT INTO `lat_local` (`id`, `name`, `is_lang`, `is_img`, `is_skin`) VALUES
(1, 'Latova Skin', 0, 0, 2),
(2, 'Latova Images', 0, 1, 0),
(3, 'English', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lat_local_icon`
--

CREATE TABLE IF NOT EXISTS `lat_local_icon` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `txt` varchar(250) NOT NULL DEFAULT '',
  `image` varchar(250) NOT NULL DEFAULT '',
  `is_post` tinyint(1) NOT NULL DEFAULT '0',
  `is_icon` tinyint(1) NOT NULL DEFAULT '0',
  `o` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `lat_local_icon`
--

INSERT INTO `lat_local_icon` (`id`, `txt`, `image`, `is_post`, `is_icon`, `o`) VALUES
(1, ':)', 'happy.png', 2, 1, 1),
(2, ':(', 'sad.png', 2, 1, 2),
(3, ';)', 'wink.png', 2, 1, 3),
(4, ':P', 'tounge.png', 2, 1, 4),
(5, '^^', 'innocence.png', 2, 1, 5),
(6, ':D', 'smile.png', 2, 1, 6),
(7, ';P', 'winktounge.png', 1, 0, 20),
(8, ':mad:', 'angry.png', 2, 1, 8),
(9, ':&#039;(', 'cry.png', 2, 1, 9),
(10, '8)', 'cool.png', 2, 1, 10),
(11, '0.o', 'huh.png', 2, 1, 11),
(12, '&gt;&lt;', 'guilty.png', 2, 1, 12),
(13, ':oops:', 'oops.png', 2, 1, 13),
(14, ':o', 'ohmy.png', 2, 1, 14),
(15, ':evil:', 'plotting.png', 2, 1, 15),
(16, ':|', 'straight.png', 1, 0, 16),
(17, ':!:', 'exclaim.png', 1, 1, 17),
(18, ':?:', 'question.png', 1, 1, 18),
(19, '&lt;3', 'heart.png', 1, 1, 19),
(21, ':p', 'tounge.png', 0, 0, 0),
(22, ';p', 'winktounge.png', 0, 0, 0),
(23, ':cute:', 'cute.png', 1, 0, 21),
(24, '&lt;_&lt;', 'glare.png', 2, 1, 7),
(25, ':XD:', 'laugh.png', 2, 0, 22),
(26, ':idea:', 'idea.png', 1, 0, 23),
(27, ':sick:', 'sick.png', 1, 0, 242),
(28, ':/', 'indifferent.png', 1, 0, 25),
(29, ':&#92;', 'indifferent.png', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lat_local_lang`
--

CREATE TABLE IF NOT EXISTS `lat_local_lang` (
  `label` varchar(32) NOT NULL DEFAULT '',
  `pg` varchar(16) NOT NULL DEFAULT '',
  `lid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `word` text,
  UNIQUE KEY `unilang` (`label`,`pg`,`lid`),
  KEY `pg` (`pg`),
  KEY `lid` (`lid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_local_lang`
--

INSERT INTO `lat_local_lang` (`label`, `pg`, `lid`, `word`) VALUES
('action', '', 1, 'Action'),
('act_email_validate', '', 1, 'Your email address has been updated, however your account has been sent back to validating status. The administrator of this website has specified verification that the email address belongs to you. Check your email and validate your account.'),
('act_logged_in', '', 1, 'You have successfully logged in, welcome!'),
('act_logged_out', '', 1, 'You have successfully logged out, bye!'),
('act_registered', '', 1, 'Your new account has been successfully created and you have been automatically logged in.'),
('aim', '', 1, 'AOL Instant Messenger'),
('b', '', 1, ' byte'),
('smi_limit', '', 1, '<br />Simile Limit: <!-- NUM -->'),
('bs', '', 1, ' bytes'),
('bshort', '', 1, 'b'),
('captcha', '', 1, 'Verification:'),
('charset', '', 1, 'ISO-8859-1'),
('clicksmilies', '', 1, 'Clickable Smilies'),
('code', '', 1, 'Code'),
('codebox', '', 1, 'Codebox'),
('cp', '', 1, 'Control Panel'),
('date', '', 1, 'Date'),
('delete', '', 1, 'Delete'),
('empty', '', 1, 'Empty'),
('enter_msg', '', 1, 'Enter Message'),
('errban', '', 1, 'Access Denied. You have attempted to access an area which an administrator or moderator has banned you from.'),
('errforms1', '', 1, 'Some errors occurred in the processing of your form submission. It has resulted in rejection of your form submission. The errors returned were:'),
('error', '', 1, 'Error'),
('errsuperban', '', 1, 'You are banned.'),
('err_captcha', '', 1, 'The verification code you entered did not match.'),
('err_critical1', '', 1, 'A critical error occurred in the processing of your page request. It has resulted in a halt of this script. The error returned was:'),
('err_critical2', '', 1, 'You may <a href="javascript:history.go(-1)">click here</a> to visit the last page you were just viewing.'),
('err_critical_title', '', 1, 'Critical Error'),
('err_form', '', 1, 'There were problems in the processing of your form submission. Please fix all the listed problems and submit again.'),
('err_form_title', '', 1, 'Form Submission Error'),
('err_input', '', 1, 'Generic input error. You''re sending data to the software that it should never normally receive. If you got this error from accessing a normal form, contact an administrator.'),
('err_key', '', 1, 'Authorization key does not match. This is usually caused by a long period of inactivity. Press back in your browser, refresh the page, and try again.'),
('err_key_form', '', 1, 'Authorization key does not match. This is usually caused by a long period of inactivity. Check over your form and hit submit.'),
('err_logged_in', '', 1, 'You are logged in. This area is only for guests.'),
('err_logged_out', '', 1, 'This area is for people who are logged into their accounts. Please login.'),
('err_moderator_privileges', '', 1, 'You don''t have the correct privileges to preform this moderation action in this forum.'),
('err_no_forum', 'forum', 1, 'This forum doesn''t exist!'),
('err_no_page', '', 1, 'The page you attempted to access does not exist.'),
('female', '', 1, 'Female'),
('forums', '', 1, 'Forums'),
('forum_list', '', 1, 'Forum List'),
('gbshort', '', 1, 'gb'),
('gender', '', 1, 'Gender'),
('gig', '', 1, ' gigabyte'),
('gigs', '', 1, ' gigabytes'),
('go', '', 1, 'Go'),
('gototop', '', 1, 'Go to top'),
('group', '', 1, 'Group: '),
('groups', '', 1, 'Groups'),
('gstats', '', 1, 'Global Statistics'),
('gtalk', '', 1, 'Google Talk'),
('guest', '', 1, 'Guest'),
('guestnamesettings', '', 1, 'Guest Name Settings'),
('hello', '', 1, 'Hello, '),
('help_captcha', '', 1, 'Type in the letters and/or numbers you see in the image to the right into the box below it.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 5 characters.&lt;br /&gt;Case Insensitive.&lt;/i&gt;'),
('help_email', '', 1, 'A valid email address so administrators and members can email you if you allow them at the bottom. It also may be required to activate your account.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Must be a valid email address.&lt;/i&gt;'),
('change_name', 'ucp', 1, 'Change Username'),
('about_change_name', 'ucp', 1, 'Change the name that represents you on the website.'),
('new_name', 'ucp', 1, 'New Username:'),
('help_new_name', 'ucp', 1, 'The new username to represent you on this website. You will login with this name for now on. Make sure you double check it for accuracy before you submit!&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-25 characters.&lt;/i&gt;'),
('hide', '', 1, 'Hide'),
('html_off', '', 1, 'HTML is <b>DISABLED</b>.'),
('html_on', '', 1, 'HTML is <b>ENABLED</b>.'),
('hundred', '', 1, '100%'),
('icq', '', 1, 'ICQ'),
('inbox_header', '', 1, 'Inbox (<!-- NUM -->)'),
('insufficient_permissions', '', 1, 'Insufficient permissions'),
('interests', '', 1, 'Interests &amp; Hobbies'),
('invert_select', '', 1, 'Invert selection'),
('job', '', 1, 'Occupation'),
('joined', '', 1, 'Joined:'),
('kb', '', 1, ' kilobyte'),
('kbs', '', 1, ' kilobytes'),
('kbshort', '', 1, 'kb'),
('last_act', '', 1, 'Last Activity'),
('location', '', 1, 'Location'),
('lock', '', 1, 'Lock'),
('locked', '', 1, 'Locked'),
('login', '', 1, 'Login'),
('logout', '', 1, 'Logout'),
('male', '', 1, 'Male'),
('mb', '', 1, ' megabyte'),
('mbs', '', 1, ' megabytes'),
('mbshort', '', 1, 'mb'),
('members', '', 1, 'Members'),
('members_off', '', 1, 'There are no registered members currently online.'),
('members_online', '', 1, 'Members online'),
('member_stats', '', 1, 'Total registered members: <b><!-- NUM --></b>. Latest member: <b><!-- USER --></b>.'),
('minute_ago', '', 1, ' minutes ago'),
('moderator_options', '', 1, 'Moderator Options'),
('most_users', '', 1, 'Most users ever online: <b><!-- USERS --></b> on <i><!-- DATE --></i>.'),
('move', '', 1, 'Move'),
('msn', '', 1, 'MSN Messenger'),
('multi_guest', '', 1, '<b><!-- NUM --></b> guests'),
('multi_member', '', 1, '<b><!-- NUM --></b> members'),
('multi_user', '', 1, 'are <b><!-- NUM --></b> users'),
('name', '', 1, 'Name'),
('nametitle', '', 1, 'Name:'),
('new_pm', '', 1, 'New Private Message'),
('none', '', 1, 'None'),
('number_topics', '', 1, 'Topics:'),
('one_guest', '', 1, '<b>1</b> guest'),
('one_member', '', 1, '<b>1</b> member'),
('one_minute', '', 1, 'One minute ago'),
('one_user', '', 1, 'is <b>1</b> user'),
('online_main', '', 1, 'There <!-- USERS --> online (<!-- MEMBERS --> &amp; <!-- GUESTS --> &amp; <!-- SPIDERS -->).'),
('page', '', 1, 'page'),
('pages', '', 1, 'pages:'),
('php', '', 1, 'PHP Code'),
('orientation', 'global', 1, 'Orientation:'),
('search_forums', 'global', 1, 'Forums to search:'),
('preview', '', 1, 'Preview'),
('qs_text', '', 1, 'search'),
('qs_forum_1', '', 1, 'posts'),
('quote', '', 1, 'Quote:'),
('read', '', 1, 'Read'),
('register', '', 1, 'Register'),
('right_now', '', 1, 'Right now'),
('said', '', 1, 'said:'),
('search', '', 1, 'Search'),
('search_results', '', 1, 'Search Results'),
('second_ago', '', 1, ' seconds ago'),
('select', '', 1, 'Select:'),
('select_all', '', 1, 'Select all'),
('sendmailtouser', '', 1, 'Send email'),
('send_pm_to_user', '', 1, 'Send PM'),
('settings', '', 1, 'Settings'),
('show', '', 1, 'Show'),
('show_signature', '', 1, 'Show signature'),
('show_smilies', '', 1, 'Show smilies'),
('skype', '', 1, 'Skype'),
('smiley', '', 1, 'Smiley'),
('smilies', '', 1, 'Smilies'),
('multi_spider', '', 1, '<b><!-- NUM --></b> spiders'),
('sql_queries', '', 1, 'SQL queries'),
('statistics', '', 1, 'Statistics'),
('submit', '', 1, 'Submit'),
('technical_stats', '', 1, 'Render Time: <!-- TIME -->s | Query Execution Time: <!-- SQL TIME -->s | Queries Executed: <!-- SQL QUERIES -->'),
('text', '', 1, 'Text'),
('today', '', 1, 'Today,'),
('tommorow', '', 1, 'Tomorrow,'),
('totart', '', 1, 'Total Articles: '),
('totreply', '', 1, 'Total Replies: '),
('tottopics', '', 1, 'Total Topics: '),
('unannounce', '', 1, 'Unannounce'),
('unread', '', 1, 'Unread'),
('unselect_all', '', 1, 'Select none'),
('forum_stats', '', 1, 'Total forum topics: <b><!-- TOPICS --></b>. Total forum posts: <b><!-- POSTS --></b>.'),
('user_title', '', 1, 'User Title'),
('view_inbox', '', 1, 'Go to inbox'),
('view_new', '', 1, 'View in new window'),
('view_this', '', 1, 'View in current window'),
('website', '', 1, 'Website'),
('xmllang', '', 1, 'en'),
('yesterday', '', 1, 'Yesterday,'),
('yim', '', 1, 'Yahoo Instant Messenger'),
('zero', '', 1, '0%'),
('delete_topics', 'topic', 1, 'Delete Topics'),
('purge_topics', 'topic', 1, 'Purge Topics'),
('delete_topics_txt', 'topic', 1, 'Are you <b>absolutely</b> sure you want to delete these topics?'),
('purge_topics_txt', 'topic', 1, 'Are you <b>absolutely</b> sure you want to purge these topics? This is irreversible!'),
('purge', '', 1, 'Purge'),
('unhide', '', 1, 'Unhide'),
('undelete', '', 1, 'Undelete'),
('hidden_prefix', 'forum', 1, 'Hidden:'),
('delete_prefix', 'forum', 1, 'Deleted:'),
('topic_moderation', 'topic', 1, 'Topic Moderation'),
('post_moderation', 'topic', 1, 'Post Moderation'),
('act_mod_forum', 'forum', 1, 'Moderation actions successful on selected topics.'),
('act_read_board', 'forum', 1, 'All topics on this entire board have been marked as read.'),
('act_read_forum', 'forum', 1, 'All topics in this forum have been marked as read.'),
('announce', 'forum', 1, 'Announce'),
('announcement', 'forum', 1, 'Announcement:'),
('by', 'forum', 1, 'By:'),
('clicks', 'forum', 1, 'Clicks:'),
('creator', 'forum', 1, 'Creator'),
('err_no_permission_forum_list', 'forum', 1, 'You don''t have permission to view anything on the forum list.'),
('err_permission_topics', 'forum', 1, 'You don''t have permission to view topics in this forum.'),
('last_date', 'forum', 1, 'Last Post'),
('last_post_info', 'forum', 1, 'Last post in: <!-- TOPIC --><br />By: <!-- USER --><br />&raquo; <!-- TIME -->'),
('locked_prefix', 'forum', 1, 'Locked:'),
('locked_topic', 'forum', 1, 'Locked topic'),
('make_new_topic', 'forum', 1, 'New topic'),
('mark_board', 'forum', 1, 'Mark all forums as read'),
('mark_forum', 'forum', 1, 'Mark forum as read'),
('members_off_forum', 'forum', 1, 'There are currently no members browsing this forum.'),
('moderators', 'forum', 1, 'Moderators'),
('moved', 'forum', 1, 'Moved:'),
('moved_topic', 'forum', 1, 'Moved Topic'),
('never_posts', 'forum', 1, 'There have never been any posts in this forum.'),
('new_posts', 'forum', 1, 'New posts'),
('no_new_posts', 'forum', 1, 'No new posts'),
('no_topics', 'forum', 1, 'There are no topics that fit your desired parameters.'),
('number_replies', 'forum', 1, 'Replies:'),
('online_forum', 'forum', 1, 'There <!-- USERS --> browsing this forum (<!-- MEMBERS --> &amp; <!-- GUESTS -->).'),
('open', 'forum', 1, 'Open'),
('read_poll', 'forum', 1, 'Read poll'),
('read_poll_hot', 'forum', 1, 'Read hot poll'),
('read_topic', 'forum', 1, 'Read topic'),
('read_topic_hot', 'forum', 1, 'Read hot topic'),
('redirect', 'forum', 1, 'Redirect'),
('replies', 'forum', 1, 'Replies'),
('selected_topics', 'forum', 1, 'With Selected Topics...'),
('topic_sticky', 'forum', 1, 'Sticky:'),
('sticky_topic', 'forum', 1, 'Sticky topic'),
('subforums', 'forum', 1, 'Subforums:'),
('topics', 'forum', 1, 'Topics'),
('topic_date', 'forum', 1, 'Start Date'),
('top_posters', 'forum', 1, 'Top posters'),
('unannounce', 'forum', 1, 'Unannounce'),
('unread_poll', 'forum', 1, 'Unread poll'),
('unread_poll_hot', 'forum', 1, 'Unread hot poll'),
('unread_topic', 'forum', 1, 'Unread topic'),
('unread_topic_hot', 'forum', 1, 'Unread hot topic'),
('unsticky', 'forum', 1, 'Unsticky'),
('views', 'forum', 1, 'Views'),
('view_all_topics', 'forum', 1, 'View all topics'),
('about_login', 'login', 1, 'You need to enter your account details in order to login.'),
('about_recover', 'login', 1, 'Recover your account''s password or resend activation email.'),
('activate_account', 'login', 1, 'Activate Account'),
('activation_code', 'login', 1, 'Activation Code:'),
('activation_from_mail', 'login', 1, 'Refer to your email for this information if nothing appears.'),
('activation_id', 'login', 1, 'Activation ID:'),
('act_activate', '', 1, 'Your new account has been successfully created and is awaiting email validation. You should be getting an email with the details for activating your account in the next few minutes if it hasn''t arrived already. Make sure you check your spam folder in case it was moved there by mistake.'),
('act_new_password_done', 'login', 1, 'Your password has been reset successfully. You may now login with your new account details.'),
('act_password_sent', 'login', 1, 'An email with details about resetting your password has been sent to the inputted email address and should arrive in the next few minutes if it hasn''t arrived already. Make sure you check your spam folder in case it was moved there by mistake.'),
('act_validate_done', 'login', 1, 'Your account has been activated. You may now login.'),
('act_validate_sent', 'login', 1, 'A new validation email has been sent to the email you submitted and should arrive in the next few minutes if it hasn''t arrived already. Make sure you check your spam folder in case it was moved there by mistake.'),
('agree', 'login', 1, 'I agree to the terms and conditions of registration'),
('amail', 'login', 1, 'Allow administration to email you.'),
('birthday', 'login', 1, 'Birthday:'),
('email', 'login', 1, 'Email Address:'),
('email_activate', '', 1, 'Activate Account'),
('email_recover', 'login', 1, '<!-- NAME -->,\r\nAn individual has requested to recover your account at <!-- RAW URL -->. If you did not take such an action, ignore this email, as no changes to your account have been made.\r\n\r\nIf you did request to reset the account of a password you own, you can do it at the following link:\r\n<!-- URL -->pg=login;do=<!-- DO -->;id=<!-- ID -->;code=<!-- CODE -->\r\n\r\nYou may need to copy and paste it into your browser.\r\n\r\n-----------------\r\n\r\nThe page will ask you for your activation ID and email code. They should be already filled out with the below information, if not, fill them in manually.\r\n\r\nActivation ID: <!-- ID -->\r\nActivation Code: <!-- CODE -->\r\n\r\n-----------------\r\n\r\nThe IP Address of the individual who initiated the sending of this email is: <!-- IP -->\r\n\r\nThank you!'),
('email_register', '', 1, '<!-- NAME -->,\r\nYour email has been used for an account at <!-- RAW URL -->. Ignore this email if you did not sign up for this account.\r\n\r\nIf you did register this account and wish to activate it, you can do it at the following link:\r\n<!-- URL -->pg=login;do=submit_activate;id=<!-- ID -->;code=<!-- CODE -->\r\n\r\nYou may need to copy and paste it into your browser.\r\n\r\n-----------------\r\n\r\nThe page will ask you for your activation ID and email code. They should be already filled out with the below information, if not, fill them in manually.\r\n\r\nActivation ID: <!-- ID -->\r\nActivation Code: <!-- CODE -->\r\n\r\n-----------------\r\n\r\nThe IP Address of the individual who initiated the sending of this email is: <!-- IP -->\r\n\r\nThank you!'),
('email_reset_password', 'login', 1, 'Reset Password'),
('enter_new_password', 'login', 1, 'Enter the new password you wish to use for your account.'),
('err_attempts_left', 'login', 1, 'You have <!-- NUM --> attempt(s) left before you are locked out from logging in for 15 minutes.'),
('err_attempts_none', 'login', 1, 'You have been temporarily locked out of logging in due to too many attempts returning errors. Please wait for 15 minutes and try again.'),
('err_bad_mail', 'login', 1, 'The email address is invalid.'),
('err_birthday_valid', 'login', 1, 'The birthday is not an actual date,'),
('err_email_used', 'login', 1, 'The email is already being used by another account.'),
('err_long_name', 'login', 1, 'The username is too long.'),
('err_match_password', 'login', 1, 'Your inputted passwords don''t match.'),
('err_name_not_exists', 'login', 1, 'The submitted username has never been registered.'),
('err_no_birthday', 'login', 1, 'Birthday was not properly filled out.'),
('err_no_mail', 'login', 1, 'You did not fill in an email address.'),
('err_no_name', 'login', 1, 'You did not fill in a username.'),
('err_no_name_email_found', 'login', 1, 'The name and email you entered did not match any of our records.'),
('err_no_password', 'login', 1, 'You did not fill in a password.'),
('err_no_validating', 'login', 1, 'The account you requested is not marked under any type of validation.'),
('err_no_vpassword', 'login', 1, 'You did not verify your password.'),
('err_password_incorrect', '', 1, 'The password you entered did not match your account password.'),
('err_password_too_easy', 'login', 1, 'Your password is too easy to guess or is using a keyboard combination. Please choose a different password.'),
('err_short_password', 'login', 1, 'Your desired changed password is too short.'),
('err_taken_name', 'login', 1, 'Your desired username has already been taken.'),
('err_terms', 'login', 1, 'You did not accept the terms of registration.'),
('err_validate_data', 'login', 1, 'The activation details that you submitted do not match. This could be due to an older email or incorrect information. Try to resend your activation request one more time or check the id and code.'),
('err_validating', 'login', 1, 'This account exists but is currently awaiting email validation. You cannot login until you recieve this email. If you have not received this email yet, please fill out our ''resend validation email'' form. You may even resend it to a new email address.'),
('forgot_pass', 'login', 1, 'Retrieve my password'),
('help_birthday', 'login', 1, 'The day you were born. Since your birthday never changes, you only get the opportunity to select it ONCE. Make absolutely sure it is accurate as you do not have the option to change it later.&lt;br />&lt;br /&gt;&lt;i&gt;Must be a valid date.&lt;br /&gt;You must be 13 years of age or older.&lt;/i&gt;'),
('help_email_recover', 'login', 1, 'A valid email address. This only has to be the email you signed up with if you did not fill out the password field above. If you choose to fill out both fields, your activation email will go to the new address you fill out, regardless if it is the one you signed up with or not. &lt;br /&gt;&lt;br /&gt;&lt;i&gt;Must be a valid email address.&lt;/i&gt;'),
('help_password', 'login', 1, 'The password that you entered when you registered the account you typed in above.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: Minimum 5 characters.&lt;/i&gt;'),
('help_password_register', 'login', 1, 'A password for authentication. It will be required for whenever you wish to login. If you forget the password later, you can always recover it using the lost password form.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: Minimum 5 characters.&lt;/i&gt;'),
('help_remember_me', 'login', 1, 'Check this option to automatically log you in everytime you visit this website on the computer you are currently on. Do not enable this option if you are on a public computer.&lt;br />&lt;br /&gt;&lt;i&gt;Cookies must be enabled on your computer.&lt;/i&gt;'),
('help_username', 'login', 1, 'The screen name you entered when you previously registered on this website.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-25 characters.&lt;br /&gt;Disallowed Characters: [ ]&lt;/i&gt;'),
('help_username_register', 'login', 1, 'A screen name which will represent you on this website. You will need to use it for logging in, and it will appear next to any content you submit, so that others may identify you. If the administrator allows, you may change this later in your control panel.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-25 characters.&lt;br /&gt;Disallowed Characters: [ ]&lt;/i&gt;'),
('help_vpassword', 'login', 1, 'Type in the same password as you typed in above to verify consistency.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 5-32 characters.&lt;/i&gt;'),
('mail', 'login', 1, 'Email Settings:'),
('mmail', 'login', 1, 'Allow members to email you.'),
('month_01', 'login', 1, 'January'),
('month_02', 'login', 1, 'February'),
('month_03', 'login', 1, 'March'),
('month_04', 'login', 1, 'April'),
('month_05', 'login', 1, 'May'),
('month_06', 'login', 1, 'June'),
('month_07', 'login', 1, 'July'),
('month_08', 'login', 1, 'August'),
('month_09', 'login', 1, 'September'),
('month_10', 'login', 1, 'October'),
('month_11', 'login', 1, 'November'),
('month_12', 'login', 1, 'December'),
('must_agree', 'login', 1, 'You must agree to the terms and conditions of registration below'),
('obdst', 'login', 1, 'Currently observing daylight-saving time'),
('pass', 'login', 1, 'Password:'),
('recover', 'login', 1, 'Recover my Account'),
('remember', 'login', 1, 'Remember me'),
('required', 'login', 1, 'All fields below must be filled out'),
('reset_password', 'login', 1, 'Reset Password'),
('submit_activation', 'login', 1, 'Activate my account!'),
('submit_login', 'login', 1, 'Submit Login'),
('submit_recover', 'login', 1, 'Recover Account'),
('submit_registration', 'login', 1, 'Submit Registration'),
('submit_resend', 'login', 1, 'Resend validation email'),
('submit_reset_password', 'login', 1, 'Reset my Password'),
('terms', 'login', 1, 'Read the terms and conditions of registration. If you agree with what is written here, then check the checkbox below and your registration process will continue.\r\n<br /><br />\r\nYou must be at least 13 years of age in order to register. \r\n<br /><br />\r\nThe content found herein may not be the opinions or views of the administrators or creator of the Latova. There is not way to guarantee authenticity of any content hosted by this website.\r\n<br /><br />\r\nAny content that you submit will NOT be abusive, destructive, threatening, racist, false, infringes on copyrights, or breaks laws according to the laws in your country or the country in which this server resides in. You will obey the guidelines and rules posted by administrators. \r\n<br /><br />\r\nThe administrators reserve all and any rights to censor or change information, for example by editing, deleting, or moving. You may contact them for any further clarification about these guidelines.\r\n<br /><br />\r\nYou acknowledge that the administrators may not be able to moderate all content that may be displayed, as it is impossible for them to review all everything. You are encouraged to report content that you think may be a breach of any rules or regulations of this website to the administrators.'),
('timezone_settings', 'login', 1, 'Time zone Settings:'),
('time_-01', 'login', 1, '(GMT -1:00 hour) Azores, Cape Verde Islands'),
('time_-02', 'login', 1, '(GMT -2:00) Mid-Atlantic'),
('time_-03', 'login', 1, '(GMT -3:00) Brazil, Buenos Aires, Georgetown'),
('time_-03.5', 'login', 1, '(GMT -3:30) Newfoundland'),
('time_-04', 'login', 1, '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz'),
('time_-05', 'login', 1, '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima'),
('time_-06', 'login', 1, '(GMT -6:00) Central Time (US &amp; Canada), Mexico City'),
('time_-07', 'login', 1, '(GMT -7:00) Mountain Time (US &amp; Canada)'),
('time_-08', 'login', 1, '(GMT -8:00) Pacific Time (US &amp; Canada)'),
('time_-09', 'login', 1, '(GMT -9:00) Alaska'),
('time_-10', 'login', 1, '(GMT -10:00) Hawaii'),
('time_-11', 'login', 1, '(GMT -11:00) Midway Island, Samoa'),
('time_-12', 'login', 1, '(GMT -12:00) Eniwetok, Kwajalein'),
('time_0', 'login', 1, '(GMT) Western Europe Time, London, Lisbon, Casablanca'),
('time_01', 'login', 1, '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris'),
('time_02', 'login', 1, '(GMT +2:00) Kaliningrad, South Africa'),
('time_03', 'login', 1, '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg'),
('time_03.5', 'login', 1, '(GMT +3:30) Tehran'),
('time_04', 'login', 1, '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi'),
('time_04.5', 'login', 1, '(GMT +4:30) Kabu'),
('time_05', 'login', 1, '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent'),
('time_05.5', 'login', 1, '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'),
('time_06', 'login', 1, '(GMT +6:00) Almaty, Dhaka, Colombo'),
('time_07', 'login', 1, '(GMT +7:00) Bangkok, Hanoi, Jakarta'),
('time_08', 'login', 1, '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'),
('time_09', 'login', 1, '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk'),
('time_09.5', 'login', 1, '(GMT +9:30) Adelaide, Darwin'),
('time_10', 'login', 1, '(GMT +10:00) Eastern Australia, Guam, Vladivostok'),
('time_11', 'login', 1, '(GMT +11:00) Magadan, Solomon Islands, New Caledonia'),
('time_12', 'login', 1, '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'),
('user', 'login', 1, 'Username:'),
('validate', 'login', 1, 'Validate'),
('vpassword', 'login', 1, 'Verify Password:'),
('err_no_forums', 'global', 1, 'No forums were selected to search!'),
('asc', 'global', 1, 'Ascending'),
('con', 'member', 1, 'Content Submitted'),
('conl_posts', 'member', 1, 'pg=global;do=submit_search;branch=topic;order=lastd;or=desc;res=posts;searchin=msg;forums_skip=1;user_id=<!-- USER -->">View All Posts'),
('conl_topics', 'member', 1, 'pg=global;do=submit_search;branch=topic;order=lastd;or=desc;res=topics;searchin=title;forums_skip=1;user_id=<!-- USER -->">View All Topics'),
('cons_posts', 'member', 1, 'Total Posts: '),
('cons_topics', 'member', 1, 'Total Topics: '),
('contact_details', 'member', 1, 'Contact Details'),
('desc', 'global', 1, 'Descending'),
('errchangeqs', 'member', 1, 'You didn''t change the quick search textbox.'),
('err_no_search', 'global', 1, 'No search results were found. Check for spelling errors or use different keywords and try again.'),
('err_search', 'global', 1, 'You did not choose anything to search for.'),
('err_no_name', 'global', 1, 'Could not find the specified username.'),
('err_search_flood', 'global', 1, 'You have attempted too many searches within a short period of time. Please wait at least <!-- NUM --> seconds and try again.'),
('err_search_short', 'global', 1, 'One or more keywords are under 4 characters.'),
('err_no_user', 'member', 1, 'This user does not exist.'),
('joined_date', 'member', 1, 'Registration Date: '),
('last_login', 'member', 1, 'Last Login: '),
('member_list', 'member', 1, 'Member List'),
('no_info', 'member', 1, '<i>No Information</i>'),
('no_photo', 'member', 1, '<i>No Photo</i>'),
('online_list', 'member', 1, 'Detailed online member list'),
('on_error-', '', 1, 'Encountered an error'),
('on_forum-', '', 1, 'Viewing forum index'),
('on_forum-view', '', 1, 'Viewing forum: <a href="<!-- URL -->forum=<!-- ID -->"><!-- EXTRA --></a>'),
('on_login-', '', 1, 'Logging in'),
('on_login-lost_pass', '', 1, 'Sending lost password email'),
('on_login-manual_reset', '', 1, 'Manually reseting password'),
('on_login-manual_validate', '', 1, 'Manually validating a user account'),
('on_login-register', '', 1, 'Creating a new user account'),
('on_login-resend', '', 1, 'Resending a validation email'),
('on_login-reset', '', 1, 'Resetting account password'),
('on_member-', '', 1, 'Viewing Member List'),
('on_member-online', '', 1, 'Viewing the online list'),
('on_member-profile', '', 1, 'Viewing member profile: <a href="<!-- URL -->member=<!-- ID -->"><!-- EXTRA --></a>'),
('on_post-edit', '', 1, 'Editing reply in <a href="<!-- URL -->pg=topic;do=find;id=<!-- ID -->"><!-- EXTRA --></a>'),
('on_post-reply', '', 1, 'Posting a new reply in <a href="<!-- URL -->topic=<!-- ID -->"><!-- EXTRA --></a>'),
('on_post-topic', '', 1, 'Posting a new topic in <a href="<!-- URL -->forum=<!-- ID -->"><!-- EXTRA --></a>'),
('on_topic-', '', 1, 'Viewing topic: <a href="<!-- URL -->topic=<!-- ID -->"><!-- EXTRA --></a>'),
('on_ucp-', '', 1, 'Viewing dashboard overview'),
('on_ucp-avatar', '', 1, 'Choosing a new avatar'),
('on_ucp-config', '', 1, 'Updating account configuration'),
('on_ucp-email', '', 1, 'Changing account email'),
('on_ucp-passwd', '', 1, 'Changing account password'),
('on_ucp-photo', '', 1, 'Choosing a new photo'),
('on_ucp-signature', '', 1, 'Editing signature'),
('on_unknown', '', 1, 'Viewing the website'),
('order_results', 'global', 1, 'Order results by:'),
('global', 'member', 1, 'Orientation: '),
('other', 'member', 1, 'Other'),
('per_day', 'member', 1, ' (<!-- NUM --> per day)'),
('qs_forum_2', '', 1, 'topics'),
('s_forum', 'global', 1, 'Forum'),
('send_pm', 'member', 1, 'Send Private Message to <!-- NAME -->'),
('show_results_as', 'global', 1, 'Show results as:'),
('signature', 'member', 1, 'Signature'),
('last_post_date', 'global', 1, 'Last Post Date'),
('posts', 'global', 1, 'Posts'),
('replies', 'global', 1, 'Replies'),
('topics', 'global', 1, 'Topics'),
('views', 'global', 1, 'Views'),
('userl_gid', 'member', 1, 'Group'),
('userl_posts', 'member', 1, 'Posts'),
('userl_profile_location', 'member', 1, 'Location'),
('userl_username', 'member', 1, 'Username'),
('user_statistics', 'member', 1, 'User Statistics'),
('viewing_profile', 'member', 1, 'Viewing Profile: '),
('search_terms', 'global', 1, 'Search terms:'),
('search_user', 'global', 1, 'Posted by user:'),
('act_folders', 'msg', 1, 'Private message folders have been updated with your changes.'),
('act_pms', 'msg', 1, 'Actions to selected private messages were successful.'),
('act_pm_sent', 'msg', 1, 'Private message has been sent.'),
('confirm_delete', 'msg', 1, 'ARE YOU SURE YOU WISH TO DELETE THIS FOLDER?'),
('confirm_empty', 'msg', 1, 'ARE YOU SURE YOU WISH TO DELETE ALL MESSAGES IN THIS FOLDER?'),
('contain_pm', 'msg', 1, '(Contains 1 Private Message) '),
('contain_pms', 'msg', 1, '(Contains <!-- NUM --> Private Messages) '),
('drafts', 'msg', 1, 'Drafts'),
('edit_folders', 'msg', 1, 'Edit Folders'),
('edit_folders_info', 'msg', 1, 'Folders (except root) must be emptied before deletion is possible.'),
('err_cant_pm', 'msg', 1, 'You aren''t permitted to use the private message features.'),
('err_folder', 'msg', 1, 'The requested folder doesn''t exist.'),
('err_folder_length', 'msg', 1, 'You have too many folders!'),
('err_folder_long', 'msg', 1, 'One of the folders you have entered is too long!'),
('err_folder_name', 'msg', 1, 'The folder name you have entered is reserved or already in use.'),
('err_folder_null', 'msg', 1, 'That is not a valid name for a folder.'),
('err_max_pms', 'msg', 1, 'You have reached your limit of private messages.'),
('err_message_long', 'msg', 1, 'The message you have entered is too long.'),
('err_message_none', 'msg', 1, 'You didn''t enter in a message!'),
('err_no_pm_find', 'msg', 1, 'Could not find the private message requested.'),
('err_no_select', 'msg', 1, 'You didn''t select an action to take!'),
('err_old_password_mismatch', 'msg', 1, 'Your inputted old password does not match your account password.'),
('err_pm_select', 'msg', 1, 'No private messages have been selected for this action!'),
('err_pm_to_found', 'msg', 1, 'The recipient you entered was not found.'),
('err_pm_to_none', 'msg', 1, 'You didn''t fill in a recipient.'),
('err_subject_long', 'msg', 1, 'The subject you have entered is too long.'),
('err_subject_none', 'msg', 1, 'You didn''t enter in a subject!'),
('err_to_cant_pm', 'msg', 1, 'A recipient of this private message has no private messaging permission.'),
('err_to_max_pms', 'msg', 1, 'A recipient of this private message has reached their limit of private messages.'),
('go_to_folder', 'msg', 1, 'Go to folder:'),
('help_new_folder', 'msg', 1, 'A new private message folder that will be listed on your control panel navigation where you can store your private messages.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-50 characters&lt;/i&gt;'),
('help_subject', 'msg', 1, 'A private message subject that the recipient(s) can identify the private message contents with.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-50 characters.&lt;/i&gt;'),
('help_to', 'msg', 1, 'Enter in the main recipient of this private message. This must be a valid user signed up for this website and has permission to use the private message system.&lt;br />&lt;br /&gt;&lt;i&gt;Invalid characters: , [ ]&lt;br /&gt;Length: 1-25 characters.&lt;/i&gt;'),
('inbox', 'msg', 1, 'Inbox'),
('make_folder', 'msg', 1, 'Create new folder'),
('make_new_pm', 'msg', 1, 'New PM'),
('making_new_pm', 'msg', 1, 'Composing new Private Message'),
('message', 'msg', 1, 'Message'),
('message_details', 'msg', 1, 'Message details'),
('no_delete', 'msg', 1, 'Cannot be deleted'),
('no_pms', 'msg', 1, 'No private messages are stored in this folder'),
('pm_amount', 'msg', 1, 'You have <!-- NUM --> out of <!-- MAX --> stored PMs'),
('preview_pm', 'msg', 1, 'Preview Private Message'),
('reply_to_pm', 'msg', 1, 'Reply'),
('save_pm', 'msg', 1, 'Save copy to sent'),
('select_read', 'msg', 1, 'Select read'),
('sending_details', 'msg', 1, 'Private message recipient(s)'),
('sent', 'msg', 1, 'Sent'),
('sent_by', 'msg', 1, 'Sent by'),
('sent_on', 'msg', 1, 'Sent:'),
('sent_to', 'msg', 1, 'Sent to'),
('subject', 'msg', 1, 'Subject:'),
('take_action', 'msg', 1, 'Take the following action:'),
('to', 'msg', 1, 'To:'),
('with_selected', 'msg', 1, 'With selected messages...'),
('add_poll', 'post', 1, 'Add Poll'),
('after_posting', 'post', 1, 'After Posting:'),
('announce', 'post', 1, 'Announce'),
('edit_in', 'post', 1, 'Editing a reply in '),
('edit_last', 'post', 1, 'Preserve the previous editor'),
('edit_none', 'post', 1, 'Remove the last editor'),
('edit_record', 'post', 1, 'Mark me as the last editor'),
('errpostedit', 'post', 1, 'You don''t have permission to edit this post!'),
('errpostupper', 'post', 1, 'Too much of your post was found to be in uppercase. Please revise your post and use less capital letters.'),
('err_permission_edit', 'post', 1, 'You don''t have permission to edit your posts in this forum.'),
('err_permission_replies', 'post', 1, 'You don''t have permission to make replies in this forum.'),
('err_permission_topic', 'post', 1, 'You don''t have permission to make topics in this forum.'),
('errtopicupper', 'post', 1, 'Too much of your topic title was found to be in uppercase. Please revise your topic title and use less capital letters.'),
('err_locked', 'post', 1, 'You can''t post in a locked topic!'),
('err_no_post', 'post', 1, 'The post you are attempting to edit doesn''t exist!'),
('err_no_topic', 'post', 1, 'The topic does not exist.'),
('err_option', 'post', 1, 'One or more polls have too many or too little options. Check to make sure that each option is on its own line.'),
('err_option_long', 'post', 1, 'One or more poll options are too long. Please recheck your options and ensure they are under or equal 50 characters.'),
('err_post_flood', 'post', 1, 'You have posted too much in a short period of time. Please wait at least <!-- NUM --> seconds and try again.'),
('err_post_long', 'post', 1, 'The post you have entered is too long.'),
('err_post_none', 'post', 1, 'You didn''t enter in a post!'),
('err_title_long', 'post', 1, 'The topic title you have entered is too long.'),
('err_title_none', 'post', 1, 'You need to enter a topic title!'),
('err_title_short', 'post', 1, 'The topic title you have entered is too short.'),
('help_poll', 'post', 1, 'Adds poll options to this form. It will allow you to survey individuals who read your topic and view the results. They can vote for different options that you specify.'),
('help_poptions', 'post', 1, 'Put one option on each line to represent different options your poll will have. You can have up to &lt;!-- NUM --&gt; options.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Each option can be 1-200 characters.&lt;/i&gt;'),
('help_pquestion', 'post', 1, 'Ask a poll question for users who read your topic. The members will answer the question with one of the options you specify below. If you want to skip this poll item, just leave it empty.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 1-50 characters.&lt;/i&gt;'),
('help_ptype', 'post', 1, 'Choose a poll type. Multiple selection will allow the user to choose as many options as you provide. Single selection will limit the member to only one choice out of the options you specify.'),
('help_topic_title', 'post', 1, 'The title to the topic you are creating. It should describe what the topic is about. Make sure you use specific keywords to help others search for the topic and to understand exactly what it contains.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: 2-50 characters.&lt;/i&gt;'),
('lastpoststitle', 'post', 1, 'Last 10 Posts (Newest at the top)'),
('new_modify', 'post', 1, 'Modifying Post'),
('new_modify_in', 'post', 1, 'Modifying Post in '),
('new_reply', 'post', 1, 'Posting New Reply'),
('new_reply_in', 'post', 1, 'Posting Reply in '),
('new_topic', 'post', 1, 'Posting New Topic'),
('new_topic_in', 'post', 1, 'Posting Topic in '),
('pollcheck', 'post', 1, 'Multiple Selection'),
('polloptions', 'post', 1, 'Poll Options:'),
('pollquestion', 'post', 1, 'Poll Question:'),
('pollradio', 'post', 1, 'Single Selection'),
('polltype', 'post', 1, 'Poll Type:'),
('poll_num', 'post', 1, 'Poll <!-- NUM -->'),
('post_settings', 'post', 1, 'Post Settings'),
('preview_post', 'post', 1, 'Post Preview'),
('sticky', 'post', 1, 'Sticky'),
('title', 'post', 1, 'Topic Title:'),
('topicdetails', 'post', 1, 'Topic Details'),
('topic_icon', 'post', 1, 'Topic Icon:'),
('topic_settings', 'post', 1, 'Topic Settings'),
('about_move_topics', 'topic', 1, 'Select options for the topics you are about to move.'),
('about_announce_topic', 'topic', 1, 'Turns topics into an announcement that can be seen over several separate forums.'),
('act_mod_topic', 'topic', 1, 'Moderation actions successful on current topic.'),
('act_new_topic', 'topic', 1, 'Your topic has been posted successfully.'),
('act_vote', 'topic', 1, 'Your vote has been processed.'),
('announce', 'topic', 1, 'Announce'),
('announce_topic', 'topic', 1, 'Announce Topic'),
('confirm_delete_post', 'topic', 1, 'Are you sure you want to delete this post?'),
('deletepost', 'topic', 1, 'Delete post'),
('destination_forum', 'topic', 1, 'Destination forum:'),
('err_no_forum', 'post', 1, 'This forum doesn''t exist!'),
('errnovote', 'topic', 1, 'You didn''t vote for one or more options. You are required to vote for all single choice polls but multiple choice polls are optional.'),
('errpreposts', 'topic', 1, 'You don''t have permission to view posts in this forum.'),
('errvoted', 'topic', 1, 'You''ve already voted in this poll!'),
('err_no_item_moderate', 'topic', 1, 'No items were selected for moderation.'),
('err_no_topic', 'topic', 1, 'The topic does not exist.'),
('err_topic_open', 'topic', 1, 'You are not allowed to open topics that moderators have locked.'),
('help_destination_forum', 'topic', 1, 'The topics you selected previously will be moved to the forum in which you select here.'),
('help_leave_link', 'topic', 1, 'This option will leave a link topic to redirect members to where the topic has been moved. This applies for all topics you have selected!'),
('help_target_forums', 'topic', 1, 'The forums you select will display selected topic(s) on the top of the forum as an announcement.'),
('homeforum', 'topic', 1, '(home forum)'),
('last_edited', 'topic', 1, 'Last edited <b><!-- TIME --></b> by <!-- USER -->.'),
('leave_link', 'topic', 1, 'Leave topic link'),
('make_new_reply', 'topic', 1, 'Reply'),
('make_new_topic', 'topic', 1, 'New topic'),
('members_off_topic', 'topic', 1, 'There are currently no registered members reading this topic.'),
('modify', 'topic', 1, 'Modify'),
('moreoptions', 'topic', 1, 'More options'),
('move_topics', 'topic', 1, 'Move topics'),
('multi_quote', 'topic', 1, 'Click here to toggle multiquote'),
('online_topic', 'topic', 1, 'There <!-- USERS --> reading this topic (<!-- MEMBERS --> &amp; <!-- GUESTS -->).'),
('open', 'topic', 1, 'Open'),
('poll', 'topic', 1, 'Poll'),
('poll_login', 'topic', 1, 'You need to login before you can interact with polls.'),
('posted', 'topic', 1, 'Posted:'),
('posted_in', 'topic', 1, 'Posted in: '),
('posted_ip', 'topic', 1, ' <i>from</i> <!-- IP -->'),
('post_popup', 'topic', 1, 'Link to the post:'),
('qr', 'topic', 1, 'Quick Reply'),
('quote', 'topic', 1, 'Quote'),
('showresults', 'topic', 1, 'Show Results'),
('sticky', 'topic', 1, 'Sticky'),
('target_forums', 'topic', 1, 'Target forums:'),
('total_votes', 'topic', 1, 'Total Voters: <!-- VOTES -->'),
('unsticky', 'topic', 1, 'Unsticky'),
('view_first_unread', 'topic', 1, 'View First Unread Post'),
('vote', 'topic', 1, 'Vote'),
('votes_stats', 'topic', 1, ' <!-- VOTES --> votes (<!-- PERCENT -->%)'),
('vote_stats', 'topic', 1, ' <!-- VOTES --> vote (<!-- PERCENT -->%)'),
('with_selected_posts', 'topic', 1, 'With selected posts...'),
('with_this_topic', 'topic', 1, 'With this topic...'),
('about', 'ucp', 1, 'About'),
('about_avatar', 'ucp', 1, 'Change the image which represents you on the website.'),
('about_avatars', 'ucp', 1, 'Toggle display of avatars which applies over the entire site.'),
('about_change_email', 'ucp', 1, 'Change the email attached to your account for communication.'),
('about_change_password', 'ucp', 1, 'Change the password you use to login to your account.'),
('about_dst', 'ucp', 1, 'If your timezone is correctly selected and the times appear to be an hour off, switch this setting.'),
('about_long_date', 'ucp', 1, 'Use PHP date format to change the way longer dates appear on the site. Text encased with square brackets [ ] will be replaced with relative time when applicable. Leave this field blank to use site default.'),
('about_num_posts', 'ucp', 1, 'Number of posts to display per page in a topic. Higher numbers will increase load time.'),
('about_num_topics', 'ucp', 1, 'Number of topics to display per page in a forum. Higher numbers will increase load time.'),
('about_photo', 'ucp', 1, 'Change the picture of yourself that appears in your profile.'),
('about_profile', 'ucp', 1, 'Update your contact and personal details.'),
('about_setting_forum', 'ucp', 1, 'The forum module for community discussion.'),
('about_setting_global', 'ucp', 1, 'Site-wide settings that apply to all or most modules.'),
('about_short_date', 'ucp', 1, 'Use PHP date format to change the way shorter dates appear on the site. Text encased with square brackets [ ] will be replaced with relative time when applicable. Leave this field blank to use site default.'),
('about_signature', 'ucp', 1, 'Change the personal text that appears at the bottom of content you submit.'),
('about_signatures', 'ucp', 1, 'Toggle display of signatures which applies over the entire site.'),
('about_timezone', 'ucp', 1, 'Select your timezone to properly set dates relative to your time. Current time in the selected timezone: <!-- TIME -->.'),
('about_user_title', 'ucp', 1, 'Text that will appear under your name in locations which content are displayed. It should represent who you are or something about you.'),
('account_configuration', 'ucp', 1, 'Account Configuration'),
('act_avatar', 'ucp', 1, 'Your avatar has been updated.'),
('act_config_update', 'ucp', 1, 'Configuration updated.'),
('act_delete_avatar', 'ucp', 1, 'Your avatar has been deleted.'),
('act_delete_photo', 'ucp', 1, 'Your photo has been deleted.'),
('act_email', 'ucp', 1, 'Your email address has been updated.'),
('act_password', 'ucp', 1, 'Your password has been updated.'),
('act_photo', 'ucp', 1, 'Your photo has been updated.'),
('act_signature', 'ucp', 1, 'Your signature has been updated.'),
('avatar', 'ucp', 1, 'Avatar'),
('avatars', 'ucp', 1, 'Avatars'),
('change_email', 'ucp', 1, 'Change Email Address'),
('change_password', 'ucp', 1, 'Change Password'),
('chrs_sig', 'ucp', 1, ' characters.'),
('config', 'ucp', 1, 'Configuration'),
('current_sig', 'ucp', 1, 'Current Signature'),
('default', 'ucp', 1, 'Default'),
('delete_avatar', 'ucp', 1, 'Delete Avatar'),
('delete_photo', 'ucp', 1, 'Delete Photo'),
('disable_dst', 'ucp', 1, 'Disable DST'),
('dst', 'ucp', 1, 'Daylight-Savings'),
('editing_sig', 'ucp', 1, 'Editing Signature'),
('enable_dst', 'ucp', 1, 'Enable DST'),
('enter_sig', 'ucp', 1, 'Enter Signature'),
('err_bad_mail', 'ucp', 1, 'Your inputted email address is invalid.'),
('err_corrupt_avatar', 'ucp', 1, 'The avatar you have attempted to select appears to be corrupt or invalid.'),
('err_corrupt_photo', 'ucp', 1, 'The photo you have attempted to select appears to be corrupt or invalid.'),
('err_extension_avatar', 'ucp', 1, 'The extension for the avatar you have attempted to upload is not permitted on this board.'),
('err_extension_photo', 'ucp', 1, 'The extension for the photo you have attempted to upload is not permitted on this board.'),
('err_folder_delete', 'ucp', 1, 'Cannot delete this folder, it is not empty or reserved.'),
('err_long_link', 'ucp', 1, 'The link you have entered is too long.'),
('err_match_mail', 'ucp', 1, 'Your inputted email addresses don''t match.'),
('err_match_new_password', 'ucp', 1, 'Your desired new passwords don''t match.'),
('err_move_avatar', 'ucp', 1, 'There was a problem in moving your avatar. Notify the administrator.'),
('err_move_photo', 'ucp', 1, 'There was a problem in moving your photo. Notify the administrator.'),
('err_not_gallery', 'ucp', 1, 'That gallery does not exist.'),
('err_no_avatar', 'ucp', 1, 'You did not fill in any information to submit an avatar.'),
('err_no_avatars', 'ucp', 1, 'No valid avatars exist in this gallery.'),
('err_no_config', 'ucp', 1, 'No configuration set exists for the requested profile section.'),
('err_no_new_mail', 'ucp', 1, 'You did not fill in a new email address.'),
('err_no_new_password', 'ucp', 1, 'You did not fill in a new password.'),
('err_no_new_vpass', 'ucp', 1, 'You did not verify your new password.'),
('err_no_old_password', 'ucp', 1, 'You did not fill in a old password.'),
('err_no_photo', 'ucp', 1, 'You did not fill in any information to submit an photo.'),
('err_no_vmail', 'ucp', 1, 'You did not verify your new email address.'),
('err_password_too_easy', 'ucp', 1, 'Your password is too easy to guess or is using a keyboard combination. Please choose a different password.'),
('err_same_mail', 'ucp', 1, 'Your desired email is the same as the one you have currently.'),
('err_same_pass', 'ucp', 1, 'Your desired password is the same as the one you have currently. If you don''t wish to change your password, don''t fill in the fields.'),
('err_short_password', 'ucp', 1, 'Your desired changed password is too short.'),
('err_signature_long', 'ucp', 1, 'Your signature is too long'),
('err_size_avatar', 'ucp', 1, 'The avatar you have attempted to upload is too large.'),
('err_size_photo', 'ucp', 1, 'The photo you have attempted to upload is too large.'),
('err_used_mail', 'ucp', 1, 'Your desired email is being used by another account.'),
('err_write_avatar', 'ucp', 1, 'The avatar directory is not writable. Please notify the administrator.'),
('err_write_photo', 'ucp', 1, 'The photo directory is not writable. Please notify the administrator.'),
('fields_required', 'ucp', 1, 'Fields marked with an asterisk (*) are required.'),
('gallery_avatar', 'ucp', 1, 'Gallery Avatar '),
('gallery_pick', 'ucp', 1, 'Gallery:'),
('global', 'ucp', 1, 'Global'),
('help_link', 'ucp', 1, 'Links to a avatar/photo hosted on a website accessible on the internet.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Must be an image hosted on a internet server.&lt;br /&gt;Must have a valid extention listed above.&lt;/i&gt;'),
('help_mailv', 'ucp', 1, 'Type in the same email address as you typed in above to verify consistency.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Optional.&lt;br /&gt;Must be a valid email address.&lt;/i&gt;'),
('help_new_email', 'ucp', 1, 'You can change your email address which is used for administrator and member contact (if enabled). Your account may have to be reactivated if the administrator has specified so.&lt;br /&gt;&lt;br /&gt;Must be a valid email address.&lt;/i&gt;'),
('help_new_password', 'ucp', 1, 'The new password you want to use to login to your account.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: Minimum 5 characters.&lt;/i&gt;'),
('help_old_password', 'ucp', 1, 'Your current password that you enter when you wish to login to your account.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: Minimum 5 characters.&lt;/i&gt;'),
('help_passvo', 'ucp', 1, 'Type in the same password as you typed in above to verify consistency.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Optional.&lt;br /&gt;Length: 5-32 characters.&lt;/i&gt;'),
('help_password', 'ucp', 1, 'The password that you entered when you registered the account you typed in above.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Length: Minimum 5 characters.&lt;/i&gt;'),
('help_upload', 'ucp', 1, 'Uploads an avatar/photo from your computer to this website.&lt;br /&gt;&lt;br /&gt;&lt;i&gt;Must be an image locally hosted on your computer.&lt;br /&gt;Must follow guidelines listed above.&lt;/i&gt;'),
('information', 'ucp', 1, 'Information'),
('link_avatar', 'ucp', 1, 'Linked Avatar '),
('link_photo', 'ucp', 1, 'Linked Photo '),
('link_pick', 'ucp', 1, 'Link:'),
('long_date', 'ucp', 1, 'Long Date Format'),
('manage_folders', 'ucp', 1, 'Folder Management'),
('max_sig', 'ucp', 1, 'Maximum Signature Length: '),
('module_settings', 'ucp', 1, 'Module Settings'),
('move_to_folder', 'msg', 1, 'Move to folder:'),
('navigation', 'ucp', 1, 'Navigation'),
('new_email', 'ucp', 1, 'New Email:'),
('new_password', 'ucp', 1, 'New Password:'),
('new_vmail', 'ucp', 1, 'Verify New Email:'),
('new_vpassword', 'ucp', 1, 'Verify New Password:'),
('no_avatar', 'ucp', 1, 'No avatar has been selected'),
('no_photo', 'ucp', 1, 'No photo has been selected'),
('num_posts', 'ucp', 1, 'Posts in topics'),
('num_topics', 'ucp', 1, 'Topics in forums'),
('old_password', 'ucp', 1, 'Old Password:'),
('password', 'ucp', 1, 'Password:'),
('personalization', 'ucp', 1, 'Personalization'),
('photo', 'ucp', 1, 'Photo'),
('pic_dim', 'ucp', 1, 'Maximum Dimensions: <b><!-- WIDTH -->x<!-- HEIGHT --></b>.'),
('pic_exts', 'ucp', 1, 'Permitted Extensions: <b><!-- EXTS --></b>.'),
('pic_size', 'ucp', 1, 'Maximum File size: <b><!-- SIZE --></b>.'),
('pm_reply_prefix', 'msg', 1, 'RE: '),
('previewpost', 'ucp', 1, 'Preview private message'),
('profile', 'ucp', 1, 'Profile'),
('select_avatar', 'ucp', 1, 'Select Avatar'),
('select_photo', 'ucp', 1, 'Select Photo'),
('settings_prefix', 'ucp', 1, 'Settings: '),
('setting_forum', 'ucp', 1, 'Forum'),
('setting_global', 'ucp', 1, 'Global'),
('short_date', 'ucp', 1, 'Short Date Format'),
('signature', 'ucp', 1, 'Signature'),
('signatures', 'ucp', 1, 'Signatures'),
('signature_preview', 'ucp', 1, 'Signature Preview (not saved)'),
('sig_height', 'ucp', 1, '<br />If your signature exceeds <!-- HEIGHT -->px in height, the excess will be hidden from view.'),
('timezone', 'ucp', 1, 'Timezone'),
('time_-01', 'ucp', 1, '(GMT -1:00 hour) Azores, Cape Verde Islands'),
('time_-02', 'ucp', 1, '(GMT -2:00) Mid-Atlantic'),
('time_-03', 'ucp', 1, '(GMT -3:00) Brazil, Buenos Aires, Georgetown'),
('time_-03.5', 'ucp', 1, '(GMT -3:30) Newfoundland'),
('time_-04', 'ucp', 1, '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz'),
('time_-05', 'ucp', 1, '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima'),
('time_-06', 'ucp', 1, '(GMT -6:00) Central Time (US &amp; Canada), Mexico City'),
('time_-07', 'ucp', 1, '(GMT -7:00) Mountain Time (US &amp; Canada)'),
('time_-08', 'ucp', 1, '(GMT -8:00) Pacific Time (US &amp; Canada)'),
('time_-09', 'ucp', 1, '(GMT -9:00) Alaska'),
('time_-10', 'ucp', 1, '(GMT -10:00) Hawaii'),
('time_-11', 'ucp', 1, '(GMT -11:00) Midway Island, Samoa');
INSERT INTO `lat_local_lang` (`label`, `pg`, `lid`, `word`) VALUES
('time_-12', 'ucp', 1, '(GMT -12:00) Eniwetok, Kwajalein'),
('time_0', 'ucp', 1, '(GMT) Western Europe Time, London, Lisbon, Casablanca'),
('time_01', 'ucp', 1, '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris'),
('time_02', 'ucp', 1, '(GMT +2:00) Kaliningrad, South Africa'),
('time_03', 'ucp', 1, '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg'),
('time_03.5', 'ucp', 1, '(GMT +3:30) Tehran'),
('time_04', 'ucp', 1, '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi'),
('time_04.5', 'ucp', 1, '(GMT +4:30) Kabu'),
('time_05', 'ucp', 1, '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent'),
('time_05.5', 'ucp', 1, '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'),
('time_06', 'ucp', 1, '(GMT +6:00) Almaty, Dhaka, Colombo'),
('time_07', 'ucp', 1, '(GMT +7:00) Bangkok, Hanoi, Jakarta'),
('time_08', 'ucp', 1, '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'),
('time_09', 'ucp', 1, '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk'),
('time_09.5', 'ucp', 1, '(GMT +9:30) Adelaide, Darwin'),
('time_10', 'ucp', 1, '(GMT +10:00) Eastern Australia, Guam, Vladivostok'),
('time_11', 'ucp', 1, '(GMT +11:00) Magadan, Solomon Islands, New Caledonia'),
('time_12', 'ucp', 1, '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'),
('ucp', 'ucp', 1, 'User Control Panel'),
('update_avatar', 'ucp', 1, 'Update Avatar'),
('update_photo', 'ucp', 1, 'Update Photo'),
('upload_avatar', 'ucp', 1, 'Uploaded Avatar '),
('upload_photo', 'ucp', 1, 'Uploaded Photo '),
('upload_pick', 'ucp', 1, 'Upload:'),
('viewing_gallery', 'ucp', 1, 'Viewing Gallery: '),
('sticky', 'forum', 1, 'Sticky'),
('err_pm_flood', 'msg', 1, 'You have sent too many private messages in a short period of time. Please wait at least <!-- NUM --> seconds and try again.'),
('view_all', '', 1, 'View All'),
('one_spider', '', 1, '<b>1</b> spider'),
('img_limit', '', 1, '<br />Image Limit: <!-- NUM -->'),
('mda_limit', '', 1, '<br />Media Limit: <!-- NUM -->'),
('bbtag_help', '', 1, 'BBtag Help'),
('err_long_name', 'ucp', 1, 'The username is too long.'),
('err_name_invalid', 'ucp', 1, 'The name you have chosen is invalid.'),
('err_name_invalid', 'login', 1, 'The name you have chosen is invalid.'),
('err_no_name', 'ucp', 1, 'You did not fill in a username.'),
('err_taken_name', 'ucp', 1, 'Your desired username has already been taken.'),
('err_same_name', 'ucp', 1, 'Your desired username is exactly the same as your old one.'),
('change_name_info', 'ucp', 1, '<br />You have used <!-- NUM --> out of <!-- TOTAL --> name changes allocated to you in a <!-- DAYS --> day period.'),
('err_disabled', '', 1, 'This feature has been disabled.'),
('err_name_limit', 'ucp', 1, 'You have reached your allocated name changes for now. You''ll have to come back later.'),
('chr_limit', '', 1, '<br />Character Limit: <!-- NUM -->'),
('enlarge', '', 1, 'View full image'),
('bbtags', '', 1, 'BBtags'),
('autoparse', 'global', 1, 'Autoparse'),
('about_autoparse', 'global', 1, 'Links to images and media will be automatically embedded into your content that you are posting. If you do not wish for a link to autoparse, encase the link with the [url] bbtag if available.<br />\r\nImage extensions: <!-- IMAGE EXT -->.<br />\r\nMedia websites: <!-- MEDIA EXT -->.<br />\r\nImage limit: <!-- IMAGE LIMIT -->.<br />\r\nMedia limit: <!-- MEDIA LIMIT -->.'),
('bb_example', 'global', 1, 'Example'),
('bb_example_opt', 'global', 1, 'Option'),
('result', '', 1, 'Result'),
('act_name_changed', 'ucp', 1, 'Your name was successfully changed.'),
('name_changes', 'member', 1, 'Name Changes:'),
('view_history', 'member', 1, '(view history)'),
('name_history', 'member', 1, 'Name Change History'),
('time', '', 1, 'Time'),
('name_change', 'member', 1, 'Changed to'),
('registration', 'member', 1, 'Registration'),
('on_ucp-name', '', 1, 'Changing username'),
('on_msg-', '', 1, 'Viewing inbox'),
('on_msg-view', '', 1, 'Viewing a private message'),
('on_msg-edit', '', 1, 'Editing private message folders'),
('on_msg-new', '', 1, 'Composing a private message'),
('on_global-search', '', 1, 'Making a new search'),
('on_global-view_search', '', 1, 'Viewing search results'),
('resize_imgs', 'ucp', 1, 'Resize images'),
('about_resize_imgs', 'ucp', 1, 'Allow this website to automatically shrink images that are too large.'),
('yes', '', 1, 'Yes'),
('no', '', 1, 'No'),
('err_no_name_email', 'login', 1, 'No account was found with matching email and name details.'),
('hidden_suffix', '', 1, ' <i>(hidden)</i>'),
('deleted_suffix', '', 1, ' <i>(deleted)</i>'),
('no_posts', 'topic', 1, 'There are no posts in this topic.'),
('post_hidden', 'topic', 1, 'This post has been hidden <b><!-- TIME --></b> by <!-- USER -->.'),
('post_deleted', 'topic', 1, 'This post has been deleted <b><!-- TIME --></b> by <!-- USER -->.'),
('no_posts', 'forum', 1, 'No posts in this topic'),
('last_post_hidden', 'post', 1, 'This post is hidden.'),
('last_post_deleted', 'post', 1, 'This post is deleted.'),
('escalation_desc', '', 1, 'The page you are accessing requires you to verify your account password just for this session.<br />It is important that you verify that you are still on the same website.'),
('escalation', '', 1, 'Permission Escalation Required'),
('jump_to_forum', '', 1, 'Jump to forum:');

-- --------------------------------------------------------

--
-- Table structure for table `lat_local_skin`
--

CREATE TABLE IF NOT EXISTS `lat_local_skin` (
  `label` varchar(16) NOT NULL DEFAULT '',
  `pg` varchar(16) NOT NULL DEFAULT '',
  `sid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `skin` mediumtext,
  UNIQUE KEY `uniskin` (`label`,`pg`,`sid`),
  KEY `pg` (`pg`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_local_skin`
--

INSERT INTO `lat_local_skin` (`label`, `pg`, `sid`, `skin`) VALUES
('action', '', 1, '<div id="action" onmousedown="get_element(''action'').style.display = ''none''">\r\n	<div class="bdr">\r\n		<h1><a href="#"><img src="{$this->lat->image_url}close.png" alt="X" /></a>{$this->lat->lang[''action'']}</h1>\r\n		<div class="bdr2_action" style="padding: 15px 0px 15px 0px">\r\n			<div style="float: left; width: 40px; text-align: center;"><img src="{$this->lat->image_url}msg_action.png" alt="" /></div>\r\n			<div style="margin-left: 44px">\r\n				<i>{$this->lat->user[''act'']}</i>\r\n			</div>\r\n		</div>\r\n	</div>\r\n	<div class="clear"></div>\r\n</div>'),
('bb_button', '', 1, '<input type="button" tabindex="10" name="{$this->lat->cache[''bbtag''][$bb[''id'']][''bbcode'']}" onclick="bbInsert(''[{$this->lat->cache[''bbtag''][$bb[''id'']][''tag'']}]'', ''[/{$this->lat->cache[''bbtag''][$bb[''id'']][''tag'']}]'', ''{$this->lat->cache[''bbtag''][$bb[''id'']][''tag'']}'');" value="&nbsp;{$bname}&nbsp;" class="form_bb_button" accesskey="{$this->lat->cache[''bbtag''][$bb[''id'']][''hotkey'']}" />&nbsp;'),
('bb_dropdown', '', 1, '<select tabindex="10" class="form_bb_select" name="{$this->lat->cache[''bbtag''][$bb[''id'']][''tag'']}" onchange="optionInsert(this.options[this.selectedIndex].value, ''{$this->lat->cache[''bbtag''][$bb[''id'']][''tag'']}'')">{$opthtml}</select>&nbsp;'),
('browsing_list', '', 1, '<div class="bdr">\r\n	<h2>{$data[''on'']}</h2>\r\n	<div class="bdr2">\r\n		{$data[''off'']}\r\n	</div>\r\n</div>'),
('error', '', 1, '<div class="bdr">\r\n	<h1>{$this->lat->lang[''error'']}</h1>\r\n	<div class="bdr2_error">\r\n		<div style="float: left; width: 40px; text-align: center;"><img src="{$this->lat->image_url}msg_error.png" alt="" /></div>\r\n		<div style="margin-left: 44px">\r\n			{$this->lat->lang[''err_critical1'']}\r\n			<blockquote><i>{$error}</i></blockquote>\r\n			{$this->lat->lang[''err_critical2'']}\r\n		</div>\r\n	</div>\r\n</div>'),
('form_error', '', 1, '<div class="bdr">\r\n	<h1>{$this->lat->lang[''err_form_title'']}</h1>\r\n	<div class="bdr2_error">\r\n		<div style="float: left; width: 40px; text-align: center;"><img src="{$this->lat->image_url}msg_form.png" alt="" /></div>\r\n		<div style="margin-left: 44px">\r\n			{$this->lat->lang[''err_form'']}\r\n			<blockquote><i>{$form_errors_parsed}</i></blockquote>\r\n		</div>\r\n	</div>\r\n</div>\r\n<div class="clear"></div>\r\n'),
('guest_profile', '', 1, '		<div style="text-align: center; padding: 0px 0px 5px 0px;">\r\n			<b>{$data[''name'']}</b>\r\n		</div>\r\n		<div class="tiny_text" style="text-align: center">\r\n			{$this->lat->lang[''guest'']}\r\n		</div>'),
('layout', '', 1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html  xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->lat->lang[''xmllang'']}" lang="{$this->lat->lang[''xmllang'']}">\r\n<head>\r\n<title>{$this->lat->cache[''config''][''script_name'']} &gt; <!-- TITLE --></title>\r\n<!-- HEAD -->\r\n</head>\r\n<body class="body_padding">\r\n<!-- JS -->\r\n<a name="top"></a>\r\n<div class="bdr">\r\n	<div class="modules">\r\n		<div>\r\n			<ul><!-- MODULES --></ul>\r\n		</div>\r\n	</div>\r\n	<div class="logo">\r\n		<div>\r\n			<a href="{$this->lat->url}"><img src="{$this->lat->image_url}logo.png" alt="" /></a>\r\n			<div class="logo_text">\r\n				<a href="{$this->lat->url}">{$title}</a><br />\r\n				{$this->lat->cache[''config''][''logo_des'']}\r\n			</div>\r\n		</div>\r\n	</div>\r\n	<div class="user">\r\n		<!-- USER -->\r\n	</div><!-- NAVIGATION -->\r\n</div>\r\n<div class="clear"></div>\r\n<!-- DATA -->\r\n<div class="clear"></div>\r\n<div class="bdr">\r\n	<div class="foot">\r\n		<div class="foot_left">\r\n			{$select}<br />{$stats}\r\n		</div>\r\n		<div class="foot_right">\r\n			{$qsearch}\r\n			{$date}\r\n		</div>\r\n	</div>\r\n</div>\r\n<div class="copyright">\r\n	<!-- COPYRIGHT -->\r\n</div>\r\n<!-- EXTRA -->\r\n</body>\r\n</html>'),
('logged_in', '', 1, '<div class="user_left">{$this->lat->lang[''hello'']}<b><a href="{$this->lat->url}member={$this->lat->user[''id'']}">{$this->lat->user[''name'']}</a></b></div><div class="user_right"><ul><li class="first">{$cp}{$pms}</li><li><a href="{$this->lat->url}pg=ucp">{$this->lat->lang[''settings'']}</a></li><li><a href="{$this->lat->url}pg=login;do=logout;key={$this->lat->user[''key'']}">{$this->lat->lang[''logout'']}</a></li></ul></div>'),
('logged_out', '', 1, '<div class="user_left">{$this->lat->lang[''hello'']}{$this->lat->lang[''guest'']}</div><div class="user_right"><ul><li class="first"><a href="{$this->lat->url}pg=login">{$this->lat->lang[''login'']}</a></li><li><a href="{$this->lat->url}pg=login;do=register">{$this->lat->lang[''register'']}</a></li></ul></div>'),
('menu', '', 1, '<li{$first}><a href="{$menu_url}">{$this->lat->cache[''page''][$m][''menu'']}</a></li>'),
('topic_list_foot', 'forum', 1, '{$active_users}\r\n<div class="clear"></div>\r\n<div class="bdr_invisible">\r\n	<div class="bdr_float" style="float: left">\r\n		<div class="bdr2_full">\r\n			<div style="float: left; padding: 3px;" class="tiny_text">\r\n				<img src="{$this->lat->image_url}topic_unread.png" alt="" /> {$this->lat->lang[''unread_topic'']}<br />\r\n				<img src="{$this->lat->image_url}topic_unreadh.png" alt="" /> {$this->lat->lang[''unread_topic_hot'']}<br />\r\n				<img src="{$this->lat->image_url}topic_read.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=open">{$this->lat->lang[''read_topic'']}</a><br />\r\n				<img src="{$this->lat->image_url}topic_readh.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=hot">{$this->lat->lang[''read_topic_hot'']}</a><br />\r\n			</div>\r\n			<div style=" padding: 3px; float: left;" class="tiny_text">\r\n				<img src="{$this->lat->image_url}poll_unread.png" alt="" /> {$this->lat->lang[''unread_poll'']}<br />\r\n				<img src="{$this->lat->image_url}poll_unreadh.png" alt="" /> {$this->lat->lang[''unread_poll_hot'']}<br />\r\n				<img src="{$this->lat->image_url}poll_read.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=polls">{$this->lat->lang[''read_poll'']}</a><br />\r\n				<img src="{$this->lat->image_url}poll_readh.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=hot_polls">{$this->lat->lang[''read_poll_hot'']}</a>\r\n			</div>\r\n			<div style="padding: 3px; float: left;" class="tiny_text">\r\n				<img src="{$this->lat->image_url}topic_announce.png" alt="" /> {$this->lat->lang[''announcement'']}<br />\r\n				<img src="{$this->lat->image_url}topic_sticky.png" alt="" /> {$this->lat->lang[''sticky_topic'']}<br />\r\n				<img src="{$this->lat->image_url}topic_locked.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=locked">{$this->lat->lang[''locked_topic'']}</a><br />\r\n				<img src="{$this->lat->image_url}topic_moved.png" alt="" /> <a href="{$this->lat->url}forum={$this->lat->input[''id'']};state=moved">{$this->lat->lang[''moved_topic'']}</a>\r\n			</div>\r\n			<div style="clear: both; text-align: center;"><a href="{$this->lat->url}pg=forum;do=read_forum;id={$this->lat->input[''id'']};key={$this->lat->user[''key'']}">{$this->lat->lang[''mark_forum'']}</a> &middot; <a href="{$this->lat->url}forum={$this->lat->input[''id'']}">{$this->lat->lang[''view_all_topics'']}</a></div>\r\n		</div>\r\n	</div>{$moderate_html}\r\n	<div style="clear: both"></div>\r\n</div>'),
('nav', '', 1, '\r\n	<div class="navigation">\r\n		<div>\r\n			{$nav}\r\n		</div>\r\n	</div>'),
('pm', '', 1, '<div id="pm_popup" style="position: absolute; display: none; width: 250px;" class="bdr">\r\n	<h2 style="padding-right: 1px"><a href="javascript:toggle(''pm_popup'');"><img src="{$this->lat->image_url}close.png" alt="X" id="img_pm_popup" /></a>{$this->lat->lang[''new_pm'']}</h2>\r\n	<div class="bdr2">\r\n		<div style="float: left; width: 75px; text-align: center;">\r\n			<a href="{$this->lat->url}pm={$pm_fetch[''id'']}" target="_blank" onclick="toggle(''pm_popup'');"><img src="{$this->lat->image_url}pm.png" alt="pm" /></a>\r\n		</div>\r\n		<div style="margin-left: 75px">\r\n			<span class="tiny_text">\r\n				&middot; <a href="{$this->lat->url}pm={$pm_fetch[''id'']}">{$this->lat->lang[''view_this'']}</a><br />\r\n				&middot; <a href="{$this->lat->url}pm={$pm_fetch[''id'']}" target="_blank" onclick="toggle(''pm_popup'');">{$this->lat->lang[''view_new'']}</a><br />\r\n				&middot; <a href="{$this->lat->url}pg=msg">{$this->lat->lang[''view_inbox'']}</a>\r\n			</span>\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n</div>\r\n'),
('name', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_name" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''change_name'']}</h1>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_password'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''password'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" size="25" name="password" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_new_name'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''new_name'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" size="25" name="name" maxlength="25" value="{$this->lat->input[''name'']}" /><span class="tiny_text">{$name_change}</span>\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('posting_buttons', '', 1, '	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" name="submit" tabindex="4" /> <input type="submit" class="form_button" value="{$this->lat->lang[''preview'']}" name="preview" tabindex="4" /></h3>'),
('post_box', '', 1, '		<h2>{$this->lat->lang[''enter_msg'']}</h2>\r\n		<div class="bdr2">\r\n			<div class="left">\r\n{$smilies}\r\n				<div style="text-align: center; padding: 3px 0px;" class="tiny_text">\r\n					<b><a href="javascript:view_bbtags();">{$this->lat->lang[''bbtag_help'']}</a></b>{$post_footer}\r\n				</div>\r\n				<div style="text-align: center">\r\n					{$post_settings}\r\n				</div>\r\n			</div>\r\n			<div class="right">\r\n				{$buttons}<textarea class="form_text_content" rows="12" cols="60" name="data" tabindex="3">{$this->lat->input[''data'']}</textarea>{$post_extra}\r\n			</div>\r\n			<div class="clear" style="clear: both"></div>\r\n'),
('post_im', '', 1, ' <a href="{$this->lat->url}member={$im_id}" class="small_button"><img src="{$this->lat->image_url}im_{$im}.png" title="{$im}" alt="{$im}" /></a>'),
('post_table', '', 1, '<form action="{$this->lat->url}{$form}" method="post" name="post" id="post" enctype="multipart/form-data">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<script type="text/javascript"><!--\r\nfunction view_smilies(){\r\n	window.open("{$this->lat->url}pg=global&do=smilies","smilies","width=200,height=400,scrollbars=yes,resizable=yes");\r\n}\r\nfunction view_bbtags(){\r\n	window.open("{$this->lat->url}pg=global&do=bbtags&type={$post_type}","{$this->lat->lang[''bbtags'']}","width=600,height=400,scrollbars=yes,resizable=yes");\r\n}\r\n--></script>\r\n<div class="bdr">\r\n<h1>{$name}</h1>\r\n{$form_html}\r\n</div>\r\n</form>'),
('preview_post', '', 1, '	<h2>{$lang_post}</h2>\r\n	<div class="bdr2">\r\n		{$preview_post}\r\n	</div>\r\n'),
('qs', '', 1, '<form action="{$this->lat->url}pg=global;do=submit_search;quick=1" method="post">\r\n			<input type="text" class="quick" size="10" name="terms" value="{$this->lat->lang[''qs_text'']}" tabindex="100" onfocus="clear_qs(this);" />\r\n			<select name="quick_type" tabindex="100" class="quick">\r\n				{$qs_opt}\r\n			</select>\r\n			<input type="submit" class="quick_button" value="Go" name="submit" tabindex="100" />\r\n			</form>'),
('sig_show', '', 1, '<div class="signature_line"></div><div class="signature_text"{$sig_height}>{$sig}</div>'),
('site_statistics', '', 1, '<div class="clear"></div>\r\n<div class="bdr">\r\n	<h2><a href="{$this->lat->url}pg=member;do=online">{$this->lat->lang[''members_online'']}</a></h2>\r\n	<div class="bdr2">\r\n		<div style="float: left; width: 50px; text-align: center; padding: 5px 0px 0px 0px;"><a href="{$this->lat->url}pg=member;do=online"><img src="{$this->lat->image_url}online.png" alt="" /></a></div>\r\n		<div style="margin-left: 50px;">\r\n			{$this->lat->lang[''online_main'']}\r\n			<blockquote>{$active}</blockquote>\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n\r\n	<h2>{$this->lat->lang[''statistics'']}</h2>\r\n	<div class="bdr2">\r\n		<div style="float: left; width: 50px; text-align: center; padding: 5px 0px 0px 0px;"><a href="{$this->lat->url}pg=member;do=online"><img src="{$this->lat->image_url}stats.png" alt="" /></a></div>\r\n		<div style="margin-left: 50px;">\r\n			{$statistics}\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n</div>'),
('smilies_table', '', 1, '				<table cellpadding="0" cellspacing="0" align="center" class="smilie_bdr">\r\n					<tr>\r\n						<td class="tiny_text" colspan="{$this->lat->cache[''config''][''smilies_table'']}" align="center">\r\n							{$this->lat->lang[''clicksmilies'']}\r\n						</td>\r\n					</tr>\r\n					{$smilies_html}\r\n					<tr>\r\n						<td class="tiny_text" colspan="{$this->lat->cache[''config''][''smilies_table'']}" align="center">\r\n							<a href="javascript:view_smilies();">{$this->lat->lang[''view_all'']}</a>\r\n						</td>\r\n					</tr>\r\n				</table>'),
('sql', '', 1, '<div class="clear"></div>\r\n<div class="bdr">\r\n	<h1>{$div[0]}{$this->lat->lang[''sql_queries'']}</h1>\r\n	<div class="bdr2" id="debug" style="overflow: auto;{$div[1]}">\r\n		{$sql}\r\n	</div>\r\n</div>'),
('user_profile', '', 1, '		<div style="text-align: center; overflow: hidden;" class="content_overflow">\r\n			<b>{$data[''name'']}</b>\r\n		</div>\r\n		<center>{$data[''avatar'']}</center>\r\n		<div class="content_overflow">\r\n			<div class="user_title">\r\n				{$data[''user_title'']}\r\n			</div>\r\n			<div class="info">\r\n				{$this->lat->lang[''group'']} {$this->lat->cache[''group''][$data[''gid'']][''name'']}<br />\r\n				{$this->lat->lang[''joined'']} {$data[''registered'']}\r\n			</div>\r\n		</div>'),
('category_footer', 'forum', 1, '<div class="tiny_text" style="float: left;white-space: nowrap;padding-left: 4px;">\r\n	<a href="{$this->lat->url}pg=forum;do=read_board;key={$this->lat->user[''key'']}">{$this->lat->lang[''mark_board'']}</a> &middot; <a href="{$this->lat->url}pg=member;do=user_list;sort=posts">{$this->lat->lang[''top_posters'']}</a>\r\n</div>\r\n<div class="tiny_text" style="float: right;white-space: nowrap;padding-right: 4px;">\r\n	<img src="{$this->lat->image_url}forum_unread.png" alt="" /> {$this->lat->lang[''new_posts'']} &nbsp; &nbsp; &nbsp;\r\n	<img src="{$this->lat->image_url}forum_read.png" alt="" /> {$this->lat->lang[''no_new_posts'']} &nbsp; &nbsp; &nbsp;\r\n	<img src="{$this->lat->image_url}forum_link.png" alt="" /> {$this->lat->lang[''redirect'']}\r\n</div>\r\n<div style="clear: both"></div>'),
('category_forums', 'forum', 1, '<div class="bdr">\r\n	<h1>{$div[0]}{$category_name}</h1>\r\n	<div class="table_bdr" id="{$ftype}{$category}" style="{$div[1]}">\r\n		<table width="100%" cellpadding="0" cellspacing="0" border="0">\r\n{$forum_html}\r\n		</table>\r\n	</div>\r\n</div>\r\n<div class="clear"></div>'),
('forum', 'forum', 1, '		<tr>\r\n			<td class="cell_1_first">\r\n				<div style="float: left; width: 40px; text-align: center;">{$icon}</div>\r\n				<div style="margin-left: 44px;">\r\n					<span class="category_title"><a href=''{$this->lat->url}forum={$this->lat->cache[''forum''][$mainforum][''id'']}''>{$this->lat->cache[''forum''][$mainforum][''name'']}</a></span><br />\r\n					{$this->lat->cache[''forum''][$mainforum][''description'']}{$subforums}\r\n				</div>\r\n			</td>\r\n			<td class="cell_2" width="32%">\r\n				{$postinfo}\r\n			</td>\r\n			<td class="cell_1" width="100" align="center">\r\n				{$this->lat->lang[''number_replies'']} {$this->lat->cache[''forum''][$mainforum][''replies'']}<br />\r\n				{$this->lat->lang[''number_topics'']} {$this->lat->cache[''forum''][$mainforum][''topics'']}\r\n			</td>\r\n		</tr>'),
('forum_link', 'forum', 1, '\r\n		<tr>\r\n			<td class="cell_1_first" colspan="2">\r\n				<div style="float: left; width: 40px; text-align: center;">{$icon}</div>\r\n				<div style="margin-left: 44px;">\r\n					<span class="category_title"><a href=''{$this->lat->url}forum={$this->lat->cache[''forum''][$mainforum][''id'']}''>{$this->lat->cache[''forum''][$mainforum][''name'']}</span></a><br />\r\n					{$this->lat->cache[''forum''][$mainforum][''description'']}{$subforums}\r\n				</div>\r\n			</td>\r\n			<td class="cell_2" width="100" align="center">\r\n				{$this->lat->lang[''clicks'']} {$this->lat->cache[''forum''][$mainforum][''link_clicks'']}\r\n			</td>\r\n		</tr>'),
('forum_moderate', 'forum', 1, '	<div style="text-align: center">\r\n		<a href="javascript:check_on(''moderate'');"><img src="{$this->lat->image_url}select_all.png" alt="{$this->lat->lang[''select_all'']}" title="{$this->lat->lang[''select_all'']}" /></a>\r\n		<a href="javascript:check_off(''moderate'');"><img src="{$this->lat->image_url}select_none.png" alt="{$this->lat->lang[''unselect_all'']}" title="{$this->lat->lang[''unselect_all'']}" /></a>\r\n		<a href="javascript:check_invert(''moderate'');"><img src="{$this->lat->image_url}select_invert.png" alt="{$this->lat->lang[''invert_select'']}" title="{$this->lat->lang[''invert_select'']}" /></a>\r\n		<select name="mod" class="quick"><option value="" selected="selected">{$this->lat->lang[''selected_topics'']}</option>{$moderate}</select> <input type="submit" class="quick_button" value="{$this->lat->lang[''go'']}" />\r\n	</div>'),
('moderator_item', 'forum', 1, '	<h2>{$lang}</h2>\r\n	<div class="bdr2">\r\n		{$list}\r\n	</div>'),
('moderator_list', 'forum', 1, '	<div class="bdr_float" style="float: right; width: 40%;">\r\n		<h1>{$this->lat->lang[''moderators'']}</h1>{$group_mod}{$user_mod}\r\n	</div>'),
('no_topics', 'forum', 1, '<tr>\r\n        <td colspan="7" align="center" class="text">\r\n            {$this->lat->lang[''no_topics'']}\r\n        </td>\r\n    </tr>'),
('subforum', 'forum', 1, ' <a href="{$this->lat->url}forum={$subforum}">{$this->lat->cache[''forum''][$subforum][''name'']}</a>'),
('topic', 'forum', 1, '		<tr>\r\n			<td class="cell_1_first">\r\n				{$topic[''i'']}\r\n			</td>\r\n			<td class="cell_1">\r\n				{$topic[''icon'']}\r\n			</td>\r\n			<td class="cell_1">\r\n				{$topic[''mod'']}\r\n				<div style="overflow: hidden; float: left;">\r\n					{$topic[''unread'']}{$topic[''prefix'']}\r\n					<span class="text"><a href="{$this->lat->url}pg=topic;do=unread;id={$topic[''id'']}">{$topic[''title'']}</a></span>\r\n				</div>\r\n				{$topic[''pages'']}\r\n			</td>\r\n			<td class="cell_2" align="center">\r\n				{$topic[''author'']}\r\n			</td>\r\n			<td class="cell_1" align="center">\r\n				{$topic[''posts'']}\r\n			</td>\r\n			<td class="cell_2" align="center">\r\n				{$topic[''views'']}\r\n			</td>\r\n			<td class="cell_1">\r\n				<div style="overflow: hidden">{$topic[''last'']}</div>\r\n			</td>\r\n		</tr>\r\n'),
('topic_list', 'forum', 1, '<form action="{$this->lat->url}pg=topic;do=moderate;id={$this->lat->input[''id'']};key={$this->lat->user[''key'']};st={$this->lat->input[''st'']}{$order_url}" method="post" id="moderate">\r\n<div class="bdr_invisible" style="padding: 3px 0 1px 0">\r\n	{$buttons}{$pages}\r\n</div>\r\n<div class="clear" style="clear: both"></div>\r\n<div class="bdr">\r\n	<h1>{$this->lat->cache[''forum''][$this->lat->input[''id'']][''name'']}</h1>\r\n	<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">\r\n		<tr>\r\n			<th width="1%">\r\n				&nbsp;\r\n			</th>\r\n			<th width="1%">\r\n				&nbsp;\r\n			</th>\r\n			<th>\r\n				<div style="float:left"><a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''topic_title'']}{$sort_link_order[''st'']}">{$this->lat->lang[''topics'']}{$img[''topic_title'']}</a></div>\r\n				<div style="float:right"><a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''topic_date'']}{$sort_link_order[''st'']}">{$this->lat->lang[''topic_date'']}{$img[''topic_date'']}</a></div>\r\n			</th>\r\n			<th width="15%">\r\n				<a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''creator'']}{$sort_link_order[''st'']}">{$this->lat->lang[''creator'']}{$img[''creator'']}</a>\r\n			</th>\r\n			<th width="6%">\r\n				<a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''replies'']}{$sort_link_order[''st'']}">{$this->lat->lang[''replies'']}{$img[''replies'']}</a>\r\n			</th>\r\n			<th width="6%">\r\n				<a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''views'']}{$sort_link_order[''st'']}">{$this->lat->lang[''views'']}{$img[''views'']}</a>\r\n			</th>\r\n			<th width="22%">\r\n				<a href="{$this->lat->url}forum={$this->lat->input[''id'']}{$sort_link_order[''last_date'']}{$sort_link_order[''st'']}">{$this->lat->lang[''last_date'']}{$img[''last_date'']}</a>\r\n			</th>\r\n		</tr>\r\n{$topichtml}		</table>\r\n</div>\r\n<div class="clear"></div>\r\n<div class="bdr_invisible" style="padding: 3px 0 1px 0">\r\n	<div style="float: left">{$pages}</div><div style="float: right">{$buttons}</div>{$moderate}\r\n</div>\r\n<div class="clear" style="clear: both"></div>\r\n</form>'),
('smilies_pop', 'global', 1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->lat->lang[''xmllang'']}" lang="{$this->lat->lang[''xmllang'']}">\r\n<head>\r\n<title>{$this->lat->lang[''smilies'']}</title>\r\n<!-- HEAD -->\r\n</head>\r\n<body class="body_no_padding">\r\n<!-- JS -->\r\n<script type="text/javascript"><!--\r\nfunction smilieInsert(smilie)\r\n{\r\n	smilie = " " + smilie + " ";\r\n\r\n	if (opener.document.post.data.createTextRange)\r\n	{\r\n		opener.document.post.data.focus();\r\n		var sel = opener.document.selection;\r\n		var range = sel.createRange();\r\n		range.colapse;\r\n\r\n		range.text = smilie;\r\n		range.collapse(true);\r\n		range.moveEnd(''character'', -smilie.length);\r\n		range.moveStart(''character'', smilie.length);\r\n		range.select();\r\n	}\r\n	else if (opener.document.post.data.selectionStart >= 0)\r\n	{\r\n		var sel = opener.document.post.data.value.substr(opener.document.post.data.selectionStart, opener.document.post.data.selectionEnd - opener.document.post.data.selectionStart);\r\n		var selstart = opener.document.post.data.selectionStart;\r\n		var scrollp = opener.document.post.data.scrollTop;\r\n\r\n		opener.document.post.data.value = opener.document.post.data.value.substr(0, selstart) + sel + smilie + opener.document.post.data.value.substr(opener.document.post.data.selectionEnd);\r\n\r\n		if (opener.document.post.data.setSelectionRange)\r\n		{\r\n			opener.document.post.data.setSelectionRange(selstart + smilie.length, selstart + smilie.length);\r\n\r\n			opener.document.post.data.focus();\r\n		}\r\n\r\n		opener.document.post.data.scrollTop = scrollp;\r\n	}\r\n	else\r\n	{\r\n		opener.document.post.data.value += smilie;\r\n		opener.document.post.data.focus(opener.document.post.data.value.length - 1);\r\n	}\r\n}\r\n--></script>\r\n<h1>{$this->lat->lang[''smilies'']}</h1>\r\n<table cellpadding="0" cellspacing="0" class="table_bdr" width="100%">\r\n	<tr>\r\n		<th width="50%">\r\n			{$this->lat->lang[''text'']}\r\n		</th>\r\n		<th width="50%">\r\n			{$this->lat->lang[''smiley'']}\r\n		</th>\r\n	</tr>{$smilies}\r\n</table>\r\n</body>\r\n</html>'),
('smilies_pop_row', 'global', 1, '	<tr>\r\n		<td class="pop_left" style="text-align:center">\r\n			<a href="javascript:smilieInsert(''{$smilie[''txt'']}'');">{$smilie[''txt'']}</a>\r\n		</td>\r\n		<td class="pop_right" style="text-align:center">\r\n			<a href=''javascript:smilieInsert("{$smilie[''txt'']}");''><img src="{$this->lat->config[''FILES_PATH'']}smilies/{$smilie[''image'']}" alt="{$smilie[''txt'']}" /></a>\r\n		</td>\r\n	</tr>'),
('activate', 'login', 1, '<form action="{$this->lat->url}pg=login;do=submit_activate" method="post">\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''activate_account'']}</h1>\r\n	<h2>{$this->lat->lang[''activation_from_mail'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			{$this->lat->lang[''activation_id'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="id" maxlength="8" value="{$this->lat->input[''id'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''activation_code'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="code" maxlength="32" value="{$this->lat->input[''code'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit_activation'']}" /></h3>\r\n</div>\r\n</form>'),
('login', 'login', 1, '<form action="{$this->lat->url}pg=login;do=submit" method="post">\r\n<input type="hidden" name="refer" value="{$this->lat->input[''refer'']}" />\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''login'']}</h1>\r\n	<h2>{$this->lat->lang[''about_login'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_username'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''user'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="name" maxlength="25" value="{$this->lat->input[''name'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_password'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''pass'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" name="pass" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_remember_me'']}'', this)" onmouseout="unhelp()" class="help" />\r\n		</div>\r\n		<div class="right">\r\n			<div style="width: 350px">\r\n				<div style="float: left">\r\n					<label><input type="checkbox" name="remember" class="form_check" value="1"{$this->lat->input[''remember'']} /> {$this->lat->lang[''remember'']}</label>\r\n				</div>\r\n				<div style="float: right">\r\n					<a href=''{$this->lat->url}pg=login;do=recover''>{$this->lat->lang[''recover'']}</a>\r\n				</div>\r\n			</div>\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit_login'']}" /></h3>\r\n</div>\r\n</form>'),
('recover', 'login', 1, '<form action="{$this->lat->url}pg=login;do=submit_recover" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''recover'']}</h1>\r\n	<h2>{$this->lat->lang[''about_recover'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_username'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''user'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="name" maxlength="25" value="{$this->lat->input[''name'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_email_recover'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''email'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="email" maxlength="255" value="{$this->lat->input[''email'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		{$captcha_html}\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit_recover'']}" /></h3>\r\n</div>\r\n</form>'),
('register', 'login', 1, '<form action="{$this->lat->url}pg=login;do=submit_register" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''register'']}</h1>\r\n	<h2>{$this->lat->lang[''required'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_username_register'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''user'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="name" maxlength="25" value="{$this->lat->input[''name'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_email'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''email'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="email" maxlength="255" value="{$this->lat->input[''email'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_password_register'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''pass'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" name="pass" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_vpassword'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''vpassword'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" name="vpass" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''mail'']}\r\n		</div>\r\n		<div class="right">\r\n			<label><input type="checkbox" name="mmail" value="1" class="form_check"{$this->lat->input[''mmail'']} /> {$this->lat->lang[''mmail'']}</label><br />\r\n			<label><input type="checkbox" name="amail" value="1" class="form_check"{$this->lat->input[''amail'']} /> {$this->lat->lang[''amail'']}</label>\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''timezone_settings'']}\r\n		</div>\r\n		<div class="right">\r\n			<select name="timezone" class="form_select">\r\n				<option value="-12"{$this->lat->input[''timezone''][''-12'']}>{$this->lat->lang[''time_-12'']}</option>\r\n				<option value="-11"{$this->lat->input[''timezone''][''-11'']}>{$this->lat->lang[''time_-11'']}</option>\r\n				<option value="-10"{$this->lat->input[''timezone''][''-10'']}>{$this->lat->lang[''time_-10'']}</option>\r\n				<option value="-9"{$this->lat->input[''timezone''][''-9'']}>{$this->lat->lang[''time_-09'']}</option>\r\n				<option value="-8"{$this->lat->input[''timezone''][''-8'']}>{$this->lat->lang[''time_-08'']}</option>\r\n				<option value="-7"{$this->lat->input[''timezone''][''-7'']}>{$this->lat->lang[''time_-07'']}</option>\r\n				<option value="-6"{$this->lat->input[''timezone''][''-6'']}>{$this->lat->lang[''time_-06'']}</option>\r\n				<option value="-5"{$this->lat->input[''timezone''][''-5'']}>{$this->lat->lang[''time_-05'']}</option>\r\n				<option value="-4"{$this->lat->input[''timezone''][''-4'']}>{$this->lat->lang[''time_-04'']}</option>\r\n				<option value="-3.5"{$this->lat->input[''timezone''][''-3.5'']}>{$this->lat->lang[''time_-03.5'']}</option>\r\n				<option value="-3"{$this->lat->input[''timezone''][''-3'']}>{$this->lat->lang[''time_-03'']}</option>\r\n				<option value="-2"{$this->lat->input[''timezone''][''-2'']}>{$this->lat->lang[''time_-02'']}</option>\r\n				<option value="-1"{$this->lat->input[''timezone''][''-1'']}>{$this->lat->lang[''time_-01'']}</option>\r\n				<option value="0"{$this->lat->input[''timezone''][''0'']}>{$this->lat->lang[''time_0'']}</option>\r\n				<option value="1"{$this->lat->input[''timezone''][''1'']}>{$this->lat->lang[''time_01'']}</option>\r\n				<option value="2"{$this->lat->input[''timezone''][''2'']}>{$this->lat->lang[''time_02'']}</option>\r\n				<option value="3"{$this->lat->input[''timezone''][''3'']}>{$this->lat->lang[''time_03'']}</option>\r\n				<option value="3.5"{$this->lat->input[''timezone''][''3.5'']}>{$this->lat->lang[''time_03.5'']}</option>\r\n				<option value="4"{$this->lat->input[''timezone''][''4'']}>{$this->lat->lang[''time_04'']}</option>\r\n				<option value="4.5"{$this->lat->input[''timezone''][''4.5'']}>{$this->lat->lang[''time_04.5'']}</option>\r\n				<option value="5"{$this->lat->input[''timezone''][''5'']}>{$this->lat->lang[''time_05'']}</option>\r\n				<option value="5.5"{$this->lat->input[''timezone''][''5.5'']}>{$this->lat->lang[''time_05.5'']}</option>\r\n				<option value="6"{$this->lat->input[''timezone''][''6'']}>{$this->lat->lang[''time_06'']}</option>\r\n				<option value="7"{$this->lat->input[''timezone''][''7'']}>{$this->lat->lang[''time_07'']}</option>\r\n				<option value="8"{$this->lat->input[''timezone''][''8'']}>{$this->lat->lang[''time_08'']}</option>\r\n				<option value="9"{$this->lat->input[''timezone''][''9'']}>{$this->lat->lang[''time_09'']}</option>\r\n				<option value="9.5"{$this->lat->input[''timezone''][''9.5'']}>{$this->lat->lang[''time_09.5'']}</option>\r\n				<option value="10"{$this->lat->input[''timezone''][''10'']}>{$this->lat->lang[''time_10'']}</option>\r\n				<option value="11"{$this->lat->input[''timezone''][''11'']}>{$this->lat->lang[''time_11'']}</option>\r\n				<option value="12"{$this->lat->input[''timezone''][''12'']}>{$this->lat->lang[''time_12'']}</option>\r\n			</select><br />\r\n			<label><input type="checkbox" name="dst" value="1" class="form_check"{$this->lat->input[''dst'']} /> {$this->lat->lang[''obdst'']}</label>\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_birthday'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''birthday'']}\r\n		</div>\r\n		<div class="right">\r\n			<select name="bmonth" class="form_select">\r\n				<option value=""> </option>\r\n				<option value="1"{$this->lat->input[''bmonth''][''1'']}>{$this->lat->lang[''month_01'']}</option>\r\n				<option value="2"{$this->lat->input[''bmonth''][''2'']}>{$this->lat->lang[''month_02'']}</option>\r\n				<option value="3"{$this->lat->input[''bmonth''][''3'']}>{$this->lat->lang[''month_03'']}</option>\r\n				<option value="4"{$this->lat->input[''bmonth''][''4'']}>{$this->lat->lang[''month_04'']}</option>\r\n				<option value="5"{$this->lat->input[''bmonth''][''5'']}>{$this->lat->lang[''month_05'']}</option>\r\n				<option value="6"{$this->lat->input[''bmonth''][''6'']}>{$this->lat->lang[''month_06'']}</option>\r\n				<option value="7"{$this->lat->input[''bmonth''][''7'']}>{$this->lat->lang[''month_07'']}</option>\r\n				<option value="8"{$this->lat->input[''bmonth''][''8'']}>{$this->lat->lang[''month_08'']}</option>\r\n				<option value="9"{$this->lat->input[''bmonth''][''9'']}>{$this->lat->lang[''month_09'']}</option>\r\n				<option value="10"{$this->lat->input[''bmonth''][''10'']}>{$this->lat->lang[''month_10'']}</option>\r\n				<option value="11"{$this->lat->input[''bmonth''][''11'']}>{$this->lat->lang[''month_11'']}</option>\r\n				<option value="12"{$this->lat->input[''bmonth''][''12'']}>{$this->lat->lang[''month_12'']}</option>\r\n			</select>\r\n			<select name="bday" class="form_select">\r\n				<option value=""> </option>\r\n				<option value="1"{$this->lat->input[''bday''][''1'']}>1</option>\r\n				<option value="2"{$this->lat->input[''bday''][''2'']}>2</option>\r\n				<option value="3"{$this->lat->input[''bday''][''3'']}>3</option>\r\n				<option value="4"{$this->lat->input[''bday''][''4'']}>4</option>\r\n				<option value="5"{$this->lat->input[''bday''][''5'']}>5</option>\r\n				<option value="6"{$this->lat->input[''bday''][''6'']}>6</option>\r\n				<option value="7"{$this->lat->input[''bday''][''7'']}>7</option>\r\n				<option value="8"{$this->lat->input[''bday''][''8'']}>8</option>\r\n				<option value="9"{$this->lat->input[''bday''][''9'']}>9</option>\r\n				<option value="10"{$this->lat->input[''bday''][''10'']}>10</option>\r\n				<option value="11"{$this->lat->input[''bday''][''11'']}>11</option>\r\n				<option value="12"{$this->lat->input[''bday''][''12'']}>12</option>\r\n				<option value="13"{$this->lat->input[''bday''][''13'']}>13</option>\r\n				<option value="14"{$this->lat->input[''bday''][''14'']}>14</option>\r\n				<option value="15"{$this->lat->input[''bday''][''15'']}>15</option>\r\n				<option value="16"{$this->lat->input[''bday''][''16'']}>16</option>\r\n				<option value="17"{$this->lat->input[''bday''][''17'']}>17</option>\r\n				<option value="18"{$this->lat->input[''bday''][''18'']}>18</option>\r\n				<option value="19"{$this->lat->input[''bday''][''19'']}>19</option>\r\n				<option value="20"{$this->lat->input[''bday''][''20'']}>20</option>\r\n				<option value="21"{$this->lat->input[''bday''][''21'']}>21</option>\r\n				<option value="22"{$this->lat->input[''bday''][''22'']}>22</option>\r\n				<option value="23"{$this->lat->input[''bday''][''23'']}>23</option>\r\n				<option value="24"{$this->lat->input[''bday''][''24'']}>24</option>\r\n				<option value="25"{$this->lat->input[''bday''][''25'']}>25</option>\r\n				<option value="26"{$this->lat->input[''bday''][''26'']}>26</option>\r\n				<option value="27"{$this->lat->input[''bday''][''27'']}>27</option>\r\n				<option value="28"{$this->lat->input[''bday''][''28'']}>28</option>\r\n				<option value="29"{$this->lat->input[''bday''][''29'']}>29</option>\r\n				<option value="30"{$this->lat->input[''bday''][''30'']}>30</option>\r\n				<option value="31"{$this->lat->input[''bday''][''31'']}>31</option>\r\n			</select>\r\n			<select name="byear" class="form_select">\r\n				<option value=""> </option>\r\n				{$year}\r\n			</select>\r\n		</div>\r\n		<div class="clear"></div>\r\n		{$captcha_html}\r\n	</div>\r\n	<h2>{$this->lat->lang[''must_agree'']}</h2>\r\n	<div class="bdr2">\r\n		<center>\r\n			<div class="terms">{$this->lat->lang[''terms'']}</div>\r\n			<label><input type="checkbox" name="agree" value="1" class="form_check"{$this->lat->input[''agree'']} /> <b>{$this->lat->lang[''agree'']}</b></label>\r\n		</center>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit_registration'']}" /></h3>\r\n</div>\r\n</form>'),
('captcha_help', '', 1, '<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_captcha'']}'', this)" onmouseout="unhelp()" class="help" />'),
('captcha', '', 1, '		<div class="left">\r\n			{$captcha_help}{$this->lat->lang[''captcha'']}\r\n		</div>\r\n		<div class="right">\r\n			{$captcha}\r\n		</div>\r\n		<div class="clear"></div>'),
('reset_password', 'login', 1, '<form action="{$this->lat->url}pg=login;do=submit_activate" method="post">\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''activate_account'']}</h1>\r\n	<h2>{$this->lat->lang[''activation_from_mail'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			{$this->lat->lang[''activation_id'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="id" maxlength="8" value="{$this->lat->input[''id'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''activation_code'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="code" maxlength="32" value="{$this->lat->input[''code'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h2>{$this->lat->lang[''enter_new_password'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_password_register'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''pass'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" name="pass" maxlength="32" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_vpassword'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''vpassword'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" name="vpass" maxlength="32" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit_activation'']}" /></h3>\r\n</div>\r\n</form>'),
('im', 'member', 1, '<div><div class="icon_profile"><img src="{$this->lat->image_url}im_{$im}.png" alt="" /></div>{$pval}</div>'),
('online_list', 'member', 1, '{$pages}\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''online_list'']}</h1>\r\n	<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">\r\n		<tr>\r\n			<th width="30%">\r\n				{$this->lat->lang[''name'']}\r\n			</th>\r\n			<th width="20%">\r\n				{$this->lat->lang[''last_act'']}\r\n			</th>\r\n			<th width="50%">\r\n				{$this->lat->lang[''location'']}\r\n			</th>\r\n		</tr>{$session_rows}\r\n	</table>\r\n</div>\r\n{$pages}'),
('online_row', 'member', 1, '		<tr>\r\n			<td class="cell_1_first">\r\n				<span class="text">{$sfetch[''name'']}</span>\r\n			</td>\r\n			<td class="cell_2" align="center">\r\n				<span class="text">{$sfetch[''last_time'']}</span>\r\n			</td>\r\n			<td class="cell_1" align="center">\r\n				{$location}\r\n			</td>\r\n		</tr>'),
('other', 'member', 1, '	<h2>{$this->lat->lang[''other'']}</h2>\r\n	<div class="bdr2">\r\n		{$profile[''data'']}\r\n	</div>'),
('other_row', 'member', 1, '<div><b>{$this->lat->cache[''setting''][$pname][''title'']}:</b> {$pval}</div>'),
('profile', 'member', 1, '<script type="text/javascript"><!--\r\nfunction view_name(){\r\n	window.open("{$this->lat->url}pg=member&do=history&id={$this->lat->input[''id'']}","namehistory","width=300,height=400,scrollbars=yes,resizable=yes");\r\n}\r\n--></script>\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''viewing_profile'']}{$profile[''name'']}</h1>\r\n	<div class="bdr2">\r\n		<div style="float: left; width: 50%; overflow: hidden;">\r\n			<h4>{$profile[''name'']}</h4>\r\n			<div><span class="user_title">{$profile[''user_title'']}</span></div>\r\n			{$profile[''avatar'']}\r\n		</div>\r\n		<div style="float: left; width: 50%; overflow: hidden; text-align: right;">\r\n			{$profile[''photo'']}\r\n		</div>\r\n		<div style="clear: both" class="clear"></div>\r\n		<div style="float: left; width: 48%; overflow: hidden;">\r\n			<h2>{$this->lat->lang[''contact_details'']}</h2>\r\n			<div class="bdr2">\r\n				<div><div class="icon_profile"><img src="{$this->lat->image_url}send_pm.png" alt="" /></div><a href="{$this->lat->url}pg=msg;do=new;user={$profile[''id'']}">{$this->lat->lang[''send_pm'']}</a></div>{$profile[''im'']}\r\n			</div>\r\n		</div>\r\n		<div style="float: right; width: 48%; overflow: hidden;">\r\n			<h2>{$this->lat->lang[''user_statistics'']}</h2>\r\n			<div class="bdr2">\r\n				<b>{$this->lat->lang[''name_changes'']}</b> {$name_changes} {$name_history}<br />\r\n				<b>{$this->lat->lang[''joined_date'']}</b>{$profile[''registered'']}<br />\r\n				<b>{$this->lat->lang[''last_login'']}</b>{$profile[''last_login'']}{$content[''stats'']}\r\n			</div>\r\n		</div>\r\n		<div style="clear: both" class="clear"></div>\r\n		{$profile[''signature_cached'']}{$profile[''data'']}\r\n	</div>\r\n</div>'),
('search', 'global', 1, '<form action="{$this->lat->url}pg=global;do=submit_search;p={$this->lat->input[''p'']}" method="post" id="moderate">\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''search'']}</h1>\r\n	<div class="sub_header"><ul>{$search_pages}</ul></div>\r\n{$this->lat->output}\r\n</div>\r\n</form>'),
('sig', 'member', 1, '	<h2>{$this->lat->lang[''signature'']}</h2>\r\n	<div class="bdr2">\r\n		<span class="signaturetext">\r\n			{$profile[''signature_cached'']}\r\n		</span>\r\n	</div>\r\n	<div class="clear"></div>'),
('search_topic', 'global', 1, '	<div class="bdr2">\r\n		<div class="right" style="width: 40%">\r\n			{$this->lat->lang[''search_forums'']} &nbsp; &nbsp;\r\n			<a href="javascript:check_on(''moderate'');"><img src="{$this->lat->image_url}select_all.png" alt="+" title="{$this->lat->lang[''select_all'']}" /></a> <a href="javascript:check_off(''moderate'');"><img src="{$this->lat->image_url}select_none.png" alt="-" title="{$this->lat->lang[''unselect_all'']}" /></a> <a href="javascript:check_invert(''moderate'');"><img src="{$this->lat->image_url}select_invert.png" alt="-/+" title="{$this->lat->lang[''invert_select'']}" /></a>\r\n			<div class="clear"></div>\r\n			<div class="content_list">\r\n				{$dropdown}\r\n			</div>				\r\n		</div>\r\n		<div class="left" style="width: 19%">\r\n			{$this->lat->lang[''search_terms'']}\r\n		</div>\r\n		<div class="left" style="width: 39%">\r\n			<input type="text" class="form_text" size="30" name="terms" value="{$this->lat->input[''terms'']}" style="min-width: 275px; width: 80%;" />\r\n		</div>\r\n		<div style="clear: left; padding-top:3px;"></div>\r\n		<div class="left" style="width: 19%">\r\n			{$this->lat->lang[''search_user'']}\r\n		</div>\r\n		<div class="left" style="width: 39%">\r\n			<input type="text" class="form_text" size="30" name="usr" value="{$this->lat->input[''usr'']}" style="min-width: 275px; width: 80%;" />\r\n		</div>\r\n		<div style="clear: left; padding-top:3px;"></div>\r\n		<div class="left" style="width: 19%">\r\n			{$this->lat->lang[''order_results'']}\r\n		</div>\r\n		<div class="left" style="width: 39%">\r\n			<select name="odr" class="form_select" tabindex="2">\r\n				<option value="l"{$this->lat->input[''odr''][''l'']}>{$this->lat->lang[''last_post_date'']}</option>\r\n				<option value="v"{$this->lat->input[''odr''][''v'']}>{$this->lat->lang[''views'']}</option>\r\n				<option value="r"{$this->lat->input[''odr''][''r'']}>{$this->lat->lang[''replies'']}</option>\r\n			</select>\r\n		</div>\r\n		<div style="clear: left; padding-top:3px;"></div>\r\n		<div class="left" style="width: 19%">\r\n			{$this->lat->lang[''orientation'']}\r\n		</div>\r\n		<div class="left" style="width: 39%">\r\n			<div class="left" style="width: 120px"><label><input type="radio" name="ort" value="1"{$this->lat->input[''ort''][1]}/>{$this->lat->lang[''desc'']}</label></div>\r\n			<div class="left" style="width: 120px"><label><input type="radio" name="ort" value="2"{$this->lat->input[''ort''][2]}/>{$this->lat->lang[''asc'']}</label></div>\r\n		</div>\r\n		<div style="clear: left; padding-top:3px;"></div>\r\n		<div class="left" style="width: 19%">\r\n			{$this->lat->lang[''show_results_as'']}\r\n		</div>\r\n		<div class="left" style="width: 39%">\r\n			<div class="left" style="width: 120px"><label><input type="radio" name="sra" value="1"{$this->lat->input[''sra''][1]}/>{$this->lat->lang[''posts'']}</label></div>\r\n			<div class="left" style="width: 120px"><label><input type="radio" name="sra" value="2"{$this->lat->input[''sra''][2]}/>{$this->lat->lang[''topics'']}</label></div>\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n	<h3>\r\n		<input type="submit" class="form_button" value="{$this->lat->lang[''search'']}" name="submit" tabindex="3" />\r\n	</h3>'),
('folder_row', 'msg', 1, '		<div style="float: left">\r\n			<input type="hidden" name="name_{$folder_id}" value="{$fname}"><input type="text" class="form_text" size="35" name="change_{$folder_id}" maxlength="50" value="{$fname}" />\r\n		</div>\r\n		<div style="float: right">\r\n			{$action}\r\n		</div>\r\n		<div class="clear" style="clear: both"></div>'),
('folder_row_def', 'msg', 1, '\r\n		<div style="float: left">\r\n			{$this->lat->lang[$fname]}\r\n		</div>\r\n		<div style="float: right">\r\n			{$action}\r\n		</div>\r\n		<div class="clear"></div>'),
('manage_folder', 'msg', 1, '<form action="{$this->lat->url}pg=msg;do=action_folder" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''edit_folders'']}</h1v>\r\n	<h2>{$this->lat->lang[''edit_folders_info'']}</h2>\r\n	<div class="bdr2">{$main_pm_folders}{$pm_folders}\r\n		<br />\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_new_folder'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''make_folder'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="hidden" name="total_folders" value="{$folder_id}"><input type="text" class="form_text" name="new_folder" maxlength="50" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('no_pms', 'msg', 1, '\r\n		<tr>\r\n			<td colspan="3" align="center" class="cell_1_first">\r\n				{$this->lat->lang[''no_pms'']}\r\n			</td>\r\n		</tr>');
INSERT INTO `lat_local_skin` (`label`, `pg`, `sid`, `skin`) VALUES
('pm_folder_list', 'msg', 1, '<form id="pm" action="{$this->lat->url}pg=msg;do=pm_action;fd={$encoded_folder}" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$nav_name}</h1>\r\n	<div class="bdr2">\r\n		<div style="float: left;text-align: left;white-space: nowrap;">\r\n			{$pages}\r\n		</div>\r\n		{$pm_space}\r\n		<div style="clear: both"></div>\r\n	</div>\r\n	<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">\r\n		<tr>\r\n			<th width="45%">\r\n				{$this->lat->lang[''message'']}\r\n			</th>\r\n			<th width="30%">\r\n				{$this->lat->lang[''sent_by'']}\r\n			</th>\r\n			<th width="25%">\r\n				{$this->lat->lang[''date'']}\r\n			</th>\r\n		</tr>{$pm_html}\r\n	</table>\r\n	<div class="bdr2">\r\n		<div style="float: left;text-align: left;white-space: nowrap;">\r\n			{$pages}\r\n		</div>\r\n		<div style="float: right;text-align: right;white-space: nowrap;padding: 2px 5px 0px 0px;">\r\n			<a href="javascript:check_on(''pm'');"><img src="{$this->lat->image_url}select_all.png" alt="" title="{$this->lat->lang[''select_all'']}" /></a> \r\n			<a href="javascript:check_off(''pm'');"><img src="{$this->lat->image_url}select_none.png" alt="" title="{$this->lat->lang[''unselect_all'']}" /></a> \r\n			<a href="javascript:check_invert(''pm'');"><img src="{$this->lat->image_url}select_invert.png" alt="" title="{$this->lat->lang[''invert_select'']}" /></a> \r\n			<a href="javascript:check_selective(''pm'', 1);"><img src="{$this->lat->image_url}select_special.png" alt="" title="{$this->lat->lang[''select_read'']}" /></a> \r\n			<select name="pm_action" class="quick">\r\n				<option value="" selected="selected">{$this->lat->lang[''with_selected'']}</option>\r\n				{$folders}\r\n				<optgroup label="{$this->lat->lang[''take_action'']}">\r\n					<option value="delete" style="color: red">{$this->lat->lang[''delete'']}</option>\r\n				</optgroup>\r\n			</select>\r\n			<input type="submit" class="quick_button" value="{$this->lat->lang[''submit'']}" />\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n</div>\r\n</form>'),
('pm_row', 'msg', 1, '		<tr>\r\n			<td class="cell_1_first" width="45%">\r\n				<input type="checkbox" name="message_{$pm[''id'']}" value="{$pm[''unread'']}" /> <a href="{$this->lat->url}pm={$pm[''id'']}">{$pm[''title'']}</a>\r\n			</td>\r\n			<td class="cell_2" width="30%">\r\n				{$pm[''name'']}\r\n			</td>\r\n			<td class="cell_1" width="25%">\r\n				{$pm[''sent_date'']}\r\n			</td>\r\n		</tr>'),
('pm_table', 'msg', 1, '<form action="{$this->lat->url}pg=msg;do=submit_pm" method="post" name="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<script type="text/javascript"><!--\r\nfunction view_smilies(){\r\n	window.open("{$this->lat->url}pg=global&do=smilies","Smilies","width=200,height=400,scrollbars=yes,resizable=yes");\r\n}\r\n--></script>\r\n<div class="bdr">\r\n<h1>{$this->lat->lang[''making_new_pm'']}</h1>\r\n<h2>{$this->lat->lang[''sending_details'']}</h2>\r\n<div class="bdr2">\r\n	<div class="left">\r\n		<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_to'']}'', this)" onmouseout="unhelp()" class="help" />\r\n		{$this->lat->lang[''to'']}\r\n	</div>\r\n	<div class="altright">\r\n		<input type="text" class="form_text" size="35" name="to" maxlength="25" value="{$this->lat->input[''to'']}" tabindex="1" />\r\n	</div>\r\n	<div class="clear"></div>\r\n</div>\r\n<h2>{$this->lat->lang[''message_details'']}</h2>\r\n<div class="bdr2">\r\n	<div class="left">\r\n		<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_subject'']}'', this)" onmouseout="unhelp()" class="help" />\r\n		{$this->lat->lang[''subject'']}\r\n	</div>\r\n	<div class="right">\r\n		<input type="text" class="form_text" size="35" name="subject" maxlength="50" value="{$this->lat->input[''subject'']}" tabindex="2" />\r\n	</div>\r\n	<div class="clear"></div>\r\n</div>\r\n{$form_html}\r\n</div>\r\n</form>'),
('show_pm', 'msg', 1, '<div class="bdr">\r\n	<h1>{$post[''title'']}</h1>\r\n	<h2><u>{$this->lat->lang[''sent_on'']}</u> {$post[''sent_date'']}</h2>\r\n	<div class="bdr2" style="clear: both; min-height: 100px;">\r\n		<div style="float: left; width: 180px; height: 100%;margin:0;">\r\n{$profile}\r\n		</div>\r\n		<div style="margin-left:180px;">\r\n			<div class="content_overflow">\r\n				{$post[''data_cached'']}{$post[''signature_cached'']}\r\n			</div>\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n	<div class="footer">\r\n		<div style="float: left">{$post[''profile_buttons'']}</div>\r\n		<div style="float: right"><a href="{$this->lat->url}pg=msg;do=new;pm={$post[''id'']}" class="small_button"><span>{$this->lat->lang[''reply_to_pm'']}</span></a></div>\r\n		<div class="clear" style="clear:both"></div>\r\n	</div>\r\n</div>'),
('add_poll', 'post', 1, '		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_poll'']}'', this)" onmouseout="unhelp()" class="help" />\r\n		</div>\r\n		<div class="right">\r\n			<input type="submit" class="form_button" value="{$this->lat->lang[''add_poll'']}" name="addpoll" tabindex="2" />\r\n		</div>\r\n		<div class="clear"></div>\r\n'),
('last_posts_row', 'post', 1, ' <div class="sub_header">  <div style="float: left">  {$post[''name'']} </div>  <div style="float: right">  {$post[''poster_time'']} </div>  </div>  <div class="bdr2">  <div class="content_overflow" style="max-height: 200px">  {$post[''data_cached'']} </div>  </div>'),
('last_posts_table', 'post', 1, '<div class="clear"></div>\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''lastpoststitle'']}</h1>{$post_html}\r\n</div>'),
('new_topic', 'post', 1, '	<h2>{$this->lat->lang[''topicdetails'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_topic_title'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''title'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" name="title" maxlength="50" value="{$this->lat->input[''title'']}" tabindex="1" /><input type="hidden" name="poll_number" value="{$this->lat->input[''poll_number'']}" />\r\n		</div>\r\n		<div class="clear"></div>{$addpoll}\r\n	</div>\r\n'),
('poll_make', 'post', 1, '	<h2>{$pollnum}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_pquestion'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''pollquestion'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" size="50" name="pq[{$i}]" maxlength="50" value="{$this->lat->input[''pq''][$i]}" tabindex="2" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_ptype'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''polltype'']}\r\n		</div>\r\n		<div class="right">\r\n			<select name="pt[{$i}]" tabindex="2" class="form_select"><option value="0">{$this->lat->lang[''pollradio'']}</option><option value="1"{$this->lat->input[''pt''][$i][1]}>{$this->lat->lang[''pollcheck'']}</option></select>\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_poptions'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''polloptions'']}\r\n		</div>\r\n		<div class="right">\r\n			<textarea class="form_text" rows="5" cols="50" name="po[{$i}]" tabindex="2">{$this->lat->input[''po''][$i]}</textarea>\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n'),
('topic_mod', 'post', 1, '	</div>\r\n	<h2>{$this->lat->lang[''moderator_options'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			{$this->lat->lang[''after_posting'']}\r\n		</div>\r\n		<div class="right">\r\n			{$mod_options}\r\n		</div>\r\n		<div class="clear"></div>\r\n'),
('topic_settings', 'post', 1, '	</div>\r\n	<h2>{$this->lat->lang[''topic_settings'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			{$this->lat->lang[''topic_icon'']}\r\n		</div>\r\n		<div class="right">\r\n			{$icons}\r\n		</div>\r\n		<div class="clear"></div>\r\n'),
('announce_topics', 'topic', 1, '<form action="{$this->lat->url}pg=topic;do=moderate;mod=tannounce_submit" method="post" id="moderate">\r\n<input type="hidden" name="item" value="{$this->lat->input[''item'']}" />\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<input type="hidden" name="st" value="{$this->lat->input[''st'']}" />\r\n<input type="hidden" name="tid" value="{$this->lat->input[''tid'']}" />\r\n<input type="hidden" name="id" value="{$this->lat->input[''id'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''announce_topic'']}</h1>\r\n	<h2>{$this->lat->lang[''about_announce_topic'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_target_forums'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''target_forums'']}\r\n		</div>\r\n		<div class="right">\r\n			<a href="javascript:check_on(''moderate'');"><img src="{$this->lat->image_url}select_all.png" alt="+" title="{$this->lat->lang[''select_all'']}" /></a> <a href="javascript:check_off(''moderate'');"><img src="{$this->lat->image_url}select_none.png" alt="-" title="{$this->lat->lang[''unselect_all'']}" /></a> <a href="javascript:check_invert(''moderate'');"><img src="{$this->lat->image_url}select_invert.png" alt="-/+" title="{$this->lat->lang[''invert_select'']}" /></a>\r\n			<div style="padding-top: 3px"></div>\r\n			{$dropdown}\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('delete_topics', 'topic', 1, '<form action="{$this->lat->url}pg=topic;do=moderate;mod={$pg}" method="post">\r\n<input type="hidden" name="item" value="{$this->lat->input[''item'']}" />\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<input type="hidden" name="st" value="{$this->lat->input[''st'']}" />\r\n<input type="hidden" name="tid" value="{$this->lat->input[''tid'']}" />\r\n<input type="hidden" name="id" value="{$this->lat->input[''id'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->title}</h1>\r\n	<div class="bdr2" style="text-align: center">\r\n		{$msg}\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''delete'']}" /></h3>\r\n</div>\r\n</form>'),
('move_topics', 'topic', 1, '<form action="{$this->lat->url}pg=topic;do=moderate;mod=tmove_submit" method="post">\r\n<input type="hidden" name="item" value="{$this->lat->input[''item'']}" />\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<input type="hidden" name="st" value="{$this->lat->input[''st'']}" />\r\n<input type="hidden" name="tid" value="{$this->lat->input[''tid'']}" />\r\n<input type="hidden" name="id" value="{$this->lat->input[''id'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''move_topics'']}</h1>\r\n	<h2>{$this->lat->lang[''about_move_topics'']}</h2>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_destination_forum'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''destination_forum'']}\r\n		</div>\r\n		<div class="right">\r\n			{$dropdown}\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_leave_link'']}'', this)" onmouseout="unhelp()" class="help" />\r\n		</div>\r\n		<div class="right">\r\n			<label><input type="checkbox" name="link" value="1" checked="checked" /> {$this->lat->lang[''leave_link'']}</label>\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('poll_a', 'topic', 1, '		<h2>{$poll[''question'']}</h2>\r\n		<table width="100%" cellpadding="0" cellspacing="0" class="table_bdr">\r\n{$poll[''data'']}\r\n		</table>\r\n'),
('poll_buttons', 'topic', 1, '		<h3><input type="submit" class="form_button" value="{$this->lat->lang[''vote'']}" name="vote" /> <input type="submit" class="form_button" value="{$this->lat->lang[''showresults'']}" name="show" /></h3>\r\n'),
('poll_cell', 'topic', 1, '	<tr>\r\n		<td class="cell_2_first">\r\n			{$opt[0]} &nbsp; \r\n		</td>\r\n		<td width="50%" class="cell_1" style="padding: 1px 3px 1px 1px">\r\n			{$bar}\r\n		</td>\r\n		<td width="125" align="right" class="cell_2">\r\n			<span class="tiny_text">{$lstat}</span>\r\n		</td>\r\n	</tr>'),
('poll_q', 'topic', 1, '	<h2>{$poll[''question'']}</h2>\r\n	<div class="bdr2">\r\n		{$poll[''data'']}\r\n	</div>\r\n'),
('poll_total', 'topic', 1, '	<h3>{$this->lat->lang[''total_votes'']}</h3>\r\n'),
('post', 'topic', 1, '<a name="{$post[''id'']}"></a>\r\n<div class="bdr">\r\n	<div class="sub_header">\r\n		<div style="float: left">\r\n			<i>{$this->lat->lang[''posted'']}</i> {$post[''poster_time'']}{$post[''ip'']}\r\n		</div>\r\n		<div style="float: right">\r\n			{$post[''mod'']}&nbsp;<a onclick="prompt(''{$this->lat->lang[''post_popup'']}'', ''{$this->lat->url}post={$post[''id'']}'');">{$post[''num'']}</a>\r\n		</div>\r\n	</div>\r\n	<div class="bdr2" style="clear: both; min-height: 100px;">\r\n		<div style="float: left; width: 180px; height: 100%;margin:0;">\r\n{$profile}\r\n		</div>\r\n		<div style="margin-left:180px;">\r\n			<div class="content_overflow">\r\n				{$post[''data_cached'']}\r\n			</div>\r\n			{$post[''signature_cached'']}\r\n		</div>\r\n		<div style="clear: both"></div>\r\n	</div>\r\n	<div class="footer">\r\n		<div style="float: left">{$post[''profile_buttons'']}</div>\r\n		<div style="float: right">{$post[''buttons'']}</div>\r\n		<div class="clear" style="clear:both"></div>\r\n	</div>\r\n</div>\r\n<div class="clear"></div>'),
('quick_reply', 'topic', 1, '<form action="{$this->lat->url}pg=post;do=submit_reply;id={$topic[''id'']}" method="post" name="post">\r\n<input type="hidden" name="show_smi" value="1" />\r\n<input type="hidden" name="show_sig" value="1" />\r\n<input type="hidden" name="quick_reply" value="1" />\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div style="display:none" id="qr">\r\n<div class="clear"></div>\r\n	<div class="bdr">\r\n		<h1>{$this->lat->lang[''qr'']}</h1>\r\n		<div class="bdr2" style="text-align: center">\r\n			<textarea class="form_text_content" rows="8" cols="60" name="data"></textarea>\r\n		</div>\r\n		<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" name="submit" /> <input type="submit" class="form_button" value="{$this->lat->lang[''moreoptions'']}" name="preview" /></h3>\r\n	</div>\r\n</div>\r\n</form>'),
('topic_view', 'topic', 1, '<form action="{$this->lat->url}pg=topic;do=moderate;tid={$this->lat->input[''id'']};id={$topic[''fid'']};key={$this->lat->user[''key'']}" method="post" id="moderate">\r\n<div class="bdr">\r\n	<h1>{$topic[''title'']}</h1>\r\n	<div class="bdr2">\r\n		{$buttons}{$pages}\r\n		<div style="clear: both"></div>\r\n	</div>{$polls}\r\n</div>\r\n<div class="clear"></div>{$posthtml}\r\n<div class="bdr">\r\n	<div class="bdr2_full">\r\n		{$buttons}{$qr}{$pages}\r\n		<div style="clear: both"></div>{$moderator[''option'']}\r\n	</div>\r\n</div>\r\n</form><a name="qra"></a>{$qr_html}\r\n<div class="clear"></div>{$active_users}'),
('avatar', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_avatar" method="post" enctype="multipart/form-data">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''avatar'']}</h1>\r\n	<h2>{$this->lat->lang[''information'']}</h2>\r\n	<div class="bdr2">\r\n		<div style="float: right; text-align: center;">\r\n			{$avatar_display}\r\n		</div>\r\n		{$this->lat->lang[''pic_dim'']}<br />{$this->lat->lang[''pic_exts'']}<br />{$this->lat->lang[''pic_size'']}\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h2>{$this->lat->lang[''select_avatar'']}</h2>\r\n	<div class="bdr2">\r\n		{$change_html}\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''update_avatar'']}" /> <input type="submit" class="form_button" name="no_avatar" value="{$this->lat->lang[''delete_avatar'']}" /></h3>\r\n	</div>\r\n</div>\r\n</form>'),
('avatar_gallery', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_avatar;gallery={$this->lat->input[''gallery'']}" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''viewing_gallery'']}{$this->lat->input[''gallery'']}</h1>\r\n	<div class="bdr2">\r\n		<table border="0" cellpadding="0" cellspacing="0" width="100%">\r\n			{$gallery}\r\n		</table>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''update_avatar'']}" /></h3>\r\n</div>\r\n</form>'),
('change_email', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_email" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''change_email'']}</h1>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_password'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''password'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" size="25" name="password" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_new_email'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''new_email'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" size="25" name="new_email" maxlength="255" value="{$this->lat->input[''new_email'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''new_vmail'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="text" class="form_text" size="25" name="new_vemail" maxlength="255" value="{$this->lat->input[''new_vemail'']}" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('change_password', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_passwd" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''change_password'']}</h1>\r\n	<div class="bdr2">\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_old_password'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''old_password'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" size="25" name="old_password" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			<img src="{$this->lat->image_url}help.png" alt="" onmouseover="help(''{$this->lat->lang[''help_new_password'']}'', this)" onmouseout="unhelp()" class="help" />\r\n			{$this->lat->lang[''new_password'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" size="25" name="new_password" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n		<div class="left">\r\n			{$this->lat->lang[''new_vpassword'']}\r\n		</div>\r\n		<div class="right">\r\n			<input type="password" class="form_text" size="25" name="new_vpassword" value="" />\r\n		</div>\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('config', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_config;sc={$this->lat->input[''sc'']}" method="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''settings_prefix'']}{$this->lat->cache[''setting_page''][$this->lat->input[''sc'']][''title'']}</h1>\r\n	<h2>{$this->lat->lang[''fields_required'']}</h2>\r\n	<div class="bdr2">{$config_html}\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('config_field', 'ucp', 1, '		<div class="left">\r\n			{$settings[''about'']}{$settings[''title'']}:\r\n		</div>\r\n		<div class="right">\r\n			{$settings[''content'']}\r\n		</div>\r\n		<div class="clear"></div>'),
('main_page', 'ucp', 1, '<div class="bdr">\r\n	<h1>{$this->lat->lang[''settings'']}</h1>\r\n	<h2>{$this->lat->lang[''personalization'']}</h2>\r\n	<div class="bdr2">\r\n		{$this->menu[0]}\r\n		<div class="clear" style="clear: both"></div>\r\n	</div>\r\n	<h2>{$this->lat->lang[''module_settings'']}</h2>\r\n	<div class="bdr2">\r\n		{$this->menu[1]}\r\n		<div class="clear" style="clear: both"></div>\r\n	</div>\r\n	<h2>{$this->lat->lang[''account_configuration'']}</h2>\r\n	<div class="bdr2">\r\n		{$this->menu[2]}\r\n		<div class="clear" style="clear: both"></div>\r\n	</div>\r\n</div>'),
('bbtags_pop_row', 'global', 1, '\r\n	<tr>\r\n		<td class="pop_left" width="50%">\r\n			{$example}\r\n		</td>\r\n		<td class="pop_right" width="50%">\r\n			{$result}\r\n		</td>\r\n	</tr>'),
('main_page_link', 'ucp', 1, '		<div style="float: {$float}; width: 50%;"><h4><a href="{$this->lat->url}{$link}">{$name}</a></h4><span class="tiny_text">{$desc}</span></div>'),
('photo', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_photo" method="post" enctype="multipart/form-data">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''photo'']}</h1>\r\n	<h2>{$this->lat->lang[''information'']}</h2>\r\n	<div class="bdr2">\r\n		<div style="float: right; text-align: center;">\r\n			{$photo_display}\r\n		</div>\r\n		{$this->lat->lang[''pic_dim'']}<br />{$this->lat->lang[''pic_exts'']}<br />{$this->lat->lang[''pic_size'']}\r\n		<div class="clear"></div>\r\n	</div>\r\n	<h2>{$this->lat->lang[''select_photo'']}</h2>\r\n	<div class="bdr2">\r\n		{$change_html}\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''update_photo'']}" /> <input type="submit" class="form_button" name="no_photo" value="{$this->lat->lang[''delete_photo'']}" /></h3>\r\n	</div>\r\n</div>\r\n</form>'),
('sig_box', 'ucp', 1, '<form action="{$this->lat->url}pg=ucp;do=submit_signature" method="post" name="post">\r\n<input type="hidden" name="key" value="{$this->lat->user[''key'']}" />\r\n<div class="bdr">\r\n	<h1><span class="headtext">{$this->lat->lang[''editing_sig'']}</span></h1>{$current}\r\n	{$form_html}\r\n{$buttons_submit}\r\n	</div>\r\n</div>\r\n</form>'),
('sig_preview', 'ucp', 1, '	<h2>{$sig_title}</h2>\r\n	<div class="bdr2">\r\n		{$sig[''signature_cached'']}\r\n	</div>'),
('bbtags_pop', 'global', 1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->lat->lang[''xmllang'']}" lang="{$this->lat->lang[''xmllang'']}">\r\n<head>\r\n<title>{$this->lat->lang[''bbtags'']}</title>\r\n<!-- HEAD -->\r\n</head>\r\n<body class="body_no_padding">\r\n<!-- JS -->\r\n<h1>{$this->lat->lang[''bbtags'']}</h1>\r\n<table cellpadding="0" cellspacing="0" class="table_bdr" width="100%">\r\n	<tr>\r\n		<td class="row_head" colspan="2">\r\n			{$this->lat->lang[''autoparse'']}\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td class="pop_right" colspan="2">\r\n			<span class="tiny_text">{$this->lat->lang[''about_autoparse'']}</span>\r\n		</td>\r\n	</tr>\r\n	<tr>\r\n		<td class="row_head" width="50%">\r\n			{$this->lat->lang[''text'']}\r\n		</td>\r\n		<td class="row_head" width="50%">\r\n			{$this->lat->lang[''result'']}\r\n		</td>\r\n	</tr>{$bbtags}\r\n</table>\r\n</body>\r\n</html>'),
('name_history', 'member', 1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->lat->lang[''xmllang'']}" lang="{$this->lat->lang[''xmllang'']}">\r\n<head>\r\n<title>{$this->lat->lang[''name_history'']}</title>\r\n<!-- HEAD -->\r\n</head>\r\n<body class="body_no_padding">\r\n<!-- JS -->\r\n<h1>{$this->lat->lang[''name_history'']}</h1>\r\n<table cellpadding="0" cellspacing="0" class="table_bdr" width="100%">\r\n	<tr>\r\n		<th width="50%">\r\n			{$this->lat->lang[''name_change'']}\r\n		</th>\r\n		<th width="50%">\r\n			{$this->lat->lang[''time'']}\r\n		</th>\r\n	</tr>{$name}\r\n</table>\r\n</body>\r\n</html>'),
('name_history_row', 'member', 1, '\r\n	<tr>\r\n		<td class="pop_left" style="text-align:center">\r\n			{$nh[''name'']}\r\n		</td>\r\n		<td class="pop_right" style="text-align:center">\r\n			{$time}\r\n		</td>\r\n	</tr>'),
('no_content', '', 1, '<div class="bdr">\r\n	<div class="bdr2" style="clear: both; text-align: center;">\r\n		&nbsp;<br />{$msg}<br />&nbsp;\r\n	</div>\r\n</div>\r\n<div class="clear"></div>'),
('escalation', '', 1, '<form name="login" action="{$this->lat->url}{$page}" method="post">\r\n<div class="bdr">\r\n	<h1>{$this->lat->lang[''escalation'']}</h1>\r\n	<div class="bdr2" style="text-align: center">\r\n		{$this->lat->lang[''escalation_desc'']}\r\n		<br />&nbsp;<br /><input type="password" class="form_text" name="pass" value="" />\r\n	<div class="clear"></div>\r\n	</div>\r\n	<h3><input type="submit" class="form_button" value="{$this->lat->lang[''submit'']}" /></h3>\r\n</div>\r\n</form>'),
('captcha_latova', '', 1, '<div class="captcha" style="width:250px;height:50px;"><img src="{$this->lat->url}pg=global;do=img" alt="" border="0" /></div><input type="text" class="form_text" name="captcha" maxlength="5" value="" style="width:70px" />'),
('captcha_nogd', '', 1, '<div class="captcha" style="width:125px;\r\nheight:40px;"><img src="{$this->lat->url}pg=global;do=img;n=1" alt="" border="0" /><img src="{$this->lat->url}pg=global;do=img;n=2" alt="" border="0" /><img src="{$this->lat->url}pg=global;do=img;n=3" alt="" border="0" /><img src="{$this->lat->url}pg=global;do=img;n=4" alt="" border="0" /><img src="{$this->lat->url}pg=global;do=img;n=5" alt="" border="0" /></div><input type="text" class="form_text" name="captcha" maxlength="5" value="" style="width:70px" />'),
('footer_nav', '', 1, '\r\n<div style="padding-top: 3px; padding-right: 1px; float: right;" class="text">\r\n	{$nav}\r\n</div>\r\n<div style="padding-top: 5px; float: right;" class="text">\r\n	{$nav_lang} &nbsp;\r\n</div>\r\n'),
('embed', '', 1, '<div id="embed_{$embed_hash}" class="embed_div">\r\n	<span  class="embed_text">Embedded content from {$ap[''site'']}</span><br />\r\n	<a href="javascript:unhide(''embed_{$embed_hash}'');">View it here</a> / <a href="{$data[''url'']}" target="_blank">View in new window</a>\r\n</div>\r\n<div id="embed_{$embed_hash}_hidden" class="embed_content" style="display: none">\r\n	{$out}\r\n</div>');

-- --------------------------------------------------------

--
-- Table structure for table `lat_poll`
--

CREATE TABLE IF NOT EXISTS `lat_poll` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `question` varchar(250) NOT NULL DEFAULT '',
  `options` text,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_poll`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_poll_vote`
--

CREATE TABLE IF NOT EXISTS `lat_poll_vote` (
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `opt` tinyint(2) unsigned NOT NULL DEFAULT '0',
  KEY `tid` (`tid`,`pid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_poll_vote`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_setting`
--

CREATE TABLE IF NOT EXISTS `lat_setting` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL DEFAULT '',
  `section` varchar(16) NOT NULL DEFAULT '',
  `about` text,
  `content` text,
  `charlimit` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `check` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `im` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_reg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_use` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_pro` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `newline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `o` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `lat_setting`
--

INSERT INTO `lat_setting` (`id`, `name`, `title`, `section`, `about`, `content`, `charlimit`, `type`, `check`, `im`, `in_reg`, `in_use`, `in_pro`, `newline`, `required`, `o`) VALUES
(1, 'user_title', '<lang:user_title>', 'global', '<lang:about_user_title>', '', 51, 2, 1, 0, 0, 1, 0, 0, 0, 1),
(2, 'timezone', '<lang:timezone>', 'global', '<lang:about_timezone>', '-12|<lang:time_-12>\r\n-11|<lang:time_-11>\r\n-10|<lang:time_-10>\r\n-9|<lang:time_-09>\r\n-8|<lang:time_-08>\r\n-7|<lang:time_-07>\r\n-6|<lang:time_-06>\r\n-5|<lang:time_-05>\r\n-4|<lang:time_-04>\r\n-3.5|<lang:time_-03.5>\r\n-3|<lang:time_-03>\r\n-2|<lang:time_-02>\r\n-1|<lang:time_-01>\r\n0|<lang:time_0>\r\n1|<lang:time_01>\r\n2|<lang:time_02>\r\n3|<lang:time_03>\r\n3.5|<lang:time_03.5>\r\n4|<lang:time_04>\r\n4.5|<lang:time_04.5>\r\n5|<lang:time_05>\r\n5.5|<lang:time_05.5>\r\n6|<lang:time_06>\r\n7|<lang:time_07>\r\n8|<lang:time_08>\r\n9|<lang:time_09>\r\n9.5|<lang:time_09.5>\r\n10|<lang:time_10>\r\n11|<lang:time_11>\r\n12|<lang:time_12>', 0, 1, 0, 0, 0, 1, 0, 0, 1, 2),
(3, 'dst', '<lang:dst>', 'global', '<lang:about_dst>', '0|<lang:enable_dst>\r\n1|<lang:disable_dst>', 0, 1, 0, 0, 0, 1, 0, 0, 1, 3),
(4, 'long_date', '<lang:long_date>', 'global', '<lang:about_long_date>', '', 51, 2, 1, 0, 0, 1, 0, 0, 0, 4),
(5, 'short_date', '<lang:short_date>', 'global', '<lang:about_short_date>', '', 51, 2, 1, 0, 0, 1, 0, 0, 0, 5),
(6, 'profile_aim', '<lang:aim>', 'profile', '', '{[^0-9A-Za-z]}', 16, 2, 5, 1, 0, 0, 0, 0, 0, 6),
(7, 'profile_gtalk', '<lang:gtalk>', 'profile', '', '', 255, 2, 2, 2, 0, 0, 0, 0, 0, 7),
(8, 'profile_icq', '<lang:icq>', 'profile', '', '', 10, 2, 4, 3, 0, 0, 0, 0, 0, 8),
(9, 'profile_msn', '<lang:msn>', 'profile', '', '', 255, 2, 2, 2, 0, 0, 0, 0, 0, 9),
(10, 'profile_yim', '<lang:yim>', 'profile', '', '{[^0-9A-Za-z&#92;._]}', 22, 2, 5, 1, 0, 0, 0, 0, 0, 10),
(11, 'profile_website', '<lang:website>', 'profile', '', '', 255, 2, 3, 0, 0, 0, 1, 0, 0, 11),
(12, 'profile_location', '<lang:location>', 'profile', '', '', 150, 2, 1, 0, 0, 0, 1, 0, 0, 13),
(13, 'profile_interests', '<lang:interests>', 'profile', '', '', 500, 3, 1, 0, 0, 0, 1, 1, 0, 15),
(14, 'profile_gender', '<lang:gender>', 'profile', '', '1|<lang:male>\r\n2|<lang:female>', 0, 1, 0, 0, 0, 0, 1, 0, 0, 16),
(15, 'profile_job', '<lang:job>', 'profile', '', '', 255, 2, 1, 0, 0, 0, 1, 0, 0, 14),
(16, 'num_posts', '<lang:num_posts>', 'forum', '<lang:about_num_posts>', '0|<lang:default>\r\n5|5\r\n10|10\r\n15|15\r\n20|20\r\n25|25\r\n30|30', 0, 1, 0, 0, 0, 1, 0, 0, 1, 17),
(17, 'num_topics', '<lang:num_topics>', 'forum', '<lang:about_num_topics>', '0|<lang:default>\r\n5|5\r\n10|10\r\n15|15\r\n20|20\r\n25|25\r\n30|30\r\n35|35\r\n40|40\r\n45|45\r\n50|50', 0, 1, 0, 0, 0, 1, 0, 0, 1, 18),
(18, 'hide_sig', '<lang:signatures>', 'global', '<lang:about_signatures>', '0|<lang:show>\r\n1|<lang:hide>', 0, 1, 0, 0, 0, 1, 0, 0, 1, 19),
(19, 'hide_ava', '<lang:avatars>', 'global', '<lang:about_avatars>', '0|<lang:show>\r\n1|<lang:hide>', 0, 1, 0, 0, 0, 1, 0, 0, 1, 20),
(20, 'profile_skype', '<lang:skype>', 'profile', '', '', 25, 2, 1, 1, 0, 0, 0, 0, 0, 12),
(21, 'dont_resize_imgs', '<lang:resize_imgs>', 'global', '<lang:about_resize_imgs>', '0|<lang:yes>\r\n1|<lang:no>', 0, 1, 0, 0, 0, 1, 0, 0, 1, 21);

-- --------------------------------------------------------

--
-- Table structure for table `lat_setting_page`
--

CREATE TABLE IF NOT EXISTS `lat_setting_page` (
  `name` varchar(16) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_setting_page`
--

INSERT INTO `lat_setting_page` (`name`, `title`, `description`) VALUES
('global', '<lang:setting_global>', '<lang:about_setting_global>'),
('forum', '<lang:setting_forum>', '<lang:about_setting_forum>');

-- --------------------------------------------------------

--
-- Table structure for table `lat_topic`
--

CREATE TABLE IF NOT EXISTS `lat_topic` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `posts` smallint(5) unsigned NOT NULL DEFAULT '0',
  `views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `stick` text,
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `moved` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `hidden` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` smallint(5) unsigned NOT NULL DEFAULT '0',
  `start_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `start_ip` varchar(16) NOT NULL DEFAULT '',
  `start_name` varchar(125) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_name` varchar(125) NOT NULL DEFAULT '',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `poll` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `poll_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `last_time` (`last_time`),
  KEY `fid` (`fid`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_topic`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_topic_read`
--

CREATE TABLE IF NOT EXISTS `lat_topic_read` (
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `tiduid` (`tid`,`uid`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_topic_read`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_topic_reply`
--

CREATE TABLE IF NOT EXISTS `lat_topic_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data` text,
  `data_cached` text,
  `data_reparse` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `on_smi` tinyint(1) NOT NULL DEFAULT '0',
  `on_sig` tinyint(1) NOT NULL DEFAULT '0',
  `edit_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `edit_ip` varchar(16) NOT NULL DEFAULT '',
  `edit_time` int(10) unsigned NOT NULL DEFAULT '0',
  `poster_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `poster_time` int(10) unsigned NOT NULL DEFAULT '0',
  `poster_ip` varchar(16) NOT NULL DEFAULT '',
  `poster_name` varchar(125) NOT NULL DEFAULT '',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hidden_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `hidden_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `poster_ip` (`poster_ip`),
  KEY `poster_id` (`poster_id`),
  KEY `poster_time` (`poster_time`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_topic_reply`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_user`
--

CREATE TABLE IF NOT EXISTS `lat_user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `salt` varchar(10) NOT NULL DEFAULT '',
  `birthday` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `user_title` varchar(255) NOT NULL DEFAULT '',
  `user_ip` varchar(16) NOT NULL DEFAULT '',
  `gid` smallint(5) unsigned DEFAULT '0',
  `dst` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `validate` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `validate_code` varchar(32) NOT NULL DEFAULT '',
  `timezone` float NOT NULL DEFAULT '0',
  `short_date` varchar(255) NOT NULL DEFAULT '',
  `long_date` varchar(255) NOT NULL DEFAULT '',
  `avatar_url` varchar(255) NOT NULL DEFAULT '',
  `avatar_height` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `avatar_width` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `avatar_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pm_unread` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pm_notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pm_total` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pm_folders` text,
  `registered` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `set_skin` smallint(5) unsigned NOT NULL DEFAULT '0',
  `set_lang` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hide_sig` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hide_ava` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `member_mail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin_mail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `num_posts` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `num_topics` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `topics` smallint(5) unsigned NOT NULL DEFAULT '0',
  `forum_cutoff` int(10) unsigned NOT NULL DEFAULT '0',
  `dont_resize_imgs` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lat_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_user_history`
--

CREATE TABLE IF NOT EXISTS `lat_user_history` (
  `uid` mediumint(8) unsigned NOT NULL,
  `name` varchar(125) DEFAULT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_user_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_user_lock`
--

CREATE TABLE IF NOT EXISTS `lat_user_lock` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `attempts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `time` (`time`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_user_lock`
--


-- --------------------------------------------------------

--
-- Table structure for table `lat_user_profile`
--

CREATE TABLE IF NOT EXISTS `lat_user_profile` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `signature` text,
  `signature_cached` text,
  `signature_reparse` int(10) unsigned NOT NULL DEFAULT '0',
  `photo_url` varchar(255) NOT NULL DEFAULT '',
  `photo_width` smallint(4) unsigned NOT NULL DEFAULT '0',
  `photo_height` smallint(4) unsigned NOT NULL DEFAULT '0',
  `photo_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `profile_aim` varchar(16) NOT NULL DEFAULT '',
  `profile_gtalk` varchar(255) NOT NULL DEFAULT '',
  `profile_icq` int(10) unsigned NOT NULL DEFAULT '0',
  `profile_msn` varchar(255) NOT NULL DEFAULT '',
  `profile_skype` varchar(128) NOT NULL DEFAULT '',
  `profile_yim` varchar(22) NOT NULL DEFAULT '',
  `profile_website` text,
  `profile_location` text,
  `profile_interests` text,
  `profile_gender` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `profile_job` text,
  UNIQUE KEY `uid` (`uid`),
  FULLTEXT KEY `signature` (`signature`),
  FULLTEXT KEY `signature_2` (`signature`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lat_user_profile`
--


REPLACE INTO `lat_kernel_page` (`name`, `file`, `menu`, `menu_url`, `can_search`, `system`, `cp`) VALUES('cp_global', 'cp/cp_global', '', '', 0, 0, 1);

REPLACE INTO `lat_kernel_autoparse` (`id`, `type`, `site`, `data`, `content`) VALUES(5, 1, 'youtube.com', 'v', '<object width="853" height="505"><param name="movie" value="http://www.youtube.com/v/<!-- VIDEO -->&fs=1&hd=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/<!-- VIDEO -->&fs=1&hd=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="853" height="505"></embed></object>');
REPLACE INTO `lat_kernel_autoparse` (`id`, `type`, `site`, `data`, `content`) VALUES(6, 2, 'metacafe.com', '([0-9]+/[a-z_]+)', '<embed flashVars="playerVars=showStats=yes|autoPlay=no|" src="http://www.metacafe.com/fplayer/<!-- VIDEO -->.swf" width="498" height="423" wmode="transparent" allowFullScreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>');
REPLACE INTO `lat_kernel_autoparse` (`id`, `type`, `site`, `data`, `content`) VALUES(7, 2, '5min.com', '([0-9]+$)', '<object width=''480'' height=''401'' id=''FiveminPlayer''><param name=''allowfullscreen'' value=''true''/><param name=''allowScriptAccess'' value=''always''/><param name=''movie'' value=''http://www.5min.com/Embeded/<!-- VIDEO -->/''/><embed src=''http://www.5min.com/Embeded/<!-- VIDEO -->/'' type=''application/x-shockwave-flash'' width=''480'' height=''401'' allowfullscreen=''true'' allowScriptAccess=''always''></embed></object>');
