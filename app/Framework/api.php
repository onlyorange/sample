<?php
/*
add_filter( 'wp_default_scripts', 'dequeue_jquery_migrate' );

function dequeue_jquery_migrate( &$scripts){
	if(!is_admin()){
		$scripts->remove( 'jquery');
	}
}
*/
function includeLess()
{
    $less = FRAMEWORK_DIR . '/Libs/lessphp/lessc';
    if(file_exists($less.'.inc'))
        require_once $less.'.inc';
    else
        require_once $less.'.inc.php';
}

/**
 * CSS from LESS
 * @param string $input, $output, array $options
 */
function lessToCss($input = null, $output = null, $options = null)
{
    includeLess();

    if ($input === null and $output === null and $options === null) {
        $options = get_option('en');
        $input = THEME_DIR . "/style.less.css";
        $output = THEME_STYLESHEET_FILE;
    }

    if ($options === false) { // for theme preview

    }

    $less = new swLess();
    $less->importDir = THEME_DIR . '/';

    $content = file_get_contents($input);

    $onlyDesignVars = true;

    //$configTypes = getOptionsTypes($GLOBALS['themeConfig'], $onlyDesignVars);

    if (isset($customCss['custom-css'])) {
        foreach ($customCss['custom-css'] as $css) {
            $content .= $css['value'];
            unset($options[$css['section']][$css['key']]);
        }
    }

    //$variables = prepareVariablesForLess($options, $configTypes);
    try {
        $css = $less->parse($content, $variables);
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }

    // save also comment header
    preg_match("/\/\*.*?\*\//s", $content, $match);
    $header = $match[0];

    unset($content); // clean up

    $header .= "\n\n/* **\n * AUTO!!MATED!!    *\n *  **/\n\n";

    if(!defined('DEVELOPMENT') or DEVELOPMENT != true)
        $css = preg_replace('~\\s*([:;{},])\\s*~', '\\1', preg_replace('~/\\*.*\\*/~sU', '', $css));


    @chmod($output, 0777);
    $written = @file_put_contents($output, $header . "\n" . $css);
    @chmod($output, 0755);

    if($written === false)

        return false;
    else
        return true;
}



/**
 * Converts structured config array to simple key => value array
 * @param array $options Config array
 * @return array
 */
/*
function prepareVariablesForLess($options = array(), $configTypes = null)
{
    if (empty($options)) {
        $options = get_option(OPTIONS_KEY);
        if($options === false) $options = array();
    }

    if ($configTypes === null) {
        $onlyDesignVars = true;
        $configTypes = getOptionsTypes($GLOBALS['themeConfig'], $onlyDesignVars);
    }

    $variables = array();
    foreach ($options as $section => $values) {
        foreach ($values as $option => $value) {
            if (isset($configTypes[$section][$option])) {
                if ($configTypes[$section][$option] == 'custom-css') {
                    continue;
                }
                if (is_string($value)) {
                    if (empty($value)) {
                        $variables[$option] = '';
                    } else {
                        $variables[$option] = $value;
                    }
                    // url to images must be in quotes
                    if(preg_match('/\.(jpg|png|gif)/', $variables[$option]) !== 0)
                        $variables[$option] = "\"$variables[$option]\"";

                } elseif (is_array($value) and isset($value['font']) and !empty($value['type'])) {
                    $font = str_replace('+', ' ', $value['font']);
                    $pos = strpos($font, ':');

                    if($pos !== false)
                        $font = substr($font, 0, $pos);

                    $variables[$option] = "'" . $font . "'";

                } elseif ($configTypes[$section][$option] == 'transparent' and is_array($value) and isset($value['color'])) {
                    if (startsWith('#', $value['color']) and $value['opacity'] == 1) {
                        $variables[$option] = $value['color'];
                    } else {
                        $rgba = "rgba(%s, %s, %s, %s);";

                        $rgb = hex2rgb($value['color']);

                        $rgba = sprintf($rgba, $rgb[0], $rgb[1], $rgb[2], $value['opacity']);
                        $ieFilter = "progid:DXImageTransform.Microsoft.gradient(startColorstr='#".base_convert(floor($value['opacity']*255),10,16).str_replace('#','',$value['color'])."',endColorstr='#".base_convert(floor($value['opacity']*255),10,16).str_replace('#','',$value['color'])."',GradientType=0)";
            $variables[$option] = $rgba;
            $variables[$option.'-ie'] = $ieFilter;
                    }

                } elseif ($configTypes[$section][$option] == 'custom-css-vars' and is_array($value)) {
                    foreach ($value as $var) {
                        if (isset($var['variable']) and isset($var['value']) and !empty($var['variable']) and !empty($var['value'])) {
                            if(preg_match('/\.(jpg|png|gif)/', $var['value']) !== 0)
                                $var['value'] = "\"$var[value]\"";
                            $variables[$var['variable']] = $var['value'];
                        }
                    }
                }
            }
        }
    }

    return $variables;
}
*/
/**
 * Gets default values from Neon theme config file
 * @param type $config
 * @return type
 */
