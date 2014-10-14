# Latova 0.3.2
======

A fully featured forum/cms software written in PHP. Includes:

* Forums with infinite recursive subforums
* A control panel backend for administrative management
* Registering users, each with independent groups and permissions
* Forum permission sets
* Custom BBcodes and word filters
* Extensive Moderation tools (deleting, editing, hiding, stickies, announcements, splitting, merging, etc)
* Searching topics, posts, members, etc
* Members listing
* User control panel for signatures, avatars, etc
* Debugging tools for developers
* Statistics
* Security features such as captchas, brute force prevention, etc.

### Installing

Drop the files into a folder, use the installation script to create a database and throw config.php into the main folder and edit accordingly.

Then, edit the DB in table lat_config with the row named "script_url", rename it to your forum url with a trailing slash. Edit index.php and set the DEBUG constant to 1, visit in your browser http://www.yourforumurl.com/index.php?pg=forum&do=cache to flush the forum cache. Then set the DEBUG constant back to 0 and your forum is now setup and good to go!
