<?php
/*
Plugin Name: Sweden Unlimited Media Library
Plugin URI: http://swedenunlimited.com
Description: Media Libraray Helper.
Version: 0.0.5
Author: Juhong Lee
Author URI: http://devjuhong.com
License: 
*/

$swml_version = '0.0.5';
$swml_old_version = get_option('swml_version', false);

// build this as plugin???
$swml_dir = ADMIN_URL . '/gui/';
/**
 * 
 * unassian taxs
 * 
 * @param unknown $taxonomy
 * @param unknown $object_type
 * @return boolean
 */
function swml_unregister_taxonomy_for_object_type( $taxonomy, $object_type ) {
	global $wp_taxonomies;

	if (!isset($wp_taxonomies[$taxonomy]))
		return false;

	if (!get_post_type_object($object_type))
		return false;

	$key = array_search($object_type, $wp_taxonomies[$taxonomy]->object_type, true);
	if (false === $key)
		return false;

	unset($wp_taxonomies[$taxonomy]->object_type[$key]);
	return true;
}

/**
 * 
 * taxs validation
 * TODO: more deeper validation needs for unassigned attachment
 * 
 * @param unknown $input
 * @return Ambigous <multitype:, string, mixed>
 */
function swml_taxonomies_validate($input) {		
	if (!$input) $input = array();

	foreach ($input as $taxonomy => $params) {	
		$sanitized_taxonomy = sanitize_key($taxonomy);
		
		if($sanitized_taxonomy !== $taxonomy) {
			$input[$sanitized_taxonomy] = $input[$taxonomy];
			unset($input[$taxonomy]);
			$taxonomy = $sanitized_taxonomy;
		}
		
		if (!isset($params['hierarchical']))
			$input[$taxonomy]['hierarchical'] = 0;		
		
		if (!isset($params['sort']))
			$input[$taxonomy]['sort'] = 0;
		
		if (!isset($params['show_admin_column']))
			$input[$taxonomy]['show_admin_column'] = 0;
		
		if (!isset($params['show_in_nav_menus']))
			$input[$taxonomy]['show_in_nav_menus'] = 0;

		if (!isset($params['assigned']))
			$input[$taxonomy]['assigned'] = 0;

		if (!isset($params['admin_filter']))
			$input[$taxonomy]['admin_filter'] = 0;
		
		if (!isset($params['media_uploader_filter']))
			$input[$taxonomy]['media_uploader_filter'] = 0;
			
		$input[$taxonomy]['hierarchical'] = intval($input[$taxonomy]['hierarchical']);
		$input[$taxonomy]['sort'] = intval($input[$taxonomy]['sort']);
		$input[$taxonomy]['show_admin_column'] = intval($input[$taxonomy]['show_admin_column']);
		$input[$taxonomy]['show_in_nav_menus'] = intval($input[$taxonomy]['show_in_nav_menus']);
		$input[$taxonomy]['assigned'] = intval($input[$taxonomy]['assigned']);
		$input[$taxonomy]['admin_filter'] = intval($input[$taxonomy]['admin_filter']);
		$input[$taxonomy]['media_uploader_filter'] = intval($input[$taxonomy]['media_uploader_filter']);
		
		if (isset($params['labels'])) {
			$default_labels = array(
				'menu_name' => $params['labels']['name'],
				'all_items' => 'All ' . $params['labels']['name'],
				'edit_item' => 'Edit ' . $params['labels']['singular_name'],
				'view_item' => 'View ' . $params['labels']['singular_name'], 
				'update_item' => 'Update ' . $params['labels']['singular_name'],
				'add_new_item' => 'Add New ' . $params['labels']['singular_name'],
				'new_item_name' => 'New ' . $params['labels']['singular_name'] . ' Name',
				'parent_item' => 'Parent ' . $params['labels']['singular_name'],
				'search_items' => 'Search ' . $params['labels']['name']
			);
			
			foreach ($params['labels'] as $label => $value) {
				$input[$taxonomy]['labels'][$label] = sanitize_text_field($value);
				
				if (empty($value) && isset($default_labels[$label])) {
					$input[$taxonomy]['labels'][$label] = sanitize_text_field($default_labels[$label]);
				}
			}
		}
		
		if (isset($params['rewrite']['slug']))
			$input[$taxonomy]['rewrite']['slug'] = sanitize_key($params['rewrite']['slug']);
	}
	return $input;
}

