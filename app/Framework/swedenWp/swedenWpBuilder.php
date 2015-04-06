<?php
/**
* Base class for all template builder class
*
* @author juhonglee
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists( 'swedenBuilder' ) ) {
    class swedenBuilder
    {
        const VERSION = '0.5.0';
        public static $mode = "";
        public static $path = array();
        public static $resources_to_load = array();

        public $paths;
        public $shortcode_class;
        public $tabs;
        public $builderTemplate;

        /**
         * Initializes plugin variables and sets up WordPress hooks/actions.
         *
         * @return void
         */
        public function __construct() {
            $this->paths['coreDir'] = FRAMEWORK_DIR . '/swedenWp/';

            $this->paths['pluginPath'] 	= trailingslashit( dirname( dirname(__FILE__) ) );
            $this->paths['pluginDir'] 	= trailingslashit( basename( $this->paths['pluginPath'] ) );
            $this->paths['pluginUrl'] 	= apply_filters('avia_builder_plugins_url',  plugins_url().'/'.$this->paths['pluginDir']);
            $this->paths['assetsURL']	= FRAMEWORK_URL . '/assets/';
            $this->paths['imagesURL']	= FRAMEWORK_URL . '/assets/images/';
            $this->paths['configPath']	= apply_filters('avia_builder_config_path', ADMIN_DIR .'/templatebuilder-config/');

            swedenBuilder::$path = $this->paths;


            add_action('load-post.php', array(&$this, 'admin_init') , 5 );
            add_action('load-post-new.php', array(&$this, 'admin_init') , 5 );
            add_action('init', array(&$this, 'loadLibraries') , 5 );
            add_action('init', array(&$this, 'init') , 10 );
        }

        /**
         * Load all functions that are needed for both front and backend
         **/
        public function init() {
            if (isset($_GET['avia_mode'])) {
                swedenBuilder::$mode = esc_attr($_GET['avia_mode']);
            }

            $this->createShortcode();
            $this->addActions();
            swedenStoragePost::generate_post_type();

            //hook into the media uploader. we always need to call this for several hooks to be active
            new AviaMedia();

            //on ajax call load the functions that are usually only loaded on new post and edit post screen
            if (swedenWpFunctions::is_ajax()) {
                $this->admin_init();
            }
        }


        /**
         * Load functions that are only needed on add/edit post screen
         **/
        public function admin_init() {
            $this->addAdminFilters();
            $this->addAdminActions();
            $this->loadTextDomain();
            $this->call_classes();
            $this->apply_editor_wrap();
        }

        /**
         * Load all the required library files.
         **/
        public function loadLibraries() {
            require_once( $this->paths['coreDir'].'Classes/pointer.class.php' );
            require_once( $this->paths['coreDir'].'Classes/shortcode-helper.class.php' );
            require_once( $this->paths['coreDir'].'Classes/html-helper.class.php' );
            require_once( $this->paths['coreDir'].'Classes/meta-box.class.php' );
            require_once( $this->paths['coreDir'].'Classes/shortcode-template.class.php' );
            require_once( $this->paths['coreDir'].'Classes/media.class.php' );
            require_once( $this->paths['coreDir'].'Classes/tiny-button.class.php' );
            require_once( $this->paths['coreDir'].'Classes/save-buildertemplate.class.php' );
            require_once( $this->paths['coreDir'].'Classes/storage-post.class.php' );


            //autoload files in shortcodes folder and any other folders that were added by filter
            $folders = apply_filters('load_shortcodes', array(FRAMEWORK_DIR.'/shortcodes/'));
            $this->autoloadLibraries($folders);
        }

        /**
         * auto loads all shortcodes files located in /shortcodes
         * filter can override from framework load file.
         * @param string $paths
         */
        protected function autoloadLibraries($paths) {
            foreach ($paths as $path) {
                foreach (glob($path.'*.php') as $file) {
                    require_once( $file );
                }
            }
        }


        /**
         * Add filters to various wordpress filter hooks
         **/
        protected function addAdminFilters()
        {
            // add_filter('tiny_mce_before_init', array($this, 'tiny_mce_helper')); // remove span tags from tinymce - currently disabled, doesnt seem to be necessary
        }

        /**
         * Add Admin Actions to some wordpress action hooks
         **/
        protected function addAdminActions() {

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_styles' ) );
            add_action( 'admin_print_scripts', array($this,'load_shortcode_assets'), 2000);
            add_action( 'print_media_templates', array($this, 'js_template_editor_elements' )); //create js templates for swedenBuilder Canvas Elements
            add_action( 'avia_save_post_meta_box', array($this, 'meta_box_save' )); //hook into meta box saving and store the status of the template builder and the shortcodes that are used


            //custom ajax actions
            add_action('wp_ajax_avia_ajax_text_to_interface', array($this,'text_to_interface'));
        }


        /**
         * Add Actions for the frontend
         **/
        protected function addActions() {

            // Enable shortcodes in widget areas
            add_filter('widget_text', 'do_shortcode');

            //default wordpress hooking
            add_action('wp_head', array($this,'load_shortcode_assets'), 2000);
            add_action( 'template_redirect',array($this, 'template_redirect' ));
        }

        /**
         * Automatically load assests like fonts into your frontend
         **/
        public function load_shortcode_assets() {
            $output = "";

            foreach (swedenBuilder::$resources_to_load as $element) {
                if ($element['type'] == 'iconfont') {
                    $output .= swedenWpFunctions::load_font($element);
                }
            }

            echo $output;
        }


        /**
         * Load css and js files when in editable mode
         **/
        public function admin_scripts_styles() {
            $ver = swedenBuilder::VERSION;

            #js
            wp_enqueue_script('avia_builder_js', $this->paths['assetsURL'].'js/avia-builder.js', array('jquery','jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-droppable','wp-color-picker'), $ver, TRUE );
            wp_enqueue_script('avia_element_js' , $this->paths['assetsURL'].'js/avia-element-behavior.js' , array('avia_builder_js'), $ver, TRUE );
            wp_enqueue_script('avia_modal_js' , $this->paths['assetsURL'].'js/avia-modal.js' , array('jquery', 'avia_element_js', 'wp-color-picker'), $ver, TRUE );
            wp_enqueue_script('avia_history_js' , $this->paths['assetsURL'].'js/avia-history.js' , array('avia_element_js'), $ver, TRUE );
            wp_enqueue_script('avia_tooltip_js' , $this->paths['assetsURL'].'js/avia-tooltip.js' , array('avia_element_js'), $ver, TRUE );

            #css
            wp_enqueue_style( 'avia-modal-style' , $this->paths['assetsURL'].'css/avia-modal.css');
            wp_enqueue_style( 'avia-builder-style' , $this->paths['assetsURL'].'css/avia-builder.css');
            wp_enqueue_style( 'wp-color-picker' );

            #localize strings for javascript
            include_once($this->paths['configPath']."javascript_strings.php");

            if (!empty($strings)) {
                foreach ($strings as $key => $string) {
                    wp_localize_script( $key, str_replace('_js', '_L10n', $key), $string );
                }
            }
        }


        /**
         * Mulilanguage activation
         **/
        public function loadTextDomain() {
            load_plugin_textdomain( 'swedenWp', false, $this->paths['pluginDir'] . 'lang/');
        }

        /**
         * Mulilanguage activation
         *
         * @param string $status
         */
        public function setMode($status = "") {
            swedenBuilder::$mode = apply_filters('avia_builder_mode', $status);
        }

        /**
         * Calls external classes that are needed for the script to operate
         */
        public function call_classes() {
            //create the meta boxes
            new MetaBoxBuilder($this->paths['configPath']);

            // save button
            $this->builderTemplate = new swedenLayoutSaver($this);

            //activate helper function hooks
            swedenWpFunctions::backend();

            //create tiny mce button
            $tiny = array(
                'id'			 => 'avia_builder_button',
                'title'			 => __('Insert Shortcode','swedenWp' ),
                'image'			 => $this->paths['imagesURL'].'tiny-button.png',
                'js_plugin_file' => $this->paths['assetsURL'].'js/avia-tinymce-buttons.js',
                'shortcodes'	 => array_map(array($this, 'fetch_configs'), $this->shortcode_class)
            );

            new avia_tinyMCE_button($tiny);


            //fetch all Wordpress pointers that help the user to use the builder
            include($this->paths['configPath']."pointers.php");
            $myPointers = new swedenPointer($pointers);
        }

        /**
         * Array mapping helper that returns the config arrays of a shortcode
         *
         * @param array $array
         */
        public function fetch_configs($array) {
            return $array->config;
        }

        /**
         * Automatically load all child classes of the swedenShortcodeTemplate class and create an instance
         *
         **/
        public function createShortcode() {
            $children  = array();
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'swedenShortcodeTemplate')) {
                     $allow = false;
                     $children[] = $class;
                     $this->shortcode_class[$class] = new $class($this);
                     $shortcode = $this->shortcode_class[$class]->config['shortcode'];

                     //check if the shortcode is allowed. if so init the shortcode, otherwise unset the item
                     if( empty(ShortcodeHelper::$manually_allowed_shortcodes) && empty(ShortcodeHelper::$manually_disallowed_shortcodes) ) $allow = true;
                     if( !$allow && !empty(ShortcodeHelper::$manually_allowed_shortcodes) && in_array($shortcode, ShortcodeHelper::$manually_allowed_shortcodes)) $allow = true;
                     if( !$allow && !empty(ShortcodeHelper::$manually_disallowed_shortcodes) && !in_array($shortcode, ShortcodeHelper::$manually_disallowed_shortcodes)) $allow = true;


                     if ($allow) {
                        $this->shortcode_class[$class]->init();
                        $this->shortcode[$this->shortcode_class[$class]->config['shortcode']] = $class;

                        //save shortcode as allowed by default. if we only want to display the shortcode in tinymce remove it from the list but keep the class instance alive
                        if (empty($this->shortcode_class[$class]->config['tinyMCE']['tiny_only'])) {
                            ShortcodeHelper::$allowed_shortcodes[] = $this->shortcode_class[$class]->config['shortcode'];
                        }

                        //save nested shortcodes if they exist
                        if (isset($this->shortcode_class[$class]->config['shortcode_nested'])) {
                            ShortcodeHelper::$nested_shortcodes = array_merge(ShortcodeHelper::$nested_shortcodes, $this->shortcode_class[$class]->config['shortcode_nested']);
                        }
                     } else {
                        unset($this->shortcode_class[$class]);
                     }
                }
            }
        }


        /**
         * Create JS templates for enfold
         **/
        public function js_template_editor_elements() {
            foreach ($this->shortcode_class as $shortcode) {
                $class 	= $shortcode->config['php_class'];
                $template = $this->shortcode_class[$class]->prepare_editor_element();

                if(is_array($template)) continue;

                echo "\n<script type='text/html' id='avia-tmpl-{$class}'>\n";
                echo $template;
                echo "\n</script>\n\n";
            }

        }


        /**
         * Set status of builder (open/closed) and save the shortcodes that are used in the post
         **/
        public function meta_box_save() {
            if (isset($_POST['post_ID'])) {
                //save if the editor is active
                if (isset($_POST['aviaLayoutBuilder_active'])) {
                    update_post_meta((int) $_POST['post_ID'], '_aviaLayoutBuilder_active', $_POST['aviaLayoutBuilder_active']);
                    $_POST['content'] = ShortcodeHelper::clean_up_shortcode($_POST['content']);
                }

                //save the hidden container with unmodified shortcode
                if (isset($_POST['_aviaLayoutBuilderCleanData'])) {
                    update_post_meta((int) $_POST['post_ID'], '_aviaLayoutBuilderCleanData', $_POST['_aviaLayoutBuilderCleanData']);
                }


                //extract all shortcodes from the post array and store them so we know what we are dealing with when the user opens a page.
                //usesfull for special elements that we might need to render outside of the default loop like fullscreen slideshows
                preg_match_all("/".ShortcodeHelper::get_fake_pattern()."/s", $_POST['content'], $matches);

                if (is_array($matches) && !empty($matches[0])) {
                    $matches = ShortcodeHelper::build_shortcode_tree($matches);
                    update_post_meta((int) $_POST['post_ID'], '_avia_builder_shortcode_tree', $matches);
                }
            }
        }

        /**
         * Function that checks if a dynamic template exists and uses that template instead of the default page template
         */
        public function template_redirect() {
            $post_id = @get_the_ID();

            if ($post_id && is_singular()) {
               ShortcodeHelper::$tree = get_post_meta($post_id, '_avia_builder_shortcode_tree', true);

               if ('active' == get_post_meta($post_id, '_aviaLayoutBuilder_active', true) && $template = locate_template('template-builder.php', false)) {
                    global $avia_config;
                    $avia_config['conditionals']['is_builder'] = true;

                    //only redirect if no custom template is set
                    $template_file = get_post_meta($post_id, '_wp_page_template', true);

                    if ("default" == $template_file || empty($template_file)) {
                        $avia_config['conditionals']['is_builder_template'] = true;
                        require_once($template);
                        exit();
                    }
               }
            }
        }

        /**
         * Adding wrapper to default worpdress editor
         *
         * @param void
         * @return NULL
         */
        public function apply_editor_wrap() {
            //fetch the config array
            include($this->paths['configPath']."meta.php");

            $slug = "";
            $pages = array();
            //check to which pages the avia builder is applied
            foreach ($elements as $element) {
                if (is_array($element['type']) && $element['type'][1] == 'visual_editor') {
                    $slug = $element['slug']; break;
                }
            }

            foreach ($boxes as $box) {
                if ($box['id'] == $slug) {
                    $pages = $box['page'];
                }
            }
            global $typenow;

            if (!empty($pages) && in_array($typenow, $pages)) {
                if (isset($_GET['post_type'])&&$_GET['post_type']=="fashion") {
                    add_action( 'edit_form_advanced', array($this, 'wrap_default_editor' ), 100000);
                    add_action( 'edit_form_advanced', array($this, 'close_default_editor_wrap' ), 1);
                } else {
                    //html modification of the admin area: wrap
                    add_action( 'edit_form_after_title', array($this, 'wrap_default_editor' ), 100000);
                    add_action( 'edit_form_after_editor', array($this, 'close_default_editor_wrap' ), 1);
                }
            }
        }

        /**
         * Default wordpress editor wrapper for swapping to builder
         *
         * @return string
         */
        public function wrap_default_editor() {
            global $post_ID;

            $visual_label 	= __( 'Advanced Layout Editor', 'swedenWp' );
            $default_label  = __( 'Default Editor', 'swedenWp' );
            $status         = get_post_meta($post_ID, '_aviaLayoutBuilder_active', true);
            $active_builder = $status == "active" ? $default_label : $visual_label;
            $editor_class   = $status == "active" ? "class='avia-hidden-editor'" : "";
            $button_class   = $status == "active" ? "avia-builder-active" : "";

            echo "<div id='postdivrich_wrap' {$editor_class}>";
            echo '<a id="avia-builder-button" href="#" class="avia-builder-button button-primary '.$button_class.'" data-active-button="'.$default_label.'" data-inactive-button="'.$visual_label.'">'.$active_builder.'</a>';
        }

        /**
         * Default editor closer
         */
        public function close_default_editor_wrap() {
           echo "</div>";
        }

        /**
         * Function called by the metabox class that creates the interface in wordpress backend
         *
         * @param bool $element
         * @return string
         */
        public function visual_editor($element) {
            $output = "";
            $title  = "";
            $i = 0;

            $this->shortcode_buttons = apply_filters('avia_show_shortcode_button', array());


            if (!empty($this->shortcode_buttons)) {
                $this->tabs = isset($element['tab_order']) ? array_flip($element['tab_order']) : array();
                foreach($this->tabs as &$empty_tabs) $empty_tabs = array();


                foreach ($this->shortcode_buttons as $shortcode) {
                    if (empty($shortcode['tinyMCE']['tiny_only'])) {
                        if(!isset($shortcode['tab'])) $shortcode['tab'] = __("Custom Elements",'swedenWp' );

                        $this->tabs[$shortcode['tab']][] = $shortcode;
                    }
                }

                foreach ($this->tabs as $key => $tab) {
                    if(empty($tab)) continue;

                    usort($tab,array($this, 'sortByOrder'));

                    $i ++;
                    $title .= "<a href='#avia-tab-$i'>".$key."</a>";

                    $output .= "<div class='avia-tab avia-tab-$i'>";

                    foreach ($tab as $shortcode) {
                        $output .= $this->create_shortcode_button($shortcode);
                    }

                    $output .= "</div>";
                }
            }

            global $post_ID;
            $active_builder  = get_post_meta($post_ID, '_aviaLayoutBuilder_active', true);
            $extra = swedenBuilder::$mode != true ? "" : swedenBuilder::$mode;
            $hotekey_info = htmlentities($element['desc'], ENT_QUOTES, get_bloginfo( 'charset' ));

            $output  = '<div class="shortcode_button_wrap avia-tab-container"><div class="avia-tab-title-container">'.$title.'</div>'.$output.'</div>';
            $output .= '<input type="hidden" value="'.$active_builder.'" name="aviaLayoutBuilder_active" id="aviaLayoutBuilder_active" />';
            $output .= '<a href="#info" class="avia-hotkey-info" data-avia-help-tooltip="'.$hotekey_info.'">'.__('Information', 'swedenWp' ).'</a>';



            $output .= $this->builderTemplate->create_save_button();
            $output .= "<div class='layout-builder-wrap  {$extra}'>";
            $output .= "	<div class='avia-controll-bar'><span id='layoutType'></span></div>";
            $output .= "	<div id='aviaLayoutBuilder' class='avia-style avia_layout_builder avia_connect_sort preloading av_drop' data-dragdrop-level='0'>";
            $output .= "	</div>";
            $output .= "	<textarea id='_aviaLayoutBuilderCleanData' name='_aviaLayoutBuilderCleanData'>".get_post_meta($post_ID, '_aviaLayoutBuilderCleanData', true)."</textarea>";
            $output .= "</div>";

            return $output;
        }

        /**
         * Create a shortcode button
         *
         * @param string $shortcode
         * @return string
         */
        protected function create_shortcode_button($shortcode) {
            $icon   = isset($shortcode['icon']) ? '<img src="'.$shortcode['icon'].'" alt="'.$shortcode['name'].'" />' : "";
            $data   = !empty($shortcode['tooltip']) ? " data-avia-tooltip='".$shortcode['tooltip']."' " : "";
            $data  .= !empty($shortcode['drag-level']) ? " data-dragdrop-level='".$shortcode['drag-level']."' " : "";
            $class  = isset($shortcode['class']) ? $shortcode['class'] : "";
            $class .= !empty($shortcode['target']) ? " ".$shortcode['target'] : "";

            $link   = "";
            $link  .= "<a {$data} href='#".$shortcode['php_class']."' class='shortcode_insert_button ".$class."' >".$icon.'<span>'.$shortcode['name']."</span></a>";

            return $link;
        }


        /**
         * Helper function to sort the shortcode buttons
         *
         * @param int $a
         * @param int $b
         * @return boolean
         */
        protected function sortByOrder($a, $b) {
            if(empty($a['order'])) $a['order'] = 10;
            if(empty($b['order'])) $b['order'] = 10;

            return $b['order'] <= $a['order'];
        }

        /**
         * Get text input and display it to admin panel
         *
         * @param string $text
         * @return string
         */
        public function text_to_interface($text = NULL) {
            global $shortcode_tags;

            $allowed = false;

            if(isset($_POST['text'])) $text = $_POST['text']; //isset when avia_ajax_text_to_interface is executed (avia_builder.js)
            if(isset($_POST['params']) && isset($_POST['params']['allowed'])) $allowed = explode(',',$_POST['params']['allowed']); //only build pattern with a subset of shortcodes


            //build the shortcode pattern to check if the text that we want to check uses any of the builder shortcodes
            ShortcodeHelper::build_pattern($allowed);
            $text_nodes = preg_split("/".ShortcodeHelper::$pattern."/s", $text);


            foreach ($text_nodes as $node) {
                if ( strlen( trim( $node ) ) == 0 || strlen( trim( strip_tags($node) ) ) == 0) {
                   //$text = preg_replace("/(".preg_quote($node, '/')."(?!\[\/))/", '', $text);
                } else {
                   $text = preg_replace("/(".preg_quote($node, '/')."(?!\[\/))/", '[av_textblock]$1[/av_textblock]', $text);
                }
            }

            $text = $this->do_shortcode_backend($text);

            if (isset($_POST['text'])) {
                echo $text;
                exit();
            } else {
                return $text;
            }
        }

        /**
         * Helper fucntion hook for enfold
         *
         * @param string $text
         */
        public function do_shortcode_backend($text) {
            return preg_replace_callback( "/".ShortcodeHelper::$pattern."/s", array($this, 'do_shortcode_tag'), $text );
        }

        /**
         * Shortcode handler
         *
         * @param array $m
         * @return first value from array
         */
        public function do_shortcode_tag($m) {
            global $shortcode_tags;

            // allow [[foo]] syntax for escaping a tag
            if ($m[1] == '[' && $m[6] == ']') {
                    return substr($m[0], 1, -1);
            }

            //check for enclosing tag or self closing
            $values['closing'] 	= strpos($m[0], '[/'.$m[2].']');
            $values['content'] 	= $values['closing'] !== false ? $m[5] : NULL;
            $values['tag']		= $m[2];
            $values['attr']		= shortcode_parse_atts( stripslashes($m[3]) );

            if (is_array($values['attr'])) {
                $charset = get_bloginfo( 'charset' );
                foreach ($values['attr'] as &$attr) {
                    $attr =	htmlentities($attr, ENT_QUOTES, $charset);
                }
            }

            if (isset($_POST['params']['extract'])) {
                //if we open a modal window also check for nested shortcodes
                if($values['content']) $values['content'] = $this->do_shortcode_backend($values['content']);

                $_POST['extracted_shortcode'][] = $values;

                return $m[0];
            }

            if (in_array($values['tag'], ShortcodeHelper::$allowed_shortcodes)) {
                return $this->shortcode_class[$this->shortcode[$values['tag']]]->prepare_editor_element( $values['content'], $values['attr'] );
            } else {
                return $m[0];
            }
        }

        /**
         * this helper function tells the tiny_mce_editor to remove any span tags that dont have a classname (list insert on ajax tinymce tend do add them)
         * see more: http://martinsikora.com/how-to-make-tinymce-to-output-clean-html
         *
         * @param string $mceInit
         * @return string
         */
        public function tiny_mce_helper($mceInit) {
            $mceInit['extended_valid_elements'] = empty($mceInit['extended_valid_elements']) ? "" : $mceInit['extended_valid_elements'] .",";
            $mceInit['extended_valid_elements'] = "span[!class],figure,figcaption";

            return $mceInit;
        }
    } // end class
} // end if !class_exists


