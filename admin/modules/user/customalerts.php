<?php
/**
 * Custom Alerts for MyAlerts 1.0.1
 * 
 * Provides the ability to push custom alerts for @euantor's MyAlerts plugin.
 *
 * @package Custom Alerts for MyAlerts 1.0.1
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license (same as MyAlerts)
 * @version 1.0.1
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
// Push a new alert directly
elseif($mybb->input['action'] == "pushalert") {
	if($mybb->request_method == "post") {
		
		// cleaning up the code!
		$conditions = $mybb->input['conditions'];
		$forced = $mybb->input['forced'];
		$userID = $mybb->input['uids'];
		$usergroups = $mybb->input['group'];
		$text = $mybb->input['text'];
		
		// errors
		// no conditions
		if(!$conditions) {
			$errors[] = $lang->customalerts_error_noconditions;
		}
		if(is_array($conditions)) {
			// no/unexisting UIDs
			if(in_array('uid', $conditions)) {
				
				// clean up the string from whitespaces
				$userID = preg_replace("/[^0-9,]/", "", $userID);
				// split them in an array and clean it up
				$_users = array_values(array_filter(explode(",", $userID)));
				
				if(empty($_users)) {
					$errors[] = $lang->customalerts_error_nouid;
				}
				
				// does those users even exist?
				foreach ($_users as $user) {
					$user_exists = user_exists($user);
					if(!$user_exists) {
						$errors[] = $lang->customalerts_error_noexistinguid;
						break;
					}
				}
			}
			// no usergroups
			if(in_array('usergroup', $conditions) AND empty($usergroups)) {
				$errors[] = $lang->customalerts_error_nogroup;
			}
		}
		// no text
		if(empty($text)) {
			$errors[] = $lang->customalerts_error_notext;
		}
		if(!$errors) {
			// we're in ACP baby!
			require_once MYALERTS_PLUGIN_PATH.'Alerts.class.php';
			$Alerts = new Alerts($mybb, $db);
			
			$alertText = htmlspecialchars($text);
			
			// UID but no usergroup
			if(in_array('uid', $conditions) AND !in_array('usergroup', $conditions)) {
				// clean up the string from whitespaces
				$userID = preg_replace("/[^0-9,]/", "", $userID);
				// split them in an array and clean it up
				$_users = array_values(array_filter(explode(",", $userID)));
				
				$uids = "'".implode("','", $_users)."'";
				$query = $db->simple_select("users", "uid, myalerts_settings", "uid IN ({$uids})");
				// let's check whether if the user would like to receive custom alerts, or if the alert is forced
				$users = array();
				$userSettings = array();
				while ($user = $db->fetch_array($query)) {
					$userSettings[$user['uid']] = json_decode($user['myalerts_settings'], true);
					if ($userSettings[$user['uid']]['custom'] OR $forced) {
						$users[] = $user['uid'];
					}
				}
		
				// add the alert
				if (!empty($users)) {
					$Alerts->addMassAlert((array) $users, "custom", 0, (int) $mybb->user['uid'], array(
					'text'  =>  $alertText,
					), $forced);
				}
				// oops, no users here
				else {
					$errors[] = $lang->customalerts_error_nousers;
				}
				
				// errors won't stop here
				if(!$errors) {
					// output a friendly successful message
					if($forced) {
						flash_message($lang->customalerts_success_forced, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
					else {
						flash_message($lang->customalerts_success, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
				}
			}
			// usergroup but no UID
			elseif(in_array('usergroup', $conditions) AND !in_array('uid', $conditions)) {
				$usergroups = "'".implode("','", $usergroups)."'";
				$query = $db->simple_select("users", "uid, myalerts_settings", "usergroup IN ({$usergroups})");
				
				// let's check whether if the user would like to receive custom alerts, or if the alert is forced
				$users = array();
				$userSettings = array();
				while ($user = $db->fetch_array($query)) {
					$userSettings[$user['uid']] = json_decode($user['myalerts_settings'], true);
					if ($userSettings[$user['uid']]['custom'] OR $forced) {
						$users[] = $user['uid'];
					}
				}
		
				// add the alert
				if (!empty($users)) {
					$Alerts->addMassAlert((array) $users, "custom", 0, (int) $mybb->user['uid'], array(
					'text'  =>  $alertText,
					), $forced);
				}
				// oops, no users here
				else {
					$errors[] = $lang->customalerts_error_nousers;
				}
				
				// errors won't stop here
				if(!$errors) {
					// output a friendly successful message
					if($forced) {
						flash_message($lang->customalerts_success_group_forced, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
					else {
						flash_message($lang->customalerts_success_group, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
				}
			}
			// both of them
			elseif(in_array('uid', $conditions) AND in_array('usergroup', $conditions)) {
				$usergroups = "'".implode("','", $usergroups)."'";
				$userID = intval($userID);
				$userID = "'".$userID."'";
				$query = $db->simple_select("users", "uid, myalerts_settings", "usergroup IN ({$usergroups}) AND uid IN ({$userID})");
				
				// let's check whether if the user would like to receive custom alerts, or if the alert is forced
				$users = array();
				$userSettings = array();
				while ($user = $db->fetch_array($query)) {
					$userSettings[$user['uid']] = json_decode($user['myalerts_settings'], true);
					if ($userSettings[$user['uid']]['custom'] OR $forced) {
						$users[] = $user['uid'];
					}
				}
		
				// add the alert
				if (!empty($users)) {
					$Alerts->addMassAlert((array) $users, "custom", 0, (int) $mybb->user['uid'], array(
					'text'  =>  $alertText,
					), $forced);
				}
				// oops, no users here
				else {
					$errors[] = $lang->customalerts_error_nousers;
				}
				
				// errors won't stop here
				if(!$errors) {
					// output a friendly successful message
					if($forced) {
						flash_message($lang->customalerts_success_uidandgroup_forced, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
					else {
						flash_message($lang->customalerts_success_uidandgroup, 'success');
						admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
					}
				}
			}
			else {
				flash_message($lang->customalerts_error_debug, 'error');
				admin_redirect("index.php?module=".MODULE."&amp;action=pushalert");
			}
		}
	}
	// header before anything else
	$page->output_header($lang->customalerts);
	// errors
	if($errors) {
		$page->output_inline_error($errors);
	}
	// generate the tab
	generate_tabs("pushalerts");
	// construct the main form
	$form = new Form("index.php?module=".MODULE."&amp;action=pushalert", "post");
	$form_container = new FormContainer($lang->customalerts_pushalerts);
	
	// store things in variables to clean up the code
	$conditions_list = array(
				"uid" => $lang->customalerts_uid,
				"usergroup" => $lang->customalerts_group);
	$add_conditions = $form->generate_select_box("conditions[]", $conditions_list, $conditions, array("multiple"=>true, "id"=>"conditions"));
	$uid = $form->generate_text_box('uids', $userID);
	$text = $form->generate_text_area('text', $text);
	$options = $form->generate_check_box('forced', '1', $lang->customalerts_options_forceonuser, array('checked' => 1, 'id' => 'forced'))."<br /><small>{$lang->customalerts_options_forceonuser_desc}</small>";
	$group = $form->generate_group_select("group[]", $usergroups, array("multiple"=>true));

	// actually construct the form
	$form_container->output_row($lang->customalerts_add_conditions." <em>*</em>", $lang->customalerts_add_conditions_desc, $add_conditions);
	$form_container->output_row($lang->customalerts_uid, $lang->customalerts_uid_desc, $uid, 'uid', array(), array('id' => 'uid'));
	$form_container->output_row($lang->customalerts_group, $lang->customalerts_group_desc, $group, 'group', array(), array('id' => 'usergroup'));
	$form_container->output_row($lang->customalerts_text, $lang->customalerts_text_desc, $text, 'text');
	$form_container->output_row($lang->customalerts_options, "", $options, 'options');
		
	$form_container->end();
		
	$buttons[] = $form->generate_submit_button($lang->customalerts_push_button);
		
	$form->output_submit_wrapper($buttons);
		
	$form->end();
	
	echo '<script type="text/javascript" src="./jscripts/customalerts_peeker.js"></script>
		<script type="text/javascript">
			Event.observe(window, "load", function() {
				loadPeekers();
			});
			function loadPeekers()
			{
				new Peeker($("conditions"), $("uid"), /uid/, false);
				new Peeker($("conditions"), $("usergroup"), /usergroup/, false);
			}
		</script>';
	
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
		'link' => "index.php?module=".MODULE."&amp;action=pushalert",
		'description' => $lang->customalerts_pushalerts_desc
	);
	$sub_tabs['documentation'] = array(
		'title' => $lang->customalerts_documentation,
		'link' => "index.php?module=".MODULE."&amp;action=documentation",
		'description' => $lang->customalerts_documentation_desc
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}