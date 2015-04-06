<?php
/**
 *
 *
 */
if ( !class_exists( 'el_cta' ) ) {
    class el_cta extends swedenShortcodeTemplate {

            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button() {
                $this->config['name']			= __('Call to Action', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-icon_box.png";
                $this->config['order']			= 1;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_cta';
                $this->config['modal_data']     = array('modal_class' => 'mediumscreen');
                $this->config['tooltip'] 	    = __('Creates a CTA button', 'swedenWp' );
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
                $customCTA = array_merge(array(' - Custom Label'=> 'custom'), swedenWpFunctions::get_saved_cta_value(1));
                $this->elements = array(
                    array(	"name" 	=> __("CTA Label", 'swedenWp' ),
                            "desc" 	=> __("This is the text that appears on the button.", 'swedenWp' ),
                            "id" 	=> "label",
                            "type" 	=> "select",
                            "subtype" => $customCTA,
                            "std" => "custom",
                    ),
                    array(	"name" => __("Custom Label", 'swedenWp'),
                            "desc" => __("This is the text that appears on the button.", 'swedenWp'),
                            "id"   => "c_label",
                            "type" => "input",
                            "std" => "",
                            "required" => array('label','equals','custom'),

                    ),
                    array(
                            "name" 	=> __("Button Link?", 'swedenWp' ),
                            "desc" 	=> __("Where should the CTA link to?", 'swedenWp' ),
                            "id" 	=> "link",
                            "type" 	=> "linkpicker",
                            "fetchTMPL"	=> true,
                            "subtype" => array(
                                                __('Set Manually', 'swedenWp' ) =>'manually',
                            					__('Product', 'swedenWp' ) =>'product',
                                                __('Single Entry', 'swedenWp' ) =>'single',
                                                ),
                            "std" 	=> "#"),

                    array(
                            "name" 	=> __("Open Link in new Window?", 'swedenWp' ),
                            "desc" 	=> __("Select here if you want to open the linked page in a new window", 'swedenWp' ),
                            "id" 	=> "link_target",
                            "type" 	=> "select",
                            "std" 	=> "",
                            "subtype" => array(
                                __('Open in same window',  'swedenWp' ) =>'',
                                __('Open in new window',  'swedenWp' ) =>'_blank')),
                    array(
                            "name" 	=> __("Button Alignment", 'swedenWp' ),
                            "desc" 	=> __("Change CTA alignment", 'swedenWp' ),
                            "id" 	=> "position",
                            "type" 	=> "select",
                            "std" 	=> "center",
                            "subtype" => array(
                                    __('Center', 'swedenWp' )=>'center',
                                    __('Left', 'swedenWp' )=>'left',
                                    __('Right', 'swedenWp' )=>'right',
                            )),
                    array(
                            "name" 	=> __("Button Color", 'swedenWp' ),
                            "desc" 	=> __("Choose a color for the CTA here", 'swedenWp' ),
                            "id" 	=> "color",
                            "type" 	=> "select",
                            "std" 	=> "theme-color",
                            "subtype" => array(
                                                __('Black', 'swedenWp' )=>'is-black',
                                                __('White', 'swedenWp' )=>'is-white',
                                                __('Custom Color', 'swedenWp' )=>'custom',
                                                )),
                    array(
                            "name" 	=> __("Custom Background Color", 'swedenWp' ),
                            "desc" 	=> __("Select a custom background color for the CTA here", 'swedenWp' ),
                            "id" 	=> "custom_bg",
                            "type" 	=> "colorpicker",
                            "std" 	=> "#444444",
                            "required" => array('color','equals','custom')
                        ),

                    array(
                            "name" 	=> __("Custom Font Color", 'swedenWp' ),
                            "desc" 	=> __("Select a custom font color for the CTA here", 'swedenWp' ),
                            "id" 	=> "custom_font",
                            "type" 	=> "colorpicker",
                            "std" 	=> "#ffffff",
                            "required" => array('color','equals','custom')
                        ),
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
                $params['class'] = "";
                $params['innerHtml']  = "";
                $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style'>";
                $params['innerHtml'] .= "	<div class='avia_button_box avia_hidden_bg_box' style='text-align:".$params['args']['position']."'>";
                $params['innerHtml'] .= "		<div ".$this->class_by_arguments('color' ,$params['args']).">";
                if ($params['args']['label'] == "custom") {
                    $params['innerHtml'] .= "			<span data-update_with='c_label' class='avia_title' >".$params['args']['c_label']."</span>";
                } else {
                    $params['innerHtml'] .= "			<span data-update_with='label' class='avia_title' >".$params['args']['label']."</span>";
                }
                $params['innerHtml'] .= "		</div>";
                $params['innerHtml'] .= "	</div>";
                $params['innerHtml'] .= "</div>";

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
                global $swedenUnlimited;
                global $projectConfig;

                $output = "";
                $class  = "";

                $atts =  shortcode_atts(array(
                                             'label' => '',
                                             'c_label' => '',
                                             'link' => '',
                                             'link_target' => '',
                                             'position' => '',
                                             'color' => 'is-black',
                                             'custom_bg' => '#444444',
                                             'custom_font' => '#ffffff',
                                             ), $atts);

                $blank = $atts['link_target'] ? 'target="_blank" ' : "";
                $productLink = explode(',', $atts['link']);
                if (!empty($link)) {

                    $link  = swedenWpFunctions::get_url($atts['link']);
                    $link  = $link == "http://" ? "" : $link;
                }

                // category
                $link  = swedenWpFunctions::get_url($atts['link']);
                $link  = $link == "http://" ? "" : $link;
				if($atts['label'] == 'custom' && !empty($atts['c_label'])) {
					$label = $atts['c_label'];
				} else {
					$label = $atts['label'];
				}
                //$label = $atts['label'] == 'custom' ? $atts['c_label'] : $atts['label'];

                // wrapper
                $output .='<div class="content_container">';

                // CTA
                $style = "";
                if ($atts['color'] == "custom") {
                    $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                }

                $output .= "	<div class='sw_btn_wrapper' style='text-align:". $atts['position'] ."'>";

                if($productLink[0] == 'product') {

                    if($swedenUnlimited['client']->isPhone) {
                        // no shop widget on mobile -- just link to PDP
                        $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                        $blank = 'target="_blank"';
                        $output .= "        <a href='{$link}' class='swBtn' {$blank} {$style} title='{$label}'>";
                        $output .= "            <span class='read-more cta'>".$label."<span class='icon-arrow-right'></span></span>";
                        $output .= "        </a>";

                    } else {
                    	$styleNum = str_replace(strtoupper($atts['country']).'_', '', strtoupper($atts['p_id']));
                    	$output .= '<div title="Quickview" id="widget-o-pop" data-requirejs-id="utils/shop" data-source="" data-style="'.
                    			$styleNum.'" data-country="'.strtoupper($productLink[3]).'" data-skuid="'.strtoupper($productLink[2]).'">
    							<span class="read-more" style="margin-top:20px; cursor:pointer">'.$label.'<span class="icon-arrow-right"></span></span></div>';
                    }

                } else {
                	$output .= "		<a href='{$link}' class='swBtn' {$blank} {$style} title='{$label}'>";
                	$output .= "			<span class='read-more cta'>".$label."<span class='icon-arrow-right'></span></span>";
                	$output .= "		</a>";
                }
                $output .= "	</div>";

                //close wrapper
                $output.= "</div>";

                return do_shortcode($output);

            }

    }
}