/*
function getOptionsTypes($config, $onlySkinable = false)
{
    $settings = $config;
    $types = array();
    // $types[<section>][<key>] = <type>

    foreach ($config as $menuKey => $page) {

        if (isset($page['tabs'])) {
            foreach ($page['tabs'] as $tabKey => $tabPage) {
                unset($settings[$menuKey]);
                $settings[$tabKey] = $tabPage;
            }
        }
    }

    $designTypes = array('transparent', 'colorpicker', 'image-url', 'font', 'select', 'radio', 'custom-css', 'custom-css-vars');

    foreach ($settings as $section => $options) {
        foreach ($options['options'] as $key => $value) {
            if (is_string($value) and startsWith('section', $value)) {
                continue;
            }

            if ($onlySkinable) {

                if (in_array($value['type'], $designTypes) and (!isset($value['skinable']) or (isset($value['skinable']) and $value['skinable'] != false))) {
                    if (($value['type'] == 'select' or $value['type'] == 'radio') and (endsWith('X', $key) or endsWith('Y', $key) or endsWith('Repeat', $key) or endsWith('Attach', $key))) {
                        $types[$section][$key] = $value['type'];
                    } else {
                        $types[$section][$key] = $value['type'];
                    }
                }

                if (isset($value['skinable']) and $value['skinable'] == true) {
                    $types[$section][$key] = $value['type'];
                }

            } else {
                $types[$section][$key] = $value['type'];
            }
        }
    }

    return $types;
}
*/


/**
 * Loads and parses json config files
 * @param string $filename // absoluth path
 * @return array
 */
function loadConfig($filename)
{
    $file = realpath($filename);
    if($file === false)

        return false;
    $options = json_decode(file_get_contents($file));

    return $options;
}



/**
 * Converts raw array to object
 * @param array $array
 * @return stdClass|boolean
 */
function arrayToObject($array)
{
    $temp = array();
    $object = new stdClass;
    if (is_array($array) and count($array) > 0) {
        foreach ($array as $name => $value) {
            foreach ($value as $k => $v) {
                if (is_array($v) and $k != 'sectionsOrder') {
                    foreach ($v as $i => $j) {
                        if (is_numeric($i)) { // cloned items
                            $temp[$i] = (object) $j;
                            @$object->$name->$k = $temp;
                        } else {
                            @$object->$name->$k->$i = $j; // checkbox
                        }
                    }
                } else {
                    @$object->$name->$k = $v; // @ - PHP 5.4 compatibility
                }
            }
        }

        return $object;
    } else {
        return false;
    }
}


/**
 * Replaces elements from passed arrays into the first array recursively
 * (PHP 5 >= 5.3.0)
 * @return array|null
 */
if (!function_exists('array_replace_recursive')) {
    function array_replace_recursive()
    {
        $arrays = func_get_args();
        $original = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $original[$key] = array_replace_recursive($original[$key], $array[$key]);
                } else {
                    $original[$key] = $value;
                }
            }
        }

        return $original;
    }
}



/**
 * Starts the $haystack string with the prefix $needle?
 * @param  string
 * @return bool
 */
function startsWith($needle, $haystack)
{
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}

/**
 * Ends the $haystack string with the suffix $needle?
 * @param  string
 * @return bool
 */
