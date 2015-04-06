<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'sw_sc_fashion_all_access' ) )
{
    class sw_sc_fashion_all_access extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']           = __('Fashion All Access', 'swedenWp' );
                $this->config['tab']            = __('Content Elements', 'swedenWp' );
                $this->config['icon']           = swedenBuilder::$path['imagesURL']."sc-image.png";
                $this->config['order']          = 6;
                $this->config['target']         = 'avia-target-insert';
                $this->config['shortcode']      = 'sw_fashion_all_access';
                $this->config['shortcode_nested'] = array('sw_fashion_access_link');
                $this->config['modal_data']     = array('modal_class' => 'mediumscreen');
                $this->config['tooltip']        = __('Display Fashion All Access', 'swedenWp' );
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
                            "std"   => swedenBuilder::$path['imagesURL']."placeholder.jpg"),

                    array(
                            "name"  => __("Title Text", 'swedenWp' ),
                            "desc"  => __("Enter text to appear over cover image", 'swedenWp' ),
                            "id"    => "title",
                            "type"  => "input",
                            "std"   => "image title"
                        ),

                    array(
                        "name" => __("Links", 'swedenWp' ),
                        "desc" => __("Add, edit, and remove the stories linked from this module", 'swedenWp' ),
                        "type"          => "modal_group",
                        "id"            => "content",
                        "modal_title"   => __("Edit Link Element", 'swedenWp' ),
                        "std"           => array(
                            array('title' => __('Link 1', 'swedenWp' ), 'tags'=>''),
                        ),
                        'subelements'   => array(

                                            array(
                                                    "name"  => __("Link Text", 'swedenWp' ),
                                                    "desc"  => __("", 'swedenWp' ),
                                                    "id"    => "title",
                                            		"std"   => "link title",
                                                    "type"  => "input",

                                                ),
                                            array(
                                                    "name"  => __("Link Location", 'swedenWp' ),
                                                    "desc"  => __("Select link to an existing article, an existing category/archive page, or an outside URL", 'swedenWp' ),
                                                    "id"    => "link",
                                                    "type"  => "linkpicker",
                                                    "fetchTMPL" => true,
                                                    "subtype" => array(
                                                                        __('Set Manually', 'swedenWp' ) =>'manually',
                                                                        __('Single Entry', 'swedenWp' ) =>'single',
                                                                        __('Taxonomy Overview Page',  'swedenWp' )=>'taxonomy',
                                                                        ),
                                                    "std"   => "single"),

                                            array(
                                                    "name"  => __("Open Link in new Window", 'swedenWp' ),
                                                    "desc"  => __("Select \"Yes\" to force link to open in a new tab/window", 'swedenWp' ),
                                                    "id"    => "link_target",
                                                    "type"  => "select",
                                                    "std"   => "",
                                                    "subtype" => array(
                                                        __('Open in same window',  'swedenWp' ) =>'',
                                                        __('Open in new window',  'swedenWp' ) =>'_blank')
                                                )
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

                $template = $this->update_template("src", "<img src='{{imgsrc}}' alt=''/>");
                $img      = "";

                if(isset($params['args']['imgsrc']) && is_numeric($params['args']['imgsrc']))
                {
                    $img = wp_get_attachment_image($params['args']['imgsrc'],'large');
                }
                else if(!empty($params['args']['imgsrc']))
                {
                    $img = "<img src='".$params['args']['imgsrc']."' alt=''  />";
                }

                $params['innerHtml']  = "<div class='avia_image avia_image_style avia_hidden_bg_box'>";
                $params['innerHtml'] .= "<div ".$this->class_by_arguments('align' ,$params['args']).">";
                $params['innerHtml'] .= "<div class='avia_image_container' {$template}>{$img}</div>";
                $params['innerHtml'] .= "</div>";
                $params['innerHtml'] .= "</div>";
                $params['class'] = "";

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

                  $params['innerHtml']  = "";
                  $params['innerHtml'] .= "<div class='avia_title_container' {$template}>".$params['args']['title']."</div>";

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
                global $wp_embed;
				global $swedenUnlimited;
                extract(shortcode_atts(array('imgsrc' => '', 'title' => '','target'=>'no', 'attachment'=>''), $atts));
                $anchor = str_replace(" ", "_", $title);
                $output = "<a id='".$anchor."' data-title='".$title."'></a>";
                $output .= "<div><div class='all-access-cover-image'>";
                if($attachment) {
                	if($swedenUnlimited['client']->isPhone)
                		// get phone image
                		$output.= wp_get_attachment_image($attachment, '1/3-image-with-text');
                	else if($swedenUnlimited['client']->isTablet)
                		$output.= wp_get_attachment_image($attachment, '');
                	else
                		$output.= wp_get_attachment_image($attachment, '');
                }
                $output .= "</div><div class='all-access-overlay'><div class='all-access-title'><div class='title-container'><div class='runway-headline'>{$title}</div>";
                $output .= "<div class='link-container'>";
                $content = ShortcodeHelper::shortcode2array($content);

                foreach($content as $key => $value)
                {
                $output .= $this->link_content($value['attr']);
                }
                $output .= "</div></div></div></div></div>";
                $output = '<div class="avia-all-access grid-element">'.$output.'</div>';

                return $output;
            }

        /**
         * Link Content Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        protected function link_content($atts) {

            extract(shortcode_atts(array('link'=>'', 'link_target'=>'', 'title'=>'', 'target'=>'no'), $atts));
            $linkArr = explode(",",$link);
            // $slug =  basename(get_permalink($linkArr[1]));
            //$link  = swedenWpFunctions::get_url($link);
            $link = get_permalink($linkArr[1]);
            $link  = $link == "http://" ? "" : $link;
            $output = "<div class='fashion-link'><a href='".$link."'' target='".$link_target."'>".$title."<span class='icon-arrow-right'></span></a></div>";

            return $output;
        }
    }
}

