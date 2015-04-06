<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'Parallax' ) )
{

    class Parallax extends swedenShortcodeTemplate
    {

            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']           = __('Parallax', 'swedenWp' );
                $this->config['tab']            = __('Content Elements', 'swedenWp' );
                $this->config['icon']           = swedenBuilder::$path['imagesURL']."sc-image.png";
                $this->config['order']          = 13;
                $this->config['target']         = 'avia-target-insert';
                $this->config['shortcode']      = 'sw_content_parallax_box';
                $this->config['modal_data']     = array('modal_class' => 'bigscreen');
                $this->config['tooltip']        = __('Create a full bleed image element with parallax.', 'swedenWp' );
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

                    array(  "name"  => __("Parallax Text", 'swedenWp' ),
                            "desc"  => __("This text appears above the parallax image.", 'swedenWp' ),
                            "id"    => "cover_text",
                            "type"  => "tiny_mce",
                            "std" => ""),

                    array(
                            "name"  => __("Choose Image",'swedenWp' ),
                            "desc"  => __("Upload a new image or choose an existing one from your media library.",'swedenWp' ),
                            "id"    => "image_src",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std"   => ""),

                    array(
                            "name"  => __("Text Alignment", 'swedenWp' ),
                            "desc"  => __("Choose an alignment for your text content.", 'swedenWp' ),
                            "id"    => "align",
                            "type"  => "select",
                            "std"   => "center",
                            "subtype" => array(
                                    __('Center',  'swedenWp' ) =>'center',
                                    __('Right',  'swedenWp' ) =>'right avia-align-right is-right-aligned',
                                    __('Left',  'swedenWp' ) =>'left avia-align-left is-left-aligned')
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
                $template_body = $this->update_template("image_src", "<img src='{{image_src}}' alt=''/>");
                $title    = "";
                $img = "";

                if (!empty($params['args']['cover_text'])) {
                    $title = "<p>" . $params['args']['cover_text']. "</p>";
                } else if (empty($params['args']['cover_text']) && isset($params['args']['src']) && is_numeric($params['args']['src'])) {
                    $title = wp_get_attachment_image($params['args']['src'],'large');
                } else if (!empty($params['args']['cover_text']) && empty($params['args']['title'])) {
                    $title = "<img src='".$params['args']['cover_text']."' alt=''  />";
                } else {

                }
                //var_dump($title);
                if (isset($params['args']['image_src']) && is_numeric($params['args']['image_src'])) {
                    $img = wp_get_attachment_image($params['args']['image_src'],'large');
                } else if (!empty($params['args']['image_src'])) {
                    $img = "<img src='".$params['args']['image_src']."' alt=''  />";
                }
                $params['class'] = "";
                $params['innerHtml']  = "";
                $params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style avia_hidden_bg_box'>";
                $params['innerHtml'] .= "   <div ".$this->class_by_arguments('button' ,$params['args']).">";
                $params['innerHtml'] .= "       <div class='avia-promocontent'>";
                $params['innerHtml'] .= "           <div class='avia_image_container' {$template_title}>{$title}</div>";
                $params['innerHtml'] .= "           <div class='avia_image_container' {$template_body}>{$img}</div>";
                $params['innerHtml'] .= "           <div data-update_with='content' class='element-content'>".stripslashes(wpautop(trim($params['content'])))."</div>";
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
                $output = "";
                $class  = "";
                $alt    = "";
                $title  = "";
                $splitVar = '';

                extract(shortcode_atts(array('cover_text', 'image_src', 'align'), $atts));
                $atts =  shortcode_atts(array(
                                             'cover_text' => "",
                                             'image_src' => "",
                                             'align' => ""
                                             ), $atts);

                $image_src = $atts['image_src'];
                $cover_text = $atts['cover_text'];
                $align_classes = $atts['align'];

                if($swedenUnlimited['client']->isPhone || $swedenUnlimited['client']->isTablet) {

                    // TODO -- make sure this is entirely compatible with mobile.

                    $output = <<<PARALLAX_ELEMENT
                        <div class="">
                            <div class="" >
                                <img src="$image_src" />
                            </div>
                            <div class="$align_classes">
                                $cover_text
                            </div>
                        </div>
PARALLAX_ELEMENT;

                } else {

                    $output = <<<PARALLAX_ELEMENT
                        <div class="parallax parallax--full-bleed" data-requirejs-id="elements/parallax">
                            <div class="parallax-active parallax-image" style="background: url($image_src);" data-stellar-ratio="0.5"></div>
                            <div class="parallax-text $align_classes" data-stellar-ratio="1.5" data-stellar-vertical-offset="0">$cover_text</div>
                        </div>
PARALLAX_ELEMENT;

                }


                return do_shortcode($output);
            }
    }
}
