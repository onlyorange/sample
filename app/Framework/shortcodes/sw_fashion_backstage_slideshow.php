<?php
/**
 * Slider
 * Shortcode for backstage slideshow
 */

if ( !class_exists( 'sw_sc_backstage_slideshow' ) ) {
    class sw_sc_backstage_slideshow extends swedenShortcodeTemplate {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Fashion Backstage Slideshow', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
                $this->config['order']			= 7;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_backstage_slideshow';
                $this->config['shortcode_nested'] = array('sw_backstage_image_content_slider_2');
                $this->config['tooltip'] 	    = __('Display a backstage slideshow element', 'swedenWp' );
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
                            "name"  => __("Choose Cover Image",'swedenWp' ),
                            "desc"  => __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                            "id"    => "imgsrc",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"
                        ),

                    array(
                            "name"  => __("CTA Text", 'swedenWp' ),
                            "desc"  => __("Text to display beneath the 'BACKSTAGE' heading", 'swedenWp' ),
                            "id"    => "title",
                            "type"  => "input",
                            "std"   => "image title"
                        ),

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
                                            "name"  => __("Choose Image",'swedenWp' ),
                                            "desc"  => __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                                            "id"    => "src",
                                            "type"  => "image",
                                    		"fetch"	=> "id",
                                            "title" => __("Insert Image",'swedenWp' ),
                                            "button" => __("Insert",'swedenWp' ),
                                            "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"
                                    ),

                        			array(	"name"  => __("Display Mobile Image", 'swedenWp' ),
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
	                        				"std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"
                        			),
                                    array(
                                        "name"  => __("Date", 'swedenWp' ),
                                        "desc"  => __("Text to display in \"Date\" section of slide caption", 'swedenWp' ),
                                        "id"    => "category",
                                        "type"  => "input",
                                        "std"   => "",
                                    ),

                                    array(
                                        "name"  => __("What", 'swedenWp' ),
                                        "desc"  => __("Text to display in \"What\" section of slide caption", 'swedenWp' ),
                                        "id"    => "title",
                                        "type"  => "tiny_mce",
                                        "std"   => "",
                                    ),

                                    array(
                                        "name"  => __("Where", 'swedenWp' ),
                                        "desc"  => __("Text to display in \"Where\" section of slide caption", 'swedenWp' ),
                                        "id"    => "place",
                                        "type"  => "tiny_mce",
                                        "std"   => "",
                                    )
                                    /*,

                                    array(
                                        "name"  => __("Content", 'swedenWp' ),
                                        "desc"  => __("Content", 'swedenWp' ),
                                        "id"    => "content",
                                        "type"  => "tiny_mce",
                                        "std"   => "",
                                    ),
                                    */
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
				  //$imgThumb = $this->update_template("src", "<img width='50px' src='{{src}}'>");
				  $imgThumb = $this->update_template("img_fakeArg", "{{img_fakeArg}}");
				  if(is_numeric($params['args']['src'])) {
				  	$thumb = isset($params['args']['src']) ? wp_get_attachment_image($params['args']['src']) : "";
				  } else {
				  	$thumb = "<img width='50px' src='". $params['args']['src'] ."'>";
				  }
                  $params['innerHtml']  = "";
                  $params['innerHtml'] .= "<div {$imgThumb}>".$thumb."</div><div class='avia_title_container' {$template}>".$params['args']['title']."</div>";
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
                $atts = shortcode_atts(array(
                'type'          => 'slider',
                'autoplay'		=> 'false',
                'animation'     => 'fade',
                'interval'		=> 5,
                'navigation'    => 'arrows',
                'heading'		=> '',
                'columns'       => 3,
                'handle'		=> $shortcodename,
                'carousel_type' => "home",
                'imgsrc'        => "",
                'mobile_fallback' => "",
                'src_mobile'	=> "",
                "title"         => "",
                'content'		=> ShortcodeHelper::shortcode2array($content),
                'class'			=> $meta['el_class']
                ), $atts);

                $slider  = new sw_backstage_image_content_slider_2($atts);

                return $slider->html();
            }

    }
}

