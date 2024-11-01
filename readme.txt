=== wp-w3-validation ===
Contributors: zigon
Donate link: http://www.haveyougotanypets.com
Tags: xhtml validation, css validation, javascript validation, entry validation, validator
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 0.1

This plugin Validates an entry, then shows the html and or css validity on the page.


== Description ==


Validates a page or post and places an image on showing its html and or css validity
(only visible to the person editing the entry - just like the “Edit this entry” link).

To use just add

`<?php if(function_exists('wp_w3_validation')) {wp_w3_validation();} ?>`

within the main Loop of your template php file,
preferably right next to the function call `<?php edit_post_link(’Edit this entry.’ ” ‘ ‘); ?>`


== Installation ==

1. **ADD FILES:** Add the contents(folder) in the *wp-w3-validation.zip* to your
wordpress’s plugin directory (http://www.your-site.com/wp-content/plugins/).

2. **ACTIVATE PLUGIN:** Activate the plugin through the 'Plugins' menu in WordPress

3. **ADD VISUAL OUTPUT:** Place `<?php if(function_exists('wp_w3_validation'))
{wp_w3_validation();} ?>` in your templates wherever you wish to see the result
of the pages validity preferably somewhere near
`edit_post_link(’Edit this entry.’ ” ‘ ‘);` (In page.php and single.php in the
default wordpress theme.) NOTE: This tag should appear somewhere within
[The Loop](http://codex.wordpress.org/The_Loop_in_Action/) (




**Uninstallation**

1. **REMOVE VISUAL OUPUT** Go though your theme and remove
`<?php if(function_exists('wp_w3_validation')) {wp_w3_validation();} ?>` from
wherever you added it to your templates.

2. **REMOVE FILES** Remove the folder *wp-w3-validation* from your wordpress’s
plugin directory (http://www.your-site.com/wp-content/plugins/). You can do this
manually or though the deactivation -> remove plugin option in wordpress's admin
section.

3. **REMOVE DATABASE ENTRYS** Look though your wordpress database's `wp_options`
table and remove the entry named `wp_w3_validation_admin_options`.


== Frequently Asked Questions ==

= I need help =

Dont Panic - checkout the [plugin's page](http://www.haveyougotanypets.com/wp-w3-validation/) on [my site](http://www.haveyougotanypets.com) for more info.
Or send me a message to:

`
$name='wp-w3-validation';
$domain='haveyougotanypets';
echo $email = $name . '@' . $domain . '.com'; // no spam
`

= I wish to improve this plugins speed =

1. As this plugin is really only meant for entry editors/admins only the plugin
only adds its code when it knows a user has editing rights. As such it will not
slow the end users page loading anywhere near so much as the admins.
(The following two points do not apply to end users)

2. The main reason for slower page loading times is the requirement of an external
call to various validators APIs. If your feeling extra enthusiastic you could install
your own instances of the validators on your server. If so then you can change
the locations of in the `wp_w3_validation` classes global vars `$validator_...`
See the validators homepages for more details.

3. This plugin uses its own stylesheet hence another request is needed to complete
the page to improve speed you may wish to combine the contents of `wp_w3_validation_main.css`
with your main css file. After doing so you can comment out the add_action in core.php to
stop the stylesheet from loading

= I wish to only ever call one of the validators =

you can call the individual validators by making a call to the validator hence:

`$wp_w3_validation = new wp_w3_validation();`

`$wp_w3_validation->display_html_validity($display_type); // for xhtml validation. $display_type = "text" or "image"`

`$wp_w3_validation->display_css_validity($display_type); // for css validation. $display_type = "text" or "image"``

*Note: i will try and keep these function calls the same but I'm NOT promising
anything on future updates.*


== Screenshots ==

1. coming soon

== Version History ==

> **Future Versions**

* Javascript validation.
* [Started] Validation of all pages via admin. use a wordpress loop of all post and pages (dont foget to sleep for 1 second)
* Validation of page shown in page/post editor in admin section (maybe as bkground colour)
* Warnings ontop of invalid, valid
* Display no of errors on each page/post
* Auto append function after edit page call (no need to change theme files)

> **v0.2** (Trunk Development)

*Page:*

* Display methods using text or image

*Admin:*

* Options to change display method (text or image)

*Source:*

* Complete restructuring of code using classes for validator and admin validator
can now be used independently by another plugin by including the validator.php file
and creating a new instance of the wp_w3_validation class `$wp_w3_validation = new wp_w3_validation();`
* Updated `can_check()` function
* Stylesheets now loaded via `wp_register_style()` and `wp_enqueue_style()` functions
* Various Globals added

> **v0.1**

*Post:*

* XTHML Validation.
* CSS Validation.

*Admin:*

* Quick how to install.
* Ability to change the display of any of the validations.

*Source:*

* Fully commented code.
* Easy ability to change validator engines vie global vars.
* Ability to call only one of the editors (see FAQ).
* Speed ups by only making calls to validators and stylesheets when the viewer is an editor.
* Lang `_e('')` tags