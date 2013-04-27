<?php
/**
 * Custom Alerts for MyAlerts
 * 
 * Provides the ability to push custom alerts for @euantor's MyAlerts plugin.
 *
 * @package Custom Alerts for MyAlerts
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license (same as MyAlerts)
 * @version 1.1
 * @module MYBB_ROOT/admin/modules/user/customalerts.php
 */

if (!defined('IN_MYBB')) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

if (!defined("PLUGINLIBRARY")) {
	define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

define(MODULE, "user-customalerts");

$PL or require_once PLUGINLIBRARY;
// require useful class
require_once MYBB_ROOT . "inc/class_parser.php";
$parser = new postParser;

$lang->load("customalerts");

// Breadcrumb
$page->add_breadcrumb_item($lang->customalerts, "index.php?module=" . MODULE);

// Begin!
// Docs
if ($mybb->input['action'] == "documentation") {
	// Breadcrumb
	$page->add_breadcrumb_item($lang->customalerts_documentation, "index.php?module=" . MODULE . "&amp;action=documentation");
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("documentation");
	
	// construct the main table
	$table = new Table;
	
	// actually construct the table header
	$table->construct_header($lang->customalerts_doc_info, array(
		"width" => "25%"
	));
	$table->construct_header($lang->customalerts_doc_description);
	
	// overview
	$table->construct_cell($lang->customalerts_doc_overview);
	$table->construct_cell($lang->customalerts_doc_overview_desc);
	$table->construct_row();
	
	// features
	$table->construct_cell($lang->customalerts_doc_features);
	$table->construct_cell($lang->customalerts_doc_features_desc);
	$table->construct_row();
	
	// new alert
	$table->construct_cell($lang->customalerts_doc_newalert);
	$table->construct_cell($lang->customalerts_doc_newalert_desc);
	$table->construct_row();
	
	// methods
	$table->construct_cell($lang->customalerts_doc_methods);
	$table->construct_cell($lang->customalerts_doc_methods_desc);
	$table->construct_row();
	
	// let's spread the word a bit ;)
	$table->construct_cell($lang->customalerts_doc_otherplugins);
	$table->construct_cell($lang->customalerts_doc_otherplugins_desc);
	$table->construct_row();
	
	// output it!
	$table->output($lang->customalerts_documentation);
}
/*elseif ($mybb->input['action'] == "logs") {
	
	// Breadcrumb
	$page->add_breadcrumb_item($lang->customalerts_logs, "index.php?module=" . MODULE . "&amp;action=logs");
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("logs");
	
	// construct the main table
	$table = new Table;
	
	// actually construct the table header
	$table->construct_header($lang->customalerts_logs_alertid, array(
		"width" => "5%"
	));
	$table->construct_header($lang->customalerts_logs_alertfrom, array(
		"width" => "15%"
	));
	$table->construct_header($lang->customalerts_logs_alertto, array(
		"width" => "15%"
	));
	$table->construct_header($lang->customalerts_logs_alertcontent);
	$table->construct_header($lang->customalerts_logs_status, array(
		"width" => "5%"
	));
	$table->construct_header($lang->customalerts_logs_forced, array(
		"width" => "5%"
	));
	
	$query = $db->write_query("SELECT a.*, t.uid AS touid, t.username AS toname, t.usergroup AS togroup, t.displaygroup AS todgroup, f.uid AS fromuid, f.username AS fromname, f.usergroup AS fromgroup, f.displaygroup AS fromdgroup FROM ".TABLE_PREFIX."alerts a
	LEFT JOIN ".TABLE_PREFIX."users t ON (a.uid = t.uid)
	LEFT JOIN ".TABLE_PREFIX."users f ON (a.from_id = f.uid)
	ORDER BY a.id");
		
	// loop through all alerts
	while ($alert = $db->fetch_array($query)) {
		
		$alert['toname'] = format_name($alert['toname'], $alert['togroup'], $alert['todgroup']);
    	$alert['toname'] = build_profile_link($alert['toname'], $alert['touid']);
		$alert['fromname'] = format_name($alert['fromname'], $alert['fromgroup'], $alert['fromdgroup']);
    	$alert['fromname'] = build_profile_link($alert['fromname'], $alert['fromuid']);
				
		$table->construct_cell($alert['id']);
		$table->construct_cell($alert['fromname']);
		$table->construct_cell($alert['toname']);
		$table->construct_cell($alert['content']);
		$table->construct_cell($alert['unread']);
		$table->construct_cell($alert['forced']);
			
		$table->construct_row();
	}
	
	// output it!
	$table->output($lang->customalerts_logs);
	
}*/
// Push a new alert directly
elseif ($mybb->input['action'] == "pushalert") {
	if ($mybb->request_method == "post") {
		// cleaning up the code!
		$methods = $mybb->input['methods'];
		$forced = $mybb->input['forced'];
		$userID = $mybb->input['uids'];
		$usergroups = $mybb->input['group'];
		$text = $mybb->input['text'];
		$users = $mybb->input['users'];
		
		// errors
		// no methods
		if (!$methods) {
			$errors[] = $lang->customalerts_error_nomethods;
		}
		if (is_array($methods)) {
			// no/unexisting UIDs
			if (in_array('uid', $methods)) {
				
				// clean up the string from whitespaces
				$userID = preg_replace("/[^0-9,]/", "", $userID);
				// split them in an array and clean it up
				$_users = array_values(array_filter(explode(",", $userID)));
				
				if (empty($_users)) {
					$errors[] = $lang->customalerts_error_nouid;
				}
				
				// does those users even exist?
				foreach ($_users as $user) {
					$user_exists = user_exists($user);
					if (!$user_exists) {
						$errors[] = $lang->customalerts_error_noexistinguid;
						break;
					}
				}
			}
			// no usergroups
			if (in_array('usergroup', $methods) AND empty($usergroups)) {
				$errors[] = $lang->customalerts_error_nogroup;
			}
		}
		// no text
		if (empty($text)) {
			$errors[] = $lang->customalerts_error_notext;
		}
		if (!$errors) {
			// we're in ACP baby!
			require_once MYALERTS_PLUGIN_PATH . 'Alerts.class.php';
			$Alerts = new Alerts($mybb, $db);
						
			$alertText = $text;
			
			// UIDs
			if (in_array('uid', $methods)) {
				// clean up the string from whitespaces
				$userID = preg_replace("/[^0-9,]/", "", $userID);
				// split them in an array and clean it up
				$users_uid = array_values(array_filter(explode(",", $userID)));
			}
			// usergroup
			if (in_array('usergroup', $methods)) {
				if (count($methods) > 1) {
					$separator = " OR ";
				}
				
				$usergroups = implode("','", $usergroups);
				$usergroups = "usergroup IN ({$usergroups})";
			}
			// users
			if (in_array('users', $methods)) {
				$users_users = $users;
			}
			// all
			if (in_array('all', $methods)) {
				$all = 1;
			}
			
			// having some fun with arrays!
			settype($users_uid, "array");
			settype($users_users, "array");
			$users = array_unique(array_merge($users_uid, $users_users));
			// fixes #1
			if (!empty($users)) {
				$uids = implode(",", $users);
				$users = "uid IN ({$uids})";
			} else {
				$users = "";
			}
			
			// our main query
			if ($all) {
				// forced, select all users (#1)
				if ($forced) {
					$query = $db->simple_select("users", "uid");
				}
				// not forced, select users who wants this type of alerts
				else {
					$query = $db->simple_select("alert_setting_values", "user_id", "code = 'custom'");
				}
			}
			// only specified users... this is truly mindblowing
			else {
				// whether is forced (#2) ...
				if ($forced) {
					$query = $db->simple_select("users", "uid", "{$users}{$separator}{$usergroups}");
				}
				// ... or not!
				else {
					// we must specify "u." before our puzzle's pieces
					if (!empty($users)) {
						$users = "u." . $users;
					}
					if (!empty($usergroups)) {
						$usergroups = "u." . $usergroups;
					}
					// let's do it!
					$query = $db->write_query("SELECT a.user_id
						FROM " . TABLE_PREFIX . "alert_setting_values a
						LEFT JOIN " . TABLE_PREFIX . "users u ON (a.user_id = u.uid)
						LEFT JOIN " . TABLE_PREFIX . "alert_settings s ON (a.setting_id = s.id)
						WHERE s.code = 'custom' AND a.value <> 0 AND ({$users}{$separator}{$usergroups})");
				}
			}
			
			// let's build our users array
			$users = array();
			while ($user = $db->fetch_array($query)) {
				// #3 "is forced" check
				if ($forced) {
					$users[] = $user['uid'];
				} else {
					$users[] = $user['user_id'];
				}
			}
			
			// finally push the alert
			if (!empty($users)) {
				$Alerts->addMassAlert((array) $users, "custom", 0, (int) $mybb->user['uid'], array(
					'text' => $alertText
				), $forced);
			}
			// oops, no users here
			else {
				$errors[] = $lang->customalerts_error_nousers;
			}
			
			// errors won't stop here
			if (!$errors) {
				// output a friendly successful message, #4 and final forced check
				if ($forced) {
					flash_message($lang->customalerts_success_uidandgroup_forced, 'success');
					admin_redirect("index.php?module=" . MODULE . "&amp;action=pushalert");
				} else {
					flash_message($lang->customalerts_success_uidandgroup, 'success');
					admin_redirect("index.php?module=" . MODULE . "&amp;action=pushalert");
				}
			}
		}
	}
	// Breadcrumb
	$page->add_breadcrumb_item($lang->customalerts_pushalerts, "index.php?module=" . MODULE . "&amp;action=pushalert");
	// header before anything else
	$page->output_header($lang->customalerts);
	// errors
	if ($errors) {
		$page->output_inline_error($errors);
	}
	// generate the tab
	generate_tabs("pushalerts");
	// construct the main form
	$form = new Form("index.php?module=" . MODULE . "&amp;action=pushalert", "post");
	$form_container = new FormContainer($lang->customalerts_pushalerts);
	
	// store things in variables to clean up the code
	$methods_list = array(
		"uid" => $lang->customalerts_uid,
		"usergroup" => $lang->customalerts_group,
		"users" => $lang->customalerts_users,
		"all" => $lang->customalerts_all
	);
	$add_methods = $form->generate_select_box("methods[]", $methods_list, $methods, array(
		"multiple" => true,
		"id" => "methods"
	));
	$uid = $form->generate_text_box('uids', $userID);
	$text = $form->generate_text_area('text', $text, array(
		"id" => "text"
	));
	$forceonusers = $form->generate_yes_no_radio('forced', $forced, true);
	$group = $form->generate_group_select("group[]", $usergroups, array(
		"multiple" => true
	));
	$query = $db->simple_select("users", "uid, username");
	// users
	while ($user = $db->fetch_array($query)) {
		$userarray[$user['uid']] = $user['username'];
	}
	asort($userarray);
	$users = $form->generate_select_box("users[]", $userarray, $users, array(
		"multiple" => true
	));
	
	// click&insert function
	$replacement_fields = array(
		"{username}" => $lang->customalerts_username,
		"{date}" => $lang->customalerts_date
	);
	
	$personalisations = "<script type=\"text/javascript\">\n<!--\ndocument.write('{$lang->customalerts_personalize_message} ";
	foreach ($replacement_fields as $value => $name) {
		$personalisations .= " [<a href=\"#\" onclick=\"insertText(\'{$value}\', \$(\'text\')); return false;\">{$name}</a>], ";
	}
	$personalisations = substr($personalisations, 0, -2) . "');\n// --></script>\n";
	
	// actually construct the form
	$form_container->output_row($lang->customalerts_add_methods . " <em>*</em>", $lang->customalerts_add_methods_desc, $add_methods);
	// methods
	$form_container->output_row($lang->customalerts_uid, $lang->customalerts_uid_desc, $uid, 'uid', array(), array(
		'id' => 'uid'
	));
	$form_container->output_row($lang->customalerts_users, $lang->customalerts_users_desc, $users, 'users', array(), array(
		'id' => 'users'
	));
	$form_container->output_row($lang->customalerts_group, $lang->customalerts_group_desc, $group, 'group', array(), array(
		'id' => 'usergroup'
	));
	// text
	$form_container->output_row($lang->customalerts_text, $personalisations, $text, 'text');
	// force on users
	$form_container->output_row($lang->customalerts_options_forceonuser, $lang->customalerts_options_forceonuser_desc, $forceonusers, 'options');
	
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
				new Peeker($("methods"), $("uid"), /uid/, false);
				new Peeker($("methods"), $("usergroup"), /usergroup/, false);
				new Peeker($("methods"), $("users"), /users/, false);
			}
			function insertText(value, textarea)
			{
				// Internet Explorer
				if(document.selection) {
					textarea.focus();
					var selection = document.selection.createRange();
					selection.text = value;
				}
				// Firefox
				else if(textarea.selectionStart || textarea.selectionStart == "0") {
					var start = textarea.selectionStart;
					var end = textarea.selectionEnd;
					textarea.value = textarea.value.substring(0, start)	+ value	+ textarea.value.substring(end, textarea.value.length);
				}
				else {
					textarea.value += value;
				}
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
		'link' => "index.php?module=" . MODULE,
		'description' => $lang->customalerts_overview_desc
	);
	$sub_tabs['pushalerts'] = array(
		'title' => $lang->customalerts_pushalerts,
		'link' => "index.php?module=" . MODULE . "&amp;action=pushalert",
		'description' => $lang->customalerts_pushalerts_desc
	);
	/*$sub_tabs['logs'] = array(
		'title' => $lang->customalerts_logs,
		'link' => "index.php?module=" . MODULE . "&amp;action=logs",
		'description' => $lang->customalerts_logs_desc
	);*/
	$sub_tabs['documentation'] = array(
		'title' => $lang->customalerts_documentation,
		'link' => "index.php?module=" . MODULE . "&amp;action=documentation",
		'description' => $lang->customalerts_documentation_desc
	);
	
	$page->output_nav_tabs($sub_tabs, $selected);
}

// debugging stuff
function customalerts_debug($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit;
}