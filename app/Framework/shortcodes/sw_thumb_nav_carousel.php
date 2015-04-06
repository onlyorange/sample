<?php
/**
 * Slider
 * Shortcode for look carousel
 */

if ( !class_exists( 'sw_sc_thumb_nav_carousel' ) )
{
  class sw_sc_thumb_nav_carousel extends swedenShortcodeTemplate
  {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Carousel With Thumbnails', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
                $this->config['order']			= 2;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_thumb_nav_carousel';
                $this->config['shortcode_nested'] = array('sw_thumb_carousel_slide');
                $this->config['tooltip'] 	    = __('Display a carousel element with thumb navigation', 'swedenWp' );
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
                        "name" => __("Slides", 'swedenWp' ),
                        "desc" => __("Add, edit, and remove slides", 'swedenWp' ),
                        "type" 			=> "modal_group",
                        "id" 			=> "content",
                        "modal_title" 	=> __("Edit Form Element", 'swedenWp' ),
                        "std"			=> array(
                            array('title'=>__('Slide 1', 'swedenWp' ), 'tags'=>''),
                            array('title'=>__('Slide 2', 'swedenWp' ), 'tags'=>''),

                        ),

                        'subelements' 	=> array(

                                                array(
                                                    "name"  => __("Display Related Articles", 'swedenWp' ),
                                                    "desc"  => __("Choose \"Yes\" to display related articles after this slide", 'swedenWp' ),
                                                    "id"    => "is_related",
                                                    "type"  => "select",
                                                    "subtype" => array(
                                                            __('No',  'swedenWp' ) =>'no',
                                                            __('Yes', 'swedenWp' ) =>'yes',
                                                    ),
                                                    "std" => "no",
                                                ),
                                                array(
                                                    "name"  => __("Related Articles Format", "swedenWp"),
                                                    "desc"  => __("Choose format of related articles"),
                                                    "id"    => "show_type",
                                                    "type"  => "select",
                                                    "required" => array('is_related','equals','yes'),
                                                    "std"   => "3cols",
                                                    "subtype" => array(
                                                            __('Three Columns', 'swedenWp') =>'3cols',
                                                            //__('Single Column', 'swedenWp') =>'1col',
                                                    )
                                                ),
                                                array(
							                        "name" 	=> __("Related Article - Left", 'swedenWp' ),
							                        "desc" 	=> __("Select first related article", 'swedenWp' ),
							                        "id" 	=> "linka",
							                        "type" 	=> "linkpickerB",
							                        "container_class" =>"avia-element-fullwidth",
							                        "required" => array('show_type','equals','3cols'),
							                        "fetchTMPL"	=> true,
							                        "subtype" => array(
							                                __('Single Entry', 'swedenWp' ) =>'single',
							                        ),
							                        "std" 	=> ""),
                                                array(
                                                        "name" 	=> __("Related Article - Center", 'swedenWp' ),
								                        "desc" 	=> __("Select second related article", 'swedenWp' ),
								                        "id" 	=> "linkb",
								                        "type" 	=> "linkpickerB",
								                        "container_class" =>"avia-element-fullwidth",
								                        "required" => array('show_type','equals','3cols'),
								                        "fetchTMPL"	=> true,
								                        "subtype" => array(
								                                //__('Set Manually', 'swedenWp' ) =>'manual',
								                                __('Single Entry', 'swedenWp' ) =>'single',
								                        ),
								                        "std" 	=> ""),
                                                array(
                                                        "name"  => __("Related Article - Right", 'swedenWp' ),
                                                        "desc"  => __("Select third related article", 'swedenWp' ),
                                                        "id"    => "linkc",
                                                        "type"  => "linkpickerB",
                                                        "container_class" =>"avia-element-fullwidth",
                                                        "required" => array('show_type','equals','3cols'),
                                                        "fetchTMPL" => true,
                                                        "subtype" => array(
                                                                //__('Set Manually', 'swedenWp' ) =>'manual',
                                                                __('Single Entry', 'swedenWp' ) =>'single',
                                                        ),
                                                        "std"   => ""),
//This stuff isn't being used in any of the current thumbnail carousels, but could be desired later
/*
                                                array(
                                                        "name"  => __("Related Article", 'swedenWp' ),
                                                        "desc"  => __("Select related article", 'swedenWp' ),
                                                        "id"    => "link",
                                                        "type"  => "linkpicker",
                                                        "container_class" =>"avia-element-fullwidth",
                                                        "required" => array('show_type','equals','1col'),
                                                        "fetchTMPL" => true,
                                                        "subtype" => array(
                                                                __('Set Manually', 'swedenWp' ) =>'manually',
                                                                //__('Single Entry', 'swedenWp' ) =>'single',
                                                        ),
                                                        "std"   => "manually"),
*/
                                                array(
                                                        "name"  => __("Choose Image",'swedenWp' ),
                                                        "desc"  => __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                                                        "id"    => "src",
                                                        "type"  => "image",
                                                		"fetch" => "id",
                                                        "title" => __("Insert Image",'swedenWp' ),
                                                        "button" => __("Insert",'swedenWp' ),
                                                        "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"
                                                ),
//This stuff isn't being used in any of the current thumbnail carousels, but could be desired later - ESP
                                                // array(
                                                //     "name"  => __("Title", 'swedenWp' ),
                                                //     "desc"  => __("Title", 'swedenWp' ),
                                                //     "id"    => "title",
                                                //     "type"  => "input",
                                                //     "std"   => "",
                                                // ),
                                                // array(
                                                //     "name"  => __("Caption", 'swedenWp' ),
                                                //     "desc"  => __("Caption of photo (optional)", 'swedenWp' ),
                                                //     "id"    => "content",
                                                //           "required" => array('is_related','equals','no'),
                                                //     "type"  => "tiny_mce",
                                                //     "std"   => "",
                                                // ),

                                                // array(
                                                //     "name"  => __("Has Video?", 'swedenWp' ),
                                                //     "desc"  => __("Will this slide be a video?", 'swedenWp' ),
                                                //     "id"    => "is_video",
                                                //           "required" => array('is_related','equals','no'),
                                                //     "type"  => "select",
                                                //     "subtype" => array(
                                                //             __('no',  'swedenWp' ) =>'no',
                                                //             __('yes', 'swedenWp' ) =>'yes',
                                                //     ),
                                                //     "std" => "no",
                                                // ),
                                                // array(
                                                //         "name"  => __("Video Type", 'swedenWp' ),
                                                //         "desc"  => __("Choose if video is scene7 or other", 'swedenWp' ),
                                                //         "id"    => "videotype",
                                                //         "type"  => "select",
                                                //         "std"   => "scene7",
                                                //         "required" => array('is_video','equals','yes'),
                                                //         "subtype" => array(
                                                //                             __('scene7',  'swedenWp' ) =>'scene7',
                                                //                             __('other', 'swedenWp' ) =>'other',
                                                //         )
                                                // ),

                                                // array(
                                                //         "name"  => __("Choose Video",'swedenWp' ),
                                                //         "desc"  => __("Either upload a new video, choose an existing video from your media library or link to a video by URL",'swedenWp' )."<br/><br/>".
                                                //                     __("A list of all supported Video Services can be found on",'swedenWp' ).
                                                //                     " <a target='_blank' href='http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'>WordPress.org</a><br/><br/>".
                                                //                     __("Working examples, in case you want to use an external service:",'swedenWp' ). "<br/>".
                                                //                     "<strong>http://vimeo.com/18439821</strong><br/>".
                                                //                     "<strong>http://www.youtube.com/watch?v=G0k3kHtyoqc</strong><br/>",

                                                //         "id"    => "video_src",
                                                //         "type"  => "video",
                                                //         "title" => __("Insert Video",'swedenWp' ),
                                                //         "button" => __("Insert",'swedenWp' ),
                                                //         "std"   => "",
                                                //         "required" => array('videotype','equals','other')
                                                // ),

                                                // array(
                                                //         "name"  => __("Scene7 video ID", 'swedenWp' ),
                                                //         "desc"  => __("Enter Scene7 video ID", 'swedenWp' ),
                                                //         "id"    => "scene7_id",
                                                //         "type"  => "input",
                                                //         "std"   => "",
                                                //         "required" => array('videotype','equals','scene7')
                                                // ),
                                                // array(
                                                //         "name"  => __("Video Format", 'swedenWp' ),
                                                //         "desc"  => __("Choose if you want to display a modern 16:9 or classic 4:3 Video, or use a custom ratio", 'swedenWp' ),
                                                //         "id"    => "format",
                                                //         "type"  => "select",
                                                //         "std"   => "16:9",
                                                //         "required" => array('is_video','equals','yes'),
                                                //         "subtype" => array(
                                                //                             __('16:9',  'swedenWp' ) =>'16-9',
                                                //                             __('4:3', 'swedenWp' ) =>'4-3',
                                                //                             __('Custom Ratio', 'swedenWp' ) =>'custom',
                                                //         )
                                                // ),

                                                // array(
                                                //         "name"  => __("Video width", 'swedenWp' ),
                                                //         "desc"  => __("Enter a value for the width", 'swedenWp' ),
                                                //         "id"    => "width",
                                                //         "type"  => "input",
                                                //         "std"   => "16",
                                                //         "required" => array('format','equals','custom')
                                                // ),

                                                // array(
                                                //         "name"  => __("Video height", 'swedenWp' ),
                                                //         "desc"  => __("Enter a value for the height", 'swedenWp' ),
                                                //         "id"    => "height",
                                                //         "type"  => "input",
                                                //         "std"   => "9",
                                                //         "required" => array('format','equals','custom')
                                                // )

                            )
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
				  //$imgThumb = $this->update_template("src", "<img width='50px' src='{{src}}'>");
				  $imgThumb = $this->update_template("img_fakeArg", "{{img_fakeArg}}");
				  if(is_numeric($params['args']['src'])) {
				  	$thumb = isset($params['args']['src']) ? wp_get_attachment_image($params['args']['src']) : "";
				  } else {
				  	$thumb = "<img width='50px' src='". $params['args']['imgsrc'] ."'>";
				  }
                  $params['innerHtml']  = "";
                  //$params['innerHtml'] .= "<div {$imgThumb}><img style='float:left; margin-right:5px;' width='90px' src='".$params['args']['src']."'></div>";
                  $params['innerHtml'] .= "<div {$imgThumb}>".$thumb."</div>";
                  $params['innerHtml'] .= "<div class='avia_title_container' {$template}>".$params['args']['title']."</div>";
                  $params['innerHtml'] .= "<div style='clear:both;'></div>";

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

                //wp_register_script('product_carousel', plugins_url('js/per-pas-belanja-online.js', __FILE__), array('jquery'), '1.0.0');
                //wp_enqueue_script('product_carousel');

                $atts = shortcode_atts(array(
                'type'          => 'slider',
                'title'         => '',
                'autoplay'		=> 'false',
                'animation'     => 'fade',
                'interval'		=> 5,
                'navigation'    => 'arrows',
                'heading'		=> '',
                'columns'       => 3,
                'handle'		=> $shortcodename,
                'carousel_type' => "home",
                'scale'         => "no",
                'content'		=> ShortcodeHelper::shortcode2array($content),
                'class'			=> $meta['el_class']
                ), $atts);

                $slider  = new sw_thumb_carousel_slide($atts);

                return $slider->html();
            }

    }
}

