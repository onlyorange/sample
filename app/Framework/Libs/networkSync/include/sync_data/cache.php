<?php

namespace net_sync\sync_data;

use net_sync\BroadcastData;

/**
	@brief		Cache for sync data
	@since		20131009
**/
class cache
	extends \net_sync\cache\posts_cache
{

	/**
		@brief		Store an empty sync data object.
		@since		20131010
	**/
	public function cache_no_data( $blog_id, $post_id )
	{
		$key = $this->key( $blog_id, $post_id );
		$this->set( $key, new BroadcastData );
	}

	/**
		@brief		Gets the sync data of a blog+post combo.
		@details 	Will always return a sync_data object.
		@return		sync_data		Broadcast data object.
		@since		20131010
	**/
	public function get_for( $blog_id, $post_id )
	{
		$key = $this->key( $blog_id, $post_id );

		if ( ! $this->has( $key ) )
		{
			// Retrieve the post data for this solitary post.
			$results = $this->lookup( $blog_id, $post_id );
			if ( count( $results ) == 1 )
			{
				$results = reset( $results );
				$bcd = $results[ 'data' ];
			}
			else
				$bcd = new BroadcastData;
			$this->set_for( $blog_id, $post_id, $bcd );
		}
		return $this->get( $key );
	}

	/**
		@brief		Asks netSync to look up some sync datas.
		@since		20131010
	**/
	public function lookup( $blog_id, $post_ids )
	{
		return \net_sync\netSync::instance()->sql_get_sync_datas( $blog_id, $post_ids );
	}
}
