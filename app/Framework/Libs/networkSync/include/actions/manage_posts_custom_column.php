<?php

namespace net_sync\actions;

use \net_sync\blog_collection;

class manage_posts_custom_column
	extends action
{
	public $html;

	public function _construct()
	{
		$this->html = new \net_sync\collections\strings_with_metadata;
	}

	public function render()
	{
		$r = '';
		foreach( $this->html as $key => $html )
		{
			$r .= sprintf( '<div class="%s">%s</div>', $key, $html );
		}
		return $r;
	}
}