add_action( 'wp_ajax_query-attachments', 'swml_ajax_query_attachments', 0 );
/**
 * override wp ajax action.
 */
function swml_ajax_query_attachments() {
	if (!current_user_can('upload_files'))
		wp_send_json_error();

	$taxonomies = get_object_taxonomies('attachment','names');

	$query = isset($_REQUEST['query']) ? (array) $_REQUEST['query'] : array();

	$defaults = array(
		's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
		'post_parent', 'post__in', 'post__not_in'
	);
	$query = array_intersect_key($query, array_flip( array_merge($defaults, $taxonomies)));

	$query['post_type'] = 'attachment';
	$query['post_status'] = 'inherit';
	if (current_user_can(get_post_type_object('attachment')->cap->read_private_posts))
		$query['post_status'] .= ',private';
		
	$query['tax_query'] = array('relation' => 'AND');

	foreach($taxonomies as $taxonomy) {		
		
		if (isset($query[$taxonomy]) && is_numeric($query[$taxonomy])) {
			array_push($query['tax_query'],array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => $query[$taxonomy]
			));	
		}
		unset($query[$taxonomy]);
	}

	$query = apply_filters('ajax_query_attachments_args', $query);
	$query = new WP_Query($query);

	$posts = array_map('wp_prepare_attachment_for_js', $query->posts);
	$posts = array_filter($posts);

	wp_send_json_success($posts);
}

/**
 * ajax filtering
 */
add_action('restrict_manage_posts','swml_restrict_manage_posts');

function swml_restrict_manage_posts() {
	global $pagenow, $wp_query;
	
	$swml_taxonomies = get_option('swml_taxonomies');
	
	if($pagenow == 'upload.php') {
		foreach(get_object_taxonomies('attachment','object') as $taxonomy) {
			if ($swml_taxonomies[$taxonomy->name]['admin_filter']) {	
				$selected = 0;				
					
				foreach($wp_query->tax_query->queries as $taxonomy_var) {					
					if ($taxonomy_var['taxonomy'] == $taxonomy->name && $taxonomy_var['field'] == 'slug') {
						$term = get_term_by('slug', $taxonomy_var['terms'][0], $taxonomy->name);
						if ($term) $selected = $term->term_id;
					}
				}
					
				wp_dropdown_categories(
					array(
						'show_option_all' =>  $taxonomy->labels->all_items,
						'taxonomy'        =>  $taxonomy->name,
						'name'            =>  $taxonomy->name,
						'orderby'         =>  'name',
						'selected'        =>  $selected,
						'hierarchical'    =>  true,
						'show_count'      =>  false,
						'hide_empty'      =>  false,
						'hide_if_empty'   =>  true
					)
				);
			}
		}
	}
}

add_filter('parse_query', 'swml_parse_query');

/**
 * parse filtered media
 * 
 * @param array $query
 */
function swml_parse_query($query) {
	global $pagenow;
	
	if ($pagenow=='upload.php') {
		$qv = &$query->query_vars;
		
		// do I really need to worry about post tags here??
		foreach(get_object_taxonomies('attachment','object') as $taxonomy) {
			if(isset( $qv['taxonomy']) && isset($qv['term'])) {
				$tax = $qv['taxonomy'];
				
				if($tax == 'category')
					$tax = 'category_name';
					
				if($tax == 'post_tag')
					$tax = 'tag';					
				
				$qv[$tax] = $qv['term'];
				unset($qv['taxonomy']);
				unset($qv['term']);
			}
			
			if (isset($_REQUEST[$taxonomy->name]) && $_REQUEST[$taxonomy->name] && is_numeric($_REQUEST[$taxonomy->name])) {
				$tax = $taxonomy->name;
				
				if($tax == 'category')
					$tax = 'category_name';
					
				if($tax == 'post_tag')
					$tax = 'tag'; 
				
				$term = get_term_by('id', $_REQUEST[$taxonomy->name], $taxonomy->name);
				
				if ($term) 
					$qv[$tax] = $term->slug;
			}
		}
	}
}


add_filter( 'attachment_fields_to_edit', 'swml_attachment_fields_to_edit', 10, 2 );

/**
 * 
 * updating wp core fields for media
 * 
 * @param unknown $form_fields
 * @param unknown $post
 * @return string
 */
