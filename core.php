<?php
/*
Plugin Name: wp-w3-validation
Plugin URI: http://www.haveyougotanypets.com/wp-w3-validation/
Version: 0.1
Author: zigon
Author URI: http://www.haveyougotanypets.com
Description: Places an image on an entry showing its validity (only visible to the person editing the entry - just like the "Edit this entry" link). To use just add <?php if(function_exists('wp_w3_validation')){wp_w3_validation();} ?> to your template file, preferably right next to the function call edit_post_link('Edit this entry.').

Copyright 2009  Richard Smith  (rich@haveyougotanypets.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

/*
 * ACKNOWLEDGEMENTS
 *
 * I would like to use thank Roland Rust (http://wordpress.designpraxis.at)
 * for the Batch Validator plugin which i used as a basic template for this one.
 *
 * As well as Ronald Huereca at devlounge.net
 * (http://www.devlounge.net/extras/how-to-write-a-wordpress-plugin)
 * for the How to Write a Wordpress Plugin guide which i also used as a basic
 * template for this one.
 *
 */

/*
 * INSTALLATION
 *
 *
 * This plugin shows a page or post author(**only**) the xhtml and css validity
 * of an entry by using the:
 * http://validator.w3.org and http://jigsaw.w3.org/css-validator/validator
 * APIs to validate the entry, then places images corresponding to the result
 * on the page or post.
 *
 * To use add:
 *
 * <?php if(function_exists('wp_w3_validation')) {wp_w3_validation();} ?>
 *
 * To your theme wherever you would most like the validity output,
 * preferably next to the `<?php edit_post_link(’Edit this entry.’ ” ‘ ‘); ?>`.
 *
 */



/**
 * TODO:
 *
 * Check in a post before useing post var
 *
 * add check is_home()
 *
 * make work for previews (maybe use $post->guid insted of ID)
 *
 * write theme checker as well as entry checker
 * currently the post or entry is checked in its form (ie the single or page
 * template as it appers) may be possible to just check the_content code - will
 * need to find DTD
 *
 */

    /***********************************************************************
     *
     *  Plugin code
     *
     **********************************************************************/

// Check to see if another plugin has created this class already or registered function
if (!class_exists("wp_w3_validation_admin") && !class_exists("wp_w3_validation") && !function_exists("wp_w3_validation")) {


/***********************************************************************
*
*  Start up plugin
*
**********************************************************************/

    // include classes
    require_once('includes/validator_admin.php');
    require_once('includes/validator.php');

    // create new validator and admin
    $wp_w3_validation_admin = new wp_w3_validation_admin();
    $wp_w3_validation = new wp_w3_validation();

    //Actions and Filters
    if (isset($wp_w3_validation_admin) && isset($wp_w3_validation)) {

        // ACTIONS

        // Add style sheet (only if page user can edit page otherwise no point)
        add_action('wp_print_styles', array(&$wp_w3_validation, 'add_stylesheet'));

        // Call the init function to initialise sb options
        add_action('wp_w3_validation_core',  array(&$wp_w3_validation_admin, 'init'));

        // Init and add the admin panel
        add_action('admin_menu', array(&$wp_w3_validation_admin, 'init_admin_panel'));


        // FILTERS

    }

/***********************************************************************
 *
 *  End user function calls
 *
 **********************************************************************/

/**
* Function to call to display validitys selected in admin panel
*
* @global <type> $wp_w3_validation
*/
    function wp_w3_validation()
    {
        global $wp_w3_validation;

                $src = get_bloginfo('wpurl') . '/wp-content/plugins/wp-w3-validation/css/wp_w3_validation_main.css';
        //if (file_exists($src)) {
            wp_register_style('wp_w3_validation_stylesheet', $src);
            wp_enqueue_style( 'wp_w3_validation_stylesheet');

        // check if page is valid
        $wp_w3_validation->display_validity();
    }

}



?>