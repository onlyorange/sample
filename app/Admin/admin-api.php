<?php
// for theme controller..
define('ADMIN_POSITION', 200); // Dashboard->1, Posts->5, Comments->25, 26 - 59->free, second separator->60..not sure need to double check

function swBrandingFooter($default_text) {
    return '<span id="sw-theme">&copy; <a href="http://www.michaelkors.com/" target="_blank">Michael Kors</a>
            | Powered by <a href="http://wordpress.org/" target="_blank">wordpress</a></span>';
}
// init admins js/css
function adminScripts() {
    wp_enqueue_style('CSS_admin', ADMIN_URL . '/gui/admin.css' );
    wp_enqueue_script('JS_admin', ADMIN_URL . '/gui/admin.js', array('jquery') );
    wp_enqueue_style('LeafletCSS', 'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css');
    wp_enqueue_script('LeafletJS','http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js');

}
// update branding for footer
add_filter('admin_footer_text', 'swBrandingFooter');
add_action('admin_enqueue_scripts', 'adminScripts');

// theme activation hook
// TODO: build this?
function activatingTheme() {
    global $pagenow;

    if ($pagenow == 'themes.php' && isset($_GET['activated'])) {
        // try to change write permissions
        @chmod(CACHE_DIR, 0777);
        @chmod(THEME_STYLESHEET_FILE, 0777);
        @touch(THEME_STYLESHEET_FILE, time() - 30);

        // can do this and that...
        // do_action('this');

        // and maybe initial data load..?? for theme controller..again..not sure we are going to
        // make a theme controller or not

    }
}
add_action('load-themes.php', 'activatingTheme');

// removing wordpress junks from dashboard
add_action('wp_dashboard_setup', 'cleanupDashboard' );
function cleanupDashboard() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
}

// Remove admin pages we don't use
add_action( 'admin_menu', 'adjust_the_wp_menu', 999 );
function adjust_the_wp_menu() {
    $page = remove_submenu_page( 'themes.php', 'widgets.php','post-new.php?post_type=sweeps' );
    // $page[0] is the menu title
    // $page[1] is the minimum level or capability required
    // $page[2] is the URL to the item's file
}

// adding new item to amin dashboard
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

function dashboardWidgetFunc() {
    $txt = "Add something? like GA?";
    echo $txt;
    echo '<br/><a href="?error=1">Error list</a></br>';
}

function example_add_dashboard_widgets() {
    wp_add_dashboard_widget( 'dashboard_widget', 'New Dashboard Item', 'dashboardWidgetFunc' );

    // Globalize the metaboxes array, this holds all the widgets for wp-admin

    global $wp_meta_boxes;

    // Get the regular dashboard widgets array
    // (which has our new widget already but at the end)

    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

    // Backup and delete our new dashboard widget from the end of the array

    $widget_backup = array( 'dashboard_widget' => $normal_dashboard['dashboard_widget'] );
    unset( $normal_dashboard['dashboard_widget'] );

    $sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );

    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}

// adding CTA button label controller
add_action('admin_menu', 'initThemeSetting');

