<?php
// Load worpdress native plugin lib.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Helper functions for swedenWp main and swedenWp macros
 *
 * @author juhonglee
 *
 */
class swedenWpFunctions {

    /**
     * Gets content for title in <title> element
     * @return string
     */
    public static function getTitle() {
        global $page, $paged;

        $return = '';

        // If Wordpress SEO is activated (Yoast Wordpress SEO)
        if (is_plugin_active('wordpress-seo/wp-seo.php')) {
            $return .= wp_title('');
        } else {

            $return .= wp_title('|', false, 'right');
            $return .= get_bloginfo('name');
            $site_description = get_bloginfo('description', 'display');
            if($site_description && (is_home() || is_front_page()))
                $return .= " | $site_description";

            // Add a page number if necessary:
            if($paged >= 2 || $page >= 2)
                $return .= ' | ' . sprintf(__('Page %s', 'ait' ), max($paged, $page));
        }

        return $return;
    }

    /**
     * Alias simply isn't enough for edit_comment_link(). Didn't make a controller at worpdress template so...doing it here!
     * @param string $link, type $id, string $before, string $after
     * @return string HTML link
     */
    public static function editCommentLink($link = null, $id, $before = '', $after = '') {
        if(!current_user_can('edit_comment', $id))

            return;

        if(is_null($link))
            $link = __('Edit This', 'ait');

        $link = '<a class="comment-edit-link" href="' . get_edit_comment_link($id) . '" title="' . esc_attr__( 'Edit comment' ) . '">' . $link . '</a>';
        echo $before . apply_filters( 'edit_comment_link', $link, $id ) . $after;
    }



    public static function lessify() {
        ////////////////////////////////////////
        // AK: no long using anything LESS.
        //  I removed two related functions
        //  that appeared below this one.
        return "";
        ////////////////////////////////////////
        // $importDir = THEME_CSS_DIR;
        // $input = THEME_DIR . '/style.less.css';
        // $output = THEME_STYLESHEET_FILE;
        // if (!file_exists($input)) {
        //     wp_die("File '$input' doesn't exists.", "File '$input' doesn't exists.", array('response' => 500, 'back_link' => true));
        // }
        // $timeIn  = filemtime($input);
        // $timeOut = intval(@filemtime($output)); // @ - file not exists, intval(false) -> 0
        // $a = array();
        // $a[-1] = $timeIn;
        // $fs =  array_filter(array_merge((array) @glob("{$importDir}/*.css"), (array) @glob("{$importDir}/*.less")));
        // foreach ($fs as $f) {
        //     $a[] = filemtime($f);
        // }
        // $max = max($a);
        // // parse only if there is no style.css or LESS file is newer then CSS file
        // if (!file_exists($output) || ((file_exists($output) &&  $max > $timeOut) || (file_exists($output) &&  $max == $timeOut))) {
        //     self::saveLess2Css();
        // }
        // return THEME_STYLESHEET_URL . "?" . $max;
    }

    /**
     * Creating LESS file from CSS
     *
     * @return string
     */
    public static function generatedCss() {

        ////////////////////////////////////////
        // AK: no long using anything LESS.
        return "";
        ////////////////////////////////////////

        // $importDir = THEME_CSS_DIR;
        // $input = THEME_DIR . '/style.less.css';
        // $outputName = "style." . (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'en') . ".css";
        // $output = CACHE_URL . "/$outputName";
        // if (!file_exists($input)) {
        //     wp_die("File '$input' doesn't exists.", "File '$input' doesn't exists.", array('response' => 500, 'back_link' => true));
        // }
        // $timeIn  = filemtime($input);
        // $timeOut = intval(@filemtime($output)); // @ - file not exists, intval(false) -> 0
        // $a = array();
        // $a[-1] = $timeIn;
        // $fs =  array_filter(array_merge((array) @glob("{$importDir}/*.css"), (array) @glob("{$importDir}/*.less")));
        // foreach ($fs as $f) {
        //     $a[] = @filemtime($f);
        // }
        // $max = max($a);
        // // parse only if there is no style.css or LESS file is newer then CSS file
        // if (!file_exists($output) || ((file_exists($output) &&  $max > $timeOut) || (file_exists($output) &&  $max == $timeOut))) {
        //     aitGenerateCss();
        // }
        // return $output . "?" . $max;
    }



    /**
     * Helper function, use in future... maybe...
     *
     * @param string $key
     * @throws Exception
     * @return multitype:
     */
    public static function getConfigOptions($key) {
        $config = array();

        if (isset($GLOBALS['themeConfig'][$key])) {
            $config = $GLOBALS['themeConfig'][$key]['options'];
        } else {
            foreach ($GLOBALS['themeConfig'] as $k => $v) {
                if (isset($v['tabs'])) {
                    if (isset($GLOBALS['themeConfig'][$k]['tabs'][$key])) {
                        $config = $GLOBALS['themeConfig'][$k]['tabs'][$key]['options'];
                        break;
                    }
                }
            }
        }

        if(empty($config))
            throw new Exception("There is no key like '$key' in config file.");

        return $config;
    }

    /**
     * Get option value for exact blog
     *
     * @param string $key
     * @param int $blog_id
     * @return string:
     */
    public static function getOptionValue($key, $blog_id = 1) {
        global $wpdb;

        if($blog_id == 1)
            $wp_option_table = "wp_options";
        else
            $wp_option_table = "wp_".$blog_id."_options";

        $value = $wpdb->get_var("SELECT option_value FROM $wp_option_table WHERE option_name = '".$key."'");

        return $value;
    }

