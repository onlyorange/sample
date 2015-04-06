<?php
/**
 * Layout Type
 * Restrict content and layout elements depend on layout type
 */

if ( !class_exists( 'builder_layout_type' ) ) {

    class builder_layout_type extends swedenShortcodeTemplate{

        static $extraClass = "layoutType";

        /**
         * Create the config array for the layout type
         */
        function shortcode_insert_button() {
            $this->config['name']		= 'Waterfall';
            $this->config['icon']		= swedenBuilder::$path['imagesURL']."icon-cut.png";
            $this->config['tab']		= __('Layout Type', 'swedenWp' );
            $this->config['target']		= "sw-waterfall";
            $this->config['order']		= 10;
            $this->config['shortcode'] 	= 'sw_waterfall';
            $this->config['tooltip'] 	= __('Creates a waterfall layout page', 'swedenWp' );
            $this->config['drag-level'] = 1;
            $this->config['drop-level'] = 4;
        }

        /**
         * Editor Element - this function defines the visual appearance of an element on the swedenBuilder Canvas
         * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
         * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
         *
         *
         * @param array $params this array holds the default values for $content and $args.
         * @return $params the return array usually holds an innerHtml key that holds item specific markup.
         */

        function editor_element($params)
        {
            $params['innerHtml'] = "Layout set to Waterfall";
            $output = '';

            return $params;
            //return $output;
        }

        /**
         * Frontend Shortcode Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
        {
            global $avia_config;

            //$avia_config['current_column'] = $shortcodename;

            // Totally old and no longer in use. It would break a page if it were ever included.
            $output = "";

            // "<script>
            //         jQuery(function() {
            //             jQuery('#content').addClass('waterfall').removeClass('paginated');
            //         });
            //         </script>
            //         ";

            return $output;
        }
    }
}

if ( !class_exists( 'builder_layout_type_paged')) {
    class builder_layout_type_paged	extends builder_layout_type {
        function shortcode_insert_button()
        {
            $this->config['name']		= 'Paginated';
            $this->config['icon']		= swedenBuilder::$path['imagesURL']."icon-edit.png";
            $this->config['tab']		= __('Layout Type', 'swedenWp' );
            $this->config['target']		= "sw-paginated";
            $this->config['order']		= 5;
            $this->config['shortcode'] 	= 'sw_paginated';
            $this->config['tooltip'] 	= __('Creates a paginated layout page', 'swedenWp' );
            $this->config['drag-level'] = 1;
            $this->config['drop-level'] = 4;
        }
        function editor_element($params)
        {
            $params['innerHtml'] = 'Layout set to paginated';

            return $params;
            //return $output;
        }

        /**
         * Frontend Shortcode Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
        {
            global $avia_config;

            //$avia_config['current_column'] = $shortcodename;

            $output = "<script>
                    jQuery(function() {
                        jQuery('#content').addClass('paginated').removeClass('waterfall');
                    });
                    </script>
                    ";

            return $output;
        }
    }
}
