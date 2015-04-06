<?php

// The Category Entity. category.php is using this
class swedenWpCategoryEntity extends swedenWpBaseEntity
{

    /** @var int ID of category */
    protected $id;

    /** @var string Title of category */
    protected $title;

    /** @var string Slug of category */
    protected $slug;

    /** @var string Description of category */
    protected $description;

    /** @var int ID of parent category */
    protected $parent;

    /** @var int Number of items in this category */
    protected $count;

    public function __construct($category)
    {
        if (is_int($category)) {
            $category = get_category($category);
        }

        $this->id = (int) $category->term_id;
        $this->title = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;

        $this->parent = (int) $category->parent;
        $this->count = (int) $category->count;
    }
}