if ( !class_exists( 'sw_backstage_image_content_slider_2' ) )
{
    class sw_backstage_image_content_slider_2
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

            if (!empty($swedenUnlimited) && $swedenUnlimited['page_type']==="backstage") {
                    $thepost = $swedenUnlimited["post"];
                    //check to see if the shortcode is rendering on full slideshow, or article landing page
                    //if full slideshow, render grid_content, else if article, render 7 thumbs and CTA

                    $parent_link = preg_replace("#[^\/]+?\/$#i", "", $thepost->permalink);
                    $output = "";
                    $counter = 0;
                    swedendSliderHelper::$slider++;
                    if(empty($this->config['content'])) return $output;
                    extract($this->config);
                    $slide_count = count($content);
                    //build the list of header images
                    $modal_grid_elements_heading = "";
                    $modal_carousel_images = "";
                    $carousel_thumbs = "";
                    $term_name = wp_get_post_terms($swedenUnlimited['post']->id, 'fashion-category', array("fields" => "names"));
                    foreach ($content as $key => $value) {
                    	// TODO: default image handler, direct db import handler
                    	if($value['attr']['mobile_fallback'] == 'yes' && $swedenUnlimited['client']->isMobile){
                    		$image_id = $value['attr']['src_mobile'];
                    	} else {
                    		if(!empty($value['attr']['src'])){
                    			if(is_numeric($value['attr']['src'])) {
                    				$image_id = $value['attr']['src'];
                    			} else {
                    				$image_id = $value['attr']['attachment'];
                    			}
                    		}
                    	}

                        if ($image_id) {
                        	if($swedenUnlimited['client']->isPhone) {
                        		// get phone image
                        		$image_full = wp_get_attachment_image_src($image_id, '1/2-image-with-text');
	                            $image_full = $image_full[0];
	                            $image_medium = wp_get_attachment_image_src($image_id, 'small-thumbnail');
	                            $image_medium = $image_medium[0];
                        	} else if($swedenUnlimited['client']->isTablet) {
                        		$image_full = wp_get_attachment_image_src($image_id, 'full');
	                            $image_full = $image_full[0];
	                            $image_medium = wp_get_attachment_image_src($image_id, 'medium');
	                            $image_medium = $image_medium[0];
                        	} else {
                        		$image_full = wp_get_attachment_image_src($image_id, 'full');
	                            $image_full = $image_full[0];
	                            $image_medium = wp_get_attachment_image_src($image_id, 'medium');
	                            $image_medium = $image_medium[0];
                        	}
                        } else {
                            $image_full = $value['attr']['src'];
                            $image_medium = $value['attr']['src'];
                        }
                        $modal_grid_elements_heading .= ' <li class="modal-grid-element fashion-backstage" style="background-image: url('.$image_full.');"></li> ';
                        $carousel_thumbs .= ' <div class="carousel-thumb"> <a href="#"><img src="'.$image_medium.'"></a> </div> ';

                        $modal_carousel_images .= ' <div class="carousel-image"> <img src="'.$image_full.'"> ';

                        $modal_carousel_images .= '<div class="slide-content"><div class="category slide-cat"><a href="' . $parent_link . '">'.$term_name[0].'</a></div><div class="slide-name">'.$thepost->title.'</div>';

                        if($value['attr']['category']) {
                            $modal_carousel_images .= '<div class="slide-headline">date</div><div class="slide-subhead">'.$value['attr']['category'].'</div>';
                        }

                        if($value['attr']['title']) {
                            $modal_carousel_images .= '<div class="slide-headline">what</div><div class="slide-subhead">'.$value['attr']['title'].' </div>';
                        }

                        if($value['attr']['place']) {
                            $modal_carousel_images .= '<div class="slide-headline">where</div><div class="slide-subhead">'.$value['attr']['place'].' </div>';
                        }

                        $modal_carousel_images .= '</div></div>';

                    }
                    $output .= <<<EOF
<div id="celebrities-modal" class="celebrities-modal backstage-carousel">
<div class="slideshow-navigation">
          <span class="carousel-next"></span>
          <span class="carousel-prev"></span>
          <a href="{$thepost->permalink}" class="close"></a>
          <div id="app-carousel-count" class="carousel-count">1 / {$slide_count}</div>
          <div id="app-carousel-trigger" class="view-all"><span class="view-all-icon"><span></span></span> View all</div>
</div>
  <div class="modal-grid" id="app-grid">
    <div class="modal-grid-elements">
      <div class="modal-grid-elements-heading"> {$slide_count} items </div>
      <ul>
         {$modal_grid_elements_heading}
      </ul>
    </div>
  </div>
  <div class="modal-carousel">
    <div class="modal-carousel-images">
      <div data-carousel="celebrities-app" id="app-carousel">

        {$modal_carousel_images}

      </div>
    </div>
    <div class="modal-carousel-sidebar">
      <div class="sidebar-content">
        <div id="app-sidebar-content" class="fullscreen-slideshow-block">
          <div class="category slide-cat"><a  href="{$parent_link}">{$term_name[0]}</a></div>
          <div class="slide-name">{$thepost->title}</div>
EOF;

            if($content[0]['attr']['category']) {
                $output .= <<<EOF
                  <div class="slide-headline">date</div>
                  <div class="slide-subhead">{$content[0]['attr']['category']}</div>
EOF;
            }

            if($content[0]['attr']['title']) {
                $output .= <<<EOF
                    <div class="slide-headline">what</div>
                    <div class="slide-subhead">{$content[0]['attr']['title']}</div>
EOF;
            }

            if($content[0]['attr']['place']) {
                $output .= <<<EOF
                    <div class="slide-headline">where</div>
                    <div class="slide-subhead">{$content[0]['attr']['place']}</div>
EOF;
            }

            $output .= <<<EOF
        </div>
        <!--<div class="modal-carousel-related">
          <h3>More {$thepost->title}</h3>
          <div class="carousel-thumbs" data-carousel="3thumbs">
            {$carousel_thumbs}
          </div>
          <span class="carousel-next"></span> <span class="carousel-prev"></span> </div>-->
      </div>
    </div>
  </div>
</div>
EOF;

            }//end if page_type == "backstage"
            else {
                extract($this->config);
                $output .= "<a id='Backstage' href='backstage' data-title='Backstage'>";
                $output .= "<div><div class='backstage-cover-image'><img src='".$imgsrc."'></div><div class='backstage-overlay'><div class='backstage-title'><div class='title-container'><div class='runway-headline'>BACKSTAGE</div>";
                $output .= "<div class='link-container'>";
                $output .= "<div class='fashion-link'>".$title."<span class='icon-arrow-right'></span></div>";
                $output .= "</div></div></div></div></div>";
                $output .= "</a>";

                $output = '<div class="grid-element avia-backstage">'.$output.'</div>';


            }

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

            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));
            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidedisplaynum = "0".$slidenum;
            }

            $output .='<div class="thumb">';
            if (!empty($attachment)) {
				$output .= '<div class="image">';

				if($swedenUnlimited['client']->isPhone)
					// get phone image
					$output.= wp_get_attachment_image($attachment, 'small-thumbnail');
				else if($swedenUnlimited['client']->isTablet)
					$output.= wp_get_attachment_image($attachment, 'small-thumbnail');
				else
					$output.= wp_get_attachment_image($attachment, 'small-thumbnail');

				$output .= '</div>';
			} else {
				// this is mostly default image or image is not in wordpress db (no thumbs)
				$output .='<div class="image"><img src="'.$src.'"></div>';
			}
            $output .='</div>';

            return $output;
        }

        /**
         * Grid Content Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        protected function grid_content($atts, $content = "", $counter) {
        	global $swedenUnlimited;
            $output = "";
            $class  = "";
            $alt    = "";
            $title  = "";

            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));
            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidedisplaynum = "0".$slidenum;
            }

            $output .='<li class="element" data-slide-num="'.$slidenum.'">';
            $output .='<div class="element-num">'.$slidedisplaynum.'</div>';
            if (!empty($src)) {
                if($attachment) {
                    $output .= '<div class="image">';
                    if($swedenUnlimited['client']->isPhone)
					// get phone image
						$output.= wp_get_attachment_image($attachment, 'grid-mid');
					else if($swedenUnlimited['client']->isTablet)
						$output.= wp_get_attachment_image($attachment, 'grid-mid');
					else
						$output.= wp_get_attachment_image($attachment, 'grid-mid');
                    $output .= '</div>';
                } else {
                    $output .='<div class="image"><img src="'.$src.'"></div>';
                }
            }
            $output .='</li>';

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

            extract(shortcode_atts(array('src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));

            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidenumdisplaynum = "0".$slidenum;
            }

            $output .='<li class="slide-element" data-slide-num="'.$slidenum.'">';
            $output .='<div class="content"><h2>'.$category.'</h2><h3>'.$title.'</h3><div>'.$content.'</div></div>';
            if (!empty($src)) {
                if($attachment)
                {
                    $output .= '<div class="image">';
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output.= wp_get_attachment_image($attachment, 'grid-mid');
                    else if($swedenUnlimited['client']->isTablet)
                    	$output.= wp_get_attachment_image($attachment, '2/3-image-with-text');
                    else
                    	$output.= wp_get_attachment_image($attachment, '2/3-image-with-text');
                    $output .= '</div>';
                }
                else
                {
                    $output .='<div class="image"><img src="'.$src.'"></div>';
                }
            }
            $output .='</li>';

            return $output;
        }

    }
}

