<?php
/**
 * 
 * @description: Adds the ability to sync from one site to another on the same network.
 * @author JL
 *
 * TODO : detect wp setup if it's not network site then treat post as different way.
 * TODO : add delete function
 * 
 * Note : ahhhhh....this plainview lib using namespace all over.. :S need to get along with it.
 * net_sync will be main ns for swedenWp class
 */
namespace net_sync;

require_once(FRAMEWORK_DIR . '/Libs/networkSync/vendor/autoload.php' );

use \plainview\sdk\collections\collection;
use \net_sync\sync_data\blog;
use \plainview\sdk\html\div;
if (!class_exists('netSync')) {
    class netSync extends \plainview\sdk\wordpress\base
    {
		use debug;
		private $networkSync = array();
		public $networkSync_data = null;
		public $display_sync = true;
		public $display_sync_columns = true;
		public $display_sync_menu = true;
		public $permalink_cache;
		protected $site_options = array(
			// internal custom field option added
			'sync_internal_custom_fields' => false,
			'canonical_url' => true,
			'clear_post' => true,								// Clear the post before networkSync.
			'custom_field_whitelist' => '_wp_page_template _wplp_ _aioseop_',	// Internal custom fields that should be synced.
			'custom_field_blacklist' => '',						// Internal custom fields that should not be synced.
			'database_version' => 0,							// Version of database and settings
			'debug' => true,									// Display debug information?
			'debug_ips' => '',									// List of IP addresses that can see debug information, when debug is enabled.
			'override_child_permalinks' => false,				// Make the child's permalinks link back to the parent item?
			'post_types' => 'post page mks-edit fashion jet kors-cares',		// Custom post types which use networkSync
			'existing_attachments' => 'use',					// What to do with existing attachments: use, overwrite, randomize
			'role_sync' => 'super_admin',					// Role required to use sync function
			'role_link' => 'super_admin',						// Role required to use the link function
			'role_sync_as_draft' => 'super_admin',			// Role required to sync posts as templates
			'role_sync_scheduled_posts' => 'super_admin',	// Role required to sync scheduled, future posts
			'role_taxonomies' => 'super_admin',					// Role required to sync the taxonomies
			'role_custom_fields' => 'super_admin',				// Role required to sync the custom fields
		);
	
		public function _construct() {
			if ( ! $this->is_network )
				wp_die( $this->_( 'sync requires a Wordpress network to function.' ) );
			
			$this->add_action( 'admin_menu' );
	
			if ( $this->get_site_option( 'override_child_permalinks' ) ) {
				$this->add_filter( 'post_link', 10, 3 );
				$this->add_filter( 'post_type_link', 'post_link', 10, 3 );
			}
			
			$this->add_filter( 'net_sync_admin_menu', 100 );
			$this->add_filter( 'net_sync_sync_post' );
			$this->add_filter( 'net_sync_get_user_writable_blogs', 11 );		// Allow other plugins to do this first.
			$this->add_filter( 'net_sync_get_post_types', 9 );					// Add our custom post types to the array of syncable post types.
			$this->add_action( 'net_sync_manage_posts_custom_column', 9 );		// Just before the standard 10.
			$this->add_action( 'net_sync_maybe_clear_post', 11 );
			$this->add_action( 'net_sync_menu', 9 );
			$this->add_action( 'net_sync_menu', 'net_sync_menu_final', 100 );
			$this->add_action( 'net_sync_prepare_networkSync_data' );
			$this->add_filter( 'net_sync_prepare_meta_box', 9 );
			$this->add_filter( 'net_sync_prepare_meta_box', 'net_sync_prepared_meta_box', 100 );
			$this->add_action( 'net_sync_wp_insert_term', 9 );
			$this->add_action( 'net_sync_wp_update_term', 9 );
			
			if ( $this->get_site_option( 'canonical_url' ) )
				$this->add_action( 'wp_head', 1 );
	
			$this->permalink_cache = new \stdClass;
		}
	
		public function admin_menu() {
			$this->add_action( 'save_post', 200 );
		}
		
		/**
			Deletes a synced post.
		**/
		public function user_delete()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
			$child_blog_id = $_GET[ 'child' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_delete';
			$nonce_key .= '_' . $child_blog_id;
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
	
			switch_to_blog( $child_blog_id );
			$synced_post_id = $sync_data->get_linked_child_on_this_blog();
	
			if ( $synced_post_id === null )
				wp_die( 'No synced child post found on this blog!' );
			wp_delete_post( $synced_post_id, true );
			$sync_data->remove_linked_child( $child_blog_id );
	
			restore_current_blog();
	
			$sync_data = $this->set_post_sync_data( $blog_id, $post_id, $sync_data );
	
			$message = $this->_( 'The child post has been deleted.' );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		/**
			@brief		Deletes all of a post's children.
		**/
		public function user_delete_all()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_delete_all';
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
			foreach( $sync_data->get_linked_children() as $child_blog_id => $child_post_id )
			{
				switch_to_blog( $child_blog_id );
				wp_delete_post( $child_post_id, true );
				$sync_data->remove_linked_child( $child_blog_id );
				restore_current_blog();
			}
	
			$sync_data = $this->set_post_sync_data( $blog_id, $post_id, $sync_data );
	
			$message = $this->_( "All of the child posts have been deleted." );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		/**
			Finds orphans for a specific post.
		**/
		public function user_find_orphans()
		{
			$current_blog_id = get_current_blog_id();
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_find_orphans_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$form = $this->form2();
			$post = get_post( $post_id );
			$r = '';
			$table = $this->table();
	
			$row = $table->head()->row();
			$table->bulk_actions()
				->form( $form )
				->add( $this->_( 'Create link' ), 'create_link' )
				->cb( $row );
			$row->th()->text_( 'Domain' );
	
			$sync_data = $this->get_post_sync_data( $current_blog_id, $post_id );
	
			// Get a list of blogs that this user can link to.
			$filter = new filters\get_user_writable_blogs( $this->user_id() );
			$blogs = $filter->apply()->blogs;
	
			$orphans = array();
	
			foreach( $blogs as $blog )
			{
				if ( $blog->id == $current_blog_id )
					continue;
	
				if ( $sync_data->has_linked_child_on_this_blog( $blog->id ) )
					continue;
	
				$blog->switch_to();
	
				$args = array(
					'cache_results' => false,
					'name' => $post->post_name,
					'numberposts' => 2,
					'post_type'=> $post->post_type,
					'post_status' => $post->post_status,
				);
				$posts = get_posts( $args );
	
				if ( count( $posts ) == 1 )
				{
					$orphan = reset( $posts );
					$orphan->permalink = get_permalink( $orphan->ID );
					$orphans[ $blog->id ] = $orphan;
				}
	
				$blog->switch_from();
			}
	
			if ( $form->is_posting() )
			{
				$form->post();
				if ( $table->bulk_actions()->pressed() )
				{
					switch ( $table->bulk_actions()->get_action() )
					{
						case 'create_link':
							$ids = $table->bulk_actions()->get_rows();
	
							foreach( $orphans as $blog_id => $orphan )
							{
								$bulk_id = sprintf( '%s_%s', $blog_id, $orphan->ID );
								if ( ! in_array( $bulk_id, $ids ) )
									continue;
	
								$sync_data->add_linked_child( $blog_id, $orphan->ID );
								unset( $orphans[ $blog_id ] );		// There can only be one orphan per blog, so we're not interested in the blog anymore.
	
								// Update the child's sync data.
								$child_sync_data = $this->get_post_sync_data( $blog_id, $orphan->ID );
								$child_sync_data->set_linked_parent( $current_blog_id, $post_id );
								$this->set_post_sync_data( $blog_id, $orphan->ID, $child_sync_data );
							}
	
							// Update the sync data for the parent post.
							$this->set_post_sync_data( $current_blog_id, $post_id, $sync_data );
							echo $this->message_( 'The selected children were linked!' );
						break;
					}
				}
			}
	
			if ( count( $orphans ) < 1 )
			{
				$r .= $this->_( 'No possible child posts were found on the other blogs you have write access to. Either there are no posts with the same title as this one, or all possible orphans have already been linked.' );
			}
			else
			{
				foreach( $orphans as $blog_id => $orphan )
				{
					$row = $table->body()->row();
					$bulk_id = sprintf( '%s_%s', $blog_id, $orphan->ID );
					$table->bulk_actions()->cb( $row, $bulk_id );
					$row->td()->text( '<a href="' . $orphan->permalink . '">' . $blogs[ $blog_id ]->blogname . '</a>' );
				}
				$r .= $form->open_tag();
				$r .= $table;
				$r .= $form->close_tag();
			}
	
			echo $r;
	
			echo '<p><a href="edit.php?post_type='.$post->post_type.'">Back to post overview</a></p>';
		}
	
		
		/**
			@brief		Restores a trashed post.
		**/
		public function user_restore()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
			$child_blog_id = $_GET[ 'child' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_restore';
			$nonce_key .= '_' . $child_blog_id;
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
	
			switch_to_blog( $child_blog_id );
	
			$child_post_id = $sync_data->get_linked_child_on_this_blog();
			wp_publish_post( $child_post_id );
	
			restore_current_blog();
	
			$message = $this->_( 'The child post has been restored.' );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		/**
			@brief		Restores all of the children from the trash.
		**/
		public function user_restore_all()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_restore_all';
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
			foreach( $sync_data->get_linked_children() as $child_blog_id => $child_post_id )
			{
				switch_to_blog( $child_blog_id );
				wp_publish_post( $child_post_id );
				restore_current_blog();
			}
	
			$message = $this->_( 'The child posts have been restored.' );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		/**
			Trashes a synced post.
		**/
		public function user_trash()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
			$child_blog_id = $_GET[ 'child' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_trash';
			$nonce_key .= '_' . $child_blog_id;
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
			switch_to_blog( $child_blog_id );
			$synced_post_id = $sync_data->get_linked_child_on_this_blog();
			wp_trash_post( $synced_post_id );
			restore_current_blog();
	
			$message = $this->_( 'The synced child post has been put in the trash.' );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		/**
			Trashes a synced post.
		**/
		public function user_trash_all()
		{
			// Nonce check
			global $blog_id;
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_trash_all';
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
			foreach( $sync_data->get_linked_children() as $child_blog_id => $child_post_id )
			{
				switch_to_blog( $child_blog_id );
				wp_trash_post( $child_post_id );
				restore_current_blog();
			}
	
			$message = $this->_( 'The child posts have been put in the trash.' );
	
			echo $this->message( $message);
			echo sprintf( '<p><a href="%s">%s</a></p>',
				wp_get_referer(),
				$this->_( 'Back to post overview' )
			);
		}
	
		public function user_unlink()
		{
			// Check that we're actually supposed to be removing the link for real.
			$nonce = $_GET[ '_wpnonce' ];
			$post_id = $_GET[ 'post' ];
			if ( isset( $_GET[ 'child' ] ) )
				$child_blog_id = $_GET[ 'child' ];
	
			// Generate the nonce key to check against.
			$nonce_key = 'sync_unlink';
			if ( isset( $child_blog_id) )
				$nonce_key .= '_' . $child_blog_id;
			else
				$nonce_key .= '_all';
			$nonce_key .= '_' . $post_id;
	
			if ( ! wp_verify_nonce( $nonce, $nonce_key) )
				die( __method__ . " security check failed." );
	
			global $blog_id;
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
			$linked_children = $sync_data->get_linked_children();
	
			// Remove just one child?
			if ( isset( $child_blog_id ) )
			{
				$this->delete_post_sync_data( $child_blog_id, $linked_children[ $child_blog_id ] );
				$sync_data->remove_linked_child( $child_blog_id );
				$this->set_post_sync_data( $blog_id, $post_id, $sync_data );
				$message = $this->_( 'Link to child post has been removed.' );
			}
			else
			{
				$blogs_url = array();
				foreach( $linked_children as $linked_child_blog_id => $linked_child_post_id)
				{
					// And about the child blog
					switch_to_blog( $linked_child_blog_id );
					$blogs_url[] = '<a href="'.get_bloginfo( 'url' ).'">'.get_bloginfo( 'name' ).'</a>';
					restore_current_blog();
					$this->delete_post_sync_data( $linked_child_blog_id, $linked_child_post_id );
				}
	
				$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
				$sync_data->remove_linked_children();
				$message = $this->_( 'All links to child posts have been removed!' );
			}
	
			$this->set_post_sync_data( $blog_id, $post_id, $sync_data );
	
			echo '
				'.$this->message( $message).'
				<p>
					<a href="'.wp_get_referer().'">Back to post overview</a>
				</p>
			';
		}
	
		// --------------------------------------------------------------------------------------------
		// ----------------------------------------- Callbacks
		// --------------------------------------------------------------------------------------------
		public function delete_post( $post_id)
		{
			$this->trash_untrash_delete_post( 'wp_delete_post', $post_id );
		}
	
		public function manage_posts_custom_column( $column_name, $parent_post_id )
		{
			if ( $column_name != '3wp_sync' )
				return;
	
			$blog_id = get_current_blog_id();
	
			// Prep the bcd cache.
			$sync_data = $this->sync_data_cache()
				->expect_from_wp_query()
				->get_for( $blog_id, $parent_post_id );
	
			global $post;
			$action = new actions\manage_posts_custom_column();
			$action->post = $post;
			$action->parent_blog_id = $blog_id;
			$action->parent_post_id = $parent_post_id;
			$action->sync_data = $sync_data;
			$action->apply();
	
			echo $action->render();
		}
		
		
		// TODO : push this to PHASE 2...still doesn't work well..
		public function manage_posts_columns( $defaults)
		{
			$defaults[ '3wp_sync' ] = '<span title="'.$this->_( 'Shows which blogs have posts linked to this one' ).'">'.$this->_( 'Broadcasted' ).'</span>';
			return $defaults;
		}
	
		public function post_link( $link, $post )
		{
			// Don't overwrite the permalink if we're in the editing window.
			// This allows the user to change the permalink.
			if ( $_SERVER[ 'SCRIPT_NAME' ] == '/wp-admin/post.php' )
				return $link;
	
			if ( isset( $this->_is_getting_permalink ) )
				return $link;
	
			$this->_is_getting_permalink = true;
	
			$blog_id = get_current_blog_id();
	
			// Have we already checked this post ID for a link?
			$key = 'b' . $blog_id . '_p' . $post->ID;
			if ( property_exists( $this->permalink_cache, $key ) )
			{
				unset( $this->_is_getting_permalink );
				return $this->permalink_cache->$key;
			}
	
			$sync_data = $this->get_post_sync_data( $blog_id, $post->ID );
	
			$linked_parent = $sync_data->get_linked_parent();
	
			if ( $linked_parent === false)
			{
				$this->permalink_cache->$key = $link;
				unset( $this->_is_getting_permalink );
				return $link;
			}
	
			switch_to_blog( $linked_parent[ 'blog_id' ] );
			$post = get_post( $linked_parent[ 'post_id' ] );
			$permalink = get_permalink( $post );
			restore_current_blog();
	
			$this->permalink_cache->$key = $permalink;
	
			unset( $this->_is_getting_permalink );
			return $permalink;
		}
	
		public function post_row_actions( $actions, $post )
		{
			$this->sync_data_cache()->expect_from_wp_query();
	
			$sync_data = $this->sync_data_cache()->get_for( get_current_blog_id(), $post->ID );
	
			if ( $sync_data->get_linked_parent() === false )
			{
				$url = sprintf( 'admin.php?page=net_sync&amp;action=user_find_orphans&amp;post=%s', $post->ID );
				$url = wp_nonce_url( $url, 'sync_find_orphans_' . $post->ID );
				$actions[ 'sync_find_orphans' ] =
					sprintf( '<a href="%s" title="%s">%s</a>',
						$url ,
						$this->_( 'Find posts on other blogs that are identical to this post' ),
						$this->_( 'Find orphans' )
					);
			}
			return $actions;
		}
	
		public function save_post($post_id) {
			// We must be on the source blog.
			if (ms_is_switched()) return;
			if ($this->is_networkSync()) return;
			// No post?
			if (count($_POST) < 1) return;
			// Nothing of interest in the post?
			if (!isset($_POST['sync'])) return;
			
			$sync_data = $this->get_post_sync_data( get_current_blog_id(), $post_id );
			if ($sync_data->get_linked_parent() !== false) return;
	
			// No permission.
			if (!$this->role_at_least($this->get_site_option('role_sync'))) return;
			
			// Save the user's last settings.
			if (isset($_POST['sync' ]))
				$this->save_last_used_settings($this->user_id(), $_POST['sync']);
			$post = get_post($post_id);
			$meta_box_data = $this->create_meta_box($post);
	
			// Allow plugins to modify the meta box with their own info.
			$action = new actions\prepare_meta_box;
			$action->meta_box_data = $meta_box_data;
			$action->apply();
	
			// Post the form.
			if (!$meta_box_data->form->has_posted) {
				$meta_box_data->form->post();
				$meta_box_data->form->use_post_values();
			}
	
			$networkSync_data = new networkSync_data(array(
				'_POST' => $_POST,
				'meta_box_data' => $meta_box_data,
				'parent_blog_id' => get_current_blog_id(),
				'parent_post_id' => $post_id,
				'post' => $post,
				'upload_dir' => wp_upload_dir(),
			));
	
			$action = new actions\prepare_networkSync_data;
			$action->networkSync_data = $networkSync_data;
			$action->apply();
	
			if ( $networkSync_data->has_blogs() )
				$this->filters( 'net_sync_sync_post', $networkSync_data );
		}
	
		/**
			@brief		Begin adding admin hooks.
			@since		20131015
		**/
		public function net_sync_admin_menu()
		{
			if ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_link' ) ) )
			{
				//if (  $this->display_sync_columns )
				//{
					$this->add_action( 'post_row_actions', 10, 2 );
					$this->add_action( 'page_row_actions', 'post_row_actions', 10, 2 );
	
					$this->add_filter( 'manage_posts_columns' );
					$this->add_action( 'manage_posts_custom_column', 10, 2 );
	
					$this->add_filter( 'manage_pages_columns', 'manage_posts_columns' );
					$this->add_action( 'manage_pages_custom_column', 'manage_posts_custom_column', 10, 2 );
				//}
	
				// Hook into the actions that keep track of the sync data.
				$this->add_action( 'wp_trash_post', 'trash_post' );
				$this->add_action( 'trash_post' );
				$this->add_action( 'trash_page', 'trash_post' );
	
				$this->add_action( 'untrash_post' );
				$this->add_action( 'untrash_page', 'untrash_post' );
	
				$this->add_action( 'delete_post' );
				$this->add_action( 'delete_page', 'delete_post' );
			}
		}
	
		/**
			@brief		Prepare and display the meta box data.
		**/
		public function net_sync_prepare_meta_box( $action ) {
			$meta_box_data = $action->meta_box_data;	// Convenience.
			if ($action->is_applied()) return;
	
			if ( $meta_box_data->sync_data->get_linked_parent() !== false) {
				$meta_box_data->html->put( 'already_synced',  sprintf( '<p>%s</p>',
					$this->_( 'This post is synced child post. It cannot be synced further.' )
				) );
				$action->applied();
				return;
			}
	
			$form = $meta_box_data->form;		// Convenience
			$form->prefix( 'sync' );		// Create all inputs with this prefix.
	
			$published = $meta_box_data->post->post_status == 'publish';
	
			$has_linked_children = $meta_box_data->sync_data->has_linked_children();
	
			$meta_box_data->last_used_settings = $this->load_last_used_settings( $this->user_id() );
	
			$post_type = $meta_box_data->post->post_type;
			$post_type_object = get_post_type_object( $post_type );
			$post_type_supports_thumbnails = post_type_supports( $post_type, 'thumbnail' );
			$post_type_is_hierarchical = $post_type_object->hierarchical;
	
			// 20140327 Because so many plugins create broken post types, assume that all post types support custom fields.
			// $post_type_supports_custom_fields = post_type_supports( $post_type, 'custom-fields' );
			$post_type_supports_custom_fields = true;
	
			if ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_link' ) ) )
			{
				// Check the link box is the post has been published and has children OR it isn't published yet.
				$linked = (
					( $published && $meta_box_data->sync_data->has_linked_children() )
					||
					! $published
				);
				$link_input = $form->checkbox( 'link' )
					->checked( $linked )
					->label_( 'Link this post to its children' )
					->title( $this->_( 'Create a link to the children, which will be updated when this post is updated, trashed when this post is trashed, etc.' ) );
				$meta_box_data->html->put( 'link', '' );
			}
	
			if (
				( $post_type_supports_custom_fields || $post_type_supports_thumbnails )
				&&
				( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_custom_fields' ) ) )
			)
			{
				$custom_fields_input = $form->checkbox( 'custom_fields' )
					->checked( isset( $meta_box_data->last_used_settings[ 'custom_fields' ] ) )
					->label_( 'Custom fields' )
					->title( 'Broadcast all the custom fields and the featured image?' );
				$meta_box_data->html->put( 'custom_fields', '' );
			}
	
			if ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_taxonomies' ) ) )
			{
				$taxonomies_input = $form->checkbox( 'taxonomies' )
					->checked( isset( $meta_box_data->last_used_settings[ 'taxonomies' ] ) )
					->label_( 'Taxonomies' )
					->title( 'The taxonomies must have the same name (slug) on the selected blogs.' );
				$meta_box_data->html->put( 'taxonomies', '' );
			}
	
			$meta_box_data->html->put( 'sync_strings', '
				<script type="text/javascript">
					var sync_strings = {
						hide_all : "' . $this->_( 'hide all' ) . '",
						invert_selection : "' . $this->_( 'Invert selection' ) . '",
						select_deselect_all : "' . $this->_( 'Select / deselect all' ) . '",
						show_all : "' . $this->_( 'show all' ) . '"
					};
				</script>
			' );
	
			$filter = new filters\get_user_writable_blogs( $this->user_id() );
			$blogs = $filter->apply()->blogs;
	
			$blogs_input = $form->checkboxes( 'blogs' )
				->css_class( 'blogs checkboxes' )
				->label( 'Broadcast to' )
				->prefix( 'blogs' );
	
			// Preselect those children that this post has.
			$linked_children = $meta_box_data->sync_data->get_linked_children();
			foreach( $linked_children as $blog_id => $ignore )
			{
				$blog = $blogs->get( $blog_id );
				if ( ! $blog )
					continue;
				$blog->linked()->selected();
			}
	
			foreach( $blogs as $blog )
			{
				$blogs_input->option( $blog->blogname, $blog->id );
				$input_name = 'blogs_' . $blog->id;
				$option = $blogs_input->input( $input_name );
				$option->get_label()->content = $form::unfilter_text( $blog->blogname );
				$option->css_class( 'blog ' . $blog->id );
				if ( $blog->is_disabled() )
					$option->disabled()->css_class( 'disabled' );
				if ( $blog->is_linked() )
					$option->css_class( 'linked' );
				if ( $blog->is_required() )
					$option->css_class( 'required' )->title_( 'This blog is required' );
				if ( $blog->is_selected() )
					$option->checked( true );
				// The current blog should be "selectable", for the sake of other plugins that modify the meta box. But hidden from users.
				if ( $blog->id == $meta_box_data->blog_id )
					$option->hidden();
			}
	
			$meta_box_data->html->put( 'blogs', '' );
	
			$action->applied();
		}
	
		/**
			@brief		Fix up the inputs.
			@since		20131010
		**/
		public function net_sync_prepared_meta_box( $action )
		{
			$meta_box_data = $action->meta_box_data;
	
			// If our places in the html are still left, insert the inputs.
			foreach( array(
				'link',
				'custom_fields',
				'taxonomies',
				'groups',
				'blogs'
			) as $type )
				if ( $meta_box_data->html->has( $type ) )
				{
					$input = $meta_box_data->form->input( $type );
					$meta_box_data->html->put( $type, $input );
				}
		}
	
		/**
			@brief		Return a collection of blogs that the user is allowed to write to.
			@since		20131003
		**/
		public function net_sync_get_user_writable_blogs( $filter )
		{
			if ( $filter->is_applied() )
				return;
	
			$blogs = get_blogs_of_user( $filter->user_id, true );
			foreach( $blogs as $blog)
			{
				$blog = blog::make( $blog );
				$blog->id = $blog->userblog_id;
				if ( ! $this->is_blog_user_writable( $filter->user_id, $blog ) )
					continue;
				$filter->blogs->set( $blog->id, $blog );
			}
	
			$filter->blogs->sort_logically();
			$filter->applied();
			return $filter;
		}
	
		/**
			@brief		Convert the post_type site option to an array in the action.
			@since		2014-02-22 10:33:57
		**/
		public function net_sync_get_post_types( $action )
		{
			$post_types = $this->get_site_option( 'post_types' );
			$post_types = explode( ' ', $post_types );
			foreach( $post_types as $post_type )
				$action->post_types[ $post_type ] = $post_type;
		}
	
		/**
			@brief		Handle the display of the custom column.
			@since		2014-04-18 08:30:19
		**/
		public function net_sync_manage_posts_custom_column( $filter )
		{
			if ( $filter->sync_data->get_linked_parent() !== false)
			{
				$parent = $filter->sync_data->get_linked_parent();
				$parent_blog_id = $parent[ 'blog_id' ];
				switch_to_blog( $parent_blog_id );
	
				$html = $this->_(sprintf( 'Linked from %s', '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' .$parent[ 'post_id' ] . '&action=edit">' . get_bloginfo( 'name' ) . '</a>' ) );
				$filter->html->put( 'linked_from', $html );
				restore_current_blog();
			}
			elseif ( $filter->sync_data->has_linked_children() )
			{
				$children = $filter->sync_data->get_linked_children();
	
				if ( count( $children ) > 0 )
				{
					// Only display if there is more than one child post
					if ( count( $children ) > 1 )
					{
						$strings = new \net_sync\collections\strings_with_metadata;
	
						$strings->set( 'div_open', '<div class="row-actions synced_blog_actions">' );
						$strings->set( 'text_all', $this->_( 'All' ) );
						$strings->set( 'div_small_open', '<small>' );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_restore_all&amp;post=%s", $filter->parent_post_id );
						$url = wp_nonce_url( $url, 'sync_restore_all_' . $filter->parent_post_id );
						$strings->set( 'restore_all_separator', ' | ' );
						$strings->set( 'restore_all', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Restore all of the children from the trash' ),
							$this->_( 'Restore' )
						) );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_trash_all&amp;post=%s", $filter->parent_post_id );
						$url = wp_nonce_url( $url, 'sync_trash_all_' . $filter->parent_post_id );
						$strings->set( 'trash_all_separator', ' | ' );
						$strings->set( 'trash_all', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Put all of the children in the trash' ),
							$this->_( 'Trash' )
						) );
	
						$url_unlink_all = sprintf( "admin.php?page=net_sync&amp;action=user_unlink_all&amp;post=%s", $filter->parent_post_id );
						$url_unlink_all = wp_nonce_url( $url_unlink_all, 'sync_unlink_all_' . $filter->parent_post_id );
						$strings->set( 'unlink_all_separator', ' | ' );
						$strings->set( 'unlink_all', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Unlink all of the child posts' ),
							$this->_( 'Unlink' )
						) );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_delete_all&amp;post=%s", $filter->parent_post_id );
						$url = wp_nonce_url( $url, 'sync_delete_all_' . $filter->parent_post_id );
						$strings->set( 'delete_all_separator', ' | ' );
						$strings->set( 'delete_all', sprintf( '<span class="trash"><a href="%s" title="%s">%s</a></span>',
							$url,
							$this->_( 'Permanently delete all the synced children' ),
							$this->_( 'Delete' )
						) );
	
						$strings->set( 'div_small_close', '</small>' );
						$strings->set( 'div_close', '</div>' );
	
						$filter->html->put( 'delete_all', $strings );
					}
	
					$collection = new \net_sync\collections\strings;
	
					foreach( $children as $child_blog_id => $child_post_id )
					{
						$strings = new \net_sync\collections\strings_with_metadata;
	
						$url_child = get_blog_permalink( $child_blog_id, $child_post_id );
						// The post id is for the current blog, not the target blog.
	
						// For get_bloginfo.
						switch_to_blog( $child_blog_id );
						$blogname = get_bloginfo( 'blogname' );
						restore_current_blog();
	
						$strings->metadata()->set( 'child_blog_id', $child_blog_id );
						$strings->metadata()->set( 'blogname', $blogname );
	
						$strings->set( 'div_open', sprintf( '<div class="child_blog_name blog_%s">', $child_blog_id ) );
						$strings->set( 'a_synced_child', sprintf( '<a class="synced_child" href="%s">%s </a>', $url_child, $blogname ) );
						$strings->set( 'span_row_actions_open', '<span class="row-actions synced_blog_actions">' );
						$strings->set( 'small_open', '<small>' );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_restore&amp;post=%s&amp;child=%s", $filter->parent_post_id, $child_blog_id );
						$url = wp_nonce_url( $url, 'sync_restore_' . $child_blog_id . '_' . $filter->parent_post_id );
						$strings->set( 'restore_separator', ' | ' );
						$strings->set( 'restore', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Restore all of the children from the trash' ),
							$this->_( 'Restore' )
						) );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_trash&amp;post=%s&amp;child=%s", $filter->parent_post_id, $child_blog_id );
						$url = wp_nonce_url( $url, 'sync_trash_' . $child_blog_id . '_' . $filter->parent_post_id );
						$strings->set( 'trash_separator', ' | ' );
						$strings->set( 'trash', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Put this synced child post in the trash' ),
							$this->_( 'Trash' )
						) );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_unlink&amp;post=%s&amp;child=%s", $filter->parent_post_id, $child_blog_id );
						$url = wp_nonce_url( $url, 'sync_unlink_' . $child_blog_id . '_' . $filter->parent_post_id );
						$strings->set( 'unlink_separator', ' | ' );
						$strings->set( 'unlink', sprintf( '<a href="%s" title="%s">%s</a>',
							$url,
							$this->_( 'Remove link to this synced child post' ),
							$this->_( 'Unlink' )
						) );
	
						$url = sprintf( "admin.php?page=net_sync&amp;action=user_delete&amp;post=%s&amp;child=%s", $filter->parent_post_id, $child_blog_id );
						$url = wp_nonce_url( $url, 'sync_delete_' . $child_blog_id . '_' . $filter->parent_post_id );
						$strings->set( 'delete_separator', ' | ' );
						$strings->set( 'delete', sprintf( '<span class="trash"><a href="%s" title="%s">%s</a></span>',
							$url,
							$this->_( 'Unlink and delete this synced child post' ),
							$this->_( 'Delete' )
						) );
	
						$strings->set( 'small_close', '</small>' );
						$strings->set( 'span_row_actions_close', '</span>' );
						$strings->set( 'div_close', '</div>' );
	
						$collection->set( $blogname, $strings );
					}
	
					$collection->sort_by( function( $child )
					{
						return $child->metadata()->get( 'blogname' );
					});
	
					$filter->html->put( 'synced_to', $collection );
				}
			}
			$filter->applied();
		}
	
		/**
			@brief		Decide what to do with the POST.
		**/
		public function net_sync_maybe_clear_post($action) {
			if ($action->is_applied()) {
				return;
			}
	
			$clear_post = $this->get_site_option( 'clear_post', true );
			if ( $clear_post ) {
				$action->post = array();
			}
		}
	
		/**
			@brief		Fill the networkSync_data object with information.
	
			@details
	
			The difference between the calculations in this filter and the actual sync_post method is that this filter
	
			1) does access checks
			2) tells sync_post() WHAT to sync, not how.
	
			@since		20131004
		**/
		public function net_sync_prepare_networkSync_data( $action ) {
			$bcd = $action->networkSync_data;
			$allowed_post_status = array('pending', 'private', 'publish');
	
			if ( $bcd->post->post_status == 'draft' && $this->role_at_least( $this->get_site_option( 'role_sync_as_draft' ) ) )
				$allowed_post_status[] = 'draft';
	
			if ( $bcd->post->post_status == 'future' && $this->role_at_least( $this->get_site_option( 'role_sync_scheduled_posts' ) ) )
				$allowed_post_status[] = 'future';
	
			if ( ! in_array( $bcd->post->post_status, $allowed_post_status ) )
				return;
	
			$form = $bcd->meta_box_data->form;
			if ( $form->is_posting() && ! $form->has_posted )
					$form->post();
	
			// Collect the list of blogs from the meta box.
			$blogs_input = $form->input( 'blogs' );
			foreach( $blogs_input->inputs() as $blog_input )
				if ( $blog_input->is_checked() )
				{
					$blog_id = $blog_input->get_name();
					$blog_id = str_replace( 'blogs_', '', $blog_id );
					$blog = new sync_data\blog;
					$blog->id = $blog_id;
					$bcd->sync_to( $blog );
				}
	
			// Remove the current blog
			$bcd->blogs->forget( $bcd->parent_blog_id );
	
			$bcd->post_type_object = get_post_type_object( $bcd->post->post_type );
			$bcd->post_type_supports_thumbnails = post_type_supports( $bcd->post->post_type, 'thumbnail' );
			//$bcd->post_type_supports_custom_fields = post_type_supports( $bcd->post->post_type, 'custom-fields' );
			$bcd->post_type_supports_custom_fields = true;
			$bcd->post_type_is_hierarchical = $bcd->post_type_object->hierarchical;
	
			$bcd->custom_fields = $form->checkbox( 'custom_fields' )->get_post_value()
				&& ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_custom_fields' ) ) );
	
			$bcd->link = $form->checkbox( 'link' )->get_post_value()
				&& ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_link' ) ) );
	
			$bcd->taxonomies = $form->checkbox( 'taxonomies' )->get_post_value()
				&& ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_taxonomies' ) ) );
	
			// Is this post sticky? This info is hidden in a blog option.
			$stickies = get_option( 'sticky_posts' );
			$bcd->post_is_sticky = in_array( $bcd->post->ID, $stickies );
		}
	
		public function trash_post( $post_id) {
			$this->trash_untrash_delete_post( 'wp_trash_post', $post_id );
		}
	
		/**
		 * Issues a specific command on all the blogs that this post_id has linked children on.
		 * @param string $command Command to run.
		 * @param int $post_id Post with linked children
		 */
		private function trash_untrash_delete_post( $command, $post_id) {
			global $blog_id;
			$sync_data = $this->get_post_sync_data( $blog_id, $post_id );
	
			if ( $sync_data->has_linked_children() )
			{
				foreach( $sync_data->get_linked_children() as $childBlog=>$childPost)
				{
					if ( $command == 'wp_delete_post' )
					{
						// Delete the sync data of this child
						$this->delete_post_sync_data( $childBlog, $childPost );
					}
					switch_to_blog( $childBlog);
					$command( $childPost);
					restore_current_blog();
				}
			}
	
			if ( $command == 'wp_delete_post' )
			{
				global $blog_id;
				// Find out if this post has a parent.
				$linked_parent_sync_data = $this->get_post_sync_data( $blog_id, $post_id );
				$linked_parent_sync_data = $linked_parent_sync_data->get_linked_parent();
				if ( $linked_parent_sync_data !== false)
				{
					// Remove ourselves as a child.
					$parent_sync_data = $this->get_post_sync_data( $linked_parent_sync_data[ 'blog_id' ], $linked_parent_sync_data[ 'post_id' ] );
					$parent_sync_data->remove_linked_child( $blog_id );
					$this->set_post_sync_data( $linked_parent_sync_data[ 'blog_id' ], $linked_parent_sync_data[ 'post_id' ], $parent_sync_data );
				}
	
				$this->delete_post_sync_data( $blog_id, $post_id );
			}
		}

		/**
			@brief		Broadcasts a post.
			@param		networkSync_data		$networkSync_data		Object containing networkSync instructions.
			@since		20130927
		**/
		public function net_sync_sync_post( $networkSync_data )
		{
			if ( ! is_a( $networkSync_data, get_class( new networkSync_data ) ) )
				return $networkSync_data;
			return $this->sync_post( $networkSync_data );
		}
	
		/**
			@brief		Allows Broadcast plugins to update the term with their own info.
			@since		2014-04-08 15:12:05
		**/
		public function net_sync_wp_insert_term( $action )
		{
			if ( ! isset( $action->term->parent ) )
				$action->term->parent = 0;
	
			$term = wp_insert_term(
				$action->term->name,
				$action->taxonomy,
				array(
					'description' => $action->term->description,
					'parent' => $action->term->parent,
					'slug' => $action->term->slug,
				)
			);
	
			// Sometimes the search didn't find the term because it's SIMILAR and not exact.
			// WP will complain and give us the term tax id.
			if ( is_wp_error( $term ) ) {
				$wp_error = $term;
				if (isset($wp_error->error_data['term_exists'])) {
					$term_id = $wp_error->error_data[ 'term_exists' ];
					$term = get_term_by( 'id', $term_id, $action->taxonomy, ARRAY_A );
				} else {
					throw new Exception( 'Unable to create a new term.' );
				}
			}
	
			$term_taxonomy_id = $term[ 'term_taxonomy_id' ];
	
			$action->new_term = get_term_by( 'term_taxonomy_id', $term_taxonomy_id, $action->taxonomy, ARRAY_A );
		}
	
		/**
			@brief		[Maybe] update a term.
			@since		2014-04-10 14:26:23
		**/
		public function net_sync_wp_update_term( $action )
		{
			$update = true;
	
			// If we are given an old term, then we have a chance of checking to see if there should be an update called at all.
			if ( $action->has_old_term() )
			{
				// Assume they match.
				$update = false;
				foreach(array('name', 'description', 'parent') as $key )
					if ( $action->old_term->$key != $action->new_term->$key )
						$update = true;
			}
	
			if ( $update ) {
				wp_update_term( $action->new_term->term_id, $action->taxonomy, array(
					'description' => $action->new_term->description,
					'name' => $action->new_term->name,
					'parent' => $action->new_term->parent,
				) );
				$action->updated = true;
			}
		}
	
		public function untrash_post( $post_id)
		{
			$this->trash_untrash_delete_post( 'wp_untrash_post', $post_id );
		}
	
		/**
			@brief		Use the correct canonical link.
		**/
		public function wp_head()
		{
			// Only override the canonical if we're looking at a single post.
			if ( ! is_single() )
				return;
	
			global $post;
			global $blog_id;
	
			// Find the parent, if any.
			$sync_data = $this->get_post_sync_data( $blog_id, $post->ID );
			$linked_parent = $sync_data->get_linked_parent();
			if ( $linked_parent === false)
				return;
	
			// Post has a parent. Get the parent's permalink.
			switch_to_blog( $linked_parent[ 'blog_id' ] );
			$url = get_permalink( $linked_parent[ 'post_id' ] );
			restore_current_blog();
	
			echo sprintf( '<link rel="canonical" href="%s" />', $url );
			echo "\n";
	
			// Prevent Wordpress from outputting its own canonical.
			remove_action( 'wp_head', 'rel_canonical' );
	
			// Remove Canonical Link Added By Yoast WordPress SEO Plugin
			$this->add_filter( 'wpseo_canonical', 'wp_head_remove_wordpress_seo_canonical' );;
		}
	
		/**
			@brief		Remove Wordpress SEO canonical link so that it doesn't conflict with the parent link.
			@since		2014-01-16 00:36:15
		**/
	
		public function wp_head_remove_wordpress_seo_canonical()
		{
			// Tip seen here: http://wordpress.org/support/topic/plugin-wordpress-seo-by-yoast-remove-canonical-tags-in-header?replies=10
			return false;
		}
	
		// --------------------------------------------------------------------------------------------
		// ----------------------------------------- Misc functions
		// --------------------------------------------------------------------------------------------
	
		/**
			@brief		Returns the current sync_data cache object.
			@return		sync_data\\cache		A newly-created or old cache object.
			@since		201301009
		**/
		public function sync_data_cache()
		{
			$property = 'sync_data_cache';
			if ( ! property_exists( $this, 'sync_data_cache' ) )
				$this->$property = new \net_sync\sync_data\cache;
			return $this->$property;
		}
	
		/**
			@brief		Returns the name of the sync data table.
			@since		20131104
		**/
		public function sync_data_table()
		{
			return $this->wpdb->base_prefix . '_3wp_sync_syncdata';
		}
	
		/**
			@brief		Broadcast a post.
			@details	The BC data parameter contains all necessary information about what is being synced, to which blogs, options, etc.
			@param		networkSync_data		$networkSync_data		The networkSync data object.
			@since		20130603
		**/
		public function sync_post( $networkSync_data )
		{
			$bcd = $networkSync_data;
	
			// For nested syncs. Just in case.
			switch_to_blog( $bcd->parent_blog_id );
	
			if ( $bcd->link ) {
				// Prepare the sync data for linked children.
				$sync_data = $this->get_post_sync_data( $bcd->parent_blog_id, $bcd->post->ID );
	
				// Does this post type have parent support, so that we can link to a parent?
				if ( $bcd->post_type_is_hierarchical && $bcd->post->post_parent > 0) {
					$parent_sync_data = $this->get_post_sync_data( $bcd->parent_blog_id, $bcd->post->post_parent );
				}
			}
			if ( $bcd->taxonomies ) {
				$this->collect_post_type_taxonomies( $bcd );
			}
			
			$bcd->attachment_data = array();
			$attached_files = get_children( 'post_parent='.$bcd->post->ID.'&post_type=attachment' );
			$has_attached_files = count( $attached_files) > 0;
			if ($has_attached_files) {
				foreach($attached_files as $attached_file) {
					$bcd->attachment_data[ $attached_file->ID ] = attachment_data::from_attachment_id( $attached_file, $bcd->upload_dir );
				}
			}
	
			if ($bcd->custom_fields) {
				$bcd->post_custom_fields = get_post_custom( $bcd->post->ID );
	
				$bcd->has_thumbnail = isset( $bcd->post_custom_fields[ '_thumbnail_id' ] );
	
				// Check that the thumbnail ID is > 0
				$bcd->has_thumbnail = $bcd->has_thumbnail && ( reset( $bcd->post_custom_fields[ '_thumbnail_id' ] ) > 0 );
	
				if ($bcd->has_thumbnail) {
					$bcd->thumbnail_id = $bcd->post_custom_fields[ '_thumbnail_id' ][0];
					$bcd->thumbnail = get_post( $bcd->thumbnail_id );
					unset( $bcd->post_custom_fields[ '_thumbnail_id' ] ); // There is a new thumbnail id for each blog.
					$bcd->attachment_data[ 'thumbnail' ] = attachment_data::from_attachment_id( $bcd->thumbnail, $bcd->upload_dir);
					// Now that we know what the attachment id the thumbnail has, we must remove it from the attached files to avoid duplicates.
					unset( $bcd->attachment_data[ $bcd->thumbnail_id ] );
				}
	
				// Remove all the _internal custom fields.
				$bcd->post_custom_fields = $this->keep_valid_custom_fields( $bcd->post_custom_fields );
			}
	
			// Handle any galleries.
			$bcd->galleries = new collection;
			$matches = $this->find_shortcodes( $bcd->post->post_content, 'gallery' );
	
			// [2] contains only the shortcode command / key. No options.
			foreach( $matches[ 2 ] as $index => $key )
			{
				// We've found a gallery!
				$bcd->has_galleries = true;
				$gallery = new \stdClass;
				$bcd->galleries->push( $gallery );
	
				// Complete matches are in 0.
				$gallery->old_shortcode = $matches[ 0 ][ $index ];
	
				// Extract the IDs
				$gallery->ids_string = preg_replace( '/.*ids=\"([0-9,]*)".*/', '\1', $gallery->old_shortcode );
				$gallery->ids_array = explode( ',', $gallery->ids_string );
				foreach( $gallery->ids_array as $id ) {
					$ad = attachment_data::from_attachment_id( $id, $bcd->upload_dir );
					$bcd->attachment_data[ $id ] = $ad;
				}
			}
	
			// To prevent recursion
			array_push( $this->networkSync, $bcd );
	
			// POST is no longer needed. Empty it so that other plugins don't use it.
			$action = new actions\maybe_clear_post;
			$action->post = $_POST;
			$action->apply();
			$_POST = $action->post;
	
			$action = new actions\networkSync_started;
			$action->networkSync_data = $bcd;
			$action->apply();
			foreach($bcd->blogs as $child_blog) {
				$child_blog->switch_to();
				$bcd->current_child_blog_id = $child_blog->get_id();
				
				// Create new post data from the original stuff.
				$bcd->new_post = (array) $bcd->post;
	
				foreach(array('comment_count', 'guid', 'ID', 'post_parent') as $key )
					unset( $bcd->new_post[ $key ] );
	
				$action = new actions\networkSync_after_switch_to_blog;
				$action->networkSync_data = $bcd;
				$action->apply();
	
				// Post parent
				if ( $bcd->link && isset( $parent_sync_data) )
					if ( $parent_sync_data->has_linked_child_on_this_blog() )
					{
						$linked_parent = $parent_sync_data->get_linked_child_on_this_blog();
						$bcd->new_post[ 'post_parent' ] = $linked_parent;
					}
				
				// Insert new? Or update? Depends on whether the parent post was linked before or is newly linked?
				$need_to_insert_post = true;
				if ( $bcd->link )
					if ( $sync_data->has_linked_child_on_this_blog() )
					{
						$child_post_id = $sync_data->get_linked_child_on_this_blog();
	
						// Does this child post still exist?
						$child_post = get_post( $child_post_id );
						if ( $child_post !== null )
						{
							$temp_post_data = $bcd->new_post;
							$temp_post_data[ 'ID' ] = $child_post_id;
							$bcd->new_post[ 'ID' ] = wp_update_post( $temp_post_data );
							$need_to_insert_post = false;
						}
					}
	
				if ($need_to_insert_post) {
					$temp_post_data = $bcd->new_post;
					//unset( $temp_post_data[ 'ID' ] );
					
					switch_to_blog($child_blog->get_id());
					if(get_post_status($bcd->_POST['post_ID'])) {
						$extPost = true;
					}
					restore_current_blog();
					
					if($extPost) {
						$temp_post_data['ID'] = $bcd->_POST['post_ID'];
					} else {
						$temp_post_data['import_id'] = $bcd->_POST['post_ID'];
					}
					
					$result = wp_insert_post( $temp_post_data );
					// Did we manage to insert the post properly?
					if (intval( $result ) < 1) {
						continue;
					}
					// Yes we did.
					$bcd->new_post[ 'ID' ] = $result;
	
					if ($bcd->link) {
						$sync_data->add_linked_child( $bcd->current_child_blog_id, $bcd->new_post[ 'ID' ] );
					}
				}
	
				if ($bcd->taxonomies) {
					foreach($bcd->parent_post_taxonomies as $parent_post_taxonomy => $parent_post_terms) {
						// If we're updating a linked post, remove all the taxonomies and start from the top.
						if ( $bcd->link )
							if ( $sync_data->has_linked_child_on_this_blog() )
								wp_set_object_terms( $bcd->new_post[ 'ID' ], array(), $parent_post_taxonomy );
	
						// Skip this iteration if there are no terms
						if ( ! is_array( $parent_post_terms ) ) {
							continue;
						}
	
						// Get a list of terms that the target blog has.
						$target_blog_terms = $this->get_current_blog_taxonomy_terms( $parent_post_taxonomy );
	
						// Go through the original post's terms and compare each slug with the slug of the target terms.
						$taxonomies_to_add_to = array();
						foreach( $parent_post_terms as $parent_post_term )
						{
							$found = false;
							$parent_slug = $parent_post_term->slug;
							foreach( $target_blog_terms as $target_blog_term ) {
								if ( $target_blog_term[ 'slug' ] == $parent_slug ) {
									$found = true;
									$taxonomies_to_add_to[] = intval( $target_blog_term[ 'term_id' ] );
									break;
								}
							}
	
							// Should we create the taxonomy if it doesn't exist?
							if ( ! $found )
							{
								// Does the term have a parent?
								$target_parent_id = 0;
								if ( $parent_post_term->parent != 0 )
								{
									// Recursively insert ancestors if needed, and get the target term's parent's ID
									$target_parent_id = $this->insert_term_ancestors(
										(array) $parent_post_term,
										$parent_post_taxonomy,
										$target_blog_terms,
										$bcd->parent_blog_taxonomies[ $parent_post_taxonomy ][ 'terms' ]
									);
								}
	
								$new_term = clone( $parent_post_term );
								$new_term->parent = $target_parent_id;
								$action = new actions\wp_insert_term;
								$action->taxonomy = $parent_post_taxonomy;
								$action->term = $new_term;
								$action->apply();
								$new_taxonomy = $action->new_term;
								$term_taxonomy_id = $new_taxonomy[ 'term_taxonomy_id' ];
								$taxonomies_to_add_to = array();
								$taxonomies_to_add_to = intval( $term_taxonomy_id );
							}
						}
						
						$this->sync_terms( $bcd, $parent_post_taxonomy );
	
						if ( count( $taxonomies_to_add_to ) > 0 )
						{
							// This relates to the bug mentioned in the method $this->set_term_parent()
							delete_option( $parent_post_taxonomy . '_children' );
							clean_term_cache( '', $parent_post_taxonomy );
							wp_set_object_terms( $bcd->new_post[ 'ID' ], $taxonomies_to_add_to, $parent_post_taxonomy );
						}
					}
				}
	
				// Remove the current attachments.
				$attachments_to_remove = get_children( 'post_parent='.$bcd->new_post[ 'ID' ] . '&post_type=attachment' );
				foreach ( $attachments_to_remove as $attachment_to_remove ) {
					wp_delete_attachment( $attachment_to_remove->ID );
				}
	
				// Copy the attachments
				$bcd->copied_attachments = array();
				foreach( $bcd->attachment_data as $key => $attachment ) {
					if ( $key != 'thumbnail' ) {
						$o = clone( $bcd );
						$o->attachment_data = $attachment;
						if ( $o->attachment_data->post->post_parent > 0 )
							$o->attachment_data->post->post_parent = $bcd->new_post[ 'ID' ];
						$this->maybe_copy_attachment( $o );
						$a = new \stdClass();
						$a->old = $attachment;
						$a->new = get_post( $o->attachment_id );
						$a->new->id = $a->new->ID;		// Lowercase is expected.
						$bcd->copied_attachments[] = $a;
					}
				}
	
				// Maybe modify the post content with new URLs to attachments and what not.
				$unmodified_post = (object)$bcd->new_post;
				$modified_post = clone( $unmodified_post );
	
				//print_r($unmodified_post);
				// If there were any image attachments copied...
				if ( count( $bcd->copied_attachments ) > 0 ) {
					// Update the URLs in the post to point to the new images.
					$new_upload_dir = wp_upload_dir();
					foreach( $bcd->copied_attachments as $a ) {
						// Replace the GUID with the new one.
						//$modified_post->post_content = str_replace( $a->old->guid, $a->new->guid, $modified_post->post_content );
						// And replace the IDs present in any image captions.
						$modified_post->post_content = str_replace( 'id="attachment_' . $a->old->id . '"', 'id="attachment_' . $a->new->id . '"', $modified_post->post_content );
						$modified_post->post_content = str_replace($a->old->id, $a->new->id, $modified_post->post_content);
					}
				}
					
				// If there are galleries...
				foreach($bcd->galleries as $gallery) {
					// Work on a copy.
					$gallery = clone( $gallery );
					$new_ids = array();
	
					// Go through all the attachment IDs
					foreach($gallery->ids_array as $id) {
						// Find the new ID.
						foreach($bcd->copied_attachments as $ca) {
							if ( $ca->old->id != $id )
								continue;
							$new_ids[] = $ca->new->id;
						}
					}
					$new_ids_string = implode( ',', $new_ids );
					$new_shortcode = $gallery->old_shortcode;
					$new_shortcode = str_replace( $gallery->ids_string, $new_ids_string, $gallery->old_shortcode );
					$modified_post->post_content = str_replace( $gallery->old_shortcode, $new_shortcode, $modified_post->post_content );
				}
	
				$bcd->modified_post = $modified_post;
				$action = new actions\networkSync_modify_post;
				$action->networkSync_data = $bcd;
				$action->apply();
	
				// Maybe updating the post is not necessary.
				if ($unmodified_post->post_content != $modified_post->post_content) {
					wp_update_post( $modified_post );	// Or maybe it is.
				}
	
				if ($bcd->custom_fields) {
					// Remove all old custom fields.
					$old_custom_fields = get_post_custom( $bcd->new_post[ 'ID' ] );
	
					foreach($old_custom_fields as $key => $value) {
						// This post has a featured image! Remove it from disk!
						if ( $key == '_thumbnail_id' ) {
							$thumbnail_post = $value[0];
							wp_delete_post( $thumbnail_post );
						}

						
						delete_post_meta( $bcd->new_post[ 'ID' ], $key );
					}
	
					foreach( $bcd->post_custom_fields as $meta_key => $meta_value )
					{
						if ( is_array( $meta_value ) )
						{
							foreach( $meta_value as $single_meta_value )
							{
								$single_meta_value = maybe_unserialize( $single_meta_value );
								add_post_meta( $bcd->new_post[ 'ID' ], $meta_key, $single_meta_value );
							}
						}
						else
						{
							$meta_value = maybe_unserialize( $meta_value );
							add_post_meta( $bcd->new_post[ 'ID' ], $meta_key, $meta_value );
						}
					}
	
					// Attached files are custom fields... but special custom fields.
					if ( $bcd->has_thumbnail )
					{
						$o = clone( $bcd );
						$o->attachment_data = $bcd->attachment_data[ 'thumbnail' ];
	
						// Clear the attachment cache for this blog because the featured image could have been copied by the file copy.
						if ( property_exists( $this, 'attachment_cache' ) ) {
							$this->attachment_cache->forget( $bcd->current_child_blog_id );
						}
	
						if ( $o->attachment_data->post->post_parent > 0 )
							$o->attachment_data->post->post_parent = $bcd->new_post[ 'ID' ];
						
						$this->maybe_copy_attachment( $o );
						if ( $o->attachment_id !== false ) {
							update_post_meta( $bcd->new_post[ 'ID' ], '_thumbnail_id', $o->attachment_id );
						}
					}
				}
	
				// Sticky behaviour
				$child_post_is_sticky = is_sticky( $bcd->new_post[ 'ID' ] );
				if ( $bcd->post_is_sticky && ! $child_post_is_sticky )
					stick_post( $bcd->new_post[ 'ID' ] );
				if ( ! $bcd->post_is_sticky && $child_post_is_sticky )
					unstick_post( $bcd->new_post[ 'ID' ] );
	
				if ( $bcd->link ) {
					$new_post_sync_data = $this->get_post_sync_data( $bcd->current_child_blog_id, $bcd->new_post[ 'ID' ] );
					$new_post_sync_data->set_linked_parent( $bcd->parent_blog_id, $bcd->post->ID );
					$this->set_post_sync_data( $bcd->current_child_blog_id, $bcd->new_post[ 'ID' ], $new_post_sync_data );
				}
	
				$action = new actions\networkSync_before_restore_current_blog;
				$action->networkSync_data = $bcd;
				$action->apply();
	
				$child_blog->switch_from();
			}
	
			// For nested syncs. Just in case.
			restore_current_blog();
	
			// Save the post sync data.
			if ( $bcd->link ) {
				$this->set_post_sync_data( $bcd->parent_blog_id, $bcd->post->ID, $sync_data );
			}
	
			$action = new actions\networkSync_finished;
			$action->networkSync_data = $bcd;
			$action->apply();
	
			// Finished networkSync.
			array_pop( $this->networkSync );
			
			return $bcd;
		}
	
		/**
			@brief		Collects the post type's taxonomies into the networkSync data object.
			@details	Requires only that $bcd->post->post_type be filled in.
		**/
		public function collect_post_type_taxonomies( $bcd )
		{
			$bcd->parent_blog_taxonomies = get_object_taxonomies(array('object_type' => $bcd->post->post_type), 'array' );
			$bcd->parent_post_taxonomies = array();
			foreach( $bcd->parent_blog_taxonomies as $parent_blog_taxonomy => $taxonomy )
			{
				// Parent blog taxonomy terms are used for creating missing target term ancestors
				$bcd->parent_blog_taxonomies[ $parent_blog_taxonomy ] = array(
					'taxonomy' => $taxonomy,
					'terms'    => $this->get_current_blog_taxonomy_terms( $parent_blog_taxonomy ),
				);
				if ( isset( $bcd->post->ID ) )
					$bcd->parent_post_taxonomies[ $parent_blog_taxonomy ] = get_the_terms( $bcd->post->ID, $parent_blog_taxonomy );
				else
					$bcd->parent_post_taxonomies[ $parent_blog_taxonomy ] = get_terms(array($parent_blog_taxonomy));
			}
		}
	
		/**
			@brief		Creates a new attachment.
			@details
	
			The $o object is an extension of Broadcasting_Data and must contain:
			- @i attachment_data An attachment_data object containing the attachmend info.
	
			@param		object		$o		Options.
			@return		@i int The attachment's new post ID.
			@since		20130530
			@version	20131003
		*/
		public function copy_attachment( $o )
		{
			if ( ! file_exists( $o->attachment_data->filename_path ) ) {
				return false;
			}
			// Copy the file to the blog's upload directory
			$upload_dir = wp_upload_dir();
	
			$source = $o->attachment_data->filename_path;
			$target = $upload_dir[ 'path' ] . '/' . $o->attachment_data->filename_base;
			copy( $source, $target );
			// And now create the attachment stuff.
			// This is taken almost directly from http://codex.wordpress.org/Function_Reference/wp_insert_attachment
			$wp_filetype = wp_check_filetype( $target, null );
			$attachment = array(
				//'ID' => $o->attachment_id,
				'guid' => $upload_dir[ 'url' ] . '/' . $target,
				'menu_order' => $o->attachment_data->post->menu_order,
				'post_author' => $o->attachment_data->post->post_author,
				'post_excerpt' => $o->attachment_data->post->post_excerpt,
				'post_mime_type' => $wp_filetype[ 'type' ],
				'post_title' => $o->attachment_data->post->post_title,
				'post_content' => 'test',
				'post_status' => 'inherit',
			);
			//wp_insert_attachment( $attachment, $target, $o->attachment_data->post->post_parent );
			//$o->attachment_id = $o->attachment_data->post->ID;
			$o->attachment_id = wp_insert_attachment( $attachment, $target, $o->attachment_data->post->post_parent );
	
			// Now to maybe handle the metadata.
			if ( $o->attachment_data->file_metadata ) {
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $o->attachment_id, $target );
				
				foreach( $o->attachment_data->post_custom as $key => $value )
				{
					$value = reset( $value );
					$value = maybe_unserialize( $value );
					switch( $key )
					{
						// Some values need to handle completely different upload paths (from different months, for example).
						case '_wp_attached_file':
							$value = $attach_data[ 'file' ];
							break;
					}
					update_post_meta( $o->attachment_id, $key, $value );
				}
				wp_update_attachment_metadata( $o->attachment_id,  $attach_data );
			}
		}
	
		/**
			@brief		Creates the ID column in the sync data table.
			@since		2014-04-20 20:19:45
		**/
		public function create_sync_data_id_column()
		{
			$query = sprintf( "ALTER TABLE `%s` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'ID of row' FIRST;",
				$this->sync_data_table()
			);
			$this->query( $query );
		}
	
		/**
			@brief		Create a meta box for this post.
			@since		20131015
		**/
		public function create_meta_box( $post )
		{
			$meta_box_data = new meta_box\data;
			$meta_box_data->blog_id = get_current_blog_id();
			$meta_box_data->sync_data = $this->get_post_sync_data( $meta_box_data->blog_id, $post->ID );
			$meta_box_data->form = $this->form2();
			$meta_box_data->post = $post;
			$meta_box_data->post_id = $post->ID;
			return $meta_box_data;
		}
	
		/**
			Deletes the sync data completely of a post in a blog.
		*/
		public function delete_post_sync_data( $blog_id, $post_id)
		{
			$this->sync_data_cache()->set_for( $blog_id, $post_id, new BroadcastData );
			$this->sql_delete_sync_data( $blog_id, $post_id );
		}
		
		/**
			@brief		Find shortcodes in a string.
			@details	Runs a preg_match_all on a string looking for specific shortcodes.
						Overrides Wordpress' get_shortcode_regex without own shortcode(s).
			@since		2014-02-26 22:05:09
		**/
		public function find_shortcodes( $string, $shortcodes )
		{
			// Make the shortcodes an array
			if ( ! is_array( $shortcodes ) )
				$shortcodes = array($shortcodes);
	
			// We use Wordpress' own function to find shortcodes.
	
			global $shortcode_tags;
			// Save the old global
			$old_shortcode_tags = $shortcode_tags;
			// Replace the shortcode tags with just our own.
			$shortcode_tags = array_flip( $shortcodes );
			$rx = get_shortcode_regex();
			$shortcode_tags = $old_shortcode_tags;
	
			// Run the preg_match_all
			$matches = '';
			preg_match_all( '/' . $rx . '/', $string, $matches );
	
			return $matches;
		}
	
		public function get_current_blog_taxonomy_terms( $taxonomy )
		{
			$terms = get_terms( $taxonomy, array(
				'hide_empty' => false,
			) );
			$terms = (array) $terms;
			$terms = $this->array_rekey( $terms, 'term_id' );
			return $terms;
		}
	
		/**
			@brief		Return an array of all callbacks of a hook.
			@since		2014-04-30 00:11:30
		**/
		public function get_hooks( $hook )
		{
			global $wp_filter;
			$filters = $wp_filter[ $hook ];
			ksort( $filters );
			$hook_callbacks = [];
			//$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
			foreach( $filters as $priority => $callbacks )
			{
				foreach( $callbacks as $callback )
				{
					$function = $callback[ 'function' ];
					if ( is_array( $function ) )
					{
						if ( is_object( $function[ 0 ] ) )
							$function[ 0 ] = get_class( $function[ 0 ] );
						$function = sprintf( '%s::%s', $function[ 0 ], $function[ 1 ] );
					}
					$function = sprintf( '%s %s', $function, $priority );
					$hook_callbacks[] = $function;
				}
			}
			return $hook_callbacks;
		}
	
		/**
		 * Retrieves the BroadcastData for this post_id.
		 *
		 * Will return a fully functional BroadcastData class even if the post doesn't have BroadcastData.
		 *
		 * Use BroadcastData->is_empty() to check for that.
		 * @param int $post_id Post ID to retrieve data for.
		 */
		public function get_post_sync_data( $blog_id, $post_id )
		{
			return $this->sync_data_cache()->get_for( $blog_id, $post_id );
		}
	
		/**
			@brief		Get some standardizing CSS styles.
			@return		string		A string containing the CSS <style> data, including the tags.
			@since		20131031
		**/
		public function html_css()
		{
			return file_get_contents( __DIR__ . '/html/style.css' );
		}
	
		public function is_blog_user_writable( $user_id, $blog )
		{
			// Check that the user has write access.
			$blog->switch_to();
	
			global $current_user;
			wp_get_current_user();
			$r = current_user_can( 'edit_posts' );
	
			$blog->switch_from();
	
			return $r;
		}
	
		/**
		 * Recursively adds the missing ancestors of the given source term at the
		 * target blog.
		 *
		 * @param array $source_post_term           The term to add ancestors for
		 * @param array $source_post_taxonomy       The taxonomy we're working with
		 * @param array $target_blog_terms          The existing terms at the target
		 * @param array $parent_blog_taxonomy_terms The existing terms at the source
		 * @return int The ID of the target parent term
		 */
		public function insert_term_ancestors( $source_post_term, $source_post_taxonomy, $target_blog_terms, $parent_blog_taxonomy_terms )
		{
			// Fetch the parent of the current term among the source terms
			foreach ( $parent_blog_taxonomy_terms as $term )
			{
				if ( $term[ 'term_id' ] == $source_post_term[ 'parent' ] )
				{
					$source_parent = $term;
				}
			}
	
			if ( ! isset( $source_parent ) )
			{
				return 0; // Sanity check, the source term's parent doesn't exist! Orphan!
			}
	
			// Check if the parent already exists at the target
			foreach ( $target_blog_terms as $term )
			{
				if ( $term[ 'slug' ] === $source_parent[ 'slug' ] )
				{
					// The parent already exists, return its ID
					return $term[ 'term_id' ];
				}
			}
	
			// Does the parent also have a parent, and if so, should we create the parent?
			$target_grandparent_id = 0;
			if ( 0 != $source_parent[ 'parent' ] )
			{
				// Recursively insert ancestors, and get the newly inserted parent's ID
				$target_grandparent_id = $this->insert_term_ancestors( $source_parent, $source_post_taxonomy, $target_blog_terms, $parent_blog_taxonomy_terms );
			}
	
			// Check if the parent exists at the target grandparent
			$term_id = term_exists( $source_parent[ 'name' ], $source_post_taxonomy, $target_grandparent_id );
	
			if ( is_null( $term_id ) || 0 == $term_id )
			{
				// The target parent does not exist, we need to create it
				$new_term = (object)$source_parent;
				$new_term->parent = $target_grandparent_id;
				$action = new actions\wp_insert_term;
				$action->taxonomy = $source_post_taxonomy;
				$action->term = $new_term;
				$action->apply();
				$term_id = $action->new_term[ 'term_id' ];
			}
			elseif ( is_array( $term_id ) )
			{
				// The target parent exists and we got an array as response, extract parent id
				$term_id = $term_id[ 'term_id' ];
			}
	
			return $term_id;
		}
	
		/**
			@brief		Are we in the middle of a sync?
			@return		bool		True if we're networkSync.
			@since		20130926
		*/
		public function is_networkSync()
		{
			return count( $this->networkSync ) > 0;
		}
		
		/**
		 * internal fields start with underscore and are generally not interesting to sync.
		 * 
		 * @param unknown $custom_field
		 * @return boolean
		 */
		private function is_custom_field_valid( $custom_field ) {
			// If the field does not start with an underscore, it is automatically valid.
			if (strpos($custom_field, '_') !== 0) return true;
	
			// Has the user requested that all internal fields be synced?
			$sync_internal_custom_fields = $this->get_site_option( 'sync_internal_custom_fields' );
			if ($sync_internal_custom_fields) {
				// if sync all true then don't update list of the fields
				return true;
			} else {
				// Prep the cache.
				if ( !isset( $this->custom_field_whitelist_cache ) )
					$this->custom_field_whitelist_cache = array_filter( explode( ' ', $this->get_site_option( 'custom_field_whitelist' ) ) );
	
				foreach( $this->custom_field_whitelist_cache as $exception)
					if ( strpos( $custom_field, $exception) !== false )
						return true;
	
				// Not found in the whitelist. Do not sync.
				return false;
			}
		}
	
		private function keep_valid_custom_fields( $custom_fields )
		{
			foreach( $custom_fields as $key => $array)
				if ( ! $this->is_custom_field_valid( $key ) )
					unset( $custom_fields[$key] );
	
			return $custom_fields;
		}
	
		/**
			@brief		Converts a textarea of lines to a single line of space separated words.
			@param		string		$lines		Multiline string.
			@return		string					All of the lines on one line, minus the empty lines.
			@since		20131004
		**/
		public function lines_to_string( $lines )
		{
			$lines = explode( "\n", $lines );
			$r = [];
			foreach( $lines as $line )
				if ( trim( $line ) != '' )
					$r[] = trim( $line );
			return implode( ' ', $r );
		}
	
		private function load_last_used_settings( $user_id)
		{
			$data = $this->sql_user_get( $user_id );
			if (!isset( $data[ 'last_used_settings' ] ) )
				$data[ 'last_used_settings' ] = [];
			return $data[ 'last_used_settings' ];
		}
	
		/**
			@brief		Will only copy the attachment if it doesn't already exist on the target blog.
			@details	The return value is an object, with the most important property being ->attachment_id.
	
			@param		object		$options		See the parameter for copy_attachment.
		**/
		public function maybe_copy_attachment( $options )
		{
			if ( !isset( $this->attachment_cache ) )
				$this->attachment_cache = new collection;
	
			$attachment_data = $options->attachment_data;		// Convenience.
	
			$key = get_current_blog_id();
	
			$attachment_posts = $this->attachment_cache->get( $key, null );
			if ( $attachment_posts === null ) {
				$attachment_posts = get_posts( [
					'cache_results' => false,
					'name' => $attachment_data->post->post_name,
					'numberposts' => PHP_INT_MAX,
					'post_type' => 'attachment',
	
				] );
				$this->attachment_cache->put( $key, $attachment_posts );
			}
	
			// Is there an existing media file?
			// Try to find the filename in the GUID.
			foreach( $attachment_posts as $attachment_post )
			{
				if ( $attachment_post->post_name !== $attachment_data->post->post_name )
					continue;
				// We've found an existing attachment. What to do with it...
				switch( $this->get_site_option( 'existing_attachments', 'use' ) )
				{
					case 'overwrite':
						// Delete the existing attachment
						wp_delete_attachment( $attachment_post->ID, true );		// true = Don't go to trash
						break;
					case 'randomize':
						$filename = $options->attachment_data->filename_base;
						$filename = preg_replace( '/(.*)\./', '\1_' . rand( 1000000, 9999999 ) .'.', $filename );
						$options->attachment_data->filename_base = $filename;
						break;
					case 'use':
					default:
						// The ID is the important part.
						$options->attachment_id = $attachment_post->ID;
						return $options;
	
				}
			}
	
			// Since it doesn't exist, copy it.
			$this->copy_attachment( $options );
			return $options;
		}
	
		private function save_last_used_settings( $user_id, $settings )
		{
			$data = $this->sql_user_get( $user_id );
			$data[ 'last_used_settings' ] = $settings;
			$this->sql_user_set( $user_id, $data );
		}
	
		/**
		 * Updates / removes the BroadcastData for a post.
		 *
		 * If the BroadcastData->is_empty() then the BroadcastData is removed completely.
		 *
		 * @param int $blog_id Blog ID to update
		 * @param int $post_id Post ID to update
		 * @param BroadcastData $sync_data BroadcastData file.
		 */
		public function set_post_sync_data( $blog_id, $post_id, $sync_data )
		{
			// Update the cache.
			$this->sync_data_cache()->set_for( $blog_id, $post_id, $sync_data );
	
			if ( $sync_data->is_modified() )
				if ( $sync_data->is_empty() )
					$this->sql_delete_sync_data( $blog_id, $post_id );
				else
					$this->sql_update_sync_data( $blog_id, $post_id, $sync_data );
		}
	
		/**
			@brief		Syncs the terms of a taxonomy from the parent blog in the BCD to the current blog.
			@details	If $bcd->add_new_taxonomies is set, new taxonomies will be created, else they are ignored.
			@param		networkSync_data		$bcd			The networkSync data.
			@param		string					$taxonomy		The taxonomy to sync.
			@since		20131004
		**/
		public function sync_terms( $bcd, $taxonomy )
		{
			$source_terms = $bcd->parent_blog_taxonomies[ $taxonomy ][ 'terms' ];
			$target_terms = $this->get_current_blog_taxonomy_terms( $taxonomy );
	
			$refresh_cache = false;
	
			// Keep track of which terms we've found.
			$found_targets = [];
			$found_sources = [];
	
			// Also keep track of which sources we haven't found on the target blog.
			$unfound_sources = $source_terms;
	
			// First step: find out which of the target terms exist on the source blog
			foreach( $target_terms as $target_term_id => $target_term )
				foreach( $source_terms as $source_term_id => $source_term )
				{
					if ( isset( $found_sources[ $source_term_id ] ) )
						continue;
					if ( $source_term[ 'slug' ] == $target_term[ 'slug' ] ) {
						$found_targets[ $target_term_id ] = $source_term_id;
						$found_sources[ $source_term_id ] = $target_term_id;
						unset( $unfound_sources[ $source_term_id ] );
					}
				}
	
			// These sources were not found. Add them.
			if ( isset( $bcd->add_new_taxonomies ) && $bcd->add_new_taxonomies ) {
				foreach( $unfound_sources as $unfound_source_id => $unfound_source )
				{
					$unfound_source = (object)$unfound_source;
					unset( $unfound_source->parent );
					$action = new actions\wp_insert_term;
					$action->taxonomy = $taxonomy;
					$action->term = $unfound_source;
					$action->apply();
	
					$new_taxonomy = $action->new_term;
					$new_taxonomy_id = $new_taxonomy[ 'term_id' ];
					$target_terms[ $new_taxonomy_id ] = (array)$new_taxonomy;
					$found_sources[ $unfound_source_id ] = $new_taxonomy_id;
					$found_targets[ $new_taxonomy_id ] = $unfound_source_id;
	
					$refresh_cache = true;
				}
			}
	
			// Now we know which of the terms on our target blog exist on the source blog.
			// Next step: see if the parents are the same on the target as they are on the source.
			// "Same" meaning pointing to the same slug.
			foreach( $found_targets as $target_term_id => $source_term_id)
			{
				$source_term = (object)$source_terms[ $source_term_id ];
				$target_term = (object)$target_terms[ $target_term_id ];
	
				$action = new actions\wp_update_term;
				$action->taxonomy = $taxonomy;
	
				// The old term is the target term, since it contains the old values.
				$action->old_term = (object)$target_terms[ $target_term_id ];
				// The new term is the source term, since it has the newer data.
				$action->new_term = (object)$source_terms[ $source_term_id ];
	
				// ... but the IDs have to be switched around, since the target term has the new ID.
				$action->switch_data();
	
				// Update the parent.
				$parent_of_equivalent_source_term = $source_term->parent;
				$parent_of_target_term = $target_term->parent;
	
				$new_parent = 0;
				// Does the source term even have a parent?
				if ( $parent_of_equivalent_source_term > 0 )
				{
					// Did we find the parent term?
					if ( isset( $found_sources[ $parent_of_equivalent_source_term ] ) )
						$new_parent = $found_sources[ $parent_of_equivalent_source_term ];
				}
				else
					$new_parent = 0;
	
				$action->new_term->parent = $new_parent;
	
				$action->apply();
				$refresh_cache |= $action->updated;
			}
	
			// wp_update_category alone won't work. The "cache" needs to be cleared.
			// see: http://wordpress.org/support/topic/category_children-how-to-recalculate?replies=4
			if ( $refresh_cache )
				delete_option( 'category_children' );
		}
	
		/**
			@brief		Return yes / no, depending on value.
			@since		20140220
		**/
		public function yes_no( $value )
		{
			return $value ? 'yes' : 'no';
		}
	
		// --------------------------------------------------------------------------------------------
		// ----------------------------------------- SQL
		// --------------------------------------------------------------------------------------------
	
		/**
		 * Gets the user data.
		 *
		 * Returns an array of user data.
		 */
		public function sql_user_get( $user_id)
		{
			$r = $this->query("SELECT * FROM `".$this->wpdb->base_prefix."_3wp_sync` WHERE user_id = '$user_id'");
			$r = @unserialize( base64_decode( $r[0][ 'data' ] ) );		// Unserialize the data column of the first row.
			if ( $r === false)
				$r = [];
	
			// Merge/append any default values to the user's data.
			return array_merge(array(
				'groups' => [],
			), $r);
		}
	
		/**
		 * Saves the user data.
		 */
		public function sql_user_set( $user_id, $data)
		{
			$data = serialize( $data);
			$data = base64_encode( $data);
			$this->query("DELETE FROM `".$this->wpdb->base_prefix."_3wp_sync` WHERE user_id = '$user_id'");
			$this->query("INSERT INTO `".$this->wpdb->base_prefix."_3wp_sync` (user_id, data) VALUES ( '$user_id', '$data' )");
		}
	
		/**
			@brief		Returns an array of SQL rows for these post_ids.
			@param		int		$blog_id		ID of blog for which to fetch the datas
			@param		mixed	$post_ids		An array of ints or a string signifying which datas to retrieve.
			@return		array					An array of database rows. Each row has a BroadcastData object in the data column.
			@since		20131009
		**/
		public function sql_get_sync_datas( $blog_id, $post_ids )
		{
			if ( ! is_array( $post_ids ) )
				$post_ids = [ $post_ids ];
	
			$query = sprintf( "SELECT * FROM `%s` WHERE `blog_id` = '%s' AND `post_id` IN ('%s')",
				$this->sync_data_table(),
				$blog_id,
				implode( "', '", $post_ids )
			);
			$results = $this->query( $query );
			foreach( $results as $index => $result )
				$results[ $index ][ 'data' ] = BroadcastData::sql( $result );
			return $results;
		}
	
		/**
			@brief		Delete sync data.
			@details	If $post_id is not used, then the $blog_id is assumed to be just the row ID.
	
			If $post_id is used, then $blog_id is the actual $blog_id.
			@since		20131105
		**/
		public function sql_delete_sync_data( $blog_id, $post_id = null )
		{
			if ( $post_id === null )
				$query = sprintf( "DELETE FROM `%s` WHERE `id` = '%s'",
					$this->sync_data_table(),
					$blog_id
				);
			else
				$query = sprintf( "DELETE FROM `%s` WHERE blog_id = '%s' AND post_id = '%s'",
					$this->sync_data_table(),
					$blog_id,
					$post_id
				);
			$this->query( $query );
		}
	
		public function sql_update_sync_data( $sync_data )
		{
			$args = func_get_args();
			if ( count( $args ) == 1 )
				return $this->sql_update_sync_data_old( null, null, $sync_data );
			else
				return call_user_func_array( [ $this, 'sql_update_sync_data_old' ], $args );
		}
	
		public function sql_update_sync_data_object( $sync_data )
		{
		}
	
		public function sql_update_sync_data_old( $blog_id, $post_id, $bcd )
		{
			$bcd = '';
			$data = serialize( $bcd->getData() );
			$data = base64_encode( $data );
			$query = sprintf( "INSERT INTO `%s` (blog_id, post_id, data) VALUES ( '%s', '%s', '%s' )",
					$this->sync_data_table(),
					$blog_id,
					$post_id,
					$data
			);
			$this->query( $query );
			/*
			if ( $bcd->id > 0 )
			{
				$query = sprintf( "UPDATE `%s` SET `data` = '%s' WHERE `id` = '%s'",
					$this->sync_data_table(),
					$data,
					$bcd->id
				);
			}
			else
				$query = sprintf( "INSERT INTO `%s` (blog_id, post_id, data) VALUES ( '%s', '%s', '%s' )",
					$this->sync_data_table(),
					$blog_id,
					$post_id,
					$data
				);
			$this->query( $query );
			*/
		}
	}
}
$net_sync = new netSync();
