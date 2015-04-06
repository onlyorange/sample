<?php

/**
 * Basic introspection methods
 * @author juhonglee
 *
 */
class JSON_API_Core_Controller {

    // static api. this is temporary quick solution.
    // copied from wmag
    public function navTest($show_static = 1, $all_categories = 1) {

        // define static nav items
        $main = array( "1", "2", "3" );

        $static = array( '4'    => array( 'id' => 'name 4'
                , 'slug' => 'slug4'
                , 'link' => '/link4'
                , 'type' => 'type4'
                )
            );

        // get base categories
        $categories = get_categories();

        if ($all_categories != 1) {

            foreach ($main as $item) {

                $dynamic[$item] = array();
            }
        }

        // iterate through results and build proper nav data-structure
        foreach ($categories as $cat) {

            if ( in_array($cat->slug, $main) || $all_categories == 1 ) {

                $dynamic[$cat->slug]['id']        = $cat->name;
                $dynamic[$cat->slug]['slug']        = $cat->slug;
                $dynamic[$cat->slug]['link']        = "/" . $cat->slug;
                $dynamic[$cat->slug]['description'] = $cat->description;
                $dynamic[$cat->slug]['post_count']       = $cat->count;
                $dynamic[$cat->slug]['type']        = "category";

                // grab subcategories of parent
                $sub_categories = get_categories(array( 'child_of' => $cat->cat_ID));

                // iterate through results and build proper dynamic data-structure
                foreach ($sub_categories as $sub_cat) {
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['id']        = $sub_cat->name;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['slug']        = $sub_cat->slug;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['link']        = "/". $cat->slug . "/" . $sub_cat->slug;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['description'] = $sub_cat->description;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['post_count']       = $sub_cat->count;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['type']        = "subcategory";
                }
            }
        }

        // merge dynamic and static arrays
        if ($show_static != 1) return $dynamic;
        $subscribe = array("5" => $static["name5"]);
        //unset($static["name5"]);
        $nav = array_merge($subscribe, $dynamic, $static);

        return array(
            'count' => 'null',
            'navigation' => $nav
        );
    }
    public function info() {
        global $json_api;
        $php = '';
        if (!empty($json_api->query->controller)) {
            return $json_api->controller_info($json_api->query->controller);
        } else {
            $dir = json_api_dir();
            if (file_exists("$dir/json-api.php")) {
                $php = file_get_contents("$dir/json-api.php");
            } else {
                // check class file again
                $dir = dirname($dir);
                if (file_exists("$dir/json-api.php")) {
                    $php = file_get_contents("$dir/json-api.php");
                }
            }
            $active_controllers = explode(',', get_option('json_api_controllers', 'core'));
            $controllers = array_intersect($json_api->get_controllers(), $active_controllers);

            return array(
                'version' => '1.0',
                'controllers' => array_values($controllers)
            );
        }
    }
    /**
     *
     * @return multitype:string number Ambigous <mixed, boolean, WP_Error, multitype:, multitype:Ambigous <string, NULL> >
     */
    public function navigation() {
        global $json_api;
        $items = array();
        $menu_name = 'primary-menu';
        $args = array(
                'order'                  => 'ASC',
                'orderby'                => 'menu_order',
                'post_type'              => 'nav_menu_item',
                'post_status'            => 'publish',
                'output'                 => ARRAY_A,
                'output_key'             => 'menu_order',
                'nopaging'               => true,
                'update_post_term_cache' => false );

        $items = $json_api->introspector->get_nav_items($menu_name, $args);

        return array(
            'count' => count($items),
            'API version' => '1.0',
            'navigation' => $items
        );
    }

    // get all active category
    public function category() {
        global $json_api;
        $args = null;
        if (!empty($json_api->query->parent)) {
            $args = array(
                'parent' => $json_api->query->parent
            );
        }
        $categories = $json_api->introspector->get_categories($args);

        return array(
            'count' => count($categories),
            'navigation' => $categories
        );
    }

