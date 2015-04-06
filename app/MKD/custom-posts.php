<?php
/**
 * Custom post type with default behavior
 */
add_action( 'restrict_manage_posts', 'mk_type_taxonomy_filter_restrict_manage_posts' );
function mk_type_taxonomy_filter_restrict_manage_posts() {

	global $pagenow, $current_screen;
	if (is_admin() && isset($current_screen) && ('edit.php' == $pagenow)) {
		$post_types = get_post_types( array( '_builtin' => false ) );
		if ( in_array( $current_screen->post_type, $post_types ) ) {
			$filters = get_object_taxonomies( $current_screen->post_type );

			$t = array('category' => true, 'post_tag' => true, 'nav_menu' => true, 'link_category' => true, 'post_format' => true);

			foreach ($filters as $tax_slug) {
				$isCustomTax = !isset($t[$tax_slug]);
				$tax_obj = get_taxonomy( $tax_slug );
				$qvar = $tax_obj->query_var;

				wp_dropdown_categories( array(
				'show_option_all' => sprintf(__('Show All %s', 'sw'), $tax_obj->label),
				'taxonomy' 	  => $tax_slug,
				'name' 		  => $isCustomTax ? $qvar : $tax_slug,
				'orderby' 	  => 'name',
				'selected' 	  => (isset($_GET[$qvar])) ? $_GET[$qvar] : 0,
				'hierarchical' 	  => $tax_obj->hierarchical,
				'hide_empty' => 0,
				'show_count' => 1
				) );
			}
		}
	}

}

add_action('parse_query', 'mk_type_taxonomy_filter_request');

function mk_type_taxonomy_filter_request($query)
{
	global $pagenow, $current_screen;

	if (is_admin() and isset($current_screen) and ('edit.php' == $pagenow)) {
		$taxonomies = get_object_taxonomies($current_screen->post_type, 'objects');
		$q = &$query->query_vars;

		foreach ($taxonomies as $tax) {
			$qvar = $tax->query_var;
			if (isset($q[$qvar]) and is_numeric($q[$qvar]) and $q[$qvar] != '0') {
				$term = get_term_by('id', $q[$qvar], $tax->name);
				if($term)
					$q[$qvar] = $term->slug;
			}
		}
	}
}
// add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );


function mkCustomPost() {
	mkCustomTaxonomies();
	register_post_type( 'mks-edit', array(
		'labels' => array(
			'name'			=> "Michael's Edit",
			'singular_name' => 'mksEdit',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new post',
			'edit_item'		=> 'Edit post',
			'new_item'		=> 'New post',
			'not_found'		=> 'No posts found',
			'not_found_in_trash' => 'No posts found in Trash',
			'menu_name'		=> "Michael's Edit",
		),
		'description' => "Manipulating with Michael's edit",
		'public' => true,
		'show_in_nav_menus' => true,
		'supports' => array(
			'title',
			'revisions',
			'thumbnail',
			'editor',
			'excerpt'
		),
		'show_ui' => true,
		'show_in_menu' => true,
		//'menu_icon' => '',
		'menu_position' => 6,
		'has_archive' => true,
		'query_var' => 'mks-cpost', // change to something unique
		'rewrite' => array('slug' => 'mks-cpost', 'with_front' => true, 'hierarchical' => true),
		'taxonomies' => array('post_tag')
		//'capability_type' => 'mks-edit',
		//'map_meta_cap' => true
		)
	);
	register_post_type( 'jet', array(
		'labels' => array(
			'name'			=> "Jet Set",
			'singular_name' => 'jet',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new post',
			'edit_item'		=> 'Edit post',
			'new_item'		=> 'New post',
			'not_found'		=> 'No posts found',
			'not_found_in_trash' => 'No posts found in Trash',
			'menu_name'		=> "Jet Set",
		),
		'description' => "Manipulating with Jet set",
		'public' => true,
		'show_in_nav_menus' => true,
		'supports' => array(
			'title',
			'revisions',
			'thumbnail',
			'editor',
			'excerpt',
		),
		'show_ui' => true,
		'show_in_menu' => true,
		//'menu_icon' => '',
		'menu_position' => 7,
		'has_archive' => true,
		'query_var' => 'jet-cpost',
		'rewrite' => array('slug' => 'jet-cpost', 'with_front' => true, 'hierarchical' => true),
		//'capability_type' => 'post',
		//'map_meta_cap' => true
		'taxonomies' => array('celebrity-tag', 'post_tag')
		)
	);
	register_post_type( 'fashion', array(
		'labels' => array(
			'name'			=> "Runway",
			'singular_name' => 'fashion-style',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new post',
			'edit_item'		=> 'Edit post',
			'new_item'		=> 'New post',
			'not_found'		=> 'No posts found',
			'not_found_in_trash' => 'No posts found in Trash',
			'menu_name'		=> "Runway",
		),
		'description' => "Manipulating with Runway",
		'public' => true,
		'show_in_nav_menus' => true,
		'supports' => array(
			'title',
			'revisions',
			'thumbnail',
			'editor',
			'excerpt'
		),
		'show_ui' => true,
		'show_in_menu' => true,
		//'menu_icon' => '',
		'menu_position' => 8,
		'has_archive' => true,
		'query_var' => 'fashion-style-cpost',
		'rewrite' => array('slug' => 'fashion-style-cpost', 'with_front' => true, 'hierarchical' => true),
		'taxonomies' => array('post_tag')
		//'capability_type' => 'post',
		//'map_meta_cap' => true
		)
	);
	register_post_type( 'kors-cares', array(
		'labels' => array(
			'name'			=> "Kors Cares",
			'singular_name' => 'kors-cares',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new post',
			'edit_item'		=> 'Edit post',
			'new_item'		=> 'New post',
			'not_found'		=> 'No posts found',
			'not_found_in_trash' => 'No posts found in Trash',
			'menu_name'		=> "Kors Cares",
		),
		'description' => "Manipulating with Kors Cares",
		'public' => true,
		'show_in_nav_menus' => true,
		'supports' => array(
			'title',
			'revisions',
			'thumbnail',
			'editor',
			'excerpt'
		),
		'show_ui' => true,
		'show_in_menu' => true,
		//'menu_icon' => '',
		'menu_position' => 9,
		'has_archive' => true,
		'query_var' => 'kors-cares-cpost', // change to something unique
		'rewrite' => array('slug' => 'cares-cpost', 'with_front' => true, 'hierarchical' => true),
		'taxonomies' => array('post_tag')
		)
	);
    register_post_type( 'sweeps', array(
        'labels' => array(
            'name'          => "Sweeps",
            'singular_name' => 'Sweep',
            'add_new'       => 'Add New Sweeps',
            'add_new_item'  => 'Add new post',
            'edit_item'     => 'Edit post',
            'new_item'      => 'New post',
            'not_found'     => 'No posts found',
            'not_found_in_trash' => 'No posts found in Trash',
            'menu_name'     => "Sweeps",
        ),
        'description' => "Manipulating with Sweeps",
        'public' => true,
        'show_in_nav_menus' => true,
        'supports' => array(
            'title',
            'revisions',
            'thumbnail',
            'editor',
            'excerpt'
        ),
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-tickets',
        'menu_position' => 12,
        'has_archive' => true,
        'query_var' => 'sweeps-cpost', // change to something unique
        'rewrite' => array('slug' => 'sweeps-cpost', 'with_front' => true, 'hierarchical' => true),
        'taxonomies' => ''
        )
    );
    register_post_type( 'sweeps-terms', array(
        'labels' => array(
            'name'          => "Sweeps Terms",
            'singular_name' => 'Sweep Term',
            'add_new'       => 'Add new',
            'add_new_item'  => 'Add new post',
            'edit_item'     => 'Edit post',
            'new_item'      => 'New post',
            'not_found'     => 'No posts found',
            'not_found_in_trash' => 'No posts found in Trash',
            'menu_name'     => "Sweeps Terms",
        ),
        'description' => "Manipulating with Sweeps Terms",
        'public' => true,
        'show_in_nav_menus' => true,
        'supports' => array(
            'title',
            'revisions',
            'thumbnail',
            'editor',
            'excerpt'
        ),
        'show_ui' => true,
        'show_in_menu' => false,
        //'menu_icon' => '',
        'menu_position' => 10,
        'has_archive' => true,
        'query_var' => 'sweeps-terms-cpost', // change to something unique
        'rewrite' => array('slug' => 'fieldvisitsweeps-terms', 'with_front' => true, 'hierarchical' => true),
        'taxonomies' => ''
        )
    );
    register_post_type( 'sweeps-rules', array(
        'labels' => array(
            'name'          => "Sweeps Rules",
            'singular_name' => 'Sweep Rule',
            'add_new'       => 'Add new',
            'add_new_item'  => 'Add new post',
            'edit_item'     => 'Edit post',
            'new_item'      => 'New post',
            'not_found'     => 'No posts found',
            'not_found_in_trash' => 'No posts found in Trash',
            'menu_name'     => "Sweeps Rules",
        ),
        'description' => "Sweeps Rules",
        'public' => true,
        'show_in_nav_menus' => true,
        'supports' => array(
            'title',
            'revisions',
            'thumbnail',
            'editor',
            'excerpt'
        ),
        'show_ui' => true,
        'show_in_menu' => false,
        //'menu_icon' => '',
        'menu_position' => 10,
        'has_archive' => true,
        'query_var' => 'sweeps-rules-cpost', // change to something unique
        'rewrite' => array('slug' => 'fieldvisitsweeps-rules', 'with_front' => true, 'hierarchical' => true),
        'taxonomies' => ''
        )
    );

	//register_taxonomy_for_object_type('tag', 'mks-edit');
	//flushing rewriting rules...is kinda expensive to do in every updates...
	//flush_rewrite_rules(false);
}

