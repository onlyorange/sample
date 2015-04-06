<?php
/**
 *
 * @author juhonglee
 *
 */
class JSON_API {

    function __construct() {
        $this->query = new JSON_API_Query();
        $this->introspector = new JSON_API_Introspector();
        $this->response = new JSON_API_Response();
        add_action('template_redirect', array(&$this, 'template_redirect'));
        //add_action('admin_menu', array(&$this, 'admin_menu'));
        
        //flushing rewriting rules...is kinda expensive to do in every updates...
        add_action('update_option_json_api_base', array(&$this, 'flush_rewrite_rules'));
        add_action('pre_update_option_json_api_controllers', array(&$this, 'update_controllers'));
    }

    function template_redirect() {
        // Check to see if there's an appropriate API controller + method
        $controller = strtolower($this->query->get_controller());
        $available_controllers = $this->get_controllers();
        $enabled_controllers = explode(',', get_option('json_api_controllers', 'core'));
        $active_controllers = array_intersect($available_controllers, $enabled_controllers);

        if ($controller) {

            if (empty($this->query->dev)) {
                error_reporting(0);
            }

            if (!in_array($controller, $active_controllers)) {
                $this->error("Unknown controller '$controller'.");
            }

            $controller_path = $this->controller_path($controller);
            if (file_exists($controller_path)) {
                require_once $controller_path;
            }
            $controller_class = $this->controller_class($controller);

            if (!class_exists($controller_class)) {
                $this->error("Unknown controller '$controller_class'.");
            }

            $this->controller = new $controller_class();
            $method = $this->query->get_method($controller);

            if ($method) {

                $this->response->setup();

                // Run action hooks for method
                do_action("json_api", $controller, $method);
                do_action("json_api-{$controller}-$method");

                // Error out if nothing is found
                if ($method == '404') {
                    $this->error('Not found');
                }

                // Run the method
                $result = $this->controller->$method();
                
				// General header setup
				// Allowing all domain
	            if (isset($_SERVER['HTTP_ORIGIN'])) {
			        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			        header('Access-Control-Allow-Credentials: true');
			        header('Access-Control-Max-Age: 86400');    // cache for 1 day
			    }
			
			    // Access-Control headers are received during OPTIONS requests
			    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			
			        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
			
			        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			
			        exit(0);
			    }
                
                
                if ($method == 'endeca_data') {

                    //----------------------------------------------------
                    // TODO : pull from cached file generated via cron job
                    //----------------------------------------------------
                    header('Content-type: application/xhtml+xml');
                    //header('');
                    echo $result;
                } else if ($method == 'sitemap') {

                    //----------------------------------------------------
                    // TODO : pull from cached file generated via cron job
                    //----------------------------------------------------
                    header('Content-type: application/xhtml+xml');
                    print_r($result);
                } else {
                    // Handle the result
                    $this->response->respond($result);
                }
                // Done!
                exit;
            }
        }
    }

    // admin panle controller
    // all admin area will be controlled from Admin folder
    /*
    function admin_menu() {
        add_options_page('Public API', 'Public API', 'manage_options', 'json-api', array(&$this, 'admin_options'));
    }
    */
    static function admin_options() {
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
    ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"><br /></div>
            <h2>Public API Info</h2>
            <p>Currently core only</p>
            <h3>Address</h3>
                <p>/api/navigation</p>
                <p>if permalinks not set -> /?json=navigation</p>
            <?php // TODO: make admin page dynamic ?>
        </div>
    <?php
    }

    function get_controllers() {
        $controllers = array();
        $dir = json_api_dir();
        $this->check_directory_for_controllers("$dir/controllers", $controllers);
        $this->check_directory_for_controllers(get_stylesheet_directory(), $controllers);
        $controllers = apply_filters('json_api_controllers', $controllers);

        return array_map('strtolower', $controllers);
    }

    function check_directory_for_controllers($dir, &$controllers) {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if (preg_match('/(.+)\.php$/i', $file, $matches)) {
                $src = file_get_contents("$dir/$file");
                if (preg_match("/class\s+JSON_API_{$matches[1]}_Controller/i", $src)) {
                    $controllers[] = $matches[1];
                }
            }
        }
    }

    function controller_is_active($controller) {
        if (defined('JSON_API_CONTROLLERS')) {
            $default = JSON_API_CONTROLLERS;
        } else {
            $default = 'core';
        }
        $active_controllers = explode(',', get_option('json_api_controllers', $default));

        return (in_array($controller, $active_controllers));
    }

    function update_controllers($controllers) {
        if (is_array($controllers)) {
            return implode(',', $controllers);
        } else {
            return $controllers;
        }
    }

    function controller_info($controller) {
        $path = $this->controller_path($controller);
        $class = $this->controller_class($controller);
        $response = array(
                'name' => $controller,
                'description' => '(No description available)',
                'methods' => array()
        );
        if (file_exists($path)) {
            $source = file_get_contents($path);
            if (preg_match('/^\s*Controller name:(.+)$/im', $source, $matches)) {
                $response['name'] = trim($matches[1]);
            }
            if (preg_match('/^\s*Controller description:(.+)$/im', $source, $matches)) {
                $response['description'] = trim($matches[1]);
            }
            if (preg_match('/^\s*Controller URI:(.+)$/im', $source, $matches)) {
                $response['docs'] = trim($matches[1]);
            }
            if (!class_exists($class)) {
                require_once($path);
            }
            $response['methods'] = get_class_methods($class);

            return $response;
        } else if (is_admin()) {
            return "Cannot find controller class '$class' (filtered path: $path).";
        } else {
            $this->error("Unknown controller '$controller'.");
        }

        return $response;
    }

    function controller_class($controller) {
        return "json_api_{$controller}_controller";
    }

    function controller_path($controller) {
        $json_api_dir = json_api_dir();
            $json_api_path = "$json_api_dir/controllers/$controller.php";
            $theme_dir = get_stylesheet_directory();
            $theme_path = "$theme_dir/$controller.php";
            if (file_exists($theme_path)) {
                $path = $theme_path;
            } else if (file_exists($json_api_path)) {
                $path = $json_api_path;
            } else {
                $path = null;
            }
        $controller_class = $this->controller_class($controller);

        return apply_filters("{$controller_class}_path", $path);
    }

    function get_nonce_id($controller, $method) {
        $controller = strtolower($controller);
        $method = strtolower($method);

        return "json_api-$controller-$method";
    }

    function flush_rewrite_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    function error($message = 'Unknown error', $status = 'error') {
        $this->response->respond(array(
                'error' => $message
        ), $status);
    }

    function include_value($key) {
        return $this->response->is_value_included($key);
    }

}

?>
