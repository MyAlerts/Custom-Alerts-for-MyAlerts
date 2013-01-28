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
// require useful class
require_once  MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;

$lang->load("customalerts");

// Breadcrumb
$page->add_breadcrumb_item($lang->customalerts, "index.php?module=".MODULE);

// Begin!
// Docs
if($mybb->input['action'] == "documentation") {	
	// Breadcrumb
	$page->add_breadcrumb_item($lang->customalerts_documentation, "index.php?module=".MODULE."&amp;action=documentation");	
	$page->output_header($lang->customalerts);
	// generate the tab
	generate_tabs("documentation");
	
	// construct the main table
	$table = new Table;
	
	// actually construct the table header
	$table->construct_header($lang->customalerts_doc_info, array("width"=>"25%"));
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
	
	// output it!
	$table->output($lang->customalerts_documentation);
}
// Push a new alert directly
elseif($mybb->input['action'] == "pushalert") {
	if($mybb->request_method == "post") {
		// cleaning up the code!
		$methods = $mybb->input['methods'];
		$forced = $mybb->input['forced'];
		$userID = $mybb->input['uids'];
		$usergroups = $mybb->input['group'];
		$text = $mybb->input['text'];
		$users = $mybb->input['users'];
		
		// errors
		// no methods
		if(!$methods) {
			$errors[] = $lang->customalerts_error_nomethods;
		}
		if(is_array($methods)) {
			// no/unexisting UIDs
			if(in_array('uid', $methods)) {
				
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
			if(in_array('usergroup', $methods) AND empty($usergroups)) {
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
			
			$rules_parser = array(
               "allow_html" => 1,
               "allow_mycode" => 1,
               "allow_smilies" => 1,
               "allow_imgcode" => 1
            );
			
			$alertText = $parser->parse_message($text, $rules_parser);
			
			// UIDs
			if(in_array('uid', $methods)) {
				// clean up the string from whitespaces
				$userID = preg_replace("/[^0-9,]/", "", $userID);
				// split them in an array and clean it up
				$users_uid = array_values(array_filter(explode(",", $userID)));
			}
			// usergroup
			if(in_array('usergroup', $methods)) {
				if(count($methods) > 1) {
					$separator = " OR ";
				}
				
				$usergroups = "'".implode("','", $usergroups)."'";
				$usergroups = "usergroup IN ({$usergroups})";
			}
			// users
			if(in_array('users', $methods)) {
				$users_users = $users;
			}
			// all
			if(in_array('all', $methods)) {
				$all = 1;
			}
			
			// having some fun with arrays!
			settype($users_uid, "array");
			settype($users_users, "array");
			$users = array_unique(array_merge($users_uid, $users_users));
			// fixes #1
			if(!empty($users)) {
				$uids = "'".implode("','", $users)."'";
				$users = "uid IN ({$uids})";
			}
			
			// our main query
			if($all) {
				$query = $db->simple_select("users", "uid, myalerts_settings");
			}
			else {
				$query = $db->simple_select("users", "uid, myalerts_settings", "{$users}{$separator}{$usergroups}");
			}
							
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
	}
	// Breadcrumb
	$page->add_breadcrumb_item($lang->customalerts_pushalerts, "index.php?module=".MODULE."&amp;action=pushalert");
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
	$methods_list = array(
				"uid" => $lang->customalerts_uid,
				"usergroup" => $lang->customalerts_group,
				"users" => $lang->customalerts_users,
				"all" => $lang->customalerts_all);
	$add_methods = $form->generate_select_box("methods[]", $methods_list, $methods, array("multiple"=>true, "id"=>"methods"));
	$uid = $form->generate_text_box('uids', $userID);
	$text = $form->generate_text_area('text', $text, array("id" => "text"));
	$options = $form->generate_check_box('forced', '1', $lang->customalerts_options_forceonuser, array('checked' => 1, 'id' => 'forced'))."<br /><small>{$lang->customalerts_options_forceonuser_desc}</small>";
	$group = $form->generate_group_select("group[]", $usergroups, array("multiple"=>true));
	$query = $db->simple_select("users", "uid, username");
	// users
	while($user = $db->fetch_array($query)) {
		$userarray[$user['uid']] = $user['username'];
	}
	asort($userarray);
	$users = $form->generate_select_box("users[]", $userarray, $users, array("multiple"=>true));
	
	// click&insert function
	$replacement_fields = array(
		"{username}" => $lang->customalerts_username,
		"{date}" => $lang->customalerts_date,
	);
	
	$personalisations = "<script type=\"text/javascript\">\n<!--\ndocument.write('{$lang->customalerts_personalize_message} ";
	foreach($replacement_fields as $value => $name)
	{
		$personalisations .= " [<a href=\"#\" onclick=\"insertText(\'{$value}\', \$(\'text\')); return false;\">{$name}</a>], ";
	}
	$personalisations = substr($personalisations, 0, -2)."');\n// --></script>\n";
	
	// actually construct the form
	$form_container->output_row($lang->customalerts_add_methods." <em>*</em>", $lang->customalerts_add_methods_desc, $add_methods);
	// methods
	$form_container->output_row($lang->customalerts_uid, $lang->customalerts_uid_desc, $uid, 'uid', array(), array('id' => 'uid'));
	$form_container->output_row($lang->customalerts_users, $lang->customalerts_users_desc, $users, 'users', array(), array('id' => 'users'));
	$form_container->output_row($lang->customalerts_group, $lang->customalerts_group_desc, $group, 'group', array(), array('id' => 'usergroup'));
	// text
	$form_container->output_row($lang->customalerts_text, $personalisations, $text, 'text');
	// options
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

// debugging stuff
function debug($data) {
   echo "<pre>";
      print_r($data);
   echo "</pre>";
   exit;
}