function mkCustomTaxonomies() {
	register_taxonomy( 'celebrity-tag', array('celeb-tag'), array(
		'label' => 'Celebrity Tag',
		'hierachial' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => 'slideshow', 'hierarchical' => true),
	));
	register_taxonomy( 'mks-edit-category', array( 'mks-edit' ), array(
		'hierarchical' => true,
		'labels' => array(
			'name'			=> 'MK Edit Categories',
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'sw'),
			'search_items'	=> __( 'Search Category', 'sw'),
			'all_items'		=> __( 'All Categories', 'sw'),
			'parent_item'	=> __( 'Parent Category', 'sw'),
			'parent_item_colon' => __( 'Parent Category:', 'sw'),
			'edit_item'		=> __( 'Edit Category', 'sw'),
			'update_item'	=> __( 'Update Category', 'sw'),
			'add_new_item'	=> __( 'Add New Category', 'sw'),
			'new_item_name' => __( 'New Category Name', 'sw'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'capabilities' => array('manage_terms','edit_terms','delete_terms','assign_terms'),
		'rewrite' => array( 'slug' => 'mks-cat' ),
	));
	register_taxonomy( 'jet-category', array( 'jet' ), array(
		'hierarchical' => true,
		'labels' => array(
			'name'			=> 'Jet Set Categories',
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'sw'),
			'search_items'	=> __( 'Search Category', 'sw'),
			'all_items'		=> __( 'All Categories', 'sw'),
			'parent_item'	=> __( 'Parent Category', 'sw'),
			'parent_item_colon' => __( 'Parent Category:', 'sw'),
			'edit_item'		=> __( 'Edit Category', 'sw'),
			'update_item'	=> __( 'Update Category', 'sw'),
			'add_new_item'	=> __( 'Add New Category', 'sw'),
			'new_item_name' => __( 'New Category Name', 'sw'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'capabilities' => array('manage_terms','edit_terms','delete_terms','assign_terms'),
		'rewrite' => array( 'slug' => 'jet-cat' )
	));
	register_taxonomy( 'fashion-category', array( 'fashion' ), array(
		'hierarchical' => true,
		'labels' => array(
			'name'			=> 'Runway Categories',
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'sw'),
			'search_items'	=> __( 'Search Category', 'sw'),
			'all_items'		=> __( 'All Categories', 'sw'),
			'parent_item'	=> __( 'Parent Category', 'sw'),
			'parent_item_colon' => __( 'Parent Category:', 'sw'),
			'edit_item'		=> __( 'Edit Category', 'sw'),
			'update_item'	=> __( 'Update Category', 'sw'),
			'add_new_item'	=> __( 'Add New Category', 'sw'),
			'new_item_name' => __( 'New Category Name', 'sw'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'capabilities' => array('manage_terms','edit_terms','delete_terms','assign_terms'),
		'rewrite' => array( 'slug' => 'fashion-style-cat', 'hierarchical' => true )
	));
	register_taxonomy( 'kors-cares-category', array( 'kors-cares' ), array(
		'hierarchical' => true,
		'labels' => array(
			'name'			=> 'Kors Cares Categories',
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'sw'),
			'search_items'	=> __( 'Search Category', 'sw'),
			'all_items'		=> __( 'All Categories', 'sw'),
			'parent_item'	=> __( 'Parent Category', 'sw'),
			'parent_item_colon' => __( 'Parent Category:', 'sw'),
			'edit_item'		=> __( 'Edit Category', 'sw'),
			'update_item'	=> __( 'Update Category', 'sw'),
			'add_new_item'	=> __( 'Add New Category', 'sw'),
			'new_item_name' => __( 'New Category Name', 'sw'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'capabilities' => array('manage_terms','edit_terms','delete_terms','assign_terms'),
		'rewrite' => array( 'slug' => 'cares-cat' ),
	));

}
add_action( 'init', 'mkCustomPost');

/*
function mksEditChangeColumns($cols) {
	$cols = array(
			'cb'			=> '<input type="checkbox" />',
			'title'			=> __( 'Title', 'sw'),
			'category'		=> __( 'Categories', 'sw'),
			'date'		=> __( 'Dates', 'sw'),
	);

	return $cols;
}
add_filter( "manage_mks-edit_posts_columns", "mksEditChangeColumns" );

function mksEditCustomColumns($column, $post_id) {

	switch ($column) {
		case 'mks' :
			break;
		case 'mks-second' :
			break;
		case 'mks-third' :
			break;
	}
}
add_action( "manage_posts_custom_column", "mksEditCustomColumns", 10, 2);
*/

// custom template type selector
// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
function addMetaBox($post) {
	$screens = array( 'page', 'mks-edit', 'jet', 'fashion', 'kors-cares');
    foreach ($screens as $screen) {
        add_meta_box( 'templateType', __( 'Template Type', 'swedenWp' ), 'innerBox', $screen, 'side', 'high' );
        add_meta_box( 'Geodata', __( 'Geodata', 'swedenWp' ), 'geodataBox', $screen, 'side', 'high' );
        add_meta_box( 'Endeca Search', __( 'Endeca Search', 'swedenWp' ), 'endeca_search', $screen, 'side', 'high' );
        if(get_current_blog_id() == '1')
        	add_meta_box( 'netSync', __('Sync this post', 'swedenWp'), 'syncBox', $screen, 'side', 'core' );

        // adding category rubric to all posts
        // keeping same var from fashion category
        add_meta_box( 'fashion-postbox-container-1', __( 'Category Heading', 'swedenWp' ), 'fashionCategoryHeading', $screen, 'normal', 'high' );
    }
    if(isset($_GET['post'])) {
    	$post_id = $_GET['post'];
    }
    if($post_id == 12934)
    	add_meta_box( 'sweepsBox', __('Sweepstakes Modal Window', 'swedenWp'), 'sweepsBox', 'page', 'normal', 'core' );


}

add_action( 'add_meta_boxes', 'addMetaBox' );
function syncBox() {
	// testing only..
	$boxContent = '<input class="checkbox" id="plainview_sdk_form2_inputs_checkbox_custom_fields" name="sync[custom_fields]" type="hidden" value="on" checked>
				<input class="checkbox" id="plainview_sdk_form2_inputs_checkbox_taxonomies" name="sync[taxonomies]" type="hidden" value="on" checked>
				<div class="blogs html_section">
				<legend>Sync Post Data to</legend>';

	$boxContent .= '<input class="blog' . get_current_blog_id() . ' checkbox" hidden="hidden" id="plainview_sdk_form2_inputs_checkboxes_blogs_'.get_current_blog_id().'" name="sync[blogs][blogs_'.get_current_blog_id().']" type="hidden" value="'.get_current_blog_id().'">';
	$siteList = wp_get_sites();
	foreach ($siteList as $site) {
		if($site['blog_id'] == '1') {
			$boxContent .= '<input class="blog '.$site['blog_id'].' checkbox" id="plainview_sdk_form2_inputs_checkboxes_blogs_'.$site['blog_id'].'" name="sync[blogs][blogs_'.$site['blog_id'].']" type="hidden" value="'.$site['blog_id'].'">';
		} else {
			$boxContent .= '<div class="form_item form_item_plainview_sdk_form2_inputs_checkboxes_blogs_'.$site['blog_id'].' form_item_checkbox form_item_input blog '.$site['blog_id'].'">
				<input class="blog '.$site['blog_id'].' checkbox" id="plainview_sdk_form2_inputs_checkboxes_blogs_'.$site['blog_id'].'" name="sync[blogs][blogs_'.$site['blog_id'].']" type="checkbox" value="'.$site['blog_id'].'">
				<label for="plainview_sdk_form2_inputs_checkboxes_blogs_'.$site['blog_id'].'">'.$site['domain'].'</label>
				</div>';
		}
	}
	$boxContent .= '';

	$boxContent .= '</div>';
	echo $boxContent;
}

/**
 * exclude posts from edeca search result
 *
 *
 */
function endeca_search($post) {
	wp_nonce_field( 'endeca_search', 'endeca_searchNonce' );

	$meta = get_post_meta($post->ID, 'endeca_search', true);

	echo '<input type="checkbox" name="endeca_search" value="1"' . (!empty($meta) ? ' checked="checked" ' : null) . '>Remove this post from endeca search';
}
/**
 * printing geodata box
 *
 * @param wp $post
 */
function geodataBox($post) {
	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'geodataBox', 'geodataBoxNonce' );

	// get value from db first.
	$meta_element_class = get_post_meta($post->ID, 'geo_latitudes', true);
	$meta_element_class_sub = get_post_meta($post->ID, 'geo_longitudes', true);
	$meta_element = get_post_meta($post->ID, 'geo_city', true);
	$extra_meta_element = get_post_meta($post->ID, 'geo_city_extra', true);
	echo '<div>';
	echo '<label for="geo_latitudes" style="padding-right:11px;">';
	_e( "Latitude: ", 'swedenWp' );
	echo '</label>';
	echo '<input class="trigger" style="width:60%;" id="mapLat" name="geo_latitudes" value="'.$meta_element_class.'">';
	echo '</div><div>';
	echo '<label for="geo_longitudes">';
	_e( "Longitude: ", 'swedenWp' );
	echo '</label>';
	echo '<input class="trigger" style="width:60%;" id="mapLon" name="geo_longitudes" value="'.$meta_element_class_sub.'">';
	echo '</div><div>';
	echo '<label for="geo_city" style="padding-right:38px;">';
	_e( "City: ", 'swedenWp' );
	echo '</label>';
	echo '<input style="width:60%;" id="mapCity" name="geo_city" value="'.$meta_element.'">';
	if(!empty($extra_meta_element)) {
		echo '<div class="extraField">';
		echo '<input style="width:60%; margin-left:68px;" id="geo_city_extra" name="geo_city_extra" value="'. $extra_meta_element .'">';
		echo '<p class="howto" style="margin-left:68px;"> Separate city name with commas</p>';
		echo '</div>';
		echo '</div>';
	}
	else {
		echo '<div class="extraField" style="display:none;">';
		echo '<input style="width:60%; margin-left:68px;" id="geo_city_extra" name="geo_city_extra" value="'. $extra_meta_element .'">';
		echo '<p class="howto" style="margin-left:68px;">Separate city name with commas</p>';
		echo '</div>';
		echo '</div>';
		echo '<input type="button" class="button add_city_field" value="Add another city" style="float:right; margin:10px 0;">';
		echo '<div style="clear:both;"></div>';
	}
	echo '<div id="mapBox" style="width:100%; height:180px; margin-top:15px;"></div>';
	?>
	<script>
	var lon = '<?php echo $meta_element_class_sub ?>';
	var lat = '<?php echo $meta_element_class ?>';
	jQuery(function() {
		if (lon && lat) {
			var map = L.map('mapBox').setView([lat, lon], 14);
			var marker = L.marker([lat, lon]).addTo(map);
			jQuery.ajax({
				type: 'GET',
				url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+lat+','+lon+'&sensor=false',
				dataType: "json",
				success: function(data, status) {
					var city = data.results[0].address_components[3].long_name;
					console.log(city);
					if(jQuery('#mapCity').val() == '')
						jQuery('#mapCity').val(city);
				}
			});

		} else {
			var map = L.map('mapBox').setView([44,-74], 13);
			var marker = L.marker([44, -74]).addTo(map);
			jQuery('#mapBox').hide();
		}
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		    attribution: ''
		}).addTo(map);
		jQuery('input.trigger').bind("change paste keyup" ,function() {
			setTimeout(function() {
				var lon = jQuery('#mapLon').val();
				var lat = jQuery('#mapLat').val();

				if (lon && lat) {
					jQuery('#mapBox').show('slow');
					var newLatLng = new L.LatLng(lat, lon);
					map.panTo(newLatLng);
					marker.setLatLng(newLatLng);
					jQuery.ajax({
						type: 'GET',
						url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+lat+','+lon+'&sensor=false',
						//contentType: 'application/json; charset=UTF-8',
						dataType: "json",
						success: function(data, status) {
							var city = data.results[0].address_components[3].long_name;
							console.log(city);
							jQuery('#mapCity').val(city);
						}
					});
				}
			}, 1500);
		});
	});

	</script>
	<?php
}
/**
 * printing box content
 * @param wp $post
 */
