<?php
/**
 * Gallery
 * Shortcode that allows to create a gallery based on images selected from the media library
 */

if ( !class_exists( 'sw_sc_section_divider' ) )
{
    class sw_sc_section_divider extends swedenShortcodeTemplate
    {
            static $gallery = 0;
            var $extra_style = "";

            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Section Divider', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-hr.png";
                $this->config['order']			= 16;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_section_divider';
                $this->config['modal_data']     = array('modal_class' => 'mediumscreen');
                $this->config['tooltip']        = __('Creates a custom gallery', 'swedenWp' );
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
                            "name" 	=> __("Alignment", 'swedenWp' ),
                            "desc" 	=> __("Align divider left/right", 'swedenWp' ),
                            "id" 	=> "alignment",
                            "type" 	=> "select",
                            "std" 	=> "left",
                            "subtype" => array(
                                __('left',  'swedenWp' ) =>'is-aligned-left',
                                __('right',  'swedenWp' ) =>'is-aligned-right'
                            ),
                        ),

                    array(
                            "name" 	=> __("Need Header?", 'swedenWp' ),
                            "desc" 	=> __("Does divider need a header?", 'swedenWp' ),
                            "id" 	=> "hasheader",
                            "type" 	=> "select",
                            "std" 	=> "no",
                            "subtype" => array(
                                __('no',  'swedenWp' ) =>'no',
                                __('yes',  'swedenWp' ) =>'yes'
                            ),
                         ),
                    array(	"name" 	=> __("Header", 'swedenWp' ),
                            "desc" 	=> __("Header Text", 'swedenWp' ),
                            "id" 	=> "title",
                            "type" 	=> "input",
                            "required" => array('hasheader','equals','yes'),
                            "std" => ""
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
                $params['innerHtml'] = "<img src='".$this->config['icon']."' title='".$this->config['name']."' />";
                $params['innerHtml'].= "<div class='avia-element-label'>".$this->config['name']."</div>";
                $params['content'] 	 = NULL; //remove to allow content elements

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
                $output  = "";
                $first   = true;

                extract(shortcode_atts(array(
                'alignment'      	=> 'left',
                'hasheader' 		=> 'no',
                'title' 			=> ''
                ), $atts));

                $output .='<div class="grid-element-section-divider is-full-width '.$alignment.'">';

                if ($hasheader == "yes") {
                    $output .='<h2>'.$title.'</h2>';
                }

                $output .='</div>';

                return $output;
            }

    }
}

