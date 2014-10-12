<?php

define ("ROOT_PATH", "./");
define ("LAT", 1);

////////////////////////////////////////
// SECURITY IP
// +-----------------------------------+
//  "-1" disables the feature and display sql error debug information to nobody
//  "0"  displays sql error debug only to a system administrator if possible
//  "1"  displays sql error debug details to any IP address. Not recommended
//  Typing in an IP address will display the details to only that single IP
////////////////////////////////////////
define("SECURITY_IP", "0");
////////////////////////////////////////

// Debug Mode (for queries and whatever)
// Also as of 0.3.1, it removes redirects so I can read queries.
define("DEBUG", "0");

// System Vars
if(DEBUG || SECURITY_IP == $_SERVER['REMOTE_ADDR'] || SECURITY_IP == 1)
{
    error_reporting(E_ALL & ~E_NOTICE);
}
else
{
    error_reporting(0);
}

if(DEBUG)
{
    ob_start("ob_gzhandler");
}

@set_magic_quotes_runtime(0);

// Main class of the software
class latova { }
$lat = new latova;
$lat->module = new latova;
$lat->inc = new latova;
require_once(ROOT_PATH."config.php");

////////////////
// BASIC PATHS
////////////////
// There is really no reason you'd want to change these values
// unless you are changing the directory structure of latova.
////////////////
$lat->config['ROOT_PATH']    = ROOT_PATH;
$lat->config['MODULES_PATH'] = ROOT_PATH."modules/";
$lat->config['KERNEL_PATH']  = ROOT_PATH."kernel/";
$lat->config['STORAGE_PATH'] = ROOT_PATH."storage/";
$lat->config['PLUGINS_PATH'] = ROOT_PATH."plugins/";
////////////////

// Load up Latova
require_once ($lat->config['KERNEL_PATH']."core.php");
$lat->core = new kernel_core;
$lat->core->initialize();
?>