function innerBox( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'innerBox', 'innerBoxNonce' );

	// get value from db first.
	$meta_element_class = get_post_meta($post->ID, 'template_key', true);
	$meta_element_class_sub = get_post_meta($post->ID, 'background_type', true);
	$meta_element_class_grid = get_post_meta($post->ID, 'grid_type', true);

	echo '<label for="background_type">';
	_e( "Choose Background Color", 'swedenWp' );
	echo '</label><br/>';
	echo '<select id="background_type" name="background_type">';
	?>
		<option value="has-white-bg" <?php selected( $meta_element_class_sub, 'has-white-bg' ); ?>>Default</option>
		<option value="has-white-bg" <?php selected( $meta_element_class_sub, 'has-white-bg' ); ?>>White Background</option>
		<option value="has-black-bg" <?php selected( $meta_element_class_sub, 'has-black-bg' ); ?>>Black Background</option>
	<?php
	echo '</select>';
	echo '<Br/><Br/>';
	echo '<label for="grid_type">';
	_e( "Choose Grid Type", 'swedenWp' );
	echo '</label><br/>';
	echo '<select id="grid_type" name="grid_type">';
	?>
		<option value="none" <?php selected( $meta_element_class_grid, 'none' ); ?>>None</option>
		<option value="has-borders" <?php selected( $meta_element_class_grid, 'has-borders' ); ?>>Has Border</option>
	<?php
	echo '</select>';

}
function freeFormContentBox($post) {
	wp_nonce_field('fFContent', 'fFContentNonce');
	$val = get_post_meta($post->ID, 'freeFormContent', true);
	wp_editor($val, 'freeFormContentID', array( 'textarea_name' => 'freeFormContent', 'media_buttons' => false ));
}

