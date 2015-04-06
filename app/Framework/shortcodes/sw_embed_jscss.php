<?php 
/**
 * Embed
 * Shortcode which creates an embed element wrapped in a div
 */

if ( !class_exists( 'sw_embed_jscss' ) )
{
    class sw_embed_jscss extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']      = __('Embed JS & CSS', 'swedenWp' );
                $this->config['tab']       = __('Content Elements', 'swedenWp' );
                $this->config['icon']      = swedenBuilder::$path['imagesURL']."sc-text_block.png";
                $this->config['order']     = 5;
                $this->config['target']    = 'avia-target-insert';
                $this->config['shortcode'] = 'sw_embed_jscss';
                $this->config['tinyMCE']   = array('disable' => true);
                $this->config['tooltip']   = __('Creates an embed block with js and css', 'swedenWp' );
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
                			"name"      => __("CSS Code",'swedenWp' ),
                			"desc"      => __("Place css here without style tag.",'swedenWp' ),
                			"id"        => "css_box",
                			"type"      => "textarea",
                			"std"       => __("", "swedenWp" )
                	),
                	array(
                			"name"      => __("CSS File",'swedenWp' ),
                			"desc"      => __("Place css file link here.",'swedenWp' ),
                			"id"        => "enqueue_css",
                			"type"      => "input",
                			"std"       => __("", "swedenWp" )
                	),
                	array(
                			"name"      => __("JS Code",'swedenWp' ),
                			"desc"      => __("Place js code here.",'swedenWp' ),
                			"id"        => "content",
                			"type"      => "textarea",
                			"std"       => __("", "swedenWp" )
                	),
                	array(
                			"name"      => __("JS File",'swedenWp' ),
                			"desc"      => __("Place js file link here.",'swedenWp' ),
                			"id"        => "enqueue_js",
                			"type"      => "input",
                			"std"       => __("", "swedenWp" )
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
            function editor_element($params)
            {
                $params['class'] = "";
                $params['innerHtml'] = "<div class='avia_embed avia_embed_style' data-update_with='text_content' style='background:grey; height:50px; color:#FFF; text-align:center;'>JS/CSS<br/>Click here to update the files</div>";

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
            	extract(shortcode_atts(array('css_box' => '', 'enqueue_css' => '', 'enqueue_js' => ''), $atts));
            	
            	$pattern = '\[(\[?)(sw_embed_jscss)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            	preg_match_all('/'.$pattern.'/s', $content, $matches);
            	
            	if(!empty($matches) && is_array($matches)) {
            		foreach($matches[0] as $key => $placeholder) {
            			if(!empty($matches[3][$key])) $atts = shortcode_parse_atts($matches[3][$key]);
            	
            			$content = str_replace($placeholder, trim($content), $content);
            		}
            	}
            	if($enqueue_css) {
            		wp_enqueue_style('embeded', $enqueue_css);
            	}
            	if($enqueue_js) {
            		wp_enqueue_script('embeded-js', $enqueue_js, '', '', true);
            	}
            	//$js_box = strip_tags($content);
            	//$js_box = $content;
            	//$content = ($content);
            	//$js_box = html_entity_decode($js_box);
            	$output = '';
            	$output .= '<style>'.$css_box.'</style>';
            	//$output .= '<script type="text/javascript" language="JavaScript">';
            	$output .= $content;
            	//$output .= '</script>';
            	
            	return $output;
            }
            
            /**
             * extract content from editor
             * 
             * note: testing this func in shortcode 
             * 
             * @param unknown $content
             * @return unknown
             */
            function code_block_extraction($content) {
            	if (strpos($content, '[sw_embed_jscss') === false) return $content;
            
            	$pattern = '\[(\[?)(sw_embed_jscss)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            	preg_match_all('/'.$pattern.'/s', $content, $matches);
            
            	if(!empty($matches[0]) && is_array($matches)) {
            		foreach($matches[0] as $key => $data) {
            			$codeblock = !empty($matches[5][$key]) ? $matches[5][$key] : '';
            			$codeblock = trim($codeblock);
            
            			if(!empty($matches[3][$key])) {
            				$atts = shortcode_parse_atts($matches[3][$key]);
            					
            				$codeblock = !empty($atts['escape_html']) ? esc_html($codeblock) : $codeblock;
            				$codeblock = !empty($atts['escape_html']) && empty($atts['wrapper_element']) ? nl2br($codeblock) : $codeblock;
            				$codeblock = !empty($atts['deactivate_shortcode']) ? do_shortcode($codeblock) : $codeblock;
            			}
            
            			self::$codeblocks[$key] = $codeblock;
            		}
            	}
            
            	return $content;
            }
			
            /**
             * add stripped content to short code
             * 
             * TODO: find way to inject content before wp converts content to mess. or create own hook??
             * 
             * @param unknown $content
             * @return unknown|mixed
             */
            function code_block_injection($content) {
            	$pattern = '\[(\[?)(sw_embed_jscss)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            	preg_match_all('/'.$pattern.'/s', $content, $matches);
            
            	if(!empty($matches) && is_array($matches)) {
            		foreach($matches[0] as $key => $placeholder) {
            			if(!empty($matches[3][$key])) $atts = shortcode_parse_atts($matches[3][$key]);
            			$id = !empty($atts['uid']) ? $atts['uid'] : 0;
            
            			$codeblock = !empty($codeblocks[$id]) ? $codeblocks[$id] : '';
            			$content = str_replace($placeholder, $codeblock, $content);
            		}
            	}
            
            	return $content;
            }

    }
}





