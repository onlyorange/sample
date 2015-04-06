<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'sw_sc_trend' ) )
{
    class sw_sc_trend extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Trend Module', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-text_block.png";
                $this->config['order']			= 20;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_trend';
                $this->config['tinyMCE'] 	    = array('disable' => true);
                $this->config['tooltip'] 	    = __('Creates a trend module', 'swedenWp' );

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
                        "name" 	=> __("Number of items", 'swedenWp' ),
                        "desc" 	=> __("Number of items to be displayed at once??", 'swedenWp' ),
                        "id" 	=> "item_count",
                        "type" 	=> "select",
                        "std" 	=> "4",
                        "subtype" => array(	__('3', 'swedenWp' )=>'3',
                            __('4', 'swedenWp' )=>'4',
                            __('5', 'swedenWp' )=>'5'
                        )),
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
                $params['innerHtml'] = "<div class='avia_textblock avia_textblock_style' data-update_with='content'>Trend carousel displaying ".stripslashes(wpautop(trim(html_entity_decode( $params['args']['item_count']) )))." items</div>";

                return $params;
            }

            function display_twitter($item,$av_column) {
                $output = <<<HTML
                        <div class="flex_column $av_column is-tweet">
                            <div class="content is-tweet">
                                <div class="tab">
                                    <div class="tab-cell">
                                        <span class='quote quote-open'></span>
                                        <p>$item->text</p>
                                        <span class='quote quote-close'></span>
                                    </div>
                                </div>
                                <a class="twitter-handle" href="#" target="_blank" title='$item->author'>$item->author</a>
                                <a href="https://twitter.com/michaelkors" target="_blank" title='$item->author'><span class="now-trending-icon"></span></a>
                            </div>
                        </div>
HTML;

                return $output;

            }

            function display_instagram($item,$av_column) {
                $output = <<<HTML
                        <div class="flex_column $av_column is-instagram">

                            <div class="content is-instagram">
                                <img class='avia_image'
                                src='$item->image'
                                alt='$item->text'>
                                <div class='now-trending-caption'>$item->text</div>
                                <a href="http://instagram.com/michaelkors" target="_blank" title='$item->text'><span class="now-trending-icon"></span></a>
                            </div>
                        </div>
HTML;

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
                global $wpdb;

                $url = THEME_TRENDS_URL;

                // $content = file_get_contents($url);

                // if (empty($content)) {
                //     $url = '/var/www/wordpress/wp-content/themes/destinationkors/static/dist/json/trend.json';
                //     $content = file_get_contents($url);
                // }

                $content = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name="dk_trending"');
                $json = json_decode($content);
                $trends = $json->trend;

                extract(shortcode_atts(array('item_count'=>'4'), $atts));

                $column = "4column";
                $av = "av_one_fourth";

                switch ($item_count) {
                    case 4:
                        $column = "4";
                        $av_column = "av_one_fourth";
                        break;
                    case 5:
                        $column = "5";
                        $av_column = "av_one_fifth";
                        break;
                    case 3:
                        $column = "3";
                        $av_column = "av_one_third";
                        break;
                    default:
                        $column = "4";
                        $av_column = "av_one_fourth";
                        break;
                }

                //print head of carousel
                $output = <<<HTML
<div class="grid-element is-full-width now-trending gleambuttons">

                <div class="now-trending-heading slug">your support</div>

                <div class="now-trending-carousel">
                    <div class="enfold-grid now-trending-grid slick" data-carousel="$column">
HTML;


                //print content of carousel
                foreach ($trends as $item) {
                    if($item->type == "twitter")
                    {
                        $output .= $this->display_twitter($item,$av_column);
                    }
                    else if($item->type == "instagram")
                    {
                        $output .= $this->display_instagram($item,$av_column);
                    }
                }


                //print end of carousel
                $output .= <<<HTML
                        </div>
                        <div class="carousel-next-cont">
                            <span class="carousel-next">
                                <span class="carousel-next-gleam"></span>
                            </span>
                        </div>
                        <div class="carousel-prev-cont">
                            <span class="carousel-prev">
                                <span class="carousel-prev-gleam"></span>
                            </span>
                        </div>
                    </div>
                </div>
HTML;

                return $output;
            }

    }
}
