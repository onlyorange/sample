<?php
/**
 * single ecommerce product module
 * Shortcode which creates product element
 */

if ( !class_exists( 'sw_ecommerce' ) ) {
    class sw_ecommerce extends swedenShortcodeTemplate {
        /**
         * Create the config array for the shortcode button
         */
        function shortcode_insert_button() {
            $this->config['name']           = __('Product', 'swedenWp' );
            $this->config['tab']            = __('Content Elements', 'swedenWp' );
            $this->config['icon']           = swedenBuilder::$path['imagesURL']."sc-postcontent.png";
            $this->config['order']          = 14;
            $this->config['target']         = 'avia-target-insert';
            $this->config['shortcode']      = 'sw_product_box';
            $this->config['modal_data']     = array('modal_class' => 'mediumscreen data-ATG');
            $this->config['tooltip']        = __('Creates a ecommerce product element', 'swedenWp' );
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
                    array(  "id"    => "content_wrapper_first",
                            "type"  => "wrapper",
                    ),
                    array(  "name"  => __("Product ID", 'swedenWp' ),
                            "desc"  => __("Enter product ID (eg. US_MS48U70VY0)", 'swedenWp' ),
                            "id"    => "p_id",
                            "type"  => "input",
                            "std" => ""
                    ),
                    array(  "name"  => __("SKU #", 'swedenWp' ),
                            "desc"  => __("Enter SKU number (eg. 823530423)", 'swedenWp' ),
                            "id"    => "sku",
                            "type"  => "input",
                            "std" => ""
                    ),
                    array(  "name"  => __("Country Code", 'swedenWp' ),
                            "desc"  => __("Enter country code(eg. us)", 'swedenWp' ),
                            "id"    => "country",
                            "type"  => "input",
                            "std" => ""
                    ),
                    array(  "name"  => __("Language Code", 'swedenWp' ),
                            "desc"  => __("Enter language code(eg. us_en)", 'swedenWp' ),
                            "id"    => "lang",
                            "type"  => "input",
                            "std" => ""
                    ),
                    array(  "id"    => "next",
                            "type"  => "nextBtn",
                    ),
                    array(  "id"    => "content_wrapper_first",
                            "type"  => "wrapper_close",
                    ),
                    array(  "id"    => "content_wrapper_second",
                            "type"  => "wrapper",
                    ),
                    /************************ Ends Product info******************/
                    array(  "name"  => __("Title Type", 'swedenWp' ),
                            "desc"  => __("Choose title type", 'swedenWp' ),
                            "id"    => "title_type",
                            "type"  => "select",
                            "std"   => "text",
                            "subtype" => array(
                                    __('Text title', 'swedenWp') =>'text',
                                    __('Image title', 'swedenWp') =>'image',
                            )
                    ),

                    array(  "name"  => __("Title", 'swedenWp' ),
                            "desc"  => __("This is the text that appears on title area.", 'swedenWp' ),
                            "id"    => "title",
                            "type"  => "input",
                            "required" => array('title_type','equals','text'),
                            "std" => ""),

                    array(  "name"  => __("Choose Image", 'swedenWp' ),
                            "desc"  => __("This is the image that appears on title area.", 'swedenWp' ),
                            "id"    => "src",
                            "type"  => "image_title",
                            "required" => array('title_type','equals','image'),
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std"   => ""),
                    /************************ ends title ******************/
                    array(  "name"  => __("List Price", 'swedenWp' ),
                            "desc"  => __("Product list price.", 'swedenWp' ),
                            "id"    => "list_price",
                            "type"  => "input",
                            "std" => ""
                    ),
                    array(  "name"  => __("Sale Price", 'swedenWp' ),
                            "desc"  => __("Product sale price.", 'swedenWp' ),
                            "id"    => "sale_price",
                            "type"  => "input",
                            "std" => ""
                    ),


                    /************************ ends product detail ******************/
                    /*
                    array(  "name"  => __("Category Title", 'swedenWp' ),
                            "desc"  => __("This is the text that appears on the category title area. Will be overidden if post has been selected in CTA", 'swedenWp' ),
                            "id"    => "category_title",
                            "type"  => "input",
                            "std" => $catTitle),
                    */
                    array(
                            "name"  => __("Content Card Parent Type", 'swedenWp' ),
                            "desc"  => __("Choose here, how to align your content", 'swedenWp' ),
                            "id"    => "parent_type",
                            "type"  => "select",
                            "std"   => "home",
                            "subtype" => array(
                                    __('home',  'swedenWp' ) =>'grid-element-home',
                                    __('full-width hero',  'swedenWp' ) =>'grid-element-home hero',
                                    __('category',  'swedenWp' ) =>'grid-element-category',
                                    __('subcategory',  'swedenWp' ) =>'grid-element-subcategory',
                                    __('shopnow',  'swedenWp' ) =>'grid-element-shop-now',
                                    __('none',  'swedenWp' ) =>'',
                            )
                    ),
                    /***************** parent type ************************/
                    array(
                            "name"  => __("Choose Image",'swedenWp' ),
                            "desc"  => __("Either upload a new, or choose an existing image from your media library",'swedenWp' ),
                            "id"    => "imgsrc",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std"   => ""),

                    // array(
                    //      "name"  => __("Image Alignment", 'swedenWp' ),
                    //      "desc"  => __("Choose here, how to align your image", 'swedenWp' ),
                    //      "id"    => "img_align",
                    //      "type"  => "select",
                    //      "std"   => "no",
                    //      "required" => array('image','equals','yes'),
                    //      "subtype" => array(
                    //              __('Push down top',  'swedenWp' ) =>'down',
                    //              __('Push up bottom',  'swedenWp' ) =>'up',
                    //              __('No special alignment', 'swedenWp' ) =>'no',
                    //      )
                    // ),

                    array(
                            "name"  => __("Image Layout", 'swedenWp' ),
                            "desc"  => __("Choose whether image is background or not", 'swedenWp' ),
                            "id"    => "img_layout",
                            "type"  => "select",
                            "std"   => "not background",
                            "required" => array('image','equals','yes'),
                            "subtype" => array(
                                    __('not background',  'swedenWp' ) =>'',
                                    __('background',  'swedenWp' ) =>'has-image-as-bg',
                            )
                    ),
                    /************************ ends image ******************/
                    array(
                            "name"  => __("Content Card Style", 'swedenWp' ),
                            "desc"  => __("Choose here, how to align your content", 'swedenWp' ),
                            "id"    => "card_type",
                            "type"  => "select",
                            "std"   => "",
                            "subtype" => array(
                                    __('White Block',  'swedenWp' ) =>'white-card',
                                    __('Thin-stroke Outline',  'swedenWp' ) =>'white-card is-outline',
                                    __('Text Overlay',  'swedenWp' ) =>'white-card is-no-card',
                                    __('Article Hero Text Overlay',  'swedenWp' ) =>'white-card is-no-card is-article-hero',
                                    __('Article Collaborator White Block',  'swedenWp' ) =>'white-card is-article-collaborator',
                                    __('Get The Look',  'swedenWp' ) =>'white-card is-article-get-look'
                            )
                    ),
                    array(
                            "name"  => __("Content Text Color", 'swedenWp' ),
                            "desc"  => __("Choose a color for the content here", 'swedenWp' ),
                            "id"    => "text_color",
                            "type"  => "select",
                            "std"   => "is-black",
                            "subtype" => array(
                                    __('Black', 'swedenWp' )=>'is-black',
                                    __('White', 'swedenWp' )=>'is-white',
                            )),


                    array(
                            "name"  => __("Content",'swedenWp' ),
                            "desc"  => __("Enter content here",'swedenWp' ),
                            "id"    => "content",
                            "type"  => "tiny_mce",
                            "std"   => __("", "swedenWp" )),
                    array(
                            "name"  => __("Content Alignment", 'swedenWp' ),
                            "desc"  => __("Choose here, how to align your content", 'swedenWp' ),
                            "id"    => "align",
                            "type"  => "select",
                            "std"   => "center",
                            "subtype" => array(
                                    __('Center',  'swedenWp' ) =>'center',
                                    __('Right',  'swedenWp' ) =>'right avia-align-right is-right-aligned',
                                    __('Left',  'swedenWp' ) =>'left avia-align-left is-left-aligned',
                                    __('Top Right',  'swedenWp' ) =>'top-right is-top-right-aligned',
                                    __('Top Left',  'swedenWp' ) =>'top-left is-top-left-aligned',
                                    __('Top',  'swedenWp' ) =>'top is-top-aligned',
                                    __('Bottom',  'swedenWp' ) =>'bottom is-bottom-aligned',
                                    __('Bottom Right',  'swedenWp' ) =>'bottom-right is-bottom-right-aligned',
                                    __('Bottom Left',  'swedenWp' ) =>'bottom-left is-bottom-left-aligned',
                                    __('No special alignment', 'swedenWp' ) =>'',
                            )
                    ),
                    /************************ ends content ******************/
                    array(
                            "name"  => __("CTA", 'swedenWp' ),
                            "desc"  => __("Do you want to display a Call to Action Button?", 'swedenWp' ),
                            "id"    => "button",
                            "type"  => "select",
                            "std"   => "yes",
                            "subtype" => array(
                                    __('yes',  'swedenWp' ) =>'yes',
                                    __('no',  'swedenWp' ) =>'no',
                            )
                    ),

                    array(  "name"  => __("CTA Label", 'swedenWp' ),
                            "desc"  => __("This is the text that appears on the button.", 'swedenWp' ),
                            "id"    => "label",
                            "type"  => "select",
                            "required" => array('button','equals','yes'),
                            "subtype" => swedenWpFunctions::get_saved_cta_value(1),
                            "std" => get_option('CTA_1')),
                    array(
                            "name"  => __("Button Link?", 'swedenWp' ),
                            "desc"  => __("Where should the CTA link to?", 'swedenWp' ),
                            "id"    => "link",
                            "type"  => "linkpicker",
                            "required" => array('button','equals','yes'),
                            "fetchTMPL" => true,
                            "subtype" => array(
                                    __('Set Manually', 'swedenWp' ) =>'manually',
                                    __('Product', 'swedenWp' ) =>'product_sync',
                                    __('Single Entry', 'swedenWp' ) =>'single'
                            ),
                            "std"   => "product_sync"),

                    array(
                            "name"  => __("Open Link in new Window?", 'swedenWp' ),
                            "desc"  => __("Select here if you want to open the linked page in a new window", 'swedenWp' ),
                            "id"    => "link_target",
                            "type"  => "select",
                            "std"   => "",
                            "required" => array('button','equals','yes'),
                            "subtype" => array(
                                    __('Open in same window',  'swedenWp' ) =>'',
                                    __('Open in new window',  'swedenWp' ) =>'_blank')),

                    array(
                            "name"  => __("Button Color", 'swedenWp' ),
                            "desc"  => __("Choose a color for the CTA here", 'swedenWp' ),
                            "id"    => "color",
                            "type"  => "select",
                            "std"   => "theme-color",
                            "required" => array('button','equals','yes'),
                            "subtype" => array(
                                    __('Black', 'swedenWp' )=>'is-black',
                                    __('White', 'swedenWp' )=>'is-white',
                                    __('Custom Color', 'swedenWp' )=>'custom',
                            )),


                    array(
                            "name"  => __("Custom Background Color", 'swedenWp' ),
                            "desc"  => __("Select a custom background color for the CTA here", 'swedenWp' ),
                            "id"    => "custom_bg",
                            "type"  => "colorpicker",
                            "std"   => "#444444",
                            "required" => array('color','equals','custom')
                    ),

                    array(
                            "name"  => __("Custom Font Color", 'swedenWp' ),
                            "desc"  => __("Select a custom font color for the CTA here", 'swedenWp' ),
                            "id"    => "custom_font",
                            "type"  => "colorpicker",
                            "std"   => "#ffffff",
                            "required" => array('color','equals','custom')
                    ),
                    array(  "id"    => "content_wrapper_second",
                            "type"  => "wrapper_close",
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
            $template_title = $this->update_template("src", "<img src='{{src}}' alt=''/>");
            $template_body = $this->update_template("imgsrc", "<img src='{{imgsrc}}' alt=''/>");
            $title    = "";
            $img = "";

            // show title in editor
            if (!empty($params['args']['title'])) {
                $title = "<p>" . $params['args']['title']. "</p>";
            } else if (empty($params['args']['title']) && isset($params['args']['src']) && is_numeric($params['args']['src'])) {
                $title = wp_get_attachment_image($params['args']['src'],'large');
            } else if (!empty($params['args']['src']) && empty($params['args']['title'])) {
                $title = "<img src='".$params['args']['src']."' alt=''  />";
            } else {

            }

            if (isset($params['args']['imgsrc']) && is_numeric($params['args']['imgsrc'])) {
                $img = wp_get_attachment_image($params['args']['imgsrc'],'large');
            } else if (!empty($params['args']['imgsrc'])) {
                $img = "<img src='".$params['args']['imgsrc']."' alt=''  />";
            }
            $params['class'] = "";
            $params['innerHtml']  = "";
            $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style'>";
            $params['innerHtml'] .= "   <div ".$this->class_by_arguments('button' ,$params['args']).">";
            $params['innerHtml'] .= "       <div data-update_with='content' class='avia-promocontent'>";
            $params['innerHtml'] .= "           <div class='avia_image_container' {$template_title}>{$title}</div>";
            $params['innerHtml'] .= "           <div class='avia_image_container' {$template_body}>{$img}</div>";
            $params['innerHtml'] .=             stripslashes(wpautop(trim($params['content'])))."</div>";
            $params['innerHtml'] .= "       <div class='avia_button_box avia_hidden_bg_box'>";
            $params['innerHtml'] .= "           <div ".$this->class_by_arguments('color' ,$params['args']).">";
            $params['innerHtml'] .= "               <span data-update_with='label' class='avia_title' >".$params['args']['label']."</span>";
            $params['innerHtml'] .= "           </div>";
            $params['innerHtml'] .= "       </div>";
            $params['innerHtml'] .= "   </div>";
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
        function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
        {
            global $swedenUnlimited;
            global $projectConfig;
            $output = "";
            $class  = "";
            $alt    = "";
            $title  = "";
            $splitVar = '';
            extract(shortcode_atts(array('imgsrc'=>'', 'alt_title'=>'', 'title_title'=>'', 'sale_price'=>'', 'list_price'=>'', 'src'=>'', 'link'=>'', 'attachment'=>'', 'attachment_title' => '', 'target'=>'no','layout_size'=>''), $atts));
            $atts =  shortcode_atts(array(
                    'p_id' => "",
                    'sku' => "",
                    'country' => "",
                    'lang' => "",
                    'title_type' => "",
                    'title' => "",
                    'src' => "",
                    'imgsrc' => "",
                    'parent_type' => "home",
                    'align' => "",
                    'card_type' => "",
                    // 'img_align' => "",
                    'img_layout' => "",
                    'text_color' => 'is-black',
                    'content' => "",
                    'button' => 'yes',
                    'label' => 'Read More A',
                    'link' => '',
                    'link_target' => '',
                    'color' => 'is-black',
                    'custom_bg' => '#444444',
                    'custom_font' => '#ffffff',
                    'position' => 'center',
            ), $atts);

            $blank = $atts['link_target'] ? 'target="_blank" ' : "";

            if (!empty($link)) {
                $link  = swedenWpFunctions::get_url($atts['link']);
                $link  = $link == "http://" ? "" : $link;
            }

            if (!empty($attachment)) {
                $attachment_entry = get_post( $attachment );
                $alt = get_post_meta($attachment_entry->ID, '_wp_attachment_image_alt', true);
                $alt = !empty($alt) ? esc_attr($alt) : '';
                $title = trim($attachment_entry->post_title) ? esc_attr($attachment_entry->post_title) : "";
            }
            if ($atts['title_type'] == 'image' && !empty($attachment_title)) {
                $attachment_entry_title = get_post( $attachment_title );
                $alt_title = get_post_meta($attachment_entry_title->ID, '_wp_attachment_image_alt', true);
                $alt_title = !empty($alt_title) ? esc_attr($alt_title) : '';
                $title_title = trim($attachment_entry_title->post_title) ? esc_attr($attachment_entry_title->post_title) : "";
            }

            if($atts['parent_type'] == "grid-element-shop-now")
            {
                // wrapper
                $output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .'">';
            }
            else
            {
                // wrapper
                $output .='<div class="'. $atts['align'] .' '.$atts['img_layout'].' '. $atts['parent_type'] .' has-card">';
            }

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
            // content image
            if (!empty($attachment)) {
                //$output .= "<div class='image-container {$layout_size}'><a href='{$link}'>".wp_get_attachment_image($attachment,$imgsize)."</a></div>";
                if($swedenUnlimited['client']->isPhone)
                    // get phone image
                    $output.= "<div class='image-container {$layout_size}'><a href='{$link}' class='".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget} title='{$atts['label']}'>".
                    wp_get_attachment_image($attachment, $imagesize)."</a></div>";
                else if($swedenUnlimited['client']->isTablet)
                    $output.= "<div class='image-container ".$this->class_by_arguments('align' ,$atts, true)." ".$margin."'><a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget} title='{$atts['label']}'>".
                    wp_get_attachment_image($attachment, $imgsize)."</a></div>";
                else
                    $output.= "<div class='image-container ".$this->class_by_arguments('align' ,$atts, true)." ".$margin."'><a href='{$link}' class='avia_image ".$meta['el_class']." ".$this->class_by_arguments('align' ,$atts, true)."' {$linktarget} title='{$atts['label']}'>".
                    wp_get_attachment_image($attachment, $imgsize)."</a></div>";

            } else if (!empty($imgsrc)) {
                if (is_numeric($imgsrc)) {
                    $output .= "<div class='image-container'><a href='{$link}' title='{$atts['label']}'>".wp_get_attachment_image($imgsrc,$imgsize)."</a></div>";
                } else {
                    $output.= "<div class='image-container'>";
                                        $output.= " <a href='{$link}' title='{$atts['label']}'><img class='avia_image ".$this->class_by_arguments('align' ,$atts, true)." {$class}' src='{$imgsrc}' alt='{$alt}' title='{$title}' /></a>";
                    $output.= "</div>";
                }
            }
            /**
             * image with text -> removing parent page type
             if (is_front_page()) { echo 'this is home page'; } else { echo 'its not! home page'; }
             // echo '<p>page template used: '. get_page_template_slug(get_the_ID()) . '</p>';
             $template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
             // echo '<p>tempalte name from post meta : '. $template_name . '</p>';
             // echo '<p>current tempalte : '.get_option('current_page_template') .'</p>';
             if(is_front_page()) $parentPage = 'grid-element-home';
             //else if()

                **/
            if ($atts['parent_type'] == "grid-element-shop-now") {
                // content
                $output .='<div class="content-card-container '.$atts['align'].' ">';
                $output .='   <div class="content-card cat-landing-content-block '.$atts['text_color'].'">';
                $output .='     <div class="headline">'.$atts['title'].'</div>';
                $output .='     <div class="subhead">'.stripslashes(wpautop(trim($content))).'</div>';

            } else {
                // category
                $link  = swedenWpFunctions::get_url($atts['link']);
                $link  = $link == "http://" ? "" : $link;

                // content
                $output .='<div class="content-card-container '.$atts['align'].'" >';
                $output .='  <div class="content-card '.$atts['card_type'].' '.$atts['text_color'].'">';

                /* ecomm element doesn't have post ID
                if (!empty($link)) {
                    $output.=   swedenWpFunctions::all_taxonomies_links($selectedPostID);
                }
                */
                // title
                if (!empty($src)&&$atts['title_type']!="text") {
                    if($swedenUnlimited['client']->isPhone)
                        // get phone image
                        $output.= wp_get_attachment_image($attachment_title, $imagesize);
                    else if($swedenUnlimited['client']->isTablet)
                        $output.= wp_get_attachment_image($attachment_title, $imagesize);
                    else
                        $output.= wp_get_attachment_image($attachment_title, $imagesize);

                } else {
                    $output.= "     <div class='content-title headline'>" . $atts['title']. "</div>";
                }

                if (!empty($content)) {
                    $output.= "             <div class='slug'>". stripslashes(wpautop(trim($content))) . "</div>" ;
                }

                /*
                if(!empty($list_price)) $output .= "<div class='listPrice'>List Price: " . $list_price . "</div>";
                if(!empty($sale_price)) $output .= "<div class='salePrice'>Sale Price: " . $sale_price . "</div>";
                */

            } // end if


            // CTA
            if ($atts['button'] == "yes") {
                $style = "";
                if ($atts['color'] == "custom") {
                    $style .= "style='background-color:".$atts['custom_bg']."; border-color:".$atts['custom_bg']."; color:".$atts['custom_font']."; '";
                }

                $output .= "    <div class='sw_btn_wrapper". $this->class_by_arguments('button' , $atts, true) ."'>";

                if($atts['link'] == 'product_sync' && !$swedenUnlimited['client']->isPhone) {

                    $styleNum = str_replace(strtoupper($atts['country']).'_', '', strtoupper($atts['p_id']));
                    $output .= '<div title="Quickview" id="widget-o-pop" data-requirejs-id="utils/shop" data-source="" data-style="'.
                        $styleNum.'" data-country="'.strtoupper($atts['country']).'" data-skuid="'.strtoupper($atts['sku']).'">
                        <span class="read-more" style="margin-top:20px; cursor:pointer">'.$atts['label'].'</span><span class="icon-arrow-right"></span></div>';
                    $output .= '</div>';

                } else {

                    if($atts['link'] == 'product_sync') {
                        // no shop widget on mobile -- just link to PDP
                        $link = 'http://'.$projectConfig['mk_domain'] .'/R-'. strtoupper($atts['p_id']);
                        $blank = 'target="_blank"';
                    }

                    $output .= "        <a href='{$link}' class='swBtn ".$this->class_by_arguments('color, position' , $atts, true)."' {$blank} {$style} title='{$atts['label']}'>";

                    if ($atts['parent_type'] == "grid-element-shop-now") {
                        $output .= "            <span class='grid-element-shop-now-link cta' ><a href=". $link .">".$atts['label']." ></a><span class='icon-arrow-right'></span></span>";

                    } else {
                        $output .= "            <span class='read-more cta' >".$atts['label']."<span class='icon-arrow-right'></span></span>";
                    }

                    $output .= "        </a>";
                }
            }
            //close content-card
            $output.= "     </div>";
            //close content-card-container
            $output.= "     </div>";


            //close wrapper
            $output.= " </div>";

            return do_shortcode($output);

        }
    }
}
