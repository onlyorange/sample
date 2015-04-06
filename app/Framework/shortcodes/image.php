<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'avia_sc_image' ) )
{
    class avia_sc_image extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Image', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-image.png";
                $this->config['order']			= 10;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'av_image';
                $this->config['modal_data']     = array('modal_class' => 'mediumscreen');
                $this->config['tooltip'] 	    = __('Inserts an image of your choice', 'swedenWp' );
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
                            "name" 	=> __("Choose Image",'swedenWp' ),
                            "desc" 	=> __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                            "id" 	=> "src",
                            "type" 	=> "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"),

                	array(	"name"  => __("Mobile Image", 'swedenWp' ),
                			"desc"  => __('Select "Yes" to upload a separate image for mobile devices', 'swedenWp' ),
                			"id"    => "mobile_fallback",
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

                    array(
                            "name"  => __("Image Margin", 'swedenWp' ),
                            "desc"  => __("Select \"Margin\" to add a margin between image and container", 'swedenWp' ),
                            "id"    => "margin",
                            "type"  => "select",
                            "std"   => "",
                            "subtype" => array(
                                                __('No margin',  'swedenWp' ) =>'',
                                                __('Margin',  'swedenWp' ) =>'has-margin',
                                                )
                            ),
                    // Deprecated: This setting as no effect, as images will always stretch to fill their container.
                    // array(
                    //         "name" 	=> __("Image Alignment", 'swedenWp' ),
                    //         "desc" 	=> __("Choose alignment of image within container", 'swedenWp' ),
                    //         "id" 	=> "align",
                    //         "type" 	=> "select",
                    //         "std" 	=> "center",
                    //         "subtype" => array(
                    //                             __('Center',  'swedenWp' ) =>'center',
                    //                             __('Right',  'swedenWp' ) =>'right',
                    //                             __('left',  'swedenWp' ) =>'left',
                    //                             __('No special alignment', 'swedenWp' ) =>'',
                    //                             )
                    //         ),

                    array(
                            "name" 	=> __("Image Fade in Animation", 'swedenWp' ),
                            "desc" 	=> __("Select an animation to render on image when it first scrolls into view (animations will only display on modern browsers)", 'swedenWp' ),
                            "id" 	=> "animation",
                            "type" 	=> "select",
                            "std" 	=> "no-animation",
                            "subtype" => array(
                                                __('None',  'swedenWp' ) =>'no-animation',
                                                __('Top to Bottom',  'swedenWp' ) =>'top-to-bottom',
                                                __('Bottom to Top',  'swedenWp' ) =>'bottom-to-top',
                                                __('Left to Right',  'swedenWp' ) =>'left-to-right',
                                                __('Right to Left',  'swedenWp' ) =>'right-to-left',
                                                )
                            ),

                     array(
                            "name" 	=> __("Link", 'swedenWp' ),
                            "desc" 	=> __("Select link location for image (defaults to no link)", 'swedenWp' ),
                            "id" 	=> "link",
                            "type" 	=> "linkpicker",
                            "fetchTMPL"	=> true,
                            "std"	=> "",
                            "subtype" => array(
                                                __('No Link', 'swedenWp' ) =>'',
                                                __('Set Manually', 'swedenWp' ) =>'manually',
                                                __('Single Entry', 'swedenWp' ) =>'single',
                                                __('Taxonomy Overview Page',  'swedenWp' )=>'taxonomy',
                                                ),
                            "std" 	=> ""),

                    array(
                        "name"  => __("Open Link in New Window", 'swedenWp' ),
                        "desc"  => __("Select \"Yes\" to force image link to open in new window/tab if set", 'swedenWp' ),
                        "id"    => "target",
                        "type"  => "select",
                        "std"   => "no",
                        "required"  => array('link','not',''),
                        "subtype" => array(
                            __('No', 'swedenWp' ) =>'no',
                            __('Yes',  'swedenWp' )=>'yes'
                        ),
                        "std"   => ""),

                     array(
                            "name"  => __("Image Caption", 'swedenWp' ),
                            "desc"  => __("Text to appear below image", 'swedenWp' ),
                            "id"    => "caption",
                            "type"  => "tiny_mce",
                            "std"   => "")
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
                $template = $this->update_template("src", "<img src='{{src}}' alt=''/>");
                $img	  = "";

                if(isset($params['args']['src']) && is_numeric($params['args']['src']))
                {
                    $img = wp_get_attachment_image($params['args']['src'],'large');
                }
                else if(!empty($params['args']['src']))
                {
                    $img = "<img src='".$params['args']['src']."' alt=''  />";
                }

                $params['content'] = NULL;
                $params['innerHtml']  = "<div class='avia_image avia_image_style avia_hidden_bg_box'>";
                $params['innerHtml'] .= "<div ".$this->class_by_arguments('align' ,$params['args']).">";
                $params['innerHtml'] .= "<div class='avia_image_container' {$template}>{$img}</div>";
                $params['innerHtml'] .= "</div>";
                $params['innerHtml'] .= "</div>";
                $params['class'] = "";

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
                $output = "";
                $class  = "";
                $alt 	= "";
                $title 	= "";
                global $swedenUnlimited;

                extract(
                    shortcode_atts(
                        array(
                            'src'=>'',
                            'mobile_fallback' => '',
                            'src_mobile' => '',
                            'animation'=>'no-animation',
                            'link'=>'', 'caption' => '',
                            'margin'=>'',
                			'attachment'=>'',
                            'target'=>'no',
                            'layout_size'=>''), $atts)
                    );

                $splitVar = explode(',', $atts['link']);
                if ($splitVar[0] == 'manually') {
                	// ignore the manual entry
                } else {
                	$selectedPostID = isset($splitVar[1]) ? $splitVar[1]:'';
                }
                if(!isset($swedenUnlimited['usedPost']) && isset($selectedPostID)) $swedenUnlimited['usedPost'] = array($selectedPostID);
                elseif(isset($swedenUnlimited['usedPost']) && isset($selectedPostID)) array_push($swedenUnlimited['usedPost'], $selectedPostID);

                update_post_meta(get_the_ID(), 'post_displayed', $swedenUnlimited['usedPost']);

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

                if(!empty($attachment))
                {
                    // mobile image?

                    $attachment_entry = get_post( $attachment );
                    $alt = get_post_meta($attachment_entry->ID, '_wp_attachment_image_alt', true);
                    $alt = !empty($alt) ? esc_attr($alt) : '';
                    $title = trim($attachment_entry->post_title) ? esc_attr($attachment_entry->post_title) : "";
                }
                if(!empty($src))
                {
                    $class = $animation == "no-animation" ? "" :"avia_animated_image avia_animate_when_almost_visible ".$animation;

                    $link = swedenWpFunctions::get_url($link);
                    $linktarget = ($target == 'yes') ? "target='_blank'" : '';

                    if($mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone){
                        $attachment = $src_mobile;
                        $mobimgsize = '1/1-image-with-text';
                    } elseif($swedenUnlimited['client']->isPhone){
                        $mobimgsize = 'large-thumbnail';
                    }
                    if($link) {
                        $output .= "<div class='image-element ".$margin."'>";

                    	if($swedenUnlimited['client']->isPhone)
                    		// get phone image
                    		$output.= "<a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget}>".
                    			wp_get_attachment_image($attachment, $mobimgsize)."</a>";
                    	else if($swedenUnlimited['client']->isTablet)
                    		$output.= "<a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget}>".
                    			wp_get_attachment_image($attachment, $imgsize)."</a>";
                    	else
                    		$output.= "<a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget}>".
                    			wp_get_attachment_image($attachment, $imgsize)."</a>";


                        if ($caption)
                        {
                            $output .= '<div class="image-caption">'. $caption . '</div>';
                        }

                        $output .= "</div>";


                    } else {
                        $output .= "<div class='image-element ".$margin."'>";

                    	if($swedenUnlimited['client']->isPhone)
                    		// get phone image
                    		$output.= wp_get_attachment_image($attachment, $imgsize);
                    	else if($swedenUnlimited['client']->isTablet)
                    		// get tablet image
                    		$output.= wp_get_attachment_image($attachment, $imgsize);
                    	else
                    		$output.= wp_get_attachment_image($attachment, $imgsize);

                        if ($caption)
                        {
                            $output .= '<div class="image-caption">'. $caption . '</div>';
                        }

                        $output .= "</div>";
                    }
                    //<img class='avia_image {$class}' src='{$src}' alt='{$alt}' title='{$title}' />
                    //<img class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$src}' alt='{$alt}' title='{$title}' />
                    /*
                    if(is_numeric($src))
                    {

                        $output = wp_get_attachment_image($src,'large');
                        $output .= '111';
                    } else {
                        $link = swedenWpFunctions::get_url($link);
                        $linktarget = ($target == 'yes') ? "target='_blank'" : '';

                        if($link)
                        {
                            $output.= "<div class='image-element ".$this->class_by_arguments('align' ,$atts, true)." ".$margin."'><a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget}><img class='avia_image {$class}' src='{$src}' alt='{$alt}' title='{$title}' /></a></div>";
                        }
                        else
                        {
                            $output.= "<div class='image-element ".$this->class_by_arguments('align' ,$atts, true)." ".$margin."'><img class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$src}' alt='{$alt}' title='{$title}' /></div>";
                        }
                    }
                    */

                }

                return $output;
            }

    }
}