function initThemeSetting() {
    // create new top-level menu
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
    global $submenu;
    global $menu;
    // remove wp default add new button
    unset($submenu['edit.php?post_type=mks-edit'][10]);
    unset($submenu['edit.php?post_type=jet'][10]);
    unset($submenu['edit.php?post_type=fashion'][10]);
    unset($submenu['edit.php?post_type=kors-cares'][10]);

    // remove comment menu
    unset($menu[25]);

    add_menu_page(
        __('Home Page','swedenWp'),
        __('Home','swedenWp'),
        'manage_options',
        'post.php?post=57&action=edit',
        '',
        'dashicons-admin-home', // icon
        5
    );
    add_menu_page(
	    __('Trending Page','swedenWp'),
	    __('Trending','swedenWp'),
	    'manage_options',
	    'post.php?post=5470&action=edit',
	    '',
	    'dashicons-admin-home', // icon
	    11
    );
    /*
    add_menu_page(
	    __('Kors Cares','swedenWp'),
	    __('Kors Cares','swedenWp'),
	    'manage_options',
	    'post.php?post=11644&action=edit',
	    '',
	    'dashicons-admin-home', // icon
	    12
    );
    */
    add_menu_page(
        __('General','swedenWp'),
        __('Theme Settings','swedenWp'),
        'manage_options',
        'themeSettingHandle',
        'generalSetting',
        '', // icon
        ADMIN_POSITION
    );
    add_submenu_page(
        'themeSettingHandle',
        __('Public API', 'swedenWp'),
        __('Public API','swedenWp'),
        'manage_options',
        'public-api',
        'JSON_API::admin_options'
    );
    add_submenu_page(
        'themeSettingHandle',
        __('CTA Label setting','swedenWp'),
        __('CTA Label','swedenWp'),
        'manage_options',
        'cta-label',
        'swedenWpFunctions::admin_cta_form'
    );
    add_submenu_page(
        'themeSettingHandle',
        __('Import Initial Data','menu-test'),
        __('Import Initial Data','menu-test'),
        'manage_options',
        'init-data',
        'import_init_data_page'
    );
    add_submenu_page(
        'themeSettingHandle',
        __('Multi-site sync','swedenWp'),
        __('Multi-site sync','swedenWp'),
        'manage_options',
        'multisite-sync',
        'netSync::admin_menu_settings'
    );
    // testing

    $mks = swedenWpFunctions::custom_active_terms($post_type = 'mks-edit');
    $fashion = swedenWpFunctions::custom_active_terms($post_type = 'fashion');
    $jetset = swedenWpFunctions::custom_active_terms($post_type = 'jet');
    $cares = swedenWpFunctions::custom_active_terms($post_type = 'kors-cares');
    $sweeps = swedenWpFunctions::custom_active_terms($post_type = 'sweeps');

    add_submenu_page(
        'edit.php?post_type=mks-edit',
        __('Page Landing', 'swedenWp'),
        $mks,
        'manage_options',
        'post-new.php?post_type=mks-edit',
        ''
    );
    add_submenu_page(
        'edit.php?post_type=fashion',
        __('Page Landing', 'swedenWp'),
        $fashion,
        'manage_options',
        'post-new.php?post_type=fashion',
        ''
    );
    add_submenu_page(
        'edit.php?post_type=jet',
        __('Page Landing', 'swedenWp'),
        $jetset,
        'manage_options',
        'post-new.php?post_type=jet',
        ''
    );
    add_submenu_page(
	    'edit.php?post_type=kors-cares',
	    __('Page Landing', 'swedenWp'),
	    $cares,
	    'manage_options',
	    'post-new.php?post_type=kors-cares',
	    ''
	);

    add_submenu_page(
        'edit.php?post_type=sweeps',
        __('Page Landing', 'swedenWp'),
        'Sweeps Rules',
        'manage_options',
        'edit.php?post_type=sweeps-rules',
        ''
    );
    add_submenu_page(
        'edit.php?post_type=sweeps',
        __('Page Landing', 'swedenWp'),
        'Sweeps Terms',
        'manage_options',
        'edit.php?post_type=sweeps-terms',
        ''
    );
    // add_submenu_page(
    //     'edit.php?post_type=sweeps',
    //     __('Page Landing', 'swedenWp'),
    //     'Add New Terms',
    //     'manage_options',
    //     'post-new.php?post_type=sweeps-terms',
    //     ''
    // );
    // add_submenu_page(
    //     'edit.php?post_type=sweeps',
    //     __('Page Landing', 'swedenWp'),
    //     'Add New Rules',
    //     'manage_options',
    //     'post-new.php?post_type=sweeps-rules',
    //     ''
    // );


    $landingPages = array(
            // Michaels' edit
            array(
                'post_type' => 'mks-edit',
                'page_id' => '104', //'page_id' => '5',
                'page_name' => '<span style="display:block;
                    margin:0 0 10px 0;
                    padding:0;
                    height:1px;
                    line-height:1px;
                    background:#CCCCCC;"></span>Landing Page'
            ),
            array(
                    'post_type' => 'mks-edit',
                    'page_id' => '247', //'page_id' => '8',
                    'page_name' => 'Must-Haves'
            ),
            array(
                    'post_type' => 'mks-edit',
                    'page_id' => '231', //'page_id' => '10',
                    'page_name' => "Style Confidential"
            ),
    		array(
    				'post_type' => 'mks-edit',
    				'page_id' => '241', //'page_id' => '18',
    				'page_name' => 'Spotlight On'
    		),

            // Jet set
            array(
                'post_type' => 'jet',
                'page_id' => '215', //'page_id' => '11',
                'page_name' => '<span style="display:block;
                    margin:0 0 10px 0;
                    padding:0;
                    height:1px;
                    line-height:1px;
                    background:#CCCCCC;"></span>Landing Page'
            ),
            array(
                    'post_type' => 'jet',
                    'page_id' => '228', //'page_id' => '12',
                    'page_name' => 'Celebrities'
            ),
            array(
                    'post_type' => 'jet',
                    'page_id' => '225', //'page_id' => '13',
                    'page_name' => 'Travel Diaries'
            ),
    		/* check tag.php for location landing page
            array(
                    'post_type' => 'jet',
                    'page_id' => '126', //'page_id' => '14',
                    'page_name' => 'Travel Diaries - Location'
            ),
            */
    		array(
    				'post_type' => 'jet',
    				'page_id' => '4419', //'page_id' => '14',
    				'page_name' => 'Around the World'
    		),

            // Fashion and Style
            array(
                'post_type' => 'fashion',
                'page_id' => '213', //'page_id' => '15',
                'page_name' => '<span style="display:block;
                    margin:0 0 10px 0;
                    padding:0;
                    height:1px;
                    line-height:1px;
                    background:#CCCCCC;"></span>Landing Page
            		'
            ),
            array(
                    'post_type' => 'fashion',
                    'page_id' => '243', //'page_id' => '16',
                    'page_name' => 'Lookbooks'
            ),
            array(
                    'post_type' => 'fashion',
                    'page_id' => '131', //'page_id' => '17',
                    'page_name' => 'Runway Shows'
            ),
            array(
                    'post_type' => 'fashion',
                    'page_id' => '245', //'page_id' => '19',
                    'page_name' => 'Ad Campaigns'
            ),

    		// kors cares
    		array(
    				'post_type' => 'kors-cares',
    				'page_id' => '11644',
    				'page_name' => '<span style="display:block;
                    margin:0 0 10px 0;
                    padding:0;
                    height:1px;
                    line-height:1px;
                    background:#CCCCCC;"></span>Landing Page
            		'
    		),
    		array(
    				'post_type' => 'kors-cares',
    				'page_id' => '11907',
    				'page_name' => 'Watch Hunger Stop'
    		),
    		array(
    				'post_type' => 'kors-cares',
    				'page_id' => '11909',
    				'page_name' => 'Gods Love We Deliver'
    		),
    		
    		// sweepstakes
    		array(
    				'post_type' => 'sweeps',
    				'page_id' => '12934',
    				'page_name' => '<span style="display:block;
                    margin:0 0 10px 0;
                    padding:0;
                    height:1px;
                    line-height:1px;
                    background:#CCCCCC;"></span>Landing Page
            		'
    		),
    );
    //var_dump($landingPages);

    foreach ($landingPages as $landingPage) {
        add_submenu_page(
            'edit.php?post_type=' . $landingPage['post_type'],
            __('Page Landing', 'swedenWp'),
            __($landingPage['page_name'], 'swedenWp'),
            'manage_options',
            'post.php?post='. $landingPage['page_id'] .'&action=edit&post_type=' . $landingPage['post_type'],
            ''
        );
        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
    }

    /*
    array_walk_recursive($landingPages, function($post_type, $postID) {
        add_submenu_page(
            'edit.php?post_type=' . $post_type,
            __('Page Landing', 'swedenWp'),
            __($landingPage['page_name'], 'swedenWp'),
            'manage_options',
            'post.php?post='. $postID .'&action=edit',
            ''
        );
    });
    */

    //call register settings function
    add_action('admin_init', 'swedenWpFunctions::register_cta_setting');
}