if ( !class_exists( 'sw_thumb_carousel_slide' ) )
{
    class sw_thumb_carousel_slide
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
                'content'		=> array()
                ), $config);
        }

        public function html()
        {
            global $swedenUnlimited;

            $output = "";
            $counter = 0;
            swedendSliderHelper::$slider++;
            if(empty($this->config['content'])) return $output;

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();
            extract($this->config);

            $slide_count = count($content);

                $slides = '';
                $thumbs = '';

                $counter = 0;
                foreach($content as $key => $value)
                {
                    if ($value['attr']['is_related'] == 'no') {
                        $slides .= $this->slide_content($value['attr'],$value['content'],$counter);
                      }
                      else {
                        $slides .= $this->related_article_content($value['attr'],$value['content'],$counter);
                      }
                    $thumbs .= $this->thumb_content($value['attr'],$value['content'],$counter);
                    $counter++;
                }

                //set up slideshow

                $output .= '<div class="thumb-carousel">';
                            //create image carousel
                $output .= '<div class="stage gleambuttons">';
                $output .= '<div class="carousel carousel-stage carousel-arrow-check-parent">';
                $output .= '<div class="slick">';
                $output .= $slides;
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<span class="next-stage carousel-next arrow-check"><span class="carousel-next-gleam"></span></span>';
                $output .= '<span class="prev-stage carousel-prev arrow-check"><span class="carousel-prev-gleam"></span></span>';
                $output .= '</div>';

                if(!$swedenUnlimited['client']->isPhone) {
                    $output .= '<div class="navigation">';
                    $output .= '<div class="carousel carousel-navigation">';
                    $output .= '<div class="slick">';
                    $output .= $thumbs;
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '<span class="next-navigation carousel-next"></span>';
                    $output .= '<span class="prev-navigation carousel-prev"></span>';
                    $output .= '</div>';
                }

                $output .= '</div>';

            return $output;
        }

        /**
         * Slide Content Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */

        protected function slide_content($atts, $content = "", $counter) {
        	global $swedenUnlimited;
            $output = "";
            $class  = "";
            $alt    = "";
            $title  = "";

            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'show_type' => '', 'link'=>'', 'linka'=>'', 'linkb'=>'', 'linkc'=>'', 'target'=>'no'), $atts));

            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidenumdisplaynum = "0".$slidenum;
            }

            $output .='<div class="slide-element" data-slide-num="'.$slidenum.'">';
            if (!empty($src)) {
            	if (is_numeric($src)) {
            		$output .= '<div class="image">';
            		if($swedenUnlimited['client']->isPhone)
            			// get phone image
            			$output .= wp_get_attachment_image($src, '1/2-image-with-text');
            		else if($swedenUnlimited['client']->isTablet)
            			// get tablet image
            			$output .= wp_get_attachment_image($src, '1/1-image-with-text+hero');
            		else
            			$output.= wp_get_attachment_image($src, '1/1-image-with-text+hero');
            		$output .= '</div>';
            	} else if($attachment) {
                    $output .= '<div class="image">';
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output .= wp_get_attachment_image($attachment, '1/2-image-with-text');
                    else if($swedenUnlimited['client']->isTablet)
                    	// get tablet image
                    	$output .= wp_get_attachment_image($attachment, '1/1-image-with-text+hero');
                    else
                    	$output.= wp_get_attachment_image($attachment, '1/1-image-with-text+hero');
                    $output .= '</div>';
                } else {
                    $output .='<div class="image"><img src="'.$src.'"></div>';
                }
            }
            $output .='<div class="content" data-link="">';
            $output .='<div class="title">'.$title.'</div>';
            $output .='<div class="slug">'.$content.'</div>';
            $output .='</div>';
            $output .='</div>';

            return $output;
        }

        /**
         * Thumb Content Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */

        protected function thumb_content($atts, $content = "", $counter) {
        	global $swedenUnlimited;
            $output = "";
            $class  = "";
            $alt    = "";
            $title  = "";

            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'show_type' => '', 'link'=>'', 'linka'=>'', 'linkb'=>'', 'linkc'=>'', 'target'=>'no'), $atts));

            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidenumdisplaynum = "0".$slidenum;
            }



            $output .='<div class="slide-element" data-array-test="'. $attachment .'" data-slide-num="'.$slidenum.'">';
            if (!empty($src)) {
                if(is_numeric($src)) {
                    $output .= '<div class="image">';
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output .= wp_get_attachment_image($src, 'small-thumbnail');
                    else if($swedenUnlimited['client']->isTablet)
                    	// get tablet image
                    	$output .= wp_get_attachment_image($src, 'small-thumbnail');
                    else
                    	$output.= wp_get_attachment_image($src, 'small-thumbnail');
                    $output .= '</div>';
                // can't remove this attachment because of old data input. :S grrrr..
                } else if($attachment) {
                    	$output .= '<div class="image">';
                    	if($swedenUnlimited['client']->isPhone)
                    		// get phone image
                    		$output .= wp_get_attachment_image($attachment, 'small-thumbnail');
                    	else if($swedenUnlimited['client']->isTablet)
                    		// get tablet image
                    		$output .= wp_get_attachment_image($attachment, 'small-thumbnail');
                    	else
                    		$output.= wp_get_attachment_image($attachment, 'small-thumbnail');
                    	$output .= '</div>';
                } else {
                	// if we don't get the attachment
                	// image is from outside of wordpress or it's default image.
                	// so no need to hit the db server again here
                	/*
                    global $wpdb;
                    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $src ));
                    $output .='<div class="image">'. wp_get_attachment_image($attachment[0],'small-thumbnail') .'</div>';
                    */
                    $output .='<div class="image"><img src="'.$src.'"></div>';
                }
            }
            $output .='</div>';

            return $output;
        }

        /**
         * Related Article Content Handler (when user makes the last slide a related article)
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */

        protected function related_article_content($atts, $content = "", $counter) {
            $title  = "";
            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'linka' => '', 'linkb' => '', 'linkc' => '', 'link'=>'', 'target'=>'no', 'show_type' => ''), $atts));
            
            $alt = $title;
             $pId = explode(',', $linka);

             $que = array();
             $class = " ";
             $output = "";
             $out_output = "";
             $i = 0;

            // ugly...I know. will be updated once all test is done.
            if ($show_type == '3cols') {
            	if($pId[2] == '' || $pId[3] == '') {
            		$queB = explode(',', $linkb);
            		$queC = explode(',', $linkc);
            		$pId[2] = $queB[1];
            		$pId[3] = $queC[1];
            	}
                $que = array($pId[1], $pId[2], $pId[3]);
                $output .= "<div class='related_articles flex_column av_one_full clearfix'>";
                $output .= '<div class="grid-element-section-divider is-full-width is-aligned-right"><div class="slug related-title" style="background: none;">RELATED ARTICLES</div></div>';
                $class = "av_one_third";
            } else {
                $que = array($pId[4]);
                $output .= "<div class='related_articles flex_column av_one_full clearfix'>";
                $output .= '<div></div>';
            }

            $post_query = new WP_Query(array('post_type' => array('post', 'mks-edit', 'fashion', 'jet', 'page'),
            		'orderby' => 'post__in',
                    'post__in' => $que,
                    'posts_per_page' => 9999));

            while ( $post_query->have_posts() ) : $post_query->the_post();
                $i++;
                if ($i == 1) {
                    $class = "av_one_third";
                }
                else{
                    $class = "av_one_third";
              }
                $id = get_the_ID();
                $output .="<div class='center has-card grid-element-subcategory " . $class ."'>"; // wrapper . class grid-element-home

                $args = array(
                        'post_type' => 'attachment',
                        'numberposts' => -1,
                        'post_status' => null,
                        'post_parent' => $id
                );

                $attachments = get_posts( $args ); // not getting any from enfold type of things :S

                if ($show_type == '3cols') {
                    $featured = "<a href='".get_permalink()."' >".get_the_post_thumbnail( $id, "default-thumbnail");
                    //$featured = get_the_post_thumbnail( $id, "large-thumbnail");
                }
                else {
                    $featured = "<a href='".get_permalink()."' >".get_the_post_thumbnail( $id, "1/2-image-with-text");
                    //$featured = get_the_post_thumbnail( $id, "1/2-image-with-text");
                }
                if ($featured) {
                    $output .= $featured;
                } else if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $output .= wp_get_attachment_image( $attachment->ID, 'large-thumbnail' );
                        $output .= '<p>';
                        $output .= apply_filters( 'the_title', $attachment->post_title );
                        $output .= '</p>';
                        break;
                    }
                }

                $output .= '<div class="content-card-container is-bottom-aligned img_align-up">';
                $output .= '  <div class="content-card cat-landing-content-block white-card is-medium-small">';
                $output .= '    <div class="table-container">';
                // no slug necessary, i don't think
                //$output .= swedenWpFunctions::all_taxonomies_links($id);
                $output .= "      <div class='headline'>" . trimOnWord(get_the_title(),28) . "</div>";
                $output .= "      <span class='read-more cta'>read more<span class='icon-arrow-right'></span></span>";
                $output .= "    </div>";//table-container
                $output .= "  </div>";//content-card
                $output .= "</div>";//content-card-container
                $output .= "</a>";// anchor that wraps image and content card
                $output .= "</div>";//grid-element-subcategory

            endwhile;
            wp_reset_postdata();
           $output .= "</div>";

            //prepare a slide
             $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidenumdisplaynum = "0".$slidenum;
            }
            $out_output .='<div class="slide-element" data-slide-num="'.$slidenum.'">';
            if (!empty($src)) {
                if(is_numeric($src))
                {
                    $out_output .= '<div class="image">';
                    $out_output .= wp_get_attachment_image($src,'full');
                      $out_output .= '<div class="relative_article"><div class="bg_cover"></div>';
                      $out_output .= '<div class="relart"><div class="tbl"><div class="tbl-cell">'.$output.'</div></div></div>';
                    $out_output .= '</div></div>';
                }
                else
                {
                    $out_output .= '<div class="image">';
                      $out_output .= '<img src="'.$src.'">';
                      $out_output .= '<div class="relative_article"><div class="bg_cover"></div>';
                      $out_output .= '<div class="relart"><div class="tbl"><div class="tbl-cell">'.$output.'</div></div></div>';
                    $out_output .= '</div></div>';
                }
            }
            $out_output .='<div class="content" data-link="">';
            $out_output .='<div class="title">'.$title.'</div>';
            $out_output .='<div class="slug">'.$content.'</div>';
            $out_output .='</div>';
            $out_output .='</div>';

            return $out_output;
        }

    }
}

