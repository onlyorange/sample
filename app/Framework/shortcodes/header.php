<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'post_header' ) )
{
    class post_header extends swedenShortcodeTemplate
    {

            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Header', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-promobox.png";
                $this->config['order']			= 9;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_header_box';
                $this->config['modal_data']     = array('modal_class' => 'bigscreen');
                $this->config['tooltip'] 	    = __('Creates a header element', 'swedenWp' );
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
                $catTitle = '';

                if (isset($_GET['post'])) {
                    $subTitle = get_post_meta($_GET['post'], 'category_heading', true);
                    $catTitle = (!empty($subTitle)) ? $subTitle : '';
                }

                $customCTA = array_merge(array(' - Custom Label'=> 'custom'), swedenWpFunctions::get_saved_cta_value(1));

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
                            "desc" 	=> __("Header title text", 'swedenWp' ),
                            "id" 	=> "title_editor",
                            "type" 	=> "title_tiny_mce",
                            "required" => array('title_type','equals','text'),
                    		"std"   => __("", "swedenWp" )),

                    array(	"name" 	=> __("Choose Title Image", 'swedenWp' ),
                            "desc" 	=> __("Choose an image to use as title", 'swedenWp' ),
                            "id" 	=> "src",
                            "type" 	=> "image_title",
                            "required" => array('title_type','equals','image'),
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"
					),
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
                            "desc"  => __("Manually set the category slug of the header. Leave empty to use the category of post selected in CTA", 'swedenWp' ),
                            "id"    => "category_title",
                            "type"  => "input",
                            "std" => $catTitle),

                    /***************** ends category ************************/

                    array(  "name"  => __("Media Type", 'swedenWp' ),
                            "desc"  => __("Header can display an image, video, or no media at all", 'swedenWp' ),
                            "id"    => "media_type",
                            "type"  => "select",
                            "std"   => "none",
                            "subtype" => array(
                                    __('Video', 'swedenWp') => 'video',
                                    __('Image', 'swedenWp') => 'image',
                                    __('None', 'swedenWp') => 'none'
                            )
                    ),
                    array(
                            "name"  => __("Choose Header Image",'swedenWp' ),
                            "desc"  => __("Upload a new image or choose an existing image from the media library (images should be 1440 x 660 or 1440 x 827)",'swedenWp' ),
                            "id"    => "imgsrc",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "required" => array('media_type','equals','image'),
                            "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"),

                	array(	"name"  => __("Mobile Image", 'swedenWp' ),
                			"desc"  => __('Select "Yes" to upload a separate image for mobile devices', 'swedenWp' ),
                			"id"    => "mobile_fallback",
                			"required" => array('media_type','equals','image'),
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

                    /************************ ends image ******************/

                    array(  "name"  => __("Upload or Choose Video Cover Image", 'swedenWp' ),
                            "desc"  => __("The cover image will be displayed when the video is not playing (autoplay/loop videos will not display a cover image)", 'swedenWp' ),
                            "id"    => "video_cover_src",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "required" => array('media_type','equals','video'),
                            "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"
                    ),
                    array(  "name"  => __("Video Provider", 'swedenWp' ),
                            "desc"  => __("Videos must be served by Scene7 or YouTube", 'swedenWp' ),
                            "id"    => "video_type",
                            "type"  => "select",
                            "std"   => "no",
                            "required" => array('media_type','equals','video'),
                            "subtype" => array(
                                    __('Scene7', 'swedenWp') =>'scene7',
                                    __('YouTube', 'swedenWp') =>'youtube',
                            )
                    ),

                    array(
                            "name"  => __("Video Play Button Color", 'swedenWp' ),
                            "desc"  => __("Choose color for play button (displayed beneath Title and Content)", 'swedenWp' ),
                            "id"    => "btn_color",
                            "type"  => "select",
                            "std"   => "White",
                            "required" => array('media_type','equals','video'),
                            "subtype" => array(
                                                __('White',  'swedenWp' ) =>'is-white',
                                                __('Black', 'swedenWp' ) =>'is-black',
                                                )
                            ),

                    array(  "name"  => __("Video ID", 'swedenWp' ),
                            "desc"  => __("Enter ID for video", 'swedenWp' ),
                            "required" => array('media_type','equals','video'),
                            "id"    => "video_code",
                            "type"  => "input"),

                 array(  "name"  => __("Video Auto-Play & Loop", 'swedenWp' ),
                            "desc"  => __("Select \"Yes\" to automatically play and loop video (looped videos will not display a cover image, and will be muted by default)", 'swedenWp' ),
                            "required" => array('media_type','equals','video'),
                            "id"    => "video_loop",
                            "type"  => "select",
                            "std"   => "false",
                            "subtype" => array(
                                    __('Yes', 'swedenWp' ) => 'true',
                                    __('No', 'swedenWp' ) => 'false',
                            )),

                    /************************ ends video ******************/
                    array(
                            "name"  => __("Text Color", 'swedenWp' ),
                            "desc"  => __("Choose header text color", 'swedenWp' ),
                            "id"    => "text_color",
                            "type"  => "select",
                            "std"   => "is-black",
                            "subtype" => array(
                                    __('Black', 'swedenWp' )=>'is-black',
                                    __('White', 'swedenWp' )=>'is-white',
                            )),

                    array(
                            "name"  => __("Content Text",'swedenWp' ),
                            "desc"  => __("Text entered here will display beneath Title",'swedenWp' ),
                            "id"    => "content",
                            "type"  => "tiny_mce",
                            "std"   => __("", "swedenWp" )),
                    array(
                            "name"  => __("Content Card Alignment", 'swedenWp' ),
                            "desc"  => __("Choose position of header text within header container", 'swedenWp' ),
                            "id"    => "align",
                            "type"  => "select",
                            "std"   => "center",
                            "subtype" => array(
                                    __('Center',  'swedenWp' ) =>'center',
                                    __('Right',  'swedenWp' ) =>'right right-center avia-align-right is-right-aligned',
                                    __('Left',  'swedenWp' ) =>'left left-center avia-align-left is-left-aligned',
                                    __('Top Right',  'swedenWp' ) =>'top-right is-top-right-aligned',
                                    __('Top Left',  'swedenWp' ) =>'top-left is-top-left-aligned',
                                    __('Top',  'swedenWp' ) =>'top top-center is-top-aligned',
                                    __('Bottom',  'swedenWp' ) =>'bottom bottom-center is-bottom-inner-aligned',
                                    __('Bottom Right',  'swedenWp' ) =>'bottom-right is-bottom-right-aligned',
                                    __('Bottom Left',  'swedenWp' ) =>'bottom-left is-bottom-left-aligned',
                                    __('No special alignment', 'swedenWp' ) =>'center',
                            )
                    ),

                    array(
                            "name"  => __("Content Card Text Alignment", 'swedenWp' ),
                            "desc"  => __("Choose the alignment of the text within the header", 'swedenWp' ),
                            "id"    => "text_align",
                            "type"  => "select",
                            "std"   => "text-is-center-aligned",
                            "subtype" => array(
                                __('Right',  'swedenWp' ) =>'text-is-right-aligned',
                                __('Left',  'swedenWp' ) =>'text-is-left-aligned',
                                __('Center',  'swedenWp' ) =>'text-is-center-aligned',

                            )
                    ),

                    /************************ ends content ******************/
                    array(
                            "name"  => __("Display CTA", 'swedenWp' ),
                            "desc"  => __("Select \"Yes\" to display a Call to Action button", 'swedenWp' ),
                            "id"    => "button",
                            "type"  => "select",
                            "std"   => "no",
                            "required"=> array('media_type','not','video'),
                            "subtype" => array(
                                __('yes',  'swedenWp' ) =>'yes',
                                __('no',  'swedenWp' ) =>'no',
                                )
                                ),

                    array(  "name"  => __("CTA Label", 'swedenWp' ),
                            "desc"  => __("Choose text for Call to Action button", 'swedenWp' ),
                            "id"    => "label",
                            "type"  => "select",
                            "required" => array('button','equals','yes'),
                            "subtype" => $customCTA,
                            "std" => get_option('CTA_1')),

                	array(	"name" => __("Custom CTA Label", 'swedenWp'),
                			"desc" => __("Enter custom text for CTA label", 'swedenWp'),
                			"id"   => "c_label",
                			"type" => "input",
                			"std" => "",
                			"required" => array('label','equals','custom'),
                	),
                    array(
                            "name"  => __("Button Link", 'swedenWp' ),
                            "desc"  => __("Set the CTA link to a published post, a product, or an outside page", 'swedenWp' ),
                            "id"    => "link",
                            "type"  => "linkpicker",
                            "required" => array('button','equals','yes'),
                            "fetchTMPL" => true,
                            "subtype" => array(
                                                __('Set Manually', 'swedenWp' ) =>'manually',
                            					__('Product', 'swedenWp' ) =>'product',
                                                __('Single Entry', 'swedenWp' ) =>'single',
                                                ),
                            "std"   => "single"),

                    array(
                            "name"  => __("Open Link in New Window", 'swedenWp' ),
                            "desc"  => __("Force CTA link to open in new window/tab", 'swedenWp' ),
                            "id"    => "link_target",
                            "type"  => "select",
                            "std"   => "",
                            "required" => array('button','equals','yes'),
                            "subtype" => array(
                                __('Open in same window',  'swedenWp' ) =>'',
                                __('Open in new window',  'swedenWp' ) =>'_blank')),

                    array(
                            "name"  => __("Button Color", 'swedenWp' ),
                            "desc"  => __("Select CTA text color", 'swedenWp' ),
                            "id"    => "color",
                            "type"  => "select",
                            "std"   => "theme-color",
                            "required" => array('button','equals','yes'),
                            "subtype" => array(
                                                __('Black', 'swedenWp' )=>'is-black',
                                                __('White', 'swedenWp' )=>'is-white',
                                                __('Custom Color', 'swedenWp' )=>'custom',
                                                )),

                    array(
                            "name"  => __("Custom Background Color", 'swedenWp' ),
                            "desc"  => __("Select CTA background color", 'swedenWp' ),
                            "id"    => "custom_bg",
                            "type"  => "colorpicker",
                            "std"   => "#444444",
                            "required" => array('color','equals','custom')
                        ),

                    array(
                            "name"  => __("Custom Font Color", 'swedenWp' ),
                            "desc"  => __("Set a custom font color for the CTA text", 'swedenWp' ),
                            "id"    => "custom_font",
                            "type"  => "colorpicker",
                            "std"   => "#ffffff",
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
                $title    = "";
                $img = "";

                // show title in editor
                if (!empty($params['args']['title_editor'])) {
                    $title = "<p>" . strip_tags($params['args']['title_editor']). "</p>";
                    $title = $params['args']['title_editor'];
                } else if (empty($params['args']['title_editor']) && isset($params['args']['src']) && is_numeric($params['args']['src'])) {
                    $title = wp_get_attachment_image($params['args']['src'],'large');
                } else if (!empty($params['args']['src']) && empty($params['args']['title_editor'])) {
                    $title = "<img src='".$params['args']['src']."' alt=''  />";
                } else {

                }

                if (isset($params['args']['imgsrc']) && is_numeric($params['args']['imgsrc']) && $params['args']['media_type'] == 'image') {
                    $img = wp_get_attachment_image($params['args']['imgsrc'],'large');
                } else if (!empty($params['args']['imgsrc']) && $params['args']['media_type'] == 'image') {
                    $img = "<img src='".$params['args']['imgsrc']."' alt=''  />";
                }
                $params['class'] = "";
                $params['innerHtml']  = "";
                $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style'>";
                $params['innerHtml'] .= "   <div ".$this->class_by_arguments('button' ,$params['args']).">";
                $params['innerHtml'] .= "       <div data-update_with='content' class='avia-promocontent'>";
                $params['innerHtml'] .= "           <div class='avia_image_container' {$template_title}>{$title}</div>";
                $params['innerHtml'] .= "           <div class='avia_image_container' {$template_body}>{$img}</div>";
                $params['innerHtml'] .=             stripslashes(wpautop(trim($params['content'])))."</div>";
                if ($params['args']['button'] == 'yes') {
                    $params['innerHtml'] .= "       <div class='avia_button_box avia_hidden_bg_box'>";
                    $params['innerHtml'] .= "           <div ".$this->class_by_arguments('color' ,$params['args']).">";
                    $params['innerHtml'] .= "               <span data-update_with='label' class='avia_title' >".$params['args']['label']."</span>";
                    $params['innerHtml'] .= "           </div>";
                    $params['innerHtml'] .= "       </div>";
                }
                $params['innerHtml'] .= "   </div>";
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
                global $projectConfig;
                $output = "";
                $class  = "";
                $alt    = "";
                $title  = "";
                $splitVar = '';

                extract(shortcode_atts(array('media_type' => '', 'mobile_fallback' => '', 'src_mobile' => '', 'title_mobile_fallback' => '', 'title_src_mobile' => '', 'video_loop' => '', 'video_type' => '', 'video_code' => '', 'video_cover_src' => '', 'imgsrc'=>'', 'alt_title'=>'', 'title_title'=>'','category_title'=>'', 'src'=>'', 'link'=>'', 'attachment_video'=>'', 'attachment'=>'', 'attachment_title' => '', 'target'=>'no','layout_size'=>'', 'btn_color' => ''), $atts));
                $atts =  shortcode_atts(array(
                                             'title_type' => "",
                                             'title_editor' => '',
                                             'src' => "",
                                             'media_type' => "",
                                             'imgsrc' => "",
                							 'title_mobile_fallback' => "",
                							 'title_src_mobile' => "",
                                             'video_cover_src' => "",
                                             'video_type' => "",
                                             'video_code' => "",
                                             'video_loop' => "",
                                             'align' => "",
                                             'text_align' => "",
                                             'text_color' => 'is-black',
                                             'content' => "",
                                             'button' => 'yes',
                                             'label' => 'Read More A',
                							 'c_label' => '',
                                             'link' => '',
                                             'link_target' => '',
                                             'color' => 'is-black',
                                             'custom_bg' => '#444444',
                                             'custom_font' => '#ffffff',
                                             'category_title' => '',
                                             'btn_color' => 'is-white'
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


                if ($atts['media_type'] == 'image') {

                    $contentWidth = 'has-card';

                } else {
                    $contentWidth = '';
                }

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

                global $wp_query;

                $post_type = $wp_query->query['post_type'];

                // REMOVED AS PER MDKD-2371
                // if (!$swedenUnlimited['client']->isPhone &&
                //     !$swedenUnlimited['client']->isTablet &&
                //      $post_type == 'fashion')
                // {
                //     $parallax__grid_element_header = 'data-requirejs-id="elements/parallax"';
                //     $parallax__image = 'data-stellar-ratio="0.8"';
                // }

                // wrapper
                $output .='<div class="grid-element-header '. $atts['align'] .' '. $contentWidth .' has-image-as-bg" '. $parallax__grid_element_header . '>';

                // if header is not a video
                if ($atts['media_type'] !== 'video') {

                    $imgsize = '1/1-image-with-text+hero';
                    if (!empty($layout_size)) {
                        switch ($layout_size) {
                            case 'one_half':
                                $imgsize = '1/2-image-with-text';
                                break;

                            case 'one_third':
                                $imgsize = '1/3-image-with-text';
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


                    /**
                     * image with text -> removing parent page type
                    if (is_front_page()) { echo 'this is home page'; } else { echo 'its not! home page'; }
                    // echo '<p>page template used: '. get_page_template_slug(get_the_ID()) . '</p>';
                    $template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
                    // echo '<p>tempalte name from post meta : '. $template_name . '</p>';
                    // echo '<p>current tempalte : '.get_option('current_page_template') .'</p>';
                    if(is_front_page()) $parentPage = 'grid-element-home';
                    //else if()
                     **/
                    if($mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone)
                    	$attachment = $src_mobile;
                    if (!empty($attachment) && $atts['media_type'] == 'image') {
                        if($swedenUnlimited['client']->isPhone) {
                                    $imgsize = 'large-thumbnail';
                                }

                        $imgel = "<div class='image-container {$layout_size} {$imgalign}'>";
                        if ($atts['button'] == "yes") { $imgel .= "<a href='{$link}' {$blank}>"; }
                        // $output .= wp_get_attachment_image($attachment, $imgsize, false, array('data-stellar-ratio' => '0.8'));
                        $imgel .= wp_get_attachment_image($attachment, $imgsize, false);
                        if ($atts['button'] == "yes") { $imgel .= "</a>"; }
                        $imgel.= "</div>";
                    }

                    if ( $post_type == 'fashion' && isset($imgel) ){
                       $output.= $imgel;
                    }

                    if ( $post_type == 'sweeps' && $swedenUnlimited["client"]->isPhone && isset($imgel) ){
                       $output.= $imgel;
                       $sweepImage = true;
                    }

                    // category
                    $link  = swedenWpFunctions::get_url($atts['link']);
                    $link  = $link == "http://" ? "" : $link;

                    // content
                    $containerAlign = $atts['align'];
                    if ($atts['media_type'] == 'none') {
                        $containerAlign = 'is-relative-aligned';
                    }
                    $output .='<div class="content-card-container '.$containerAlign.'">';
                    $output .='  <div class="content-card article-header-block is-no-card '.$atts['text_color'].' '. $atts['text_align'] .' title-type-' .$atts['title_type']. '">';

                    if (!empty($link)) {
                        if($category_title) $output.= '<div class="slug">'.$category_title.'</div>';
                        else $output.=  swedenWpFunctions::all_taxonomies_links($selectedPostID);

                    } elseif ($category_title) {
                        $output.= '<div class="slug">'.$category_title.'</div>';
                    }
                    // title
                    if (!empty($src)&&$atts['title_type']!="text") {
                    	if($title_mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone) {
                    		$attachment_title = $title_src_mobile;
                    	}
                        if($swedenUnlimited['client']->isPhone)
                    		// get phone image
                    		$output.= wp_get_attachment_image($attachment_title, $imgsize);
                    	else if($swedenUnlimited['client']->isTablet)
                    		// get tablet image
                    		$output.= wp_get_attachment_image($attachment_title, $imgsize);
                    	else
                    		$output.= wp_get_attachment_image($attachment_title, $imgsize);
                    } else {
                        $output.= "     <div class='headline'>" . stripslashes(wpautop(trim($atts['title_editor']))). "</div>";
                    }

                    if (!empty($content)) {

                        $output.= "             <div class='subhead'>". stripslashes(wpautop(trim($content))) . "</div>";
                    }


                    $productLink = explode(',', $atts['link']);

                    // CTA
                    if ($atts['button'] == "yes" && $atts['media_type'] != 'video' ) {
                        $style = "";
                        if($atts['label'] == 'custom' && !empty($atts['c_label'])) {
                        	$label = $atts['c_label'];
                        } else {
                        	$label = $atts['label'];
                        }
                        if ($atts['color'] == "custom") {
                            $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                        }
                        $output .= "    <div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";

	                    if($productLink[0] == 'product' && !$swedenUnlimited['client']->isPhone) {
		                	$styleNum = str_replace(strtoupper($atts['country']).'_', '', strtoupper($atts['p_id']));
		                	$output .= '<div title="Quickview" id="widget-o-pop" data-requirejs-id="utils/shop" data-source="" data-style="'.
		                			$styleNum.'" data-country="'.strtoupper($productLink[3]).'" data-skuid="'.strtoupper($productLink[2]).'">
									<span class="read-more" style="margin-top:20px; cursor:pointer">'.$label.'<span class="icon-arrow-right"></span></span></div>';

		                } else {

                            if($productLink[0] == 'product' && $swedenUnlimited['client']->isPhone) {
                                // no shop widget on mobile -- just link to PDP
                                $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                                $blank = 'target="_blank"';
                            }

		                	$output .= "<a href='{$link}' class='swBtn' {$blank} {$style} title='{$label}'>";
		                	$output .= "<span class='read-more cta'>".$label."<span class='icon-arrow-right'></span></span>";
		                	$output .= "</a>";
		                }
                        $output .= "</div>";
                    }





                    //close content
                    $output.= "</div>";
                    //close content-card-container
                    $output.= "</div>";

                     // content image
                    if ($post_type != 'fashion' && isset($imgel) && !$sweepImage){
                        $output.= $imgel;
                    }

                // if header is a video
                } elseif ($atts['media_type'] == 'video') {

                    if($swedenUnlimited['client']->isPhone || $swedenUnlimited['client']->isTablet) {
                        $video_loop = 'false';
                    }

                    $video_output = MKD_Video::displayVideo($video_code, $video_type, $video_loop);

                    if (!empty($src)&&$atts['title_type']!="text") {

                        if($swedenUnlimited['client']->isPhone)
                            // get phone image
                            $title = ($title_mobile_fallback == 'yes') ? wp_get_attachment_image($title_src_mobile, '') : wp_get_attachment_image($attachment_title, $imgsize);
                        else if($swedenUnlimited['client']->isTablet)
                            // get tablet image
                            $title = wp_get_attachment_image($attachment_title, $imgsize);
                        else
                            $title = wp_get_attachment_image($attachment_title, $imgsize);
                    } else
                    {
                        $title = $atts['title_editor'];
                    }

                    if ($video_loop == 'true') $isLoopingClass = 'is-looping';
                    else $isLoopingClass = 'is-not-looping';

                    $video_title = stripslashes(wpautop(trim($title)));
                    $video_content = stripslashes(wpautop(trim($content)));
                    $video_category = stripslashes(wpautop(trim($category_title)));

                    if($swedenUnlimited['client']->isPhone || $swedenUnlimited['client']->isTablet) {
                    	// get phone image
                        if($swedenUnlimited['client']->isPhone){
                            $imgsize = "large-thumbnail";
                        }
                    	$vidCover = wp_get_attachment_image($attachment_video, $imgsize);
                        $output.= "<!-- {$vidCover} -->";
                        preg_match('/src="([^"]*)"/i', $vidCover, $vidCoverSrc);

                    } else {
                    	$vidCover = wp_get_attachment_image($attachment_video, $imgsize);
                    }

                    $btn_color = $atts['btn_color'];


                    if($swedenUnlimited['client']->isPhone) {
                        $slug = (!empty($atts['category_title'])) ? "<div class=\"slug\"><div class=\"category-term\">{$atts['category_title']}</div></div>" : "";
                        $output .= <<<VIDEO_COVER
                            <div class="content-card-container">
                                <div class="content-card article-header-block">
                                    $slug
                                    <div class="headline">$video_title</div>
                                    <div class="subhead">$video_content</div>
                                </div>
                            </div>
                            <div $style class="video-header-container avia-video {$atts['text_color']} type-{$video_type}">
                                <div class="video-cover-image $isLoopingClass" style="background-image: url($vidCoverSrc[1])"></div>
                                <div class="video-overlay">
                                        <div class="video-play-button"><span class="icon {$btn_color}"></span></div>
                                </div>
                            $video_output
                            </div>

VIDEO_COVER;
                    } else{
                        $slug = (!empty($atts['category_title'])) ? "<div class=\"category\"><div class=\"category-term\">{$atts['category_title']}</div></div>" : "";
                        $output .= <<<VIDEO_COVER
                            <div $style class="video-header-container avia-video {$atts['text_color']} type-{$video_type} {$isLoopingClass}">
                                <div class="video-cover-image $isLoopingClass">
                                    $vidCover
                                </div>
                                <div class="video-overlay">
                                <div class="video-title">
                                    <div class="title-container {$atts['text_align']}">
                                        $slug
                                        $title
                                        <div class="video-deck">$video_content</div>
                                        <div class="video-play-button"><span class="icon {$btn_color}"></span></div>
                                    </div>
                                </div>
                            </div>
                            $video_output
                        </div>
VIDEO_COVER;
                    }

                // end if header isn't a video
                }
                $output.="</div>";
                //close wrapper

                return do_shortcode($output);

            }

    }
}