    public function posts() {
        global $json_api;
        $url = parse_url($_SERVER['REQUEST_URI']);
        $defaults = array(
                'post_type' => array('jet', 'mks-edit', 'fashion', 'kors-cares'),
                'posts_per_page' => -1, // no paging... :S if we have hundreds posts then this will kill server....
                'meta_key' => 'geo_city',
                'meta_value' => ''
        );
        $query = wp_parse_args($url['query']);
        $query = array_merge($defaults, $query);
        $posts = $json_api->introspector->get_geo_posts();
        $result = $this->geo_posts_result($posts);

        return $result;
    }
    public function dev($show_static = 1, $all_categories = 1) {
        // define static nav items
        $main = array( "1", "2", "3" );

        $static = array( '4'    => array( 'id' => 'name 4'
                , 'slug' => 'slug4'
                , 'link' => '/link4'
                , 'type' => 'type4'
        )
        );

        // get base categories
        $categories = get_categories();

        if ($all_categories != 1) {

            foreach ($main as $item) {

                $dynamic[$item] = array();
            }
        }

        // iterate through results and build proper nav data-structure
        foreach ($categories as $cat) {

            if ( in_array($cat->slug, $main) || $all_categories == 1 ) {

                $dynamic[$cat->slug]['id']        = $cat->name;
                $dynamic[$cat->slug]['slug']        = $cat->slug;
                $dynamic[$cat->slug]['link']        = "/" . $cat->slug;
                $dynamic[$cat->slug]['description'] = $cat->description;
                $dynamic[$cat->slug]['post_count']       = $cat->count;
                $dynamic[$cat->slug]['type']        = "category";

                // grab subcategories of parent
                $sub_categories = get_categories(array( 'child_of' => $cat->cat_ID));

                // iterate through results and build proper dynamic data-structure
                foreach ($sub_categories as $sub_cat) {
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['id']        = $sub_cat->name;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['slug']        = $sub_cat->slug;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['link']        = "/". $cat->slug . "/" . $sub_cat->slug;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['description'] = $sub_cat->description;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['post_count']       = $sub_cat->count;
                    $dynamic[$cat->slug]['subcategories'][$sub_cat->slug]['type']        = "subcategory";
                }
            }
        }

        // merge dynamic and static arrays
        if ($show_static != 1) return $dynamic;
        $subscribe = array("5" => $static["name5"]);
        //unset($static["name5"]);
        $nav = array_merge($subscribe, $dynamic, $static);

        return array(
                'count' => 'null',
                'navigation' => $nav
        );
    }
    public function get_recent_posts() {
        global $json_api;
        $posts = $json_api->introspector->get_posts();

        return $this->posts_result($posts);
    }

    public function post() {
        global $json_api, $post;
        $post = $json_api->introspector->get_current_post();
        if ($post) {
            $previous = get_adjacent_post(false, '', true);
            $next = get_adjacent_post(false, '', false);
            $response = array(
                    'post' => new JSON_API_Post($post)
            );
            if ($previous) {
                $response['previous_url'] = get_permalink($previous->ID);
            }
            if ($next) {
                $response['next_url'] = get_permalink($next->ID);
            }

            return $response;
        } else {
            $json_api->error("Not found.");
        }
    }

    protected function get_object_posts($object, $id_var, $slug_var) {
        global $json_api;
        $object_id = "{$type}_id";
        $object_slug = "{$type}_slug";
        extract($json_api->query->get(array('id', 'slug', $object_id, $object_slug)));
        if ($id || $$object_id) {
            if (!$id) {
                $id = $$object_id;
            }
            $posts = $json_api->introspector->get_posts(array(
                    $id_var => $id
            ));
        } else if ($slug || $$object_slug) {
            if (!$slug) {
                $slug = $$object_slug;
            }
            $posts = $json_api->introspector->get_posts(array(
                    $slug_var => $slug
            ));
        } else {
            $json_api->error("No $type specified. Include 'id' or 'slug' var in your request.");
        }

        return $posts;
    }

    protected function posts_result($posts) {
        global $wp_query;

        return array(
                'count' => count($posts),
                // no paging just dump all..yes this will be slow... :S
                //'count_total' => (int) $wp_query->found_posts,
                //'pages' => $wp_query->max_num_pages,
                'posts' => $posts
        );
    }