function sweepsBox( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'sweepsBox', 'sweepsBoxNonce' );

	$meta_head = get_post_meta($post->ID, 'sweeps_heading', true);
	$meta_desc = get_post_meta($post->ID, 'sweeps_description', true);
    $meta_disclaimer = get_post_meta($post->ID, 'sweeps_disclaimer', true);
	echo '<p><strong>Title</strong></p>';
	echo '<textarea rows="2" cols="40" name="sweeps_heading" id="sweeps_heading" >'.$meta_head.'</textarea>';
	echo '<p><strong>Description</strong></p>';
	echo '<textarea rows="2" cols="40" name="sweeps_description" id="sweeps_description" >'.$meta_desc.'</textarea>';
    echo '<p><strong>Disclaimer</strong></p>';
    echo '<textarea rows="2" cols="40" name="sweeps_disclaimer" id="sweeps_disclaimer" >'.$meta_disclaimer.'</textarea>';
}
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_post_meta_content( $post_id ) {

	/*
	* We need to verify this came from the our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/

  	//Check if our nonce is set.
	if (!isset($_POST['innerBoxNonce']) && !isset($_POST['geodataBoxNonce']) && !isset($_POST['endeca_searchNonce']))
    	return $post_id;

	$nonce = $_POST['innerBoxNonce'];
	$nonceGeo = $_POST['geodataBoxNonce'];
	$nonceEndeca = $_POST['endeca_searchNonce'];
	// Verify that the nonce is valid.
	if (!wp_verify_nonce($nonce, 'innerBox') && !wp_verify_nonce($nonceGeo, 'geodataBox') && !wp_verify_nonce($nonceEndeca, 'endeca_search'))
		return $post_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return $post_id;

	// Check the user's permissions.
	if ('page' == $_POST['post_type']) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	/* OK, its safe for us to save the data now. */

	// Sanitize user input.
	$result_sub = sanitize_text_field( $_POST['background_type'] );
	$result_grid = sanitize_text_field( $_POST['grid_type'] );
	$result_geoLat = sanitize_text_field( $_POST['geo_latitudes'] );
	$result_geoLon = sanitize_text_field( $_POST['geo_longitudes'] );
	$result_geoCity = sanitize_text_field( $_POST['geo_city'] );
	$result_extra_geoCity = sanitize_text_field( $_POST['geo_city_extra'] );
	$endeca_search_box = sanitize_text_field( $_POST['endeca_search'] );

	// Update the meta field in the database.
	// And this will be added in <div id="page"> as class
	update_post_meta( $post_id, 'background_type', $result_sub );
	update_post_meta( $post_id, 'grid_type', $result_grid );
	update_post_meta( $post_id, 'geo_latitudes', $result_geoLat );
	update_post_meta( $post_id, 'geo_longitudes', $result_geoLon );
	update_post_meta( $post_id, 'geo_city', $result_geoCity );
	update_post_meta( $post_id, 'geo_city_extra', $result_extra_geoCity );
	update_post_meta( $post_id, 'endeca_search', $endeca_search_box );


	// get value from db first.
	if(isset($_POST['sweepsBoxNonce'])) {
		$result_sweeps_head = $_POST['sweeps_heading'];
		$result_sweeps_desc = $_POST['sweeps_description'];
		$resutl_sweeps_disclaimer = $_POST['sweeps_disclaimer'];

		update_post_meta($post_id, 'sweeps_heading', $result_sweeps_head);
		update_post_meta($post_id, 'sweeps_description', $result_sweeps_desc);
		update_post_meta($post_id, 'sweeps_disclaimer', $resutl_sweeps_disclaimer);
	}

	if (isset($_POST['jetsetCelebrityContentNonce'])) {
		// Sanitize user input.
		$celeb_date = sanitize_text_field($_POST['celeb_date']);
		$celeb_where = $_POST['celeb_where'];
		$celeb_what = $_POST['celeb_what'];

		// Update the meta field in the database.
		update_post_meta( $post_id, 'celeb_date', $celeb_date );
		update_post_meta( $post_id, 'celeb_where', $celeb_where );
		update_post_meta( $post_id, 'celeb_what', $celeb_what );
	}

	  if (isset($_POST['categoryHeadingFieldNonce'])) {

		  // Sanitize user input.
		  $result =  $_POST['category_heading'];
          error_log($result);
           // Update the meta field in the database.
		  update_post_meta( $post_id, 'category_heading', $result );
	  }
	  if (isset($_POST['categorySubHeadingFieldNonce'])) {

		  // Sanitize user input.
		  $result =  $_POST['category_sub_heading'];
          error_log($result);
           // Update the meta field in the database.
		  update_post_meta( $post_id, 'category_sub_heading', $result );
	  }
	  if (isset($_POST['entryTitleFieldNonce'])) {
		  // Sanitize user input.
		  $result = ( $_POST['entry_title'] );
		  // Update the meta field in the database.
		  update_post_meta( $post_id, 'entry_title', $result );
	  }
	  if (isset($_POST['articleIntroFieldNonce'])) {
		  // Sanitize user input.
		  $result = ( $_POST['article_intro'] );
		  // Update the meta field in the database.
		  update_post_meta( $post_id, 'article_intro', $result );
	  }
	  if (isset($_POST['articleContentFieldNonce'])) {
		  // Sanitize user input.
		  $result = ( $_POST['article_content'] );
		  // Update the meta field in the database.
		  update_post_meta( $post_id, 'article_content', $result );
	  }
      if (isset($_POST['articleTypeFieldNonce'])) {
          // Sanitize user input.
          $result = ( $_POST['article_type'] );
          // Update the meta field in the database.
          update_post_meta( $post_id, 'article_type', $result );
      }

	  if (isset($_POST['fFContentNonce'])) {
		$result = ( $_POST['freeFormContent'] );
		update_post_meta( $post_id, 'freeFormContent', $result );
	  }

	  if (isset($_POST['postTypeBoxNonce'])) {
	  	$result = ($_POST['post_type_field']);
	  	update_post_meta( $post_id, 'post_type_key', $result );
	  	$result = ($_POST['category_type_field']);
	  	update_post_meta( $post_id, 'category_type_key', $result );
	  }
	  if (isset($_POST['mksContentFieldNonce'])) {
		  // Sanitize user input.
		  $result = ( $_POST['mks_article_content'] );
		  // Update the meta field in the database.
		  update_post_meta( $post_id, 'mks_article_content', $result );
	  }

}
add_action( 'save_post', 'save_post_meta_content' );




