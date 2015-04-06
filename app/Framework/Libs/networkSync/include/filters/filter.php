<?php

namespace net_sync\filters;

class filter
	extends \net_sync\actionfilter
{
	public function apply_method( $filter_name )
	{
		global $net_sync;
		$net_sync->filters( $filter_name, $this );
		return $this;
	}
}