    protected function geo_posts_result($posts) {
        global $wp_query;
        if(!empty($_GET['city'])) {
        	$city = $_GET['city'];
        } else {
        	$city = $posts[0]->city;
        }
        return array(
                'count' => count($posts),
                // no paging just dump all..yes this will be slow... :S
                //'count_total' => (int) $wp_query->found_posts,
                //'pages' => $wp_query->max_num_pages,
                'city' => $city,
                'posts' => $posts
        );
    }
    protected function posts_object_result($posts, $object) {
        global $wp_query;
        // Convert something like "JSON_API_Category" into "category"
        $object_key = strtolower(substr(get_class($object), 9));

        return array(
                'count' => count($posts),
                'pages' => (int) $wp_query->max_num_pages,
                $object_key => $object,
                'posts' => $posts
        );
    }
    public function sitemap() {
        $file = '/tmp/sitemap.xml';
        if (file_exists($file)) {
            $xml = file_get_contents($file);

            return $xml;
        } else {
            return 'file open failed.';
        }
    }
    public function endeca_data() {
        //this will be cached version
        $cached = file_get_contents('_data_feed.xml');
        if (!empty($cached)) {
            return $cached;
        } else {
            $result = '<?xml version="1.0" encoding="UTF-8" ?>';
            $args = array(  'post_type'=>array('mks-edit', 'jet', 'fashion', 'kors-cares'),
                    // need to add black list handling from admin panel.
                    'tax_query' => array(
                            array(
                                    'taxonomy' => 'jet-category',
                                    'field' => 'slug',
                                    'terms' => array('celebrities'),
                                    'operator' => 'NOT IN'
                            )
                    ),
                    'posts_per_page' => -1,
                    'order' => 'DESC',
            );
            $query_result = new WP_Query($args);
            $result .='<RECORDS>';
            $result .= '<NOTE>No cache file found. Getting live data.</NOTE>';
            while( $query_result->have_posts()) : $query_result->the_post();
                //$cont = do_shortcode(get_the_content());
                if(get_post_meta($query_result->post->ID, 'endeca_search', true) == '1') {
                    // trust me I didn't want to do this way either...
                } else {
                    $yoast_meta = get_post_meta($query_result->post->ID, '_yoast_wpseo_metadesc', true);
                    $yoast_generic = new WPSEO_Frontend();
                    $generic_meta = $yoast_generic->metadesc(false);
                    if (!empty($yoast_meta)) {
                        $cont = $yoast_meta;
                    } else if (!empty($generic_meta)) {
                        $cont = $generic_meta;
                    } else {
                        $cont = apply_filters('the_content', get_the_content());
                        $cont = strip_tags($cont);
                    }
					$cont = mb_substr($cont, 0, 251);
					$cont .= "...";
                    //$cont = utf8_encode($cont);
                    //$cont = iconv("UTF-8","UTF-8//IGNORE",$cont);
                    //$cont = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $cont);
                    //$cont = mb_convert_encoding($cont, "UTF-8");
                    //$cont = mb_convert_encoding($cont, "UTF-8", "UTF-8");
                    $result .='
                            <RECORD>
                                <PROP NAME="Article.id">
                                    <PVAL>' . $query_result->post->ID . '</PVAL>
                                </PROP>
                                <PROP NAME="Article.title">
                                    <PVAL>' . $query_result->post->post_title . '</PVAL>
                                </PROP>
                                <PROP NAME="Article.url">
                                    <PVAL>' . get_permalink($query_result->post->ID) . '</PVAL>
                                </PROP>
                                <PROP NAME="Article.Description">
                                    <PVAL><![CDATA[' . $cont . ']]></PVAL>
                                </PROP>
                                <PROP NAME="Article.imageurl">
                                    <PVAL>
                    ';
                    /*
                    remove attached images. show featured image only.
                    $arg = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_mime_type' => 'image', 'post_status' => null, 'post_parent' => $query_result->post->ID );
                    $attachments = get_posts($arg);
                    */
                    if (has_post_thumbnail($query_result->post->ID)):
                        //$result .= "<ITEM>";
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id( $query_result->post->ID ), 'default-thumbnail');
                        $result .= $image[0];
                        //$result .= "</ITEM>";
                    endif;
                    /*
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            $imgCount++;
                            $result .= "<ITEM COUNT='".$imgCount."'>";
                            $src = wp_get_attachment_image_src( $attachment->ID, "attached-image");
                            if ($src) { $result .= $src[0];}
                            // if($imgCount > 1) $result .= ", ";
                            // $result .= $attachment->guid;
                            $result .= "</ITEM>";
                        }
                    }
                    */
                    $result .='     </PVAL>
                                        </PROP>
                                        <PROP NAME="Article.locale">
                                            <PVAL>' . get_locale() .'</PVAL>
                                        </PROP>
                                    </RECORD>';
                }
            endwhile;
            wp_reset_postdata();
            $result .= '</RECORDS>';

            return $result;
        }
    }
}

?>