/*****************************************************
* Post Type and Post Category for pages
*
******************************************************/

function addPostTypeMetaBox() {
	// 'mks-edit', 'jet', 'fashion'
	$screens = array( 'page' );
    foreach ($screens as $screen) {
        add_meta_box( 'postType', __( 'Post Type', 'swedenWp' ), 'postTypeBox', $screen, 'side', 'high' );
    }
}
add_action( 'add_meta_boxes', 'addPostTypeMetaBox' );

/**
 * printing box content
 * @param wp $post
 */
function postTypeBox( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'postTypeBox', 'postTypeBoxNonce' );

	// get value from db first.
	$meta_element_class = get_post_meta($post->ID, 'post_type_key', true);
	$meta_element_class_sub = get_post_meta($post->ID, 'category_type_key', true);

	echo '<label for="post_type_field">';
	_e( "Choose Post Type", 'swedenWp' );
	echo '</label><br/>';
	echo '<select id="post_type_field" name="post_type_field">';
	?>
	<option value="none" <?php selected( $meta_element_class, 'none' ); ?>>None</option>
	<option value="mks-edit" <?php selected( $meta_element_class, 'mks-edit' ); ?>>Michael's Edit</option>
	<option value="jet" <?php selected( $meta_element_class, 'jet' ); ?>>Jet Set</option>
	<option value="fashion" <?php selected( $meta_element_class, 'fashion' ); ?>>Fashion and Style</option>
	<option value="kors-cares" <?php selected( $meta_element_class, 'kors-cares' ); ?>>Kors Cares</option>
	<?php
	echo '</select>';
	echo '<Br/><Br/>';
	echo '<label for="category_type_field">';
	_e( "Choose Category", 'swedenWp' );
	echo '</label><br/>';
	echo '<select id="category_type_field" name="category_type_field">';
	?>
		<option value="" <?php selected( $meta_element_class_sub, '' ); ?>>None (Category Landing Page)</option>

		<option value="must-haves" <?php selected( $meta_element_class_sub, 'must-haves' ); ?>>Michael's Edit - Must-Haves</option>
		<option value="spotlight-on" <?php selected( $meta_element_class_sub, 'spotlight-on' ); ?>>Michael's Edit - Spotlight on</option>
		<option value="style-confidential" <?php selected( $meta_element_class_sub, 'style-confidential' ); ?>>Michael's Edit - Style Confidential</option>
		<option value="celebrities" <?php selected( $meta_element_class_sub, 'celebrities' ); ?>>Jet Set - Celebrities</option>
		<option value="location" <?php selected( $meta_element_class_sub, 'location' ); ?>>Jet Set - Location</option>
		<option value="travel-diaries" <?php selected( $meta_element_class_sub, 'travel-diaries' ); ?>>Jet Set - Travel Diaries</option>
		<option value="around-the-world" <?php selected( $meta_element_class_sub, 'around-the-world' ); ?>>Jet Set - Around the World</option>
		<option value="ad-campaigns" <?php selected( $meta_element_class_sub, 'ad-campaigns' ); ?>>Runway - Ad Campaigns</option>
		<option value="lookbooks" <?php selected( $meta_element_class_sub, 'lookbooks' ); ?>>Runway - Lookbooks</option>
		<option value="runway-shows" <?php selected( $meta_element_class_sub, 'runway-shows' ); ?>>Runway - Runway Shows</option>
		<option value="watch-hunger-stop" <?php selected( $meta_element_class_sub, 'watch-hunger-stop' ); ?>>Kors Cares - Watch Hunger Stop</option>
		<option value="gods-love-we-deliver" <?php selected( $meta_element_class_sub, 'gods-love-we-deliver' ); ?>>Kors Cares - Gods Love We Deliver</option>
	<?php
	echo '</select>';

}




