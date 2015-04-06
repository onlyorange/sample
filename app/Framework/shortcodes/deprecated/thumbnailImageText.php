<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'thumbnail_with_text' ) )
{
	class thumbnail_with_text extends swedenShortcodeTemplate
	{

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['name']			= __('Thumbnail with Text', 'swedenWp' );
			$this->config['tab']			= __('Content Elements', 'swedenWp' );
			$this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-text_block.png";
			$this->config['order']			= 19;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'sw_thumb_text';
			$this->config['modal_data']     = array('modal_class' => 'bigscreen');
			$this->config['tooltip'] 	    = __('Creates a content box with a thumbnail and a button', 'swedenWp' );
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
			if (isset($_GET['post'])) {
				$subTitle = get_post_meta($_GET['post'], 'category_heading', true);
				if(!empty($subTitle))
					$catTitle = get_post_meta($_GET['post'], 'category_heading', true);
				else
					$catTitle = '';
			} else {
				$catTitle = '';
			}
			$this->elements = array(
				array(	"name"  => __("Title Type", 'swedenWp' ),
						"desc"  => __("Choose title type", 'swedenWp' ),
						"id"    => "title_type",
						"type"  => "select",
						"std"   => "text",
						"subtype" => array(
								__('Text title', 'swedenWp') =>'text',
								__('Image title', 'swedenWp') =>'image',
							)
						),

				array(	"name" 	=> __("Title", 'swedenWp' ),
						"desc" 	=> __("This is the text that appears on title area.", 'swedenWp' ),
						"id" 	=> "title",
						"type" 	=> "tiny_mce",
						"required" => array('title_type','equals','text'),
						"std" => ""),

				array(	"name" 	=> __("Choose Image", 'swedenWp' ),
						"desc" 	=> __("This is the image that appears on title area.", 'swedenWp' ),
						"id" 	=> "src",
						"type" 	=> "image_title",
						"required" => array('title_type','equals','image'),
						"title" => __("Insert Image",'swedenWp' ),
						"button" => __("Insert",'swedenWp' ),
						"std" 	=> ""),
				// array(	"name"      => __("Image Alignment", 'swedenWp' ),
				// 	"desc"     => __("Special image alignement option.", 'swedenWp' ),
				// 	"id"        => "img_align",
				// 	"type"      => "select",
				// 	"std"       => "no",
				// 	"required" => array('title_type','equals','image'),
				// 	"subtype" => array(
				// 		__('Push down top',  'swedenWp' ) =>'down',
				// 		__('Push up bottom',  'swedenWp' ) =>'up',
				// 		__('No special alignment', 'swedenWp' ) =>'no',
				// 	)
				// ),

				/************************ ends title ******************/

                array(  "name"  => __("Category Title", 'swedenWp' ),
                        "desc"  => __("This is the text that appears on the category title area. Will be overridden if post has been selected in CTA", 'swedenWp' ),
                        "id"    => "category_title",
                        "type"  => "input",
                        "std" => $catTitle),

                array(
                        "name" 	=> __("Content Card Parent Type", 'swedenWp' ),
                        "desc"  => __("Basic styling and positioning schemes for the text content.", 'swedenWp' ),
                        "id" 	=> "parent_type",
                        "type" 	=> "select",
                        "std" 	=> "category",
                        "subtype" => array(
                                __('home',  'swedenWp' ) =>'grid-element-home',
                                //__('full-width hero',  'swedenWp' ) =>'grid-element-home hero', AK: the thumbnail shortcode was never intended to be a full width hero
                                __('category',  'swedenWp' ) =>'grid-element-category',
                                __('subcategory',  'swedenWp' ) =>'grid-element-subcategory'
                        )
                ),

                /***************** parent type ************************/
                array(
                        "name" 	=> __("Choose Image",'swedenWp' ),
                        "desc"  => __("Upload a new image or choose an existing one from your media library.",'swedenWp' ),
                        "id" 	=> "imgsrc",
                        "type" 	=> "image",
                        "title" => __("Insert Image",'swedenWp' ),
                        "button" => __("Insert",'swedenWp' ),
                        "std" 	=> ""
                ),
                /************************ ends image ******************/

                array(
                        "name" 	=> __("Content Card Style", 'swedenWp' ),
                        "desc"  => __("Choose a visual style for your text content,", 'swedenWp' ),
                        "id" 	=> "card_type",
                        "type" 	=> "select",
                        "std" 	=> "Text Overlay",
                        "subtype" => array(
                                __('White Block',  'swedenWp' ) =>'white-card',
                                // __('Thin-stroke Outline',  'swedenWp' ) =>'white-card is-outline',
                                __('Text Overlay',  'swedenWp' ) =>'white-card is-no-card'
                        )
                ),


                array(
                        "name" 	=> __("Content Text Color", 'swedenWp' ),
                        "desc" 	=> __("Choose a color for your text content.", 'swedenWp' ),
                        "id" 	=> "text_color",
                        "type" 	=> "select",
                        "std" 	=> "is-black",
                        "subtype" => array(
                                __('Black', 'swedenWp' )=>'is-black',
                                __('White', 'swedenWp' )=>'is-white',
                        )),


                array(
                        "name" 	=> __("Content Alignment", 'swedenWp' ),
                        "desc"  => __("Choose an alignment for your text content.", 'swedenWp' ),
                        "id" 	=> "align",
                        "type" 	=> "select",
                        "std" 	=> "center",
                        "subtype" => array(
                                __('Center',  'swedenWp' ) =>'center',
                                __('Right',  'swedenWp' ) =>'right avia-align-right is-right-aligned',
                                __('Left',  'swedenWp' ) =>'left avia-align-left is-left-aligned',
                                __('Top Right',  'swedenWp' ) =>'top-right is-top-right-aligned',
                                __('Top Left',  'swedenWp' ) =>'top-left is-top-left-aligned',
                                __('Top',  'swedenWp' ) =>'top is-top-aligned',
                                __('Bottom',  'swedenWp' ) =>'bottom is-bottom-aligned',
                                __('Bottom Right',  'swedenWp' ) =>'bottom-right is-bottom-right-aligned',
                                __('Bottom Left',  'swedenWp' ) =>'bottom-left is-bottom-left-aligned',
                                __('No special alignment', 'swedenWp' ) =>'',
                        )
                ),

                /************************ ends content ******************/
                array(
                        "name" 	=> __("CTA", 'swedenWp' ),
                        "desc" 	=> __("Do you want to display a Call To Action button?", 'swedenWp' ),
                        "id" 	=> "button",
                        "type" 	=> "select",
                        "std" 	=> "yes",
                        "subtype" => array(
                            __('yes',  'swedenWp' ) =>'yes',
                            __('no',  'swedenWp' ) =>'no',
                            )
                            ),

                array(	"name" 	=> __("CTA Label", 'swedenWp' ),
                        "desc" 	=> __("This is the text that appears on the button.", 'swedenWp' ),
                        "id" 	=> "label",
                        "type" 	=> "select",
                        "required" => array('button','equals','yes'),
                        "subtype" => swedenWpFunctions::get_saved_cta_value(1),
                        "std" => get_option('CTA_1')),
                array(
                        "name" 	=> __("Button Link?", 'swedenWp' ),
                        "desc" 	=> __("Where should the CTA link to?", 'swedenWp' ),
                        "id" 	=> "link",
                        "type" 	=> "linkpicker",
                        "required" => array('button','equals','yes'),
                        "fetchTMPL"	=> true,
                        "subtype" => array(
                                            __('Set Manually', 'swedenWp' ) =>'manually',
                                            __('Single Entry', 'swedenWp' ) =>'single',
                                            __('Taxonomy Overview Page',  'swedenWp' )=>'taxonomy',
                                            ),
                        "std" 	=> "single"),

                array(
                        "name" 	=> __("Open Link in new Window?", 'swedenWp' ),
                        "desc"  => __("Should the CTA open its link in a new window", 'swedenWp' ),
                        "id" 	=> "link_target",
                        "type" 	=> "select",
                        "std" 	=> "",
                        "required" => array('button','equals','yes'),
                        "subtype" => array(
                            __('Open in same window',  'swedenWp' ) =>'',
                            __('Open in new window',  'swedenWp' ) =>'_blank')),

                array(
                        "name" 	=> __("Button Color", 'swedenWp' ),
                        "desc" 	=> __("Choose a color for the CTA.", 'swedenWp' ),
                        "id" 	=> "color",
                        "type" 	=> "select",
                        "std" 	=> "theme-color",
                        "required" => array('button','equals','yes'),
                        "subtype" => array(
                                            __('Black', 'swedenWp' )=>'is-black',
                                            __('White', 'swedenWp' )=>'is-white',
                                            __('Custom Color', 'swedenWp' )=>'custom',
                                            )),

                array(
                        "name" 	=> __("Custom Background Color", 'swedenWp' ),
                        "desc" 	=> __("Select a custom background color for the CTA.", 'swedenWp' ),
                        "id" 	=> "custom_bg",
                        "type" 	=> "colorpicker",
                        "std" 	=> "#444444",
                        "required" => array('color','equals','custom')
                    ),

                array(
                        "name" 	=> __("Custom Font Color", 'swedenWp' ),
                        "desc" 	=> __("Select a custom font color for the CTA.", 'swedenWp' ),
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
        function editor_element($params)
        {
            $template_title = $this->update_template("src", "<img src='{{src}}' alt=''/>");
            $template_body = $this->update_template("imgsrc", "<img src='{{imgsrc}}' alt=''/>");
            $title	  = "";
            $img = "";

            // show title in editor
            if (!empty($params['args']['title'])) {
                $title = "<p>" . $params['args']['title']. "</p>";
            } else if (empty($params['args']['title']) && isset($params['args']['src']) && is_numeric($params['args']['src'])) {
                $title = wp_get_attachment_image($params['args']['src'],'large');
            } else if (!empty($params['args']['src']) && empty($params['args']['title'])) {
                $title = "<img src='".$params['args']['src']."' alt=''  />";
            } else {

            }

            if (isset($params['args']['imgsrc']) && is_numeric($params['args']['imgsrc'])) {
                $img = wp_get_attachment_image($params['args']['imgsrc'],'large');
            } else if (!empty($params['args']['imgsrc'])) {
                $img = "<img src='".$params['args']['imgsrc']."' alt=''  />";
            }
            $params['class'] = "";
            $params['innerHtml']  = "";
            $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style'>";
            $params['innerHtml'] .= "	<div ".$this->class_by_arguments('button' ,$params['args']).">";
            $params['innerHtml'] .= "		<div data-update_with='content' class='avia-promocontent'>";
            $params['innerHtml'] .= "			<div class='avia_image_container' {$template_title}>{$title}</div>";
            $params['innerHtml'] .= "			<div class='avia_image_container' {$template_body}>{$img}</div>";
            $params['innerHtml'] .= 			stripslashes(wpautop(trim($params['content'])))."</div>";
            if ($params['args']['button'] == 'yes') {
                $params['innerHtml'] .= "		<div class='avia_button_box avia_hidden_bg_box'>";
                $params['innerHtml'] .= "			<div ".$this->class_by_arguments('color' ,$params['args']).">";
                $params['innerHtml'] .= "				<span data-update_with='label' class='avia_title' >".$params['args']['label']."</span>";
                $params['innerHtml'] .= "			</div>";
                $params['innerHtml'] .= "		</div>";
            }
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
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
        {
            global $swedenUnlimited;
            $output = "";
            $class  = "";
            $alt 	= "";
            $title 	= "";
            $splitVar = '';

            extract(shortcode_atts(array('imgsrc'=>'', 'alt_title'=>'', 'title_title'=>'','category_title'=>'', 'src'=>'', 'link'=>'', 'attachment'=>'', 'attachment_title' => '', 'target'=>'no','layout_size'=>''), $atts));
            $atts =  shortcode_atts(array(
                                         'title_type' => "",
                                         'title' => "",
                                         'image' => "",
                                         'src' => "",
                                         'imgsrc' => "",
                                         'parent_type' => "home",
                                         'align' => "",
                                         'card_type' => "",
                                         'img_layout' => "",
                                         // 'img_align' => "",
                                         'text_color' => 'is-black',
                                         // 'content' => "",
                                         'button' => 'yes',
                                         'label' => 'Read More A',
                                         'link' => '',
                                         'link_target' => '',
                                         'color' => 'is-black',
                                         'custom_bg' => '#444444',
                                         'custom_font' => '#ffffff',
                                         'position' => 'center',
                                         'category_title' => ''
                                         ), $atts);
            $splitVar = explode(',', $atts['link']);
            if ($splitVar[0] == 'manually') {
                // ignore the manual entry
            } else {
                $selectedPostID = isset($splitVar[1]) ? $splitVar[1]:'';
            }
            if(!isset($swedenUnlimited['usedPost']) && isset($selectedPostID)) $swedenUnlimited['usedPost'] = array($selectedPostID);
            elseif(isset($swedenUnlimited['usedPost']) && isset($selectedPostID)) array_push($swedenUnlimited['usedPost'], $selectedPostID);

            update_post_meta(get_the_ID(), 'post_displayed', $swedenUnlimited['usedPost']);

            // image with text -> removing parent page type

            /**
            $postMeata = get_post_meta(get_the_ID());
            //print_r($postMeata);
            if (is_front_page()) {
                $atts['parent_type'] = 'grid-element-home';
            } else {
                if ($postMeata['_wp_page_template'][0] == 'page-category.php') {
                    $atts['parent_type'] = 'grid-element-category';
                }
                //echo '<p>page template : '.$postMeata['_wp_page_template'][0]. '</p>';
                //echo '<p>post type key : '. $postMeata['post_type_key'][0]. '</p>';
                //echo '<p>category type key : ' . $postMeata['category_type_key'][0]. '</p>';
                //echo '<br/><br/><br/>';
                $atts['parent_type'] = 'grid-element-category';
            }
            **/
            // sampling

                /**
                *
                * __('home',  'swedenWp' ) =>'grid-element-home',
                __('full-width hero',  'swedenWp' ) =>'grid-element-home hero',
                __('category',  'swedenWp' ) =>'grid-element-category',
                __('subcategory',  'swedenWp' ) =>'grid-element-subcategory',
                __('shopnow',  'swedenWp' ) =>'grid-element-shop-now',
                __('none',  'swedenWp' ) =>'',
                */

            $blank = $atts['link_target'] ? 'target="_blank" ' : "";

            if (!empty($link)) {
                $link  = swedenWpFunctions::get_url($atts['link']);
                $link  = $link == "http://" ? "" : $link;
            }

            if (!empty($attachment)) {
                $attachment_entry = get_post( $attachment );
                $alt = get_post_meta($attachment_entry->ID, '_wp_attachment_image_alt', true);
                $alt = !empty($alt) ? esc_attr($alt) : '';
                $title = trim($attachment_entry->post_title) ? esc_attr($attachment_entry->post_title) : "";
            }
            if ($atts['title_type'] == 'image' && !empty($attachment_title)) {
                $attachment_entry_title = get_post( $attachment_title );
                $alt_title = get_post_meta($attachment_entry_title->ID, '_wp_attachment_image_alt', true);
                $alt_title = !empty($alt_title) ? esc_attr($alt_title) : '';
                $title_title = trim($attachment_entry_title->post_title) ? esc_attr($attachment_entry_title->post_title) : "";
            }

            if(!empty($atts['title'])) {
                $atts_title = trim(strip_tags($atts['title']));
                $alt = empty($alt) ? $atts_title : $alt;
                $title = empty($title) ? $atts_title : $title;
                $alt_title = empty($alt_title) ? $atts_title : $alt_title;
                $title_title = empty($title_title) ? $atts_title : $title_title;
            }

            if($atts['parent_type'] == "grid-element-shop-now")
            {
                // wrapper
                $output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .'">';
            }
            else
            {
                // wrapper
                $output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
            }


            $imgsize = '1/1-image-with-text+hero';
            if (!empty($layout_size)) {
                switch ($layout_size) {
                    case 'one_half':
                        $imgsize = '1/2-image-with-text';
                        break;

                    case 'one_third':
                        $imgsize = '1/3-image-with-text';
                        if ($atts['parent_type']==="grid-element-category") {
                            $imgsize = 'large-thumbnail';
                        }
                        break;

                    case 'one_fourth':
                        $imgsize = '1/4-image-with-text';
                        break;

                    case 'two_third':
                        $imgsize = '2/3-image-with-text';
                        break;

                    default:
                        $imgsize = '1/1-image-with-text+hero';
                        break;
                }

            }

            // content image
            $output .= "<div class='image-container {$layout_size}>";
            if (!empty($attachment)) {
            	if($swedenUnlimited['client']->isPhone)
            		// get phone image
            		$output .= "<a href='{$link}' title='{$title}'>".wp_get_attachment_image($attachment,'large-thumbnail')."</a>";
            	else if($swedenUnlimited['client']->isTablet)
            		$output .= "<a href='{$link}' title='{$title}'>".wp_get_attachment_image($attachment,'')."</a>";
            	else
            		$output .= "<a href='{$link}' title='{$title}'>".wp_get_attachment_image($attachment,'')."</a>";

            } else {
				$output.= "	<a href='{$link}' title='{$title}'><img class='avia_image ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$imgsrc}' alt='{$alt}' title='{$title}' /></a>";
			}
            //close .image-container
            $output .= "</div>";


            // content
			if($atts['parent_type'] == "grid-element-shop-now") {
					$output .='<div class="content-card-container '.$atts['align'].'">';
                    $output .='  <div class="content-card cat-landing-content-block'.$atts['text_color'].'">';
                    $output .='    <div class="table-container">';
					$output .='      <h3 class="headline">'.$atts['title'].'</h3>';
					// $output .='    <p>'.stripslashes(wpautop(trim($content))).'</p>';

			} else {

				// category
				$link  = swedenWpFunctions::get_url($atts['link']);
				$link  = $link == "http://" ? "" : $link;

				// content
                $output .='<div class="content-card-container '.$atts['align'].'">';
				$output .='  <div class="content-card cat-landing-content-block '.$atts['card_type'].' '.$atts['text_color'].'">';
                $output .='    <div class="table-container">';

				if (!empty($link)) {
					if($category_title) $output.= '<span class="category slug-small">'.$category_title.'</span>';
					else $output.= 	swedenWpFunctions::all_taxonomies_links($selectedPostID, "small");

				} else if ($category_title) {
                    $output.= '<span class="category slug-small">'.$category_title.'</span>';
                }

				// title
				if (!empty($src) && $atts['title_type']!="text") {
					if ($attachment_title) {
						if($swedenUnlimited['client']->isPhone)
							// get phone image
							$output .= wp_get_attachment_image($src,'large');
						else if($swedenUnlimited['client']->isTablet)
							$output .= wp_get_attachment_image($src,'large');
						else
							$output .= wp_get_attachment_image($src,'large');

					} else {
						$output .= "<img class='avia_image ".$meta['el_class']." {$class}' src='{$src}' alt='{$alt_title}' title='{$title_title}' />";
					}

				} else {
                    $output_title = $atts['title'];
					$output.= "		<h2 class='headline'>" . trimOnWord($output_title,28) . "</h2>";
				}

				// if (!empty($content)) {
				// 	$output.= "				<div class='slug'>". stripslashes(wpautop(trim($content))) . "</div>";
				// }
			} // end if

            // CTA
            if ($atts['button'] == "yes") {
                $style = "";
                if ($atts['color'] == "custom") {
                    $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                }

                $output .= "	<div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";
                $output .= "		<a href='{$link}' class='swBtn ".$this->class_by_arguments('color, position' , $atts, true)."' {$blank} {$style} title='{$atts['label']}'>";

                // AK: still needs this parent_type stuff?
                if ($atts['parent_type'] == "grid-element-shop-now") {
                    $output .= "            <span class='grid-element-shop-now-link' >".$atts['label']." ></span>";

                } else {
                    $output .= "            <span class='read-more cta'>".$atts['label']."<span class='icon-arrow-right ".$atts['text_color']."'></span></span>";
                }

				$output .= "		</a>";
				$output .= "	</div>";
			}
            //close table-container
            $output.= "       </div>";
			//close content-card
			$output.= "		</div>";
            //close content-card-container
            $output.= "   </div>";
            //close wrapper
            $output.= "	</div>";

            return do_shortcode($output);

        }
    }
}