function swml_attachment_fields_to_edit($form_fields, $post) {	
	foreach(get_attachment_taxonomies($post->ID) as $taxonomy) {
		$terms = get_object_term_cache($post->ID, $taxonomy);
		
		$t = (array) get_taxonomy($taxonomy);
		if (!$t['public'] || !$t['show_ui'])
			continue;
		if (empty($t['label']))
			$t['label'] = $taxonomy;
		if (empty($t['args']))
			$t['args'] = array();
		
		
		if (false === $terms)
			$terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);
			
		$values = array();
	
		foreach ($terms as $term)
			$values[] = $term->slug;
			
		$t['value'] = join(', ', $values);
		$t['show_in_edit'] = false;
		
		// currently I am extending walker to get term list..
		// TODO: find better way to display menu..
		if ($t['hierarchical']) {
			ob_start();
			
				wp_terms_checklist($post->ID, array('taxonomy' => $taxonomy, 'checked_ontop' => false, 'walker' => new Walker_Media_Taxonomy_Checklist()));
				
				if (ob_get_contents() != false)
					$html = '<ul class="term-list">' . ob_get_contents() . '</ul>';
				else
					$html = '<ul class="term-list"><li>No ' . $t['label'] . '</li></ul>';
			
			ob_end_clean();
			
			$t['input'] = 'html';
			$t['html'] = $html; 
		}
	
		$form_fields[$taxonomy] = $t;
	}

	return $form_fields;
}

/**
 * Tax checklist
 * 
 * @author JL
 *
 */
class Walker_Media_Taxonomy_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); 

	function start_lvl(&$output, $depth = 0, $args = array())  {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
		extract($args);
		
		if (empty($taxonomy))
			$taxonomy = 'category';

		$name = 'tax_input['.$taxonomy.']';

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->slug . '" type="checkbox" name="'.$name.'['. $category->slug.']" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el(&$output, $category, $depth = 0, $args = array()) {
		$output .= "</li>\n";
	}
}

/**
 * Tax uploader filtering
 * 
 * @author JL
 *
 */
class Walker_Media_Taxonomy_Uploader_Filter extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); 
	
	function start_lvl(&$output, $depth = 0, $args = array()) {
		$output .= "";
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
		$output .= "";
	}

	function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
		extract($args);

		$indent = str_repeat('&nbsp;&nbsp;&nbsp;', $depth); 
		$output .= $category->term_id . '>' . $indent . esc_html( apply_filters('the_category', $category->name )) . '|';
	}

	function end_el(&$output, $category, $depth = 0, $args = array()) {
			$output .= "";
	}
}

add_action( 'wp_ajax_save-attachment-compat', 'swml_save_attachment_compat', 0 );

/**
 * overriding default wp ajax action
 * 
 * WP ajax actions
 */
function swml_save_attachment_compat() {	
	if (!isset( $_REQUEST['id']))
		wp_send_json_error();

	if (!$id = absint( $_REQUEST['id']))
		wp_send_json_error();

	if (empty( $_REQUEST['attachments'] ) || empty( $_REQUEST['attachments'][ $id ]))
		wp_send_json_error();
	$attachment_data = $_REQUEST['attachments'][ $id ];

	check_ajax_referer('update-post_' . $id, 'nonce');

	if (!current_user_can('edit_post', $id))
		wp_send_json_error();

	$post = get_post($id, ARRAY_A);

	if ('attachment' != $post['post_type'])
		wp_send_json_error();

	/* wp-admin/includes/media.php */
	$post = apply_filters('attachment_fields_to_save', $post, $attachment_data);

	if (isset( $post['errors'])) {
		// TODO: return me and display me
		$errors = $post['errors'];
		unset($post['errors']);
	}

	wp_update_post($post);

	foreach (get_attachment_taxonomies($post) as $taxonomy) {		
		if (isset($attachment_data[$taxonomy]))
			wp_set_object_terms($id, array_map('trim', preg_split('/,+/', $attachment_data[$taxonomy])), $taxonomy, false);
		else if (isset($_REQUEST['tax_input']) && isset($_REQUEST['tax_input'][$taxonomy]))
			wp_set_object_terms($id, $_REQUEST['tax_input'][$taxonomy], $taxonomy, false);
		else 
			wp_set_object_terms($id, '', $taxonomy, false);
	}

	if (!$attachment = wp_prepare_attachment_for_js($id))
		wp_send_json_error();
	
	wp_send_json_success($attachment);
}

