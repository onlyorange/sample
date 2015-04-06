<?php

namespace net_sync\actions;

class action
	extends \net_sync\actionfilter
{
	public function apply_method( $filter_name )
	{
		do_action( $filter_name, $this );
	}
}
