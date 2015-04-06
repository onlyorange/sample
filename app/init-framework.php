<?php

ini_set( 'upload_max_size' , '64M' );
ini_set( 'post_max_size', '64M');
ini_set( 'max_execution_time', '300' );
ini_set( 'memory_limit','512M' );

// setup app constants
if(file_exists(dirname(__FILE__) . '/../.dev') and !defined('DEVELOPMENT'))
    define('DEVELOPMENT', true);
if(defined('DEVELOPMENT') and DEVELOPMENT)
    require FRAMEWORK_DIR . '/dev-tools.php';

//define('THEME_DIR'            , get_template_directory());
//define('THEME_URL'            , get_template_directory_uri());
define('THEME_DIR'            , get_template_directory());
define('THEME_URL'            , '/wp-content/themes/destinationkors');
define('THEME_STATIC_DIR'     , THEME_DIR . '/static');
define('THEME_STATIC_URL'     , THEME_URL . '/static');
define('THEME_CSS_DIR'        , THEME_DIR . '/static/css');
define('THEME_CSS_URL'        , THEME_URL . '/static/css');
define('THEME_JS_URL'         , THEME_URL . '/static/js');
define('THEME_IMG_URL'        , THEME_URL . '/static/img');
define('THEME_FONTS_DIR'      , THEME_DIR . '/static/fonts');
define('THEME_FONTS_URL'      , THEME_URL . '/static/fonts');

define('THEME_APP_DIR'        , THEME_DIR . '/app/');
define('THEME_APP_URL'        , THEME_URL . '/app/');

define('THEME_TRENDS_URL'     , THEME_URL . '/static/dist/json/trend.json');

define('THEME_STYLESHEET_URL' , get_bloginfo('stylesheet_url'));
define('THEME_STYLESHEET_FILE', THEME_DIR . '/style.css');

define('FRAMEWORK_DIR'        , THEME_DIR . '/app/Framework');
define('FRAMEWORK_URL'        , THEME_URL . '/app/Framework');
define('ADMIN_DIR'            , THEME_DIR . '/app/Admin');
define("ADMIN_URL"            , THEME_URL . '/app/Admin');



//------------------------------------------------------------------
// require project configuration class
//------------------------------------------------------------------
require_once('class/ProjectConfig.php');

$projectConfig = ProjectConfig::get(false);


//------------------------------------------------------------------
// setup raven logging
//------------------------------------------------------------------
// this eats up avg. 2 seconds of loading time.
//require_once('class/Logging.php');
//Logging::setupRavenLogging($projectConfig);



define("CACHE_DIR", '/tmp');
// TODO: Kill all less compiler :(
define("CACHE_URL", get_stylesheet_directory_uri() . '/cache');

define("TEMPLATES_DIR", get_stylesheet_directory() . '/templates');

// let's see I am going to really need this...
define("API_DIR", FRAMEWORK_DIR . '/publicApi');

define('ENV', 'dev');
define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR |
E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
//Custom error handling vars
define('DISPLAY_ERRORS', $projectConfig['debug']);
define('ERROR_REPORTING', E_ALL | E_STRICT);
define('LOG_ERRORS', $projectConfig['debug']);

// this 'wp_debug' needs be fired when wp init. not when framework init
//define('WP_DEBUG', $projectConfig['debug']);

if (is_admin() && isset($_GET['error'])) {
    register_shutdown_function('shut');
    set_error_handler('errorHandler');
}
// not catching the fatal core errors but covered pretty much everything
// Function to catch no user error handler function errors...
function shut()
{

    $error = error_get_last();

    if ($error && ($error['type'] & E_FATAL)) {
        errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }


}