/*****************************************************
* Fashion Posts
* add Article custom fields
******************************************************/

// add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );




function addFashionMetaBox() {
	global $_wp_post_type_features;
	$screen = 'fashion';
	//take out default editor
	//if (isset($_wp_post_type_features[ $screen ]['editor']) && $_wp_post_type_features[ $screen ]['editor']) {
		    //unset($_wp_post_type_features[ $screen ]['editor']);
            //add_meta_box( 'fashion-postbox-container-0', __( 'Spotlight On Article Type', 'swedenWp' ), 'spotlightOnArticleType', $screen, 'normal', 'high' );
            //category heading is added to all posts
            //add_meta_box( 'fashion-postbox-container-1', __( 'Category Heading', 'swedenWp' ), 'fashionCategoryHeading', $screen, 'normal', 'high' );
    		//add_meta_box( 'fashion-postbox-container-2', __( 'Entry Title', 'swedenWp' ), 'fashionEntryTitle', $screen, 'normal', 'high' );
			//add_meta_box( 'fashion-postbox-container-3', __( 'Article Intro', 'swedenWp' ), 'fashionArticleIntro', $screen, 'normal', 'high' );
    		//add_meta_box( 'fashion-postbox-container-4', __( 'Article Content', 'swedenWp' ), 'fashionArticleContent', $screen, 'normal', 'low' );

    		//add_meta_box( 'description_section', __('Advanced Layout Text Editor'), 'fashionEntryTitle', $screen, 'normal', 'default' );
    		//do_meta_boxes($_wp_post_type_features[ $screen ]['editor']);
			//
    //}
}
function spotlightOnArticleType( $post ) {

    // adding an nonce field so we can check for it later.
    wp_nonce_field( 'articleTypeField', 'articleTypeFieldNonce' );

    // get value from db first.
    $val = get_post_meta( $post->ID, 'article_type', true );

    //wp_editor( $val, 'article-type-id', array( 'textarea_name' => 'article_type', 'media_buttons' => true ) );
    echo '<select id="article-type-id" name="article_type">';
    ?>
        <option value="" <?php selected( $val, '' ); ?>>None</option>
        <option value="collaborator" <?php selected( $val, 'collaborator' ); ?>>Collaborator</option>
        <option value="product" <?php selected( $val, 'product' ); ?>>Product</option>
    <?php
    echo '</select>';

}

function inner_custom_box( $post ) {
	wp_editor($post->post_content, 'content', array( 'textarea_name' => 'content', 'media_buttons' => false ) );
}
function fashionCategoryHeading( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'categoryHeadingField', 'categoryHeadingFieldNonce' );

	// get value from db first.
	$meta_element = get_post_meta($post->ID, 'category_heading', true);

	echo '<textarea rows="1" cols="40" name="category_heading" id="category_heading" >'.$meta_element.'</textarea>';

}

function fashionEntryTitle( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'entryTitleField', 'entryTitleFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'entry_title', true );

    echo '<textarea rows="1" cols="40" name="entry_title" id="entry_title" >'.$val.'</textarea>';

}
function fashionArticleIntro( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'articleIntroField', 'articleIntroFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'article_intro', true );

	wp_editor( $val, 'article-intro-id', array( 'textarea_name' => 'article_intro', 'media_buttons' => false ) );


}
function fashionArticleContent( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'articleContentField', 'articleContentFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'article_content', true );

	wp_editor( $val, 'article-content-id', array( 'textarea_name' => 'article_content', 'media_buttons' => true ) );


}


/*****************************************************
* Jet Set Posts
* add Article custom fields
******************************************************/

// add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );




function addJetSetMetaBox() {
	global $_wp_post_type_features;
	$screen = 'jet';
	//take out default editor
	if (isset($_wp_post_type_features[ $screen ]['editor']) && $_wp_post_type_features[ $screen ]['editor']) {
		//unset($_wp_post_type_features[ $screen ]['editor']);
		add_meta_box( 'jetset-postbox-container-0', __( 'Celebrity Post Content', 'swedenWp' ), 'jetsetCelebrityContent', $screen, 'normal', 'high' );
	    //add_meta_box( 'jetset-postbox-container-1', __( 'Category Heading', 'swedenWp' ), 'jetsetCategoryHeading', $screen, 'normal', 'high' );
		//add_meta_box( 'jetset-postbox-container-5', __( 'Sub Heading', 'swedenWp' ), 'jetsetSubHeading', $screen, 'normal', 'high' );
    	//add_meta_box( 'jetset-postbox-container-2', __( 'Entry Title', 'swedenWp' ), 'jetsetEntryTitle', $screen, 'normal', 'high' );
		//add_meta_box( 'jetset-postbox-container-3', __( 'Article Intro', 'swedenWp' ), 'jetsetArticleIntro', $screen, 'normal', 'high' );
   		//add_meta_box( 'jetset-postbox-container-4', __( 'Article Content', 'swedenWp' ), 'jetsetArticleContent', $screen, 'normal', 'low' );

    	//add_meta_box( 'description_section', __('Advanced Layout Text Editor'), 'fashionEntryTitle', $screen, 'normal', 'default' );
    	//do_meta_boxes($_wp_post_type_features[ $screen ]['editor']);
		//
    }
}
function jetsetCelebrityContent($post) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'jetsetCelebrityContentField', 'jetsetCelebrityContentNonce' );

	$categories = get_terms('jet-category', array(
		'orderby'    => 'count',
		'hide_empty' => 0
	));
	$terms = get_the_terms( $post->ID, 'jet-category' );

	$meta_date = get_post_meta($post->ID, 'celeb_date', true);
	$meta_where = get_post_meta($post->ID, 'celeb_where', true);
	$meta_what = get_post_meta($post->ID, 'celeb_what', true);
	echo '<div>';
	echo '<label>DATE</label>';
	echo '<Br/>';
	echo '<textarea rows="1" cols="25" name="celeb_date" id="celeb_date" >'.$meta_date.'</textarea>';
	echo '</div>';
	echo '<div>';
	echo '<label>WHERE</label>';
	wp_editor($meta_where, 'celeb_where', array( 'textarea_name' => 'celeb_where', 'media_buttons' => false, 'textarea_rows' => 2 ));
	echo '</div><div>';
	echo '<label>WHAT</label>';
	wp_editor($meta_what, 'celeb_what', array( 'textarea_name' => 'celeb_what', 'media_buttons' => false, 'textarea_rows' => 2 ));
	echo '</div>';

	// get value from db first.


}
function jetsetCategoryHeading( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'categoryHeadingField', 'categoryHeadingFieldNonce' );

	// get value from db first.
	$meta_element = get_post_meta($post->ID, 'category_heading', true);

	echo '</label><br/>';
	echo '<textarea rows="1" cols="40" name="category_heading" id="category_heading" >'.$meta_element.'</textarea>';

}
function jetsetSubHeading( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'categorySubHeadingField', 'categorySubHeadingFieldNonce' );

	// get value from db first.
	$meta_element = get_post_meta($post->ID, 'category_sub_heading', true);

	echo '</label><br/>';
	echo '<textarea rows="1" cols="40" name="category_sub_heading" id="category_sub_heading" >'.$meta_element.'</textarea>';

}