add_action( 'pre_get_posts', 'swml_pre_get_posts', 99 );

/**
 * 
 * tax archive specific query for front end
 * @param unknown $query
 */
function swml_pre_get_posts($query) {
	$swml_taxonomies = get_option('swml_taxonomies');
	
	if (is_array($swml_taxonomies)) {
		foreach($swml_taxonomies as $taxonomy => $params) {
			if ($params['assigned'] && $params['swml_media'] && $query->is_main_query() && is_tax($taxonomy) && !is_admin()) {
				$query->set('post_type', 'attachment');
				$query->set('post_status', 'inherit');
			}	
		}
	}
}

add_action('init', 'swml_on_init', 12);

/**
 * initialize db and var on activation
 * 
 */
function swml_on_init() {
	// separate some init stuff for not theme type..
	swml_on_activation();
	
	$swml_taxonomies = get_option('swml_taxonomies');
	//print_r($swml_taxonomies);
	if (empty($swml_taxonomies)) $swml_taxonomies = array();
	
	foreach ($swml_taxonomies as $taxonomy => $params) {		
		if ( $params['swml_media'] && !empty($params['labels']['singular_name']) && !empty($params['labels']['name'])) {
			register_taxonomy( 
				$taxonomy, 
				'attachment', 
				array(
					'labels' => $params['labels'],
					'public' => true,
					'show_admin_column' => $params['show_admin_column'],
					'show_in_nav_menus' => $params['show_in_nav_menus'],
					'hierarchical' => $params['hierarchical'],
					'update_count_callback' => '_update_generic_term_count',
					'sort' => $params['sort'],
					'rewrite' => array( 'slug' => $params['rewrite']['slug'] )
				) 
			);
		}
	}
}




/**
 * on worpdress load, load all media library needs
 * 
 * TODO: need to optimize server load
 */
add_action('wp_loaded', 'swml_on_wp_loaded');

function swml_on_wp_loaded() {
	$swml_taxonomies = get_option('swml_taxonomies');
	if (empty($swml_taxonomies))
		$swml_taxonomies = array();
	$taxonomies = get_taxonomies(array(), 'object');
	//print_r($swml_taxonomies);
	//print_r($taxonomies['media_category']);
	foreach ($taxonomies as $taxonomy => $params) {
		//get all custom taxonomies
		if (!empty($params->object_type) && !array_key_exists($taxonomy, $swml_taxonomies)
			&& !in_array('revision',$params->object_type) && !in_array('nav_menu_item',$params->object_type)
			&& $taxonomy != 'post_format') {
			
			$swml_taxonomies[$taxonomy] = array(
				'swml_media' => 0,
				'admin_filter' => 0,
				'media_uploader_filter' => 0,
				'show_admin_column' => isset($params->show_admin_column) ? $params->show_admin_column : 0,
				'show_in_nav_menus' => isset($params->show_in_nav_menus) ? $params->show_in_nav_menus : 0,
				'hierarchical' => $params->hierarchical ? 1 : 0,
				'sort' => isset($params->sort) ? $params->sort : 0
			);
			
			if (in_array('attachment', $params->object_type))
				$swml_taxonomies[$taxonomy]['assigned'] = 1;
			else 
				$swml_taxonomies[$taxonomy]['assigned'] = 0;
		}
	}
	
	// assign/unassign taxonomies to atachment
	foreach ($swml_taxonomies as $taxonomy => $params) {		
		if ($params['assigned'])
			register_taxonomy_for_object_type($taxonomy, 'attachment');
		
		if (!$params['assigned'])
			swml_unregister_taxonomy_for_object_type($taxonomy, 'attachment');
	}
	
	// update_count_callback for attachment taxonomies if needed
	foreach ($taxonomies as $taxonomy => $params) { 
		if (in_array('attachment', $params->object_type)) {
			global $wp_taxonomies;
			
			if (!isset($wp_taxonomies[$taxonomy]->update_count_callback) || empty($wp_taxonomies[$taxonomy]->update_count_callback))
				$wp_taxonomies[$taxonomy]->update_count_callback = '_update_generic_term_count';
		}
	}
	
	update_option('swml_taxonomies', $swml_taxonomies);
}

