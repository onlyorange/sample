<?php
/**
 * ROWS
 * Shortcode which creates rows for better content separation
 */

 // Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }



if ( !class_exists( 'sw_sc_row_element' ) )
{
    class sw_sc_row_element extends swedenShortcodeTemplate{

            function shortcode_insert_button()
            {
                $this->config['name']		= 'Row';
                $this->config['icon']		= swedenBuilder::$path['imagesURL']."sc-full.png";
                $this->config['tab']		= __('Layout Elements', 'swedenWp' );
                $this->config['order']		= 10;
                $this->config['target']		= "avia-section-drop";
                $this->config['shortcode'] 	= 'av_sw_row_element';
                $this->config['shortcode_nested'] = array('av_one_full','av_one_half','av_one_third','av_two_third','av_one_fourth','av_three_fourth','av_one_fifth', 'av_three_fifth', 'av_four_fifth');
                $this->config['html_renderer'] 	= false;
                $this->config['tooltip'] 	= __('Creates a row element', 'swedenWp' );
                $this->config['tinyMCE'] 	= array('instantInsert' => "[av_sw_contentslide first]Add Content here[/av_sw_contentslide]");
                $this->config['drag-level'] = 1;
                $this->config['drop-level'] = 1;
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

                extract($params);
                $name 		= $this->config['shortcode'];
                $drag 		= $this->config['drag-level'];
                $drop 		= $this->config['drop-level'];


                $output  = "<div class='avia_layout_column avia_layout_row avia_pop_class av_one_full av_drag' data-dragdrop-level='{$drag}' data-width='{$name}'>";
                $output .= "<div class='avia_sorthandle menu-item-handle'>";


                $output .= "<span>Row</span>";
                $output .= "<a class='avia-delete'  href='#delete' title='".__('Delete Row','swedenWp' )."'>x</a>";
                //$output .= "<a class='avia-new-target'  href='#new-target' title='".__('Move Element','swedenWp' )."'>+</a>";
                $output .= "<a class='avia-clone'  href='#clone' title='".__('Clone Row','swedenWp' )."' >".__('Clone Column','swedenWp' )."</a></div>";

                $output .= "<div class='avia_inner_shortcode avia_connect_sort av_drop ' data-dragdrop-level='{$drop}'>";
                $output .= "<textarea data-name='text-shortcode' cols='20' rows='4'>".ShortcodeHelper::create_shortcode_by_array($name, $content, $args)."</textarea>";
                if($content)
                {
                    $content = $this->builder->do_shortcode_backend($content);
                }
                $output .= $content;
                $output .= "</div></div>";

                return $output;
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
                global $avia_config;

                $avia_config['content_slider'] = $shortcodename;




                $output  = '<div class="flex_column '.$shortcodename.' '.$meta['el_class'].'">';

                //if the user uses the column shortcode without the layout builder make sure that paragraphs are applied to the text
                $content =  (empty($avia_config['conditionals']['is_builder_template'])) ? ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($content)) : ShortcodeHelper::avia_remove_autop($content, true);

                $output .= $content.'</div>';

                unset($avia_config['content_slider']);

                return $output;
            }
    }
}

