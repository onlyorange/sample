<?php
/**
 * Slider
 * Shortcode for look carousel
 */

if ( !class_exists( 'sw_sc_celebrity_carousel' ) )
{
  class sw_sc_celebrity_carousel extends swedenShortcodeTemplate
  {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Celebrity Post Carousel', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
                $this->config['order']			= 3;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_post_carousel';
                $this->config['tooltip'] 	    = __('Display a post carousel', 'swedenWp' );
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
                        "name" => __("Carousel Title", 'swedenWp' ),
                        "desc" => __("This is the text that appears above the celebrity carousel.<br/> The 10 most recent celebrity posts will be displayed", 'swedenWp' ),
                        "type" 			=> "input",
                        "id" 			=> "title",
                        //"modal_title" 	=> __("Edit Form Element", 'swedenWp' ),
                        "std"			=> "Celebrities"
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
                $params['innerHtml'] .= "<div class='avia_image_container'>{$params['args']['title']}</div>";
                $params['innerHtml'].= "<div class='avia-element-label' {$template}>".$heading."</div>";

                return $params;
            }

              /**
               * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
               * Works in the same way as Editor Element
               * @param array $params this array holds the default values for $content and $args.
               * @return $params the return array usually holds an innerHtml key that holds item specific markup.
               */


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
				add_action('wp_enqueue_scripts', 'enqueueCelebrityJs');
				global $swedenUnlimited;
				$atts = shortcode_atts(array(
						'title' => ""
				), $atts);
				$args = array(
						'post_type' => 'jet',
						'posts_per_page' => 10,
						'order' => 'DESC',
						//'jet-category' => 'celebrity',

						'tax_query'      => array(
								array(
										'taxonomy' => 'jet-category',
										'field'    => 'slug',
										'terms'    => array('celebrities'),
								),
						)
				);
				// stuuuuu..hard code???
				$term = get_term_by('slug', 'celebrities', 'jet-category');
				$query = new WP_Query( $args );
				$output = '';
				$output .='<div class="grid-element is-full-width">';
				$output .='<div class="celebrities">';
				$output .='<div class="celeb-title slug">'.$atts['title'].'</div>';
				$output .='<a class="view-all view-more-cta" href="'.$swedenUnlimited['homeUrl'].'jet-set/celebrities/" title="View all">View all<span class="icon-arrow-right"></span></a>';

				//set up slideshow
				$output .= '<div class="celebrities-carousel gleambuttons">';
				//create image carousel
				$output .= '<div class="main-carousel carousel-arrow-check-parent">';
				$output .= '<div id="slidecontainer">';
				$output .= '<div class="slick">';

				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						$postid = get_the_ID();
						$post_meta = get_post_meta($postid);
//print_r($post_meta);
						$output .= '<div class="slide">';
						$output .= '<div class="grid-element-subcategory has-card">';
						$output .= '<a href="'.get_permalink().'" title="'.get_the_title().'">';
						if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
							if($swedenUnlimited['client']->isPhone)
								// get phone image
								$image = wp_get_attachment_image_src(get_post_thumbnail_id( $postid ), 'large-thumbnail');
							else if($swedenUnlimited['client']->isTablet)
								$image = wp_get_attachment_image_src(get_post_thumbnail_id( $postid ), 'large-thumbnail');
							else
								$image = wp_get_attachment_image_src(get_post_thumbnail_id( $postid ), 'large-thumbnail');
							$output .= "<img src='".$image[0]."' alt='".get_the_title()."' title='".get_the_title()."' />";
						} else {
							// TODO: we need A default image when no image is attached
							$output .= "<img src='http://www.slowthinkin.com/wp-content/uploads/2013/02/no-image-available1.png'/>";
						}
						$output .= '</a>';

                        $output .= '<div class="content-card-container is-bottom-aligned">';

                        $output .='<a href="'.get_permalink().'" title="'.get_the_title().'">';

						$output .= '<div class="content-card celeb-landing-content-block white-card">';

                        // MDKD-2539
                        // truncate name if necessary
                        $celebName = get_the_title();
                        if(strlen($celebName) > 23) {
                            $celebName = substr($celebName, 0, 20) . '...';
                        }

                        $output .= ' <div class="name">'.$celebName.'</div>';
						$output .= ' <div class="read-more cta">See the look<span class="icon-arrow-right"></span></div>';

                        // .content-card
						$output .= '</div>';

                        $output .= '</a>';

                        // .content-card-container
						$output .= '</div>';
						$output .= '</div>';
                        $output .= '</div>';
					endwhile;
					wp_reset_postdata();
				endif;


				$output .= '        </div>';
				$output .= '    </div>';
				$output .= '    </div>';
				$output .= '<span class="carousel-next arrow-check"><span class="carousel-next-gleam"></span></span>';
				$output .= '<span class="carousel-prev arrow-check"><span class="carousel-prev-gleam"></span></span>';
				$output .= '    </div>';
				$output .= '</div>';

				return $output;
			}

	}

}

function enqueueCelebrityJs() {
    // just shortcuts
    $c = THEME_CSS_URL;
    $j = THEME_JS_URL;
    $s = THEME_STATIC_URL;

    addScripts(array(
        //'avia-shortcodes' => array('file' => "$j/shortcodes.js", 'deps' => array('jquery')),
        //'avia-script' => array('file' => "$j/enfold.js", 'deps' => array('jquery')),
        'celebrity_carousel'  => array('file' => "$s/dist/js/celebrity.js", 'deps' => array('modernizr','jquery'),'inFooter' => TRUE),
    ));

}
