<?php
/**
 *
 * @author juhonglee
 *
 * this class objects must be instantiated within the loop.
 * TODO: make sure all necessary data is included.
 *
 */
class JSON_API_Post {
    var $id; // int
    var $type;
    var $slug;
    var $url;
    var $status; // string : draft, published, pending
    var $title;
    var $content; // modified by read_more query var
    var $date;
    var $modified;
    var $categories;
    var $tags;
    //var $author;
    var $attachments;
    var $thumbnail;
    // really optional.
    //var $custom_fields;
    //var $comments; // array don't need it but adding it just in case
    //var $comment_count;
    //var $comment_status;

    /**
     * constructor
     *
     * @param string $wp_post
     */
    function JSON_API_Post($wp_post = null, $fancy = true) {
        if (!empty($wp_post) && $fancy == true) {
            $this->import_wp_object($wp_post);
        } else {
        	// for trending geo data
            $this->quick_import_wp_object($wp_post);
        }
        //do_action("json_api_{$this->type}_constructor", $this);
    }
    function create($values = null) {
        unset($values['id']);
        if (empty($values) || empty($values['title'])) {
            $values = array(
                'title' => 'Untitled',
                'content' => ''
            );
        }

        return $this->save($values);
    }
    function update($values) {
        $values['id'] = $this->id;

        return $this->save($values);
    }
    function save($values = null) {
        global $json_api, $user_ID;

        $wp_values = array();

        if (!empty($values['id'])) {
            $wp_values['ID'] = $values['id'];
        }

        if (!empty($values['type'])) {
            $wp_values['post_type'] = $values['type'];
        }

        if (!empty($values['status'])) {
            $wp_values['post_status'] = $values['status'];
        }

        if (!empty($values['title'])) {
            $wp_values['post_title'] = $values['title'];
        }

        if (!empty($values['content'])) {
            $wp_values['post_content'] = $values['content'];
        }

        if (!empty($values['author'])) {
            $author = $json_api->introspector->get_author_by_login($values['author']);
            $wp_values['post_author'] = $author->id;
        }

        if (isset($values['categories'])) {
            $categories = explode(',', $values['categories']);
            foreach ($categories as $category_slug) {
                $category_slug = trim($category_slug);
                $category = $json_api->introspector->get_category_by_slug($category_slug);
                if (empty($wp_values['post_category'])) {
                    $wp_values['post_category'] = array($category->id);
                } else {
                    array_push($wp_values['post_category'], $category->id);
                }
            }
        }

        if (isset($values['tags'])) {
            $tags = explode(',', $values['tags']);
            foreach ($tags as $tag_slug) {
                $tag_slug = trim($tag_slug);
                if (empty($wp_values['tags_input'])) {
                    $wp_values['tags_input'] = array($tag_slug);
                } else {
                    array_push($wp_values['tags_input'], $tag_slug);
                }
            }
        }
        if (isset($wp_values['ID'])) {
            $this->id = wp_update_post($wp_values);
        } else {
            $this->id = wp_insert_post($wp_values);
        }

        if (!empty($_FILES['attachment'])) {
            include_once ABSPATH . '/wp-admin/includes/file.php';
            include_once ABSPATH . '/wp-admin/includes/media.php';
            include_once ABSPATH . '/wp-admin/includes/image.php';
            $attachment_id = media_handle_upload('attachment', $this->id);
            $this->attachments[] = new JSON_API_Attachment($attachment_id);
            unset($_FILES['attachment']);
        }


        $wp_post = get_post($this->id);
        $this->import_wp_object($wp_post);

        return $this->id;
    }

    function import_wp_object($wp_post) {
        global $json_api, $post;
        $date_format = $json_api->query->date_format;
        $this->id = (int) $wp_post->ID;
        setup_postdata($wp_post);
        $this->set_value('type', $wp_post->post_type);
        $this->set_value('slug', $wp_post->post_name);
        $this->set_value('url', get_permalink($this->id));
        $this->set_value('status', $wp_post->post_status);
        $this->set_value('title', get_the_title($this->id));
        $this->set_value('title_plain', strip_tags(@$this->title));
        $this->set_content_value();
        $this->set_value('excerpt', apply_filters('the_excerpt', get_the_excerpt()));
        $this->set_value('date', get_the_time($date_format));
        $this->set_value('modified', date($date_format, strtotime($wp_post->post_modified)));
        $this->set_categories_value();
        $this->set_tags_value();
        $this->set_author_value($wp_post->post_author);
        $this->set_comments_value();
        $this->set_attachments_value();
        $this->set_value('comment_count', (int) $wp_post->comment_count);
        $this->set_value('comment_status', $wp_post->comment_status);
        $this->set_thumbnail_value();
        $this->set_custom_fields_value();
        $this->set_custom_taxonomies($wp_post->post_type);
        do_action("json_api_import_wp_post", $this, $wp_post);
    }

