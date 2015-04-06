<?php

namespace net_sync\actions;

/**
	@brief		Maybe clear the POST data before networkSync.
	@since		2014-03-23 23:07:02
**/
class maybe_clear_post
	extends action
{
	/**
		@brief		The _POST variable to manipulate before networkSync.
		@since		2014-03-23 23:07:19
	**/
	public $post;
}
