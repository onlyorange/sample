<?php
/**
 * hidden post type for template snippets and posts.
 * saving post meta to wordpress xml instead of create another table.
 */

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists( 'swedenStoragePost' ) ) {

    class swedenStoragePost
    {
        /**
         * The  generate_post_type function builds the hidden posts necessary for image saving on options pages
         */
        public static function generate_post_type()
        {
            register_post_type( 'swedenWp_post', array(
            'labels' => array('name' => 'Sweden Framework' ),
            'show_ui' => false,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => false,
            'supports' => array( 'editor', 'title' ),
            'can_export' => true,
            'public' => true,
            'show_in_nav_menus' => false
        ) );
        }


        /**
         * The get_custom_post function gets a custom post based on a post title. if no post cold be found it creates one
         * @param string $post_title the title of the post
         * @package 	AviaFramework
         */
        public static function get_custom_post($post_title)
        {
            $save_title = swedenWpFunctions::save_string( $post_title );

            $args = array( 	'post_type' => 'swedenWp_post',
                            'post_title' => 'swedenWp_' . $save_title,
                            'post_status' => 'draft',
                            'comment_status' => 'closed',
                            'ping_status' => 'closed');

            $sweden_post = get_page_by_title( $args['post_title'], 'ARRAY_A', 'swedenWp_post' );

            if(!isset($sweden_post['ID']) )
            {
                $sweden_post_id = wp_insert_post( $args );
            }
            else
            {
                $sweden_post_id = $sweden_post['ID'];
            }

            return $sweden_post_id;
        }
    }
}