function customeLink() {
    ?>
    <script>
    $('#menu-posts-mks-edit').addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-post');
    </script>
    <?php
}
function generalSetting() {
    ?>
    <div class="wrap">
        <div id='icon-options-general' class='icon32'><br /></div>
        <h2>General Settings</h2>
    </div>
    <?php
}

/**
 *
 * Use wp tag as jet-set location
 */
function change_tax_object_label() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['post_tag']->labels;
    $labels->name = 'Locations';
    $labels->singular_name = 'Location';
    $labels->add_new = 'Add Location';
    $labels->add_new_item = 'Add Location';
    $labels->edit_item = 'Edit Locations';
    $labels->new_item = 'Location';
    $labels->view_item = 'View Location';
    $labels->search_items = 'Search Locations';
    $labels->not_found = 'No Locations found';
    $labels->not_found_in_trash = 'No Locations found in Trash';
    $labels->popular_items = 'Popular Locations';
    $labels->all_items = 'All Locations';
    $labels->update_item = 'Update Location';
    $labels->new_item_name = 'New Location Name';
    $labels->separate_items_with_commas = 'Separate locations with commas';
    $labels->add_or_remove_items = 'Add or remove locations';
    $labels->choose_from_most_used = 'Choose from the most used locations';
    $labels->menu_name = 'Locations';

    /*
     [parent_item] =>
    [parent_item_colon] =>
    [name_admin_bar] => post_tag
    */
}
add_action( 'init', 'change_tax_object_label' );

