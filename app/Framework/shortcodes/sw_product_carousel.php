<?php
/**
 * Slider
 * Shortcode for look carousel
 */

if ( !class_exists( 'sw_sc_product_carousel' ) )
{
  class sw_sc_product_carousel extends swedenShortcodeTemplate
  {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Shop Carousel', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
                $this->config['order']			= 17;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_product_carousel';
                $this->config['shortcode_nested'] = array('sw_product_carousel_slide');
                $this->config['tooltip'] 	    = __('Display a product carousel element', 'swedenWp' );
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
                    "name"  => __("Shop Carousel Title", 'swedenWp' ),
                    "desc"  => __("Select the title text/type for carousel", 'swedenWp' ),
                    "id"    => "title",
                    "type"  => "select",
                    "std"   => "Shop the Trend",
                    "subtype" => array(
                        __('Shop the Trend',  'swedenWp' ) =>'Shop the Trend',
                        __('Shop the Campaign',  'swedenWp' ) =>'Shop the Campaign',
                        __('Shop the Story',  'swedenWp' ) =>'Shop the Story'
                    )),
                    array(
                        "name" => __("Add/Edit Slides", 'swedenWp' ),
                        "desc" => __("Here you can add, remove and edit the slides you want to display.", 'swedenWp' ),
                        "type" 			=> "modal_group",
                        "id" 			=> "content",
                        "modal_title" 	=> __("Edit Form Element", 'swedenWp' ),
                        "std"			=> array(
                            array('title'=>__('Slide 1', 'swedenWp' )),
                            array('title'=>__('Slide 2', 'swedenWp' )),

					),

					'subelements' 	=> array(
								array(	"id"    => "content_wrapper_first",
                        				"type"  => "wrapper",
                        		),
                        		array(	"name" 	=> __("Product ID", 'swedenWp' ),
                        				"desc" 	=> __("Product ID with country prefix (eg. US_MS48U70VY0)", 'swedenWp' ),
                        				"id" 	=> "p_id",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),
                        		array(	"name" 	=> __("SKU", 'swedenWp' ),
                        				"desc" 	=> __("Product SKU code (eg. 823530423)", 'swedenWp' ),
                        				"id" 	=> "sku",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),
                        		array(	"name" 	=> __("Country Code", 'swedenWp' ),
                        				"desc" 	=> __("Two letter country code (eg. US)", 'swedenWp' ),
                        				"id" 	=> "country",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),
                        		array(	"name" 	=> __("Language Code", 'swedenWp' ),
                        				"desc" 	=> __("(eg. us_en)", 'swedenWp' ),
                        				"id" 	=> "lang",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),
                        		array(	"id"    => "next",
                        				"type"  => "carousel_nextBtn",
                        		),
                        		array(	"id"    => "content_wrapper_first",
                        				"type"  => "wrapper_close",
                        		),
                        		array(	"id"    => "content_wrapper_second",
                        				"type"  => "wrapper",
                        		),
                        		/************************ Ends Product info******************/
                        		array(	"name" 	=> __("Title", 'swedenWp' ),
                        				"desc" 	=> __("Text for product title", 'swedenWp' ),
                        				"id" 	=> "title",
                        				"type" 	=> "input",
                        				"std" => ""),
                        		/************************ ends title ******************/
                        		array(	"name" 	=> __("List Price", 'swedenWp' ),
                        				"desc" 	=> __("Product list price", 'swedenWp' ),
                        				"id" 	=> "list_price",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),
                        		array(	"name" 	=> __("Sale Price", 'swedenWp' ),
                        				"desc" 	=> __("Product sale price", 'swedenWp' ),
                        				"id" 	=> "sale_price",
                        				"type" 	=> "input",
                        				"std" => ""
                        		),


								/************************ ends product detail ******************/

                        		array(
                        				"name" 	=> __("Choose Product Image",'swedenWp' ),
                        				"desc" 	=> __("Upload a new image or choose an existing image from the media library",'swedenWp' ),
                        				"id" 	=> "src",
                        				"type" 	=> "image",
                        				"title" => __("Insert Image",'swedenWp' ),
                        				"button" => __("Insert",'swedenWp' ),
                        				"std" 	=> swedenBuilder::$path['imagesURL']."placeholder.jpg"),
                        		/************************ ends image ******************/

                       			array(
                        				"name" 	=> __("Product Description",'swedenWp' ),
                        				"desc" 	=> __("Shop Now widget product description",'swedenWp' ),
                        				"id" 	=> "content",
                        				"type" 	=> "tiny_mce",
                        				"std" 	=> __("", "swedenWp" )),

                        		/************************ ends content ******************/

								array(
									"name" 	=> __("CTA Label", 'swedenWp' ),
                        			"desc" 	=> __("Choose text for Call to Action button", 'swedenWp' ),
                        			"id" 	=> "label",
                       				"type" 	=> "select",
                      				"subtype" => swedenWpFunctions::get_saved_cta_value(1),
                     				"std" => get_option('CTA_1')),
                      			array(
                        			"name" 	=> __("Button Link", 'swedenWp' ),
                        			"desc" 	=> __("Where should the CTA link to?", 'swedenWp' ),
                       				"id" 	=> "link",
                        			"type" 	=> "linkpicker",
                       				"fetchTMPL"	=> true,
                       				"subtype" => array(
                       							__('Set Manually', 'swedenWp' ) =>'manually',
                      							__('Product', 'swedenWp' ) =>'product_sync',
                      							__('Single Entry', 'swedenWp' ) =>'single',
                       				),
                       				"std" 	=> "product_sync"),

								array(
                        			"name" 	=> __("Button Color", 'swedenWp' ),
                        			"desc" 	=> __("Select CTA color", 'swedenWp' ),
                       				"id" 	=> "color",
                       				"type" 	=> "select",
                     				"std" 	=> "theme-color",
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
								array(
									"name"  => __("Credit", 'swedenWp' ),
									"desc"  => __("Photo credit", 'swedenWp' ),
									"id"    => "credit",
									"type"  => "input",
									"std"   => "",
								),
								array(	"id"    => "content_wrapper_second",
									"type"  => "wrapper_close",
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
				  $imgThumb = $this->update_template("src", "<img width='50px' src='{{src}}'>");
                  $params['innerHtml']  = "";
                  $params['innerHtml'] .= "<div {$imgThumb}><img style='float:left; margin-right:5px;' width='90px' src='".$params['args']['src']."'></div><div class='avia_title_container' {$template}>".$params['args']['title']."</div>";
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

                $slider  = new sw_product_carousel_slide($atts);

                return $slider->html();
            }

    }
}

if ( !class_exists( 'sw_product_carousel_slide' ) )
{
    class sw_product_carousel_slide
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

            $output = "";
            $counter = 0;
            swedendSliderHelper::$slider++;
            if(empty($this->config['content'])) return $output;

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();
            extract($this->config);

            $slide_count = count($content);

                //set up slideshow

                $output .= '<div class="product-carousel gleambuttons" data-title="'.$this->config['title'].'" data-scale-slide="'.$this->config['scale'].'">';
                $output .= '<h2>'.$this->config['title'].'</h2>';
                            //create image carousel
                $output .= '<div class="main-carousel">';
                $output .= '<div id="slidecontainer" class="product-carousel-inner">';
                $output .= '<div class="slick">';

                $counter = 0;
                foreach($content as $key => $value)
                {
                    $output .= $this->slide_content($value['attr'],$value['content'],$counter);
                    $counter++;
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<span class="carousel-next"><span class="carousel-next-gleam"></span></span>';
                $output .= '<span class="carousel-prev"><span class="carousel-prev-gleam"></span></span>';
                $output .= '</div>';
                // $output .= '<div class="bottom-content"></div>'; AK: what was this supposed to do? just take up a bunch of space?
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
            global $projectConfig;
            $output = "";
            $class  = "";
            $alt    = "";
            $title  = "";

            extract(shortcode_atts(array('p_id' => "", 'sku' => "", 'country' => "", 'lang' => "", 'src'=>'', 'attachment'=>'', 'title'=>'', 'category'=>'', 'card_type'=>'','align'=>'', 'link'=>'', 'target'=>'no', 'label' => ''), $atts));

            $alt = $title;

            $slidenum = $counter+1;
            $slidedisplaynum = $slidenum;
            if ($counter<10) {
                $slidenumdisplaynum = "0".$slidenum;
            }

            $output .='<div class="slide" data-slide-num="'.$slidenum.'">';

            $link = explode(",", $link);
            $closingTag = '';

            if($link[0] == "manually") {
                $output .= '<a href="'.$link[1].'">';
                $closingTag = '</a>';

            } elseif($link[0] == 'product_sync') {

                if($swedenUnlimited['client']->isPhone) {
                    // no shop widget on mobile -- just link to PDP
                    $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                    $output .= '<a href="'.$link.'">';
                    $closingTag = '</a>';

                } else {
                    $styleNum = str_replace(strtoupper($atts['country']).'_', '', strtoupper($atts['p_id']));
                    $output .= '<div title="Quickview" id="widget-o-pop" data-requirejs-id="utils/shop" data-source="" data-style="'.
                            $styleNum.'" data-country="'.strtoupper($atts['country']).'" data-skuid="'.strtoupper($atts['sku']).'" style="cursor: pointer;">';
                    $closingTag = '</div>';
                }
            }

            //Image
            if (!empty($src)) {
                if($attachment && strpos($src,'wp-content')) {
                    $output .= '<div class="image">';
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output .= wp_get_attachment_image($attachment, 'large');
                    else if($swedenUnlimited['client']->isTablet)
                    	// get tablet image
                    	$output .= wp_get_attachment_image($attachment, 'large');
                    else
                    	$output.= wp_get_attachment_image($attachment, 'large');
                    $output .= '</div>';
                } else {
                    $output .='<div class="image"><img src="'.$src.'"></div>';
                }
            }

            $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);

            $label .= '<span class="icon-arrow-right"></span>';

            $output .='<div class="content product-block">';
            $output .='<div class="product-name"><a href="'. $link .'">'.$title.'</a></div>';
            $output .='<div class="slug">'.$content.'</div>';   // no style guide reference for this!
            $output .='<div class="cta"><a href="'. $link .'">'.$label.'</a></div>';
            $output .='<div class="credit">'.$credit.'</div>';  // no style guide reference for this!
            $output .='</div>';
            $output .= $closingTag;
            $output .='</div>';

            return $output;
        }

    }
}

