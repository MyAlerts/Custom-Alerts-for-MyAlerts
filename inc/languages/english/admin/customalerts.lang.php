<?php
// header and such
$l['customalerts'] = "Custom Alerts";
$l['customalerts_overview'] = "Overview";
$l['customalerts_overview_desc'] = "Here you can find all the informations related to this alert system.";
$l['customalerts_overview_innerdesc'] = "Hi, my name is Filippo better known as Shade. I'm an italian developer and web designer with passion for MyBB since 2012. I've never developed plugins before: I prefer to search for quality plugins and help their mainteiners to keep it as up to date as possible, introducing other features and extending them. This is my 2nd extension for an existing plugin and MyAlerts totally worths it. I've developed 9 extra actions and I've planned to add more during 2013, while Custom Alerts brings the ability to manage your custom alerts and push them to your users easily. I hope you're having fun using my extensions. If you wanna request one, please do so PMing me on Community Forums @ http://community.mybb.com.<br><br>~ Shade";
$l['customalerts_enabled'] = "Custom alerts";
$l['customalerts_enabled_desc'] = "Enables or disables the ability to push custom alerts to your users from the dedicated <a href=\"index.php?module=user-customalerts\">ACP section</a>.";
$l['customalerts_pushalerts'] = "New alert";
$l['customalerts_pushalerts_desc'] = "Push a new alert to your users according to specified methods. You might select more than one condition holding CTRL.";
$l['customalerts_documentation'] = "Documentation";
$l['customalerts_documentation_desc'] = "Full documentation on how to use MyAlerts and Custom Alerts.";
$l['customalerts_push_button'] = "Alert those user(s)";

// forms & pages stuff
$l['customalerts_uid'] = "User ID(s)";
$l['customalerts_uid_desc'] = "Enter the user IDs you would like to push the alert to. You might insert more than one UID, separated with a coma. Your input will be sanitized and the system will check if any of UIDs entered is invalid. If so, you'll be notified.";
$l['customalerts_text'] = "Alert text";
$l['customalerts_text_desc'] = "Enter the text you would like to include in the alert.";
$l['customalerts_group'] = "Usergroup(s)";
$l['customalerts_group_desc'] = "Select the usergroup(s) you would like to push the alert to. Hold CTRL to select multiple usergroups.";
$l['customalerts_users'] = "User(s)";
$l['customalerts_users_desc'] = "Select the user(s) you would like to push the alert to. Hold CTRL to select multiple users.";
$l['customalerts_options'] = "Options";
$l['customalerts_options_forceonuser'] = "Force on users";
$l['customalerts_options_forceonuser_desc'] = "If yes, this option gives you the ability to bypass users settings and display the alert regardless of them. This option is active by default.";
$l['customalerts_add_methods'] = "Method(s) of generation";
$l['customalerts_add_methods_desc'] = "Choose the method(s) of the alert generation. Hold CTRL to select multiple methods. Please note that multiple methods <b>will be chained, but adding and not substracting them!</b>!<br />
Eg.: assuming userX where X = UID, if you choose to use both UID and users methods and you insert \"1,2,3\" for the latter, \"user1, user5, user8\" for the former, an alert will be generated for: <i>user1, user2, user3, user5 and user8</i>.<br />
The same happens with usergroup method. If you choose to use UID and usergroup methods, an alert will be generated both for users present in the specified usergroups, and for the specified UIDs.";

// errors & success
$l['customalerts_error_nouid'] = "You didn't entered any UID.";
$l['customalerts_error_noexistinguid'] = "One or more User IDs you've entered were invalid. Please ensure that the User IDs exist before attempting to push them a new alert.";
$l['customalerts_error_nogroup'] = "You didn't selected any usergroup to push a new alert to. Please select one from the selectable list below.";
$l['customalerts_error_notext'] = "You didn't entered any text for your alert.";
$l['customalerts_error_debug'] = "An unknown error occured. Please contact the developer specifying how did you get this error.";
$l['customalerts_error_nousers'] = "The usergroup(s) you specified doesn't contain any user.";
$l['customalerts_error_nomethods'] = "You didn't specified any condition.";
$l['customalerts_success'] = "The alert was pushed successfully to the specified user(s).";
$l['customalerts_success_forced'] = "The alert was pushed successfully to the specified user(s) with forcing option set to on, so the user(s) <em>surely</em> received the alert.";
$l['customalerts_success_group'] = "The alert was pushed successfully to the users in the specified usergroup(s).";
$l['customalerts_success_group_forced'] = "The alert was pushed successfully to the users in the specified usergroup(s) with forcing option set to on, so the users <em>surely</em> received the alert.";
$l['customalerts_success_uidandgroup'] = "The alert was pushed successfully to the users in the specified methods.";
$l['customalerts_success_uidandgroup_forced'] = "The alert was pushed successfully to the users in the specified methods with forcing option set to on, so the users <em>surely</em> received the alert.";