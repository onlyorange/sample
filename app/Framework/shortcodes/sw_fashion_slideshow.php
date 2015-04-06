<?php
/**
 * Slider
 * Shortcode for fashion slideshow
 */

if(!class_exists('sw_sc_fashion_slideshow')) {

	class sw_sc_fashion_slideshow extends swedenShortcodeTemplate
	{

		/**
		* Create the config array for the shortcode button
		*/

		function shortcode_insert_button() {
			$this->config['name']	 = __('Fashion Slideshow', 'swedenWp' );
			$this->config['tab']	 = __('Content Elements', 'swedenWp' );
			$this->config['icon']	 = swedenBuilder::$path['imagesURL']."sc-postslider.png";
			$this->config['order']	 = 8;
			$this->config['target']	 = 'avia-target-insert';
			$this->config['shortcode'] = 'sw_fashion_slideshow';
			$this->config['shortcode_nested'] = array('sw_image_content_slide');
			$this->config['tooltip']     = __('Display a fashion slideshow element', 'swedenWp' );
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
				array(
                	"type" => "modal_group",
                	"id" => "content",
                	'container_class' =>"avia-element-fullwidth avia-multi-img",
                	"modal_title" => __("Edit Form Element", 'swedenWp' ),
                	"add_label"	 =>  __("Add single image", 'swedenWp' ),
	                "std"	 => array(),
	                'creator'	 =>array(
		                "name" => __("Add Images", 'swedenWp' ),
		                "desc" => __("Upload or select images for slideshow (supports multiple images for upload and selection)", 'swedenWp' ),
		                "id" => "id",
		                "type" => "multi_image",
		                "title" => __("Add Multiple Images",'swedenWp' ),
		                "button" => __("Insert Images",'swedenWp' ),
		                "std"	=> ""
	                ),
	                'subelements' => array(
		                array(
			                "name"  => __("Choose Image",'swedenWp' ),
			                "desc"  => __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
			                "id" => "id",
			                "fetch" => "id",
			                "type" => "image",
			                "title" => __("Insert Image",'swedenWp' ),
			                "button" => __("Insert",'swedenWp' ),
			                "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"),
		    //             array(
			   //              "name"  => __("Category", 'swedenWp' ),
			   //              "desc"  => __("Title category of image", 'swedenWp' ),
			   //              "id"    => "category",
			   //              "type"  => "input",
			   //              "std"   => "",
		    //             ),
		    //             array(
			   //              "name"  => __("Title", 'swedenWp' ),
			   //              "desc"  => __("Title", 'swedenWp' ),
			   //              "id"    => "title",
			   //              "type"  => "input",
			   //              "std"   => "",
		    //             ),
						// array(
			   //              "name"  => __("Content", 'swedenWp' ),
			   //              "desc"  => __("Content", 'swedenWp' ),
			   //              "id"    => "content",
			   //              "type"  => "tiny_mce",
			   //              "std"   => "",
						// ),
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
		function editor_element($params) {
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
		function editor_sub_element($params) {
			$img_template = $this->update_template("img_fakeArg", "{{img_fakeArg}}");
			$template = $this->update_template("title", "{{title}}");
			$content = $this->update_template("content", "{{content}}");
			$thumbnail = isset($params['args']['id']) ? wp_get_attachment_image($params['args']['id']) : "";

			$params['innerHtml']  = "";
			$params['innerHtml'] .= "<div class='avia_title_container'>";
			$params['innerHtml'] .= "	 <span class='avia_slideshow_image' {$img_template} >{$thumbnail}</span>";
			$params['innerHtml'] .= "	 <div class='slideshow_content'>";
			$params['innerHtml'] .= "	 <h4 class='title_container_inner' {$template} >".$params['args']['title']."</h4>";
			$params['innerHtml'] .= "	 <p class='content_container' {$content}>".stripslashes($params['content'])."</p>";
			$params['innerHtml'] .= "	 </div>";
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
		function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "") {
			$atts = shortcode_atts(array(
				'type'          => 'slider',
                'autoplay'	 => 'false',
                'animation'     => 'fade',
                'interval'	 => 5,
                'navigation'    => 'arrows',
                'heading'	 => '',
                'columns'       => 3,
                'handle'	 => $shortcodename,
                'carousel_type' => "home",
                'content'	 => ShortcodeHelper::shortcode2array($content),
                'class'	 => $meta['el_class']
			), $atts);

			$slider  = new sw_fashion_image_content_slider($atts);

			return $slider->html();
		}
    }
}


if (!class_exists('sw_fashion_image_content_slider')) {
	class sw_fashion_image_content_slider
	{
        static  $slider = 0; //slider count for the current page
        protected $config;	 //base config set on initialization

        function __construct($config) {
            global $avia_config;
            $output = "";

            $this->config = array_merge(array(
                'type'          => 'grid',
                'autoplay'	 => 'false',
                'animation'     => 'fade',
                'handle'	 => '',
                'heading'	 => '',
                'navigation'    => 'arrows',
                'columns'       => 3,
                'interval'	 => 5,
                'class'	 => "",
                'css_id'	 => "",
                'content'	 => array()
			), $config);
        }

        public function html() {
            global $swedenUnlimited;

            $output = "";
            $counter = 0;
            swedendSliderHelper::$slider++;
            if(empty($this->config['content'])) return $output;

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();
            extract($this->config);
            $slide_count = count($content);
            //check to see if the shortcode is rendering on full slideshow, or article landing page
            //if full slideshow, render grid_content, else if article, render 7 thumbs and CTA
            if (!empty($swedenUnlimited) && $swedenUnlimited['page_type']==="slideshow") {

                //set up slideshow
                $output .= '<div id = "slideshow-app" class="runway-slideshow">';
                $output .= '<div class="header-nav">';
                $output .= '<div class="close">Close</div>';
                $output .= '<div class="view-all"><span>VIEW ALL</span></div>';
                $output .= '</div>';//header-nav

                //create thumb grid
                $output .= '<div class="collection-grid-container">';
                $output .= '<div id="container" class="collection-grid">';

				foreach($content as $key => $value) {
					$output .= $this->grid_content($value['attr'],$value['content'],$counter);
					$counter++;
				}

                $output .= '</div>';//#container;
                $output .= '</div>';//.collection-grid-container;
                if(!$swedenUnlimited['client']->isPhone && !$swedenUnlmited['client']->isTablet){
                //create image carousel
                $output .= '<div class="main-carousel gleambuttons">';
                $output .= '<div id="slidecontainer" class="carousel-arrow-check-parent">';
                // $output .= '<div class="clearfix">';

                $counter = 0;
                foreach($content as $key => $value) {
                    $output .= $this->slide_content($value['attr'],$value['content'],$counter);
                    $counter++;
                }

                // $output .= '</div>';
                $output .= '</div>';//#slidecontainer;
                $output .= '<span class="carousel-next arrow-check"><span class="carousel-next-gleam"></span></span>';
                $output .= '<span class="carousel-prev arrow-check"><span class="carousel-prev-gleam"></span></span>';
                $output .= '</div>';//.main-carousel;
                }
                $output .= '</div>';//#slideshow-app;
            } else {
                $output .= '<a id="the_look" data-title="The Looks"></a>';
                $output .= '<div class="grid-element-runway-looks fashion-slideshow">';
                $output .= '<a href="slideshow">';
                $output .= '<div class="thumb-container">';
                $output .= '<div class="parallax-container">';
                foreach($content as $key => $value) {
                    $output .= $this->thumb_content($value['attr'],$value['content'],$counter);
                    $counter++;
                    if ($counter >= 15 || ($swedenUnlimited['client']->isPhone && $counter >= 3)) {
                        break;
                    }
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</a>';
                $output .= '<div class="content">';
                $output .= '<a href="slideshow" style="color: black;"><div class="read-more view-all-looks cta">VIEW ALL LOOKS<span class="icon-arrow-right"></span></div></a>';
                $output .= '</div>';
                $output .= '</div>';
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

            extract(shortcode_atts(array('id'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));
            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<9) {
                $slidedisplaynum = "0".$slidenum;
            }

            $output .='<div class="thumb">';

            if (!empty($id)) {
                $output .= '<div class="image">';
                if($swedenUnlimited['client']->isPhone)
                    // get phone image
                    $output.= wp_get_attachment_image($id, 'small-thumbnail');
                else if($swedenUnlimited['client']->isTablet)
                    $output.= wp_get_attachment_image($id, 'small-thumbnail');
				else
					$output.= wp_get_attachment_image($id, 'small-thumbnail');
					//$output .= wp_get_attachment_image($attachment,'1/4-image-with-text');

				$output .= '</div>';
            } else {
    	        // this should be happening.
	            // if this shows then logic has error
            	$output .='<div class="image"><img src="'.swedenBuilder::$path['imagesURL'].'placeholder.jpg"></div>';
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

            extract(shortcode_atts(array('id'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));
            $alt = $title;

            $slidenum = $counter;
            $slidedisplaynum = $slidenum;
            if ($slidenum<9) {
                $slidedisplaynum = "0".$slidenum+1;
            } else{
                $slidedisplaynum = $slidenum + 1;
            }

            $output .='<li class="element" data-slide-num="'.$slidenum.'">';
            $output .='<div class="element-num">'.$slidedisplaynum.'</div>';
            if (!empty($id)) {
            	$output .= '<div class="image">';
            	if($swedenUnlimited['client']->isPhone)
		            // get phone image
		            $output.= wp_get_attachment_image($id, '1/4-image-with-text');
	            else if($swedenUnlimited['client']->isTablet)
	    	        $output.= wp_get_attachment_image($id, '1/4-image-with-text');
	            else
	        	    $output.= wp_get_attachment_image($id, '1/4-image-with-text');
		            //$output .= wp_get_attachment_image($attachment,'1/4-image-with-text');
				$output .= '</div>';
            } else {
	            // this should be happening.
	            // if this shows then logic has error
	            $output .='<div class="image"><img src="'.swedenBuilder::$path['imagesURL'].'placeholder.jpg"></div>';
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

            extract(shortcode_atts(array('id'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no'), $atts));

            $alt = $title;
            $slidenum = $counter;

            $output .='<div class="slide-element" data-slide-num="'.$slidenum.'">';
            $output .= '<div class="element-num">'. ($slidenum + 1) . '</div>';

            if (!empty($id)) {
            	$output .= '<div class="image">';

	            if($swedenUnlimited['client']->isPhone) {
                    $img = wp_get_attachment_image($id, '1/4-image-with-text');
                }
	            else if($swedenUnlimited['client']->isTablet) {
                    $img = wp_get_attachment_image($id, '1/4-image-with-text');
                }
	            else {
                    $img = wp_get_attachment_image($id, '1/2-image-with-text');
                }

                // this gets the src attribute for the image and rather than
                //  than printing out the image just saves it as a dfn attribute.
                //  When the image is needed later, JS will create an <img> with that src.
                $imgSrc = array();
                preg_match('/src="([^"]*)"/i', $img, $imgSrc);
                $dfn = "<img src=". $imgSrc[1] ." />";
                // $dfn = "<dfn data-img-src='".$imgSrc[1]."'></dfn>";  // IE expects and requires a closing </dfn> tag

                $output .= $dfn;
	            $output .= '</div>';   // .image

            } else {
				// this shouldn't be happening.
	            // if this shows then logic has error
            	$output .='<div class="image"><img src="'.swedenBuilder::$path['imagesURL'].'placeholder.jpg"></div>';
            }

            $output .='</div>';

            return $output;
        }
    }
}

