<?php
// header and such
$l['customalerts'] = "Custom Alerts";
$l['customalerts_overview'] = "Overview";
$l['customalerts_overview_desc'] = "Here you can find all the informations related to this alert system.";
$l['customalerts_overview_innerdesc'] = "Hi, my name is Filippo better known as Shade. I'm an italian developer and web designer with passion for MyBB since 2012. I've never developed plugins before: I prefer to search for quality plugins and help their mainteiners to keep it as up to date as possible, introducing other features and extending them. This is my 2nd extension for an existing plugin and MyAlerts totally worths it. I've developed 9 extra actions and I've planned to add more during 2013, while Custom Alerts brings the ability to manage your custom alerts and push them to your users easily. I hope you're having fun using my extensions. If you wanna request one, please do so PMing me on Community Forums @ http://community.mybb.com.<br><br>~ Shade";
$l['customalerts_enabled'] = "Custom alerts";
$l['customalerts_enabled_desc'] = "Enables or disables the ability to push custom alerts to your users from the dedicated <a href=\"index.php?module=user-customalerts\">ACP section</a>.";
$l['customalerts_pushalerts'] = "New alert to single user";
$l['customalerts_pushalerts_desc'] = "Push a new alert to an user of your choice.";
$l['customalerts_pushalertsgroup'] = "New alert to usergroup";
$l['customalerts_pushalertsgroup_desc'] = "Push a new alert to all users of an usergroup of your choice.";
$l['customalerts_documentation'] = "Documentation";
$l['customalerts_documentation_desc'] = "Full documentation on how to use MyAlerts and Custom Alerts.";
$l['customalerts_push_button'] = "Alert the user";
$l['customalerts_push_group_button'] = "Alert those users";

// forms & pages stuff
$l['customalerts_uid'] = "User ID";
$l['customalerts_uid_desc'] = "Enter the user ID you would like to push the alert to.";
$l['customalerts_text'] = "Alert text";
$l['customalerts_text_desc'] = "Enter the text you would like to include in the alert.";
$l['customalerts_group'] = "Usergroup(s)";
$l['customalerts_group_desc'] = "Select the usergroup(s) you would like to push the alert to. Hold CTRL to select multiple usergroups.";
$l['customalerts_options'] = "Options";
$l['customalerts_options_forceonuser'] = "Force on user";
$l['customalerts_options_forceonuser_desc'] = "If yes, this option gives you the ability to bypass user settings and display the alert regardless of them. This option is active by default.";

// errors & success
$l['customalerts_error_nouid'] = "The User ID you've entered was invalid, or the user doesn't exist. Please ensure that the User ID exists before attempting to push him a new alert.";
$l['customalerts_error_nogroup'] = "You didn't selected any usergroup to push a new alert to. Please select one from the selectable list below.";
$l['customalerts_error_notext'] = "You didn't entered any text for your alert";
$l['customalerts_success'] = "The alert was pushed successfully to the specified user.";
$l['customalerts_success_forced'] = "The alert was pushed successfully to the specified user with forcing option set to on, so the user <em>surely</em> received the alert.";
$l['customalerts_success_group'] = "The alert was pushed successfully to the users in the specified usergroup(s).";
$l['customalerts_success_group_forced'] = "The alert was pushed successfully to the users in the specified usergroup(s) with forcing option set to on, so the users <em>surely</em> received the alert.";