function errorHandler($errno, $errstr, $errfile, $errline) {

    switch ($errno) {

        case E_ERROR: // 1 //
            $typestr = 'E_ERROR'; break;
        case E_WARNING: // 2 //
            $typestr = 'E_WARNING'; break;
        case E_PARSE: // 4 //
            $typestr = 'E_PARSE'; break;
        case E_NOTICE: // 8 //
            $typestr = 'E_NOTICE'; break;
        case E_CORE_ERROR: // 16 //
            $typestr = 'E_CORE_ERROR'; break;
        case E_CORE_WARNING: // 32 //
            $typestr = 'E_CORE_WARNING'; break;
        case E_COMPILE_ERROR: // 64 //
            $typestr = 'E_COMPILE_ERROR'; break;
        case E_CORE_WARNING: // 128 //
            $typestr = 'E_COMPILE_WARNING'; break;
        case E_USER_ERROR: // 256 //
            $typestr = 'E_USER_ERROR'; break;
        case E_USER_WARNING: // 512 //
            $typestr = 'E_USER_WARNING'; break;
        case E_USER_NOTICE: // 1024 //
            $typestr = 'E_USER_NOTICE'; break;
        case E_STRICT: // 2048 //
            $typestr = 'E_STRICT'; break;
        case E_RECOVERABLE_ERROR: // 4096 //
            $typestr = 'E_RECOVERABLE_ERROR'; break;
        case E_DEPRECATED: // 8192 //
            $typestr = 'E_DEPRECATED'; break;
        case E_USER_DEPRECATED: // 16384 //
            $typestr = 'E_USER_DEPRECATED'; break;

    }

    ob_start();
    $message = '<table border="2"><tr><th>Error Type</th><th>Error String</th><th>Error File</th><th>Error Line</th></tr>';
    $message .= '<tr><td>'.$typestr.'</td><td>'.$errstr.'</td><td>'.$errfile.'</td><td>'.$errline.'</td></tr>';
    $message .= '</table>';
    ob_flush();


    if (($errno & E_FATAL) && ENV === 'production') {

        header('Location: 500.html');
        header('Status: 500 Internal Server Error');

    }

    if(!($errno & ERROR_REPORTING))

        return;

    if (DISPLAY_ERRORS) {
        echo $message;
    }

    //Logging error on php file error log...
    if(LOG_ERRORS)
        error_log(strip_tags($message), 0);

}


$showAdmin = array('dashboard' => 'enabled', 'backup' => 'enabled', 'skins' => 'enabled', 'branding' => 'disable', 'website_settings' => 'enabled', 'ait_news_notifications' => 'enabled', 'wysiwyg' => 'enabled');

// admin config...optional config file. so file checking first.
if (file_exists(THEME_DIR."/config.php")) {
    $showAdmin = array_merge($showAdmin, parse_ini_file(THEME_DIR."/config.php"));
}

// adding xml importer
// require FRAMEWORK_DIR . '/Libs/Import/importer.php';

// adding mobile detect script
require FRAMEWORK_DIR . '/Libs/Mobile_Detect.php';

// adding nette framework..only 550K total
$nette = FRAMEWORK_DIR . '/Libs/Nette/nette.min.';
if(file_exists($nette.'inc'))
    require_once $nette.'inc';
else
    require_once $nette.'php';

unset($nette);

require_once FRAMEWORK_DIR . '/api.php';
require FRAMEWORK_DIR . '/load.php';

// add multiple post thumbnail
require_once ADMIN_DIR . '/multiple_post_thumbs.php';


/*
$bloginfo = wp_get_sites();
$blogLang = get_bloginfo('language');
$siteLang = get_site_option('WPLANG');
*/
// initialize/set locale
//add_filter( 'locale', 'setWpLocale' );
function setWpLocale($lang) {
	$localLocale = $_COOKIE['dk_locale'];
	$currentSite = get_current_blog_id();
	$siteDetail = get_blog_details($currentSite);

	if ($localLocale == 'en_CA' || $currentSite == '3') {
		$val = 'en_CA';
		header( 'Location: '. $siteDetail->siteurl);
		setcookie("dk_locale", $val, time()+3600*24);
		setlocale(LC_MONETARY, $val);

		return $val;
	} else if($localLocale == 'fr_CA' || $currentSite == '2') {
		$val = 'fr_CA';
		header( 'Location: '. $siteDetail->siteurl);
		setcookie("dk_locale", $val, time()+3600*24);
		setlocale(LC_MONETARY, $val);

		return $val;
	} else {
		setcookie("dk_locale", $lang, time()+3600*24);
		setlocale(LC_MONETARY, $lang);
		return $lang;
	}
	
}