    /**
     * Gets the breadcrumbs
     * @param string $delimiter
     * @param array array('home' => 'Home', 'delimiter' => '&raquo;')
     * @return string Breadcrumbs HTML
     */
    public static function breadcrumbs($args = array()) {

        if(!isset($args['delimiter']))
            $delimiter = '&raquo;';
        else
            $delimiter = $args['delimiter'];

        if(!isset($args['home']))
            $home = __('Home', 'ait');
        else
            $home = $args['home'];

        $return = '';

        $before = '<span class="current">'; // tag before the current crumb
        $after = '</span>'; // tag after the current crumb

        if (!is_home() && !is_front_page() || is_paged()) {

            $return .= '<span class="crumbs">';

            global $post;
            //$homeLink = get_bloginfo('url');
            $homeLink = home_url();
            $return .= '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

            if (is_category()) {
                $cat_obj = $GLOBALS['wp_query']->queried_object;
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);

                if ($thisCat->parent != 0)
                    $return .= get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');

                $return .= $before . sprintf(__('Category: "%s"', 'ait'), single_cat_title('', false)) . $after;

            } elseif (is_day()) {
                $return .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                $return .= '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                $return .= $before . get_the_time('d') . $after;

            } elseif (is_month()) {
                $return .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                $return .= $before . get_the_time('F') . $after;

            } elseif (is_year()) {
                $return .= $before . get_the_time('Y') . $after;

            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;

                    $return .= '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';

                    // if we don't want to use different one but...doesn't seems that we will use breadcrumb maybe I just need to kill all
                    // $return .= $post_type->labels->singular_name . ' ' . $delimiter . ' ';

                    $return .= $before . get_the_title() . $after;
                } else {
                    $cat = get_the_category();
                    if (!empty($cat)) {
                        $cat = $cat[0];
                        $return .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                        $return .= $before . get_the_title() . $after;
                    }
                }

            } elseif (!is_single() && !is_search() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                if(is_null($post_type)) return '';
                $return .= $before . $post_type->labels->singular_name . $after;

            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                if (is_array($cat) and isset($cat[0])) {
                    $cat = $cat[0];
                    $return .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                    $return .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
                    $return .= $before . get_the_title() . $after;
                }

            } elseif (is_page() && !$post->post_parent) {
                $return .= $before . get_the_title() . $after;

            } elseif (is_page() && $post->post_parent) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();

                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id  = $page->post_parent;
                }

                $breadcrumbs = array_reverse($breadcrumbs);

                $return .= trim(join($breadcrumbs, ' ' . $delimiter . ' '));

                $return .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

            } elseif (is_tag()) {
                $return .= $before . sprintf(__('Posts tagged &quot;%s&quot;', 'ait'), single_tag_title('', false)) . $after;

            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                $return .= $before . sprintf(__('Author: &quot;%s&quot;', 'ait'), $userdata->display_name) . $after;

            } elseif (is_404()) {
                $return .= $before . __('Error 404', 'ait') . $after;

            } elseif (is_search()) {
                $return .= $before . sprintf(__('Search results for &quot;%s&quot;', 'ait'), get_search_query()) . $after;
            }

            if (get_query_var('paged')) {
                if( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                    $return .= ' (';

                $return .= __('Page', 'ait') . ' ' . get_query_var('paged');

                if(is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                    $return .= ')';
            }

            $return .= '</span>';

        } elseif ($GLOBALS['wp_query']->is_posts_page) {

            $return .= '<span class="crumbs">';
            //$return .= '<a href="' . get_bloginfo('url') . '">' . $home . '</a> ' . $delimiter . ' ';
      $return .= '<a href="' . home_url() . '">' . $home . '</a> ' . $delimiter . ' ';
            $return .= $GLOBALS['wp_query']->queried_object->post_title;
            $return .= '</span>';

        } elseif (is_home() || is_front_page()) {
      $return .= '<span class="crumbs">';
            //$return .= '<a href="' . get_bloginfo('url') . '">' . $home . '</a>';
      $return .= '<a href="' . home_url() . '">' . $home . '</a>';
            $return .= '</span>';
    }

        return $return;
    }

    /**
     * Get day link
     * @param string $date Date in format accepted by strtotime().
     */
    public static function getDayLink($date = "") {
        $dateArray = date_parse($date);

        return get_day_link($dateArray['year'], $dateArray['month'], $dateArray['day']);
    }

    /**
     * Get post ID from its slug
     *
     * @param string $post_slug
     * @return integer
     */
    public static function get_ID_by_slug($post_slug) {
        $post = get_page_by_path($post_slug);
        if ($post) {
            return $post->ID;
        } else {
            return null;
        }
    }
    /**
     *
     * @return string
     */
    public static function getSingleNav() {
        global $post;

        $tax = get_post_type() . "-category";
        $postType = 'post';
        $terms = get_the_terms($post->ID, $tax);
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $postType = $term->name;
            }
        }
        $nav = "";
        $listArgs = array(
                'posts_per_page'  => -1,
                'offset'		=> 1,
                'order'           => 'ASC',
                'post_type'       => get_post_type(),
                $tax => $postType
        );
        $postList = get_posts($listArgs);

        // get ids of posts retrieved from get_posts
        $ids = array();
        foreach ($postList as $list) {
            $ids[] += $list->ID;
        }

        // get and echo previous and next post in the same taxonomy
        $current = array_search($post->ID, $ids);

        $prevId = $current > 0 ? $ids[$current - 1] : false;
        $nextId = $current < count($ids)-1 ? $ids[$current + 1] : false;

        if ( !empty($prevId) ) {
            $nav .= '<a rel="prev" class="prev" href="' . get_permalink($prevId). '"></a>';
        }
        else $nav .= '<a rel="prev" class="prev disabled"></a>';
        if ( !empty($nextId) ) {
            $nav .= '<a rel="next" class="next" href="' . get_permalink($nextId). '"></a>';
        }
        else $nav .= '<a rel="next" class="next disabled"></a>';
        return $nav;
    }

    /**
     * Get paginate links
     *
     * @param string $new
     * @return string
     */
    public static function paginateLinks($new = false) {
        global $wp_query;

        $return = "";

        if ($new) {

            $range = 1;
            $centerItems = ($range*2)+1;

            $total = intval($wp_query->max_num_pages);
            $current = max(1, get_query_var('paged')); // if 0 then 1

            if ($total > 1) {

                $return = '<div class="page_nav">';

                // Prev
                if ($current > 1) {
                    $return .= '<a class="page-numbers prev" href="'.get_pagenum_link($current - 1).'">'.__('Prev', 'ait').'</a>';
                }
                // First
                if ($current == 1) {
                    $return .= '<span class="page-numbers current">1</span>';
                } else {
                    $return .= '<a class="page-numbers" href="'.get_pagenum_link(1).'">1</a>';
                }

                $j = 0;
                for ($i = $current - $range; $i <= $current + $range; $i++) {
                    if ($i > 1 && $i < $total) {
                        $j++;
                    }
                }
                if($j > 0) $return .= '<span class="dots">...</span>';

                for ($i = $current - $range; $i <= $current + $range; $i++) {
                    if ($i > 1 && $i < $total) {
                        if ($i == $current) {
                            // Current
                            $return .= '<span class="page-numbers current">'.$current.'</span>';
                        } else {
                            $return .= '<a class="page-numbers" href="'.get_pagenum_link($i).'">'.$i.'</a>';
                        }
                    }
                }

                if($j > 0) $return .= '<span class="dots">...</span>';

                // Last
                if ($current == $total) {
                    $return .= '<span class="page-numbers current">'.$total.'</span>';
                } else {
                    $return .= '<a class="page-numbers" href="'.get_pagenum_link($total).'">'.$total.'</a>';
                }
                // Next
                if ($current < $total) {
                    $return .= '<a class="page-numbers next" href="'.get_pagenum_link($current + 1).'">'.__('Next', 'ait').'</a>';
                }

                $return .= '</div>';

            }

        } else {

            $total_pages = $wp_query->max_num_pages;

            if ($total_pages > 1) {

              $current_page = max(1, get_query_var('paged'));

              $return = '<div class="page_nav">';

              $return .= paginate_links(array(
                  'base' => get_pagenum_link(1) . '%_%',
                  'format' => 'page/%#%',
                  'current' => $current_page,
                  'total' => $total_pages,
                  'prev_text' => __('Prev', 'ait'),
                  'next_text' => __('Next', 'ait'),
                ));

              $return .= '</div>';

            }

        }

        return $return;

    }
    /**
     * camelCase -> underscore_separated.
     * @param  string
     * @return string
     */
    public static function camel2underscore($s) {
        $s = preg_replace('#(.)(?=[A-Z])#', '$1_', $s);
        $s = strtolower($s);

        return $s;
    }

    /**
     * Alias for strip_tags() PHP function
     * @param  string $inputText
     * @return string
     */
    public static function stripTag($inputText = "") {
        $stripped = strip_tags($inputText);

        return $stripped;
    }

    // collection of enfold helper functions
    //
    static $cache = array(); 		//holds database requests or results of complex functions
    static $templates = array(); 	//an array that holds all the templates that should be created when the print_media_templates hook is called

    /**
     * get_url - Returns a url based on a string that holds either post type and id or taxonomy and id
    */
    static function get_url($link, $post_id = false) {
        $link = explode(',', $link);

        if ($link[0] == 'lightbox') {
            $link = wp_get_attachment_image_src($post_id, 'large');

            return $link[0];
        }

        if(empty($link[1]))               return $link[0];
        if($link[0] == 'manually')        return $link[1];
        if(post_type_exists( $link[0] ))  return get_permalink($link[1]);
        if(taxonomy_exists( $link[0]  ))  return get_term_link(get_term($link[1], $link[0]));
    }

    /**
     * Get list of Category by post link
     *
     * @param string $link
     * @param string $post_id
     * @return void|string
     */
    static function get_category($link, $post_id = false) {

        if (empty($link) && !$post_id) {
            return;
        }

        $link = explode(',', $link);
        $categories = get_the_category($link);
        $separator = ' ';
        $output = '';
        $result = '';
        if ($categories) {
            foreach ($categories as $category) { //href="'.get_category_link( $category->term_id ).'" removed
                $output .= '<a class="category"
                        title="' . esc_attr( sprintf( __( "View all articles in %s" ), $category->name ) ) . '">'.
                        $category->cat_name.'</a>'.$separator;
            }
            $result = trim($output, $separator);
        }

        return $result;
    }

    /**
     * get_entry - fetches an entry based on a post type and id
     *
     * @param string $entry list of entry information comma-separted
     * @return boolean
     */
    static function get_entry($entry) {
        $entry = explode(',', $entry);

        if(empty($entry[1]))              return false;
        if($entry[0] == 'manually')        return false;
        if(post_type_exists( $entry[0] ))  return get_post($entry[1]);
    }

    /**
     * Fetch all available sidebars
     *
     * @param array $sidebars
     * @param array $exclude
     * @return array
     */
    static function get_registered_sidebars($sidebars = array(), $exclude = array()) {
        //fetch all registered sidebars and save them to the sidebars array
        global $wp_registered_sidebars;

        foreach ($wp_registered_sidebars as $sidebar) {
            if ( !in_array($sidebar['name'], $exclude)) {
                $sidebars[$sidebar['name']] = $sidebar['name'];
            }
        }

        return $sidebars;
    }



    /**
     * Get all category for any post type in loop
     *
     * @param post id
     * @return string
     */
    static function all_taxonomies_links($param, $classOption='') {
        // get post by post id

        $post = get_post($param);

        // get post type by post
        $post_type = $post->post_type;

        if (!isset($classOption)) {
            $classOption = "";
        }

        // get post type taxonomies
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        $out = array();
        $i = 0;
        // $out[] = '<div class="category">';
        foreach ($taxonomies as $taxonomy_slug => $taxonomy) {

            // get the terms related to post
            $terms = get_the_terms( $post->ID, $taxonomy_slug );
            if ( !empty( $terms ) ) {

                if (count($terms) > 1) {
                    // when it has parent -> xxx : xxx
                    foreach ($terms as $term) { // href="'.    get_term_link( $term->slug, $taxonomy_slug ) .'" removing link
                        if ($term-> parent > 0) {
                            if(get_cat_name($term->parent)){
                            $out[] = '<div class="slug '.$classOption.'">'
                                    . get_cat_name($term->parent) . ': ' . $term->name
                                    . "</div>";
                            } else {
                            $out[] = '<div class="slug '.$classOption.'">'
                                    . $term->name
                                    . "</div>";

                            }
                        }
                    }
                } else {
                    // alyway show only 1 cat
                    if ($terms[0]->taxonomy == 'post_tag') {
                        // don't show location category
                        // handle when the post is in location
                    } else {
                        $out[] = '<div class="slug '.$classOption.'">'
                                . $terms[0]->name
                                . "</div>";
                    }
                }

            }
            $out[] = "";
        }
        // $out[] = "</div>";
        return implode('', $out );
    }


    static function custom_active_terms($param) {
        $taxonomies = array(
                'mks-edit-category',
                'jet-category',
                'fashion-category',
        		'kors-cares-category'
        );
        $taxonomies = $param . "-category";
        $categories = get_terms($taxonomies);
        $out = 'Add New Page</a><ul class="addNewSub">';
        foreach ($categories as $cat) {
            $nesting = get_term_children($cat->term_id, $cat->taxonomy);
            if($cat->parent == '0') $out .= '<li><a href="post-new.php?post_type='. $param .'&term_id=' . $cat->term_id . '">'. $cat->name . '</a>';
            if (isset($nesting)) {
                $out .= '<ul style="margin-left:20px;">';
                foreach ($nesting as $nest) {
                    $term = get_term_by('id', $nest, $cat->taxonomy);
                    $out .= '<li>';
                    $out .= '<a style="margin-top:-5px;" href="post-new.php?post_type=' . $param . '&term_id=' . $nest . '">'. $term->name .'</a>';
                    //$out .= '<a style="margin-top:-5px" href="post-new.php?post_type=' . $param . '&term_id=' . $nest . '">'.$what.'</a>';
                    $out .= '</li>';
                }
                $out .= '</ul></li>';
            } else {
                $out .= '</li>';
            }
        }

        $out .= "</ul>";

        return $out;
    }

    /**
     * Get array of registered image sizes from wp functions
     *
     * @param array $exclude
     * @param string $enforce_both
     * @return multitype:unknown
     */
    static function get_registered_image_sizes($exclude = array(), $enforce_both = false)
    {
        global $_wp_additional_image_sizes;

        // Standard sizes
        $image_sizes = array(   'no scaling'=> array("width"=>"Original Width ", "height"=>" Original Height"),
                'thumbnail' => array("width"=>get_option('thumbnail_size_w'), "height"=>get_option('thumbnail_size_h')),
                'medium' 	=> array("width"=>get_option('medium_size_w'), "height"=>get_option('medium_size_h')),
                'large' 	=> array("width"=>get_option('large_size_w'), "height"=>get_option('large_size_h')));

        if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
            $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes  );

        $result = array();
        foreach($image_sizes as $key => $image)
        {
            if( (is_array($exclude) && !in_array($key, $exclude)) || (is_numeric($exclude) && ($image['width'] > $exclude || $image['height'] > $exclude)) || !is_numeric($image['height']))
            {
                if($enforce_both == true && is_numeric($image['height']))
                {
                    if($image['width'] < $exclude || $image['height'] < $exclude) continue;
                }


                $title = str_replace("_",' ', $key) ." (".$image['width']."x".$image['height'].")";

                $result[ucwords( $title )] =  $key;
            }
        }

        return $result;
    }


    /**
     * Check if it's ajax call
     *
     * is_ajax - Returns true when the page is loaded via ajax.
     * @return boolean
     */
    static function is_ajax() {
        if ( defined('DOING_AJAX') )
            return true;

        return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
    }

    /**
     * function that gets called on backend pages and hooks other functions into wordpress
     *
     * @return void
     */
    static function backend() {
        add_action( 'print_media_templates', array('swedenWpFunctions', 'print_templates' )); 		//create js templates for swedenBuilder Canvas Elements
    }

    /**
     * Helper function that prints all the javascript templates
     *
     * @return void
     */
    static function print_templates() {
        foreach (self::$templates as $key => $template) {
            echo "\n<script type='text/html' id='avia-tmpl-{$key}'>\n";
            echo $template;
            echo "\n</script>\n\n";
            }

            //reset the array
            self::$templates = array();
    }

    /**
    * Helper function that creates a new javascript template to be called
    *
    * @return void
    */
    static function register_template($key, $html) {
        self::$templates[$key] = $html;
    }

    /**
    * Helper function that fetches all "public" post types.
    *
    * @return array $post_types example output: data-modal='true'
    */
    static function public_post_types() {
        $post_types 		= get_post_types(array(
                                        'public' => false,
                                        '_builtin' => true,
                                        'name' => 'attachment',
                                        'show_ui'=>false,
                                        'publicly_queryable'=>false
                                    ), 'names', 'NOT');
        // remove post type page from link picker
        // $post_types['page'] = 'page';
        $post_types 		= array_map("ucfirst", $post_types);
        $post_types			= apply_filters('avia_public_post_types', $post_types);
        self::$cache['post_types'] = $post_types;

        return $post_types;
    }

    /**
    * Helper function that fetches all taxonomies attached to public post types.
    *
    * @return array $taxonomies
    */
    static function public_taxonomies($post_types = false, $merged = false) {
        $taxonomies = array();

        if(!$post_types)
            $post_types = empty(self::$cache['post_types']) ? self::public_post_types() : self::$cache['post_types'];

        if(!is_array($post_types))
            $post_types = array($post_types => ucfirst($post_types));

        foreach ($post_types as $type => $post) {
            $taxonomies[$type] = get_object_taxonomies($type);
        }

        $taxonomies = apply_filters('avia_public_taxonomies', $taxonomies);
        self::$cache['taxonomies'] = $taxonomies;

        if ($merged) {
            $new = array();
            foreach ($taxonomies as $taxonomy) {
                foreach ($taxonomy as $tax) {
                    $new[$tax] = ucwords(str_replace("_", " ",$tax));
                }
            }

            $taxonomies = $new;
        }

        return $taxonomies;
    }

    /**
    * Helper function that converts an array into a html data string
    *
    * @param array $data example input: array('modal'=>'true')
    * @return string $data_string example output: data-modal='true'
    */
    static function create_data_string($data = array()) {
        $data_string = "";

        foreach ($data as $key=>$value) {
            if(is_array($value)) $value = implode(", ",$value);
            $data_string .= " data-$key='$value' ";
        }

        return $data_string;
    }

    /**
    * Create a lower case version of a string without spaces so we can use that string for database settings
    *
    * @param string $string to convert
    * @return string the converted string
    */
    public static function save_string($string , $replace = "_") {
        $string = strtolower($string);

        $trans = array(
            '&\#\d+?;'				=> '',
            '&\S+?;'				=> '',
            '\s+'					=> $replace,
            'ä'					=> 'ae',
            'ö'					=> 'oe',
            'ü'					=> 'ue',
            'Ä'					=> 'Ae',
            'Ö'					=> 'Oe',
            'Ü'					=> 'Ue',
            'ß'					=> 'ss',
            '[^a-z0-9\-\._]'		=> '',
            $replace.'+'			=> $replace,
            $replace.'$'			=> $replace,
            '^'.$replace			=> $replace,
            '\.+$'					=> ''
        );

        $trans = apply_filters('avf_save_string_translations', $trans, $string, $replace);

        $string = strip_tags($string);

        foreach ($trans as $key => $val)
        {
            $string = preg_replace("#".$key."#i", $val, $string);
        }

        return stripslashes($string);
    }

    /**
    * Helper function that creates the necessary css code to include a custom font
    *
    * @param array $element requires an element that matches the structure of an elemen passed to the swedenHtmlHelper
    * @return string $output
    */
    static function load_font($element) {
        $font 			= $element['font'];
        $fstring 		= $element['folder'].$font;
        $container_id 	= "avia_".$element['id'];
        $output 		= "";

        $output .="
<style type='text/css'>
@font-face {
    font-family: '$font'; font-weight: normal; font-style: normal;
    src: url('$fstring.eot');
    src: url('$fstring.eot?#iefix') format('embedded-opentype'),
        url('$fstring.woff') format('woff'),
        url('$fstring.ttf') format('truetype'),
        url('$fstring.svg#fontello') format('svg');
}

.avia-font-$font{ font-family: '$font'; }
</style>
        ";

        return $output;
    }

    /**
     * Support template builder layout array via enfold configuration
     *
     * @param string $post_type
     * @param string $post_id
     *
     * !!!! not doing layout thing...let's do it wordpress way
     */
    public static function template_layout_array($post_type = false, $post_id = false) {
        // grrrr enfold thingy keeps overwriting my page options!!! I am making dedicated template array for anything related enfold codes
        // support enfold type short codes.
        // enfold layout array -> [][][]
        //global $avia_config;

        // check which string to use
        $result = false;
    }

    /**
     * better looking pagination
     *
     * @param string $pages
     * @return string
     */
    public static function pagination($pages = '') {
        global $paged;

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        $output = "";
        $prev = $paged - 1;
        $next = $paged + 1;
        $range = 2;
        $showitems = ($range * 2)+1;



        if ($pages == '') {
            global $wp_query;
            // $pages = ceil(wp_count_posts($post_type)->publish / $per_page);
            // easier way to get it.. :S
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        $method = "get_pagenum_link";
        if (is_single()) {
            $method = "avia_post_pagination_link";
        }

        if (1 != $pages) {
            $output .= "<div class='pagination'>";
            $output .= "<span class='pagination-meta'>".sprintf(__("Page %d of %d", 'avia_framework'), $paged, $pages)."</span>";
            $output .= ($paged > 2 && $paged > $range+1 && $showitems < $pages)? "<a href='".$method(1)."'>&laquo;</a>":"";
            $output .= ($paged > 1 && $showitems < $pages)? "<a href='".$method($prev)."'>&lsaquo;</a>":"";

            for ($i=1; $i <= $pages; $i++) {
                if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
                    $output .= ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".$method($i)."' class='inactive' >".$i."</a>";
                }
            }

            $output .= ($paged < $pages && $showitems < $pages) ? "<a href='".$method($next)."'>&rsaquo;</a>" :"";
            $output .= ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) ? "<a href='".$method($pages)."'>&raquo;</a>":"";
            $output .= "</div>\n";
        }

        return $output;
    }

    function post_pagination_link($link) {
        $url =  preg_replace('!">$!','',_wp_link_page($link));
        $url =  preg_replace('!^<a href="!','',$url);

        return $url;
    }

    /**
     * Shorten a string
     *
     * @param unknown $string
     * @param unknown $limit
     * @param string $break
     * @param string $pad
     * @param string $stripClean
     * @param string $excludetags
     * @param string $safe_truncate
     * @return Ambigous <string, string, mixed>
     */
    public static function backend_truncate($string, $limit, $break=".", $pad="…", $stripClean = false, $excludetags = '<strong><em><span>', $safe_truncate = false) {
        if ($stripClean) {
            $string = strip_shortcodes(strip_tags($string, $excludetags));
        }

        if(strlen(trim($string)) <= $limit) return $string;

        if (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
                if ($safe_truncate) {
                    $string = mb_strimwidth($string, 0, $breakpoint) . $pad;
                } else {
                    $string = substr($string, 0, $breakpoint) . $pad;
                }
            }
        }

        // when no breakpoint and no tags
        if (!$breakpoint && strlen(strip_tags($string)) == strlen($string)) {
            if ($safe_truncate) {
                $string = mb_strimwidth($string, 0, $limit) . $pad;
            } else {
                $string = substr($string, 0, $limit) . $pad;
            }
        }

        return $string;
    }

    /**
     * fetching images based on its id.
     *
     * @param unknown $thumbnail_id
     * @param unknown $size
     * @param string $output
     * @param string $data
     * @return boolean|Ambigous <unknown, multitype:, Ambigous <string, boolean, mixed>, boolean, mixed, unknown, Ambigous <multitype:, multitype:int , multitype:Ambigous <int, mixed> >>|string
     */
    public static function image_by_id($thumbnail_id, $size = array('width'=>800,'height'=>800), $output = 'image', $data = "") {
        if (!is_numeric($thumbnail_id)) {return false; }

        if (is_array($size)) {
            $size[0] = $size['width'];
            $size[1] = $size['height'];
        }

        // get the image with appropriate size by checking the attachment images
        $image_src = wp_get_attachment_image_src($thumbnail_id, $size);

        //if output is set to url return the url now and stop executing, otherwise build the whole img string with attributes
        if ($output == 'url') return $image_src[0];

        //get the saved image metadata:
        $attachment = get_post($thumbnail_id);

        if (is_object($attachment)) {
            $image_description = $attachment->post_excerpt == "" ? $attachment->post_content : $attachment->post_excerpt;
            $image_description = trim(strip_tags($image_description));
            $image_title = trim(strip_tags($attachment->post_title));

            return "<img src='".$image_src[0]."' title='".$image_title."' alt='".$image_description."' ".$data."/>";
        }
    }

    /**
     * get the category for custom post.
     *
     * @param string $post_type
     * @param object $post
     * @return string;
     */
    public static function get_custom_post_category($post_type, $post) {
            $taxonomies = get_object_taxonomies( $post_type, 'objects' );
            foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
                $terms = get_the_terms( $post->ID, $taxonomy_slug );
                if ( !empty( $terms ) ) {
                    foreach ($terms as $term) {
                        $category = $term->name;
                    }
                }
            }

            return empty($category) ? '' : $category;

    }

    /**
     * get posts for category/subcategory landing pages.
     *
     * @param string $post_type
     * @param string $category
     * @param string $posts_per_age
     * @param int $page
     * @return array
     */
    public static function get_category_posts($post_type,$category,$posts_per_page = 6, $page = 0, $exclude = '') {

        //build custom taxonomy
        $tax = $post_type . "-category";
        // to use exclude, param must be serialized..duh...
        // $exclude = implode(",", $exclude);

        //build query arguments array with post type and custom taxonomy category
        if (!empty($category)) {
        	if($category == 'celebrity-tag') {
        		$args = array(
        				'posts_per_page' => $posts_per_page,
        				'tax_query' => array(
        						array(
        								'taxonomy' => 'celebrity-tag',
        								'field' => 'slug',
        								'terms' => $celebSlug
        						)
        				)
        		);
        	} else {
        		$args = array(
        				'post_type' => $post_type,
        				$tax => $category,
        				'posts_per_page' => $posts_per_page,
        				'tax_query' => array(
        						'relation' => 'AND',
        						array(
        								'taxonomy' => $tax,
        								'field' => 'slug',
        								'terms' => array($category)
        						),
        						array(
        								'taxonomy' => $tax,
        								'field' => 'slug',
        								'terms' => array('all-access'),
        								'operator' => 'NOT IN'
        						)
        				),
        				'post__not_in' => $exclude,
        				'paged' => $page
        		);
        	}
        } else {
        	//if main category landing page
        	if($post_type == 'fashion') {
        		$args = array(
        				'post_type' => $post_type,
        				'posts_per_page' => $posts_per_page,
        				'tax_query'	=> array(
        						array(
        								'taxonomy'  => $tax,
        								'field'     => 'slug',
        								'terms'     => 'all-access',
        								'operator'  => 'NOT IN'
        						)
        				),
        				'post__not_in' => $exclude,
        				'paged' => $page
        		);
        	} else {
        		$args = array(
        				'post_type' => $post_type,
        				'posts_per_page' => $posts_per_page,
        				'post__not_in' => $exclude,
        				'paged' => $page
        		);
        	}

        }
        $wp_query = new WP_Query;
        $posts = $wp_query->query($args);
        $max_pages  = $wp_query->max_num_pages;

        /*
        if(count($exclude) < count($posts)) {
        	$args = array(
        			'post_type' => $post_type,
        			'posts_per_page' => $exclude,
        			'post__not_in' => $exclude,
        			'paged' => $page
        	);
        	$imperfectQue = new WP_Query($args);
        	$max_pages  = $imperfectQue->max_num_pages;
        	//echo 'conditional: '.$max_pages;
        }
		*/

        $more_posts = ($max_pages > 1 && $max_pages != $page) ? "true" : "false";
        $posts_array = array();

        //iterate and grab needed info from each posts

        foreach ($posts as $p) {

            $category = '';
            $id = $p->ID;

            $category = self::get_custom_post_category($post_type,$p);
            //get permalink
            $permalink = get_permalink($p->ID);
            $permalink = empty($permalink) ? '' : $permalink;
            //get featured image
			if($category == 'Celebrities' || $category == 'celebrity-tag') {
				$featured_img_full 		= self::get_featured_image($p,'2/3-image-with-text');
				$featured_img_medium 	= self::get_featured_image($p,'large-thumbnail');
				$featured_img_small 	= self::get_featured_image($p,'small-thumbnail');
				$featured_img_half 	= self::get_featured_image($p,'1/2-celebrity');
			} else {
				$featured_img_full 		= self::get_featured_image($p,'1/1-image-with-text+hero');
				$featured_img_medium 	= self::get_featured_image($p,'large-thumbnail');
				$featured_img_half 	= self::get_featured_image($p,'1/2-image-with-text');
			}

            $post_type = get_post_type($p);
            $category_landscape_img = MultiPostThumbnails::get_post_thumbnail_url($post_type, 'category-landscape-image', $p->ID);

            //if no category landscape image inserted, use full image
            if (empty($category_landscape_img)) {
                $category_landscape_img = $featured_img_full;
            }

            $p_array = array( 'category' 					=> $category
                            , 'permalink'					=> $permalink
                            , 'featured_img_full'			=> $featured_img_full
                            , 'featured_img_medium'			=> $featured_img_medium
            				, 'featured_img_half'			=> $featured_img_half
                            , 'category_landscape_img'		=> $category_landscape_img
                            , 'title'						=> $p->post_title
                            , 'post_id'						=> $p->ID
                            );

            array_push($posts_array, $p_array);
        }

        return array('posts' => $posts_array
                    , 'more_posts' => $more_posts
                    );


    }

    /**
     * get posts for category/subcategory landing pages.
     *
     * @param string $post_type
     * @param string $category
     * @param string $posts_per_age
     * @param int $page
     * @return array
     */
    public static function get_archive_posts($post_type,$category,$posts_per_page = 6, $page = 0, $exclude) {

        //build custom taxonomy
        $tax = $post_type . "-category";

        $exclude = implode(",", $exclude);
        //build query arguments array with post type and custom taxonomy category
        $args = array(
                    'post_type' => $post_type,
                    'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                    'taxonomy' => $tax,
                                    'field' => 'slug',
                                    'terms' => array($category)
                            ),
                            array(
                                    'taxonomy' => $tax,
                                    'field' => 'slug',
                                    'terms' => array('all-access'),
                                    'operator' => 'NOT IN'
                            )
                    ),
                    'posts_per_page' => $posts_per_page,
                    'paged' => $page
                );
        //$posts = get_posts($args);

        $wp_query = new WP_Query;
        $posts = $wp_query->query($args);

        $max_pages  = $wp_query->max_num_pages;
        $more_posts = ($max_pages > 1 && $max_pages != $page) ? "true" : "false";

        $posts_array = array();

        //iterate and grab needed info from each posts

        foreach ($posts as $p) {
            $id = $p->ID;
            $category = get_post_meta($id, 'category_heading', true);

            if ($category == '') {
                $category = self::get_custom_post_category($post_type,$p);
            } else {
                $category = get_post_meta($id, 'category_heading', true);
            }

            //get permalink
            $permalink = get_permalink($p->ID);
            $permalink = empty($permalink) ? '' : $permalink;
            //get featured image

            $featured_img_full      = self::get_featured_image($p,'2/3-image-with-text');
            $featured_img_medium    = self::get_featured_image($p,'large-thumbnail');

            $post_type = get_post_type($p);
            $category_landscape_img = MultiPostThumbnails::get_post_thumbnail_url($post_type, 'category-landscape-image', $p->ID);

            //if no category landscape image inserted, use full image
            if (empty($category_landscape_img)) {
                $category_landscape_img = $featured_img_full;
            }

            $p_array = array( 'category'                    => $category
                            , 'permalink'                   => $permalink
                            , 'featured_img_full'           => $featured_img_full
                            , 'featured_img_medium'         => $featured_img_medium
                            , 'category_landscape_img'      => $category_landscape_img
                            , 'title'                       => $p->post_title
                            , 'post_id'                     => $p->ID
                            );

            array_push($posts_array, $p_array);
        }

        return array('posts' => $posts_array
                    , 'more_posts' => $more_posts
                    );


    }

    /**
     * get posts for tag landing pages.
     *
     * @param string $post_type
     * @param string $category
     * @param string $posts_per_age
     * @param int $page
     * @return array
     */
    public static function get_tag_posts($locations,$posts_per_page = 6, $page = 0) {

        //build custom taxonomy

        //build query arguments array with post type and custom taxonomy category
        $args = array(
                    'tag' => $locations,
                    'posts_per_page' => $posts_per_page,
                    'paged' => $page
                );
        //$posts = get_posts($args);
        
        $wp_query = new WP_Query;
        $posts = $wp_query->query($args);
                
        $max_pages  = $wp_query->max_num_pages;
        $more_posts = ($max_pages > 1 && $max_pages != $page) ? "true" : "false";

        $posts_array = array();

        //iterate and grab needed info from each posts
        foreach ($posts as $p) {

            $category = '';
            $id = $p->ID;
            $post_type = get_post_type($p);
            $category = self::get_custom_post_category($post_type,$p);

            //get permalink
            $permalink = get_permalink($p->ID);
            $permalink = empty($permalink) ? '' : $permalink;
            //get featured image

            $featured_img_full      = self::get_featured_image($p,'2/3-image-with-text');
            $featured_img_medium    = self::get_featured_image($p,'large-thumbnail');

            $category_landscape_img = MultiPostThumbnails::get_post_thumbnail_url($post_type, 'category-landscape-image', $p->ID);

            //if no category landscape image inserted, use full image
            if (empty($category_landscape_img)) {
                $category_landscape_img = $featured_img_full;
            }

            $p_array = array( 'category'                    => $category
                            , 'permalink'                   => $permalink
                            , 'featured_img_full'           => $featured_img_full
                            , 'featured_img_medium'         => $featured_img_medium
                            , 'category_landscape_img'      => $category_landscape_img
                            , 'title'                       => $p->post_title
                            , 'post_id'                     => $p->ID
                            );

            array_push($posts_array, $p_array);
        }
        
        return array('posts' => $posts_array
                    , 'more_posts' => $more_posts
                    );


    }

    /**
     * get caption for post thumbnail.
     *
     * @param object $post
     * @return string;
     */
    public static function get_post_thumbnail_caption($post) {

      $thumbnail_id    = get_post_thumbnail_id($post->ID);
      $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

      if ($thumbnail_image && isset($thumbnail_image[0])) {
        return $thumbnail_image[0]->post_excerpt;
      } else {
        return "";
      }
    }


    /**
     * get post featured image
     *
     * @param object $post
     * @return string;
     */
    public static function get_featured_image($post,$size="default-thumbnail") {
        $thumbID = get_post_thumbnail_id($post->ID);
        $featured_img = wp_get_attachment_image_src($thumbID, $size);
        $full_img = wp_get_attachment_image_src($thumbID, "2/3-image-with-text");
        $featured_img = empty($featured_img) ? $full_img : $featured_img[0];

        return $featured_img;
    }
    /**
     *
     * @param unknown $raw
     * @return unknown
     */
    public static function get_saved_cta_value($raw) {
        global $wpdb;
        $cta_list = array();
        $count = 0;
        $fields = $wpdb->get_results("
                SELECT option_name, option_value
                FROM $wpdb->options
                WHERE option_name LIKE 'CTA_%'
                ORDER BY CAST(SUBSTRING(option_name,LOCATE('_', option_name)+1) AS SIGNED)"
                    );
        foreach ($fields as $field) {
            if (!empty($field->option_value)) {
                $cta_list = array_merge($cta_list, array($field->option_value => $field->option_value));
                $count++;
            }
        }
        if($raw > '0') return $cta_list;
        else if ($raw < '0') return $count;
        else return $fields;

    }
    public static function admin_cta_form() {
        $values = self::get_saved_cta_value(0);
        ?>
        <div class="wrap">
            <div id='icon-options-general' class='icon32'><br /></div>
            <h2>CTA Label setting</h2>

            <form method="post" action="options.php">
                <?php settings_fields( 'theme-settings-group' ); ?>
                <?php do_settings_sections( 'theme-settings-group' ); ?>
                <div class="form-table" id="here">
                    <?php
                    $c = 1;
                    foreach ($values as $field) {
                        if (!empty($field->option_value)) {
                            echo '<div class="ctaField">';
                            echo '<span class="label">' .$field->option_name. ' label name</span>';
                            echo '<input class="counting" type="text" name="' . $field->option_name .'" value="' . $field->option_value . '" />';
                            echo '<input class="remove button" type="button" value="Delete" />';
                            echo '</div>';
                            $c++;
                        }
                    }
                    ?>
                </div>
                <div class="addNew">
                    <span class="label">Add Another Field</span>
                    <input class="button tagadd add" type="button" value="<?php _e('Add a field'); ?>" />
                </div>
                <script>
                var $ =jQuery.noConflict();
                $(document).ready(function() {
                    var count = <?php echo $c; ?>;
                    var name = <?php echo $t;?>
                    $(".add").click(function() {
                        $('#here').append(
                            '<div class="ctaField"><span class="label">CTA_'+count+' label name</span><input class="counting" type="text" name="CTA_'+count+'" value=" " /><input class="remove button" type="button" value="Delete" />'
                        );
                        count = count + 1;

                        return false;
                    });
                    $(".remove").live('click', function() {
                        $(this).parent().remove();
                    });
                });
                </script>
                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }
    public static function register_cta_setting() {
        $counting = self::get_saved_cta_value(-1);
        for ($i=-4; $i<=$counting; $i++) {
            $num = $i + 5;
            $optionName = 'CTA_'.$num;
            register_setting( 'theme-settings-group', $optionName );
        }
    }

    /**
     * list of location posts
     *
     * TODO: query using term group.
     * TODO: need to add term group when user is adding location
     *
     * @return string
     */
    public static function get_location_list($wraper, $class) {
        $locations = get_tags();

        $out = '<'.$wraper.' class="'.$class.'">';
        foreach ($locations as $tag) {
            /*
            if (posts_per_tag($tag->term_id, $post_type) > 0) {
                $out .= '<br/>echoing tag: ' . $tag->name;
            }
            */
            $tag_link = get_tag_link($tag->term_id);



            $out .= '<li>';
            $out .= "<a href='{$tag_link}' title='Location {$tag->name}' class='location {$tag->slug}'>";
            $out .= "{$tag->name}</a>";
            $out .= '</li>';
        }
        $out .= '</'.$wraper.'>';

        return $out;
    }
    /**
     *
     * @param unknown $id
     * @param unknown $post_type
     */
    function posts_per_tag($id, $post_type) {

        $args = array(
                'post_type' => array($post_type),
                'posts_per_page' => -1,
                'tag_id' => $id
        );

        $the_query = new WP_Query( $args );
        wp_reset_query();

        return sizeof($the_query->posts);
    }

    public static function get_subcat_list($wraper, $class, $que) {
        $out = '<'.$wraper.' class="'.$class.'">';

        foreach ($que as $p) {
            $out .= '<li>';
            $out .= "<a href='{$p->guid}' title='{$p->post_title}' class='location {$p->post_name}'>";
            $out .= "{$p->post_title}</a>";
            $out .= '</li>';
        }

        $out .= '</'.$wraper.'>';

        return $out;
    }
    //
    /**
     * get list of posts in same category
     * for single post
     *
     * @param string $wraper
     * @param string $class
     * @param int $id
     * @param string $term
     * @param string $type
     * @return string
     */
    public static function get_post_list_from_single($default, $class, $id, $term, $type) {
        $tax = $type . '-category';
        $list_query = new WP_Query(array(
            //'post__not_in' => array($id),
            'post_type' => $type,
            'posts_per_page' => 9999,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                        'taxonomy' => $tax,
                        'field' => 'slug',
                        'terms' => $term
                ),
                array(
                        'taxonomy' => $tax,
                        'field' => 'slug',
                        'terms' => 'all-access',
                        'operator' => 'NOT IN'
                )
            )
        ));

        $out = '<div class="selector"><a href="/runway/'.$term.'" style="padding:0;"><span>' . $default .'</span></a><div class="selector-icon"></div></div>';
        $out .= '<ul class="'.$class.'">';
        while ($list_query->have_posts()) : $list_query->the_post();
        	if($list_query->post->ID == $id) $active = 'active'; else $active = '';
        	$catHead = get_post_meta($list_query->post->ID, 'category_heading', true);
            $out .= '<li class="'.$active.'">';
            $out .= "<a href='";
            $out .= get_permalink();
            $out .= "' class='".$active."' title='' class='menu'>";
            $out .= get_the_title();
            if($catHead) {
            	$out .= ' - ';
            	$out .= $catHead;
            }
            $out .= "</a>";
            $out .= '</li>';
        endwhile;
        wp_reset_postdata();
        $out .= '</ul>';

        return $out;
    }
    /**
     * Get a posts list by taxonomy
     * for category landing page
     *
     * @param string $default
     * @param string $class
     * @param string $term
     * @param string $type
     * @param mixed $exclude
     * @return string
     */
    public static function get_post_list_from_landing($default, $class, $term, $type, $exclude) {
        $tax = $type . '-category';
        //$term_list = wp_get_post_terms($post->ID, $tax, array("fields" => "all"));
        //$termchildren = get_term_children($term_list[0]->term_id, $tax );

        $list_query = new WP_Query(array(
                'post_type' => $type,
                'posts_per_page' => 99999,
                'tax_query' => array(
                        'relation' => 'AND',
                        array(
                                'taxonomy' => $tax,
                                'field' => 'slug',
                                'terms' => array($term)
                        ),
                        array(
                                'taxonomy' => $tax,
                                'field' => 'slug',
                                'terms' => $exclude,
                                'operator' => 'NOT IN'
                        )
                )
        ));
        $out = "<div class='selector'><a style='padding:0;' href='".get_bloginfo('url'). "/runway/" .$term. "/'>
                <span>" . $default ."</span></a><div class='selector-icon'></div></div>";
        $out .= "<ul class='".$class."'>";
        while ($list_query->have_posts()) : $list_query->the_post();
            $out .= "<li>";
            $out .= "<a href='";
            $out .= get_permalink();
            $out .= "' title='' class='menu'>";
            $out .= get_the_title();
            if(get_post_meta($list_query->post->ID, 'category_heading', true)) {
				$out .= ' - ';
            	$out .= get_post_meta($list_query->post->ID, 'category_heading', true);
			}
            $out .= "</a>";
            $out .= "</li>";
        endwhile;
        $out .= "</ul>";
        wp_reset_postdata();

        return $out;

    }

    /**^
     * get list of child categories
     * for categories that have sub categories
     *
     * @param string $default
     * @param string $class
     * @param string $term
     * @param string $type
     * @return string
     */
    public static function get_child_category_from_single($default, $class, $term, $type) {
        $tax = $type . '-category';
        $pTerm = get_term_by('slug', $term, $tax);
        $termchildren = get_term_children($pTerm->term_id, $tax);
        $href = (is_archive() ? "/michaels-edit/spotlight-on" : get_term_link((int) $pTerm->term_id, $tax));

        $out = "<div class='selector'><a style='padding:0;' href='". $href ."'>
                <span>" . $default ."</span></a><div class='selector-icon'></div></div>";
        $out .= "<ul class='".$class."'>";
        foreach ($termchildren as $child) {
            $term = get_term_by('id', $child, $tax);
            if($term -> count > 0 )
            	$out .= '<li><a href="' . get_term_link($child, $tax) . '">' . $term->name . '</a></li>';
        }
        $out .= "</ul>";

        return $out;
    }

    /**
     * get attachment id from image url
     *
     * @param string $attachment_url
     * @return void|Ambigous <boolean, string, NULL>
     */
    public static function get_attachment_id_from_url($attachment_url = '') {

        global $wpdb;
        $attachment_id = false;
        $attachment_url = parse_url($attachment_url);
        $adds = $attachment_url['path'];
        $attachment_url = '%' . $adds;
        if ($attachment_url == '') {
            return;
        } else {
            $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT p.ID
                                            FROM $wpdb->posts as p
                                            WHERE p.guid LIKE '%s' AND p.post_type = 'attachment'"
                                            , $attachment_url));
        }

        return $attachment_id;
    }

    public static function sweeps_list() {

    	$list_query = new WP_Query(array('nopaging'=>true, 'orderby'=>'title', 'order'=>'ASC', 'post_type'=>'sweeps'));

    	$meta_head = get_post_meta(12934, 'sweeps_heading', true);
    	$meta_desc = get_post_meta(12934, 'sweeps_description', true);
        $meta_disclaimer = get_post_meta(12934, 'sweeps_disclaimer', true);

    	if(empty($meta_head)) $meta_head = 'Please verify your country';
    	if(empty($meta_desc)) $meta_desc = 'At this time Sweepstakes are only offered in some countries.';
        if(empty($meta_disclaimer)) $meta_disclaimer = '';

    	$out = '<div class="modal-overlay">
					<div class="language-modal" id="language-modal">
						<div class="inner-wrapper">
							<h1>' . $meta_head . '</h1>';
    	$out .= $meta_desc .
					'<div class="select-wrapper">
						<select id="country">';
    	while ($list_query->have_posts()) : $list_query->the_post();

        if (get_the_title() == 'United States') $selected = "selected";
        else $selected = null;

    	$out .= '<option value="';
    	$out .= get_permalink(). '"'. $selected .'>'. get_the_title() .'</option>';
    	endwhile;
    	$out .= '</select>
					<div class="chevron"><img src="/wp-content/themes/destinationkors/static/dist/images/icon-arrow-right.png" /></div>
						</div>
							<input type="submit" value="Submit" class="submit-button" />
                    		<br/>
                            '.$meta_disclaimer.'
                            </div>
							</div>
							</div>';
    	return $out;

    }
}

