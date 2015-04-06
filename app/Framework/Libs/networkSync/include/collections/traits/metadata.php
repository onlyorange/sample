<?php

namespace net_sync\collections\traits;

trait metadata
{
	/**
		@brief		Return the metadata part of this collection.
		@since		2014-04-18 11:01:09
	**/
	public function metadata()
	{
		if ( ! isset( $this->__metadata ) )
			$this->__metadata = new \plainview\sdk\collections\collection;
		return $this->__metadata;
	}
}