    // not facny version..for quering speed
    function quick_import_wp_object($wp_post) {
        global $json_api, $post;
        // $start = microtime(true);
        $date_format = $json_api->query->date_format;
        unset($this->type);
        unset($this->status);
        unset($this->attachments);
        unset($this->thumbnail);
        unset($this->thumbnail_size);
        $this->id = (int) $wp_post->ID;
        setup_postdata($wp_post);
        $this->set_value('slug', $wp_post->post_name);
        //unset($this->slug);
        $this->set_value('url', get_permalink($this->id));
        $this->set_value('title', get_the_title($this->id));
        $this->set_value('latitude', get_post_meta($this->id, 'geo_latitudes', true ));
        $this->set_value('longitude', get_post_meta($this->id, 'geo_longitudes', true ));
        $this->set_city_val('city', $this->id);
        $this->set_content_value_with_seo($this->id);
        $this->set_value('date', get_the_time($date_format));
        $this->set_value('modified', date($date_format, strtotime($wp_post->post_modified)));
        $this->mk_set_category_value($wp_post->post_type);
        $this->set_tags_value();
        $this->set_trending_thumbnail_value();
        //$this->set_thumbnail_value();

        //do_action("json_api_import_wp_post", $this, $wp_post);
        // $time_taken = microtime(true) - $start;
        // echo $time_taken;
    }

    function set_value($key, $value) {
        global $json_api;
        if ($json_api->include_value($key)) {
            $this->$key = $value;
        } else {
            unset($this->$key);
        }
    }
	function set_city_val($key, $id) {
		$city = get_post_meta($id, 'geo_city', true );
		$extra_city = get_post_meta($id, 'geo_city_extra', true );
		$extra_city = preg_replace('/\s*,\s*/', ',', $extra_city);
		$extra_city = explode(",", $extra_city);

		if(isset($extra_city)) {
			$this->$key = array_merge(array($city), $extra_city);
		} else {
			$this->$key = $city;
		}

	}
    function set_content_value() {
        global $json_api;
        if ($json_api->include_value('content')) {
            $content = get_the_content($json_api->query->read_more);
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            $this->content = $content;
        } else {
            unset($this->content);
        }
    }

    function set_content_value_with_seo($id) {
    	global $json_api;
    	if ($json_api->include_value('content')) {
    		/*
    		$content = get_the_content($json_api->query->read_more);
    		$content = apply_filters('the_content', $content);
    		$content = str_replace(']]>', ']]&gt;', $content);
    		$this->content = $content;
    		*/

    		$yoast_meta = get_post_meta($id, '_yoast_wpseo_metadesc', true);
    		if(class_exists('WPSEO_Frontend')) {
	    		$yoast_generic = new WPSEO_Frontend();
	    		$generic_meta = $yoast_generic->metadesc(false);
    		}
    		if (!empty($yoast_meta)) {
    			$cont = $yoast_meta;
    		} else if (!empty($generic_meta)) {
    			$cont = $generic_meta;
    		} else {
    			$cont = apply_filters('the_content', get_the_content());
    			$cont = strip_tags($cont);
    			$cont = str_replace(array("\r\n","\r","\n"),"", $cont);
    		}
    		$this->content = $cont;
    	} else {
    		unset($this->content);
    	}
    }
    function set_categories_value() {
        global $json_api;
        if ($json_api->include_value('categories')) {
            $this->categories = array();
            if ($wp_categories = get_the_category($this->id)) {
                foreach ($wp_categories as $wp_category) {
                    $category = new JSON_API_Category($wp_category);
                    if ($category->id == 1 && $category->slug == 'uncategorized') {
                        // Skip the 'uncategorized' category
                        continue;
                    }
                    $this->categories[] = $category;
                }
            }
        } else {
            unset($this->categories);
        }
    }

    function mk_set_category_value($type) {
        global $json_api;
        $taxonomies = get_taxonomies(array(
                'object_type' => array($type),
                'public'   => true,
                '_builtin' => false
        ), 'objects');
        foreach ($taxonomies as $taxonomy_id => $taxonomy) {
            $taxonomy_key = "taxonomy_$taxonomy_id";
            if (!$json_api->include_value($taxonomy_key)) {
                continue;
            }
            $taxonomy_class = $taxonomy->hierarchical ? 'JSON_API_Category' : 'JSON_API_Tag';
            $terms = get_the_terms($this->id, $taxonomy_id);
            $this->categories = array();
            if (!empty($terms)) {
                $taxonomy_terms = array();
                foreach ($terms as $term) {
                    //print_r($term);
                    $taxonomy_terms[] = $term->name;
                }
                $this->categories = $taxonomy_terms;
            }
        }
    }

