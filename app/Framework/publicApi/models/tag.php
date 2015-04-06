<?php
/**
 *
 * @author juhonglee
 *
 * note: currently custom post type doesn't have tags.
 *
 */
class JSON_API_Tag {
    var $id;
    var $slug;
    var $title;
    var $description;

    function JSON_API_Tag($wp_tag = null) {
        if ($wp_tag) {
            $this->import_wp_object($wp_tag);
        }
    }

    function import_wp_object($wp_tag) {
        $this->id = (int) $wp_tag->term_id;
        $this->slug = $wp_tag->slug;
        $this->title = $wp_tag->name;
        $this->description = $wp_tag->description;
        $this->post_count = (int) $wp_tag->count;
    }
}

?>
