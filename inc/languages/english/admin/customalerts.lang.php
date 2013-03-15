<?php
// header and such
$l['customalerts'] = "Custom Alerts";
$l['customalerts_overview'] = "Overview";
$l['customalerts_overview_desc'] = "Here you can find all the informations related to this alert system.";
$l['customalerts_overview_innerdesc'] = "Hi, my name is Filippo better known as Shade. I'm an italian developer and web designer with passion for MyBB since 2012, member of MyAlerts team made up of Euan T., JordanMussi, Wildcard and me. Our goal is to make the best notification system for MyBB grow up, eventually overcoming other platforms systems. This is my favorite extension for an existing plugin and MyAlerts totally worths it. Custom Alerts brings the ability to manage your custom alerts and push them to your users easily. Please have a brief look at <a href=\"index.php?module=user-customalerts&action=documentation\">the documentation</a> to see how it works. If you wanna request me one plugin, please do so PMing me on Community Forums @ http://community.mybb.com.<br><br>~ Shade";
$l['customalerts_enabled'] = "Custom alerts";
$l['customalerts_enabled_desc'] = "Enables or disables the ability to push custom alerts to your users from the dedicated <a href=\"index.php?module=user-customalerts\">ACP section</a>.";
$l['customalerts_pushalerts'] = "New alert";
$l['customalerts_pushalerts_desc'] = "Push a new alert to your users according to specified methods. You might select more than one method holding CTRL.";
$l['customalerts_documentation'] = "Documentation";
$l['customalerts_documentation_desc'] = "Full documentation on how to use MyAlerts and Custom Alerts.";
$l['customalerts_push_button'] = "Alert those user(s)";
$l['customalerts_username'] = "Your username";
$l['customalerts_date'] = "Date and time";
$l['customalerts_personalize_message'] = "Personalize your alert:";

// forms & pages stuff
$l['customalerts_all'] = "Alert all users";
$l['customalerts_uid'] = "Alert by user ID(s)";
$l['customalerts_uid_desc'] = "Enter the user IDs you would like to push the alert to. You might insert more than one UID, separated with a coma. Your input will be sanitized and the system will check if any of UIDs entered is invalid. If so, you'll be notified.";
$l['customalerts_text'] = "Alert text";
$l['customalerts_text_desc'] = "Enter the text you would like to include in the alert. You can also use the power of HTML and MyCode.";
$l['customalerts_group'] = "Alert by usergroup(s)";
$l['customalerts_group_desc'] = "Select the usergroup(s) you would like to push the alert to. Hold CTRL to select multiple usergroups.";
$l['customalerts_users'] = "Alert by username(s)";
$l['customalerts_users_desc'] = "Select the user(s) you would like to push the alert to. Hold CTRL to select multiple users.";
$l['customalerts_options'] = "Options";
$l['customalerts_options_forceonuser'] = "Force on users";
$l['customalerts_options_forceonuser_desc'] = "If yes, this option gives you the ability to bypass users settings and display the alert regardless of them. This option is active by default.";
$l['customalerts_add_methods'] = "Method(s) of generation";
$l['customalerts_add_methods_desc'] = "Choose the method(s) of the alert generation. Hold CTRL to select multiple methods. See the <a href=\"index.php?module=user-customalerts&action=documentation\">documentation</a> to know how to use this feature.";

// errors & success
$l['customalerts_error_nouid'] = "You didn't entered any UID.";
$l['customalerts_error_noexistinguid'] = "One or more User IDs you've entered were invalid. Please ensure that the User IDs exist before attempting to push them a new alert.";
$l['customalerts_error_nogroup'] = "You didn't selected any usergroup to push a new alert to. Please select one from the selectable list below.";
$l['customalerts_error_notext'] = "You didn't entered any text for your alert.";
$l['customalerts_error_debug'] = "An unknown error occured. Please contact the developer specifying how did you get this error.";
$l['customalerts_error_nousers'] = "No users were returned from the method(s) of generation you've specified. Please retry.";
$l['customalerts_error_nomethods'] = "You didn't specified any method.";
$l['customalerts_success'] = "The alert was pushed successfully to the specified user(s).";
$l['customalerts_success_forced'] = "The alert was pushed successfully to the specified user(s) with forcing option set to on, so the user(s) <em>surely</em> received the alert.";
$l['customalerts_success_group'] = "The alert was pushed successfully to the users in the specified usergroup(s).";
$l['customalerts_success_group_forced'] = "The alert was pushed successfully to the users in the specified usergroup(s) with forcing option set to on, so the users <em>surely</em> received the alert.";
$l['customalerts_success_uidandgroup'] = "The alert was pushed successfully to the users in the specified methods.";
$l['customalerts_success_uidandgroup_forced'] = "The alert was pushed successfully to the users in the specified methods with forcing option set to on, so the users <em>surely</em> received the alert.";

