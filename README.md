Custom Alerts for MyAlerts
===============================

**IMPORTANT**: *Thanks to this GitHub Repo you can track bugfixes and keep your Custom Alerts for MyAlerts copy up to date, but keep in mind that this is a <strong>development version</strong>. Therefore, you may encounter errors and relevant bugs using this version, although I will try to leave its code as functional as possible.*

> **Current version** 1.1.1  
> **Dependencies** MyAlerts, available to download [here][1], and PluginLibrary, which is required to install MyAlerts  
> **Author** Shade  
> **Special thanks goes to...** euantor who developed MyAlerts, the best notification plugin ever made for MyBB installations.

[1]: http://mods.mybb.com/view/MyAlerts

General
-------

Custom Alerts for MyAlerts is an extension made for euantor's [MyAlerts][1] plugin with the aim to push custom notifications from the Admin Control Panel in a dedicated section.

If you have any feature request, suggestion, or you want to report any issue, please let me know opening a new issue on GitHub. Your contribute is very important to me and helps me into making Custom Alerts for MyAlerts more complete on every commit. 

Main features
-------------

At the moment, Custom Alerts for MyAlerts works in a very simple way: in a dedicated section under Users & Groups module group you can push new notifications to an user or usergroup(s) of your choice. You can choose one or multiple methods of user selection, by UID, by username, by usergroup or all. If multiple methods are used, alert generation will be chained.

Custom Alerts for MyAlerts adds one setting into ACP and one setting into UCP.

Looking forward to the future
-----------------------------------

I've planned to update progressively Custom Alerts for MyAlerts, making it a fully-integrated MyAlerts manager. Hopefully you'll be soon able to:

* Heavily customize your alerts, including custom publishing date and combining conditions. Eg.: send alert to users X, Y and Z in Registered and Premium Users usergroups where posts => 200 and reputation => 20, @ XX/YY/2013 20:10
* Save your alerts and manage them
* View existing alerts and delete them from ACP
* And more!

Development has started, this Repo will be updated accordingly and progressively. Suggestions and Pull Requests are always appreciated!
