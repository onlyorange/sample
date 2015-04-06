<?php

namespace net_sync\actions;

/**
	@brief		This is called before [maybe] updating the post.
	@details	Called before custom field handling. If you want after custom fields, use networkSync_before_restore_current_blog.
	@since		2014-02-23 22:36:32
**/
class networkSync_modify_post
	extends action
{
	/**
		@brief		The networkSync data.
		@details	The BCD contains the ->modified_post object to which you write your changes.
		@since		2014-02-23 22:36:32
	**/
	public $networkSync_data;
}