// docs
$l['customalerts_doc_info'] = "Information";
$l['customalerts_doc_description'] = "Description";
$l['customalerts_doc_overview'] = "Overview";
$l['customalerts_doc_overview_desc'] = "<b>MyAlerts</b> is an integrated notification system for your board. Your users can be notified of certain events which happens regularly in your board: they can decide which notifications to receive within their Control Panel. <b>Custom Alerts for MyAlerts</b> adds a manager into your Admin Control Panel in which you can push new notifications to users of your choice.";
$l['customalerts_doc_features'] = "Features";
$l['customalerts_doc_features_desc'] = "<b>Custom Alerts for MyAlerts</b> comes with a dedicated section in the ACP: the one you're currently watching. You can choose out of 3 different methods to select the recipents of the alert which is safely generated and stored in your database: you can send an alert to specific UIDs, usernames or usergroups.<br />
The extension also adds a setting into MyAlerts Settings group that you can turn on and off as you like. It controls the module generation, so turning it off will hide the entire module until the setting turns back active.";
$l['customalerts_doc_newalert'] = "Generate a new alert";
$l['customalerts_doc_newalert_desc'] = "Generate a new alert have never been so easy. Just click on the <b>New alert</b> tab above and set your preferred method of selecting users. Once you've finished setting them, just enter any text you would like to push to the specified users and click <b>Make it happen!</b> button at the bottom of the page.<br />
You are able to customize your alert with the handy buttons placed above the alert textarea. You can insert your formatted name as <b>{username}</b> and the timestamp of the alert generation as <b>{date}</b>: they aren't included in the alert by default, so it's up to you deciding whether to include them or not.";
$l['customalerts_doc_methods'] =  "Methods of alert generation";
$l['customalerts_doc_methods_desc'] =  "You can currently choose up to 4 methods to select users to push the alert to. They are:<br/><ul><li><b>Alert by User ID(s)</b>: choosing this method, you'll be asked to enter a list of user IDs, each of them separated by a coma. To prevent PHP or MySQL errors to be thrown, your input will be sanitized and every user ID you'll enter will be checked to ensure the users exist. If they won't exist, a friendly error will be thrown and no alert will be generated.</li>
<li><b>Alert by usergroup(s)</b>: choosing this method, you'll be asked to select the usergroups you want to alert. All users which belongs to the specified usergroups will be notified.</li>
<li><b>Alert by username(s)</b>: choosing this method, you'll be asked to select the users you want to alert from a list of all users registered to your board. This option is very handy in case you don't want to look for a specific user ID, but for large boards this should be avoided since the userlist might be very long and time-wasteful.</li>
<li><b>Alert all users</b>: choosing this method, all other methods will be overwritten since an alert will be pushed to all users currently registered to your board.</li></ul>
You can optionally enable the <b>Force on users</b> option at the end of the page, which let you alert the specified users regardless of their settings.<br />
Please note that selecting multiple methods <b>will make the final userlist grow, but adding and not substracting users!</b>!<br />
Eg.: assuming userX where X = UID, if you choose to use both UID and users methods and you insert \"1,2,3\" for the latter, \"user1, user5, user8\" for the former, an alert will be generated for: <i>user1, user2, user3, user5 and user8</i>.<br />
The same happens with usergroup method. If you choose to use UID and usergroup methods, an alert will be generated both for users present in the specified usergroups, and for the specified UIDs.";
$l['customalerts_doc_otherplugins'] =  "Are you having fun? Don't stop!";
$l['customalerts_doc_otherplugins_desc'] =  "Custom Alerts for MyAlerts isn't the only plugin I've made so far. If you're having fun using it, you should consider having a look at:
<ul><li><a href=\"http://bit.ly/VuGv2m\">ProjectX</a>, an awesome theme for the Administration Control Panel of MyBB</li>
<li><a href=\"http://bit.ly/YJxSgf\">Moderation Alerts Pack</a>, a collection of 11 extra alert types for MyAlerts related to moderation actions</li>
<li><a href=\"http://bit.ly/WDFaCD\">Plugins Alerts Pack</a>, a collection of 4 extra alert types (and growing) for MyAlerts related to existing and widely used plugins, including MyNetwork Profile Comments, MySupport, Announcement and core subscriptions</li>
<li><a href=\"http://bit.ly/XBPPzk\">iDLChat</a>, a revolutionary chat for MyBB currently in development stages</li></ul>";