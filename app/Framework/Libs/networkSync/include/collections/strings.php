<?php

namespace net_sync\collections;

class strings
	extends \plainview\sdk\collections\collection
{
	/**
		@brief		Converts each item to a string.
		@since		2014-04-18 10:01:02
	**/
	public function __toString()
	{
		$r = '';

		foreach( $this as $item )
			$r .= $item;

		return $r;
	}
}
