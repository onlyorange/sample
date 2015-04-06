<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'sw_sc_video' ) )
{
    class sw_sc_video extends swedenShortcodeTemplate
    {
            /**
             * Create the config array for the shortcode button
             */
            function shortcode_insert_button()
            {
                $this->config['name']			= __('Video', 'swedenWp' );
                $this->config['tab']			= __('Content Elements', 'swedenWp' );
                $this->config['icon']			= swedenBuilder::$path['imagesURL']."sc-video.png";
                $this->config['order']			= 21;
                $this->config['target']			= 'avia-target-insert';
                $this->config['shortcode'] 		= 'sw_video';
                $this->config['modal_data']     = array('modal_class' => 'mediumscreen');
                $this->config['tooltip']        = __('Display a video', 'swedenWp' );
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
                            "name"  => __("Title", 'swedenWp' ),
                            "desc"  => __("Text to appear on top of video", 'swedenWp' ),
                            "id"    => "title",
                            "type"  => "input",
                            "std"   => ""
                        ),
                    array(  "name"  => __("Choose Cover Image", 'swedenWp' ),
                            "desc"  => __("Cover image is displayed while video is not playing", 'swedenWp' ),
                            "id"    => "imgsrc",
                            "type"  => "image",
                            "title" => __("Insert Image",'swedenWp' ),
                            "button" => __("Insert",'swedenWp' ),
                            "std"   => ""
                        ),
                    array(
                            "name"  => __("Play Button Color", 'swedenWp' ),
                            "desc"  => __("Choose color of play button", 'swedenWp' ),
                            "id"    => "btn_color",
                            "type"  => "select",
                            "std"   => "White",
                            "subtype" => array(
                                                __('White',  'swedenWp' ) =>'is-white',
                                                __('Black', 'swedenWp' ) =>'is-black',
                                                )
                            ),
                    array(
                            "name" 	=> __("Video Provider", 'swedenWp' ),
                            "desc" 	=> __("Videos must be served by Scene7 or YouTube", 'swedenWp' ),
                            "id" 	=> "video_type",
                            "type" 	=> "select",
                            "std" 	=> "scene7",
                            "subtype" => array(
                                                __('Scene7',  'swedenWp' ) =>'scene7',
                                                __('YouTube',  'swedenWp' ) =>'youtube'
                                                )
                            ),
                    array(
                            "name"  => __("Video Auto-Play & Loop", 'swedenWp' ),
                            "desc"  => __("Select \"Yes\" to automatically play and loop video (looped videos will not display a cover image, and will be muted by default)", 'swedenWp' ),
                            "id"    => "video_loop",
                            "type"  => "select",
                            "std"   => "false",
                            "subtype" => array(
                                                __('Yes',  'swedenWp' ) =>'true',
                                                __('No',  'swedenWp' ) =>'false'
                                                )
                            ),
                    array(
                            "name"  => __("Video ID", 'swedenWp' ),
                            "desc"  => __("YouTube or Scene7 video ID", 'swedenWp' ),
                            "id"    => "video_id",
                            "type"  => "input",
                            "std"   => ""
                        ),
                    array(
                            "name" 	=> __("Video Format", 'swedenWp' ),
                            "desc" 	=> __("Choose video aspect ratio (16:9 is standard widescreen, 4:3 is legacy television ratio)", 'swedenWp' ),
                            "id" 	=> "format",
                            "type" 	=> "select",
                            "std" 	=> "16:9",
                            "subtype" => array(
                                                __('16:9',  'swedenWp' ) =>'16-9',
                                                __('4:3', 'swedenWp' ) =>'4-3',
                                                __('Custom Ratio', 'swedenWp' ) =>'custom',
                                                )
                            ),

                    array(
                            "name" 	=> __("Relative Video Width", 'swedenWp' ),
                            "desc" 	=> __("Enter the width of the video (relative to its height)", 'swedenWp' ),
                            "id" 	=> "width",
                            "type" 	=> "input",
                            "std" 	=> "16",
                            "required" => array('format','equals','custom')
                        ),

                    array(
                            "name" 	=> __("Relative Video Height", 'swedenWp' ),
                            "desc" 	=> __("Enter the height of the video (relative to its width)", 'swedenWp' ),
                            "id" 	=> "height",
                            "type" 	=> "input",
                            "std" 	=> "9",
                            "required" => array('format','equals','custom')
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
                $template = $this->update_template("imgsrc", "URL: {{imgsrc}}");

                $params['content'] = NULL;
                $params['innerHtml'] = "<img src='".$this->config['icon']."' title='".$this->config['name']."' />";
                $params['innerHtml'].= "<div class='avia-element-label'>".$this->config['name']."</div>";
                $params['innerHtml'].= "<div class='avia-element-url' {$template}> URL: ".$params['args']['imgsrc']."</div>";
                $params['class'] = "avia-video-element";

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

                extract(shortcode_atts(array('video_type'=>'','btn_color' => 'is-white', 'video_loop'=> '', 'imgsrc'=>'', 'attachment'=>'' ,'video_id'=>'','video_src' => '', 'autoplay' => '', 'format' => '', 'height'=>'9', 'width'=>'16', 'title'=>'', 'fallback_image'=>''), $atts));
                $custom_class = !empty($meta['custom_class']) ? $meta['custom_class'] : "";
                $style = "";
                $html  = "";
                $playerid = $video_id;

                if ($format == 'custom') {
                    $height = intval($height);
                    $width  = intval($width);
                    $ratio  = (100 / $width) * $height;
                    $style = "style='padding-bottom:{$ratio}%;'";
                }

                $video_output = MKD_Video::displayVideo($video_id, $video_type, $video_loop);

                $anchor = str_replace(" ", "_", $title);

                if ($video_loop == 'true') $isLoopingClass = 'is-looping';
                else $isLoopingClass = 'is-not-looping';
                if($swedenUnlimited['client']->isPhone){
                	$imgsize = ($swedenUnlimited['client']->isPhone) ? 'large-thumbnail': '';
                	$vidCover = wp_get_attachment_image_src($attachment, $imgsize)[0];
                } else {
                	$vidCover = wp_get_attachment_image($attachment, '');
                }

                $output = ($swedenUnlimited['client']->isPhone) ? "<div class=\"video-title\"><div class=\"runway-headline\">$title</div></div>" : "";
                $output .= <<<VIDEO_BEGIN
                    <div class="grid-element">
                        <a id="$anchor" data-title="$title"></a>
                        <div $style class="avia-video $html $custom_class type-$video_type">
VIDEO_BEGIN;
                if($swedenUnlimited['client']->isPhone){
                $output .= <<<VIDEO_IMAGE
                            <div class="video-cover-image $isLoopingClass" style="background-image: url($vidCover);">
VIDEO_IMAGE;
                }else{
                    $output .= <<<VIDEO_IMAGE
                             <div class="video-cover-image $isLoopingClass">$vidCover
VIDEO_IMAGE;
                }

                    // if (empty($atts['btn_color'])) $btn_color = 'is-white';
                    // else $btn_color = $atts['btn_color'];

                    $output .= <<<VIDEO_END
                        </div>
                            <div class="video-overlay">
                                <div class="video-title">
                                    <div class="title-container">
                                        <div class="runway-headline">$title</div>
                                        <div class="video-play-button"><span class="icon $btn_color"></span></div>
                                    </div>
                                </div>
                            </div>
                            $video_output
                        </div>
                    </div>
VIDEO_END;

                return $output;
            }

    }
}
