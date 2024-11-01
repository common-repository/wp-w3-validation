<?php

// Check to see if another plugin has created this class already
if (!class_exists("wp_w3_validation_admin")) {

    class wp_w3_validation_admin {

        const ADMIN_OPTIONS_NAME = "wp_w3_validation_admin_options";

        /*
         * Display Types
         *
         * Method of displaying validation info
         *
         */
        const DISPLAY_TYPE_TEXT = 'text';
        const DISPLAY_TYPE_IMAGE = 'image';
        private $display_type_default = 'text';


        function wp_w3_validation_admin()
        {

        }

        /**
         * Initalise plugin for first time run (adds stuff to db)
         */
        public function init()
        {
            $plugin_options = get_option(self::ADMIN_OPTIONS_NAME);
            //echo 'plugin options: ' . $plugin_options;
            // updates vars to be passed back from wp db or adds them if not already there.
            if (!$plugin_options) {
                $wp_w3_validation_admin_options = array(
                'display_html' => 'true',
                'display_css' => 'false',
                'display_js' => 'false',
                'display_type' => $this->display_type_default
                );
                update_option(self::ADMIN_OPTIONS_NAME, $wp_w3_validation_admin_options);
            }
        }

        public function init_admin_panel()
        {
            global $wp_w3_validation_admin;
            if(isset($wp_w3_validation_admin)){
                add_options_page('Validation W3 Settings', 'Validation W3', 8, basename(__FILE__), array(&$wp_w3_validation_admin, 'print_admin_page'));
            }
        }


        /***********************************************************************
         *
         *  Admin Panel
         *
         **********************************************************************/

        /**
         * Prints out Admin option page and deals with user updating it.
         */
        public function print_admin_page() {

            $plugin_options = get_option(self::ADMIN_OPTIONS_NAME);

            // Set options in wp db after page update
            if (isset($_POST['wp_w3_validation_update_settings'])) {
                if (isset($_POST['wp_w3_validation_display_html'])) {
                    $plugin_options['display_html'] = 'true';
                } else {
                    $plugin_options['display_html'] = 'false';
                }

                if (isset($_POST['wp_w3_validation_display_css'])) {
                    $plugin_options['display_css'] = 'true';
                } else {
                    $plugin_options['display_css'] = 'false';
                }

                if (isset($_POST['wp_w3_validation_display_js'])) {
                    $plugin_options['display_js'] = 'true';
                } else {
                    $plugin_options['display_js'] = 'false';
                }

                if (isset($_POST['wp_w3_validation_display_type'])) {
                    switch($_POST['wp_w3_validation_display_type']) {
                        case self::DISPLAY_TYPE_TEXT:
                            $plugin_options['display_type'] = self::DISPLAY_TYPE_TEXT;
                            break;
                        case self::DISPLAY_TYPE_IMAGE:
                            $plugin_options['display_type'] = self::DISPLAY_TYPE_IMAGE;
                            break;

                        default :
                            $plugin_options['display_type'] = $this->display_type_default;
                            break;
                    }
                } else {
                    $plugin_options['display_type'] = $this->display_type_default;
                }

                update_option(self::ADMIN_OPTIONS_NAME, $plugin_options);

                ?>
<div class="updated">
    <p>
        <strong><?php _e("Settings Updated.", "wp_w3_validation");?></strong>
    </p>
</div>
<?php

}
?>

<div class="wrap">
    <div class="icon32" id="icon-options-general"></div>
    <h2><?php _e("W3 Validation Settings", "wp_w3_validation"); ?></h2>
    <p>
        <?php _e("Welcome to the W3 Validation. To use just add:", "wp_w3_validation"); ?>
    </p>
    <p>
        <strong>&#60;&#63;php if(function_exists('wp_w3_validation')){wp_w3_validation();} &#63;&#62;</strong>
    </p>
    <p>
        <?php _e("To your Page and Post Theme Templates where you want this plugin to show you the page/post's validity.", "wp_w3_validation"); ?><br />
        <a href="#more_info"><?php _e("More Info", "wp_w3_validation"); ?></a>
        <span style="display: none;">
            <?php _e("Example go to Appearance -> Editor in your wp admin section then ", "wp_w3_validation"); ?>
        </span>
    </p>
    <p>
        <strong><?php _e("Note: ", "wp_w3_validation");?></strong>
        <ul>
            <li><?php _e("This plugin slows page load times ONLY to people with page edit privlages (Regular visiters are not affected).", "wp_w3_validation"); ?></li>
            <li><?php _e("The validation icons are only displayed to page people with page edit privlages.", "wp_w3_validation"); ?></li>
        </ul>
    </p>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

        <h3><?php _e("Display Type", "wp_w3_validation"); ?></h3>
        <p>
            <label for="wp_w3_validation_display_type">
                <select id="wp_w3_validation_display_type" name="wp_w3_validation_display_type">
                    <option value="<?= self::DISPLAY_TYPE_TEXT ?>"  <?php if($plugin_options['display_type'] == self::DISPLAY_TYPE_TEXT) { echo 'selected="selected"'; } ?> ><?php _e('Text', "wp_w3_validation"); ?></option>
                    <option value="<?= self::DISPLAY_TYPE_IMAGE ?>"  <?php if($plugin_options['display_type'] == self::DISPLAY_TYPE_IMAGE) { echo 'selected="selected"'; } ?> ><?php _e('Image', "wp_w3_validation"); ?></option>
                </select>
                <?php _e("Display Method", "wp_w3_validation"); ?>
            </label>
        </p>

        <h3><?php _e("XHTML Validation", "wp_w3_validation"); ?></h3>
        <p>
            <label for="wp_w3_validation_display_hmtl">
                <input type="checkbox" id="wp_w3_validation_display_hmtl" name="wp_w3_validation_display_html" value="true" <?php if($plugin_options['display_html'] == 'true') { ?>checked="checked"<?php } ?> />
                <?php _e("Display on Page/Post", "wp_w3_validation"); ?>
            </label>
        </p>
        <h3><?php _e("CSS Validation", "wp_w3_validation"); ?></h3>
        <p>
            <label for="wp_w3_validation_display_css">
                <input type="checkbox" id="wp_w3_validation_display_css" name="wp_w3_validation_display_css" value="true" <?php if($plugin_options['display_css'] == 'true') { ?>checked="checked"<?php } ?> />
                <?php _e("Display on Page/Post", "wp_w3_validation"); ?>
            </label>
        </p>
        <h3><?php _e("Javascript Validation", "wp_w3_validation"); ?></h3>
        <p>
            <?php _e("Coming soon...", "wp_w3_validation"); ?>
        </p>
        <p>
            <label for="wp_w3_validation_display_js">
                <input disabled="disabled" type="checkbox" id="wp_w3_validation_display_js" name="wp_w3_validation_display_js" value="true" <?php if($plugin_options['display_js'] == 'true') { ?>checked="checked"<?php } ?> />
                <?php _e("Display on Page/Post", "wp_w3_validation"); ?>
            </label>
        </p>
        <div class="submit">
            <input type="submit" name="wp_w3_validation_update_settings" value="<?php _e("update", "wp_w3_validation"); ?>" />
        </div>
    </form>
</div>
<?php

}

// class ends

}

}
?>