<?php
/**
 * Custom Alerts for MyAlerts
 * 
 * Provides the ability to push custom alerts for @euantor's MyAlerts plugin.
 *
 * @package Custom Alerts for MyAlerts
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license (same as MyAlerts)
 * @version 1.1.1
 */


if(!defined('IN_MYBB'))
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
		'name' => 'Custom Alerts for MyAlerts',
		'description' => 'Provides the ability to push to your users custom alerts for @euantor\'s <a href="http://community.mybb.com/thread-127444.html"><b>MyAlerts</b></a> plugin.<br /><span style="color:#f00">MyAlerts is required for Custom Alerts for MyAlerts to work</span>.',
		'website' => 'http://github.com/MyAlerts/Custom-Alerts-for-MyAlerts',
		'author' => 'Shade',
		'authorsite' => '',
		'version' => '1.1.1',
		'compatibility' => '16*',
		'guid' => '456bd2a816d19700e08f11cab6e27e5f'
	);
}

function customalerts_is_installed()
{
	global $cache;
	
	$info = customalerts_info();
	$installed = $cache->read("shade_plugins");
	if($installed[$info['name']])
	{
		return true;
	}
}

function customalerts_install()
{
	global $db, $PL, $lang, $mybb, $cache;
	
	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	// Check if a random myalerts setting exist - if false, then MyAlerts is not installed, warn the user and redirect him
	if(!$mybb->settings['myalerts_enabled'])
	{
		flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/myalerts\">MyAlerts</a> is not installed. Moderation Alerts Pack requires MyAlerts to be installed in order to properly work.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	if(!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	$info = customalerts_info();
	$shadePlugins = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title' => $info['name'],
		'version' => $info['version']
	);
	$cache->update('shade_plugins', $shadePlugins);
	
	// Search for myalerts existing settings and add our custom ones
	$query = $db->simple_select("settinggroups", "gid", "name='myalerts'");
	$gid = intval($db->fetch_field($query, "gid"));
	
	$settings = array();

	$settings[] = array(
		"name" => "myalerts_alert_custom",
		"title" => $lang->customalerts_enabled,
		"description" => $lang->customalerts_enabled_desc,
		"optionscode" => "yesno",
		"value" => "1"
	);

	$insert_settings = array();

	$i = 1;
	foreach($settings as $setting)
	{
		$insert['name'] = $db->escape_string($setting['name']);
		$insert['title'] = $db->escape_string($setting['title']);
		$insert['description'] = $db->escape_string($setting['description']);
		$insert['optionscode'] = $db->escape_string($setting['optionscode']);
		$insert['value'] = $db->escape_string($setting['value']);
		$insert['disporder'] = $i;
		$insert['gid'] = $gid;

		$insert_settings[] = $insert;

		$i++;
	}

	$db->insert_query_multiple('settings', $insert_settings);

	
	$insertArray = array(
		0 => array(
			'code' => 'custom'
		)
	);
	
	$db->insert_query_multiple('alert_settings', $insertArray);
	
	$query = $db->simple_select('users', 'uid');
	while($uids = $db->fetch_array($query))
	{
		$users[] = $uids['uid'];
	}
	
	$query = $db->simple_select("alert_settings", "id", "code IN ('custom')");
	while($setting = $db->fetch_array($query))
	{
		$settings[] = $setting['id'];
	}
	
	foreach($users as $user)
	{
		foreach($settings as $setting)
		{
			$userSettings[] = array(
				'user_id' => (int) $user,
				'setting_id' => (int) $setting,
				'value' => 1
			);
		}
	}
	
	$db->insert_query_multiple('alert_setting_values', $userSettings);
	
	// Rebuild ./inc/settings.php
	rebuild_settings();
	
}

function customalerts_uninstall()
{
	global $db, $PL, $cache;
	
	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be uninstalled because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	// Delete ACP settings
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myalerts_alert_custom')");
	
	// Delete existing values
	$query = $db->simple_select("alert_settings", "id", "code IN ('custom')");
	while($setting = $db->fetch_array($query))
	{
		$settings[] = $setting['id'];
	}
	$settings = implode(",", $settings);
	
	// Truly delete them
	if(!empty($settings))
	{
		$db->delete_query("alert_setting_values", "setting_id IN ({$settings})");
	}
	// Delete UCP settings
	$db->delete_query("alert_settings", "code IN ('custom')");
	
	$info = customalerts_info();
	// Delete the plugin from cache
	$shadePlugins = $cache->read('shade_plugins');
	unset($shadePlugins[$info['name']]);
	$cache->update('shade_plugins', $shadePlugins);


	// Rebuild ./inc/settings.php
	rebuild_settings();
}

if($settings['myalerts_enabled'] AND $settings['myalerts_alert_custom'])
{
	$plugins->add_hook("admin_user_menu", "customalerts_admin_user_menu");
	$plugins->add_hook("admin_user_action_handler", "customalerts_admin_user_action_handler");
}

// Load our custom lang file into MyAlerts
$plugins->add_hook('myalerts_load_lang', 'customalerts_load_lang');
function customalerts_load_lang()
{
	global $lang;
	
	if(!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
}

function customalerts_admin_user_menu($sub_menu)
{
	global $lang;
	
	if(!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	$sub_menu[] = array(
		"id" => "customalerts",
		"title" => $lang->customalerts,
		"link" => "index.php?module=user-customalerts"
	);
	
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

// Generate text and such
$plugins->add_hook('myalerts_alerts_output_start', 'customalerts_parseAlerts');
function customalerts_parseAlerts(&$alert)
{
	global $mybb, $lang;
	
	if(!$lang->customalerts)
	{
		$lang->load('customalerts');
	}
	
	if($alert['alert_type'] == 'custom')
	{
		
		require_once  MYBB_ROOT.'inc/class_parser.php';
    	$parser = new postParser;
		
		$options = array(
			"allow_html" => 1,
			"allow_mycode" => 1,
			"allow_smilies" => 1,
			"allow_imgcode" => 1,
			"parse_badwords" => 1
		);
		
		// Format the username
		$userusername = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
		$userusername = build_profile_link($userusername, $mybb->user['uid']);
		
		$alert['text'] = $alert['content']['text'];		
		$thingsToReplace = array(
			"{username}" => $alert['user'],
			"{userusername}" => $userusername,
			"{date}" => $alert['dateline']
		);
			
		// Replace what needs to be replaced
		foreach($thingsToReplace as $find => $replace)
		{
			$alert['text'] = str_replace($find, $replace, $alert['text']);
		}
		
		$alert['text'] = $parser->parse_message($alert['text'], $options);
		
		// Output the alert
		$alert['message'] = $lang->sprintf($lang->customalerts_custom, $alert['text']);
		$alert['rowType'] = 'customAlert';
	}
}