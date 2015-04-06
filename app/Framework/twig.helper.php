<?php
require_once dirname(__FILE__) . '/Libs/Twig/Autoloader.php';

function autoload_twig() {
    global $twig;
    Twig_Autoloader::register();
    $macros = new Twig_SimpleFunction('head', function() {
        if(is_singular() && get_option("thread_comments"))
            wp_enqueue_script("comment-reply");
        wp_head();
    });
    $loader = new Twig_Loader_Filesystem( THEME_DIR . '/Templates');
    $twig = new Twig_Environment($loader, array(
            'debug' => TRUE,
            'cache' => THEME_DIR . '/cache',
            'auto_reload' => TRUE
    ));
    $twig->addFunction($macros);
}
// not using twig for now
// add_action('init', 'autoload_twig');