function endsWith($needle, $haystack)
{
    return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
}



/**
 * Converts to web safe characters [a-z0-9-] text.
* @param string(in utf-8 encoding)
* @return string
*/
function webalize($text)
{
    $url = $text;
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = @iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);

    return $url;
}


/**
 * @return array
 */
function themeUpdator()
{
    $counts = array(
        'status' => 0,
        'themeUpdate' => 0,
        'total' => 0,
    );
    $updateTitle = '';

    if (isset($GLOBALS['showAdmin']['dashboard']) == false) {
        $GLOBALS['showAdmin']['dashboard'] = "enabled";
    }

    return array(
        'counts' => $counts,
        'title' => $updateTitle,
    );
    // ahhh...will do this later..at this point I am not sure we are going to use theme specific dashboard or not so...

}



/**
 * Convert hex code to rgb for later.
 *
 * @param unknown $color
 * @return boolean|multitype:number
 */
function hex2rgb($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}

/**
 * better API for registering sidebars => widget
 *
 * @param array $areas
 * @param unknown $defaultParams
 */
function registerWidget($areas, $defaultParams = array())
{
    if (empty($defaultParams)) {
        $defaultParams = array(
            'before_widget' => '<div id="%1$s" class="box widget-container %2$s"><div class="box-wrapper">',
            'after_widget' => "</div></div>",
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        );
    }

    foreach ($areas as $id => $area) {
        $params = array_merge($defaultParams, $area, array('id' => $id));
        register_sidebar($params);
    }
}

/**
 * Better API for registering plugins
 * @param  array $plugins
 */
function registerPlugins()
{
    global $requiredPlugins;

    if (!empty($requiredPlugins)) {
        $config = array(
            'domain'           => 'sw',
            'parent_menu_slug' => 'plugins.php',
            'parent_url_slug'  => 'plugins.php',
            'menu'             => 'install-required-plugins',
            'is_automatic'     => true,
            'strings'          => array(
                'menu_title'   => __('Install Required Plugins', 'sw'),
            ),
        );
        // using tgampa plugin still in dev.
        tgmpa($requiredPlugins, $config);
    }
}

/**
 * Helper method to preserve same AIP
 * @param  plugins $plugins
 *
 * https://github.com/thomasgriffin/TGM-Plugin-Activation/blob/master/tgm-plugin-activation/example.php#L46
 * Under dev..
 */
function addPlugins($plugins)
{
    global $requiredPlugins;

    if(is_admin())
        $requiredPlugins = $plugins;
}


/**
 * API for registering and enqueueing stylesheets at sametime
 * @param  array $styles
 */
function addStyles($styles) {
    foreach ($styles as $handler => $style) {
        if (is_bool($style) and $style === true) {
            wp_enqueue_style($handler);
        } elseif (is_array($style)) {

            wp_register_style($handler, $style['file'],  isset($style['deps']) ? $style['deps'] : array(), isset($style['ver']) ? $style['ver'] : false, isset($style['media']) ? $style['media'] : 'all');

            if(!isset($style['enqueue']) or (isset($style['enqueue']) and $style['enqueue'] == true))
                wp_enqueue_style($handler);
        }
    }
}

/**
 * API for registering and enqueueing scripts at sametime
 * @param  array $scripts
 */
function addScripts($scripts) {
    foreach ($scripts as $handler => $script) {
        if (is_bool($script) and $script === true) {
            wp_enqueue_script($handler);
        } elseif (is_array($script)) {
            wp_register_script($handler, $script['file'],  isset($script['deps']) ? $script['deps'] : array(), isset($script['ver']) ? $script['ver'] : false, isset($script['inFooter']) ? $script['inFooter'] : false);

            if(!isset($script['enqueue']) or (isset($script['enqueue']) and $script['enqueue'] == true))
                wp_enqueue_script($handler);

            if(isset($script['localize']) and $script['localize'])
                wp_localize_script($handler, 'sw', $script['localize']);
        }
    }
}


/**
 * Public API
 *
 * TODO: Building structure properly..so can be extendedd easily.
 **/
require_once FRAMEWORK_DIR . '/publicApi/public-api.php';

