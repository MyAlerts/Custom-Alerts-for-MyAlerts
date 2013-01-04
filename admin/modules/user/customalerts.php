<?php
/**
 * Custom Alerts for MyAlerts 1.0
 * 
 * Provides the ability to push custom alerts for @euantor's MyAlerts plugin.
 *
 * @package Custom Alerts for MyAlerts 1.0
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license (same as MyAlerts)
 * @version 1.0
 * @module MYBB_ROOT/admin/modules/user/customalerts.php
 */
 
if (!defined('IN_MYBB'))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(!defined("PLUGINLIBRARY"))
{
	define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

define(MODULE, "user-customalerts");

$PL or require_once PLUGINLIBRARY;
$lang->load("customalerts");

// Breadcrumb
$page->add_breadcrumb_item($lang->customalerts, "index.php?module=".MODULE);

// Begin!
// Docs
if($mybb->input['action'] == "documentation") {
	
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("documentation");
}
// Push a new alert, single user
elseif($mybb->input['action'] == "pushsingle") {
	if($mybb->request_method == "post") {		
		// does this user even exists?
		$user_exists = user_exists($mybb->input['uid']);
		if(!$user_exists OR empty($mybb->input['uid'])) {
			flash_message($lang->customalerts_error_nouid, 'error');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushsingle");
		}
		
		if(empty($mybb->input['text'])) {
			flash_message($lang->customalerts_error_notext, 'error');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushsingle");
		}
		
		$userID = intval($mybb->input['uid']);
		$alertText = htmlspecialchars($mybb->input['text']);
		$forced = $mybb->input['forced'];
		
		// we're in ACP baby!
		require_once MYALERTS_PLUGIN_PATH.'Alerts.class.php';
		try
		{
			$Alerts = new Alerts($mybb, $db);
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}
		
		// add the alert
		$Alerts->addAlert((int) $userID, "custom", 0, (int) $mybb->user['uid'], array(
		'text'  =>  $alertText,
		), $forced);
		
		// output a friendly successful message
		if($forced) {
			flash_message($lang->customalerts_success_forced, 'success');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushsingle");
		}
		else {
			flash_message($lang->customalerts_success, 'success');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushsingle");
		}
			
	}
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("pushalerts");
	// construct the main form
	$form = new Form("index.php?module=".MODULE."&amp;action=pushsingle", "post");
	$form_container = new FormContainer($lang->customalerts_pushalerts);

	// store things in variables to clean up the code
	$uid = $form->generate_text_box('uid', $mybb->input['uid']);
	$text = $form->generate_text_area('text', $mybb->input['text']);
	$options = $form->generate_check_box('forced', '1', $lang->customalerts_options_forceonuser, array('checked' => 1, 'id' => 'forced'))."<br /><small>{$lang->customalerts_options_forceonuser_desc}</small>";

	// actually construct the form
	$form_container->output_row($lang->customalerts_uid, $lang->customalerts_uid_desc, $uid, 'uid');
	$form_container->output_row($lang->customalerts_text, $lang->customalerts_text_desc, $text, 'text');
	$form_container->output_row($lang->customalerts_options, "", $options, 'options');
		
	$form_container->end();
		
	$buttons[] = $form->generate_submit_button($lang->customalerts_push_button);
		
	$form->output_submit_wrapper($buttons);
		
	$form->end();
}
// Push a new alert, usergroup
elseif($mybb->input['action'] == "pushgroup") {
	if($mybb->request_method == "post") {		
	
		if(empty($mybb->input['group'])) {
			flash_message($lang->customalerts_error_nogroup, 'error');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushgroup");
		}
		
		if(empty($mybb->input['text'])) {
			flash_message($lang->customalerts_error_notext, 'error');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushgroup");
		}
		
		$forced = $mybb->input['forced'];
		$usergroups = "'".implode("','", $mybb->input['group'])."'";
		$query = $db->simple_select("users", "uid, myalerts_settings", "usergroup IN ({$usergroups})");
				
		$users = array();
		$userSettings = array();
		while ($user = $db->fetch_array($query)) {
			$userSettings[$user['uid']] = json_decode($user['myalerts_settings'], true);
			if ($userSettings[$user['uid']]['custom'] OR $forced) {
				$users[] = $user['uid'];
			}
		}
		
		$alertText = htmlspecialchars($mybb->input['text']);
		
		// we're in ACP baby!
		require_once MYALERTS_PLUGIN_PATH.'Alerts.class.php';
		try
		{
			$Alerts = new Alerts($mybb, $db);
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}
		
		// add the alert
		if (!empty($users)) {
			$Alerts->addMassAlert((array) $users, "custom", 0, (int) $mybb->user['uid'], array(
			'text'  =>  $alertText,
			), $forced);
		}
		
		// output a friendly successful message
		if($forced) {
			flash_message($lang->customalerts_success_group_forced, 'success');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushgroup");
		}
		else {
			flash_message($lang->customalerts_success_group, 'success');
			admin_redirect("index.php?module=".MODULE."&amp;action=pushgroup");
		}
			
	}
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("pushalertsgroup");
	// construct the main form
	$form = new Form("index.php?module=".MODULE."&amp;action=pushgroup", "post");
	$form_container = new FormContainer($lang->customalerts_pushalertsgroup);
	
	// store things in variables to clean up the code
	$group = $form->generate_group_select("group[]", $mybb->input['group'], array("multiple"=>true));
	$text = $form->generate_text_area('text', $mybb->input['text']);
	$options = $form->generate_check_box('forced', '1', $lang->customalerts_options_forceonuser, array('checked' => 1, 'id' => 'forced'))."<br /><small>{$lang->customalerts_options_forceonuser_desc}</small>";
	
	// actually construct the form
	$form_container->output_row($lang->customalerts_group, $lang->customalerts_group_desc, $group, 'group');
	$form_container->output_row($lang->customalerts_text, $lang->customalerts_text_desc, $text, 'text');
	$form_container->output_row($lang->customalerts_options, "", $options, 'options');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->customalerts_push_group_button);

	$form->output_submit_wrapper($buttons);
	
	$form->end();
}
// Overview
else {
	
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("overview");
	
	// construct the main table
	$table = new Table;
	
	// actually construct the table header
	$table->construct_header($lang->customalerts_overview);
	
	// info
	$table->construct_cell($lang->customalerts_overview_innerdesc);
	$table->construct_row();
	
	// output it!
	$table->output($lang->customalerts);
	
}

$page->output_footer();

// Generate tabs - technique ripped from MyBot, thank you Jones!
function generate_tabs($selected)
{
	global $lang, $page;

	$sub_tabs = array();
	$sub_tabs['overview'] = array(
		'title' => $lang->customalerts_overview,
		'link' => "index.php?module=".MODULE,
		'description' => $lang->customalerts_overview_desc
	);
	$sub_tabs['pushalerts'] = array(
		'title' => $lang->customalerts_pushalerts,
		'link' => "index.php?module=".MODULE."&amp;action=pushsingle",
		'description' => $lang->customalerts_pushalerts_desc
	);
	$sub_tabs['pushalertsgroup'] = array(
		'title' => $lang->customalerts_pushalertsgroup,
		'link' => "index.php?module=".MODULE."&amp;action=pushgroup",
		'description' => $lang->customalerts_pushalertsgroup_desc
	);
	$sub_tabs['documentation'] = array(
		'title' => $lang->customalerts_documentation,
		'link' => "index.php?module=".MODULE."&amp;action=documentation",
		'description' => $lang->customalerts_documentation_desc
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}