/**
 * register vars on admin init
 * 
 */
add_action('admin_init', 'swml_on_admin_init');
function swml_on_admin_init() {
	
	// plugin settings: taxonomies
	register_setting( 
		'swml_taxonomies', //option_group
		'swml_taxonomies', //option_name
		'swml_taxonomies_validate' //sanitize_callback
	);
	
	// plugin settings: mime types
	register_setting( 
		'swml_mimes', //option_group
		'swml_mimes', //option_name
		'swml_mimes_validate' //sanitize_callback
	);
	
	// plugin settings: mime types backup
	register_setting( 
		'swml_mimes_backup', //option_group
		'swml_mimes_backup' //option_name
	);
}

add_action('admin_enqueue_scripts', 'swml_admin_enqueue_scripts');

function swml_admin_enqueue_scripts() {
	global $swml_dir, $pagenow;
	
	wp_enqueue_script(
		'swml-media-models-script',
		$swml_dir . '/swml-media-models.js',
		array('jquery','backbone','media-models'),
		'',
		true
	);
	
	wp_enqueue_script(
		'swml-media-views-script',
		$swml_dir . '/swml-media-views.js',
		array('jquery','backbone','media-views'),
		'',
		true
	);
	
	
	// pass taxonomies to media uploadxrer's filter
	$swml_taxonomies = get_option('swml_taxonomies');
	if (empty($swml_taxonomies)) $swml_taxonomies = array();
	
	$taxonomies_array = array();
	foreach (get_object_taxonomies('attachment','object') as $taxonomy) {
		$terms_array = array();
		$terms = array();
		
		if($swml_taxonomies[$taxonomy->name]['media_uploader_filter']) {
			ob_start();
			
				wp_terms_checklist( 0, array(
								'taxonomy' => $taxonomy->name,
								'checked_ontop' => false,
								'walker' => new Walker_Media_Taxonomy_Uploader_Filter()
				));
				
				$html = '';
				if (ob_get_contents() != false) $html = ob_get_contents();
			ob_end_clean();
			
			$terms = array_filter(explode('|', $html));
			
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$term = explode('>', $term);
					array_push($terms_array, array('term_id' => $term[0], 'term_name' => $term[1]));
				}
				$taxonomies_array[$taxonomy->name] = array(
					'list_title' => $taxonomy->labels->all_items,
					'term_list' => $terms_array
				);
			}
		}
	}
	wp_localize_script( 
		'swml-media-views-script', 
		'swml_taxonomies', 
		$taxonomies_array
	);

}

/**
 * only for plugin type
 * 
 * on initial load, check exiting data and initialize data
 * 
 */
function swml_on_activation() {	
	global $swml_version, $swml_old_version;

	if( $swml_version != $swml_old_version )
		update_option('swml_version', $swml_version );
	
	if(empty($swml_old_version)) {		
		$swml_taxonomies['media_category'] = array(
			'assigned' => 1,
			'swml_media' => 1,
			'admin_filter' => 1,
			'media_uploader_filter' => 1,
			'labels' => array(
				'name' => 'Media Categories',
				'singular_name' => 'Media Category',
				'menu_name' => 'Media Categories',
				'all_items' => 'All Media Categories',
				'edit_item' => 'Edit Media Category',
				'view_item' => 'View Media Category',
				'update_item' => 'Update Media Category',
				'add_new_item' => 'Add New Media Category',
				'new_item_name' => 'New Media Category Name',
				'parent_item' => 'Parent Media Category',
				'parent_item_colon' => 'Parent Media Category:',
				'search_items' => 'Search Media Categories'
			),
			'public' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => 'media_category' ),
			'sort' => 0
		);
		update_option('swml_taxonomies', $swml_taxonomies);
	}
}


/**
 * still in concept.
 * 
 */
//add_action('admin_footer-post-new.php', 'wpml_test_script');
//add_action('admin_footer-post.php', 'wpml_test_script');
/**
 * pre select 
 * TODO: this is not right way...find right way to do this. :S
 */
function wpml_test_script() {
	?>
<script>
jQuery(function($) {
    var called = 0;
    $('#wpcontent').ajaxStop(function() {
        if ( 0 == called ) {
            $('[value="51"]').attr( 'selected', true ).parent().trigger('change');
            called = 1;
        }
    });
});
</script>
    <?php
}
?>