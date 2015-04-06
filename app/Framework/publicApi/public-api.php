<?php
/**
 * public json api
 * version 1.0
 **/

$dir = FRAMEWORK_DIR . '/publicApi';
@include_once "$dir/singletons/api.php";
@include_once "$dir/singletons/query.php";
@include_once "$dir/singletons/introspector.php";
@include_once "$dir/singletons/response.php";
@include_once "$dir/models/post.php";
@include_once "$dir/models/comment.php";
@include_once "$dir/models/category.php";
@include_once "$dir/models/tag.php";
@include_once "$dir/models/author.php";
@include_once "$dir/models/attachment.php";

function public_api_init() {
    global $json_api;

    // php version warning
    // 5.3+ will be needed
    if (phpversion() < 5) {
        add_action('admin_notices', 'public_api_php_version_warning');

        return;
    }

    add_filter('rewrite_rules_array', 'public_api_rewrites');
    $json_api = new JSON_API();
}
function public_api_php_version_warning() {
    // updated fade for wordpress notice action
    echo "<div id='versionWarning' class='updated fade'>Public API requires min PHP version 5.0+</div>";
}
function json_api_activation() {
    // rewrite rule on activation and flush it
    global $wp_rewrite;
    add_filter('rewrite_rules_array', 'public_api_rewrites');
    $wp_rewrite->flush_rules();
}
function json_api_deactivation() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function public_api_rewrites($wp_rules) {
    $base = 'api/v1';
    if (empty($base)) {
        return $wp_rules;
    }
    $json_api_rules = array(
            "$base\$" => 'index.php?json=info',
            "$base/(.+)\$" => 'index.php?json=$matches[1]'
            //"$base/(.+)\/(.+)\$" => 'index.php?json=$matches[1]&$matches[2]'
    );

    return array_merge($json_api_rules, $wp_rules);
}

function json_api_dir() {
    if (defined('API_DIR') && file_exists(API_DIR)) {
        return API_DIR;
    } else {
        return FRAMEWORK_DIR . '/publicApi';
    }
}

// Add initialization and activation hooks
add_action('init', 'public_api_init');
register_activation_hook("$dir/json-api.php", 'json_api_activation');
register_deactivation_hook("$dir/json-api.php", 'json_api_deactivation');

?>