function jetsetEntryTitle( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'entryTitleField', 'entryTitleFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'entry_title', true );

	wp_editor( $val, 'entry-title-id', array( 'textarea_name' => 'entry_title', 'media_buttons' => false ) );


}
function jetsetArticleIntro( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'articleIntroField', 'articleIntroFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'article_intro', true );

	wp_editor( $val, 'article-intro-id', array( 'textarea_name' => 'article_intro', 'media_buttons' => false ) );


}
function jetsetArticleContent( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'articleContentField', 'articleContentFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'article_content', true );

	wp_editor( $val, 'article-content-id', array( 'textarea_name' => 'article_content', 'media_buttons' => true ) );


}

function jetsetArticleType( $post ) {

    // adding an nonce field so we can check for it later.
    wp_nonce_field( 'articleTypeField', 'articleTypeFieldNonce' );

    // get value from db first.
    $val = get_post_meta( $post->ID, 'article_type', true );

    //wp_editor( $val, 'article-type-id', array( 'textarea_name' => 'article_type', 'media_buttons' => true ) );
    echo '<select id="article-type-id" name="article_type">';
    ?>
        <option value="" <?php selected( $val, '' ); ?>>None</option>
        <option value="what-to-do" <?php selected( $val, 'what-to-do' ); ?>>What To Do</option>
        <option value="what-to-pack" <?php selected( $val, 'what-to-pack' ); ?>>What To Pack</option>
    <?php
    echo '</select>';

}

/*****************************************************
* Michael's Edit Posts
* add Article custom fields
******************************************************/

// add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );




function addMichaelsEditMetaBox() {
	global $_wp_post_type_features;
	$screen = 'mks-edit';
	//take out default editor
    //add_meta_box( 'mks-postbox-container-1', __( 'Post Content (for Obsessions and Flashback)', 'swedenWp' ), 'mksEditContent', $screen, 'normal', 'high' );
}

function mksEditContent( $post ) {

	// adding an nonce field so we can check for it later.
	wp_nonce_field( 'mksContentField', 'mksContentFieldNonce' );

	// get value from db first.
	$val = get_post_meta( $post->ID, 'mks_article_content', true );

	wp_editor( $val, 'mks_article_content-id', array( 'textarea_name' => 'mks_article_content', 'media_buttons' => false ) );


}



add_action( 'add_meta_boxes', 'addMetaBox' );
add_action( 'add_meta_boxes', 'addFashionMetaBox' );
add_action( 'add_meta_boxes', 'addJetSetMetaBox' );
add_action( 'add_meta_boxes', 'addMichaelsEditMetaBox' );

/**
 * Override custom post link
 *
 * @param string $link
 * @param string $post
 * @return string
 */
function filter_post_type_link($link, $post) {
	if ($post->post_type =='post') return $link;
	if(strpos($link, 'sweeps-cpost')) {
		$link = str_replace('sweeps-cpost', 'fieldvisitsweeps', $link);
	}
	if ($cats = get_the_terms($post->ID, 'mks-edit-category')) {
		$catCount = 0;
		$subCat = '';
		$priCat = '';
		$pCat = '';
		$catLink = '';
		foreach ($cats as $k) {
			if ($k->parent != 0) {
				$subCat = '/' . $k->slug;
				$pCat = $k->parent;
			} else {
				$priCat = $k->slug;
				$subCat .= '';
			}
			$catCount++;
		}
		// if child category is checked (eg. all access)
		if ($catCount < 2 && $pCat != 0) {
			$priCat = get_category($pCat)->slug;
		}
		$catLink = $priCat . $subCat;
		$link = str_replace('mks-cpost', 'michaels-edit/' . $catLink, $link);
		$link = str_replace('mks-cat', 'michaels-edit/' . array_pop($cats)->slug, $link);
	}
	if ($cats = get_the_terms($post->ID, 'jet-category')) {
        $tags = get_the_tags();
        $celeb = get_the_terms($post->ID, 'celebrity-tag');
		if ( isset($_GET['page_type']) && empty($tags)) {
			$link = str_replace('jet-cpost', 'jet-set/' . array_pop($cats)->slug, $link);
			$link .= '/slideshow/';
		} else if (!empty($tags)) {
			$link = str_replace('jet-cpost', 'jet-set/travel-diaries/location/' . array_pop(get_the_tags())->slug, $link);
		} else if (!empty($celeb)){
			$link = str_replace('jet-cpost', 'jet-set/celebrities/' . array_pop($celeb)->slug, $link);
			$link .= 'slideshow/';
		} else {
			$link = str_replace('jet-cpost', 'jet-set/' . array_pop($cats)->slug, $link);
		}
	}
	if ($cats = get_the_terms($post->ID, 'fashion-category')) {
		if ( isset($_GET['page_type'])) {
    		$link = str_replace('fashion-style-cpost', 'runway/' . array_pop($cats)->slug, $link);
			$link .= '/slideshow/';
		} else {
			// need to find better way to nesting the sub cat and sub-sub cat
			// $catLink = '';
			$catCount = 0;
			$subCat = '';
			$priCat = '';
			$pCat = '';
			$catLink = '';
			foreach ($cats as $k) {
				if ($k->parent != 0) {
					$subCat = '/' . $k->slug;
					$pCat = $k->parent;
				} else {
					$priCat = $k->slug;
					$subCat .= '';
				}
				$catCount++;
			}
			// if child category is checked (eg. all access)
			if ($catCount < 2 && $pCat != 0) {
				$priCat = get_category($pCat)->slug;
			}
			$catLink = $priCat . $subCat;
			$link = str_replace('fashion-style-cpost', 'runway/' . $catLink, $link);
			$link = str_replace('fashion-style-cat', 'runway/' . array_pop($cats)->slug, $link);
		}
	}

	if ($cats = get_the_terms($post->ID, 'kors-cares-category')) {
		$link = str_replace('cares-cpost', 'kors-cares/' . array_pop($cats)->slug, $link);
		/*
		if ( isset($_GET['page_type']) && empty($tags)) {
			$link = str_replace('cares-cpost', 'kors-cares/' . array_pop($cats)->slug, $link);
			$link .= '/slideshow/';
		} else if (!empty($celeb)){
			$link = str_replace('cares-cpost'=, 'kors-cares/celebrities/' . array_pop($celeb)->slug, $link);
			$link .= 'slideshow/';
		} else {
			$link = str_replace('cares-cpost', 'kors-cares/' . array_pop($cats)->slug, $link);
		}
		*/
	}


	return $link;
}
add_filter('post_type_link', 'filter_post_type_link', 10, 2);

