<?php

// loading libs

// temporary this...custom using nette framework...using latte engine.
// @joe play with this one let me know if you don't like this then I will update it to twig quickly..
require_once FRAMEWORK_DIR . '/swedenWp/swedenWp.php';

// TODO: registration!
//require_once FRAMEWORK_DIR . '/twig.helper.php';

// check if the builder was already included
// plugin type for later...
function templateBuilder_plugedin() {
    if (class_exists( 'swedenBuilder' )) { return true; }

    return false;
}

// override shortcodes folder location
function add_shortcode_folder($paths) {
    $paths = array(dirname(__FILE__) ."/xxx/xxx/shortcodes/");

    return $paths;
}
// let builder class handles the location...
//add_filter('load_shortcodes','add_shortcode_folder');



// override assets folder location
function builder_plugins_url($url) {
    $url = get_template_directory_uri()."/xxx/xxx/xxx/";

    return $url;
}
// builder class handles this
// add_filter('builder_plugins_url','builder_plugins_url');

// checking template builder is included.
// for later, using both plugin type and theme type
if (!templateBuilder_plugedin()) {
    require_once( dirname(__FILE__) . '/swedenWp/swedenWpBuilder.php' );

    $builder = new swedenBuilder();

    //activates the builder safe mode. this hides the shortcodes that are built with the content builder from the default wordpress content editor.
    //can also be set to "debug", to show shortcode content and extra shortcode container field
    //$builder->setMode( 'debug' );
}