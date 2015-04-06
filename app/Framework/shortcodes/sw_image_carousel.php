<?php
/**
 * Slider
 * Shortcode for image carousel
 */

if ( !class_exists( 'sw_sc_image_carousel' ) )
{
  class sw_sc_image_carousel extends swedenShortcodeTemplate
  {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Image ContentCarousel', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
                $this->config['order']			= 11;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_image_carousel';
                $this->config['shortcode_nested'] = array('sw_image_content_slider');
                $this->config['tooltip'] 	    = __('Display a image carousel element', 'swedenWp' );
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

                                    array(
                                        "name"  => __("Carousel Type", 'swedenWp' ),
                                        "desc"  => __("Select carousel type", 'swedenWp' ),
                                        "id"    => "carousel_type",
                                        "type"  => "select",
                                        "std"   => "home",
                                        "subtype" => array(
                                                            __('Home', 'swedenWp' )=>'home',
                                                            __('General', 'swedenWp' )=>'general',
                                                    )
                                        ),

                                    array(
                                            "name"  => __("Carousel Dots", 'swedenWp' ),
                                            "desc"  => __("Select \"Yes\" to display navigation dots beneath the carousel", 'swedenWp' ),
                                            "id"    => "carousel_dots",
                                            "type"  => "select",
                                            "std"   => "false",
                                            "required" => array('carousel_type','equals','general'),
                                            "subtype" => array(
                                                    __('Yes',  'swedenWp' ) =>'true',
                                                    __('No',  'swedenWp' ) =>'false',
                                            )
                                    ),

                                    array(
                                            "name"  => __("Carousel Auto Play", 'swedenWp' ),
                                            "desc"  => __("Select \"Yes\" to automatically advance carousel slide", 'swedenWp' ),
                                            "id"    => "carousel_autoplay",
                                            "type"  => "select",
                                            "std"   => "false",
                                            //"required" => array('carousel_type','equals','general'),
                                            "subtype" => array(
                                                    __('Yes',  'swedenWp' ) =>'true',
                                                    __('No',  'swedenWp' ) =>'false',
                                            )
                                    ),

                                    array(  "name"  => __("Carousel Auto Play Interval", 'swedenWp' ),
                                            "desc"  => __("Specify the delay between slides in <strong>milliseconds</strong>", 'swedenWp' ),
                                            "id"    => "carousel_autoplay_delay",
                                            "type"  => "input",
                                            "required" => array('carousel_autoplay','equals','true'),
                                            "std" => "5000"),

                                    array(
                                            "name"  => __("Carousel Parallax", 'swedenWp' ),
                                            "desc"  => __("Select \"Yes\" to enable the parallax effect", 'swedenWp' ),
                                            "id"    => "carousel_parallax",
                                            "type"  => "select",
                                            "std"   => "true",
                                            "required" => array('carousel_type','equals','home'),
                                            "subtype" => array(
                                                    __('Yes',  'swedenWp' ) =>'true',
                                                    __('No',  'swedenWp' ) =>'false',
                                            )
                                    ),

                                    array(
                                        "name" => __("Slides", 'swedenWp' ),
                                        "desc" => __("Add, edit, and remove carousel slides", 'swedenWp' ),
                                        "type" 			=> "modal_group",
                                        "id" 			=> "content",
                                        "modal_title" 	=> __("Edit Form Element", 'swedenWp' ),
                                        "std"			=> array(
                                            array('title'=>__('Slide 1', 'swedenWp' ), 'tags'=>''),
                                            array('title'=>__('Slide 2', 'swedenWp' ), 'tags'=>''),
                                    ),

                                    'subelements' 	=> array(
                                                            array(  "name"  => __("Title Type", 'swedenWp' ),
                                                                    "desc"  => __("Select \"Image title\" to replace title with an image", 'swedenWp' ),
                                                                    "id"    => "title_type",
                                                                    "type"  => "select",
                                                                    "std"   => "text",
                                                                    "subtype" => array(
                                                                            __('Text title', 'swedenWp') =>'text',
                                                                            __('Image title', 'swedenWp') =>'image',
                                                                            __('No title', 'swedenWp') => 'none'
                                                                        )
                                                                    ),
                                                            array(  "name"  => __("Title", 'swedenWp' ),
                                                                    "desc"  => __("Text for title of content card", 'swedenWp' ),
                                                                    "id"    => "title",
                                                                    "type"  => "title_tiny_mce",
                                                                    "required" => array('title_type','equals','text'),
                                                                    "std" => ""),

                                                            array(  "name"  => __("Choose Title Image", 'swedenWp' ),
                                                                    "desc"  => __("Choose an image to use as title", 'swedenWp' ),
                                                                    "id"    => "src",
                                                                    "type"  => "image_title",
                                                            		"fetch"	=> "id",
                                                                    "required" => array('title_type','equals','image'),
                                                                    "title" => __("Insert Image",'swedenWp'),
                                                                    "button" => __("Insert",'swedenWp'),
                                                                    "std"   => ""),
                                                            /************************ ends title ******************/
                                                            array(  "name"  => __("Category", 'swedenWp' ),
                                                                    "desc"  => __("Manually set the category slug of the slide. Leave empty to use the category of post selected in CTA", 'swedenWp' ),
                                                                    "id"    => "category_title",
                                                                    "type"  => "input",
                                                                    "required" => array('title_type','not','none'),
                                                                    "std" => $catTitle),
                                                            array(
                                                                    "name"  => __("Content Card Lockup", 'swedenWp' ),
                                                                    "desc"  => __("Choose style lockup for content card", 'swedenWp' ),
                                                                    "id"    => "parent_type",
                                                                    "type"  => "select",
                                                                    "std"   => "",
                                                                    "required" => array('title_type','not','none'),
                                                                    "subtype" => array(
                                                                            __('Home Page',  'swedenWp' ) =>'grid-element-home',
                                                                            __('Category Page',  'swedenWp' ) =>'grid-element-category',
                                                                            __('Subcategory Page',  'swedenWp' ) =>'grid-element-subcategory',
                                                                            __('Shop Now Carousel',  'swedenWp' ) =>'grid-element-shop-now',
                                                                            __('none',  'swedenWp' ) =>'',
                                                                    )
                                                            ),
                                                            /***************** parent type ************************/

                                                            array(  "name"  => __("Display Image", 'swedenWp' ),
                                                                    "desc"  => __("Select \"No\" if slide has no image", 'swedenWp' ),
                                                                    "id"    => "image",
                                                                    "type"  => "select",
                                                                    "std"   => "yes",
                                                                    "subtype" => array(
                                                                            __('Yes', 'swedenWp') =>'yes',
                                                                            __('No', 'swedenWp') =>'no',
                                                                    )
                                                            ),
                                                            array(
                                                                    "name"  => __("Choose Image",'swedenWp' ),
                                                                    "desc"  => __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                                                                    "id"    => "imgsrc",
                                                            		"fetch"	=> "id",
                                                                    "type"  => "image",
                                                                    "title" => __("Insert Image",'swedenWp' ),
                                                                    "button" => __("Insert",'swedenWp' ),
                                                                    "required" => array('image','equals','yes'),
                                                                    "std"   => ""),

                                    						// mobile fallback image
				                                    		array(	"name"  => __("Mobile Image", 'swedenWp' ),
				                                    				"desc"  => __('Select "yes" to upload a separate image for mobile devices', 'swedenWp' ),
				                                    				"id"    => "mobile_fallback",
				                                    				"type"  => "select",
				                                    				"std"   => "no",
				                                    				"required" => array('image','equals','yes'),
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
				                                    				"title" => __("Insert Image",'swedenWp' ),
				                                    				"button" => __("Insert",'swedenWp' ),
				                                    				"required" => array('mobile_fallback','equals','yes'),
				                                    				"std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"),

                                                            // array(
                                                            //         "name"  => __("Image Alignment", 'swedenWp' ),
                                                            //         "desc"  => __("Choose the vertical alignment of the slide image", 'swedenWp' ),
                                                            //         "id"    => "img_align",
                                                            //         "type"  => "select",
                                                            //         "std"   => "no",
                                                            //         "required" => array('image','equals','yes'),
                                                            //         "subtype" => array(
                                                            //                 __('Push down top',  'swedenWp' ) =>'down',
                                                            //                 __('Push up bottom',  'swedenWp' ) =>'up',
                                                            //                 __('No special alignment', 'swedenWp' ) =>'no',
                                                            //         )
                                                            // ),

                                                            array(
                                                                    "name"  => __("Image Layout", 'swedenWp' ),
                                                                    "desc"  => __("Choose whether image is rendered as a background or inline", 'swedenWp' ),
                                                                    "id"    => "img_layout",
                                                                    "type"  => "select",
                                                                    "std"   => "not background",
                                                                    "required" => array('image','equals','yes'),
                                                                    "subtype" => array(
                                                                            __('not background',  'swedenWp' ) =>'',
                                                                            __('background',  'swedenWp' ) =>'has-image-as-bg',
                                                                    )
                                                            ),
                                                            /************************ ends image ******************/
                                                            array(
                                                                    "name"  => __("Content Card Style", 'swedenWp' ),
                                                                    "desc"  => __("Select the visual style of the content card", 'swedenWp' ),
                                                                    "id"    => "card_type",
                                                                    "type"  => "select",
                                                                    "required" => array('title_type','not','none'),
                                                                    "std"   => "",
                                                                    "subtype" => array(
                                                                            __('White Block',  'swedenWp' ) =>'white-card',
                                                                            // __('Thin-stroke Outline',  'swedenWp' ) =>'white-card is-outline',
                                                                            __('Text Overlay',  'swedenWp' ) =>'white-card is-no-card'
                                                                    )
                                                            ),
                                                            array(
                                                                    "name"  => __("Content Text Color", 'swedenWp' ),
                                                                    "desc"  => __("Choose color for card text. May be overidden by context specific styles", 'swedenWp' ),
                                                                    "id"    => "text_color",
                                                                    "type"  => "select",
                                                                    "std"   => "is-black",
                                                                    "subtype" => array(
                                                                            __('Black', 'swedenWp' )=>'is-black',
                                                                            __('White', 'swedenWp' )=>'is-white',
                                                                    )),

                                                            array(
                                                                    "name"  => __("Card Contents",'swedenWp' ),
                                                                    "desc"  => __("This text will run on top of the image, or within the white content card, depending on what Content Card Style has been selected",'swedenWp' ),
                                                                    "id"    => "content",
                                                                    "type"  => "tiny_mce",
                                                                    "std"   => __("", "swedenWp" )),
                                                            array(
                                                                    "name"  => __("Content Card Alignment", 'swedenWp' ),
                                                                    "desc"  => __("Choose the position of the content card on top of the image", 'swedenWp' ),
                                                                    "id"    => "align",
                                                                    "type"  => "select",
                                                                    "std"   => "center",
                                                                    "required" => array('title_type','not','none'),
                                                                    "subtype" => array(
                                                                        __('Center',  'swedenWp' ) =>'center',
                                                                        __('Right',  'swedenWp' ) =>'right avia-align-right is-right-aligned',
                                                                        __('Left',  'swedenWp' ) =>'left avia-align-left is-left-aligned',
                                                                        __('Top Right',  'swedenWp' ) =>'top-right is-top-right-aligned',
                                                                        __('Top Left',  'swedenWp' ) =>'top-left is-top-left-aligned',
                                                                        __('Top',  'swedenWp' ) =>'top is-top-aligned',
                                                                        __('Bottom',  'swedenWp' ) =>'bottom is-bottom-inner-aligned',
                                                                        __('Bottom Right',  'swedenWp' ) =>'bottom-right is-bottom-right-aligned',
                                                                        __('Bottom Left',  'swedenWp' ) =>'bottom-left is-bottom-left-aligned',
                                                                        __('No special alignment', 'swedenWp' ) =>'',

                                                                    )
                                                            ),
                                                            array(
                                                                    "name"  => __("Content Card Text Alignment", 'swedenWp' ),
                                                                    "desc"  => __("Choose the alignment of the text inside the content card", 'swedenWp' ),
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
                                                            array(  "name"  => __("Display Image Caption", 'swedenWp' ),
                                                                    "desc"  => __("Select \"Yes\" to display a block of text below the slide", 'swedenWp' ),
                                                                    "id"    => "imagecaption_displayed",
                                                                    "type"  => "select",
                                                                    "std"   => "no",
                                                                    "subtype" => array(
                                                                            __('Yes', 'swedenWp') =>'yes',
                                                                            __('No', 'swedenWp') =>'no',
                                                                    )
                                                            ),
                                                            array(
                                                                    "name"  => __("Image Caption Content",'swedenWp' ),
                                                                    "desc" => __("This content will run beneath the slide image if \"Yes\" is selected in the Display Image Caption menu", 'swedenWp'),
                                                                    "id"    => "imagecaption",
                                                                    "type"  => "title_tiny_mce",
                                                                    "class" => "fw-editor",
                                                                    "std"   => __("", "swedenWp" ),
                                                                    "required" => array('imagecaption_displayed','equals','yes')
                                                            ),
                                                            array(
                                                                    "name"  => __("Display CTA", 'swedenWp' ),
                                                                    "desc"  => __("Select \"Yes\" to display a Call to Action button", 'swedenWp' ),
                                                                    "id"    => "button",
                                                                    "type"  => "select",
                                                                    "std"   => "no",
                                                                    "subtype" => array(
                                                                        __('Yes',  'swedenWp' ) =>'yes',
                                                                        __('No',  'swedenWp' ) =>'no',
                                                                    )
															),

                                                            array(  "name"  => __("CTA Label", 'swedenWp' ),
                                                                    "desc"  => __("Choose text for Call to Action button", 'swedenWp' ),
                                                                    "id"    => "label",
                                                                    "type"  => "select",
                                                                    "required" => array('button','equals','yes'),
                                                                    "subtype" => swedenWpFunctions::get_saved_cta_value(1),
                                                                    "std" => get_option('CTA_1')),
                                                            array(
                                                                    "name"  => __("Button Link", 'swedenWp' ),
                                                                    "desc"  => __("Set the CTA link to a published post, a product, or an outside page", 'swedenWp' ),
                                                                    "id"    => "link",
                                                                    "type"  => "linkpicker",
                                                                    "required" => array('button','equals','yes'),
                                                                    "fetchTMPL" => true,
                                                                    "subtype" => array(
                                                                                        __('Set Manually', 'swedenWp' ) =>'manually',
                                                                                        __('Product', 'swedenWp') => 'product_carousel',
                                                                                        __('Single Entry', 'swedenWp' ) =>'single'
                                                                                        ),
                                                                    "std"   => ""),
                                                            //------------------------------------------------------------------------
                                                            // product section
                                                            //------------------------------------------------------------------------

                                                            array(
                                                                    "name" => __("Product ID", 'swedenWp') ,
                                                                    "desc" => __("Product ID with country prefix (eg. US_MS48U70VY0)", 'swedenWp') ,
                                                                    "id" => "p_id",
                                                                    "type" => "input",
                                                                    "required" => array('link','equals','product_carousel'),
                                                                    "std" => ""
                                                            ),
                                                            array(
                                                                    "name" => __("SKU", 'swedenWp') ,
                                                                    "desc" => __("Product SKU code (eg. 823530423)", 'swedenWp') ,
                                                                    "id" => "sku",
                                                                    "type" => "input",
                                                                    "required" => array('link','equals','product_carousel'),
                                                                    "std" => ""
                                                            ),
                                                            array(
                                                                    "name" => __("Country Code", 'swedenWp') ,
                                                                    "desc" => __("Two letter country code (eg. US)", 'swedenWp') ,
                                                                    "id" => "country",
                                                                    "type" => "input",
                                                                    "required" => array('link','equals','product_carousel'),
                                                                    "std" => ""
                                                            ),
                                                            array(
                                                                    "name" => __("Language Code", 'swedenWp') ,
                                                                    "desc" => __("(eg. us_en)", 'swedenWp') ,
                                                                    "id" => "lang",
                                                                    "type" => "input",
                                                                    "required" => array('link','equals','product_carousel'),
                                                                    "std" => ""
                                                            ),

                                                            array(
                                                                    "name"  => __("Open Link in New Window", 'swedenWp' ),
                                                                    "desc"  => __("Select yes to force link to open in a new window or tab", 'swedenWp' ),
                                                                    "id"    => "link_target",
                                                                    "type"  => "select",
                                                                    "std"   => "",
                                                                    "required" => array('button','equals','yes'),
                                                                    "subtype" => array(
                                                                        __('Open in same window',  'swedenWp' ) =>'',
                                                                        __('Open in new window',  'swedenWp' ) =>'_blank')),

                                                            array(
                                                                    "name"  => __("Button Color", 'swedenWp' ),
                                                                    "desc"  => __("Select CTA color", 'swedenWp' ),
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
                                        )
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
                $heading  = "";
                $template = $this->update_template("heading", " - <strong>{{heading}}</strong>");
                if(!empty($params['args']['heading'])) $heading = "- <strong>".$params['args']['heading']."</strong>";

                $params['innerHtml'] = "<img src='".$this->config['icon']."' title='".$this->config['name']."' />";
                $params['innerHtml'].= "<div class='avia-element-label'>".$this->config['name']."</div>";
                $params['innerHtml'].= "<div class='avia-element-label' {$template}>".$heading."</div>";

                return $params;
            }

              /**
               * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
               * Works in the same way as Editor Element
               * @param array $params this array holds the default values for $content and $args.
               * @return $params the return array usually holds an innerHtml key that holds item specific markup.
               */
              function editor_sub_element($params)
              {
                  $template = $this->update_template("title", "{{title}}");
                  $img_template = $this->update_template("img_fakeArg", "{{img_fakeArg}}");
				  //$imgThumb = $this->update_template("imgsrc", "<img width='50px' src='{{imgsrc}}'>");
                  if(is_numeric($params['args']['imgsrc'])) {
					$imgThumb = isset($params['args']['imgsrc']) ? wp_get_attachment_image($params['args']['imgsrc']) : "";
                  } else {
                  	$imgThumb = "<img width='50px' src='". $params['args']['imgsrc'] ."'>";
                  }


                  $params['innerHtml']  = "";
                  $params['innerHtml'] .= "<div {$img_template} style='float:left;'>{$imgThumb}</div>";
                  //$params['innerHtml'] .= "<div {$imgThumb}><img style='float:left; margin-right:5px;' width='90px' src='".$params['args']['imgsrc']."'></div>
                  $params['innerHtml'] .= "<div class='avia_title_container' {$template}>".strip_tags(html_entity_decode($params['args']['title']))."</div>";
                  $params['innerHtml'] .= "<div style='clear:both; padding:0;'></div>";

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
                $atts = shortcode_atts(array(
                'type'          => 'slider',
                'autoplay'		=> 'false',
                'animation'     => 'fade',
                'interval'		=> 5,
                'navigation'    => 'arrows',
                'heading'		=> '',
                'columns'       => 3,
                'handle'		=> $shortcodename,
                'carousel_type' => $this->config['carousel_type'],
                'carousel_dots' => $atts['carousel_dots'],
                'carousel_autoplay' => $this->config['carousel_autoplay'],
                'carousel_autoplay_delay' => $this->config['carousel_autoplay_delay'],
                'carousel_parallax' => $this->config['carousel_parallax'],
                'content'		=> ShortcodeHelper::shortcode2array($content),
                'class'			=> $meta['el_class']
                ), $atts);

                $slider  = new sw_image_content_slide($atts);


                return $slider->html();
            }

    }
}

if ( !class_exists( 'sw_image_content_slide' ) )
{
    class sw_image_content_slide
    {
        static  $slider = 0; 				//slider count for the current page
        protected $config;	 				//base config set on initialization

        function __construct($config)
        {
            global $avia_config;
            $output = "";
            $this->config = array_merge(array(
                'type'          => 'grid',
                'autoplay'		=> 'false',
                'animation'     => 'fade',
                'handle'		=> '',
                'heading'		=> '',
                'navigation'    => 'arrows',
                'columns'       => 3,
                'interval'		=> 5,
                'class'			=> "",
                'css_id'		=> "",
                'carousel_type' => $config['carousel_type'],
                'carousel_dots' => $config['carousel_dots'],
                'carousel_autoplay' => $config['carousel_autoplay'],
                'carousel_parallax' => $config['carousel_parallax'],
                'carousel_autoplay_delay' => $config['carousel_autoplay_delay'],
                'content'		=> array()
                ), $config);
        }

        public function html()
        {
            global $swedenUnlimited;
            $output = "";
            $counter = 0;
            //swedendSliderHelper::$slider++;
            if(empty($this->config['content'])) return $output;

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();
            if(!empty($this->atts)){
              extract($this->atts);
            }
            extract($this->config);

            $extraClass 		= 'first';
            $grid 				= 'one_third';
            $slide_loop_count 	= 1;
            $loop_counter		= 1;
            $total				= $columns % 2 ? "odd" : "even";
            $heading 			= !empty($this->config['heading']) ? '<h3>'.$this->config['heading'].'</h3>' : "&nbsp;";
            $slide_count        = count($content);
            $carousel_dots      = $this->config['carousel_dots'];
            $carousel_type      = $this->config['carousel_type'];
            $carousel_autoplay  = $this->config['carousel_autoplay'];
            $carousel_parallax  = $this->config['carousel_parallax'];
            $carousel_autoplay_delay = $this->config['carousel_autoplay_delay'];


            switch($columns)
            {
                case "1": $grid = 'av_fullwidth'; break;
                case "2": $grid = 'av_one_half'; break;
                case "3": $grid = 'av_one_third'; break;
                case "4": $grid = 'av_one_fourth'; break;
                case "5": $grid = 'av_one_fifth'; break;
                case "6": $grid = 'av_one_sixth'; break;
                case "7": $grid = 'av_full_bleed'; break;
                case "7": $grid = 'av_full_margin'; break;
            }

            $thumb_fallback = "";

                switch ($carousel_type):
                    case "home":
                            $output .='<div class="gleam-wrap black-arrows" data-requirejs-id="elements/homepage_carousel">';
                            $output .= '<div class="homepage-carousel show-text slick carousel-arrow-check-parent" id="gleam" ';
                            $output .= 'data-carousel-dots="'. $carousel_dots .'" data-carousel-autoplay="'. $carousel_autoplay .'" data-carousel-autoplay-delay="'. $carousel_autoplay_delay .'" data-carousel="single"';
                            if(!$swedenUnlimited['client']->isPhone && !$swedenUnlimited['client']->isTablet) {
                                // $output .= 'data-requirejs-id="elements/parallax"';
                            }
                            $output .= ' >';

                            $output .= '        <div class="gleam-slides">';

                                foreach($content as $key => $value)
                                {
                                    $output .= $this->slide_content($value['attr'],$value['content'],"home");
                                }

                            break;

                    default:
                            $output .='<div class="grid-element">';
                            $output .= '<div class="main-carousel">';
                            $output .= '    <div class="main-carousel-inner">';
                            $output .= '        <div class="main-carousel-slides slick carousel-arrow-check-parent" data-carousel-dots="'. $carousel_dots .'" data-carousel-autoplay="'. $carousel_autoplay .'" data-carousel-autoplay-delay="'. $carousel_autoplay_delay .'" data-carousel="single">';
                                foreach($content as $key => $value)
                                {
                                    $output .= '<div class="flex_column av_one_full">';
                                    $output .= $this->slide_content($value['attr'],$value['content'],"general");
                                    $output .= '</div>';
                                }
                            $output .= '        </div>';
                             break;
                endswitch;

                $output .= '        </div>';
                $output .= '    <div class="right-control gleambuttons"><span class="carousel-next arrow-check"><span class="carousel-next-gleam"></span></span></div>';
                $output .= '    <div class="left-control gleambuttons"><span class="carousel-prev arrow-check"><span class="carousel-prev-gleam"></span></span></div>';
                $output .= '    </div>';
                $output .= '</div>';
                // $output .='</div>';
            return $output;
        }

        public function class_by_arguments($classNames, $args, $classNamesOnly = false)
        {
            $classNames = str_replace(" ","",$classNames);
            $dataString = "data-update_class_with='$classNames' ";
            $classNames = explode(',',$classNames);
            $classString = "class='";
            $classes = "";

            foreach($classNames as $class)
            {
                $classes .= "$class-".str_replace(" ","_",$args[$class])." ";
            }

            if($classNamesOnly) return $classes;

            return $classString .$classes."' ".$dataString;
        }

        /**
         * Slide Content Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */

        protected function slide_content($atts, $content = "", $shortcodename = "", $meta = "", $carousel_type = 'home') {
                global $swedenUnlimited;
                global $projectConfig;

                $output = "";
                $class  = "";
                $alt    = "";
                $title  = "";

                extract(shortcode_atts(array('imgsrc'=>'', 'imgsrc' => "", 'mobile_fallback' => '', 'src_mobile'=>'', 'alt_title'=>'', 'title_title'=>'', 'category_title'=>'', 'src'=>'', 'link'=>'', 'attachment'=>'', 'attachment_title' => '', 'target'=>'no'), $atts));
                $atts =  shortcode_atts(array(
                                             'p_id' => "",
                                             'sku' => "",
                                             'country' => "",
                                             'lang' => "",
                                             'title_type' => "",
                                             'title' => "",
                                             'src' => "",
                                             'parent_type' => "home",
                                             'align' => "",
                                             'text_align' => '',
                                             'card_type' => "",
                                             // 'img_align' => "",
                                             'img_layout' => "",
                                             'text_color' => 'is-black',
                                             'content' => "",
                                             'button' => 'yes',
                                             'label' => '',
                                             'link' => '',
                                             'link_target' => '',
                                             'color' => 'is-black',
                                             'custom_bg' => '#444444',
                                             'custom_font' => '#ffffff',
                                             'position' => '',
                                             'carousel_dots' => '',
                                             'carousel_autoplay' => '',
                                             'carousel_autoplay_delay' => '',
                                             // 'carousel_parallax' => '',
                                             'imagecaption' => '',
                                             'imagecaption_displayed' => '',
                                             ), $atts);

                // carat tacked onto all of the 'shop now' type ctas
                $ctaCarat = "<span class='icon-arrow-right'></span>";

                // TODO: verify this element will need used post value
                // $swedenUnlimited['usedPost'] = array($atts['link']);
                $splitVar = explode(',', $atts['link']);

                // handle manual cta entries
                // TODO : should probably rename splitVar[0] and similar into semantic variable names
                if ($splitVar[0] == 'manually') {
                    // ignore the manual entry
                }
                // handle product cta entries
                elseif ($splitVar[0] == 'product_carousel') {

                    if($swedenUnlimited['client']->isPhone) {
                        // no shop widget on mobile -- just link to PDP
                        $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                        $productLabel = '<a href="'.$link.'" target="_blank">'.$atts['label'].$ctaCarat.'</a>';

                    } else {
                        $productLink = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                        $styleNum = str_replace($atts['country'] . '_', '', $atts['p_id']);
                        $productLabel = '<div title="Quickview" id="widget-o-pop" data-requirejs-id="utils/shop" data-source="" data-style="' . $styleNum . '" data-country="' . $atts['country'] . '" data-skuid="' . $atts['sku'] . '">
                                                        <span class="read-more"><a href="'.$productLink.'" target="_blank" title="'.$atts['label'].'">' . $atts['label'] . $ctaCarat . '</a></span></div>';
                    }
                }
                // TODO : can probably remove this selected post functionality
                else {
                    $selectedPostID = isset($splitVar[1]) ? $splitVar[1]:'';
                }
                if (!empty($attachment)) {
                    $attachment_entry = get_post( $attachment );
                    $alt = get_post_meta($attachment_entry->ID, '_wp_attachment_image_alt', true);
                    $alt = !empty($alt) ? esc_attr($alt) : '';
                    $title = trim($attachment_entry->post_title) ? esc_attr($attachment_entry->post_title) : '';
                }
                if ($atts['title_type'] == 'image' && !empty($attachment_title)) {
                    $attachment_entry_title = get_post( $attachment_title );
                    $alt_title = get_post_meta($attachment_entry_title->ID, '_wp_attachment_image_alt', true);
                    $alt_title = !empty($alt_title) ? esc_attr($alt_title) : '';
                    $title_title = trim($attachment_entry_title->post_title) ? esc_attr($attachment_entry_title->post_title) : '';

                }

                if(!empty($atts['title'])) {
                    $atts_title = trim(strip_tags($atts['title']));
                    $alt = empty($alt) ? $atts_title : $alt;
                    $title = empty($title) ? $atts_title : $title;
                    $alt_title = empty($alt_title) ? $atts_title : $alt_title;
                    $title_title = empty($title_title) ? $atts_title : $title_title;
                }


                // detect safari and prevent parallax from loading
                // if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
                //     $safari = true;
                // }

                if (is_user_logged_in()) $carousel_parallax_offset_amount = 55;
                else $carousel_parallax_offset_amount = 0;

                // if ($safari && $this->config['carousel_parallax'] == 'true') $extra_attrs = array('style' => 'top: -110px;');
                if ($this->config['carousel_parallax'] == 'true') {
                  $extra_attrs = array('data-stellar-ratio' => '0.8', 'data-stellar-vertical-offset' => $carousel_parallax_offset_amount);
                  } else {
                    $extra_attrs = array('class' => 'no-parallax');
                  }



                // content image
                if (!empty($imgsrc)) {
                	if ($this->config['carousel_type'] == "home") {
                		$output .='<div class="gleam-slide parallax-parent '. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
                	}
                	else {
                		$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card parallax-parent">';
                	}


                	if($mobile_fallback == 'yes' && $swedenUnlimited['client']->isPhone) {
                		$imgsrc = $src_mobile;
                	}


                	if (is_numeric($imgsrc)) {
                		if($swedenUnlimited['client']->isPhone) {
                			// get phone image
                			$output .= wp_get_attachment_image($imgsrc,'large-thumbnail');

                		} else if($swedenUnlimited['client']->isTablet) {
                			//$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
                			$output .= wp_get_attachment_image($imgsrc,'large');

                		} else {
                			//$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
                			$output .= wp_get_attachment_image($imgsrc,'large', false, $extra_attrs);
                		}
                	} else if ($attachment) {
                		if($swedenUnlimited['client']->isPhone) {
                			// get phone image
                			$output .= wp_get_attachment_image($attachment,'large-thumbnail');

                		} else if($swedenUnlimited['client']->isTablet) {
                			//$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
                			$output .= wp_get_attachment_image($attachment,'large');

                		} else {
                			//$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
                			$output .= wp_get_attachment_image($attachment,'large', false, $extra_attrs);
                		}
                	} else {
                		// AK: why does this have a background-image and an <img> of the same file?
						// JL: this is default image when user didn't add any image. or in case of direct sql import.
                		$output.= " <img class='avia_image ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$imgsrc}' alt='{$alt}' title='{$title}' />";
                	}
                } else {
                	$output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';

                }

                $link  = swedenWpFunctions::get_url($atts['link']);
                $link  = $link == "http://" ? "" : $link;

                // content
                if ($atts['title_type'] !== 'none')
                {
                    if ($atts['button'] == "yes" && $splitVar[0] != 'product_carousel') {

                        if ($safari && $this->config['carousel_parallax'] == 'true') $extra_text_attrs = 'data-stellar-ratio="1.5" data-stellar-vertical-offset="250"';
                        elseif ($this->config['carousel_parallax'] == 'true' && is_user_logged_in()) $extra_text_attrs = 'data-stellar-ratio="1.5" data-stellar-vertical-offset="269"';
                        elseif ($this->config['carousel_parallax'] == 'true' && !is_user_logged_in()) $extra_text_attrs = 'data-stellar-ratio="1.5" data-stellar-vertical-offset="230"';

                        $output .='<div class="content-card-container is-clickable '.$atts['align'].'" onclick="javascript:window.location.href=\''.$link.'\'; return false;"'. $extra_text_attrs . '">';

                    } else {

                        if ($this->config['carousel_parallax'] == 'true') $extra_text_attrs = 'data-stellar-ratio="1.5" data-stellar-vertical-offset="230"';

                        $output .='<div class="content-card-container '.$atts['align'].'" '. $extra_text_attrs .'>';
                    }

                    $output .='<div class="content-card image-carousel '.$atts['align'].' '.$atts['card_type'].' '.$atts['text_color'].' '. $atts['text_align'] .'">';

                    // category
                    if($this->config['carousel_type'] === "home"){
                        if(empty($category_title) ){
                            $output.=   swedenWpFunctions::all_taxonomies_links($selectedPostID);
                        } else {
                            $output.= '<div class="image-carousel-slug">'.$category_title.'</div>';
                        }
                    } else if (!empty($link) && $splitVar[0] != 'manually' && $splitVar[0] != 'product_carousel') {
                        $output.= 	swedenWpFunctions::all_taxonomies_links($selectedPostID);

                    } else if(!empty($category_title)) {
                        $output.= '<div class="image-carousel-slug">'.$category_title.'</div>';
                    }
                    // title
                    if (!empty($src)) {
                        if ($attachment_title) {
                        	if($swedenUnlimited['client']->isPhone)
                        		$output .= wp_get_attachment_image($attachment_title,'large');
							else if($swedenUnlimited['client']->isTablet)
                        		$output .= wp_get_attachment_image($attachment_title,'large');
                        	else
                        		$output .= wp_get_attachment_image($attachment_title,'large');
                        } else if (is_numeric($src)) {
                        	if($swedenUnlimited['client']->isPhone)
                        		$output .= wp_get_attachment_image($src,'large');
                        	else if($swedenUnlimited['client']->isTablet)
                        		$output .= wp_get_attachment_image($src,'large');
                        	else
                        		$output .= wp_get_attachment_image($src,'large');
                        } else {
                            $output .= "<a href='{$link}'><img class='avia_image {$class} image-carousel-img-headline' src='{$src}' alt='{$alt_title}' title='{$title_title}' /></a>";

                        }
                    } else {
                        if(!empty($atts['title'])) {
                            $output.= "     <h2 class='content-title image-carousel-headline'><a href='{$link}'>" . $atts['title']. "</a></h2>";
                        }
                    }

                    if (!empty($content)) {
                        $output.= "             <div class='image-carousel-subhead'>". stripslashes(wpautop(trim($content))) . "</div>";
                    }
                }

                // if caption is seleceted then remove the cta from content card
                if ($atts['imagecaption_displayed'] != 'yes')
                {
                    // CTA
                    if ($atts['button'] == "yes") {
                        $style = "";
                        if ($atts['color'] == "custom") {
                            $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                        }

                        $blank = $atts['link_target'] ? 'target="_blank" ' : "";

                        // $link  = swedenWpFunctions::get_url($atts['link']);
                        // $link  = $link == "http://" ? "" : $link;
                        $output .= "    <div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";
                        $output .= "        <span class='swBtn ".$this->class_by_arguments('color, position' , $atts, true)."' {$blank} {$style} >";

                        if ($atts['parent_type'] == "grid-element-shop-now") {
                            $output .= "            <div class='grid-element-shop-now-link image-carousel-cta' >".$atts['label'].$ctaCarat."</div>";
                        }
                        else {

                            // setup read more label
                            if (!$atts['label']) {
                                $atts['label'] = "read more";
                            }

                            // display quick shop modal if we have a product
                            if ($splitVar[0] == 'product_carousel') {
                                $output .= "            <div class='grid-element-shop-now-link image-carousel-cta' >".$productLabel."</div>";
                            }
                            // else display other variables selected from cta dropdown
                            else {
                                $output .= "            <a href='{$link}'><div class='read-more image-carousel-cta'>".$atts['label'].$ctaCarat."</div></a>";
                            }
                        }

                        $output .= "        </span>";
                        $output .= "    </div>";
                    }
                }

                if ($atts['title_type'] !== 'none') {
	                //close .content-card
	                $output.= "     </div>";
	                //close .content-card-container
	                $output.= "     </div>";
                }


                if ($atts['imagecaption_displayed'] == 'yes')
                {
                    $output .= '<div class="image-caption gallery-carousel-block">';
                    $output .= $atts['imagecaption'];
                    // adding in the CTA to cation if CTA and Caption are set
                    if ($atts['button'] == "yes") {
                        $style = "";
                        if ($atts['color'] == "custom") {
                            $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                        }

                        $blank = $atts['link_target'] ? 'target="_blank" ' : "";

                        $link  = swedenWpFunctions::get_url($atts['link']);
                        $link  = $link == "http://" ? "" : $link;

                        $output .= "    <div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";
                        if ($splitVar[0] == 'product_carousel') {

                        	$output .= "<div class='grid-element-shop-now-link image-carousel-cta' >".$productLabel."</div>";
                        } else {
                        	$output .= "        <a href='{$link}' class='swBtn ".$this->class_by_arguments('color, position' , $atts, true)."' {$blank} {$style} >";

                        	if ($atts['parent_type'] == "grid-element-shop-now") {
                        		$output .= "            <div class='grid-element-shop-now-link image-carousel-cta' >".$atts['label'].$ctaCarat."</div>";
                        	}
                        	else {
                        		if(!$atts['label']) $atts['label'] = "read more";
                        		$output .= "            <div class='read-more image-carousel-cta'>".$atts['label'].$ctaCarat."</div>";
                        	}

                        	$output .= "        </a>";

                        }
                        $output .= "    </div>";
                    }

                    $output .= '</div>';
                }

                //close outside div with image
                $output.= " </div>";

                return $output;

        }

    }
}
