<?php

class JSON_API_Introspector {
    public function get_date_archive_permalinks() {
        $archives = wp_get_archives('echo=0');
        preg_match_all("/href='([^']+)'/", $archives, $matches);

        return $matches[1];
    }

    public function get_date_archive_tree($permalinks) {
        $tree = array();
        foreach ($permalinks as $url) {
            if (preg_match('#(\d{4})/(\d{2})#', $url, $date)) {
                $year = $date[1];
                $month = $date[2];
            } else if (preg_match('/(\d{4})(\d{2})/', $url, $date)) {
                $year = $date[1];
                $month = $date[2];
            } else {
                continue;
            }
            $count = $this->get_date_archive_count($year, $month);
            if (empty($tree[$year])) {
                $tree[$year] = array(
                        $month => $count
                );
            } else {
                $tree[$year][$month] = $count;
            }
        }

        return $tree;
    }

    public function get_date_archive_count($year, $month) {
        if (!isset($this->month_archives)) {
            global $wpdb;
            $post_counts = $wpdb->get_results("
                    SELECT DATE_FORMAT(post_date, '%Y%m') AS month,
                    COUNT(ID) AS post_count
                    FROM $wpdb->posts
                    WHERE post_status = 'publish'
                    AND post_type = 'post'
                    GROUP BY month
                    ");
                    $this->month_archives = array();
                    foreach ($post_counts as $post_count) {
                    $this->month_archives[$post_count->month] = $post_count->post_count;
            }
            }

            return $this->month_archives["$year$month"];
    }
    public function get_categories($args = null) {
        $wp_categories = get_categories($args);
        $categories = array();
        foreach ($wp_categories as $wp_category) {
            if ($wp_category->term_id == 1 && $wp_category->slug == 'uncategorized') {
                continue;
            }
            $categories[] = $this->get_category_object($wp_category);
        }

        return $categories;
    }
    //public function get_menu()

    protected function get_category_object($wp_category) {
        if (!$wp_category) {
            return null;
        }

        return new JSON_API_Category($wp_category);
    }
    /**
     * putting in the loop and get post content
     *
     * @param string $query
     * @param string $wp_posts
     * @return multitype:JSON_API_Post
     */
    public function get_posts($query = false, $wp_posts = false) {
        global $post, $wp_query;
        $this->set_posts_query($query);
        $output = array();
        while (have_posts()) {
            the_post();
            if ($wp_posts) {
                $new_post = $post;
            } else {
                // let's not make any fancy format...this is taking too long
                $new_post = new JSON_API_Post($post, false);
            }
            $output[] = $new_post;
        }

        return $output;
    }
    public function get_geo_posts() {
        global $json_api;
        global $post, $wp_query;
        extract($json_api->query->get(array('city')));

        if ($city) {
        	if($city == 'global') {
        		$que = array(
        				'post_type' => array('jet', 'mks-edit', 'fashion', 'kors-cares'),
        				'posts_per_page' => -1, // no paging... :S if we have hundreds posts then this will kill server....
        				'meta_query' => array(
						    array(
						        'key' => 'geo_city',
						        'value'   => array(''),
						        'compare' => 'NOT IN'
						    )
						)
	        		);
        	} else {
        		$que = array(
        				'post_type' => array('jet', 'mks-edit', 'fashion', 'kors-cares'),
        				'posts_per_page' => -1, // no paging... :S if we have hundreds posts then this will kill server....
        				//'meta_key' => 'geo_city',
        				//'meta_value' => $city

        				'meta_query' => array(
        							'relation' => 'OR',
        							array(
        								'key' => 'geo_city',
        								'value' => $city
        							),
	        						array(
	        							'key' => 'geo_city_extra',
	        							'value' => $city,
	        							//'compare' => 'LIKE'
	        						),

        						)


        		);
        	}
            $this->set_posts_query($que);
            $output = array();
            while (have_posts()) {
                the_post();
                if ($wp_posts) {
                    $new_post = $post;
                } else {
                    // let's not make any fancy format...this is taking too long
                    $new_post = new JSON_API_Post($post, false);
                }
                $output[] = $new_post;
            }

            return $output;
        } else {
            $json_api->error("Include 'city name' var in your request.");
        }
    }
    public function get_current_post() {
        global $json_api;
        extract($json_api->query->get(array('id', 'slug', 'post_id', 'post_slug')));

        if ($id || $post_id) {
            if (!$id) {
                $id = $post_id;
            }
            $posts = $this->get_posts(array(
                    'p' => $id,
                    'post_type' => array('mks-edit','jet','fashion', 'page','kors-cares', 'sweeps'),
                    'posts_per_page' => -1
            ), true);
        } else if ($slug || $post_slug) {
            if (!$slug) {
                $slug = $post_slug;
            }
            $posts = $this->get_posts(array(
                    'name' => $slug,
                    'post_type' => array('mks-edit','jet','fashion','kors-cares', 'sweeps'),
                    'posts_per_page' => -1
            ), true);
        } else {
            $json_api->error("Include 'id' or 'slug' var in your request.");
        }
        if (!empty($posts)) {
            return $posts[0];
        } else {
            return null;
        }
    }
    /**
     * set up the post query..
     * internal use only
     *
     * @param string $query
     */
    protected function set_posts_query($query = false) {
        global $json_api, $wp_query;

        if (!$query) {
            $query = array();
        }
        $query = array_merge($query, $wp_query->query);

        if ($json_api->query->page) {
            $query['paged'] = $json_api->query->page;
        }

        if ($json_api->query->count) {
            $query['posts_per_page'] = $json_api->query->count;
        }

        if ($json_api->query->post_type) {
            $query['post_type'] = $json_api->query->post_type;
        }
        if (!empty($query)) {
            query_posts($query);
            do_action('json_api_query', $wp_query);
        }
    }
    /**
     * note: couldn't find wp api for getting author by id..
     *
     * @param unknown $login
     */
    public function get_author_by_login($login) {
        global $wpdb;
        $id = $wpdb->get_var($wpdb->prepare("
                SELECT ID
                FROM $wpdb->users
                WHERE user_nicename = %s
                ", $login));

                return $this->get_author_by_id($id);
    }

    /**
     *
     * @param unknown $id
     * @return NULL | JSON_API_Author
     */
    public function get_author_by_id($id) {
        $id = get_the_author_meta('ID', $id);
        if (!$id) {
            return null;
        }

        return new JSON_API_Author($id);
    }
    /**
     *
     * @param unknown $category_slug
     * @return Ambigous <NULL, JSON_API_Category>
     */
    public function get_category_by_slug($category_slug) {
        $wp_category = get_term_by('slug', $category_slug, 'category');

        return $this->get_category_object($wp_category);
    }

    /**
     *
     * @param unknown $post_id
     * @return multitype:JSON_API_Attachment
     */
    public function get_attachments($post_id) {
        $wp_attachments = get_children(array(
                'post_type' => 'attachment',
                'post_parent' => $post_id,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'suppress_filters' => false
        ));
        $attachments = array();
        if (!empty($wp_attachments)) {
            foreach ($wp_attachments as $wp_attachment) {
                $attachments[] = new JSON_API_Attachment($wp_attachment);
            }
        }

        return $attachments;
    }
    public function get_current_category() {
        global $json_api;
        extract($json_api->query->get(array('id', 'slug', 'category_id', 'category_slug')));
        if ($id || $category_id) {
            if (!$id) {
                $id = $category_id;
            }

            return $this->get_category_by_id($id);
        } else if ($slug || $category_slug) {
            if (!$slug) {
                $slug = $category_slug;
            }

            return $this->get_category_by_slug($slug);
        } else {
            $json_api->error("Include 'id' or 'slug' var in your request.");
        }

        return null;
    }
    public function get_category_by_id($category_id) {
        $wp_category = get_term_by('id', $category_id, 'category');

        return $this->get_category_object($wp_category);
    }
    public function get_tags() {
        $wp_tags = get_tags();

        return array_map(array(&$this, 'get_tag_object'), $wp_tags);
    }

    public function get_current_tag() {
        global $json_api;
        extract($json_api->query->get(array('id', 'slug', 'tag_id', 'tag_slug')));
        if ($id || $tag_id) {
            if (!$id) {
                $id = $tag_id;
            }

            return $this->get_tag_by_id($id);
        } else if ($slug || $tag_slug) {
            if (!$slug) {
                $slug = $tag_slug;
            }

            return $this->get_tag_by_slug($slug);
        } else {
            $json_api->error("Include 'id' or 'slug' var in your request.");
        }

        return null;
    }

    public function get_tag_by_id($tag_id) {
        $wp_tag = get_term_by('id', $tag_id, 'post_tag');

        return $this->get_tag_object($wp_tag);
    }

    public function get_tag_by_slug($tag_slug) {
        $wp_tag = get_term_by('slug', $tag_slug, 'post_tag');

        return $this->get_tag_object($wp_tag);
    }

    public function get_authors() {
        global $wpdb;
        $author_ids = $wpdb->get_col("
            SELECT u.ID, m.meta_value AS last_name
            FROM $wpdb->users AS u,
            $wpdb->usermeta AS m
            WHERE m.user_id = u.ID
            AND m.meta_key = 'last_name'
            ORDER BY last_name
        ");
        $all_authors = array_map(array(&$this, 'get_author_by_id'), $author_ids);
        $active_authors = array_filter($all_authors, array(&$this, 'is_active_author'));

        return $active_authors;
    }

    public function get_current_author() {
        global $json_api;
        extract($json_api->query->get(array('id', 'slug', 'author_id', 'author_slug')));
        if ($id || $author_id) {
            if (!$id) {
                $id = $author_id;
            }

            return $this->get_author_by_id($id);
        } else if ($slug || $author_slug) {
            if (!$slug) {
                $slug = $author_slug;
            }

            return $this->get_author_by_login($slug);
        } else {
            $json_api->error("Include 'id' or 'slug' var in your request.");
        }

        return null;
    }

    public function get_comments($post_id) {
        global $wpdb;
        $wp_comments = $wpdb->get_results($wpdb->prepare("
            SELECT *
            FROM $wpdb->comments
            WHERE comment_post_ID = %d
            AND comment_approved = 1
            AND comment_type = ''
            ORDER BY comment_date
            ", $post_id));
        $comments = array();
        foreach ($wp_comments as $wp_comment) {
            $comments[] = new JSON_API_Comment($wp_comment);
        }

        return $comments;
    }

    public function get_attachment($attachment_id) {
        global $wpdb;
        $wp_attachment = $wpdb->get_row(
            $wpdb->prepare("
                SELECT *
                FROM $wpdb->posts
                WHERE ID = %d
                ", $attachment_id)
        );

        return new JSON_API_Attachment($wp_attachment);
    }

    public function attach_child_posts(&$post) {
        $post->children = array();
        $wp_children = get_posts(array(
            'post_type' => $post->type,
            'post_parent' => $post->id,
            'order' => 'ASC',
            'orderby' => 'menu_order',
              'numberposts' => -1,
            'suppress_filters' => false
        ));
        foreach ($wp_children as $wp_post) {
            $new_post = new JSON_API_Post($wp_post);
            $new_post->parent = $post->id;
            $post->children[] = $new_post;
        }
        foreach ($post->children as $child) {
            $this->attach_child_posts($child);
        }
    }
    /**
     * NOTE : short array and fucntion array dereferencing is not supported in PHP 5.3. Re-coded for 5.3
     * TODO : currently post counting is not working. get post_type meta value by term_ID and get count by meta value as category for landing
     * 		: get category meta value by term_ID and count by meta value as category for sub-landing
     *
     * @param string $menu_slug
     * @param array $args
     * @return array
     */
    public function get_nav_items($menu_slug, $args) {
        global $post;
        if (($locations = get_nav_menu_locations()) && isset( $locations[$menu_slug])) {
            $menu = wp_get_nav_menu_object($locations[$menu_slug]);
        } else {
            return 'Menu name is not defined.';
            break;
        }

        $menu_items = wp_get_nav_menu_items($menu->term_id, $args );
        $current_menu_id = 0;

        foreach ($menu_items as $item) {
            if ($item->object_id == $post->ID) {
                $current_menu_id = ( $item->menu_item_parent ) ? $item->menu_item_parent : $item->ID;
                break;
            }
        }
        // display the submenu
        foreach ($menu_items as $key => $item) {
            if ($item->menu_item_parent == $current_menu_id) {
                $out[$item->title]['name'] = $item->title;
                $out[$item->title]['slug'] = $item->attr_title;
                $out[$item->title]['link'] = $item->url;
                $out[$item->title]['description'] = $item->description;
                $out[$item->title]['post_count'] = '';
                $out[$item->title]['menu_order'] = $item->menu_order;
                $out[$item->title]['type'] = "Landing Page";

                $sub_menu_items = array();

                foreach ($menu_items as $s_item) {
                    if ( $s_item->menu_item_parent == $item->ID ) $sub_menu_items[] = $s_item;
                }

                if ($sub_menu_items) {
                    foreach ($sub_menu_items as $k => $sub_item) {
                        $out[$item->title]['subcategories'][$k]['name'] = $sub_item->title;
                        $out[$item->title]['subcategories'][$k]['slug'] = $sub_item->attr_title;
                        $out[$item->title]['subcategories'][$k]['link'] = $sub_item->url;
                        $out[$item->title]['subcategories'][$k]['description'] = $sub_item->description;
                        $out[$item->title]['subcategories'][$k]['post_count'] = '';
                        $out[$item->title]['subcategories'][$k]['menu_order'] = $sub_item->menu_order;
                        $out[$item->title]['subcategories'][$k]['type'] = 'Sub-landing Page';
                    }

                }
            }
        }

        return $out;
    }

    protected function get_tag_object($wp_tag) {
        if (!$wp_tag) {
            return null;
        }

        return new JSON_API_Tag($wp_tag);
    }

    protected function is_active_author($author) {
        if (!isset($this->active_authors)) {
            $this->active_authors = explode(',', wp_list_authors(array(
                'html' => false,
                'echo' => false,
                'exclude_admin' => false
            )));
            $this->active_authors = array_map('trim', $this->active_authors);
        }

        return in_array($author->name, $this->active_authors);
    }
}

?>