/**
 *
 * Shortcode carousel helper class
 */
if ( !class_exists( 'swedendSliderHelper' ) ) {
    class swedendSliderHelper
    {
        static  $slider = 0; //slider count for the current page
        protected $config; //base config set on initialization

        function __construct($config) {
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


        public function html() {
            $output = "";
            $counter = 0;
            swedendSliderHelper::$slider++;
            if(empty($this->config['content'])) return $output;

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();

            extract($this->config);

            $extraClass 		= 'first';
            $grid 				= 'one_third';
            $slide_loop_count 	= 1;
            $loop_counter		= 1;
            $total				= $columns % 2 ? "odd" : "even";
            $heading 			= !empty($this->config['heading']) ? '<h3>'.$this->config['heading'].'</h3>' : "&nbsp;";
            $slide_count = count($content);

            switch ($columns) {
                case "1": $grid = 'av_fullwidth'; break;
                case "2": $grid = 'av_one_half'; break;
                case "3": $grid = 'av_one_third'; break;
                case "4": $grid = 'av_one_fourth'; break;
                case "5": $grid = 'av_one_fifth'; break;
                case "6": $grid = 'av_one_sixth'; break;
            }

            $data = swedenWpFunctions::create_data_string(array('autoplay'=>$autoplay, 'interval'=>$interval, 'animation' => $animation, 'show_slide_delay'=>30));

            $thumb_fallback = "";
            $output .= "<div {$data} class='avia-content-slider-element-container avia-content-slider-element-{$type} avia-content-slider avia-smallarrow-slider avia-content-{$type}-active avia-content-slider".swedendSliderHelper::$slider." avia-content-slider-{$total} {$class}' >";

            $output .= "<div class='avia-smallarrow-slider-heading'>";
            $output .= "<div class='new-special-heading'>".$heading."</div>";



            if ($slide_count > $columns && $type == 'slider' && $navigation != 'no') {
                if($navigation == 'dots') $output .= $this->slide_navigation_dots();
                if($navigation == 'arrows') $output .= $this->slide_navigation_arrows();
            }
            $output .= "</div>";


            $output .= "<div class='avia-content-slider-inner'>";

            foreach ($content as $key => $value) {
                extract($value['attr']);

                $link = swedenWpFunctions::get_url($link);
                $link_target = !empty($link_target) ? " target='_blank' " : "";

                $parity			= $loop_counter % 2 ? 'odd' : 'even';
                $last       	= $slide_count == $slide_loop_count ? " post-entry-last " : "";
                $post_class 	= "post-entry slide-entry-overview slide-loop-{$slide_loop_count} slide-parity-{$parity} {$last}";

                if($loop_counter == 1) $output .= "<div class='slide-entry-wrap'>";

                $output .= "<div class='slide-entry flex_column {$post_class} {$grid} {$extraClass}'>";
                $output .= !empty($title) ? "<h3 class='slide-entry-title entry-title'>" : '';
                $output .= (!empty($link) && !empty($title)) ? "<a href='{$link}' $link_target title='".esc_attr($title)."'>".$title."</a>" : $title;
                $output .= !empty($title) ? '</h3>' : '';
                $output .= !empty($value['content']) ? "<div class='slide-entry-excerpt entry-content'>".ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($value['content']))."</div>" : "";
                $output .= "</div>";

                $loop_counter ++;
                $slide_loop_count ++;
                $extraClass = "";

                if ($loop_counter > $columns) {
                    $loop_counter = 1;
                    $extraClass = 'first';
                }

                if ($loop_counter == 1 || !empty($last)) {
                    $output .="</div>";
                }
            }

            $output .= "</div>";

            $output .= "</div>";

            return $output;
        }


        protected function slide_navigation_arrows() {
            $html  = "";
            $html .= "<div class='avia-slideshow-arrows avia-slideshow-controls'>";
            $html .= 	"<a href='#prev' class='prev-slide' >".__('Previous','swedenWp' )."</a>";
            $html .= 	"<a href='#next' class='next-slide' >".__('Next','swedenWp' )."</a>";
            $html .= "</div>";

            return $html;
        }


        protected function slide_navigation_dots() {
            $html   = "";
            $html  .= "<div class='avia-slideshow-dots avia-slideshow-controls'>";
            $active = "active";

            $entry_count = count($this->config['content']);
            $slidenumber = $entry_count / (int) $this->config['columns'];
            $slidenumber = $entry_count % (int) $this->config['columns'] ? ((int) $slidenumber + 1) : (int) $slidenumber;

            for ($i = 1; $i <= $slidenumber; $i++) {
                $html .= "<a href='#{$i}' class='goto-slide {$active}' >{$i}</a>";
                $active = "";
            }

            $html .= "</div>";

            return $html;
        }
    }
}

