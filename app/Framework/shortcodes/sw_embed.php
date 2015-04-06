<?php
/**
 * Embed
 * Shortcode which creates an embed element wrapped in a div
 */

if ( !class_exists( 'avia_sc_embed' ) )
{
    class avia_sc_embed extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']      = __('Embed', 'swedenWp' );
                $this->config['tab']       = __('Content Elements', 'swedenWp' );
                $this->config['icon']      = swedenBuilder::$path['imagesURL']."sc-text_block.png";
                $this->config['order']     = 5;
                $this->config['target']    = 'avia-target-insert';
                $this->config['shortcode'] = 'av_embed';
                $this->config['tinyMCE']   = array('disable' => true);
                $this->config['tooltip']   = __('Creates an embed block', 'swedenWp' );
            }

            /**
             * Popup Elements
             *
             * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
             * opens a modal window that allows to edit the element properties
             *
             * @return void
             */
            function popup_elements()
            {
                $this->elements = array(

                    array(
                            "name"      => __("Embed Code",'swedenWp' ),
                            "desc"      => __("Place embed code here",'swedenWp' ),
                            "id"        => "content",
                            "type"      => "textarea",
                            "std"       => __("Place embed code here", "swedenWp" )
                         ),
                	array(
                			"name"      => __("Mobile Embed Code",'swedenWp' ),
                			"desc"      => __("Place embed code for mobile device here",'swedenWp' ),
                			"id"        => "mobile_content",
                			"type"      => "textarea",
                			"std"       => __("", "swedenWp" )
                	),

                    array(
                            "name"     => __("Padding top bottom", 'swedenWp' ),
                            "desc"     => __("Select padding for top and bottom", 'swedenWp' ),
                            "id"       => "padding_top_bottom",
                            "type"     => "select",
                            "std"      => "none",
                            "subtype"  => array(
                                                __('none', 'swedenWp' )=>'0',
                                                __('10px', 'swedenWp' )=>'10',
                                                __('20px', 'swedenWp' )=>'20',
                                                __('30px', 'swedenWp' )=>'30',
                                                __('40px', 'swedenWp' )=>'40',
                                                __('50px', 'swedenWp' )=>'50',
                                                )),
                    array(
                            "name"     => __("Padding left right", 'swedenWp' ),
                            "desc"     => __("Select padding for top and bottom", 'swedenWp' ),
                            "id"       => "padding_left_right",
                            "type"     => "select",
                            "std"      => "none",
                            "subtype"  => array(
                                                __('none', 'swedenWp' )=>'0',
                                                __('10px', 'swedenWp' )=>'10',
                                                __('20px', 'swedenWp' )=>'20',
                                                __('30px', 'swedenWp' )=>'30',
                                                __('40px', 'swedenWp' )=>'40',
                                                __('50px', 'swedenWp' )=>'50',
                                                )),
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
            function editor_element($params)
            {
                $params['class'] = "";
                $params['innerHtml'] = "<div class='avia_embed avia_embed_style' data-update_with='content'>".stripslashes(wpautop(trim(html_entity_decode($params['content']))))."</div>";

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
            function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
            {
            	global $swedenUnlimited;
            	extract(shortcode_atts(array('mobile_content' => ''), $atts));
            	
            	$pattern = '\[(\[?)(av_embed)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            	preg_match_all('/'.$pattern.'/s', $content, $matches);
            	 
            	if(!empty($matches) && is_array($matches)) {
            		foreach($matches[0] as $key => $placeholder) {
            			if(!empty($matches[3][$key])) $atts = shortcode_parse_atts($matches[3][$key]);
            			 
            			$content = str_replace($placeholder, trim($content), $content);
            			if(!empty($mobile_content)) 
            				$mobile_content = str_replace($placeholder, trim($mobile_content), $mobile_content);
            		}
            	}
            	if($swedenUnlimited['client']->isPhone && !empty($mobile_content))
            		return $mobile_content;
                else
                	return $content;

            }

    }
}


