<?php
/**
 * Creates a horizontal ruler that provides whitespace for the layout and helps with content separation
 *
 *
 **/
if (!class_exists('padding')) {
    class padding extends swedenShortcodeTemplate{

        /**
         * Create the config array for the shortcode button
         */
        function shortcode_insert_button() {
            $this->config['name']		= __('Content Padding', 'swedenWp' );
            $this->config['tab']		= __('Content Elements', 'swedenWp' );
            $this->config['icon']		= swedenBuilder::$path['imagesURL']."sc-heading.png";
            $this->config['order']		= 4;
            $this->config['target']		= 'avia-target-insert';
            $this->config['shortcode'] 	= 'sw_padding';
            $this->config['modal_data'] = array('modal_class' => 'mediumscreen');
            $this->config['tooltip'] 	= __('Creates a padding', 'swedenWp' );
        }

        /**
         * Popup Elements
         *
         * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
         * opens a modal window that allows to edit the element properties
         *
         * @return void
         */
        function popup_elements() {
            $this->elements = array(
                    array(
                            "name"  => __("Desktop & Tablet Padding Height", 'swedenWp'),
                            "desc"  => __("Set height of padding in pixels", 'swedenWp'),
                            "id"    => "p_height",
                            "type"  => "select",
                            "subtype" => array(
                                    __('0',  'swedenWp' ) =>'pad-0',
                                    __('3',  'swedenWp' ) =>'pad-3',
                                    __('5',  'swedenWp' ) =>'pad-5',
                                    __('7',  'swedenWp' ) =>'pad-7',
                                    __('10',  'swedenWp' ) =>'pad-10',
                                    __('15',  'swedenWp' ) =>'pad-15',
                                    __('20',  'swedenWp' ) =>'pad-20',
                                    __('25',  'swedenWp' ) =>'pad-25',
                                    __('30',  'swedenWp' ) =>'pad-30',
                                    __('35',  'swedenWp' ) =>'pad-35',
                                    __('40',  'swedenWp' ) =>'pad-40',
                                    __('45',  'swedenWp' ) =>'pad-45',
                                    __('50',  'swedenWp' ) =>'pad-50',
                                    __('55',  'swedenWp' ) =>'pad-55',
                                    __('55',  'swedenWp' ) =>'pad-60'
                            ),
                            "std"   => "0"
                    ),
            		array(
							"name" => __("Present In Mobile", "swedenWp"),
            				"desc" => __("Set to \"Yes\" to display padding on mobile devices", "swedenWp"),
            				"id"   => "m_padding",
            				"type" => "select",
            				"subtype" => array(
            						__('No', 'swedenWp') => 'null',
            						__('Yes', 'swedenWp') => 'on-mobile',
            				),
            				"std" => "null"
            		),
            		array(
            				"name"  => __("Mobile Padding Height", 'swedenWp'),
            				"desc"  => __("Set height of mobile padding in pixels", 'swedenWp'),
            				"required" => array('m_padding','equals','on-mobile'),
            				"id"    => "pm_val",
            				"type"  => "select",
            				"subtype" => array(
            						__('0',  'swedenWp' ) =>'m-pad-0',
            						__('3',  'swedenWp' ) =>'m-pad-3',
            						__('5',  'swedenWp' ) =>'m-pad-5',
            						__('7',  'swedenWp' ) =>'m-pad-7',
            						__('10',  'swedenWp' ) =>'m-pad-10',
            						__('15',  'swedenWp' ) =>'m-pad-15',
            						__('20',  'swedenWp' ) =>'m-pad-20',
            						__('25',  'swedenWp' ) =>'m-pad-25',
            						__('30',  'swedenWp' ) =>'m-pad-30',
            						__('35',  'swedenWp' ) =>'m-pad-35',
            						__('40',  'swedenWp' ) =>'m-pad-40',
            						__('45',  'swedenWp' ) =>'m-pad-45',
            						__('50',  'swedenWp' ) =>'m-pad-50',
            						__('55',  'swedenWp' ) =>'m-pad-55',
            						__('55',  'swedenWp' ) =>'m-pad-60'
            				),
            				"std"   => "m-pad-0"
            		)
            );

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
        function editor_element($params) {
        	$mobileStat = '';
        	if($params['args']['m_padding'] == 'on-mobile') $mobileStat = ', ' . $params['args']['pm_val'] . ' present in mobile.';
            $params['class'] = "";
            $params['innerHtml'] = "<div class='avia_textblock avia_textblock_style'>
                    <span>Padding Height: </span><span data-update_with='p_height'>"
                    .$params['args']['p_height']."</span><span>". $mobileStat ."</span></div>";

            return $params;
        }

        /**
         * Frontend Shortcode Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "") {
            extract(shortcode_atts(array('p_height' => '', 'm_padding' => '', 'pm_val' => ''), $atts));
            $output  = "";
			if($m_padding == 'on-mobile') {
				$mobile_padding = $pm_val;
			} else {
				$mobile_padding = '';
			}
            $output .= "<div class='{$p_height} {$mobile_padding}'>";
            $output .= "</div>";

            return do_shortcode($output);
        }
    }
}
