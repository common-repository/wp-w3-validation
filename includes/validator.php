<?php

// Check to see if another plugin has created this class already
if (!class_exists("wp_w3_validation")) {

    class wp_w3_validation {

        //private $html_doctype = '';

        private $css_doctype = 'css2.1'; // possible values css1 css2 css2.1 css3

        // ! need to change url('' ) in style sheet manualy
        // (this is currently only used or imgs) <- check this may not be true anymore
        private $plugin_url = '/wp-content/plugins/wp-w3-validation/';

        // Admin table
        const ADMIN_OPTIONS_NAME = "wp_w3_validation_admin_options";

        /*
         * Validators urls
         *
         * If you wish for faster loading times and higher reliability...
         * you may wish to install your own instances of these validators if so
         * change the url bellow.
         */
        private $validator_xhtml = 'http://validator.w3.org/check';
        private $validator_css = 'http://jigsaw.w3.org/css-validator/validator';
        private $validator_js = '';

        /*
         * Display Types
         *
         * Method of displaying validation info
         *
         */
        const DISPLAY_TYPE_TEXT = 'text';
        const DISPLAY_TYPE_IMAGE = 'image';
        private $display_type_default = 'text'; // must be one of above or breaks

        /*
         * Wait time bettween performing validator checks as validators are a
         * free public service they ask a sleep time of at least a second bettween
         * each call so as to not overload thier network
         *
         * ONLY set this over 1 SECOND if you are running your OWN validators!!!
         */
        private $courtesy_wait = '1000'; // ms

        function wp_w3_validation()
        {

        }





        function display_mass_validity()
        {

            /*
             * counts no of posts
            $numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
            if(0 < $numposts){
                $numposts = number_format($numposts);
            }
            $output = '<h2>' . $numposts.' recipes published since October 06, 2008' . '</h2>' . "\n";
            */


            //if(is_home()){
            //  $id = get_bloginfo("url");
            //}


            echo '<ul id="all_entry_list">';

            $myposts = get_posts('numberposts=-1&');
            foreach($myposts as $post){
                $valid = 0;
                $output = '';

                // HTML validation
                if($plugin_options['display_html'] == 'true'){
                    $html = $this->display_html_validity($id);
                    $output .= $html[1];
                    $valid &= $html[0];
                } else {
                    $output .= '<span class="nocheck">' . __('HMTL - No Check') . '</span>';
                }
                // CSS validation
                if($plugin_options['display_css'] == 'true'){
                    $css = $this->display_css_validity($id);
                    $output .= $css[1];
                    $valid &= $css[0];
                } else {
                    $output .= '<span class="nocheck">' . __('CSS - No Check') . '</span>';
                }
                // JS validation
                if($plugin_options['display_js'] == 'true'){
                    $js = $this->display_js_validity($id);
                    $output .= $js[1];
                    $valid &= $js[0];
                } else {
                    $output .= '<span class="nocheck">' . __('JS - No Check') . '</span>';
                }

                $output .= the_time('m/d/y') . ': <a href="' . the_permalink() . '">' . the_title() . '</a>';

                if($valid === true){
                    $output = '<li class="valid">' . $output . '</li>' . "\n";
                } elseif($valid === false) {
                    $output = '<li class="invalid">' . $output . '</li>' . "\n";
                } else {
                    $output = '<li class="nocheck">' . $output . '</li>' . "\n";
                }

                echo $output;

                sleep($this->courtesy_wait); // sleep for at least a second as validators request it
            }
            echo '</ul>';

            // no point, as will display well late... $output = '<h2>' . $counter.' Posts published since October 06, 2008' . '</h2>' . "\n";

        }



        /**
         * Displays validity of W3s allowed
         *
         * @return <type>
         */
        public function display_validity()
        {
            // Check if user should see output
            if(!$this->can_view()){
                return;
            }

            // Get saved vars
            $plugin_options = get_option(self::ADMIN_OPTIONS_NAME);
            $valid = true;
            $output = '';

            // Run validation functions
            // HTML validation
            if($plugin_options['display_html'] == 'true'){
                $html = $this->display_html_validity($plugin_options['display_type']);
                if($html[0]) {
                    $output .= '<li class="valid">' . $html[1] . '</li>' . "\n";
                } else {
                    $output .= '<li class="invalid">' . $html[1] . '</li>' . "\n";
                }
                $valid &= $html[0];
            }
            // CSS validation
            if ($plugin_options['display_css'] == 'true'){
                $css = $this->display_css_validity($plugin_options['display_type']);
                if($css[0]) {
                    $output .= '<li class="valid">' . "\n" . $css[1] . "\n" . '</li>' . "\n";
                } else {
                    $output .= '<li class="invalid">' . "\n" . $css[1] . "\n" . '</li>' . "\n";
                }
                $valid &= $css[0];
            }
            // JS validation (not yet implemented)
            if ($plugin_options['display_js'] == 'true'){
                $js = $this->display_js_validity($plugin_options['display_type']);
                if($js[0]) {
                    $output .= '<li class="valid">' . "\n" . $js[1] . "\n" . '</li>' . "\n";
                } else {
                    $output .= '<li class="invalid">' . "\n" . $js[1] . "\n" . '</li>' . "\n";
                }
                $valid &= $js[0];
            }

            if($output != ''){
                if($valid){
                    echo "\n" . '<div id="wp-w3-validation-drop-down">' . "\n\t" . '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/valid.png" alt="' . __('Valid', "wp_w3_validation") . '" />' . "\n\t" . '<ul>' . "\n\t\t" . $output . "\t" . '</ul>' . "\n" . '</div>';

                } else {
                    echo "\n" . '<div id="wp-w3-validation-drop-down">' . "\n\t" . '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/invalid.png" alt="' . __('Invalid', "wp_w3_validation") . '" />' . "\n\t" . '<ul>' . "\n\t\t" . $output . "\t" . '</ul>' . "\n" . '</div>';
                }
            }
        }

        /**
         * Adds html to show if page is/not valid HTML.
         */
        public function display_html_validity($display_type, $entry_id = '')
        {
            // Check if user should see output (as is public func)
            if(!$this->can_view()){
                return;
            }

            // Use id of post or custom function var
            $id = '';
            if($entry_id != ''){
                $id = $entry_id;
            } else {
                global $post;
                $id = $post->ID;
            }

            $output_format = '';
            $valid = false;
            $check_link = $this->validator_xhtml . '?uri=' . get_bloginfo("url") . '/?p=' . $id;
            switch($display_type) {
                case self::DISPLAY_TYPE_TEXT:
                    if($this->is_valid_html_pID($id)) {
                        $output_format =  __('Valid HTML', 'wp_w3_validation');
                        $valid = true;
                    } else {
                        $output_format =  __('Invalid HTML', 'wp_w3_validation');
                    }
                    break;
                case self::DISPLAY_TYPE_IMAGE:
                    if($this->is_valid_html_pID($id)) {
                        $output_format = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/xhtml-valid.png" alt="' . __('Valid HTML', 'wp_w3_validation') . '" />';
                        $valid = true;
                    } else {
                        $output_format = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/xhtml-invalid.png" alt="' . __('Invalid HTML', 'wp_w3_validation') . '" />';
                    }
                    break;
                default:
                    return $this->display_html_validity($this->display_type_default, $id);
                    break;
            }

            $output = '<a href="' . $check_link . '">' . $output_format . '</a>';
            return array($valid, $output);
        }

        /**
         * Adds html to show if page is/not valid css.
         */
        public function display_css_validity($display_type, $entry_id = '')
        {
            // Check if user should see output (as is public func)
            if(!$this->can_view()){
                return;
            }

            // Use id of post or custom function var
            $id = '';
            if($entry_id != ''){
                $id = $entry_id;
            } else {
                global $post;
                $id = $post->ID;
            }

            $output_format = '';
            $valid = false;
            $check_link = $this->validator_css . '?uri=' . get_bloginfo("url") . '/?p=' . $id;
            switch($display_type) {
                case self::DISPLAY_TYPE_TEXT:
                    if($this->is_valid_css_pID($id)) {
                        $output_format =  __('Valid CSS', 'wp_w3_validation');
                        $valid = true;
                    } else {
                        $output_format =  __('Invalid CSS', 'wp_w3_validation');
                    }
                    break;
                case self::DISPLAY_TYPE_IMAGE:
                    if($this->is_valid_css_pID($id)){
                        $output_format = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/css-valid.png" alt="' . __('Valid CSS', "wp_w3_validation") . '" />';
                        $valid = true;
                    } else {
                        $output_format = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/css-invalid.png" alt="' . __('Invalid CSS', "wp_w3_validation") . '" />';
                    }
                    break;
                default:
                    return $this->display_css_validity($this->display_type_default, $id);
                    break;
            }

            $output = '<a href="' . $check_link . '">' . $output_format . '</a>';
            return array($valid, $output);
        }

        /**
         * Adds html to show if page is/not valid Javascript.
         *
         * NOT YET FULLY IMPLEMENTED, ALLWAYS RETURNS TRUE
         */
        public function display_js_validity($display_type, $entry_id = '')
        {
            // Check if user should see output (as is public func)
            if(!$this->can_view()){
                return;
            }

            // Use id of post or custom function var
            $id = '';
            if($entry_id != ''){
                $id = $entry_id;
            } else {
                global $post;
                $id = $post->ID;
            }

            $output_format = '';
            $valid = false;
            $check_link = '';
            switch($display_type) {
                case self::DISPLAY_TYPE_TEXT:
                    if($this->is_valid_js_pID($id)) {
                        $output_format =  __('Valid JS', 'wp_w3_validation');
                        $valid = true;
                    } else {
                        $output_format =  __('Invalid JS', 'wp_w3_validation');
                    }
                    break;
                case self::DISPLAY_TYPE_IMAGE:
                    if($this->is_valid_js_pID($id)) {
                        $output = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/js-valid.png" alt="' . __('Valid JS', "wp_w3_validation") . '" />';
                        $valid = true;
                    } else {
                        $output = '<img src="' . get_bloginfo("url") . $this->plugin_url . 'images/js-invalid.png" alt="' . __('Invalid JS', "wp_w3_validation") . '" />';
                    }
                    break;
                default:
                    return $this->display_js_validity($this->display_type_default, $id);
                    break;
            }

            $output = '<a href="' . $check_link . '">' . $output_format . '</a>';
            return array($valid, $output);
        }


        /**
         * Checks HTML Validity of page or post based on id returns boolean
         *
         * Uses http://validator.w3.org api to return a soap responce via the
         * snoopy class, which is then parsed for the validity result.
         *
         * @param <type> $id
         * @return <type>
         */
        private function is_valid_html_pID($id)
        {
            require_once (ABSPATH . WPINC . '/class-snoopy.php');

            $wpurl = get_bloginfo("url") . '/?p=' . $id;
            $url = $this->validator_xhtml . '?uri=' . $wpurl  . '&output=soap12';

            $client = new Snoopy();
            @$client->fetch($url);
            $data = $client->results;
            $data = explode("\n", $data);
            foreach ($data as $buffer) {
                if (eregi("m:validity",$buffer)) {
                    if(trim(strip_tags($buffer)) == "true") {
                        return true;
                    }
                    break;
                }
            }
            return false;
        }

        /**
         * Checks CSS Validity of page or post based on id returns boolean
         *
         * Uses http://jigsaw.w3.org/css-validator/validator api to return a
         * soap responce via the snoopy class, which is then parsed for the
         * validity result.
         *
         * @param <type> $id
         * @return <type>
         */
        private function is_valid_css_pID($id)
        {
            require_once (ABSPATH . WPINC . '/class-snoopy.php');

            $wpurl = get_bloginfo("url") . '/?p=' . $id;
            $url = $this->validator_css . '?uri=' . $wpurl . '&output=soap12' . '&profile=' . $this->css_doctype;
            $client = new Snoopy();
            @$client->fetch($url);
            $data = $client->results;
            $data = explode("\n", $data);
            foreach ($data as $buffer) {
                if (eregi("m:validity",$buffer)) {
                    if(trim(strip_tags($buffer)) == "true") {
                        return true;
                    }
                    break;
                }
            }
            return false;
        }

        /**
         * Checks JS Validity of page or post based on id returns boolean
         *
         * Except it doesnt as its not valid
         *
         * @param <type> $id
         * @return <type>
         */
        private function is_valid_js_pID($id)
        {
            return true; // everyone writes valid js, right?....
        }


        /**
         * Check and perform validation only to page editors
         *
         * @global <type> $post
         * @return <type>
         */
        private function can_view()
        {
            global $post;

            if(isset($post->post_type)){
                if ($post->post_type == 'page'){
                    if (current_user_can('edit_page' , $post->ID)){
                        return true;
                    }
                } else if($post->post_type == 'post'){
                    if (current_user_can('edit_post', $post->ID)){
                        return true;
                    }
                }
            }
            return false;
        }

       /**
         * Link stylesheet in header
         */
        public function add_stylesheet()
        {
            if($this->can_view()){
                wp_register_style('wp_w3_validation_stylesheet', $this->plugin_url . 'css/wp_w3_validation_main.css');
                wp_enqueue_style( 'wp_w3_validation_stylesheet');
            }
        }




        // End Class
    }

}

?>