    function set_tags_value() {
        global $json_api;
        if ($json_api->include_value('tags')) {
            $this->tags = array();
            if ($wp_tags = get_the_tags($this->id)) {
                foreach ($wp_tags as $wp_tag) {
                    $this->tags[] = new JSON_API_Tag($wp_tag);
                }
            }
        } else {
            unset($this->tags);
        }
    }

    function set_author_value($author_id) {
        global $json_api;
        if ($json_api->include_value('author')) {
            $this->author = new JSON_API_Author($author_id);
        } else {
            unset($this->author);
        }
    }

    function set_comments_value() {
        global $json_api;
        if ($json_api->include_value('comments')) {
            $this->comments = $json_api->introspector->get_comments($this->id);
        } else {
            unset($this->comments);
        }
    }

    function set_attachments_value() {
        global $json_api;
        if ($json_api->include_value('attachments')) {
            $this->attachments = $json_api->introspector->get_attachments($this->id);
        } else {
            unset($this->attachments);
        }
    }

    function set_thumbnail_value() {
        global $json_api;
        if (!$json_api->include_value('thumbnail') || !function_exists('get_post_thumbnail_id')) {
            unset($this->thumbnail_images);

            return;
        }
        $attachment_id = get_post_thumbnail_id($this->id);
        if (!$attachment_id) {
            unset($this->thumbnail_images);

            return;
        }
        //$thumbnail_size = $this->get_thumbnail_size();
        //$this->thumbnail_size = $thumbnail_size;
        $attachment = $json_api->introspector->get_attachment($attachment_id);
        //$image = $attachment->images[$thumbnail_size];
        //$this->thumbnail = $image->url;

        $this->thumbnail_images = $attachment->images;
    }

    function set_trending_thumbnail_value() {
    	global $json_api;
    	if (!$json_api->include_value('thumbnail') || !function_exists('get_post_thumbnail_id')) {
    		unset($this->thumbnail_images);

    		return;
    	}
    	$attachment_id = get_post_thumbnail_id($this->id);
    	if (!$attachment_id) {
    		unset($this->thumbnail_images);

    		return;
    	}

    	//$attachment = $json_api->introspector->get_attachment($attachment_id);
    	//list($url, $width, $height) = wp_get_attachment_image_src(get_post_thumbnail_id($this->id), "1/1-image-with-text+hero");
    	$thumbs = wp_get_attachment_image_src(get_post_thumbnail_id($this->id), "1/1-image-with-text+hero");
    	$this->thumbnail_images->full = (object) array(
    			'url' => $thumbs[0],
    			'width' => $thumbs[1],
    			'height' => $thumbs[2]
    	);
    }

    function set_custom_fields_value() {
        global $json_api;
        if ($json_api->include_value('custom_fields')) {
            $wp_custom_fields = get_post_custom($this->id);
            $this->custom_fields = new stdClass();
            if ($json_api->query->custom_fields) {
                $keys = explode(',', $json_api->query->custom_fields);
            }
            foreach ($wp_custom_fields as $key => $value) {
                if ($json_api->query->custom_fields) {
                    if (in_array($key, $keys)) {
                        $this->custom_fields->$key = $wp_custom_fields[$key];
                    }
                } else if (substr($key, 0, 1) != '_') {
                    $this->custom_fields->$key = $wp_custom_fields[$key];
                }
            }
        } else {
            unset($this->custom_fields);
        }
    }

    function set_custom_taxonomies($type) {
        global $json_api;
        $taxonomies = get_taxonomies(array(
            'object_type' => array($type),
            'public'   => true,
            '_builtin' => false
        ), 'objects');
        foreach ($taxonomies as $taxonomy_id => $taxonomy) {
            $taxonomy_key = "taxonomy_$taxonomy_id";
            if (!$json_api->include_value($taxonomy_key)) {
                continue;
            }
            $taxonomy_class = $taxonomy->hierarchical ? 'JSON_API_Category' : 'JSON_API_Tag';
            $terms = get_the_terms($this->id, $taxonomy_id);
            $this->$taxonomy_key = array();
            if (!empty($terms)) {
                $taxonomy_terms = array();
                foreach ($terms as $term) {
                    $taxonomy_terms[] = new $taxonomy_class($term);
                }
                $this->$taxonomy_key = $taxonomy_terms;
            }
        }
    }

    function get_thumbnail_size() {
        global $json_api;
        if ($json_api->query->thumbnail_size) {
            return $json_api->query->thumbnail_size;
        } else if (function_exists('get_intermediate_image_sizes')) {
            $sizes = get_intermediate_image_sizes();
            if (in_array('post-thumbnail', $sizes)) {
                return 'post-thumbnail';
            }
        }

        return 'thumbnail';
    }
}

?>
