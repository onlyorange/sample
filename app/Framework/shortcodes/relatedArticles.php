<?php
/**
 * related article
 */

if ( !class_exists( 'sw_related_articles' )) {
    class sw_related_articles extends swedenShortcodeTemplate {
        /**
        * Create the config array for the shortcode button
        */
        function shortcode_insert_button() {
            $this->config['name']			= __('Related Articles', 'swedenWp' );
            $this->config['tab']			= __('Content Elements', 'swedenWp' );
            $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-postslider.png";
            $this->config['order']			= 15;
            $this->config['target']			= 'avia-target-insert';
            $this->config['shortcode'] 		= 'sw_related_articles';
            $this->config['tooltip'] 	    = __('Display a related articles element', 'swedenWp' );
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
                        "name"	=> __("Related Articles Type", "swedenWp"),
                        "desc"	=> __("Choose related articles column you want to display"),
                        "id"	=> "show_type",
                        "type"	=> "select",
                		"container_class" =>"avia-element-fullwidth",
                        "std"   => "3cols",
                        "subtype" => array(
                                __('3 Columns', 'swedenWp') =>'3cols',
                                __('Single Column', 'swedenWp') =>'1col',
                        )
                ),
                array(
                        "name" 	=> __("Related Article - Left", 'swedenWp' ),
                        "desc" 	=> __("Which article do you want to add?", 'swedenWp' ),
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
                        "desc" 	=> __("Which article do you want to add?", 'swedenWp' ),
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
                        "name" 	=> __("Related Article - Right", 'swedenWp' ),
                        "desc" 	=> __("Which article do you want to add?", 'swedenWp' ),
                        "id" 	=> "linkc",
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
                        "name" 	=> __("Related Article", 'swedenWp' ),
                        "desc" 	=> __("Which article do you want to add?", 'swedenWp' ),
                        "id" 	=> "link",
                        "type" 	=> "linkpicker",
                        "container_class" =>"avia-element-fullwidth",
                        "required" => array('show_type','equals','1col'),
                        "fetchTMPL"	=> true,
                        "subtype" => array(
                                __('Set Manually', 'swedenWp' ) =>'manually',
                                //__('Single Entry', 'swedenWp' ) =>'single',
                        ),
                        "std" 	=> "manually"),
                array(
                        "name"  => __("Category Heading", 'swedenWp' ),
                        "desc"  => __("Category Heading", 'swedenWp' ),
                        "id"    => "category",
                        "type"  => "input",
                        "required" => array('link','equals','manually'),
                        "std"   => "",
                ),
                array(
                        "name"  => __("Title", 'swedenWp' ),
                        "desc"  => __("Title", 'swedenWp' ),
                        "id"    => "title",
                        "type"  => "input",
                        "required" => array('link','equals','manually'),
                        "std"   => "",
                ),
                array(
                        "name"  => __("Description", 'swedenWp' ),
                        "desc"  => __("Description", 'swedenWp' ),
                        "id"    => "description",
                        "type"  => "input",
                        "required" => array('link','equals','manually'),
                        "std"   => "",
                ),
                array(
                        "name"  => __("CTA", 'swedenWp' ),
                        "desc"  => __("CTA", 'swedenWp' ),
                        "id"    => "cta",
                        "type"  => "input",
                        "required" => array('link','equals','manually'),
                        "std"   => "VIEW ALL STORIES",
                ),

                array(  "name"  => __("Choose Image", 'swedenWp' ),
                        "desc"  => __("This is the image that appears on title area.", 'swedenWp' ),
                        "id"    => "imgsrc",
                        "type"  => "image_title",
                        "required" => array('link','equals','manually'),
                        "title" => __("Insert Image",'swedenWp' ),
                        "button" => __("Insert",'swedenWp' ),
                        "std"   => ""),
            );
        }

        /**
         * Admin editor elements
         * @see swedenShortcodeTemplate::editor_element()
         */
        function editor_element($params) {
            $params['innerHtml'] = "<div style='width:100%; min-height:150px;'>";
            $params['innerHtml'].= "<img src='".$this->config['icon']."' title='".$this->config['name']."' />";
            $params['innerHtml'].= "<div class='avia-element-label'>".$this->config['name']."</div><br/>";
            if ($params['args']['show_type'] == '3cols') {
                $params['innerHtml'] .= "<div style='width:30%; float:left; height:100px; border:1px solid; margin-right:4%;'>

                		</div><div style='width:30%; height:100px; border:1px solid; margin-right:4%; float:left;'>

                		</div><div style='width:30%; height:100px; float:left; border:1px solid;'>

                		</div>";
            }
            else {
                $params['innerHtml'] .= "<div style='width:100%; height:100px; background:orange;'> </div>";
            }
            $params['innerHtml'] .= "</div>";

            return $params;
        }

        /**
         * Front end shortcode hanlder
         * @see swedenShortcodeTemplate::shortcode_handler()
         */
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "") {

            global $swedenUnlimited;

            $alt    = "";
            $title  = "";
            extract(shortcode_atts(array('linka'=>'', 'linkb'=>'', 'linkc'=>''), $atts));
            $atts = shortcode_atts(array(
                'show_type'	=> '',
                'linka' 	=> '',
                'linkb' 	=> '',
                'linkc' 	=> '',
                'link'		=> '',
                'imgsrc'    => '',
                'description'=> '',
                'cta'       => '',
                'title'     => '',
                'category'  => '',
            	'attachment_title' => ''
                //'handle'	=> $shortcodename,
                //'content'	=> ShortcodeHelper::shortcode2array($content),
                //'class'		=> $meta['el_class']
            ), $atts);
            extract($atts);
            $pId = explode(',', $linka);

            $imgsize = '1/1-image-with-text+hero';
            if (!empty($layout_size)) {
            	switch ($layout_size) {
            		case 'one_half':
            			$imgsize = '1/2-image-with-text';
            			break;

            		case 'one_third':
            			$imgsize = '1/3-image-with-text';
            			if ($atts['parent_type']==="grid-element-category") {
            				$imgsize = 'large-thumbnail';
            			}
            			break;

            		case 'one_fourth':
            			$imgsize = '1/4-image-with-text';
            			break;

            		case 'two_third':
            			$imgsize = '2/3-image-with-text';
            			break;

            		default:
            			$imgsize = '1/1-image-with-text+hero';
            			break;
            	}
            }

            $singlePId = explode(',', $link);

            $que = array();
            $class = " ";
            $output = "";
            $i = 0;

            //if manual single related article with manual input
            if ($show_type == "1col" && $singlePId[0] == 'manually') {
                //link
                $output .= "<a href='{$singlePId[1]}'>";
                //full row container
                $output .= "<div class='related_article_single av_one_full'>";
                //half row container
                $output .= "<div class='av_one_half'>";

                // content image
                if (!empty($attachment_title)) {
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$output.= "<div class='image-container {$layout_size} {$imgalign}'>"
                    			. wp_get_attachment_image($attachment_title, 'large-thumbnail'). "</div>";
                    else if($swedenUnlimited['client']->isTablet)
                    	$output.= "<div class='image-container {$layout_size} {$imgalign}'>
                    			". wp_get_attachment_image($attachment_title, $imgsize). "</div>";
                    else
                    	$output.= "<div class='image-container {$layout_size} {$imgalign}'>
                    			". wp_get_attachment_image($attachment_title, '1/2-image-with-text'). "</div>";
                } else {
                	$output .= "<div class='image-container {$layout_size} {$imgalign}'>
                    		<img src='{$imgsrc}'/></div>";
                }

                $output .= "</div>"; //one_half;
                $output .= "<div class='av_one_half'>";
                $output .= "<div class='image-container'>";
                $output .= "<div class='content-cta'>";
                //cat heading, title, description and cta
                $output .= "<div class='category_heading'>".$category."</div>";
                $output .= "<div class='title'>".$title."</div>";
                $output .= "<div class='description'>".$description."</div>";
                $output .= "<div class='cta'>".$cta."<span class='icon-arrow-right'></span></div>";
                $output .= "</div>"; //content;
                $output .= "</div>"; //image-container;
                $output .= "</div>"; //one_half;

                $output .= "</div>";
                $output .= "</a>";

                return $output;
            }

            // ugly...I know. will be updated once all test is done.
            // JL: it's quick fix. I need to revisit this issue when I am free.
            if ($show_type == '3cols') {
                if($pId[2] == '' || $pId[3] == '') {
                	$queB = explode(',', $linkb);
                	$queC = explode(',', $linkc);
                	$pId[2] = $queB[1];
                	$pId[3] = $queC[1];
                }
                $que = array($pId[1], $pId[2], $pId[3]);
                $output .= "<div class='related_articles flex_column av_one_full'>";
                $output .= '<div class="grid-element-section-divider"><hr class="divider"/><div class="slug related-title">RELATED ARTICLES</div></div>';
                $class = "av_one_third";

                if($swedenUnlimited['client']->isPhone) {
                    // for mobile, stack 3
                    $class = "av_one_full";
                }

            }
            else {
                $que = array($singlePId[1]);
                $output .= "<div class='related_articles flex_column av_one_full'>";
                $output .= '<div></div>';
            }

            $post_query = new WP_Query(array('post_type' => array('post', 'mks-edit', 'fashion', 'jet', 'page', 'kors-cares'),
            	'orderby' => 'post__in',
                'post__in' => $que,
                'posts_per_page' => 9999));


            while ( $post_query->have_posts() ) : $post_query->the_post();
                $i++;
                if ($i == 1) {
                    $class .= " first";
                }
                else {
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
                    //anchor to wrap the image and content card
                    $featured = "<a href='".get_permalink()."'>";
                    if($swedenUnlimited['client']->isPhone)
                    	// get phone image
                    	$featured.= "<div class='image-container is-bottom-aligned'>".get_the_post_thumbnail($id, 'large-thumbnail')."</div>";
                    else if($swedenUnlimited['client']->isTablet)
                    	$featured.= "<div class='image-container is-bottom-aligned'>".get_the_post_thumbnail($id, 'large-thumbnail')."</div>";
                    else
                    	$featured.= "<div class='image-container is-bottom-aligned'>".get_the_post_thumbnail($id, 'large-thumbnail')."</div>";
                }
                else {
                    $featured = "<a href='".get_permalink()."' >".get_the_post_thumbnail( $id, "1/2-image-with-text");
                }

                if ($featured) {
                    $output .= $featured;
                }
                else if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if($swedenUnlimited['client']->isPhone)
                        	wp_get_attachment_image($attachment->ID, $imagesize);
                        else if($swedenUnlimited['client']->isTablet)
                        	wp_get_attachment_image($attachment->ID, $imgsize);
                        else
                        	wp_get_attachment_image($attachment->ID, $imgsize);
                        $output .= '<p>';
                        $output .= apply_filters( 'the_title', $attachment->post_title );
                        $output .= '</p>';
                        break;
                    }
                }

                $output .= '<div class="content-card-container is-bottom-aligned">';
                $output .= '  <div class="content-card cat-landing-content-block white-card is-medium">';
                $output .= '    <div class="table-container">';
                // Removing this line, which was outputting a slug element with a colon and the sub-sub-category of the post if it had one - E.P. 7/29
				// $output .= swedenWpFunctions::all_taxonomies_links($id);
			    $output .= "	  <div class='headline'>" . trimOnWord(get_the_title(), 31) . "</div>";
				$output .= "      <span class='read-more cta'>read more<span class='icon-arrow-right'></span></span>";
                $output .= "    </div>";//table-container
				$output .= "  </div>";//content-card
                $output .= "</div>";//content-card-container

                $output .= "</a>";// anchor that wraps image and content card

                $output .= "</div>";//grid-element-subcategory

            endwhile;
            wp_reset_postdata();
            $output .= "</div>";

            return $output;
        }

    }

}
