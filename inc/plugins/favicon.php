<?php
/** Plugin : Favicon
 *  Author : lukaspgm
 *  GPL Version 3, 29 June 2007
 *  (c) Copyright 2023
 */

 
/**
 * Disallow direct access to this file for security reasons
 */
if(!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';

/**
 * Register & Add plugin hooks
 */
$plugins->add_hook('global_end', 'addfavicon');


/**
 * Plugin Info
 */
function favicon_info() {
	return array(
		'name'			=> 'Favicon',
		'description'	=> 'Add a browser icon (favicon) to your forum',
		'author'		=> 'lukaspgm',
        'authorsite'    => 'https://github.com/lukas-pgm/',
		'version'		=> '1.0',
		'compatibility'	=> '18*'
	);
}

/**
 * Hook functions
 */
function addfavicon() {
	global $headerinclude, $mybb;

    if($mybb->settings['favicon_show'] != 1) {
        return $headerinclude;
    }

    if($mybb->settings['favicon_url'] == "" || !str_ends_with($mybb->settings['favicon_url'],".ico")) {
        return $headerinclude;
    }
	
	$headerinclude = '<link rel="shortcut icon" href="'.$mybb->settings['favicon_url'].'" type="image/x-icon" />'.$headerinclude;
	return $headerinclude;
}


/**
 * Plugin functions
 */
function favicon_activate() {
    global $mybb, $db;
    $favicon_group = array(
        'gid'    => 'NULL',
        'name'  => 'favicon',
        'title'      => 'Favicon',
        'description'    => 'Add a browser icon (favicon) to your forum',
        'disporder'    => "1",
        'isdefault'  => "0",
    ); 
    $db->insert_query('settinggroups', $favicon_group);
    $gid = $db->insert_id();
    $default_url = $mybb->settings['homeurl']."images/favicon.ico";

    $favicon_setting1 = array(
        'sid'            => 'NULL',
        'name'        => 'favicon_show',
        'title'            => 'Enable or Disable',
        'description'    => 'On enable this plugin will show a favicon.',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 1,
        'gid'            => intval($gid),
    );
    $favicon_setting2 = array(
        'sid'            => 'NULL',
        'name'        => 'favicon_url',
        'title'            => 'URL of the favicon',
        'description'    => 'Enter the url of the image (.ico) you wish to use as favicon',
        'optionscode'    => 'text',
        'value'        => $default_url,
        'disporder'        => 2,
        'gid'            => intval($gid),
    );
    $db->insert_query('settings', $favicon_setting1);
    $db->insert_query('settings', $favicon_setting2);
    rebuild_settings();
}
function favicon_deactivate() {
    global $db;
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'favicon_show'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'favicon_url'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='favicon'");
    rebuild_settings();
  }
?>