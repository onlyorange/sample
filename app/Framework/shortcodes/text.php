<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if (!class_exists('text_block')) {
	class text_block extends swedenShortcodeTemplate {
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button() {
				$this->config['name']			= __('Text Block', 'swedenWp' );
				$this->config['tab']			= __('Content Elements', 'swedenWp' );
				$this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-text_block.png";
				$this->config['order']			= 18;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'sw_text_block';
				$this->config['tinyMCE'] 	    = array('disable' => false);
				$this->config['tooltip'] 	    = __('Creates a simple text block', 'swedenWp' );
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

                    //for article types such as collaborator and travel diary, apply special
                    //styling (for example 60% width (there is a large white right margin))
                    //with embedded images right aligned

					array(
							"name" 	=> __("Content",'swedenWp' ),
							"desc" 	=> __("Enter some content for this textblock",'swedenWp' ),
							"id" 	=> "content",
							"type" 	=> "tiny_mce",
							"std" 	=> __("<div class='article-subhead sans'>Click here to add your own text</div>", "swedenWp" )),
					array(
							"name" 	=> __("Padding Top and Bottom", 'swedenWp' ),
							"desc" 	=> __("Select padding for top and bottom", 'swedenWp' ),
							"id" 	=> "padding_top_bottom",
							"type" 	=> "select",
							"std" 	=> "none",
							"subtype" => array(
												__('none', 'swedenWp' )=>'0',
												__('10px', 'swedenWp' )=>'10',
												__('20px', 'swedenWp' )=>'20',
												__('30px', 'swedenWp' )=>'30',
												__('40px', 'swedenWp' )=>'40',
												__('50px', 'swedenWp' )=>'50',
												__('100px', 'swedenWp' )=>'100',
												__('150px', 'swedenWp' )=>'150',
												__('200px', 'swedenWp' )=>'200',
												)),
					array(
							"name" 	=> __("Padding Left and Right", 'swedenWp' ),
							"desc" 	=> __("Select padding for top and bottom", 'swedenWp' ),
							"id" 	=> "padding_left_right",
							"type" 	=> "select",
							"std" 	=> "none",
							"subtype" => array(
												__('none', 'swedenWp' )=>'0',
												__('10px', 'swedenWp' )=>'10',
												__('20px', 'swedenWp' )=>'20',
												__('30px', 'swedenWp' )=>'30',
												__('40px', 'swedenWp' )=>'40',
												__('50px', 'swedenWp' )=>'50',
												__('100px', 'swedenWp' )=>'100',
												__('150px', 'swedenWp' )=>'150',
												__('200px', 'swedenWp' )=>'200',
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
			function editor_element($params) {
				$style = 'padding:'.$params['args']['padding_top_bottom'].'px '.$params['args']['padding_left_right'].'px;';
				$params['class'] = "";
				$params['innerHtml'] = "<div style='".$style."' class='avia_textblock avia_textblock_style' data-update_with='content'>"
								.stripslashes(wpautop(trim(html_entity_decode( $params['content']) )))."</div>";
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

				extract(shortcode_atts(array('padding_left_right' => '', 'padding_top_bottom' => ''), $atts));

                $html = "";

                if(!$swedenUnlimited['client']->isPhone) {
                    $padding_left_right = $padding_left_right."px";
                    $padding_top_bottom = $padding_top_bottom."px";
                    $html = "<div class='text-block article-subhead sans {$custom_class}' style='padding:".$padding_top_bottom." ".$padding_left_right.";'>".$content."</div>";
                } else {
                    $html = "<div class='text-block article-subhead sans {$custom_class}'>".$content."</div>";
                }

				return $html;
			}

	}
}