function import_init_data_page() {

?>
    <h2>Import Initial Data</h2>
    <p><strong>Warning!</strong><br> DO NOT INSTALL initial data on your live website. <br>It will corrupt your existing data.</p>
    <p>I suggest you install this data only on clean WordPress setup.</p>
    <br/>
    <?php
    if (importInitData::isDummyInstalled('data_install')): ?>
        <div id="message" class="updated fade" style="width:450px;margin:15px 30px !important"><p><strong>data content already installed.</strong></p></div>
    <?php endif; ?>
    <input name="install_data" type="submit" value=" Install Initial Data " class="install_data" />

    <br/><br/>
    <img class="install_data_loading" style="display:none;" src="images/loading.gif" alt="Loading" />
    <p class="install_data_result"></p>
    <br/><br/>
    <h2>XML DATA FEED FILE</strong></h2>
    <a class="install_data" target="_blank" href='<?php echo get_site_url() . '/data_feed.xml' ?>'>Download XML Feed File</a>

    <?php
}

function html_permission_multisite($caps, $cap, $user_id, $args) {
	$user = new WP_User( $user_id );
	$role = '';
	if(!empty($user->roles) && is_array($user->roles)) {
		$role = $user->roles[0];
	}
	if($role == 'editor' || $role == 'administrator') 
		$rawEdit = true;
	else $rawEdit = false;
	if($cap == 'unfiltered_html' && $rawEdit) {
		unset( $caps );
		$caps[] = $cap;
	}
	return $caps;
}
add_filter('map_meta_cap', 'html_permission_multisite', 10, 4);

add_action('admin_footer', 'mkDomainOption');
function mkDomainOption(){
	global $pagenow;
	add_blog_option($_GET['id'], 'mkDomain', '');
	add_blog_option($_GET['id'], 'mkAPICountry', '');
	add_blog_option($_GET['id'], 'mkAPILanguage', '');
	if( 'site-info.php' == $pagenow && $_GET['id']) { ?>
		<table>
			<tr class="form-field" id="mkDomain">
            <th scope="row">MK Domain</th>
            <td><code><?php echo get_blog_option($_GET['id'], 'mkDomain'); ?></code></td>
        	</tr>
        	<tr class="form-field" id="mkAPICountry">
            <th scope="row">MK API Country</th>
            <td><code><?php echo get_blog_option($_GET['id'], 'mkAPICountry'); ?></code></td>
        	</tr>
        	<tr class="form-field" id="mkAPILanguage">
            <th scope="row">MK API Language</th>
            <td><code><?php echo get_blog_option($_GET['id'], 'mkAPILanguage'); ?></code></td>
        	</tr>
        </table>
        <script>jQuery(function($){
            $('.form-table tbody').append($('#mkDomain, #mkAPICountry, #mkAPILanguage'));
        });</script><?php
    }
}

function ajax_install_data() {
    //include("data/import.php");
    //run();
    $importer = new importInitData();
    $importer->run();

}
add_action('wp_ajax_install_data', 'ajax_install_data');

   if (class_exists('MultiPostThumbnails')) {
        new MultiPostThumbnails(
            array(
                'label' => 'Category Landscape Image (960x495 ideal)',
                'id' => 'category-landscape-image',
                'post_type' => 'jet-set'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Featured Mobile Image',
                'id' => 'featured-mobile-image',
                'post_type' => 'jet-set'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Handwriting Image',
                'id' => 'handwriting-image',
                'post_type' => 'jet-set'
            )
        );

        new MultiPostThumbnails(
            array(
                'label' => 'Category Landscape Image (960x495 ideal)',
                'id' => 'category-landscape-image',
                'post_type' => 'jet'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Featured Mobile Image',
                'id' => 'featured-mobile-image',
                'post_type' => 'jet'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Handwriting Image',
                'id' => 'handwriting-image',
                'post_type' => 'jet'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Category Landscape Image (960x495 ideal)',
                'id' => 'category-landscape-image',
                'post_type' => 'mks-edit'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Featured Mobile Image',
                'id' => 'featured-mobile-image',
                'post_type' => 'mks-edit'
            )
        );

        new MultiPostThumbnails(
            array(
                'label' => 'Category Landscape Image (960x495 ideal)',
                'id' => 'category-landscape-image',
                'post_type' => 'fashion'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Featured Mobile Image',
                'id' => 'featured-mobile-image',
                'post_type' => 'fashion'
            )
        );
        new MultiPostThumbnails(
            array(
                'label' => 'Handwriting Image',
                'id' => 'handwriting-image',
                'post_type' => 'fashion'
            )
        );
    }

?>
