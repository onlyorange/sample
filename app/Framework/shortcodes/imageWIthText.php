<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'image_with_text' ) )
{

	class image_with_text extends swedenShortcodeTemplate
	{

			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Image with Text', 'swedenWp' );
				$this->config['tab']			= __('Content Elements', 'swedenWp' );
				$this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-text_block.png";
				$this->config['order']			= 12;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'sw_content_box';
				$this->config['modal_data']     = array('modal_class' => 'bigscreen');
				$this->config['tooltip'] 	    = __('Creates a content box with images and buttons', 'swedenWp' );
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
							"desc"  => __("Select \"Image title\" to replace title with an image", 'swedenWp' ),
							"id"    => "title_type",
							"type"  => "select",
							"std"   => "text",
							"subtype" => array(
									__('Text title', 'swedenWp') =>'text',
									__('Image title', 'swedenWp') =>'image',
								)
							),

					array(	"name" 	=> __("Title", 'swedenWp' ),
							"desc" 	=> __("Text for title of content card", 'swedenWp' ),
							"id" 	=> "title",
							"type" 	=> "input",
							"required" => array('title_type','equals','text'),
							"std" => ""),

					array(	"name" 	=> __("Choose Title Image", 'swedenWp' ),
							"desc" 	=> __("Choose an image to use as title", 'swedenWp' ),
							"id" 	=> "src",
							"type" 	=> "image_title",
							"required" => array('title_type','equals','image'),
							"title" => __("Insert Image",'swedenWp' ),
							"button" => __("Insert",'swedenWp' ),
							"std" 	=> ""),
					array(	"name"  => __("Mobile Image", 'swedenWp' ),
							"desc"  => __('Select "Yes" to upload a separate image for mobile devices', 'swedenWp' ),
							"id"    => "title_mobile_fallback",
							"required" => array('title_type','equals','image'),
							"type"  => "select",
							"std"   => "no",
							"subtype" => array(
									__('Yes', 'swedenWp') =>'yes',
									__('No', 'swedenWp') =>'no',
							)
					),
					array(
							"name" 	=> __("Choose Mobile Image",'swedenWp' ),
							"desc" 	=> __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
							"id" 	=> "title_src_mobile",
							"type" 	=> "image_title_mobile",
							"fetch" => "id",
							"required" => array('title_mobile_fallback','equals','yes'),
							"title" => __("Insert Image",'swedenWp' ),
							"button" => __("Insert",'swedenWp' ),
							"std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"),
					/************************ ends title ******************/

                    array(  "name"  => __("Category", 'swedenWp' ),
                            "desc"  => __("Manually set the category slug of the content card. Leave empty to use the category of post selected in CTA", 'swedenWp' ),
                            "id"    => "category_title",
                            "type"  => "input",
                            "std" => $catTitle),

                    array(
                            "name" 	=> __("Content Card Lockup", 'swedenWp' ),
                            "desc"  => __("Choose style lockup for content card", 'swedenWp' ),
                            "id" 	=> "parent_type",
                            "type" 	=> "select",
                            "std" 	=> "",
                            "subtype" => array(
                                    __('home',  'swedenWp' ) =>'grid-element-home',
                                    __('category',  'swedenWp' ) =>'grid-element-category',
                                    __('subcategory',  'swedenWp' ) =>'grid-element-subcategory',
                                    __('shopnow',  'swedenWp' ) =>'grid-element-shop-now',
                                    __('none',  'swedenWp' ) =>''                                      //AK: doesn't seem to affect or offer anything
                            )
                    ),

                    /***************** parent type ************************/

                    array(	"name"  => __("Display Image", 'swedenWp' ),
                            "desc"  => __("Select \"No\" if element is text only", 'swedenWp' ),
                            "id"    => "image",
                            "type"  => "select",
                            "std"   => "yes",
                            "subtype" => array(
                                    __('Yes', 'swedenWp') =>'yes',
                                    __('No', 'swedenWp') =>'no',
                            )
                    ),
                    array(
                            "name" 	=> __("Choose Image",'swedenWp' ),
                            "desc" 	=> __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                            "id" 	=> "imgsrc",
                            "type" 	=> "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "required" => array('image','equals','yes'),
                            "std" 	=> ""),

					array(	"name"  => __("Mobile Image", 'swedenWp' ),
							"desc"  => __('Select "Yes" to upload a separate image for mobile devices', 'swedenWp' ),
							"id"    => "mobile_fallback",
							"required" => array('image','equals','yes'),
							"type"  => "select",
							"std"   => "no",
							"subtype" => array(
										__('Yes', 'swedenWp') =>'yes',
										__('No', 'swedenWp') =>'no',
							)
					),
					array(
							"name" 	=> __("Choose Mobile Image",'swedenWp' ),
							"desc" 	=> __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
							"id" 	=> "src_mobile",
							"type" 	=> "image_mobile",
							"fetch" => "id",
							"required" => array('mobile_fallback','equals','yes'),
							"title" => __("Insert Image",'swedenWp' ),
							"button" => __("Insert",'swedenWp' ),
							"std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"),

                    // array(
                    //         "name" 	=> __("Image Alignment", 'swedenWp' ),
                    //         "desc" 	=> __("Choose the vertical alignment of image", 'swedenWp' ),
                    //         "id" 	=> "img_align",
                    //         "type" 	=> "select",
                    //         "std" 	=> "no",
                    //         "required" => array('image','equals','yes'),
                    //         "subtype" => array(
                    //                 __('Push down top',  'swedenWp' ) =>'down',
                    //                 __('Push up bottom',  'swedenWp' ) =>'up',
                    //                 __('No special alignment', 'swedenWp' ) =>'no',
                    //         )
                    // ),

                    array(
                            "name" 	=> __("Image Layout", 'swedenWp' ),
                            "desc" 	=> __("Choose whether image is rendered as a background or inline", 'swedenWp' ),
                            "id" 	=> "img_layout",
                            "type" 	=> "select",
                            "std" 	=> "not background",
                            "required" => array('image','equals','yes'),
                            "subtype" => array(
                                    __('not background',  'swedenWp' ) =>'',
                                    __('background',  'swedenWp' ) =>'has-image-as-bg',
                            )
                    ),
                    /************************ ends image ******************/

                    array(
                            "name" 	=> __("Content Card Style", 'swedenWp' ),
                            "desc" 	=> __("Select the visual style of the content card", 'swedenWp' ),
                            "id" 	=> "card_type",
                            "type" 	=> "select",
                            "std" 	=> "",
                            "subtype" => array(
                                    __('White Block',  'swedenWp' ) =>'white-card',
                                    __('Text Overlay',  'swedenWp' ) =>'white-card is-no-card',
                                 // __('Article Hero Text Overlay',  'swedenWp' ) =>'white-card is-no-card is-article-hero',
                                 // __('Article Collaborator White Block',  'swedenWp' ) =>'white-card is-article-collaborator',
                                    __('Get The Look',  'swedenWp' ) =>'white-card is-article-get-look',
                                    __('No Card', 'swedenWp') =>'no-card-content white-card is-no-card'
                            )
                    ),
                    array(
                            "name" 	=> __("Content Text Color", 'swedenWp' ),
                            "desc"  => __("Choose color for card text. May be overidden by context specific styles", 'swedenWp' ),
                            "id" 	=> "text_color",
                            "type" 	=> "select",
                            "std" 	=> "is-black",
                            "subtype" => array(
                                    __('Black', 'swedenWp' )=>'is-black',
                                    __('White', 'swedenWp' )=>'is-white',
                            )),

                    array(
                            "name" 	=> __("Card Contents",'swedenWp' ),
                            "desc" 	=> __("Body text for content card",'swedenWp' ),
                            "id" 	=> "content",
                            "type" 	=> "tiny_mce",
                            "std" 	=> __("<div class='styleclass 2'></div>", "swedenWp" )),
                    array(
                            "name" 	=> __("Content Card Alignment", 'swedenWp' ),
                            "desc" 	=> __("Choose the position of the content card on top of the image", 'swedenWp' ),
                            "id" 	=> "align",
                            "type" 	=> "select",
                            "std" 	=> "center",
                            "subtype" => array(
                                    __('Center',  'swedenWp' ) =>'center is-center-aligned',
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
                            "name" 	=> __("Display CTA", 'swedenWp' ),
                            "desc" 	=> __("Select \"Yes\" to display a Call to Action button", 'swedenWp' ),
                            "id" 	=> "button",
                            "type" 	=> "select",
                            "std" 	=> "no",
                            "subtype" => array(
                                __('Yes',  'swedenWp' ) =>'yes',
                                __('No',  'swedenWp' ) =>'no',
                                )
                                ),

                    array(	"name" 	=> __("CTA Label", 'swedenWp' ),
                            "desc" 	=> __("Choose text for Call to Action button", 'swedenWp' ),
                            "id" 	=> "label",
                            "type" 	=> "select",
                            "required" => array('button','equals','yes'),
                            "subtype" => swedenWpFunctions::get_saved_cta_value(1),
                            "std" => get_option('CTA_1')),
                    array(
                            "name" 	=> __("Button Link", 'swedenWp' ),
                            "desc" 	=> __("Set the CTA link to a published post, a product, or an outside page", 'swedenWp' ),
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
                            "name" 	=> __("Open Link in New Window", 'swedenWp' ),
                            "desc" 	=> __("Force CTA link to open in new window/tab", 'swedenWp' ),
                            "id" 	=> "link_target",
                            "type" 	=> "select",
                            "std" 	=> "",
                            "required" => array('button','equals','yes'),
                            "subtype" => array(
                                __('Open in same window',  'swedenWp' ) =>'',
                                __('Open in new window',  'swedenWp' ) =>'_blank')),

                    array(
                            "name" 	=> __("Button Color", 'swedenWp' ),
                            "desc" 	=> __("Select CTA text color", 'swedenWp' ),
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
                            "desc" 	=> __("Select CTA background color", 'swedenWp' ),
                            "id" 	=> "custom_bg",
                            "type" 	=> "colorpicker",
                            "std" 	=> "#444444",
                            "required" => array('color','equals','custom')
                        ),

                    array(
                            "name" 	=> __("Custom Font Color", 'swedenWp' ),
                            "desc" 	=> __("Set a custom font color for the CTA text", 'swedenWp' ),
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

                if (!empty($params['args']['title'])) {
                    $title = "<p>" . $params['args']['title']. "</p>";
                } else if (empty($params['args']['title']) && isset($params['args']['src']) && is_numeric($params['args']['src'])) {
                    $title = wp_get_attachment_image($params['args']['src'],'large');
                } else if (!empty($params['args']['src']) && empty($params['args']['title'])) {
                    $title = "<img src='".$params['args']['src']."' alt=''  />";
                } else {

                }
                //var_dump($title);
                if (isset($params['args']['imgsrc']) && is_numeric($params['args']['imgsrc'])) {
                    $img = wp_get_attachment_image($params['args']['imgsrc'],'large');
                } else if (!empty($params['args']['imgsrc'])) {
                    $img = "<img src='".$params['args']['imgsrc']."' alt=''  />";
                }
                $params['class'] = "";
                $params['innerHtml']  = "";
                $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style avia_hidden_bg_box'>";
                $params['innerHtml'] .= "	<div ".$this->class_by_arguments('button' ,$params['args']).">";
                $params['innerHtml'] .= "		<div class='avia-promocontent'>";
                $params['innerHtml'] .= "			<div class='avia_image_container' {$template_title}>{$title}</div>";
                $params['innerHtml'] .= "			<div class='avia_image_container' {$template_body}>{$img}</div>";
                $params['innerHtml'] .= "			<div data-update_with='content' class='element-content'>".stripslashes(wpautop(trim($params['content'])))."</div></div>";
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

                extract(shortcode_atts(array('imgsrc'=>'', 'mobile_fallback' => '', 'src_mobile' => '', 'title_mobile_fallback' => '', 'title_src_mobile' => '', 'alt_title'=>'', 'title_title'=>'','category_title'=>'', 'src'=>'', 'link'=>'', 'attachment'=>'', 'attachment_title' => '', 'target'=>'no','layout_size'=>''), $atts));
                $atts =  shortcode_atts(array(
                                             'title_type' => "",
                                             'title' => "",
                                             'image' => "",
                                             'src' => "",
                                             'imgsrc' => "",
                                             'parent_type' => "home",
                                             'align' => "",
                                             'card_type' => "",
                                             // 'img_align' => "",
                                             'img_layout' => "",
                                             'text_color' => '',
                                             'content' => "",
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
										$overlay = preg_match('/white\-card is\-no\-card/', $atts['card_type']) ? 'is-overlay ' : '';
                    $output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' '. $overlay . 'has-card">';
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
                if($mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone)
                	$attachment = $src_mobile;
                    $imgsize = '';
                if (!empty($attachment) && $atts['image'] == 'yes') {
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output.= "<div class='image-container {$layout_size}'>
                    			<a href='{$link}' {$blank} title='{$atts['label']}'>". wp_get_attachment_image($attachment, $imgsize). "</a></div>";
                    else if($swedenUnlimited['client']->isTablet)
                    	$output.= "<div class='image-container {$layout_size}'>
                    			<a href='{$link}' {$blank} title='{$atts['label']}'>". wp_get_attachment_image($attachment, $imgsize). "</a></div>";
                    else
                    	$output.= "<div class='image-container {$layout_size}'>
                    			<a href='{$link}' {$blank} title='{$atts['label']}'>". wp_get_attachment_image($attachment, $imgsize). "</a></div>";

                } else if (!empty($imgsrc) && $atts['image'] == 'yes') {

					if (is_numeric($imgsrc)) {
						$output .= "<div class='image-container ' ><a href='{$link}' title='{$atts['label']}'>".wp_get_attachment_image($imgsrc,$imgsize)."</a></div>";

					} else {
						$output.= "<div class='image-container '>";
						$output.= "	<a href='{$link}' title='{$atts['label']}'><img class='avia_image ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$imgsrc}' alt='{$alt}' title='{$title}' /></a>";
						$output.= "</div>";
					}
				}

				if ($atts['parent_type'] == "grid-element-shop-now") {
					// content
                    $output .='<div class="content-card-container '.$atts['align'].' ">';
					$output .='  <div class="content-card cat-landing-content-block '.$atts['text_color'].'">';
                    $output .='   <div class="table-container">';
					$output .='    <h3 class="headline">'.$atts['title'].'</h3>';
					$output .='    <div claass="subhead">'.stripslashes(wpautop(trim($content))).'</div>';

				} else {

					// category
					$link  = swedenWpFunctions::get_url($atts['link']);
					$link  = $link == "http://" ? "" : $link;

					// content
                    $output .='<div class="content-card-container '.$atts['align']. (($atts['card_type'] === 'no-card-content white-card is-no-card') ? " no-card-content" : "") . '" >';

                    $contentFormat = ($atts['parent_type'] == "grid-element-home") ? "special-content-block" : "cat-landing-content-block is-squished";

					$output .='  <div class="content-card '.$contentFormat.' '.$atts['card_type'].' '.$atts['text_color'].'" onclick="top.location.href=\''.$link.'\'">';
                    $output .='    <div class="table-container">';


                    if (!empty($link)) {
						if ($category_title) {
                            $output .= '<span class="slug small">'.$category_title.'</span>';

                        } else {
                            $output .= swedenWpFunctions::all_taxonomies_links($selectedPostID, "small");
                        }

					} else if ($category_title) {
                        $output.= '<span class="slug small">'.$category_title.'</span>';
                    }

					// title
					if (!empty($src)&&$atts['title_type']!="text") {
						if($title_mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone) {
							$attachment_title = $title_src_mobile;
						}
						if($swedenUnlimited['client']->isPhone)
							// get phone image
							$output.= "<div class='image-container {$layout_size}'>
									<a href='{$link}' {$blank}>". wp_get_attachment_image($attachment_title, $imagesize). "</a></div>";
						else if($swedenUnlimited['client']->isTablet)
							$output.= "<div class='image-container {$layout_size}'>
									<a href='{$link}' {$blank}>". wp_get_attachment_image($attachment_title, $imagesize). "</a></div>";
						else
							$output.= "<div class='image-container {$layout_size}'>
									<a href='{$link}' {$blank}>". wp_get_attachment_image($attachment_title, $imagesize). "</a></div>";
					} else {
                        $output_title = $atts['title'];
						$output.= "		<h2 class='content-title headline'>" . trimOnWord($output_title, 26) . "</h2>";
					}

					if (!empty($content)) {
						$output.= "				<div class='subhead'>". stripslashes(wpautop(trim($content))) . "</div>";
					}
				} // end if


                // CTA
                if ($atts['button'] == "yes") {
                    $style = "";
                    if ($atts['color'] == "custom") {
                        $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                    }

                    $output .= "	<div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";
                    $output .= "		<a href='{$link}' class='swBtn ".$this->class_by_arguments('color, position' , $atts, true)."' {$blank} {$style} title='{$atts['label']}'>";

                    if ($atts['parent_type'] == "grid-element-shop-now") {
                        $output .= "            <span class='grid-element-shop-now-link cta' >".$atts['label']."<span class='icon-arrow-right'></span></span>";
                    }
                    else
                    {
                        $output .= "            <span class='read-more cta' >".$atts['label']."<span class='icon-arrow-right'></span></span>";
                    }

					$output .= "		</a>";
					$output .= "	</div>";
				}

                // .table-container
                $output.= "    </div>";
				// .content-card
				$output.= "  </div>";
                // .content-card-container
                $output.= "</div>";

                //close wrapper
                $output.= "	</div>";

                return do_shortcode($output);
            }
    }
}
