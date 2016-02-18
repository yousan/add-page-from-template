=== Plugin Name ===
Contributors: hogetan
Tags: page, develop
Requires at least: 4.2
Tested up to: 4.4
Stable tag: 4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Are you tired to create pages through different environments?


* Add **Virtual** WordPress 'page' from php template file automatically
* Not needed to add 'page' from admin panel

 The page-xxx.php would be located at 'themes/your-theme/page-foobar.php' then the page will be created as 'http://www.example.com/foobar/' without admin panel.
  
== Installation ==

1. Download the Add Page From Templete plugin.
2. Decompress the ZIP file and upload the contents of the archive intot /tp-content/plugins/.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Activate Custom permalink setting. (Make sure .htaccess file is generated)

== Screenshots ==

1. Virtual page would be made.
2. Just make a page-foobar.php at your themes/pages directory.

== Frequently Asked Questions ==

= How to add the virtual page? =

Put your template file into your 'themes/yourtheme/pages/'.
('themes/yourtheme/pages/': default)
Make the template page name 'page-xxx.php'.

= I put file but I cannot see the page. Why? =

Check the template file located at correctly directory.
The default directory is 'wp-content/themes/yourtheme/pages/'.

Check whether the same slug page * NOT EXISTS *.
You can check the conflict at the plugin setting page.

Check custom permalink setting is activated.

Please send the problem to the author.
https://github.com/yousan/add-page-from-template/issues

== Changelog ==

= Version 0.4.4.1 =
* Fixed: Remove critical bug.

= Version 0.4.4 =
* Fixed: Set Title when called at wp_title().

= Version 0.4.3.1 =
* Fixed: Set title correctly.

= Version 0.4.3 =
* Upgraded: Show title from template file.

= Version 0.4.2 =
* Fixed: Removed a warning.

= Version 0.4.1 =
* Fixed: For old WP_version <= 4.3.

= Version 0.4 =
* Fixed: APFT Overrides global $post. Now we can use function/tags which refers to global $post such as get_post_type().

= Version 0.3 =
* Fixed: Removed debug code.
* Fixed: Added pagename when apft virtual page loaded.

= Version 0.2 =
* Fixed: version number.
* Fixed: Reload pages when the plugin is activated.

= Version 0.1 =
* Fixed: Remove Warning.

= Version 0.0.1 =
* Upgraded: Added template list table at the setting page.

= Version 0.0.0.1 =
  * Plugin is born.