if (!is_admin()) {

    swedenWp::$cacheDir = realpath(CACHE_DIR);
    swedenWp::$templatesDir = realpath(TEMPLATES_DIR);

    $mobileDetail = (object) array("isMobile"=>swedenWpSiteEntity::getInstance()->isMobile,
                         "isTablet"=>swedenWpSiteEntity::getInstance()->isTablet,
                         "isPhone"=>swedenWpSiteEntity::getInstance()->isPhone);

    // global and allways accessible template variables
    $swedenUnlimited = array(
        // url shortcuts
        'themeUrl' => THEME_URL,
        'themeCssUrl' => THEME_CSS_URL,
        'themeJsUrl' => THEME_JS_URL,
        'themeImgUrl' => THEME_IMG_URL,
        'themeFontsUrl' => THEME_FONTS_URL,
        'styleCssUrl' => THEME_STYLESHEET_URL,
        'homeUrl' =>  home_url('/'),
        // theme controller ready! :P
        // 'themeOptions' => $swThemeOptions,
        'bodyClasses' => '',
        'client' => $mobileDetail,
        'projectConfig' => $projectConfig
    );

} else {
    require ADMIN_DIR . '/load.php';
	add_action('admin_enqueue_scripts', 'adminInlineDKVar');
    // plugin controller...
    $requiredPlugins = array();
    //add_action('tgmpa_register', 'registerPlugins');
}
function adminInlineDKVar() {
	global $projectConfig;
	echo '<script type="text/javascript">
	/* <![CDATA[ */
	var mkDomain = "' . $projectConfig['mk_domain_prefix'] . get_blog_option(get_current_blog_id(), 'mkDomain') . '";
	/* ]]> */
</script>';
}

// Change http request time to correct load docs
function wp_change_request_timeout($time) {
    return 10; //new number of seconds
}
add_filter('http_request_timeout', 'wp_change_request_timeout');

// Clean up unnecessary wp default functions
function clean_wp_header() {
    remove_action( 'wp_head', 'feed_links_extra', 3 );
    remove_action( 'wp_head', 'feed_links', 2 );
    wp_deregister_script('comment-reply');
}
add_action( 'init', 'clean_wp_header' ); // after_setup_theme can replace init

// Disable pingbacks
function remove_xmlrpc_pingback_ping($methods) {
	unset($methods['pingback.ping']);
	return $methods;
}
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );

// create feed template
// overriding builtin rss2 template
//add_action( $hook, $function_to_add, $priority, $accepted_args );
function feed_request($que) {
    if (isset($que['feed']) && !isset($que['post_type']))
        $que['post_type'] = array('post', 'page', 'mks-edit', 'jet', 'fashion', 'kors-cares');

    return $que;
}
remove_all_actions( 'do_feed_rss2' );
add_action( 'do_feed_rss2', 'all_data_feed', 10, 1 );

function all_data_feed($exceptions) {
    $rss_template = get_template_directory() . '/templates/feed-template.php';

    if (file_exists($rss_template)) { //get_query_var('post_type' ) == ''
        add_filter('request', 'feed_request');
        load_template($rss_template);
    } else {
        do_feed_rss2($exceptions);
    }
}

// get production data from stage.

function rewrite_static_tags() {
	add_rewrite_tag('%proxy%', '([^&]+)');
  add_rewrite_tag('%manual%', '([^&]+)');
  add_rewrite_tag('%manual_file%', '([^&]+)');
  add_rewrite_tag('%file_name%', '([^&]+)');
  add_rewrite_tag('%file_dir%', '([^&]+)');
}
add_action('init', 'rewrite_static_tags', 10, 0);
add_action("template_redirect", 'static_query_redirect');


// Template selection
function static_query_redirect() {
	global $wp;
	global $wp_query;

	if ($wp->query_vars["proxy"] == "yes") {
		// Let's look for the property.php template file in the current theme
		include(THEME_DIR . '/proxy.php');
		die();
	} elseif ($wp->query_vars["manual_file"] == "yes"){

      if (is_user_logged_in()){
        $filepath = THEME_DIR . "/manual/" . $wp->query_vars["file_dir"] . "/" . $wp->query_vars["file_name"];
        $mime_type = ($wp->query_vars["file_dir"] === 'images') ? "Content-Type: image/jpeg" : "Content-Type: text/css";
        header($mime_type);
        readfile($filepath);
        die();
      } else{
        $wp_query->is_404 = true;
        die();
      }
      die();
  } elseif ($wp->query_vars["manual"] == "yes") {
    if (is_user_logged_in()){
        readfile(THEME_DIR . '/manual/index.html');
        die();
    } else {
      $wp_query->is_404 = true;
    }
    die();
  }
}
