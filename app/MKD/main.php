<?php
// thumbnail size update
add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');
add_filter( 'image_size_names_choose', 'custom_sizes' );
add_filter("attachment_fields_to_edit", "add_image_attachment_fields_to_edit", 10, 2);

/**
 * $form_fields is a special array of fields to include in the attachment form
 * $post is the attachment record in the database
 * $post->post_type == 'attachment'
 * attachments are treated as posts in WordPress
 *
 * @param array $form_fields
 * @param array $post
 * @return multitype:string
 */
function add_image_attachment_fields_to_edit($form_fields, $post) {
    $data = get_admin_url(). 'post.php?post=' .$post->ID. '&action=edit&image-editor';
    $form_fields["edit_link"] = array(
            "input" => "html",
            "html" => '<a href="'.$data.'&TB_iframe=true&width=960&height=550#wpbody" class="thickbox button">Edit Image</a>',
    );

    return $form_fields;
}

function remove_default_image_sizes($sizes) {
    // thumbnail plug-in won't work if thumbnail is unset :S
    // unset( $sizes['thumbnail']);
    unset( $sizes['medium']);
    unset( $sizes['large']);
    // unset( $sizes['full'] );
    return $sizes;
}
function custom_sizes($sizes) {
    return array_merge($sizes, array(
        '1/1-image-with-text+hero' => __('1/1 image w/ text + hero'),
        '2/3-image-with-text' => __('2/3 image w/ text'),
        '1/2-image-with-text' => __('1/2 image w/ text'),
        '1/3-image-with-text' => __('1/3 image w/ text'),
        '1/4-image-with-text' => __('1/4 image w/ text'),
        'grid-mid' => __('grid mid'),
        'small-thumbnail' => __('small thumbnail'),
        'default-thumbnail' => __('default thumbnail'),
        'large-thumbnail' => __('large thumbnail'),
        'fashion-slideshow-full' => __('fashion slideshow full'),
        '1/2-celebrity' => __('1/2 celebrity'),
    ));
}
if ( function_exists( 'add_image_size' ) ) {
    //resize
    add_image_size( '1/1-image-with-text+hero', 1440, 9999);
    add_image_size( '2/3-image-with-text', 960, 9999);
    add_image_size( '1/2-image-with-text', 720, 9999);
    add_image_size( '1/3-image-with-text', 480, 9999);
    add_image_size( '1/4-image-with-text', 360, 9999);
    add_image_size( 'grid-mid', 600, 9999);
    add_image_size( 'small-thumbnail', 180, 9999);

    //crop
    add_image_size( 'default-thumbnail', 300, 300, true);
    add_image_size( 'large-thumbnail', 480, 480, true);
    add_image_size( 'fashion-slideshow-full', 1440, 2160, true);
    add_image_size( '1/2-celebrity', 600, 708, true);
}
/**
 * overwriting core file wp-includes/media.php
 * filtering image_resize_dimensions
 *
 * @param array $payload
 * @param int $orig_w :: original width
 * @param int $orig_h :: original height
 * @param int $dest_w :: new width
 * @param int $dest_h : new height
 * @param boolean $crop : Optional, default is false. Whether to crop image or resize.
 * @return array|boolean|multitype:number False on failure. Returned array matches parameters for imagecopyresampled() PHP function.
 */
function image_resize_postion($payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop) {

    // Change this to a conditional that decides whether you
    // want to override the defaults for this image or not.
    if( false )

        return $payload;

    if ($crop) {
        // crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
        $aspect_ratio = $orig_w / $orig_h;
        $new_w = min($dest_w, $orig_w);
        $new_h = min($dest_h, $orig_h);

        if (!$new_w) {
            $new_w = intval($new_h * $aspect_ratio);
        }

        if (!$new_h) {
            $new_h = intval($new_w / $aspect_ratio);
        }

        $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

        $crop_w = round($new_w / $size_ratio);
        $crop_h = round($new_h / $size_ratio);
        if ($dest_w == '300') {
            $s_x = floor( ($orig_w - $crop_w) / 2 );
            $s_y = 0;
        } else {
            $s_x = floor( ($orig_w - $crop_w) / 2 );
            $s_y = floor( ($orig_h - $crop_h) / 2 );
        }
    } else {
        // don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
        $crop_w = $orig_w;
        $crop_h = $orig_h;

        $s_x = 0;
        $s_y = 0;

        list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
    }

    // if the resulting image would be the same size or larger we don't want to resize it
    if ( $new_w >= $orig_w && $new_h >= $orig_h )
        return false;

    // the return array matches the parameters to imagecopyresampled()
    // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
    return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}
add_filter( 'image_resize_dimensions', 'image_resize_postion', 10, 6 );

/**
 * Setting featured image
 * if there is no image attachment then set to default image
 */
function set_featured_image() {
    global $post;
    $already_has_thumb = has_post_thumbnail($post->ID);
    $default = wp_get_attachment_image(1491);
    if (!$already_has_thumb) {
        $attached_image = get_children(array(
            'post_parent' => $post->ID,
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'numberposts' => 1,
        ));
        if ($attached_image) {
            foreach ($attached_image as $attachment_id => $attachment) {
                set_post_thumbnail($post->ID, $attachment_id);
            }
        } else if (!$attached_image && $default) {
            set_post_thumbnail($post->ID, '1491');
        } else {

        }
    }
}
add_action('the_post', 'set_featured_image');
add_action('save_post', 'set_featured_image');
add_action('draft_to_publish', 'set_featured_image');
add_action('new_to_publish', 'set_featured_image');
add_action('pending_to_publish', 'set_featured_image');
add_action('future_to_publish', 'set_featured_image');
?>