/**
 * OVerride custom taxonomy landing page link
 *
 * @param unknown $post_type
 * @return boolean
 */
/*
function filter_archive_link( $post_type ) {
	global $wp_rewrite;
	if (!$post_type_obj = get_post_type_object($post_type))
		return false;
	if (!$post_type_obj->has_archive)
		return false;

	if (get_option('permalink_structure') && is_array($post_type_obj->rewrite)) {
		$struct = (true === $post_type_obj->has_archive) ? $post_type_obj->rewrite['slug'] : $post_type_obj->has_archive;
		if($post_type_obj->rewrite['with_front'])
			$struct = $wp_rewrite->front . $struct;
		else
			$struct = $wp_rewrite->root . $struct;
		$link = home_url(user_trailingslashit($struct, 'post_type_archive'));
	} else {
		$link = home_url('?post_type=' . $post_type);
	}
	$link = str_replace('fashion-style-cat', 'fashion-and-style', $link);

	return apply_filters('post_type_archive_link', $link, $post_type);
}
*/
//add_filter('post_type_archive_link', 'filter_archive_link', 10, 2);

function testingArchfilter() {
	global $wp_rewrite;
	$link = str_replace('fashion-style-cat', 'runway', $link);

	return $link;
}

//add_filter('post_type_archive_link', 'testingArchfilter', 10, 2);

function filter_archive_type_link($link, $post_type) {
	$link = str_replace('fashion-style-cat', 'runway/', $link);

	return $link;
}

/*
if (!function_exists('get_archive_link')) {
  function get_archive_link( $post_type ) {
    global $wp_post_types;
    $archive_link = false;
    if (isset($wp_post_types[$post_type])) {
      $wp_post_type = $wp_post_types[$post_type];
      if ($wp_post_type->publicly_queryable)
        if ($wp_post_type->has_archive && $wp_post_type->has_archive!==true)
          $slug = $wp_post_type->has_archive;
        else if (isset($wp_post_type->rewrite['slug']))
          $slug = $wp_post_type->rewrite['slug'];
        else
          $slug = $post_type;
      $archive_link = get_option( 'siteurl' ) . "/{$slug}/";
    }
    return apply_filters( 'archive_link', $archive_link, $post_type );
  }
}
*/

add_filter('rewrite_rules_array', 'cp_rewrite_rules');
/**
 * URL rules for custom post type // Custom post hierarchy rewriting
 *
 * @param string $rules
 */
function cp_rewrite_rules($rules) {
	$newRules = array();
	// TODO: find right way for more flexible way of redirecting.
	// needs for single page load. above than tag
	$newRules['jet-set/travel-diaries/location/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?tag=$matches[1]&paged=$matches[2]';
	$newRules['jet-set/travel-diaries/location/(.+)/(.+)/?$'] = 'index.php?tag=$matches[1]&jet-cpost=$matches[2]';
	$newRules['jet-set/travel-diaries/location/(.+)/?$'] = 'index.php?tag=$matches[1]';
	$newRules['jet-set/travel-diaries/location/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$newRules['jet-set/travel-diaries/location/([^/]+)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$newRules['jet-set/(.+)/(.+)/(.+)/?$'] = 'index.php?jet-cpost=$matches[2]&page_type=$matches[3]';
	$newRules['jet-set/(.+)/(.+)/?$'] = 'index.php?jet-cpost=$matches[2]';
	// this need to be added for subcategory landing page.....but but but...post view.
	// ...:S..I need to add another param to archive landing
	//$newRules['fashion-and-style/spotlight-on/(.+)/?$'] = 'index.php?fashion-category=$matches[1]';

	$newRules['michaels-edit/spotlight-on/(kors-collaborators|the-essentials|whats-in-your-kors|trend-report)/(.+)/?$'] = 'index.php?mks-cpost=$matches[2]';
	$newRules['michaels-edit/spotlight-on/(kors-collaborators|the-essentials|whats-in-your-kors|trend-report)/?$'] = 'index.php?mks-edit-category=$matches[1]';
	$newRules['michaels-edit/(.+)/(.+)/?$'] = 'index.php?mks-cpost=$matches[2]';

	$newRules['runway/runway-shows/all-access/(.+)/?$'] = 'index.php?fashion-style-cpost=$matches[1]';
	$newRules['runway/(.+)/(.+)/(.+)/?$'] = 'index.php?fashion-style-cpost=$matches[2]&page_type=$matches[3]';
	$newRules['runway/(.+)/(.+)/?$'] = 'index.php?fashion-style-cpost=$matches[2]';

	$newRules['kors-cares/(.+)/(.+)/?$'] = 'index.php?kors-cares-cpost=$matches[2]';

    $newRules['fieldvisitsweeps/(.+)/?$'] = 'index.php?sweeps-cpost=$matches[1]';
    $newRules['fieldvisitsweeps-terms/(.+)/?$'] = 'index.php?sweeps-terms-cpost=$matches[1]';
    $newRules['fieldvisitsweeps-rules/(.+)/?$'] = 'index.php?sweeps-rules-cpost=$matches[1]';

    $newRules['APIProxy/?$'] = 'index.php?proxy=yes';
		$newRules['manual/?$'] = 'index.php?manual=yes';
		$newRules['manual/images/(.+)$'] = 'index.php?manual_file=yes&file_dir=images&file_name=$matches[1]';
		$newRules['manual/css/(.+)$'] = 'index.php?manual_file=yes&file_dir=css&file_name=style.css';

	return array_merge($newRules, $rules);
}

add_filter('pre_get_posts', 'query_custom_post_type');
/**
 * Register tag for custom post type
 * Add query var for custom post type
 *
 * @param array $query
 * @return array
 */
function query_custom_post_type($query) {
	// also check query vars's suppress filter. if not nav menu item will be reset
	if (is_tag() && empty( $query->query_vars['suppress_filters'])) {

		$post_type = get_query_var('post_type');

		if($post_type) $post_type = $post_type;
		else $post_type = array('jet', 'fashion', 'mks-edit'); // apply this for jet only for now

		$query->set('post_type',$post_type);

		return $query;
	}
}

?>
