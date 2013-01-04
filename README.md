Custom Alerts for MyAlerts
===============================

**IMPORTANT**: *Thanks to this GitHub Repo you can track bugfixes and keep your Custom Alerts for MyAlerts copy up to date, but keep in mind that this is a <strong>development version</strong>. Therefore, you may encounter errors and relevant bugs using this version, although I will try to leave its code as functional as possible.*

> **Current version** 1.0  
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

At the moment, Custom Alerts for MyAlerts works in a very simple way: in a dedicated section under Users & Groups module group you can push new notifications to an user or usergroup(s) of your choice.

Custom Alerts for MyAlerts adds one setting into ACP and one setting into UCP. A [custom version of MyAlerts][2] *is required* for it to work properly: that's because standard MyAlerts copies need a UCP setting set to "on", whereas with this plugin you are able to "force" the alert display regardless of users settings. This feature may be useful for rules updates you **want** to display to users, or important feature updates and announcements.

*The customizations present in the MyAlerts copy I made will probably be in the next official MyAlerts version: I'd recommend to wait until a MyAlerts official update is out and use this extension + custom MyAlerts version on a test board, just to try it and report bugs or anything else.*

[2]: https://github.com/Shade-/MyAlerts

Looking forward to the future
-----------------------------------

I've planned to update progressively Custom Alerts for MyAlerts, making it a fully-integrated MyAlerts manager. Hopefully you'll be soon able to:

* Heavily customize your alerts, including custom publishing date and combining conditions. Eg.: send alert to users X, Y and Z in Registered and Premium Users usergroups where posts => 200 and reputation => 20, @ XX/YY/2013 20:10
* Save your alerts and manage them
* View existing alerts and delete them from ACP
* And more!

Development has started, this Repo will be updated accordingly and progressively. Suggestions and Pull Requests are always appreciated!