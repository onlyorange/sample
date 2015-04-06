<?php
// The Tag Entity. Similar to Category entity, but uses 'post_tag' terms

class swedenWpTagEntity extends swedenWpCategoryEntity
{

    public function __construct($tag)
    {
        if (is_int($tag)) {
            $tag = get_term($tag, 'post_tag');
        }

        parent::__construct($tag);
    }
}