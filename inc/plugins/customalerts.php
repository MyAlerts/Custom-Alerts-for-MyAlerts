<?php
/**
 * Custom Alerts for MyAlerts 1.0.2
 * 
 * Provides the ability to push custom alerts for @euantor's MyAlerts plugin.
 *
 * @package Custom Alerts for MyAlerts 1.0.2
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license (same as MyAlerts)
 * @version 1.0.2
 */
 
if (!defined('IN_MYBB'))
{
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if(!defined("PLUGINLIBRARY"))
{
	define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

function customalerts_info()
{
	return array(
		'name'          =>  'Custom Alerts for MyAlerts',
		'description'   =>  'Provides the ability to push to your users custom alerts for @euantor\'s <a href="http://community.mybb.com/thread-127444.html"><b>MyAlerts</b></a> plugin.<br /><span style="color:#f00">MyAlerts is required for Custom Alerts for MyAlerts to work</span>.',
		'website'       =>  'http://idevicelab.net',
		'author'        =>  'Shade',
		'authorsite'    =>  'http://idevicelab.net',
		'version'       =>  '1.0.2',
		'compatibility' =>  '16*',
		'guid'           =>  'none... yet!',
		);
}

function customalerts_is_installed()
{
	global $mybb;
	
	// Custom Alerts for MyAlerts adds one setting. Just check a random one, if not present then the plugin isn't installed
	if($mybb->settings['myalerts_alert_custom'])
	{
		return true;
	}
}

function customalerts_install()
{
	global $db, $PL, $lang, $mybb;

	if (!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	// check if a random myalerts setting exist - if false, then MyAlerts is not installed, warn the user and redirect him
	if(!$mybb->settings['myalerts_enabled'])
	{
		flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/myalerts\">MyAlerts</a> is not installed. Moderation Alerts Pack requires MyAlerts to be installed in order to properly work.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;	
	
	if (!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	// search for myalerts existing settings and add our custom ones
	$query = $db->simple_select("settinggroups", "gid", "name='myalerts'");
	$gid = intval($db->fetch_field($query, "gid"));
	
	$customalerts_settings_1 = array(
		"name" => "myalerts_alert_custom",
		"title" => $lang->customalerts_enabled,
		"description" => $lang->customalerts_enabled_desc,
		"optionscode" => "yesno",
		"value" => "1",
		"disporder" => "1",
		"gid" => $gid,
	);
	$db->insert_query("settings", $customalerts_settings_1);
	
	// Set our alerts on for all users by default, maintaining existing alerts values
    // Declare a data array containing all our alerts settings we'd like to add. To default them, the array must be associative and keys must be set to "on" (active) or 0 (not active)
    $possible_settings = array(
            'custom' => "on",
            );
    
    $query = $db->simple_select('users', 'uid, myalerts_settings', '', array());
    
    while($settings = $db->fetch_array($query))
    {
        // decode existing alerts with corresponding key values. json_decode func returns an associative array by default, we don't need to edit it
        $alert_settings = json_decode($settings['myalerts_settings']);
        
        // merge our settings with existing ones...
        $my_settings = array_merge($possible_settings, (array) $alert_settings);
        
        // and update the table cell, encoding our modified array and paying attention to SQL inj (thanks Nathan!)
        $db->update_query('users', array('myalerts_settings' => $db->escape_string(json_encode($my_settings))), 'uid='.(int) $settings['uid']);
    }
		
	// rebuild settings
	rebuild_settings();

}

function customalerts_uninstall()
{
	global $db, $PL;
	
	if (!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be uninstalled because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}

	$PL or require_once PLUGINLIBRARY;
			   	
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myalerts_alert_custom')");
		
	// rebuild settings
	rebuild_settings();
}

if($settings['myalerts_enabled'] AND $settings['myalerts_alert_custom']) {
	$plugins->add_hook("admin_user_menu", "customalerts_admin_user_menu");
	$plugins->add_hook("admin_user_action_handler", "customalerts_admin_user_action_handler");
}

function customalerts_admin_user_menu($sub_menu)
{
	global $lang;

	if (!$lang->customalerts)
	{
		$lang->load('customalerts');
	}

	$sub_menu[] = array("id" => "customalerts", "title" => $lang->customalerts, "link" => "index.php?module=user-customalerts");

	return $sub_menu;
}

function customalerts_admin_user_action_handler($actions)
{
	$actions['customalerts'] = array(
		"active" => "customalerts",
		"file" => "customalerts.php"
	);

	return $actions;
}

// generate text and such
$plugins->add_hook('myalerts_alerts_output_start', 'customalerts_parseAlerts');
function customalerts_parseAlerts(&$alert)
{
	global $mybb, $lang;
	
	if (!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	if ($alert['alert_type'] == 'custom')
	{
		// do the actual replacements
		$alert['text'] = str_replace("{username}", $alert['user'], $alert['content']['text']);
		$alert['text'] = str_replace("{date}", $alert['dateline'], $alert['text']);
		
		// output the alert
		$alert['message'] = $lang->sprintf($lang->customalerts_custom, $alert['text']);
		$alert['rowType'] = 'customAlert';
	}
}

// add alerts into UCP
$plugins->add_hook('myalerts_possible_settings', 'customalerts_possibleSettings');
function customalerts_possibleSettings(&$possible_settings)
{
	global $lang;
	
	if (!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	$_possible_settings = array('custom');
	
	$possible_settings = array_merge($possible_settings, $_possible_settings);
}